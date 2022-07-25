<?php

namespace App\Controller\User;

use Cake\Core\Configure;
use Cake\Network\Exception\ForbiddenException;
use Cake\Network\Exception\NotFoundException;
use Cake\View\Exception\MissingTemplateException;
use Cake\Event\Event;
use Cake\ORM\TableRegistry;
use App\Model\Entity\Info;
use Cake\Filesystem\Folder;
use Cake\Utility\Hash;
use App\Model\Entity\AppendItem;


use App\Model\Entity\PageConfig;

/**
 * Static content controller
 *
 * This controller will render views from Template/Pages/
 *
 * @link https://book.cakephp.org/3.0/en/controllers/pages-controller.html
 */
class ItemsController extends AppController
{
    private $list = [];

    public function initialize()
    {
        parent::initialize();

        $this->Infos = $this->getTableLocator()->get('Infos');
        $this->InfoContents = $this->getTableLocator()->get('InfoContents');
        $this->SectionSequences = $this->getTableLocator()->get('SectionSequences');
        $this->PageConfigs = $this->getTableLocator()->get('PageConfigs');
        $this->SiteConfigs = $this->getTableLocator()->get('SiteConfigs');
        $this->Categories = $this->getTableLocator()->get('Categories');
        $this->Tags = $this->getTableLocator()->get('Tags');
        $this->InfoTags = $this->getTableLocator()->get('InfoTags');
        $this->InfoAppendItems = $this->getTableLocator()->get('InfoAppendItems');
        $this->AppendItems = $this->getTableLocator()->get('AppendItems');
        $this->MstLists = $this->getTableLocator()->get('MstLists');

        $this->loadComponent('OutputHtml');

        $this->modelName = 'Infos';
        $this->set('ModelName', $this->modelName);
    }

    public function beforeFilter(Event $event)
    {
        // $this->viewBuilder()->theme('Admin');
        $this->viewBuilder()->setLayout("user");

        $this->setCommon();
        $this->getEventManager()->off($this->Csrf);
    }


    public function index()
    {
        $this->checkLogin();

        $this->setList();

        $query = $this->_getQuery();

        $this->_setView($query);

        // slug
        $page_config_id = $query['sch_page_id'];

        if (!$this->isOwnPageByUser($page_config_id)) {
            $this->Flash->set('不正なアクセスです');
            $this->redirect('/user');
            return;
        }

        $page_config = $this->PageConfigs->find()
            ->where(['PageConfigs.id' => $page_config_id])
            ->contain(['SiteConfigs' => function ($q) {
                return $q->select('slug');
            }])
            ->first();
        $preview_slug_dir = '';
        $page_title = '';
        if (!empty($page_config)) {
            $preview_slug_dir = $page_config->site_config->slug . DS . ($page_config->slug ? $page_config->slug . DS : '');
            $page_title = $page_config->page_title;
        } else {
            $preview_slug_dir = '';
        }

        $preview_slug_dir = str_replace('__', '/', $preview_slug_dir);

        $this->set(compact('preview_slug_dir', 'page_title', 'query', 'page_config'));

        $cond = array();
        $cond = $this->_getConditions($query);

        $contain = [
            'Categories',
            'InfoAppendItems' => function ($q) {
                return $q->contain(['AppendItems'])->order(['AppendItems.position' => 'ASC']);
            }
        ];

        return parent::_lists($cond, array(
            'order' => array($this->modelName . '.position' =>  'ASC'),
            'limit' => 20,
            'contain' => $contain
        ));
    }

    public function listCategory()
    {
        $cond = [
            'status' => 'publish',
        ];

        $list_category = $this->loadModel('Categories')
            ->find('all')
            ->where($cond)
            ->order(['id DESC']);

        $this->set('count', $list_category->count());
        $this->set('list_category', $list_category);
    }

    protected function _getQueryIndex()
    {
        return $this->_getQuery();
    }
    protected function _getQuery()
    {
        $query = [];

        $query['sch_page_id'] = $this->request->getQuery('sch_page_id');
        if (!$query['sch_page_id']) {
            $this->redirect('/user/');
        }

        $query['sch_category_id'] = $this->request->getQuery('sch_category_id');
        if (!$query['sch_category_id']) {
            if ($this->isCategorySort($query['sch_page_id'])) {
                $category = $this->Categories->find()->where(['Categories.page_config_id' => $query['sch_page_id']])->order(['Categories.position' => 'ASC'])->first();
                if (!empty($category)) {
                    $query['sch_category_id'] = $category->id;
                }
            } else {
                $query['sch_category_id'] = 0;
            }
        }

        $query['pos'] = $this->request->getQuery('pos');
        if (empty($query['pos'])) {
            $query['pos'] = 0;
        }

        $query['page'] = $this->request->getQuery('page');
        if (empty($query['page'])) {
            unset($query['page']);
        }

        return $query;
    }

