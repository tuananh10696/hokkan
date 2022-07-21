<?php

namespace App\Model\Entity;

class PageConfig extends AppEntity
{

    const LIST_STYLE_THUMBNAIL = 1;
    const LIST_STYLE_ONE_COLUMN = 2;

    static $list_styles = [
        self::LIST_STYLE_THUMBNAIL => 'サムネイル',
        self::LIST_STYLE_ONE_COLUMN => '１カラム'
    ];
}
