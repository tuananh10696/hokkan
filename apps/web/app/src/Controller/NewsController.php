<?php

namespace App\Controller;

use App\Controller\User\AppController;

class NewsController extends AppController
{

    public function beforeFilter(\Cake\Event\Event $event)
    {
        $this->modelName = 'Infos';
        $this->{$this->modelName} = $this->getTableLocator()->get($this->modelName);
        parent::beforeFilter($event);
    }


    public function index()
    {
        $this->setHeadTitle('新着情報');

        $cond = [
            'Infos.status' => 'publish',
            'Infos.start_date <=' => new \DateTime('now')
        ];
        $options = [
            'limit' => 10,
            'order' => array($this->modelName . '.position' =>  'ASC'),
        ];
        parent::_lists($cond, $options);
    }


    public function detail($id = null)
    {
        $cond = $this->isPreview ? [] : [
            'Infos.status' => 'publish',
            'Infos.start_date <=' => new \DateTime('now')
        ];
        $detail = $this->{$this->modelName}
            ->findById(intval($id))
            ->where($cond)
            ->first();
        if (!$detail) $this->redirect(['action' => 'index']);

        $this->setHeadTitle($detail->title);

        $this->set('detail', $detail);
    }
}
