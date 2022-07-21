<?php 
namespace App\View\Helper;

use Cake\View\Helper\HtmlHelper;

class MyHtmlHelper extends HtmlHelper
{
    public function getFullUrl($url) {
        $host = $this->Url->build('/', true);

        if (substr($url, 0, 1) == '/') {
            $url = substr($url, 1);
        }

        return $host . $url;
    }

    public function view($val, $options = array()) {

        $options = array_merge(array('before' => '',
                               'after' => '',
                               'default' => '',
                               'empty' => '',
                               'nl2br' => false,
                               'h' => true,
                               'emptyIsZero' => false,
                               'price_format' => false,
                               'decimal' => 2 //price_format=true時の小数点以下桁数
                           ),
                               $options);
        extract($options);

        if ($emptyIsZero && intval($val) === 0) {
            $val = "";
        }

        if ($val && $price_format) {
            $cost = $val;
            $cost = number_format($cost, $decimal);  // 1,234.50
            $cost = (preg_match('/\./', $cost)) ? preg_replace('/\.?0+$/', '', $cost) : $cost; // 末尾の０は消す
            $val = $cost;
        }

        if ($val != "") {
            if ($h) {
                $val = h($val);
            }
            if ($nl2br) {
                $val = nl2br($val);
            }
            return $before.$val.$after;
        }

        return $default.$empty;
    }
}