<?php

namespace App\Form;

use Cake\Mailer\Email;
use Cake\Form\Form;
use Cake\Form\Schema;
use Cake\Validation\Validator;
use Cake\Mailer\TransportFactory;
use App\Utils\CustomUtility;

class ContactForm extends Form
{
    protected function _buildSchema(Schema $schema)
    {
        return $schema
            ->addField('name', 'string')
            ->addField('kana', 'string')
            ->addField('tel', 'string')
            ->addField('email', 'string')
            ->addField('content', 'string')
            ->addField('desired', 'int')
            ->addField('inquiry', 'int')
            ->addField('is_accept', 'int');
    }

    public function _buildValidator(Validator $validator)
    {

        $validator
            ->notBlank('name', 'お名前をご入力ください')
            ->notEmptyString('name', 'お名前をご入力ください')
            ->maxLength('name', 30, '30字以内でご入力ください');
        $validator
            ->notBlank('kana', 'フリガナをご入力ください')
            ->notEmptyString('kana', 'フリガナをご入力ください')
            ->add(
                'kana',
                [
                    'custom' => [
                        'rule' => function ($value, $context) {
                            if (!preg_match("/^[\x{30a1}-\x{30fc}　 ]+$/u", $value)) {
                                return 'フリガナをご入力ください';
                            }
                            return true;
                        },
                    ],
                ],
            )
            ->maxLength('kana', 30, '30字以内でご入力ください');
        $validator
            ->notBlank('tel', '電話番号をご入力ください')
            ->notEmptyString('tel', '電話番号をご入力ください')
            ->lengthBetween('tel', [10, 13], '電話番号は半角数字(ハイフン必須)<br class="show_sp">でご入力ください')
            ->add(
                'tel',
                [
                    'custom' => [
                        'rule' => function ($value, $context) {
                            $v  = str_replace(['&nbsp;', ' ', ' '], '', $value);
                            if (!preg_match("/(^0[0-9]{2}-[0-9]{4}-[0-9]{4}$)|(^0[0-9]-[0-9]{4}-[0-9]{4}$)|(^0[0-9]{2}-[0-9]{3}-[0-9]{4}$)|(^0[0-9]{3}-[0-9]{2}-[0-9]{4}$)/u", $v)) {
                                return '電話番号を（ハイフン必須）<br class="show_sp">正しくご入力ください';
                            }
                            return true;
                        },
                    ],
                ],
            );
        $validator
            ->notBlank('email', 'E-mailをご入力ください')
            ->notEmptyString('email', 'E-mailをご入力ください')
            ->maxLength('email', 100, '100字以内でご入力ください')
            ->add(
                'email',
                [
                    'custom' => [
                        'rule' => function ($value, $context) {
                            $v  = str_replace(['&nbsp;', ' ', ' '], '', $value);
                            if (!preg_match("/^[a-zA-Z0-9_+-]+(.[a-zA-Z0-9_+-]+)*@([a-zA-Z0-9][a-zA-Z0-9-]*[a-zA-Z0-9]*\.)+[a-zA-Z]{2,}$/u", $v)) {
                                return 'メールアドレスを正しく<br class="show_sp">ご入力ください';
                            }
                            return true;
                        },
                    ],
                ],
            );
        $validator
            ->notBlank('content', 'お問い合わせ内容を<br class="show_sp">ご入力ください')
            ->notEmptyString('content', 'お問い合わせ内容を<br class="show_sp">ご入力ください')
            ->maxLength('content', 1000, '1000字以内でご入力ください');

        $validator
            ->integer('desired')
            ->allowEmpty('desired', '選択してください')
            ->add(
                'desired',
                [
                    'custom' => [
                        'rule' => function ($value, $context) {
                            if (intval($value) == 0) {
                                return '選択してください';
                            }
                            return true;
                        },
                    ],
                ],
            );

        $validator
            ->integer('is_accept')
            ->add(
                'is_accept',
                [
                    'custom' => [
                        'rule' => function ($value, $context) {
                            if (intval($value) == 0) {
                                return '同意してください';
                            }
                            return true;
                        },
                    ],
                ],
            );

        $validator
            ->integer('inquiry')
            ->add(
                'inquiry',
                [
                    'custom' => [
                        'rule' => function ($value, $context) {
                            if (intval($value) == 0) {
                                return '選択してください';
                            }
                            return true;
                        },
                    ],
                ],
            );

        return $validator;
    }


    public function checkEmail($value, $context)
    {

        return (bool) preg_match('/\A[a-zA-Z0-9_-]([a-zA-Z0-9_\!#\$%&~\*\+-\/\=\.]+)*@[a-zA-Z0-9-]+(\.[a-zA-Z0-9-]+)*\.([a-zA-Z]{2,20})\z/', $value);
    }


    public function checkPostcode($value, $context)
    {

        return (bool) preg_match('/[0-9]{3}-[0-9]{4}/', $value);
    }


    protected function _execute(array $data)
    {
        // 文字化け対応
        $data['content'] = CustomUtility::_preventGarbledCharacters($data['content']);
        $to = $data['inquiry'] == 1 ? 'develop+fujimoto@caters.co.jp' : 'develop+info@caters.co.jp';
        // $to = $data['inquiry'] == 1 ? 'fujimoto@hokkansyuzou.co.jp' : 'info@hokkansyuzou.co.jp';

        // メールを送信する 
        $info_email = new Email();
        $info_email->setCharset('ISO-2022-JP-MS');
        $info_email
            ->template('admin_contact')
            ->emailFormat('text')
            ->setViewVars(['value' => $data])
            ->setFrom(['develop+hokkansyuzou@caters.co.jp' => '北関酒造株式会社'])
            ->setTo($to)
            ->setSubject('【北関酒造】お問い合わせがありました。')
            ->send();


        $thank_email = new Email();
        $info_email->setCharset('ISO-2022-JP-MS');
        $thank_email
            ->template('contact')
            ->emailFormat('text')
            ->setViewVars(['value' => $data])
            ->setFrom(['develop+hokkansyuzou@caters.co.jp' => '北関酒造株式会社'])
            ->setTo($data['email'])
            ->setSubject('【北関酒造】お問い合わせありがとうございます！')
            ->send();

        return true;
    }
}
