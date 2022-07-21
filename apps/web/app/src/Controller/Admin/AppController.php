<?php

namespace App\Controller\Admin;

use App\Controller\AppController as BaseController;
use Cake\Http\Exception\NotFoundException;


class AppController extends BaseController
{

    protected function _lists($cond = [], $options = [])
    {

        $primary_key = $this->{$this->modelName}->getPrimaryKey();

        $options = array_merge(
            [
                'order' => [$this->modelName . '.' . $primary_key . ' DESC'],
                'limit' => 10,
                'paramType' => 'querystring',
                'url' => [
                    'sort' => null,
                    'direction' => null
                ]
            ],
            $options
        );

        $this->paginate = $options;

        try {
            if ($this->paginate['limit'] === null)
                $query = $this->{$this->modelName}
                    ->find()
                    ->where($cond)
                    ->order($options['order'])
                    ->all();
            else $query = $this->paginate($this->{$this->modelName}->find()->where($cond));

            $datas = $query->toArray();
            $this->set(compact('datas', 'query'));
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
                'create' => null,
                'callback' => null,
                'error_callback' => null,
                'redirect' => ['action' => 'index'],
                'contain' => [],
                'success_message' => '保存しました',
                'validate' => 'default',
                'associated' => null
            ],
            $option
        );
        extract($option);

        $primary_key = $this->{$this->modelName}->getPrimaryKey();
        $data = $this->request->getData();
        if (!isset($data[$primary_key]) && $id != 0) $data[$primary_key] = $id;
        $isValid = true;

        if ($this->request->is(['post', 'put']) && $this->request->getData()) {

            $entity_options = [];

            if ($associated)
                $entity_options['associated'] = $associated;

            if ($validate)
                $entity_options['validate'] = $validate;

            $entity = $this->{$this->modelName}->newEntity($data, $entity_options);
            if ($entity->getErrors()) {

                $isValid = false;

                if (property_exists($this->{$this->modelName}, 'useHierarchization') && !empty($this->{$this->modelName}->useHierarchization)) {

                    $vals = $this->{$this->modelName}->useHierarchization;
                    $_model = $vals['sequence_model'];

                    foreach ($entity[$vals['contents_table']] as $k => $v) {

                        if ($v[$vals['sequence_id_name']]) {

                            $seq = $this->{$_model}
                                ->find()
                                ->where([$_model . '.id' => $v[$vals['sequence_id_name']]])
                                ->first();

                            $entity[$vals['contents_table']][$k][$vals['sequence_table']] = $seq;
                        }
                    }
                }
                if ($error_callback)
                    $data = $error_callback($data);
            }

            if ($isValid) {
                if ($this->{$this->modelName}->save($entity)) {
                    if ($success_message)
                        $this->Flash->set($success_message);

                    if ($callback)
                        $callback($entity->id);

                    if ($redirect)
                        $this->redirect($redirect);
                }
            } else $this->Flash->set('正しく入力されていない項目があります');
        } else {

            $query = $this->{$this->modelName}
                ->find()
                ->where([$this->modelName . '.' . $primary_key => $id])
                ->contain($contain);

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
            $data = $this->request->getData();
        }

        if (property_exists($this->{$this->modelName}, 'useHierarchization') && !empty($this->{$this->modelName}->useHierarchization)) {
            $contents = $this->toHierarchization($id, $entity);
            $this->set(array_keys($contents), $contents);
        }

        $this->set('data', $data);
        $this->set('entity', $entity);
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
            $contents = $this->toHierarchization($id, $entity);
            $this->set(array_keys($contents), $contents);
        }

        $this->set('entity', $entity);
    }


    public function isLogin()
    {
        return $this->Session->read('uid');
    }


    public function checkLogin()
    {
        if (!$this->isLogin())
            return $this->redirect('/user/');
    }


    /**
     * 順番並び替え
     * */
    protected function _position($id, $pos, $options = [])
    {
        $options = array_merge([
            'redirect' => ['action' => 'index', '#' => 'content-' . $id]
        ], $options);
        extract($options);

        $primary_key = $this->{$this->modelName}->getPrimaryKey();
        $query = $this->{$this->modelName}->find()->where([$this->modelName . '.' . $primary_key => $id]);

        if (!$query->isEmpty())
            $this->{$this->modelName}->movePosition($id, $pos);

        if ($redirect)
            $this->redirect($redirect);
    }


    /**
     * 掲載中/下書き トグル
     * */
    protected function _enable($id, $options = [])
    {
        $options = array_merge([
            'redirect' => ['action' => 'index', '#' => 'content-' . $id]
        ], $options);
        extract($options);

        $primary_key = $this->{$this->modelName}->getPrimaryKey();
        $query = $this->{$this->modelName}->find()->where([$this->modelName . '.' . $primary_key => $id]);

        if (!$query->isEmpty()) {
            $entity = $query->first();
            $status = ($entity->get('status') == 'publish') ? 'draft' : 'publish';
            $this->{$this->modelName}->updateAll(['status' => $status], [$primary_key => $id]);
        }
        if ($redirect)
            $this->redirect($redirect);
    }


    /**
     * ファイル/記事削除
     *
     * */
    protected function _delete($id, $type, $columns = null, $option = [])
    {
        $option = array_merge(
            ['redirect' => null],
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

                        if (is_file($_file))
                            @unlink($_file);
                    }
                }

                $this->{$this->modelName}->updateAll(
                    [$columns => null],
                    [$this->modelName . '.' . $primary_key => $id]
                );
            } else if ($type === 'file' && isset($files_attaches[$columns])) {

                if (!empty($data['attaches'][$columns][0])) {
                    $_file = WWW_ROOT . $data['attaches'][$columns][0];

                    if (is_file($_file))
                        @unlink($_file);

                    $this->{$this->modelName}->updateAll(
                        [
                            $columns => null,
                            $columns . '_name' => null,
                            $columns . '_size' => null
                        ],
                        [$this->modelName . '.' . $primary_key => $id]
                    );
                }
            } else if ($type === 'content') {
                $image_index = array_keys($images_attaches);
                $file_index = array_keys($files_attaches);

                foreach ($image_index as $idx) {
                    foreach ($data['attaches'][$idx] as $_) {
                        $_file = WWW_ROOT . $_;
                        if (is_file($_file))
                            @unlink($_file);
                    }
                }

                foreach ($file_index as $idx) {
                    $_file = WWW_ROOT . $data['attaches'][$idx][0];
                    if (is_file($_file))
                        @unlink($_file);
                }

                $this->{$this->modelName}->delete($entity);

                $id = null;
            }
        }

        if ($redirect) $this->redirect($redirect);

        if ($redirect !== false) $this->redirect($id ? ['action' => 'edit', $id] : ['action' => 'index']);

        return;
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

        if (!$info->isEmpty())
            $result = true;

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
}
