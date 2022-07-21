<?php

namespace App\Controller\Admin;

use Cake\Event\Event;
use Cake\Filesystem\Folder;

class SiteConfigsController extends AppController
{
    private $list = [];

    public function initialize()
    {
        parent::initialize();

        $this->PageConfigs = $this->getTableLocator()->get('PageConfigs');
        $this->Infos = $this->getTableLocator()->get('Infos');


        $this->modelName = $this->name;
        $this->set('ModelName', $this->modelName);

        $this->loadComponent('OutputHtml');
        $this->checkLogin();
    }


    public function beforeFilter(Event $event)
    {
        $this->viewBuilder()->setLayout("admin");
        $this->getEventManager()->off($this->Csrf);
    }


    public function index()
    {
        $this->setList();
        return parent::_lists([], ['limit' => null]);
    }


    public function edit($id = 0)
    {
        $this->checkLogin();
        $validate = 'default';

        $this->setList();

        if ($this->request->is(['post', 'put'])) {
            if ($this->request->getData('is_root') == 1) {
                $validate = 'isRoot';
            }
        }

        $options['validate'] = $validate;

        parent::_edit($id, $options);
        $this->render('edit');
    }


    public function delete($id, $type, $columns = null)
    {
        $this->checkLogin();

        return parent::_delete($id, $type, $columns);
    }


    public function position($id, $pos)
    {
        $this->checkLogin();

        return parent::_position($id, $pos);
    }


    public function enable($id)
    {
        $this->checkLogin();

        parent::_enable($id);
    }


    public function setList()
    {

        $list = array();

        if (!empty($list)) {
            $this->set(array_keys($list), $list);
        }

        $this->list = $list;
        return $list;
    }
}
