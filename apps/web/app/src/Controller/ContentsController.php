<?php

namespace App\Controller;

use Cake\Http\Exception\NotFoundException;
use Cake\Event\Event;
use App\Model\Entity\Info;
use App\Controller\User\InfosController;

/**
 * Static content controller
 *
 * This controller will render views from Template/Pages/
 *
 * @link https://book.cakephp.org/3.0/en/controllers/pages-controller.html
 */
class ContentsController extends AppController
{
    private $list = [];


    public function initialize()
    {
        parent::initialize();

        $this->SiteConfigs = $this->getTableLocator()->get('SiteConfigs');
        $this->PageConfigs = $this->getTableLocator()->get('PageConfigs');
        $this->UserSites = $this->getTableLocator()->get('UserSites');
        $this->Infos = $this->getTableLocator()->get('Infos');
        $this->InfoContents = $this->getTableLocator()->get('InfoContents');
        $this->SectionSequences = $this->getTableLocator()->get('SectionSequences');
        $this->Categories = $this->getTableLocator()->get('Categories');
        $this->Tags = $this->getTableLocator()->get('Tags');
        $this->InfoTags = $this->getTableLocator()->get('InfoTags');

        $this->modelName = 'Infos';
        $this->set('ModelName', $this->modelName);

        $this->uid = $this->Session->read('uid');
    }


    public function beforeFilter(Event $event)
    {
        $this->viewBuilder()->setLayout("common");
        $this->getEventManager()->off($this->Csrf);
    }


    public function index($site_slug, $slug)
    {
        $cond = [];

        $site_config = $this->SiteConfigs->find()->where(['SiteConfigs.slug' => $site_slug])->first();
        $cond['PageConfigs.site_config_id'] = $site_config->id;

        if ($slug && $slug != HOME_DATA_NAME) {
            $cond['PageConfigs.slug'] = $slug;
        } else {
            $cond['PageConfigs.root_dir_type'] = 1;
        }

        $page_config = $this->PageConfigs->find()->where($cond)->first();
        if (empty($page_config)) {
            throw new NotFoundException('ページが見つかりません');
        }

        $this->set(compact('page_config', 'site_config'));

        $rootPath = '/';
        if ($site_config->slug) {
            $rootPath .= $site_config->slug . DS;
        }
        if ($page_config->slug) {
            $rootPath .= $page_config->slug . DS;
        }
        $rootPath = str_replace('__', '/', $rootPath);

        $this->set(compact('rootPath'));

        $view = $site_slug . DS . $page_config->slug . DS . 'index';
        $view = str_replace('__', '/', $view);
        $view_path = APP . 'Template' . DS . 'Contents' . DS . $view;
        if (file_exists($view_path . '.ctp')) {
            $this->render($view);
        } else {
            $view = $site_slug . DS . 'index';
            $view = str_replace('__', '/', $view);
            $view_path = APP . 'Template' . DS . 'Contents' . DS . $view;

            if (file_exists($view_path . '.ctp')) {
                $this->render($view);
            }
        }
    }


    public function home($site_slug, $id)
    {
        if (!$this->detail($site_slug, '', $id)) $this->render('detail');
    }


    /**
     * 参考用においてあるだけ
     * @param  [type] $site_slug [description]
     * @param  string $slug      [description]
     * @param  [type] $id        [description]
     * @return [type]            [description]
     */
    public function detailDptM($site_slug, $slug = 'home', $id)
    {
        $site_slug = 'dpt-m__' . $site_slug;
        $this->detail($site_slug, $slug, $id);

        $view = $site_slug . DS . $slug . DS . 'detail';
        $view = str_replace('__', '/', $view);
        $view_path = APP . 'Template' . DS . 'Contents' . DS . $view;
        if (file_exists($view_path . '.ctp')) {
            $this->render($view);
        } else {
            $view = $site_slug . DS . 'detail';
            $view = str_replace('__', '/', $view);
            $view_path = APP . 'Template' . DS . 'Contents' . DS . $view;

            if (file_exists($view_path . '.ctp')) {
                $this->render($view);
            }
        }
    }


    public function previewHome($site_slug, $id)
    {

        $this->preview($site_slug, '', $id);
        $this->render('detail');
    }


    public function _preview($id)
    {
        $this->Infos->setTable('preview_infos');
        $this->InfoContents->setTable('preview_info_contents');
        $this->InfoTags->setTable('preview_info_tags');
        if ($this->InfoContents->behaviors()->has('FileAttache')) {
            $this->InfoContents->behaviors()->get('FileAttache')->config([
                'uploadDir' => UPLOAD_DIR . 'PreviewInfoContents',
                'wwwUploadDir' => '/' . UPLOAD_BASE_URL . '/' . 'PreviewInfoContents'
            ]);
        }
        $id = preg_replace('/[^0-9]/', '', $id);

        return $id;
    }


