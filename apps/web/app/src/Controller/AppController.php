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

use Cake\Controller\Controller;
use Cake\Event\Event;

/**
 * Application Controller
 *
 * Add your application-wide methods in the class below, your controllers
 * will inherit them.
 *
 * @link https://book.cakephp.org/3.0/en/controllers.html#the-app-controller
 */
class AppController extends Controller
{

    public $Session;
    public $error_messages;
    /**
     * Initialization hook method.
     *
     * Use this method to add common initialization code like loading components.
     *
     * e.g. `$this->loadComponent('Security');`
     *
     * @return void
     */
    public function initialize()
    {
        parent::initialize();

        $this->loadComponent('RequestHandler', [
            'enableBeforeRedirect' => false,
        ]);
        $this->loadComponent('Flash');
        $this->loadComponent('Paginator');
        $this->loadComponent('Csrf');

        $this->Session = $this->getRequest()->getSession();

        $this->viewBuilder()->setLayout(false);
        $this->setHeadTitle();

        $this->isPreview = $this->isUserLogin() && $this->request->getQuery('preview') === 'on';
    }


    /**
     * Before render callback.
     *
     * @param \Cake\Event\Event $event The beforeRender event.
     * @return \Cake\Http\Response|null|void
     */
    public function beforeRender(Event $event)
    {
        // Note: These defaults are just to get started quickly with development
        // and should not be used in production. You should instead set "_serialize"
        // in each action as required.
        if (
            !array_key_exists('_serialize', $this->viewVars) &&
            in_array($this->response->getType(), ['application/json', 'application/xml'])
        ) {
            $this->set('_serialize', true);
        }

        $this->set('error_messages', $this->error_messages);
    }


    public function beforeFilter(Event $event)
    {

        if ($this->request->getParam('prefix') === 'admin') {
            $this->viewBuilder()->setLayout('admin');
        } else {
            //Theme 設定
            $this->viewBuilder()->setLayout('common');
            // 準備
            $this->_prepare();
        }
    }


    protected function _setView($lists)
    {
        $this->set(array_keys($lists), $lists);
    }


    private function _prepare()
    { }


    public function isAdminLogin()
    {
        return $this->Session->read('uid');
    }


    public function isUserLogin()
    {
        return $this->Session->read('userid');
    }


    public function getUserId()
    {
        return $this->Session->read('userid');
    }


    public function checkLogin()
    {
        if (!$this->isAdminLogin()) return $this->redirectWithException('/admin/');
    }


    public function checkUserLogin()
    {
        if (!$this->isUserLogin()) return $this->redirectWithException('/user/');
    }

    /**
     * ハイアラーキゼーションと読む！（階層化という意味だ！）
     * １次元のentityデータを階層化した状態の構造にする
     */
    public function toHierarchization($id, $entity, $options = [])
    {
        $content_count = 0;
        $contents = [
            'contents' => []
        ];

        $contents_table = $this->{$this->modelName}->useHierarchization['contents_table'];

        $sequence_table = $this->{$this->modelName}->useHierarchization['sequence_table'];
        $sequence_id_name = $this->{$this->modelName}->useHierarchization['sequence_id_name'];

        if (!empty($entity->{$contents_table})) {

            $content_count = count($entity->{$contents_table});
            $block_count = 0;

            foreach ($entity->{$contents_table} as $k => $val) {
                $v = $val->toArray();

                // 枠ブロックの中にあるブロック以外　（枠ブロックも対象）
                if (!$v[$sequence_id_name] || ($v[$sequence_id_name] > 0 && in_array($v['block_type'], $options['section_block_ids']))) {
                    $contents["contents"][$block_count] = $v;
                    $contents["contents"][$block_count]['_block_no'] = $block_count;
                } else {
                    // 枠ブロックの中身
                    if (!array_key_exists($sequence_table, $v)) continue;
                    $sequence_id = $v[$sequence_id_name];

                    $waku_number = false;
                    foreach ($contents['contents'] as $_no => $_v) {
                        if (in_array($_v['block_type'], $options['section_block_ids']) && $sequence_id == $_v[$sequence_id_name]) {
                            $waku_number = $_no;
                            break;
                        }
                    }
                    if ($waku_number === false) continue;

                    if (!array_key_exists('sub_contents', $contents["contents"][$waku_number])) $contents["contents"][$waku_number]['sub_contents'] = null;

                    $contents["contents"][$waku_number]['sub_contents'][$block_count] = $v;
                    $contents["contents"][$waku_number]['sub_contents'][$block_count]['_block_no'] = $block_count;
                }
                $block_count++;
            }
        }

        return [
            'contents' => $contents,
            'content_count' => $content_count
        ];
    }


    /**
     * 正常時のレスポンス
     */
    protected function rest_success($datas)
    {
        $data = [
            'result' => ['code' => 0],
            'data' => $datas
        ];

        $this->set(compact('data'));
        $this->set('_serialize', 'data');
    }


