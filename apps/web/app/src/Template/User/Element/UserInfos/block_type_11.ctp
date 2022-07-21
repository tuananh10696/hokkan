<tr id="block_no_<?= h($rownum); ?>"  data-sub-block-move="0" class="first-dir">
  <td>
    <div class="sort_handle"></div>
    <?= $this->Form->input("info_contents.{$rownum}.id", ['type' => 'hidden', 'value' => h($content['id']), 'id' => "idBlockId_" . h($rownum)]); ?>
    <?= $this->Form->input("info_contents.{$rownum}.position", ['type' => 'hidden', 'value' => h($content['position'])]); ?>
    <?= $this->Form->input("info_contents.{$rownum}.block_type", ['type' => 'hidden', 'value' => h($content['block_type']), 'class' => 'block_type']); ?>
    <?= $this->Form->input("info_contents.{$rownum}.title", ['type' => 'hidden', 'value' => '']); ?>
    <?= $this->Form->input("info_contents.{$rownum}.file", ['type' => 'hidden', 'value' => '']); ?>
    <?= $this->Form->input("info_contents.{$rownum}.file_size", ['type' => 'hidden', 'value' => '0']); ?>
    <?= $this->Form->input("info_contents.{$rownum}.file_name", ['type' => 'hidden', 'value' => '']); ?>
    <?= $this->Form->input("info_contents.{$rownum}.section_sequence_id", ['type' => 'hidden', 'value' => h($content['section_sequence_id']), 'class' => 'section_no']); ?>
    <?= $this->Form->input("info_contents.{$rownum}._block_no", ['type' => 'hidden', 'value' => h($rownum)]); ?>
  </td>

  <td colspan="2">
    <div class="sub-unit__wrap">
      <h4><?= (h($rownum) + 1); ?>.画像回り込み用</h4>

            
            <?php $image_column = 'image'; ?>
            <dl style="border:1px solid #cbcbcb;padding: 10px;">
              <dt>１．回り込み位置</dt>
              <dd>
              <?= $this->Form->input("info_contents.{$rownum}.image_pos", ['type' => 'radio',
                                                                        'value' => h($content['image_pos']),
                                                                        'options' => ['left' => '<img src="/user/common/images/cms/align_left.gif">', 'right' => '<img src="/user/common/images/cms/align_right.gif">'],
                                                                        'separator' => '　',
                                                                        'escape' => false,
                                                                        'defaultValue' => 'left'
                                                                      ]); ?>
              </dd>

              <dt>２．画像</dt>
              <dd>
              <?php if (!empty($content['attaches'][$image_column]['0'])) :?>
              <div>
                <a href="<?= h($content['attaches'][$image_column]['0']);?>" class="pop_image_single">
                  <img src="<?= $this->Url->build($content['attaches'][$image_column]['0'])?>" style="width: 300px; float: left;">
                  <?= $this->Form->input("info_contents.{$rownum}.attaches.{$image_column}.0", ['type' => 'hidden']); ?>
                </a><br >
                <?= $this->Form->input("info_contents.{$rownum}._old_{$image_column}", array('type' => 'hidden', 'value' => h($content[$image_column]))); ?>
              </div>
            <?php endif;?>

              <div>
                <?= $this->Form->input("info_contents.{$rownum}.{$image_column}", array('type' => 'file', 'class' => 'attaches'));?>
                <div class="remark">※jpeg , jpg , gif , png ファイルのみ</div>
                <div><?= $this->Form->getRecommendSize('InfoContents', 'image', ['before' => '※', 'after' => '']); ?></div>
                <div>※ファイルサイズ５MB以内</div>
                <br />
              </div>
              <div style="clear: both;"></div>
              </dd>
            
              <dt style="margin-top: 10px;">３．画像リンク</dt>
              <dd>
                <?= $this->Form->input("info_contents.{$rownum}.option_value3", ['type' => 'text',
                                                                                      'style' => 'width:500px;',
                                                                                      'value' => $content['option_value3'],
                                                                                      'placeholder' => 'http://'
                                                                                    ]); ?>

              </dd>

            </dl>


      <table style="margin: 0; width: 100%;table-layout: fixed;" data-block-type="<?= h($content['block_type']); ?>">
        <colgroup>
          <col style="width: 150px;">
          <col style="width: 150px;">
          <col>
          <col style="width: 90px;">
        </colgroup>
        <thead>
        </thead>
        <tbody>
          <tr>
            <td>スタイル</td>
            <td colspan="3" style="border-top: 1px;">
              <div style="text-align: right;">
                <ul class="select_style_area">
                  <li>
      フォント：<?= $this->Form->input("info_contents.{$rownum}.option_value", ['type' => 'select',
                                                      'options' => $font_list,
                                                      'empty' => ['' => '指定なし'],
                                                      'value' => h($content['option_value']),
                                                      'onChange' => 'changeStyle(this,' . h($rownum) . ', "font_target", "font_style_")'
                                                    ]
                                                  ); ?>
                </li>

                <li>
<!--     </div>
    <div style="text-align: right;"> -->
      リスト種類：<?= $this->Form->input("info_contents.{$rownum}.option_value2", ['type' => 'select',
                                                      'options' => $content_liststyle_list,
                                                      'empty' => ['' => '番号付きリスト'],
                                                      'value' => h($content['option_value2']),
                                                      'onChange' => 'changeStyle(this,' . h($rownum) . ', "font_target", "liststyle_")'
                                                    ]
                                                  ); ?>
                </li>
              </ul>
    </div>
            </td>
          </tr>
          <tr>
            <td>本文</td>
            <td colspan="3">
              <div class="font_target <?= h($content['option_value']); ?> <?= $content['option_value2']; ?>">
              <?= $this->Form->input("info_contents.{$rownum}.content", ['type' => 'textarea', 'class' => 'editor']); ?>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </td>
  <td>
      <div class='btn_area' style='float: right;'>
        <a href="javascript:void(0);" class="btn_confirm small_btn btn_list_delete size_min" data-row="<?= h($rownum);?>" style='text-align:center; width:auto;'>削除</a>
    </div>
  </td>
</tr>