    public function preview($site_slug, $slug = 'home', $id)
    {

        $id = $this->_preview($id);

        $this->detail($site_slug, $slug, $id);

        $this->render('detail');
    }


    public function detail($site_slug, $slug = 'home', $id)
    {
        $cond = [];

        $site_config = $this->SiteConfigs->find()->where(['SiteConfigs.slug' => $site_slug])->first();
        $cond['PageConfigs.site_config_id'] = $site_config->id;

        if ($slug && $slug != HOME_DATA_NAME && !is_numeric($slug)) $cond['PageConfigs.slug'] = $slug;
        else $cond['PageConfigs.root_dir_type'] = 1;

        $page_config = $this->PageConfigs->find()->where($cond)->first();
        if (empty($page_config)) throw new NotFoundException('ページが見つかりません');

        $entity = $this->_detail($id, $page_config->id, $this->isPreview);
        if (empty($entity)) throw new NotFoundException('ページが見つかりません');
        $options['section_block_ids'] = array_keys(Info::BLOCK_TYPE_WAKU_LIST);
        $data = $this->toHierarchization($id, $entity, $options);
        $this->set(compact('data', 'entity', 'page_config', 'site_config'));

        $rootPath = '/';
        if ($site_config->slug) $rootPath .= $site_config->slug . DS;
        if ($page_config->slug) $rootPath .= $page_config->slug . DS;

        $rootPath = str_replace('__', '/', $rootPath);
        $this->set(compact('rootPath'));

        $view_path = APP . 'Template' . DS . 'Contents' . DS . $site_slug . DS . 'detail';

        if (file_exists($view_path . '.ctp')) $this->render($site_slug . DS . 'detail');
        return true;
    }


    private function _detail($id, $page_config_id, $isPreview = false)
    {
        $id = preg_replace('/[^0-9]/', '', $id);
        $cond = [
            'Infos.page_config_id' => $page_config_id,
            'Infos.status' => 'publish',
            'Infos.id' => $id,
        ];

        $page_config = $this->PageConfigs->find()->where(['PageConfigs.id' => $page_config_id])->first();
        if ($this->isCategoryEnabled($page_config)) $cond['Categories.status'] = 'publish';

        if ($page_config->is_public_date) {
            $now = new \DateTime();
            $cond['Infos.start_date <='] = $now->format('Y-m-d');
            $cond['OR'] = [
                ['Infos.end_date' => DATE_ZERO],
                ['Infos.end_date !=' => DATE_ZERO, 'Infos.end_date >=' => $now->format('Y-m-d')]
            ];
        }

        if ($isPreview) {
            if ($this->isOwnInfoByUser($id)) {
                unset($cond['Infos.status']);
                unset($cond['Infos.start_date <=']);
                unset($cond['OR']);
            }
        }

        $contain = [
            'InfoContents' => function ($q) {
                return $q->order('InfoContents.position ASC')->contain(['SectionSequences']);
            },
            'Categories'
        ];

        return $this->Infos->find()->where($cond)->contain($contain)->first();
    }


    private function _getQuery()
    {
        $query = [];

        return $query;
    }


    public function ajaxDataTop($site_slug, $api_name)
    {
        $this->ajaxData($site_slug, '', $api_name);
    }


    public function ajaxDataDptM($slug1, $slug2, $api_name)
    {
        $site_slug = 'dpt-m' . '__' . $slug1;
        $page_slug = $slug2;

        $this->ajaxData($site_slug, $page_slug, $api_name);
    }


    public function ajaxData($site_slug, $slug, $api_name)
    {
        if (preg_match('/pre\-[0-9]+[(\.html)]?/', $api_name)) $api_name = $this->_preview($api_name);

        if (is_numeric($api_name)) {
            $this->loadComponent('OutputHtml');
            if (!$this->OutputHtml->_existsJson($api_name, $site_slug . DS . $slug)) {
                $cond = ['PageConfigs.slug' => $slug];
                $cond['Infos.id'] = $api_name;
                $info = $this->Infos->find()->where($cond)->contain(['PageConfigs'])->first();

                if (!empty($info)) {
                    $this->InfosController = new InfosController;
                    $data = $this->InfosController->createDetailJson($api_name, true);
                    $this->rest_success($data);
                } else throw new NotFoundException('ページが見つかりません');
            }
        } else $this->{$api_name}($site_slug, $slug);
    }


