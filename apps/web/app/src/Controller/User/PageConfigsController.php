<?php

namespace App\Controller\User;

use Cake\Event\Event;
use Cake\Filesystem\Folder;
use App\Model\Entity\PageConfig;


/**
 * Static content controller
 *
 * This controller will render views from Template/Pages/
 *
 * @link https://book.cakephp.org/3.0/en/controllers/pages-controller.html
 */
class PageConfigsController extends AppController
{
    private $list = [];

    public function initialize()
    {
        parent::initialize();

        $this->PageTemplates = $this->getTableLocator()->get('PageTemplates');
        $this->Infos = $this->getTableLocator()->get('Infos');
        $this->SiteConfigs = $this->getTableLocator()->get('SiteConfigs');
        $this->UserSites = $this->getTableLocator()->get('UserSites');

        $this->loadComponent('OutputHtml');

    }
    
    public function beforeFilter(Event $event) {

        parent::beforeFilter($event);
        // $this->viewBuilder()->theme('Admin');
        $this->viewBuilder()->setLayout("user");

        $this->setCommon();
        $this->getEventManager()->off($this->Csrf);

        $this->modelName = $this->name;
        $this->set('ModelName', $this->modelName);

    }

    protected function _getQuery() {
        $query = [];

        return $query;
    }

    protected function _getConditions($query) {
        $cond = [];

        extract($query);

        $cond['UserInfos.user_id'] = $this->userId;


        return $cond;
    }

    public function index() {
        $this->checkLogin();

        $this->setList();


        $current_site_id = $this->Session->read('current_site_id');
        $site_config = $this->SiteConfigs->find()->where(['SiteConfigs.id' => $current_site_id])->first();
        $this->set(compact('site_config'));

        $cond =['PageConfigs.site_config_id' => $current_site_id];

        $this->_lists($cond, ['order' => 'PageConfigs.position ASC',
                              'limit' => null]);
    }

