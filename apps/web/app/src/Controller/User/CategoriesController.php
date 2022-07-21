<?php

namespace App\Controller\User;

use Cake\Core\Configure;
use Cake\Network\Exception\ForbiddenException;
use Cake\Network\Exception\NotFoundException;
use Cake\View\Exception\MissingTemplateException;
use Cake\Event\Event;
use Cake\ORM\TableRegistry;
use Cake\Filesystem\Folder;

use App\Model\Entity\Category;
/**
 * Static content controller
 *
 * This controller will render views from Template/Pages/
 *
 * @link https://book.cakephp.org/3.0/en/controllers/pages-controller.html
 */
class CategoriesController extends AppController
{
    private $list = [];

    public function initialize()
    {
        parent::initialize();

        $this->PageTemplates = $this->getTableLocator()->get('PageTemplates');
        $this->Infos = $this->getTableLocator()->get('Infos');
        $this->PageConfigs = $this->getTableLocator()->get('PageConfigs');
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

        $sch_page_id = $this->request->getQuery('sch_page_id');
        $page_config = $this->PageConfigs->find()->where(['PageConfigs.id' => $sch_page_id])->first();
        $this->set(compact('sch_page_id', 'page_config'));

        if (!$this->isOwnPageByUser($sch_page_id)) {
            $this->Flash->set('不正なアクセスです');
            $this->redirect('/user/');
            return;
        }

        $cond = [
            'Categories.page_config_id' => $sch_page_id
        ];

        $this->_lists($cond, ['order' => 'position ASC',
                              'limit' => null]);
    }

    public function edit($id=0) {
        $this->checkLogin();

        $sch_page_id = $this->request->getQuery('sch_page_id');
        $this->set(compact('sch_page_id'));

        if ($id && !$this->isOwnCategoryByUser($id)) {
            $this->Flash->set('不正なアクセスです');
            $this->redirect('/user/');
            return;
        }

        $this->setList();

        $redirect = null;

        if ($this->request->is(['post', 'put'])) {
            $redirect = ['action' => 'index', '?' => ['sch_page_id' => $this->request->getData('page_config_id')]];
        }

        $callback = function($id) {
            $data = $this->Categories->find()->where(['Categories.id' => $id])->first();
            $entity = $this->Categories->patchEntity($data, ['identifier' => Category::IDENTIFIER . $data->position]);
            $this->Categories->save($entity);
        };

        $options['redirect'] = $redirect;
        $options['callback'] = $callback;

        parent::_edit($id, $options);

    }

    public function position($id, $pos) {
        $this->checkLogin();

        if ($id && !$this->isOwnCategoryByUser($id)) {
            $this->Flash->set('不正なアクセスです');
            $this->redirect('/user/');
            return;
        }

        $options = [];

        $data = $this->Categories->find()->where(['Categories.id' => $id])->first();
        if (empty($data)) {
            $this->redirect('/user/');
            return;
        }

        $options['redirect'] = ['action' => 'index', '?' => ['sch_page_id' => $data->page_config_id], '#' => 'content-' . $id];

        return parent::_position($id, $pos, $options);
    }

    public function enable($id) {
        $this->checkLogin();

        if ($id && !$this->isOwnCategoryByUser($id)) {
            $this->Flash->set('不正なアクセスです');
            $this->redirect('/user/');
            return;
        }

        $options = [];

        $data = $this->Categories->find()->where(['Categories.id' => $id])->first();
        if (empty($data)) {
            $this->redirect('/user/');
            return;
        }

        $options['redirect'] = ['action' => 'index', '?' => ['sch_page_id' => $data->page_config_id], '#' => 'content-' . $id];
        
        parent::_enable($id, $options);

        $this->requestAction(
            ['prefix' => 'user', 'controller' => 'Infos', 'action' => 'htmlUpdateAll', 'pass' => ['page_config_id' => $data->page_config_id, 'category_id' => $id]],
            ['return', 'bare' => true]
        );


    }

    public function delete($id, $type, $columns = null) {
        $this->checkLogin();

        if ($id && !$this->isOwnCategoryByUser($id)) {
            $this->Flash->set('不正なアクセスです');
            $this->redirect('/user/');
            return;
        }

        $data = $this->Categories->find()->where(['Categories.id' => $id])->first();
        if (empty($data)) {
            $this->redirect('/user/');
            return;
        }
        
        $options = ['redirect' => ['action' => 'index', '?' => ['sch_page_id' => $data->page_config_id]]];

        $result = parent::_delete($id, $type, $columns, $options);
        if (!$result) {
            $this->Infos->updateAll(['category_id' => 0, 'status' => 'draft'], ['Infos.category_id' => $data->id]);
        }
    }


    public function setList() {
        
        $list = array();

        if (!empty($list)) {
            $this->set(array_keys($list),$list);
        }

        $this->list = $list;
        return $list;
    }


}