    private function _getConditions($query)
    {
        $cond = [];

        $cond['Infos.page_config_id'] = $query['sch_page_id'];

        if ($query['sch_category_id']) {
            $cond['Infos.category_id'] = $query['sch_category_id'];
        }

        extract($query);


        return $cond;
    }
    public function preview($id = 0)
    {

        // プレビューで保存した画像とファイルを削除
        $this->deletePreviewAttachment();

        // 画像とファイルをプレビューフォルダへコピー
        if ($id) {
            $this->distAttachmentCopy($id);
        }

        // Previewテーブルのセット
        $this->Infos->setTable('preview_infos');
        $this->InfoContents->setTable('preview_info_contents');
        $this->InfoTags->setTable('preview_info_tags');
        if ($this->InfoContents->behaviors()->has('FileAttache')) {
            $this->InfoContents->behaviors()->get('FileAttache')->config([
                'uploadDir' => UPLOAD_DIR . 'PreviewInfoContents',
                'wwwUploadDir' => '/' . UPLOAD_BASE_URL . '/' . 'PreviewInfoContents'
            ]);
        }

        $this->checkLogin();

        if (!$this->request->is(['post', 'put'])) {
            $is_valid = false;
            goto EDIT_RENDER;
        }

        $id = 0;
        $this->request->data['id'] = null;

        $is_valid = true;
        $validate = 'default';

        $query = $this->_getQuery();
        $sch_page_id = $query['sch_page_id'];

        $redirect = ['action' => 'index', '?' => $query];

        // 過去のプレビュー削除
        $this->deletePreviewSource($sch_page_id);

        $this->setList();

        $options = [
            // 'saveAll' => ['associated' => ['InfoContents']], // save時使用
            'contain' => [
                'InfoContents' => function ($q) {
                    return $q->order('InfoContents.position ASC')->contain(['SectionSequences']);
                },
                'InfoTags' => function ($q) {
                    return $q->contain(['Tags'])->order(['Tags.position' => 'ASC']);
                }
            ] // find時使用
        ];

        $page_title = 'コンテンツ';
        $page_config = null;
        if ($sch_page_id) {
            $page_config = $this->PageConfigs->find()->where(['PageConfigs.id' => $sch_page_id])->contain(['SiteConfigs'])->first();
            if (!empty($page_config)) {
                $page_title = $page_config->page_title;
            }
        }

        // カテゴリリスト
        $category_list = [];
        if ($sch_page_id) {
            $category_list = $this->Categories->find('list', ['keyField' => 'id', 'valueField' => 'name'])
                ->where(['Categories.page_config_id' => $sch_page_id])
                ->order(['Categories.position' => 'ASC'])
                ->toArray();
        }



        $this->set(compact('page_title', 'query', 'page_config', 'category_list'));

        if ($this->request->is(['post', 'put'])) {

            if (!empty($this->request->getData('title')) && $page_config->slug == 'column') {
                $title = $this->request->getData('title');
                $this->request->data['title'] = strip_tags($title);
            }

            if (empty($this->request->getData('end_date'))) {
                $this->request->data['end_date'] = DATE_ZERO;
            }

            if (empty($this->request->getData())) {
                $this->Flash->error('アップロード出来る容量を超えました');
                $is_valid = false;
                goto EDIT_RENDER;
            }

            $this->request->data['page_config_id'] = $sch_page_id;

            // カテゴリ　バリデーション
            if ($this->isCategoryEnabled($page_config)) {
                $validate = 'isCategory';
            }
            // 並び順
            if (array_key_exists('info_contents', $this->request->getData())) {
                $position = 0;

                foreach ($this->request->getData('info_contents') as $k => $v) {
                    $this->request->data['info_contents'][$k]['position'] = ++$position;
                }
            }

            // 登録者
            if (!$id) {
                $this->request->data['regist_user_id'] = $this->isLogin();
            }

            // メタキーワード
            $meta_keywords = $this->request->getData('keywords');
            if (!empty($meta_keywords)) {
                $this->request->data['meta_keywords'] = '';
                $pre = '';
                foreach ($meta_keywords as $k => $v) {
                    $v = strip_tags(trim($v));
                    if (!empty($v)) {
                        $this->request->data['meta_keywords'] .= $pre . $v;
                        $pre = ',';
                    }
                }
            } else {
                $this->request->data['meta_keywords'] = '';
            }

            $delete_ids = $this->request->getData('delete_ids');
            unset($this->request->data['delete_ids']);

            $tags = $this->request->getData('tags');
            unset($this->request->data['tags']);

            // $contents = $this->request->getData('info_contents');
            // foreach ($contents as $k => $v) {
            //     if (array_key_exists('_serialize_values', $v) && !empty($v['_serialize_values'])) {
            //         $this->request->data["info_contents"][$k]['content'] = serialize($v['_serialize_values']);
            //     }
            // }


            $options['callback'] = function ($id) use ($delete_ids, $tags, $page_config) {
                // コンテンツ削除


                // 枠の紐付け
                $q = $this->InfoContents->find()->where(['InfoContents.info_id' => $id])->order(['position' => 'ASC']);
                if (!$q->isEmpty()) {
                    $info_contents = $q->all();
                    foreach ($info_contents as $v) {
                        if (array_key_exists((int) $v['block_type'], Info::BLOCK_TYPE_WAKU_LIST)) {
                            $section_query = $this->SectionSequences->find()->where(['SectionSequences.id' => $v['section_sequence_id']]);
                            if ($section_query->isEmpty()) {
                                continue;
                            }
                            $section_entity = $section_query->first();
                            $section_entity->info_content_id = $v['id'];
                            $this->SectionSequences->save($section_entity);
                        }
                    }
                }

                // タグ
                $tag_ids = $this->saveTags($page_config->id, $tags); // マスターの登録
                if (!empty($tag_ids)) {
                    foreach ($tag_ids as $tag_id) {
                        $info_tag = $this->InfoTags->find()->where(['InfoTags.tag_id' => $tag_id, 'InfoTags.info_id' => $id])->first();
                        if (empty($info_tag)) {
                            $info_tag = $this->InfoTags->newEntity();
                            $info_tag->info_id = $id;
                            $info_tag->tag_id = $tag_id;
                            $this->InfoTags->save($info_tag);
                        }
                    }
                }
                // タグの削除


                $url = ($page_config->site_config->slug ? '/' . $page_config->site_config->slug : '');
                $url .= ($page_config->slug ? '/' . $page_config->slug : '');
                $url .= '/';
                return $this->redirect($url . 'pre-' . $id . '.html?preview=on');
            };
        } else {
            if (!$id) {
                $options['get_callback'] = function ($data) use ($query) {
                    $data['category_id'] = $query['sch_category_id'];
                    return $data;
                };
            }
        }

        $options['redirect'] = $redirect;
        $options['validate'] = $validate;

        $result = parent::_edit($id, $options);
        $this->Session->delete('Flash');

        if ($result === false) {
            $is_valid = false;
        }

        EDIT_RENDER: if ($query['sch_page_id'] == 3) {
            $this->render('editFaq');
        }

        if (!$is_valid) {
            return $this->render('error');
        }
    }
    public function editItem($id = 0)
    {

        $this->checkLogin();

        if ($this->request->getData('postMode') == 'preview') {
            return $this->preview($id);
        }

        $validate = 'default';

        if ($id && !$this->isOwnInfoByUser($id)) {
            $this->Flash->set('不正なアクセスです');
            $this->redirect('/user/');
            return;
        }

        $query = $this->_getQuery();
        $sch_page_id = $query['sch_page_id'];


        $this->setList();

        $options = [
            // 'saveAll' => ['associated' => ['InfoContents']], // save時使用
            'contain' => [
                'InfoContents' => function ($q) {
                    return $q->order('InfoContents.position ASC')->contain(['SectionSequences']);
                },
                'InfoTags' => function ($q) {
                    return $q->contain(['Tags'])->order(['Tags.position' => 'ASC']);
                },
                'InfoAppendItems' => function ($q) {
                    return $q->contain(['AppendItems'])->order(['AppendItems.position' => 'ASC']);
                }
            ] // find時使用
        ];

        $page_title = 'コンテンツ';
        $page_config = null;
        if ($sch_page_id) {
            $page_config = $this->PageConfigs->find()->where(['PageConfigs.id' => $sch_page_id])->first();
            if (!empty($page_config)) {
                $page_title = $page_config->page_title;
            }
        }

        // カテゴリリスト
        $category_list = [];
        if ($sch_page_id) {
            $category_list = $this->Categories->find('list', ['keyField' => 'id', 'valueField' => 'name'])
                ->order(['Categories.position' => 'ASC'])
                ->toArray();
        }

        // 追加入力項目
        $append_list = [];
        if ($sch_page_id) {
            $append_list = $this->AppendItems->find()->where(['page_config_id' => $sch_page_id])->order('position asc')->all();
        }

        $append_item_list = $this->getAppendList($sch_page_id);

        $this->set(compact('page_title', 'query', 'page_config', 'category_list', 'append_list'));

        if ($this->request->is(['post', 'put'])) {
            if (empty($this->request->getData())) {
                $this->Flash->error('アップロード出来る容量を超えました');
                return $this->redirect(['action' => 'edit', $id, '?' => $query]);
            }

            $this->request->data['page_config_id'] = $sch_page_id;

            // カテゴリ　バリデーション
            if ($this->isCategoryEnabled($page_config)) {
                $validate = 'isCategory';
            }
            // 並び順
            if (array_key_exists('info_contents', $this->request->getData())) {
                $position = 0;

                foreach ($this->request->getData('info_contents') as $k => $v) {
                    $this->request->data['info_contents'][$k]['position'] = ++$position;
                }
            }

            // 登録者
            if (!$id) {
                $this->request->data['regist_user_id'] = $this->isLogin();
            }

            // メタキーワード
            $meta_keywords = $this->request->getData('keywords');
            if (!empty($meta_keywords)) {
                $this->request->data['meta_keywords'] = '';
                $pre = '';
                foreach ($meta_keywords as $k => $v) {
                    $v = strip_tags(trim($v));
                    if (!empty($v)) {
                        $this->request->data['meta_keywords'] .= $pre . $v;
                        $pre = ',';
                    }
                }
            } else {
                $this->request->data['meta_keywords'] = '';
            }

            $delete_ids = $this->request->getData('delete_ids');
            unset($this->request->data['delete_ids']);

            $tags = $this->request->getData('tags');
            unset($this->request->data['tags']);

            // infoAppendItemsがある場合
            if (array_key_exists('info_append_items', $this->request->getData())) {
                foreach ($this->request->getData('info_append_items') as $ap_num => $i_append_item) {
                    // 必須でないリスト対策
                    if (empty($i_append_item['value_int'])) {
                        $this->request->data['info_append_items'][$ap_num]['value_int'] = 0;
                    }
                }
            }

            $options['callback'] = function ($id) use ($delete_ids, $tags, $page_config) {
                // コンテンツ削除
                if ($id && $delete_ids) {

                    $sub_delete_ids = [];
                    foreach ($delete_ids as $del_id) {
                        $sub_delete_ids = $this->content_delete($id, $del_id);
                        // 枠ごと削除した場合の中身のコンテンツ削除
                        if (!empty($sub_delete_ids)) {
                            foreach ($sub_delete_ids as $sub_del_id) {
                                $this->content_delete($id, $sub_del_id);
                            }
                        }
                    }
                }

                // 枠の紐付け
                $q = $this->InfoContents->find()->where(['InfoContents.info_id' => $id])->order(['position' => 'ASC']);
                if (!$q->isEmpty()) {
                    $info_contents = $q->all();
                    foreach ($info_contents as $v) {
                        if (array_key_exists((int) $v['block_type'], Info::BLOCK_TYPE_WAKU_LIST)) {
                            $section_query = $this->SectionSequences->find()->where(['SectionSequences.id' => $v['section_sequence_id']]);
                            if ($section_query->isEmpty()) {
                                continue;
                            }
                            $section_entity = $section_query->first();
                            $section_entity->info_content_id = $v['id'];
                            $this->SectionSequences->save($section_entity);
                        }
                    }
                }

                // タグ
                $tag_ids = $this->saveTags($page_config->id, $tags); // マスターの登録
                if (!empty($tag_ids)) {
                    foreach ($tag_ids as $tag_id) {
                        $info_tag = $this->InfoTags->find()->where(['InfoTags.tag_id' => $tag_id, 'InfoTags.info_id' => $id])->first();
                        if (empty($info_tag)) {
                            $info_tag = $this->InfoTags->newEntity();
                            $info_tag->info_id = $id;
                            $info_tag->tag_id = $tag_id;
                            $this->InfoTags->save($info_tag);
                        }
                    }
                }
                // タグの削除
                if (empty($tag_ids)) {
                    $this->InfoTags->deleteAll(['InfoTags.info_id' => $id]);
                } else {
                    $this->InfoTags->deleteAll(['InfoTags.info_id' => $id, 'InfoTags.tag_id not in' => $tag_ids]);
                }

                // HTML更新
                $this->_htmlUpdate($id);
            };
        } else {
            if (!$id) {
                $options['get_callback'] = function ($data) use ($query) {
                    $data['category_id'] = $query['sch_category_id'];
                    return $data;
                };
            }
        }

        $options['append_validate'] = function ($isValid, $entity) use ($page_config) {
            // infoAppendItemsのバリデーション
            $isValid = true;
            if (!empty($entity['info_append_items'])) {
                $val_iAItems = $this->validInfoAppendItems($entity, $page_config);
                if (!$val_iAItems) {
                    $isValid = false;
                }
            }
            return $isValid;
        };
        $options['associated'] = ['InfoAppendItems', 'InfoContents'];
        $options['redirect'] = ['action' => 'index', '?' => $query];
        $options['validate'] = $validate;

        parent::_edit($id, $options);
    }

    

