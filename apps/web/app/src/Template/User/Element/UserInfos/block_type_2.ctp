<tr id="block_no_<?= h($rownum); ?>" data-sub-block-move="1" class="first-dir">
  <td>
    <div class="sort_handle"></div>
    <?= $this->Form->input("info_contents.{$rownum}.id", ['type' => 'hidden', 'value' => h($content['id']), 'id' => "idBlockId_" . h($rownum)]); ?>
    <?= $this->Form->input("info_contents.{$rownum}.position", ['type' => 'hidden', 'value' => h($content['position'])]); ?>
    <?= $this->Form->input("info_contents.{$rownum}.block_type", ['type' => 'hidden', 'value' => h($content['block_type']), 'class' => 'block_type']); ?>
    <?= $this->Form->input("info_contents.{$rownum}.title", ['type' => 'hidden', 'value' => '']); ?>
    <?= $this->Form->input("info_contents.{$rownum}.image", ['type' => 'hidden', 'value' => '']); ?>
    <?= $this->Form->input("info_contents.{$rownum}.image_pos", ['type' => 'hidden', 'value' => '']); ?>
    <?= $this->Form->input("info_contents.{$rownum}.file", ['type' => 'hidden', 'value' => '']); ?>
    <?= $this->Form->input("info_contents.{$rownum}.file_size", ['type' => 'hidden', 'value' => '0']); ?>
    <?= $this->Form->input("info_contents.{$rownum}.file_name", ['type' => 'hidden', 'value' => '']); ?>
    <?= $this->Form->input("info_contents.{$rownum}.section_sequence_id", ['type' => 'hidden', 'value' => h($content['section_sequence_id']), 'class' => 'section_no']); ?>
    <?= $this->Form->input("info_contents.{$rownum}._block_no", ['type' => 'hidden', 'value' => h($rownum)]); ?>
    <?= $this->Form->input("info_contents.{$rownum}.option_value3", ['type' => 'hidden', 'value' => '']); ?>
  </td>
  <td class="head"><?= (h($rownum) + 1); ?>.本文</td>
  <td>
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
    <div class="<?= h($content['option_value']); ?> font_target <?= h($content['option_value2']); ?>">
    <?= $this->Form->input("info_contents.{$rownum}.content", ['type' => 'textarea',
                                                               'class' => 'editor'
                                                             ]); ?>
    </div>
    <div>※フォント指定なしの全角文字はイタリック体が効きません。</div>
  </td>

  <td>
      <div class='btn_area' style='float: right;'>
        <a href="javascript:void(0);" class="btn_confirm small_btn btn_list_delete size_min" data-row="<?= h($rownum);?>" style='text-align:center; width:auto;'>削除</a>
    </div>
  </td>

</tr>