<?php

/**
 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link      https://cakephp.org CakePHP(tm) Project
 * @since     0.2.9
 * @license   https://opensource.org/licenses/mit-license.php MIT License
 */

namespace App\Controller;

use Cake\Core\Configure;
use Cake\Network\Exception\ForbiddenException;
use Cake\Network\Exception\NotFoundException;
use Cake\View\Exception\MissingTemplateException;

/**
 * Static content controller
 *
 * This controller will render views from Template/Pages/
 *
 * @link https://book.cakephp.org/3.0/en/controllers/pages-controller.html
 */
class HomesController extends AppController
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