    public function delete($id, $type, $columns = null)
    {
        $this->checkLogin();

        if (!$this->isOwnInfoByUser($id)) {
            $this->Flash->set('不正なアクセスです');
            $this->redirect('/user/');
            return;
        }

        $options = [];

        $data = $this->Infos->find()
            ->where(['Infos.id' => $id])
            ->contain([
                'PageConfigs' => function ($q) {
                    return $q->select(['slug', 'site_config_id', 'page_title']);
                },
                'InfoAppendItems',
                'InfoContents'
            ])
            ->first();
        if (empty($data)) {
            $this->redirect(['action' => 'index']);
            return;
        }
        if ($type == 'content') {
            $options['redirect'] = ['action' => 'index', '?' => ['sch_page_id' => $data->page_config_id, 'sch_category_id' => $data->category_id]];
        } else {
            $options['redirect'] = ['action' => 'edit', $id, '?' => ['sch_page_id' => $data->page_config_id, 'sch_category_id' => $data->category_id]];
        }

        if ($type == "content") {
            if (!empty($data->info_append_items)) {
                foreach ($data->info_append_items as $sub) {
                    $this->append_delete($id, $sub->id);
                }
            }

            if (!empty($data->info_contents)) {
                foreach ($data->info_contents as $sub) {
                    $this->content_delete($id, $sub->id);
                }
            }
        }
        parent::_delete($id, $type, $columns, $options);

        // $this->_htmlDelete($id, $data);
    }

