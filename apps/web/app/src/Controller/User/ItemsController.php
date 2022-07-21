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


    //SAN PHAM CONTROLLER

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

    public function edit($id = 0)
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
                ->where(['Categories.page_config_id' => $sch_page_id])
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


    public function listCategory()
    {
        // $day = new \DateTime('now');
        // $day = $day->format('Y-m-d');
        $cond = [
            'status' => 'publish',
            // 'publish_at <=' => new \DateTime()
        ];

        $list_category = $this->loadModel('Categories')
            ->find('all')
            ->where($cond)
            ->order(['id DESC']);

        $this->set('count', $list_category->count());
        // $list_category = $list_category->toArray();
        $this->set('list_category', $list_category);
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
}

//
