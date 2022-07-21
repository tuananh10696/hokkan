<?php

namespace App\Model\Entity;

class AppendItem extends AppEntity
{

    const TYPE_NUMBER = 1;
    const TYPE_TEXT = 2;
    const TYPE_TEXTAREA = 3;
    const TYPE_DATE = 4;
    const TYPE_LIST = 10;
    const TYPE_CHECK = 11;
    const TYPE_RADIO = 12;
    const TYPE_DECIMAL = 21;
    const TYPE_FILE = 31;
    const TYPE_IMAGE = 32;
    const TYPE_IMAGE_WP = 33;

    static $value_type_list = [
        self::TYPE_NUMBER => '数字型',
        self::TYPE_TEXT => 'テキスト型',
        self::TYPE_TEXTAREA => 'テキストエリア型',
        self::TYPE_DATE => '日付型',
        self::TYPE_LIST => 'list型',
        self::TYPE_CHECK => 'checkbox型',
        self::TYPE_RADIO => 'radio型',
        self::TYPE_DECIMAL => 'deceimal3型',
        self::TYPE_FILE => 'file型',
        self::TYPE_IMAGE => '画像型',
        self::TYPE_IMAGE_WP => '画像型(WP)',
    ];

    // TYPE_TEXTにて表示するplaceholderがあれば
    // slug => placeholder で設定
    static $placeholder_list = [
        'link_url' => 'https://...',
        'ticket_link' => 'https://...',
        'news_link' => 'https://...',
        'special_link' => 'https://...',
    ];

    // TYPE_TEXTの※部分に表示するリスト、
    // $placeholder_list同様
    // slug => text で指定
    static $notes_list = [
        'stadium_kana' => '括弧内に表示されます',

    ];
}