    public function position($id, $pos)
    {
        $this->checkLogin();

        if (!$this->isOwnInfoByUser($id)) {
            $this->Flash->set('不正なアクセスです');
            $this->redirect('/user/');
            return;
        }

        $query = $this->_getQueryIndex();

        $options = [];

        $data = $this->Infos->find()->where(['Infos.id' => $id])->first();
        if (empty($data)) {
            $this->redirect(['action' => 'index']);
            return;
        }

        if (!$this->isCategorySort($data->page_config_id)) {
            unset($query['sch_category_id']);
        }
        $options['redirect'] = ['action' => 'index', '?' => $query];

        return parent::_position($id, $pos, $options);
    }

    public function enable($id)
    {
        $this->checkLogin();

        if (!$this->isOwnInfoByUser($id)) {
            $this->Flash->set('不正なアクセスです');
            $this->redirect('/user/');
            return;
        }

        $options = [];

        $data = $this->Infos->find()->where(['Infos.id' => $id])->contain(['PageConfigs'])->first();
        if (empty($data)) {
            $this->redirect(['action' => 'index']);
            return;
        }

        $page_config_id = $this->request->getQuery('sch_page_id');
        $category_id = $this->request->getQuery('sch_category_id');
        $pos = $this->request->getQuery('pos');
        $page = $this->request->getQuery('page');
        if (empty($pos)) {
            $pos = 0;
        }

        if ($this->isCategoryEnabled($data->page_config) && $data->category_id == 0 && $data->status == 'draft') {
            $this->Flash->set('カテゴリが未設定の記事は公開できません');
            $this->redirect(['action' => 'index', '?' => ['sch_page_id' => $page_config_id, 'sch_category_id' => $category_id]]);
            return;
        }

        $options['redirect'] = ['action' => 'index', '?' => ['sch_page_id' => $page_config_id, 'sch_category_id' => $category_id, 'pos' => $pos, 'page' => $page]];

        parent::_enable($id, $options);

        $this->_htmlUpdate($id);
    }

    public function setList()
    {

        $list = array();

        $list['block_type_list'] = $this->array_asso_chunk(Info::getBlockTypeList(), 4);
        $list['block_type_waku_list'] = Info::getBlockTypeList('waku');
        $list['font_list'] = Info::$font_list;

        $current_site_id = $this->Session->read('current_site_id');
        $list['page_config_list'] = $this->PageConfigs->find('list', ['keyField' => 'id', 'valueField' => 'page_title'])->where(['PageConfigs.site_config_id' => $current_site_id])->toArray();

        $list['out_waku_list'] = Info::$out_waku_list;
        $list['line_style_list'] = Info::$line_style_list;
        $list['line_color_list'] = Info::$line_color_list;
        $list['line_width_list'] = Info::$line_width_list;
        $list['waku_style_list'] = Info::$waku_style_list;
        $list['waku_color_list'] = Info::$waku_color_list;
        $list['waku_bgcolor_list'] = Info::$waku_bgcolor_list;
        $list['button_color_list'] = Info::$button_color_list;
        $list['content_liststyle_list'] = Info::$content_liststyle_list;
        $list['link_target_list'] = Info::$link_target_list;

        $list['placeholder_list'] = AppendItem::$placeholder_list;
        $list['notes_list'] = AppendItem::$notes_list;

        if (!empty($list)) {
            $this->set(array_keys($list), $list);
        }

        $PageConfig = new PageConfig;
        $this->set('PageConfig', $PageConfig);

        $this->list = $list;
        return $list;
    }

    public function addRow()
    {
        $this->viewBuilder()->setLayout("plain");

        $this->setList();

        $rownum = $this->request->getData('rownum');
        $data['block_type'] = $this->request->getData('block_type');

        $entity = $this->InfoContents->newEntity($data);
        $entity->id = null;
        $entity->position = 0;
        $entity->block_type = $data['block_type'];
        $entity->section_sequence_id = 0;
        $entity->option_value = "";
        $entity->option_value2 = "";
        $entity->option_value3 = "";
        $entity->image_pos = "";
        $entity->title = "";

        if ($this->request->getData('section_no')) {
            $entity->section_sequence_id = $this->request->getData('section_no');
        }

        if (array_key_exists((int) $data['block_type'], Info::BLOCK_TYPE_WAKU_LIST)) {
            $entity->section_sequence_id = $this->SectionSequences->createNumber();
            if (array_key_exists($data['block_type'], Info::$option_default_values)) {
                $entity->option_value = Info::$option_default_values[$data['block_type']];
            }
        }
        if ($data['block_type'] == Info::BLOCK_TYPE_SECTION_WITH_IMAGE) {
            $entity->image_pos = 'left';
        }


        $datas = $entity->toArray();

        $this->set(compact('rownum', 'datas'));
    }