    /**
     * エラーレスポンス
     */
    protected function rest_error($code = '', $message = '')
    {

        $http_status = 200;

        $state_list = [
            '200' => 'empty',
            '400' => 'Bad Request', // タイプミス等、リクエストにエラーがあります。
            '401' => 'Unauthorixed', // 認証に失敗しました。（パスワードを適当に入れてみた時などに発生）
            // '402' => '', // 使ってない
            '403' => 'Forbidden', // あなたにはアクセス権がありません。
            '404' => 'Not Found', // 該当アドレスのページはありません、またはそのサーバーが落ちている。
            '500' => 'Internal Server Error', // CGIスクリプトなどでエラーが出た。
            '501' => 'Not Implemented', // リクエストを実行するための必要な機能をサポートしていない。
            '509' => 'Other', // オリジナルコード　例外処理
        ];

        $code2messages = [
            '1000' => 'パラメーターエラー',
            '1001' => 'パラメーターエラー',
            '1002' => 'パラメーターエラー',
            '2000' => '取得データがありませんでした',
            '2001' => '取得データがありませんでした',
            '9000' => '認証に失敗しました',
            '9001' => '',
        ];

        if (!array_key_exists($http_status, $state_list)) $http_status = '509';

        if ($message == "") {
            if (array_key_exists($code, $code2messages)) $message = $code2messages[$code];
            elseif (array_key_exists($http_status, $state_list)) $message = $state_list[$http_status];
        }

        if ($code == '') $code = $http_status;

        $data['result'] = array(
            'code' => intval($code),
            'message' => $message
        );

        $this->set(compact('data'));
        $this->set('_serialize', 'data');
        return;
    }


    public function getCategoryEnabled()
    {
        return CATEGORY_FUNCTION_ENABLED;
    }


    public function getCategorySortEnabled()
    {
        return CATEGORY_SORT;
    }


    public function isCategoryEnabled($page_config)
    {

        if (!$this->getCategoryEnabled()) return false;

        if (empty($page_config)) return false;

        if ($page_config->is_category == 'Y') return true;

        return false;
    }


    public function isCategorySort($page_config_id)
    {
        if (!CATEGORY_SORT) return false;

        $page_config = $this->PageConfigs->find()->where(['PageConfigs.id' => $page_config_id])->first();
        if (empty($page_config)) return false;

        if ($page_config->is_category_sort == 'Y') return true;

        return false;
    }


    public function isViewSort($page_config, $category_id = 0)
    {
        return ($this->getCategoryEnabled() && $page_config->is_category === 'Y'
            && ($this->isCategorySort($page_config->id)) || (!$this->isCategorySort($page_config->id) && !$category_id));
    }


    /**
     * 記事がユーザーに権限のあるものかどうか
     * @param  [type]  $info_id [description]
     * @return boolean          [description]
     */
    public function isOwnInfoByUser($info_id)
    {
        $user_id = $this->isUserLogin();

        $info = $this->Infos->find()
            ->where(['Infos.id' => $info_id])
            ->contain([
                'PageConfigs' => function ($q) {
                    return $q->select(['site_config_id']);
                }
            ])
            ->first();

        if (empty($info)) return false;

        $user_site = $this->UserSites->find()->where(['UserSites.user_id' => $user_id, 'UserSites.site_config_id' => $info->page_config->site_config_id])->first();
        return (!empty($user_site));
    }


    /**
     * ページがユーザーに権限のあるものかどうか
     * @param  [type]  $page_config_id [description]
     * @return boolean                 [description]
     */
    public function isOwnPageByUser($page_config_id)
    {
        $user_id = $this->isUserLogin();

        $page_config = $this->PageConfigs->find()->where(['PageConfigs.id' => $page_config_id])->first();
        if (empty($page_config)) return false;

        $user_site = $this->UserSites->find()->where(['UserSites.user_id' => $user_id, 'UserSites.site_config_id' => $page_config->site_config_id])->first();
        return (!empty($user_site));
    }


    public function isOwnCategoryByUser($category_id)
    {
        $user_id = $this->isUserLogin();

        $category = $this->Categories->find()
            ->where(['Categories.id' => $category_id])
            ->contain([
                'PageConfigs' => function ($q) {
                    return $q->select(['site_config_id']);
                }
            ])
            ->first();
        if (empty($category)) return false;

        $user_site = $this->UserSites->find()->where(['UserSites.user_id' => $user_id, 'UserSites.site_config_id' => $category->page_config->site_config_id])->first();
        return (!empty($user_site));
    }


    public function redirectWithException($url, $status = 302)
    {
        throw new \Cake\Routing\Exception\RedirectException(\Cake\Routing\Router::url($url, true), $status);
    }


    public function startupProcess()
    {
        try {
            return parent::startupProcess();
        } catch (\Cake\Routing\Exception\RedirectException $e) {
            return $this->redirect($e->getMessage(), $e->getCode());
        }
    }


    public function invokeAction()
    {
        try {
            return parent::invokeAction();
        } catch (\Cake\Routing\Exception\RedirectException $e) {
            return $this->redirect($e->getMessage(), $e->getCode());
        }
    }


    public function shutdownProcess()
    {
        try {
            return parent::shutdownProcess();
        } catch (\Cake\Routing\Exception\RedirectException $e) {
            return $this->redirect($e->getMessage(), $e->getCode());
        }
    }


    protected function _preventGarbledCharacters($bigText, $width = 249)
    {
        $pattern = "/(.{1,{$width}})(?:\\s|$)|(.{{$width}})/uS";
        $replace = '$1$2' . "\n";
        $wrappedText = preg_replace($pattern, $replace, $bigText);
        return $wrappedText;
    }


    protected function setHeadTitle($title = Null, $isFull = False)
    {
        $_title = \Cake\Core\Configure::read('App.headTitle');
        if ($title) {
            $title = is_array($title) ? implode(' | ', $title) : $title;
            $_title = $isFull ? $title : __('{0} | {1}', [$title, $_title]);
        }
        $this->set('__title__', $_title);
        return $_title;
    }
}
