<?php

namespace App\Controller\User;

use App\Controller\AppController as BaseController;
use App\Model\Entity\Info;
use Cake\Http\Exception\NotFoundException;


class AppController extends BaseController
{
    protected function _lists($cond = [], $options = [])
    {
        $primary_key = $this->{$this->modelName}->getPrimaryKey();
        $this->paginate = array_merge(
            [
                'order' => [$this->modelName . '.' . $primary_key . ' DESC'],
                'limit' => 10,
                'contain' => [],
                'paramType' => 'querystring',
                'url' => [
                    'sort' => null,
                    'direction' => null
                ]
            ],
            $options
        );

        $sql_query = array_key_exists('sql_query', $options) ? $sql_query = $options['sql_query'] : null;

        try {
            if ($this->paginate['limit'] === null) {

                unset($options['limit'], $options['paramType']);

                if ($cond)
                    $options['conditions'] = $cond;

                $data_query = $this->{$this->modelName}
                    ->find()
                    ->where($cond)
                    ->order($options['order'])
                    ->all();
            } elseif (!is_null($sql_query)) $data_query = $this->paginate($sql_query);
            else $data_query = $this->paginate($this->{$this->modelName}->find()->where($cond));

            $datas = $data_query->toArray();
            $numrows = $this->{$this->modelName}->find()->where($cond)->count();

            $this->set(compact('datas', 'data_query', 'numrows'));
        } catch (NotFoundException $e) {
            if (
                !empty($this->request->query['page'])
                && 1 < $this->request->query['page']
            )
                $this->redirect(array('action' => $this->request->action));
        }
    }


    protected function _edit($id = 0, $option = [])
    {
        $option = array_merge(
            [
                'saveAll' => false,
                'saveMany' => false,
                'create' => null,
                'callback' => null,
                'redirect' => ['action' => 'index'],
                'contain' => [],
                'success_message' => '保存しました',
                'validate' => 'default',
                'append_validate' => null,
                'associated' => null,
                'get_callback' => null
            ],
            $option
        );
        extract($option);

        $primary_key = $this->{$this->modelName}->getPrimaryKey();
        $data = $this->request->getData();

        if (!isset($data[$primary_key]) && $id != 0) $data[$primary_key] = $id;

        if (empty($contain) && !empty($associated)) $contain = $associated;

        $isValid = true;

        if ($this->request->is(array('post', 'put')) && $this->request->getData()) {

            $entity_options = [];

            if (!empty($associated)) $entity_options['associated'] = $associated;

            if (!empty($validate)) $entity_options['validate'] = $validate;

            $entity = $this->{$this->modelName}->newEntity($this->request->getData(), $entity_options);

            if ($entity->getErrors()) {
                $isValid = false;

                if (property_exists($this->{$this->modelName}, 'useHierarchization') && !empty($this->{$this->modelName}->useHierarchization)) {
                    $vals = $this->{$this->modelName}->useHierarchization;
                    $_model = $vals['sequence_model'];

                    if (!empty($entity[$vals['contents_table']])) {

                        foreach ($entity[$vals['contents_table']] as $k => $v) {
                            if (empty($v['id'])) $entity[$vals['contents_table']][$k]['id'] = null;

                            if ($v[$vals['sequence_id_name']]) {
                                $seq = $this->{$_model}->find()->where([$_model . '.id' => $v[$vals['sequence_id_name']]])->first();
                                $entity[$vals['contents_table']][$k][$vals['sequence_table']] = $seq;
                            }
                        }
                    }
                }

                $request = $this->getRequest()->withParsedBody($this->{$this->modelName}->toFormData($entity));
                $this->setRequest($request);
                $this->set('data', $data);
            }

            if ($append_validate) $isValid = $append_validate($isValid, $entity);

            if ($isValid) {
                if ($this->{$this->modelName}->save($entity)) {
                    if ($success_message) $this->Flash->set($success_message);
                    if ($callback)  $callback($entity->id);
                    if ($redirect) $this->redirect($redirect);
                }
            } else $this->Flash->set('正しく入力されていない項目があります');
        } else {

            $query = $this->{$this->modelName}->find()->where([$this->modelName . '.' . $primary_key => $id])->contain($contain);

            if ($create) {

                $request = $this->getRequest()->withParsedBody($create);
                $this->setRequest($request);
                $entity = $this->{$this->modelName}->newEntity($create);
            } elseif (!$query->isEmpty()) {

                $entity = $query->first();
                $request = $this->getRequest()->withParsedBody($this->{$this->modelName}->toFormData($entity));
                $this->setRequest($request);
            } else {

                $entity = $this->{$this->modelName}->newEntity();
                $entity->{$primary_key} = null;

                $request = $this->getRequest()->withParsedBody($this->{$this->modelName}->toFormData($entity));
                $this->setRequest($request);
                if (property_exists($this->{$this->modelName}, 'defaultValues')) {
                    $request = $this->getRequest()->withParsedBody(array_merge($this->request->getData(), $this->{$this->modelName}->defaultValues));
                    $this->setRequest($request);
                }
            }

            if ($get_callback) {
                $request = $this->getRequest()->withParsedBody($get_callback($this->request->getData()));
                $this->setRequest($request);
            }
        }

        if (property_exists($this->{$this->modelName}, 'useHierarchization') && !empty($this->{$this->modelName}->useHierarchization)) {
            $block_waku_list = array_keys(Info::BLOCK_TYPE_WAKU_LIST);
            $contents = $this->toHierarchization($id, $entity, ['section_block_ids' => $block_waku_list]);
            $this->set(array_keys($contents), $contents);
        }

        $this->set('data',  $this->request->getData());
        $this->set('entity', $entity);

        return $isValid;
    }