    public function addTag()
    {
        $this->viewBuilder()->setLayout("plain");

        $num = $this->request->getData('num');
        $tag = $this->request->getData('tag');
        $tag = strip_tags(trim($tag));

        // $entity = $this->Tags->find()
        //                      ->where(['Tags.tag' => $tag])
        //                      ->first();

        $this->set(compact('tag', 'num'));
    }

    private function content_delete($id, $del_id)
    {
        $q = $this->InfoContents->find()->where(['InfoContents.id' => $del_id, 'InfoContents.info_id' => $id]);
        $e = $q->first();

        $sub_delete_ids = [];

        if (array_key_exists((int) $e->block_type, Info::BLOCK_TYPE_WAKU_LIST) && $e->section_sequence_id > 0) {
            $sub_delete_ids = $this->InfoContents->find()
                ->where(
                    [
                        'InfoContents.section_sequence_id' => $e->section_sequence_id,
                        'InfoContents.id !=' => $del_id,
                        'InfoContents.info_id' => $id
                    ]
                )
                ->extract('id');
        }

        $image_index = array_keys($this->InfoContents->attaches['images']);
        $file_index = array_keys($this->InfoContents->attaches['files']);

        foreach ($image_index as $idx) {
            foreach ($e->attaches[$idx] as $_) {
                $_file = WWW_ROOT . $_;
                if (is_file($_file)) {
                    @unlink($_file);
                }
            }
        }

        foreach ($file_index as $idx) {
            $_file = WWW_ROOT . $e->attaches[$idx][0];
            if (is_file($_file)) {
                @unlink($_file);
            }
        }
        $this->InfoContents->delete($e);

        return $sub_delete_ids;
    }

    public function htmlUpdateAll($page_config_id, $category_id = 0)
    {

        $cond = [
            'Infos.page_config_id' => $page_config_id
        ];
        if ($category_id) {
            $cond['Infos.category_id'] = $category_id;
        }

        $infos = $this->Infos->find()
            ->where($cond)
            ->contain([
                'Categories' => function ($q) {
                    return $q->select(['status']);
                },
                'PageConfigs' => function ($q) {
                    return $q->select(['slug', 'site_config_id']);
                }
            ])
            ->all();

        if (!empty($infos)) {
            foreach ($infos as $info) {
                if ($info->category->status == 'publish') {
                    if ($info->status == 'publish') {
                        $this->_htmlUpdate($info->id);
                    } else {
                        $this->_htmlDelete($info->id, $info);
                    }
                } else {
                    $this->_htmlDelete($info->id, $info);
                }
            }
        }

        $this->redirect($this->referer());
    }

    public function _htmlDelete($info_id, $entity)
    {

        $site_config = $this->SiteConfigs->find()->where(['SiteConfigs.id' => $entity->page_config->site_config_id])->first();
        $slug = $site_config->slug . DS . $entity->page_config->slug;
        $this->OutputHtml->detail('Infos', $info_id, $slug);
    }

    public function _htmlUpdate($info_id)
    {

        $info = $this->Infos->find()->where(['Infos.id' => $info_id])->contain(['PageConfigs'])->first();

        $site_config = $this->SiteConfigs->find()->where(['SiteConfigs.id' => $info->page_config->site_config_id])->first();

        $slug = $site_config->slug . DS . ($info->page_config->slug ?: HOME_DATA_NAME);

        $this->OutputHtml->detail('Infos', $info_id, $slug);
        if (!empty($info)) {
            $this->createDetailJson($info_id);
        }
    }


    public function createDetailJson($info_id, $is_create = true)
    {

        $user_id = $this->isLogin();

        $options = [
            'contain' => [
                'InfoContents' => function ($q) {
                    return $q->order('InfoContents.position ASC')->contain(['SectionSequences']);
                },
                'PageConfigs' => function ($q) {
                    return $q->select(['slug', 'site_config_id', 'is_category', 'is_public_date', 'link_color']);
                },
                'Categories' => function ($q) {
                    return $q->select(['id', 'name', 'identifier']);
                }
            ]
        ];

        $this->_detail($info_id, $options);

        $contents = $this->viewVars['contents'];
        $info = $this->viewVars['entity'];
        $status = $info->status;

        $data = [
            'id' => $info->id,
            'date' => ($info->start_date ? $info->start_date->format('Y.m.d') . ' ' . Info::getWeekStr($info->start_date->format('w')) : ''),
            'title' => $info->title,
            'image' => $info->attaches['image'][0],
            'overview' => nl2br(strip_tags($info->notes)),
            'index_type' => $info->index_type,
            'link_color' => $info->page_config->link_color
        ];



        if ($info->page_config->is_public_date) {
            $data['end_date'] = '';
            if (!empty($info->end_date) && $info->end_date != DATE_ZERO) {
                $data['end_date'] = $info->end_date->format('Y-m-d') . ' ' . Info::getWeekStr($info->end_date->format('w'));
            }
        }

        if ($this->isCategoryEnabled($info->page_config)) {
            $data['category_id'] = $info->category->id;
            $data['category_name'] = $info->category->name;
            $data['category_style'] = $info->category->identifier;
            if ($info->category->status == 'draft') {
                $status = 'draft';
            }
        }
        $data['contents'] = [];

        foreach ($contents['contents'] as $k => $c) {
            $d = [];
            $d['position'] = $c['position'];
            $d['block_type'] = $c['block_type'];

            $_d = $this->setContents($c);
            if ($_d === false) {
                continue;
            }
            $d += $_d;
            if (array_key_exists('sub_contents', $c) && array_key_exists((int) $d['block_type'], Info::BLOCK_TYPE_WAKU_LIST)) {
                foreach ($c['sub_contents'] as $sub) {
                    $dd = [];
                    $dd['position'] = $sub['position'];
                    $dd['block_type'] = $sub['block_type'];
                    $_dd = $this->setContents($sub, $d['block_type']);
                    if ($_dd === false) {
                        continue;
                    }
                    $dd += $_dd;
                    $d['sub_contents'][] = $dd;
                }
            }
            $data['contents'][] = $d;
        }

        $site_config = $this->SiteConfigs->find()->where(['SiteConfigs.id' => $info->page_config->site_config_id])->first();
        $slug_dir = $site_config->slug . DS;
        if ($info->page_config->slug) {
            $slug_dir .= $info->page_config->slug . DS;
            $slug_dir = str_replace('__', '/', $slug_dir);
        }

        // タグ
        $data['info_tags'] = [];
        $tags = $this->InfoTags->find()->where(['InfoTags.info_id' => $info_id, 'Tags.status' => 'publish'])->contain(['Tags'])->all();
        if (!empty($tags)) {
            foreach ($tags as $t) {
                $data['info_tags'][] = [
                    'tag' => $t->tag->tag,
                    'link' => "/{$slug_dir}?tag={$t->tag->tag}"
                ];
            }
        }
        // pr($data);exit;
        $result = [
            'result' => ['code' => 0],
            'data' => $data
        ];

        if ($is_create) {
            $this->OutputHtml->writeJson($result, $info_id, $status, $site_config->slug . DS . ($info->page_config->slug ?: HOME_DATA_NAME));
        }

        return $data;
    }

