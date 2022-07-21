<?php 
namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\Validation\Validator;

class InfoAppendItemsTable extends AppTable {

    // テーブルの初期値を設定する
    public $defaultValues = [
        "id" => null,
    ];

    public $attaches = array('images' =>array(
                                'image' => array('extensions' => array('jpg', 'jpeg', 'gif', 'png'),
                                'width' => 1920,
                                'height' => 1920,
                                'file_name' => 'append_img_%d_%s',
                                'thumbnails' => array(
                                    's' => array(
                                        'prefix' => 's_',
                                        'width' => 320,
                                        'height' => 240
                                        )
                                    ),
                                )
                            ),
                            'files' => array(
                                'file' => array(
                                'extensions' => array('pdf'),
                                'file_name' => '%2$s%3$s%1$05d',
                                'prefix' => 'n3'
                                )
                            ),
                            );


                            // 
    public function initialize(array $config)
    {

        // $this->addBehavior('FileAttache');

        $this->belongsTo('AppendItems')
             ->setDependent(true);
        $this->belongsTo('Infos')
             ->setDependent(true);

        parent::initialize($config);
        
    }
    // Validation
    public function validationDefault(Validator $validator)
    {

        // $validator
        //     ->allowEmpty('is_required');
        
        return $validator;
    }


        
}