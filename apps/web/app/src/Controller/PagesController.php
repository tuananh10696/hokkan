<?php

namespace App\Controller;

use App\Controller\User\AppController;

class PagesController extends AppController
{

    public function beforeFilter(\Cake\Event\Event $event)
    {
        $this->modelName = 'Infos';
        $this->{$this->modelName} = $this->getTableLocator()->get($this->modelName);
        parent::beforeFilter($event);
    }

    
    public function index()
    {
        $this->loadModel('Infos');
        $info_model = $this->Infos->find()->where([
            'Infos.status' => 'publish',
            'Infos.start_date <=' => new \DateTime('now'),
        ])
            ->limit('2')
            ->order([$this->modelName . '.position' =>  'ASC'])
            ->toArray();

        $this->set('info_model', $info_model);
    }
}