    private function setContents($content, $parentBlockType = 0)
    {
        $data = [];

        switch ($content['block_type']) {
            case Info::BLOCK_TYPE_TITLE: // タイトル
            case Info::BLOCK_TYPE_TITLE_H4: // タイトル
                $data['title'] = $content['title'];
                $data['font_name'] = $content['option_value'];
                break;

            case Info::BLOCK_TYPE_CONTENT: // 本文
                $data['content'] = $content['content'];
                $data['font_name'] = $content['option_value'];
                $data['list_style'] = $content['option_value2'];
                break;

            case Info::BLOCK_TYPE_IMAGE: // 画像
                $data['content'] = Hash::get($content, 'attaches.image.0');
                $data['link'] = $content['content'];
                $data['target'] = $content['option_value'];
                break;

            case Info::BLOCK_TYPE_FILE: // ファイル
                $data['src'] = '';
                $data['file_name'] = '';
                $data['file_size'] = 0;
                if (Hash::get($content, 'attaches.file.src')) {
                    $data['src'] = '/contents' . Hash::get($content, 'attaches.file.download') . 'file.' . Hash::get($content, 'file_extension');
                    $data['file_name'] = (Hash::get($content, 'file_name') ?: '添付ファイル') . '.' . $content['file_extension'];
                    $data['file_size'] = $this->byte_format($content['file_size']);
                } else {
                    return false;
                }
                break;

            case Info::BLOCK_TYPE_RELATION: // 関連記事
                $data['title'] = nl2br($content['content']);
                $data['text'] = nl2br($content['option_value2']);
                $data['image'] = Hash::get($content, 'attaches.image.0');
                // $data['content'] = $content['content'];
                $data['link'] = $content['option_value'];
                break;

            case Info::BLOCK_TYPE_BUTTON: // リンクボタン
                $data['name'] = $content['title'];
                $data['link'] = $content['content'];
                $data['button_color'] = $content['option_value'];
                $data['target'] = $content['option_value2'];
                break;

            case Info::BLOCK_TYPE_LINE: // 区切り線
                $data['line_style'] = $content['option_value'];
                $data['line_color'] = $content['option_value2'];
                $data['line_width'] = $content['option_value3'];
                break;

            case Info::BLOCK_TYPE_SECTION: // 枠
                $data['b_style'] = $content['option_value'];
                if ($data['b_style'] == 'waku_style_6') {
                    $data['bg_color'] = $content['option_value2'];
                } else {
                    $data['b_color'] = $content['option_value2'];
                }
                $data['b_width'] = $content['option_value3'];

            case Info::BLOCK_TYPE_SECTION_RELATION: // 関連記事枠
                $data['sub_contents'] = [];
                break;

            case Info::BLOCK_TYPE_SECTION_FILE: // ファイル枠
                $data['title'] = strip_tags($content['title']);
                $data['sub_contents'] = [];
                break;

            case Info::BLOCK_TYPE_SECTION_WITH_IMAGE: // 画像回り込み用　枠
                $data['image'] = Hash::get($content, "attaches.image.0");
                $data['image_pos'] = $content['image_pos'];
                $data['image_link'] = $content['option_value3'];
                $data['title'] = $content['title'];
                $data['content'] = $content['content'];
                $data['font_name'] = $content['option_value'];
                $data['list_style'] = $content['option_value2'];

                break;

            default:
                # code...
                break;
        }

        return $data;
    }

    private function byte_format($size)
    {
        $b = 1024;    // バイト
        $mb = pow($b, 2);   // メガバイト
        $gb = pow($b, 3);   // ギガバイト

        switch (true) {
            case $size >= $gb:
                $target = $gb;
                $unit = 'GB';
                break;
            case $size >= $mb:
                $target = $mb;
                $unit = 'MB';
                break;
            default:
                $target = $b;
                $unit = 'KB';
                break;
        }

        $new_size = round($size / $target, 2);
        $file_size = number_format($new_size, 2, '.', ',') . $unit;

        return $file_size;
    }


    public function toHierarchization($id, $entity, $options = [])
    {
        // 枠ブロックとして認識させる番号を指定
        $options['section_block_ids'] = array_keys(Info::BLOCK_TYPE_WAKU_LIST);
        return parent::toHierarchization($id, $entity, $options);
    }

