<?php

namespace App\Model\Entity;

use Cake\Auth\DefaultPasswordHasher;

class User extends AppEntity
{

    protected $_accessible = [
        '*' => true,
    ];

    const ROLE_DEVELOP = 0;
    const ROLE_ADMIN = 1;
    const ROLE_STAFF = 10;
    const ROLE_DEMO = 90;

    static $role_list = [
        self::ROLE_ADMIN => '管理者',
        self::ROLE_STAFF => 'スタッフ',
        self::ROLE_DEMO => 'デモ',
    ];

    protected function _setPassword($password)
    {
        return (new DefaultPasswordHasher)->hash($password);
    }

    protected function _getListName()
    {
        return "{$this->_properties['name']}({$this->_properties['username']})";
    }
}
