<tr id="block_no_<?= h($rownum); ?>" data-sub-block-move="1" class="first-dir">
  <td>
    <div class="sort_handle"></div>
    <?= $this->Form->input("info_contents.{$rownum}.id", ['type' => 'hidden', 'value' => h($content['id']), 'id' => "idBlockId_" . h($rownum)]); ?>
    <?= $this->Form->input("info_contents.{$rownum}.position", ['type' => 'hidden', 'value' => h($content['position'])]); ?>
    <?= $this->Form->input("info_contents.{$rownum}.block_type", ['type' => 'hidden', 'value' => h($content['block_type']), 'class' => 'block_type']); ?>
    <?= $this->Form->input("info_contents.{$rownum}.title", ['type' => 'hidden', 'value' => '']); ?>
    <?= $this->Form->input("info_contents.{$rownum}.content", ['type' => 'hidden', 'value' => '']); ?>
    <?= $this->Form->input("info_contents.{$rownum}.image", ['type' => 'hidden', 'value' => '']); ?>
    <?= $this->Form->input("info_contents.{$rownum}.image_pos", ['type' => 'hidden', 'value' => '']); ?>
    <?= $this->Form->input("info_contents.{$rownum}.file", ['type' => 'hidden', 'value' => '']); ?>
    <?= $this->Form->input("info_contents.{$rownum}.file_size", ['type' => 'hidden', 'value' => '0']); ?>
    <?= $this->Form->input("info_contents.{$rownum}.file_name", ['type' => 'hidden', 'value' => '']); ?>
    <?= $this->Form->input("info_contents.{$rownum}.section_sequence_id", ['type' => 'hidden', 'value' => h($content['section_sequence_id']),  'class' => 'section_no']); ?>
    <?= $this->Form->input("info_contents.{$rownum}._block_no", ['type' => 'hidden', 'value' => h($rownum)]); ?>

  </td>
  <td class="head">
    <?= (h($rownum) + 1); ?>.区切り線</td>
  <td>
    <div style="text-align: right;">
      スタイル：<?= $this->Form->input("info_contents.{$rownum}.option_value", ['type' => 'select',
                                                                             'options' => $line_style_list,
                                                                             'empty' => ['' => '指定なし'],
                                                                             'value' => $content['option_value'],
                                                                             'escape' => false,
                                                                             'onChange' => 'changeStyle(this,' . h($rownum) . ', "style_target", "line_style_")'
                                                                           ]); ?>　
      色：<?= $this->Form->input("info_contents.{$rownum}.option_value2", ['type' => 'select',
                                                                             'options' => $line_color_list,
                                                                             'empty' => ['' => '指定なし'],
                                                                             'value' => $content['option_value2'],
                                                                             'escape' => false,
                                                                             'onChange' => 'changeStyle(this,' . h($rownum) . ', "style_target", "line_color_")'
                                                                           ]); ?>　
      太さ：<?= $this->Form->input("info_contents.{$rownum}.option_value3", ['type' => 'select',
                                                              'options' => $line_width_list,
                                                              'empty' => ['' => '指定なし'],
                                                              'value' => h($content['option_value3']),
                                                              'onChange' => 'changeWidth(this, ' . h($rownum) . ', "style_target", "border-width");'
                                                            ]
                                                          ); ?>
    </div>
    <hr class="style_target <?= $content['option_value']; ?> <?= $content['option_value2']; ?>" style="<?= ($content['option_value3'] ? 'border-width:'.$content['option_value3'].'px;':''); ?>">
  </td>
  <td>
      <div class='btn_area' style='float: right;'>
        <a href="javascript:void(0);" class="btn_confirm small_btn btn_list_delete size_min" data-row="<?= h($rownum);?>" style='text-align:center; width:auto;'>削除</a>
    </div>
  </td>
</tr>