    public function edit($id=0) {
        $this->checkLogin();

        if ($id && !$this->isOwnPageByUser($id)) {
            $this->Flash->set('不正なアクセスです');
            $this->redirect('/user/');
            return;
        }

        $this->setList();

        if ($this->request->is(['post', 'put'])) {
            if ($this->request->getData('is_category') == 'N') {
                // $this->request->withData('is_category_sort','N');
                $this->request->data['is_category_sort'] = 'N';
            }
        }

        $current_site_id = $this->Session->read('current_site_id');
        $site_config = $this->SiteConfigs->find()->where(['SiteConfigs.id' => $current_site_id])->first();

        $old_data = null;
        if ($id) {
            $old_data = $this->PageConfigs->find()->where(['PageConfigs.id' => $id])->first();
        }

        $this->set(compact('site_config'));

        $options['callback'] = function($id) use($old_data, $site_config) {
            $page_config = $this->PageConfigs->find()->where(['PageConfigs.id' => $id])->contain(['SiteConfigs'])->first();
            $content_dir = $page_config->slug;
            if (empty($page_config->slug)) {
                $content_dir = HOME_DATA_NAME;
            }

            $content_dir = $site_config->slug . DS . $content_dir;
            $content_dir = rtrim( $content_dir, '/' );

            if (!empty($old_data) && $old_data->slug != $page_config->slug) {
                if ($old_data->slug == "") {
                    // 新しいフォルダ作成
                    $Folder = new Folder();
                    $dir = USER_PAGES_DIR . $content_dir;
                    if (!is_dir($dir)) {
                        if (!$Folder->create($dir, 0777)) {
                            throw new Exception("フォルダを作成できませんでした", 1);
                        }
                    }

                    // サイトルートにあるフォルダを移動
                    $source = USER_PAGES_DIR . $site_config->slug . DS . USER_JSON_URL;
                    $Folder = new Folder($source);
                    if (!$Folder->move(USER_PAGES_DIR . $content_dir . DS . USER_JSON_URL)) {
                        throw new Exception("フォルダの移動に失敗しました", 1);
                    }
                    // サイトルートにあるファイルを移動
                    $source = USER_PAGES_DIR . $site_config->slug . DS;
                    $Folder = new Folder($source);
                    $files = $Folder->find('.*\.html');
                    if (!empty($files)) {
                        foreach ($files as $file) {
                            $file_path = $source . $file;
                            rename($file_path, USER_PAGES_DIR . $content_dir . DS . $file);
                        }
                    }

                } else {
                    $source = USER_PAGES_DIR . $site_config->slug . DS . $old_data->slug;
                    $source = rtrim( $source, '/' );
                    $Folder = new Folder($source);
                    if (!$Folder->move(USER_PAGES_DIR . $content_dir)) {
                        throw new Exception("フォルダの移動に失敗しました", 1);
                    }
                }

                $this->reCreateDetail($page_config->id, $content_dir);
            } else {

                $Folder = new Folder();
                // ユーザーフォルダの作成
                $dir = USER_PAGES_DIR . $content_dir;
                if (!is_dir($dir)) {
                    if (!$Folder->create($dir, 0777)) {
                        throw new Exception("フォルダを作成できませんでした", 1);
                    }
                }

                // dataフォルダ作成
                $dir = $dir . DS . USER_JSON_URL;
                if (!is_dir($dir)) {
                    if (!$Folder->create($dir, 0777)) {
                        throw new Exception("フォルダを作成できませんでした", 1);
                    }
                }
            }

            return $this->redirect(['controller' => 'page-configs', 'action' => 'index']);
            $this->writeIndex($content_dir);

            // // 詳細のHTMLを作り直す
            // $infos = $this->Infos->find()->where(['Infos.page_config_id' => $id])->select(['id'])->all();
            // if (!empty($infos)) {
            //     foreach ($infos as $k => $v) {
            //         $this->OutputHtml->detail('Infos', $v['id'], $content_dir);
            //     }
            // }
        };

        parent::_edit($id, $options);

    }
    public function reCreateDetail($page_config_id, $dir) {
        $infos = $this->Infos->find()->where(['Infos.page_config_id' => $page_config_id])->all();
        if (empty($infos)) {
            return;
        }

        foreach ($infos as $info) {
            $this->OutputHtml->detail('Infos', $info->id, $dir);
        }
        return;
    }
    public function writeIndex($slug) {
        $dir = USER_PAGES_DIR . $slug;
        $file = $dir . DS . "index.html";

        $params = explode('/', $slug); // [0]=site_name [1]=page_name

        if (count($params) < 2) {
            $params[] = '';
        }
        $html = $this->requestAction(
            ['controller' => 'Contents', 'action' => 'index', 'pass' => ['site_slug' => $params[0], 'slug' => $params[1]]],
            ['return', 'bare' => false]);

        file_put_contents($file, $html);

        chmod($file, 0666);

    }

    public function delete($id, $type, $columns = null) {
        $this->checkLogin();

        if (!$this->isOwnPageByUser($id)) {
            $this->Flash->set('不正なアクセスです');
            $this->redirect('/user/');
            return;
        }
        
        $options = [];
        // $options['redirect'] = ['action' => 'index'];

        parent::_delete($id, $type, $columns, $options);
    }

    public function position($id, $pos) {
        $this->checkLogin();

        if (!$this->isOwnPageByUser($id)) {
            $this->Flash->set('不正なアクセスです');
            $this->redirect('/user/');
            return;
        }

        return parent::_position($id, $pos);
    }


    public function setList() {
        
        $list = array();

        $list['template_list'] = $this->PageTemplates->find('list')->where(['PageTemplates.status' => 'publish'])->order('PageTemplates.position ASC')->toArray();

        $list['list_style_list'] = PageConfig::$list_styles;

        if (!empty($list)) {
            $this->set(array_keys($list),$list);
        }

        $this->list = $list;
        return $list;
    }


}