    private function saveTags($page_config_id, $tags)
    {
        $ids = [];
        if (!empty($tags)) {
            foreach ($tags as $t) {
                $tag = strip_tags(trim($t['tag']));
                $entity = $this->Tags->find()->where(['Tags.tag' => $tag, 'Tags.page_config_id' => $page_config_id])->first();
                if (empty($entity)) {
                    $entity = $this->Tags->newEntity();
                    $entity->tag = $tag;
                    $entity->status = 'publish';
                    $entity->page_config_id = $page_config_id;

                    $this->Tags->save($entity);
                }
                $ids[] = $entity->id;
            }
        }
        return $ids;
    }

    public function popTaglist()
    {
        $this->viewBuilder()->setLayout("pop");

        $page_config_id = $this->request->getQuery('page_config_id');

        $cond = [
            'Tags.page_config_id' => $page_config_id
        ];

        $query = $this->Tags->find();
        $sql = $query->select(['id', 'tag', 'cnt' => $query->func()->count('InfoTags.id')])
            ->where($cond)
            ->leftJoinWith('InfoTags')
            ->group('Tags.id')
            // ->enableAutoFields(true)
            ->order(['cnt' => 'DESC']);

        $this->modelName = 'Tags';
        $this->_lists($cond, [
            'limit' => 10,
            'order' => ['Tags.position' => 'ASC'],
            'sql_query' => $sql
        ]);
    }

    public function distAttachmentCopy($id)
    {

        if ($this->Infos->getTable() !== 'infos') {
            return;
        }
        if ($this->InfoContents->getTable() !== 'info_contents') {
            return;
        }

        $_data = $this->Infos->find()->where(['Infos.id' => $id])->contain(['InfoContents'])->first();

        if (!$id || empty($_data)) {
            return;
        }

        $this->Infos->copyPreviewAttachement($_data->id, 'PreviewInfos');

        foreach ($_data->info_contents as $content) {
            $this->InfoContents->copyPreviewAttachement($content->id, 'PreviewInfoContents');
        }

        return;
    }

    public function deletePreviewSource($page_id)
    {
        $now = new \DateTime();

        if ($this->Infos->getTable() !== 'preview_infos') {
            return;
        }
        if ($this->InfoContents->getTable() !== 'preview_info_contents') {
            return;
        }

        $previews = $this->Infos->find()->where(['Infos.created <' => $now->format('Y-m-d 00:00:00'), 'Infos.page_config_id' => $page_id])->contain(['InfoContents'])->all();

        foreach ($previews as $prev) {
            if (!empty($prev->info_contents)) {
                foreach ($prev->info_contents as $content) {
                    $this->modelName = 'InfoContents';
                    // $this->_delete($content->id, 'content', null, ['redirect' => false]);
                    $this->InfoContents->delete($content);
                }
            }
            $this->modelName = 'Infos';
            // $this->_delete($prev->id, 'content', null, ['redirect' => false]);
            $this->Infos->delete($prev);
        }
    }

    private function deletePreviewAttachment()
    {
        $this->_deletePreviewImage();
        $this->_deletePreviewFile();
    }

    /**
     * プレビュー用の画像削除
     * @return [type] [description]
     */
    private function _deletePreviewImage()
    {
        $limit_dt = new \DatetIme('-24 hour');

        // PreviewInfos
        $image_dir = UPLOAD_DIR . 'PreviewInfos' . DS . 'images/*';

        $file_list = glob($image_dir, GLOB_BRACE);
        if (!empty($file_list)) {
            foreach ($file_list as $file) {
                if (is_file($file)) {
                    $unixdate = filemtime($file);
                    $filedate = date("YmdHis", $unixdate);

                    if ($filedate < $limit_dt->format('YmdHis')) {
                        @unlink($file);
                    }
                }
            }
        }

        // PreviewInfoContents
        $image_dir = UPLOAD_DIR . 'PreviewInfoContents' . DS . 'images/*';

        $file_list = glob($image_dir, GLOB_BRACE);
        if (!empty($file_list)) {
            foreach ($file_list as $file) {
                if (is_file($file)) {
                    $unixdate = filemtime($file);
                    $filedate = date("YmdHis", $unixdate);
                    if ($filedate < $limit_dt->format('YmdHis')) {
                        @unlink($file);
                    }
                }
            }
        }
    }

    /**
     * プレビュー用のファイルを削除
     * @return [type] [description]
     */
    private function _deletePreviewFile()
    {
        $limit_dt = new \DatetIme('-24 hour');

        // PreviewInfos
        $file_dir = UPLOAD_DIR . 'PreviewInfos' . DS . 'files/*';

        $file_list = glob($file_dir, GLOB_BRACE);
        if (!empty($file_list)) {
            foreach ($file_list as $file) {
                if (is_file($file)) {
                    $unixdate = filemtime($file);
                    $filedate = date("YmdHis", $unixdate);

                    if ($filedate < $limit_dt->format('YmdHis')) {
                        @unlink($file);
                    }
                }
            }
        }

        // PreviewInfoContents
        $file_dir = UPLOAD_DIR . 'PreviewInfoContents' . DS . 'files/*';

        $file_list = glob($file_dir, GLOB_BRACE);
        if (!empty($file_list)) {
            foreach ($file_list as $file) {
                if (is_file($file)) {
                    $unixdate = filemtime($file);
                    $filedate = date("YmdHis", $unixdate);
                    if ($filedate < $limit_dt->format('YmdHis')) {
                        @unlink($file);
                    }
                }
            }
        }
    }

    protected function getAppendList($config_id = 0, $list_bool = false)
    {
        $list = [];

        if (empty($config_id)) {
            return $list;
        }



        if ($list_bool) {
            $append_datas = $this->MstLists->find('list', [
                'keyField' => 'ltrl_val',
                'valueField' => 'ltrl_nm'
            ])
                ->order(['MstLists.position' => 'ASC'])
                ->toArray();
        } else {
            $append_datas = $this->MstLists->find()
                ->order(['MstLists.position' => 'ASC'])
                ->toArray();
        }


        if (empty($append_datas)) {
            return $list;
        }


        if ($list_bool) {
            return $append_datas;
        }

        foreach ($append_datas as $n => $_) {
            $list[$_['use_target_id']][$_['ltrl_val']] = $_['ltrl_nm'];
        }

        return $list;
    }