    public function _detail($id, $option = [])
    {
        $option = array_merge(
            [
                'callback' => null,
                'redirect' => ['action' => 'index'],
                'contain' => []
            ],
            $option
        );
        extract($option);

        $primary_key = $this->{$this->modelName}->getPrimaryKey();

        $query = $this->{$this->modelName}->find()->where([$this->modelName . '.' . $primary_key => $id])->contain($contain);

        if (!$query->isEmpty()) {
            $entity = $query->first();
            $request = $this->getRequest()->withParsedBody($this->{$this->modelName}->toFormData($entity));
            $this->setRequest($request);
        } else {
            $entity = $this->{$this->modelName}->newEntity();
            $entity->{$primary_key} = null;
            $request = $this->getRequest()->withParsedBody($this->{$this->modelName}->toFormData($entity));
            $this->setRequest($request);
            if (property_exists($this->{$this->modelName}, 'defaultValues')) {
                $request = $this->getRequest()->withParsedBody(array_merge($this->request->getData(), $this->{$this->modelName}->defaultValues));
                $this->setRequest($request);
            }
        }
        $this->set('data', $this->request->getData());

        if (property_exists($this->{$this->modelName}, 'useHierarchization') && !empty($this->{$this->modelName}->useHierarchization)) {
            $block_waku_list = array_keys(Info::BLOCK_TYPE_WAKU_LIST);
            $contents = $this->toHierarchization($id, $entity, ['section_block_ids' => $block_waku_list]);
            $this->set(array_keys($contents), $contents);
        }

        $this->set('entity', $entity);
    }


    public function isLogin()
    {
        return $this->Session->read('userid');
    }


    public function checkLogin()
    {
        return parent::checkUserLogin();
    }


    /**
     * 順番並び替え
     * */
    protected function _position($id, $pos, $options = [])
    {
        $query = $this->_getQueryIndex();

        $options = array_merge([
            'redirect' => ['action' => 'index', '?' => $query]
        ], $options);
        extract($options);

        $primary_key = $this->{$this->modelName}->getPrimaryKey();
        $q = $this->{$this->modelName}->find()->where([$this->modelName . '.' . $primary_key => $id]);

        if (!$q->isEmpty()) $this->{$this->modelName}->movePosition($id, $pos);

        if ($redirect) $this->redirect($redirect);
    }


    /**
     * 掲載中/下書き トグル
     * */
    protected function _enable($id, $options = [])
    {
        $query = $this->_getQueryIndex();

        $options = array_merge([
            'redirect' => ['action' => 'index', '?' => $query]
        ], $options);
        extract($options);

        $primary_key = $this->{$this->modelName}->getPrimaryKey();
        $q = $this->{$this->modelName}->find()->where([$this->modelName . '.' . $primary_key => $id]);

        if (!$q->isEmpty()) {
            $entity = $q->first();
            $status = ($entity->get('status') == 'publish') ? 'draft' : 'publish';
            $this->{$this->modelName}->updateAll(['status' => $status], [$primary_key => $id]);
        }

        if ($redirect) $this->redirect($redirect);
    }

    /**
     * ファイル/記事削除
     *
     * */
    protected function _delete($id, $type, $columns = null, $option = array())
    {
        $option = array_merge(
            array('redirect' => null),
            $option
        );
        extract($option);

        $primary_key = $this->{$this->modelName}->getPrimaryKey();
        $query = $this->{$this->modelName}->find()->where([$this->modelName . '.' . $primary_key => $id]);

        if (!$query->isEmpty() && in_array($type, ['image', 'file', 'content'])) {

            $entity = $query->first();
            $data = $entity->toArray();
            $images_attaches = $this->{$this->modelName}->attaches['images'];
            $files_attaches = $this->{$this->modelName}->attaches['files'];

            if ($type === 'image' && isset($images_attaches[$columns])) {
                if (!empty($data['attaches'][$columns])) {
                    foreach ($data['attaches'][$columns] as $_) {
                        $_file = WWW_ROOT . $_;
                        if (is_file($_file)) {
                            @unlink($_file);
                        }
                    }
                }
                $this->{$this->modelName}->updateAll(
                    array($columns => null),
                    array($this->modelName . '.' . $primary_key => $id)
                );
            } else if ($type === 'file' && isset($files_attaches[$columns])) {
                if (!empty($data['attaches'][$columns][0])) {
                    $_file = WWW_ROOT . $data['attaches'][$columns][0];
                    if (is_file($_file)) {
                        @unlink($_file);
                    }

                    $this->{$this->modelName}->updateAll(
                        array(
                            $columns => null,
                            $columns . '_name' => null,
                            $columns . '_size' => null,
                        ),
                        array($this->modelName . '.' . $primary_key => $id)
                    );
                }
            } else if ($type === 'content') {
                $image_index = array_keys($images_attaches);
                $file_index = array_keys($files_attaches);

                foreach ($image_index as $idx) {
                    foreach ($data['attaches'][$idx] as $_) {
                        $_file = WWW_ROOT . $_;
                        if (is_file($_file)) @unlink($_file);
                    }
                }

                foreach ($file_index as $idx) {
                    $_file = WWW_ROOT . $data['attaches'][$idx][0];
                    if (is_file($_file)) @unlink($_file);
                }

                $this->{$this->modelName}->delete($entity);

                $id = null;
            }
        }


        if ($redirect) return $this->redirect($redirect);

        elseif ($redirect !== false) return $this->redirect($id ? ['action' => 'edit', $id] : ['action' => 'index']);
        return $id;
    }