    public function getList($site_slug, $slug)
    {

        $category_id = $this->request->getQuery('category_id');

        $cond = [
            'PageConfigs.slug' => $slug,
            'SiteConfigs.slug' => $site_slug
        ];
        // if (!$slug || $slug == HOME_DATA_NAME) {
        //     $cond = ['PageConfigs.is_home' => 1];
        // }
        $page_config = $this->PageConfigs->find()->where($cond)->contain(['SiteConfigs'])->first();
        if (empty($page_config)) {
            $this->rest_error(1000);
            return;
        }

        $slug_dir = $site_slug . DS;
        if ($slug) {
            $slug_dir .= $slug . DS;
            $slug_dir = str_replace('__', '/', $slug_dir);
        }

        // if (!$slug || $slug == HOME_DATA_NAME) {
        //     $slug_dir = '';
        // }

        $datas = [
            'infos' => [],
            'categories' => $this->_getCategories($site_slug, $slug)
        ];

        $cond = [
            'Infos.page_config_id' => $page_config->id,
            'Infos.status' => 'publish',
        ];

        if ($page_config->is_public_date) {
            $now = new \DateTime();
            $cond['Infos.start_date <='] = $now->format('Y-m-d');
            $cond['OR'] = [
                ['Infos.end_date' => DATE_ZERO],
                ['Infos.end_date !=' => DATE_ZERO, 'Infos.end_date >=' => $now->format('Y-m-d')]
            ];
        }

        if ($this->isCategoryEnabled($page_config)) {
            $cond['Categories.status'] = 'publish';
        }

        if ($this->getCategoryEnabled() && $category_id) {
            $cond['Categories.id'] = $category_id;
        }

        $entities = $this->Infos->find()
            ->where($cond)
            ->order(['Infos.position' => 'ASC'])
            ->contain(['Categories'])
            ->all();

        if (!empty($entities)) {
            foreach ($entities as $k => $e) {
                $d = [];
                $d['id'] = $e->id;
                if ($this->isCategoryEnabled($page_config)) {
                    $d['category_id'] = $e->category_id;
                    $d['category_name'] = $e->category->name;
                    $d['category_style'] = $e->category->identifier;
                }
                $d['title'] = strip_tags($e->title);
                $d['image'] = $e->attaches['image']['s'];
                // $d['overview'] = nl2br($e->notes);
                $d['date'] = ($e->start_date ? $e->start_date->format('Y-m-d') : '');
                $d['link'] = "/{$slug_dir}{$e->id}.html";
                $d['list_view_type'] = $page_config->list_style;

                // タグ
                $d['info_tags'] = [];
                $tags = $this->InfoTags->find()->where(['InfoTags.info_id' => $e->id, 'Tags.status' => 'publish'])->contain(['Tags'])->all();
                if (!empty($tags)) {
                    foreach ($tags as $t) {
                        $d['info_tags'][] = [
                            'tag' => $t->tag->tag,
                            'link' => "/{$slug_dir}?tag={$t->tag->tag}"
                        ];
                    }
                }

                $datas['infos'][] = $d;
            }
        }
        $this->rest_success($datas);
    }


    public function getCategories($site_slug, $slug)
    {

        $datas = $this->_getCategories($site_slug, $slug);
        if ($datas === false) {
            $this->rest_error(1000);
            return;
        }

        $this->rest_success($datas);
    }


    public function getSiteCategories($site_slug)
    { }


    private function _getCategories($site_slug, $slug)
    {
        $cond = [
            'PageConfigs.slug' => $slug,
            'SiteConfigs.slug' => $site_slug
        ];

        $page_config = $this->PageConfigs->find()->where($cond)->contain(['SiteConfigs'])->first();

        if (empty($page_config)) {
            return false;
        }

        $entities = $this->Categories->find()->where(['Categories.page_config_id' => $page_config->id, 'Categories.status' => 'publish'])->order(['Categories.position' => 'ASC'])->all();
        $datas = [];

        if (!empty($entities)) {
            foreach ($entities as $e) {
                $d = [];
                $d['id'] = $e->id;
                $d['name'] = $e->name;
                $d['style'] = $e->identifier;

                $datas[] = $d;
            }
        }

        return $datas;
    }


    public function setList()
    {

        $list = array();

        $list['block_type_list'] = Info::getBlockTypeList();

        if (!empty($list)) {
            $this->set(array_keys($list), $list);
        }

        $this->list = $list;
        return $list;
    }


    public function file($id = 0, $column = null)
    {
        $model = 'InfoContents';

        $query = $this->{$model}->find()->where([$model . '.id' => $id]);
        if ($query->isEmpty()) {
            throw new NotFoundException();
        }

        $data = $query->first()->toArray();

        if (array_key_exists($column, $data)) {
            $file = WWW_ROOT . $data['attaches'][$column]['src'];
            $name = ($data['file_name'] ?: '添付ファイル') . '.' . $data['file_extension'];

            $content = 'attachment;';
            $content .= 'filename=' . $name . ';';
            $content .= 'filename*=UTF-8\'\'' . rawurlencode($name);

            if (file_exists($file)) {
                $this->response->header('Content-Disposition', $content);
                $this->response->file($file);
                return $this->response;
            }
        }

        throw new NotFoundException();
    }
}