    /**
     * Undocumented function
     *
     * @param [type] $data formの元データ
     * @param [type] $page_config
     * @return bool
     */
    protected function validInfoAppendItems($data, $page_config)
    {
        $valid = false;

        if (empty($data['info_append_items'])) {
            return $valid;
        }

        // 追加バリデーション用id-slugリスト
        $append_for_additional_list = $this->AppendItems->find('list', [
            'keyField' => 'id',
            'valueField' => 'slug'
        ])
            ->toArray();



        // 必須項目リストの取得
        $contain = ['PageConfigs'];
        $cond = [
            'AppendItems.page_config_id' => $page_config->id,
            'AppendItems.is_required' => 1
        ];
        $require_append_list = $this->AppendItems->find()
            ->contain($contain)
            ->where($cond)
            ->order(['AppendItems.position' => 'ASC'])
            ->toArray();
        // empty以外のバリデーションチェック 
        if (empty($require_append_list)) {
            $r = true;
            foreach ($data['info_append_items'] as $n => $item) {
                $r = $this->additionalValidate($data, $item, $append_for_additional_list, $page_config->slug);
                if (!$r) {
                    return $valid;
                }
            }
            $valid = true;
            return $valid;
        }
        // [id => data],['slug' => id]化
        $r_list = [];
        $r_slug_list = [];
        foreach ($require_append_list as $ap) {
            $r_list[$ap['id']] = $ap;
            $r_slug_list[$ap['slug']][] = $ap['id'];
        }


        foreach ($data['info_append_items'] as $n => $item) {
            $r = true;
            if (in_array($item['append_item_id'], array_keys($r_list))) {
                // 項目別チェック
                $r = $this->validWithType($data, $item, $r_list[$item['append_item_id']], $r_slug_list, $page_config->slug);
                if ($r && !empty($append_for_additional_list)) {
                    // 追加項目に対して個別のバリデーションを入れたければユニークとなるslugを設定しここで記載
                    $r = $this->additionalValidate($data, $item, $append_for_additional_list, $page_config->slug);
                }
            }
            if (!$r) { //項目チェックがfalseならその時点でfalseを返す
                return $valid;
            }
        }

        if ($r) {
            $valid = true;
        }
        return $valid;
    }

    /**
     * Undocumented function
     *
     * @param [type] $entity formの元データ
     * @param [type] $data 評価中のinfo_append_itemデータ
     * @param [type] $append 評価中のappend_item項目データ
     * @param [type] $list append_itemの[slug,id]リスト
     * @param [type] $slug page_config->slug
     * @return bool
     */
    protected function validWithType($entity, $data, $append, $list, $slug)
    {
        $valid = true;
        // 空でないかどうか ----------------------------------
        // 数字型
        if ($append['value_type'] == AppendItem::TYPE_NUMBER) {
            if (empty($data['value_int']) && $data['value_int'] != 0) {
                $valid = false;
            }
        }
        // テキスト型
        if ($append['value_type'] == AppendItem::TYPE_TEXT) {
            if (empty($data['value_text'])) {
                $valid = false;
            }
        }
        // テキストエリア型
        if ($append['value_type'] == AppendItem::TYPE_TEXTAREA) {
            if (empty($data['value_textarea'])) {
                $valid = false;
            }
        }
        // 日付型
        if ($append['value_type'] == AppendItem::TYPE_DATE) {
            if (empty($data['value_date'])) {
                $valid = false;
            }
        }
        // list
        if ($append['value_type'] == AppendItem::TYPE_LIST) {
            if (empty($data['value_int'])) {
                $valid = false;
            }
        }
        // checkbox
        if ($append['value_type'] == AppendItem::TYPE_DECIMAL) {
            if (empty($data['value_decimal'])) {
                $valid = false;
            }
        }
        // radio
        if ($append['value_type'] == AppendItem::TYPE_RADIO) {
            if (empty($data['value_decimal'])) {
                $valid = false;
            }
        }
        // decimal
        if ($append['value_type'] == AppendItem::TYPE_DECIMAL) {
            if (empty($data['value_decimal'])) {
                $valid = false;
            }
        }
        // file
        if ($append['value_type'] == AppendItem::TYPE_FILE) {
            if (empty($data['_file']['size']) && empty($data['file_size'])) {
                $valid = false;
            }
        }
        // 画像
        if ($append['value_type'] == AppendItem::TYPE_IMAGE) {
            if (empty($data['image'])) {
                $valid = false;
            }
        }

        // エラーメッセージセット
        if (!$valid) {
            if (in_array($append['value_type'], [AppendItem::TYPE_TEXTAREA, AppendItem::TYPE_TEXT,])) {
                $entity->setErrors([
                    "{$slug}.{$append['slug']}" => [
                        'notempty' => '入力してください'
                    ]
                ]);
            }

            if (in_array($append['value_type'], [AppendItem::TYPE_RADIO, AppendItem::TYPE_LIST, AppendItem::TYPE_IMAGE, AppendItem::TYPE_FILE])) {
                $entity->setErrors([
                    "{$slug}.{$append['slug']}" => [
                        'notempty' => '選択してください'
                    ]
                ]);
            }
        }


        return $valid;
    }


    protected function additionalValidate($entity, $data, $list, $slug)
    {
        $valid = true;
        $append_slug = $list[$data['append_item_id']];

        return $valid;
    }


    private function append_delete($id, $del_id)
    {
        $q = $this->InfoAppendItems->find()->where(['InfoAppendItems.id' => $del_id, 'InfoAppendItems.info_id' => $id]);
        $e = $q->first();

        $image_index = array_keys($this->InfoAppendItems->attaches['images']);
        $file_index = array_keys($this->InfoAppendItems->attaches['files']);

        foreach ($image_index as $idx) {
            if (!empty($e[$idx])) {
                foreach ($e->attaches[$idx] as $_) {
                    $_file = WWW_ROOT . $_;
                    if (is_file($_file)) {
                        @unlink($_file);
                    }
                }
            }
        }
        foreach ($file_index as $idx) {
            if (!empty($e[$idx])) {
                $_file = WWW_ROOT . $e->attaches[$idx][0];
                if (is_file($_file)) {
                    @unlink($_file);
                }
            }
        }


        return $this->InfoAppendItems->delete($e);
    }
}