    /**
     * ログインユーザーの記事かチェック
     * @param  [type] $info_id [description]
     * @return [type]          [description]
     */
    protected function checkOwner($info_id)
    {
        $result = false;

        $cond = [
            'UserInfos.id' => $info_id,
            'UserInfos.user_id' => $this->isLogin()
        ];
        $info = $this->UserInfos->find()->where($cond);
        if (!$info->isEmpty()) $result = true;

        return $result;
    }


    protected function getUsername()
    {
        return $this->Session->read('data.username');
    }


    public function getUserId()
    {
        return $this->isLogin();
    }


    public function array_asso_chunk($datas, $num)
    {
        $res = [];
        $count = 0;
        $i = 0;

        foreach ($datas as $k => $v) {
            $res[$i][$k] = $v;
            $count++;
            if (!($count % $num)) $i++;
        }

        return $res;
    }


    public function setCommon()
    {
        $this->UserSites = $this->loadModel('UserSites');
        $this->PageConfigs = $this->loadModel('PageConfigs');

        $user_id = $this->isLogin();
        if (!$user_id) return;

        // サイト　取得
        $user_site_list = $this->_getUserSite($user_id);

        $current_site_id = $this->Session->read('current_site_id');
        $current_site_slug = $this->Session->read('current_site_slug');

        // コンテンツ　取得
        $site_ids = array_keys($user_site_list);

        $user_page_configs = $this->PageConfigs->find()
            ->select(['id', 'page_title', 'slug'])
            ->where(['PageConfigs.site_config_id' => $current_site_id])
            ->order(['PageConfigs.position' => 'ASC'])
            ->all();

        $user_menu_site_list = [];
        if (!empty($user_page_configs)) {
            foreach ($user_page_configs as $config) {
                $user_menu_site_list[$config->page_title] = [
                    '新規登録' => '/user/infos/edit/?sch_page_id=' . $config->id,
                    '登録一覧' => '/user/infos/?sch_page_id=' . $config->id
                ];
            }
        }

        $this->set(compact('user_site_list', 'user_menu_site_list', 'current_site_id', 'current_site_slug'));
    }


    public function _getUserSite($user_id)
    {
        $user_sites = $this->UserSites->find()
            ->where(['UserSites.user_id' => $user_id])
            ->contain(['SiteConfigs'])
            ->all();

        $user_site_list = [];

        if (!empty($user_sites)) foreach ($user_sites as $site) $user_site_list[$site->site_config->id] = $site->site_config->site_name;

        if (!$this->Session->read('current_site_id')) {
            foreach ($user_site_list as $site_id => $config) {

                $this->Session->write('current_site_id', $site_id);

                if (!$this->Session->read('current_site_slug')) {
                    foreach ($user_sites as $site) {
                        if ($site->site_config_id == $site_id) {
                            $this->Session->write('current_site_slug', $site->site_config->slug);
                        }
                    }
                }
                break;
            }
        }
        return $user_site_list;
    }


    protected function isUserRole($role_key, $isOnly = false)
    {
        $role = $this->Session->read('user_role');

        if (intval($role) === 0) $res = 'develop';
        elseif ($role < 10) $res = 'admin';
        /** 必要に応じて追加 */
        else $res = 'staff';

        if (!$isOnly) {
            if ($role_key == 'admin') $role_key = array('develop', 'admin');
            elseif ($role_key == 'staff') $role_key = array('develop', 'admin', 'staff');
        }

        return in_array($res, (array) $role_key);
    }


    protected function _getQueryIndex()
    {
        $query = [];

        $query['pos'] = $this->request->getQuery('pos');
        if (empty($query['pos'])) $query['pos'] = 0;

        $query['page'] = $this->request->getQuery('page');
        if (empty($query['page'])) unset($query['page']);

        return $query;
    }
}
