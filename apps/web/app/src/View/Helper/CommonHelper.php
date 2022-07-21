<?php 
namespace App\View\Helper;

use Cake\Datasource\ModelAwareTrait;

class CommonHelper extends AppHelper
{
    use ModelAwareTrait;

    public function session_read($key) {
        return $this->getView()->getRequest()->getSession()->read($key);
    }
    public function session_check($key) {
        return $this->getView()->getRequest()->getSession()->check($key);
    }

    public function getCategoryEnabled() {
        return CATEGORY_FUNCTION_ENABLED;
    }

    public function getCategorySortEnabled() {
        return CATEGORY_SORT;
    }

    public function isCategoryEnabled($page_config) {

        if (!$this->getCategoryEnabled()) {
            return false;
        }

        if (empty($page_config)) {
            return false;
        }

        if ($page_config->is_category == 'Y') {
            return true;
        }

        return false;
    }

    public function isCategorySort($page_config_id) {
        $this->modelFactory('Table', ['Cake\ORM\TableRegistry', 'get']);
        $this->loadModel('PageConfigs');

        if (!CATEGORY_SORT) {
            return false;
        }
        $page_config = $this->PageConfigs->find()->where(['PageConfigs.id' => $page_config_id])->first();

        if (empty($page_config)) {
            return false;
        }

        if ($page_config->is_category_sort == 'Y') {
            return true;
        }

        return false;
    }

    public function isViewSort($page_config, $category_id=0) {

        if ($this->getCategoryEnabled() && $page_config->is_category === 'Y'
             && ($this->isCategorySort($page_config->id)) || (!$this->isCategorySort($page_config->id) && !$category_id) ) {
            return true;
        }

        return false;
    }

    public function isUserRole($role_key, $isOnly = false) {
        
        $role = $this->session_read('user_role');
        
        if (intval($role) === 0) {
            $res = 'develop';
        }
        elseif ($role < 10) {
            $res = 'admin';
        } 
        else if ($role >= 90) {
            $res = 'demo';
        }
        /** 必要に応じて追加 */
        else {
            $res = 'staff';
        }

        if (!$isOnly) {
            if ($role_key == 'admin') {
                $role_key = array('develop', 'admin');
            } elseif ($role_key == 'staff') {
                $role_key = array('develop', 'admin', 'staff');
            }
        } 

        if (in_array($res, (array)$role_key)) {
            return true;
        } else {
            return false;
        }

    }

    public function getAppendFields($info_id) {
        $this->modelFactory('Table', ['Cake\ORM\TableRegistry', 'get']);
        $this->loadModel('InfoAppendItems');

        $contain = [
            'AppendItems'
        ];
        $append_items = $this->InfoAppendItems->find()->where(['InfoAppendItems.info_id' => $info_id])->contain($contain)->all();
        if (empty($append_items)) {
            return [];
        }

        $result = [];
        foreach ($append_items as $item) {
            // $_data = $item;
            $result[$item->append_item->slug] = $item;
        }

        return $result;
    }
}