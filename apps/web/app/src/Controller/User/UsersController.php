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

namespace App\Controller\User;

use Cake\Event\Event;
use Cake\Auth\DefaultPasswordHasher;

/**
 * Static content controller
 *
 * This controller will render views from Template/Pages/
 *
 * @link https://book.cakephp.org/3.0/en/controllers/pages-controller.html
 */
class UsersController extends AppController
{

    public function beforeFilter(Event $event)
    {
        $this->getEventManager()->off($this->Csrf);
        parent::beforeFilter($event);
        $this->viewBuilder()->setLayout("plain");
    }


    public function index()
    {
        $view = "login";
        if ($this->request->is('post') || $this->request->is('put')) {
           
            $data = $this->request->getData();

            if (!empty($data['username']) && !empty($data['password'])) {
                $r = $this->Users
                    ->find()
                    ->where(['Users.username' => $data['username'], 'Users.status' => 'publish'])
                    ->first();

                $is_login = false;

                if (!is_null($r)) {
                    $hasher = new DefaultPasswordHasher();
                    if ($hasher->check($data['password'], $r->password) && $r->temp_password == '') {
                        $is_login = true;
                    } elseif ($r->temp_password == $data['password']) {
                        $is_login = true;
                    }
                } else $this->Flash->set('アカウント名またはパスワードが違います');

                if ($is_login) {
                    $this->Session->write(array(
                        'userid' => $r->id,
                        'data' => array(
                            'name' => $r->name
                        ),
                        'user_role' => $r->role
                    ));
                }
            }
        }

        if (0 < $this->isLogin()) {
            $this->viewBuilder()->setLayout("user");
            $this->setCommon();

            $this->setList();
            $view = "index";
        }
        $this->render($view);
    }

    public function logout()
    {
        if (0 < $this->isLogin()) {
            $this->Session->delete('userid');
            $this->Session->delete('role');
            $this->Session->delete('current_site_id');
            $this->Session->delete('current_site_slug');
            $this->Session->destroy();
        }
        $this->redirect('/user/');
    }

    public function setList()
    {
        $current_site_id = $this->Session->read('current_site_id');

        if (!$current_site_id) {
            $this->Flash->set('サイト権限がありません');
            $this->logout();
        }

        $list = [];

        $page_configs = $this->loadModel('PageConfigs')->find()
            ->where(['PageConfigs.site_config_id' => $current_site_id])
            ->order(['PageConfigs.position' => 'ASC'])
            ->all()
            ->toArray();

        $list['user_menu_list'] = [
            'コンテンツ' => []
        ];

        // if ($this->isUserRole('admin')) $list['user_menu_list']['設定'] = [['コンテンツ設定' => '/user/page-configs']];

        if (!empty($page_configs)) {
            $configs = array_chunk($page_configs, 3);

            foreach ($configs as $_) {
                $menu = [];

                
                foreach ($_ as $config) {
                    $menu[$config->page_title] = '/user/infos/?sch_page_id=' . $config->id;
                }
                $list['user_menu_list']['コンテンツ'][] = $menu;
            }
        }

        if (!empty($list)) $this->set(array_keys($list), $list);

        $this->list = $list;
        return $list;
    }
}
