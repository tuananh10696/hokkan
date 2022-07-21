<tr id="append_block-<?= $append['slug'] ?>">
    <td>
        <?= h($append['name']);?>
        <?= ($append['is_required'] == 1)?'<span class="attent">※必須</span>':'';?>
    </td>
    <td>
        <?= $this->Form->input("info_append_items.{$num}.id",['type' => 'hidden','value' => empty($data['info_append_items'][$num]['id'])?'':$data['info_append_items'][$num]['id']]);?>
        <?= $this->Form->input("info_append_items.{$num}.append_item_id",['type' => 'hidden','value' => $append['id']]);?>
        <?= $this->Form->input("info_append_items.{$num}.is_required",['type' => 'hidden','value' => $append['is_required']]);?>
        <?= $this->Form->input("info_append_items.{$num}.value_text",['type' => 'hidden','value' => '']);?>
        <?= $this->Form->input("info_append_items.{$num}.value_textarea",['type' => 'hidden','value' => '']);?>
        <?= $this->Form->input("info_append_items.{$num}.value_date",['type' => 'hidden','value' => '0']);?>
        <?= $this->Form->input("info_append_items.{$num}.value_datetime",['type' => 'hidden','value' => '0000-00-00']);?>
        <?= $this->Form->input("info_append_items.{$num}.value_time",['type' => 'hidden','value' => '0']);?>
        <?= $this->Form->input("info_append_items.{$num}.value_int",['type' => 'hidden','value' => '0']);?>
        <?= $this->Form->input("info_append_items.{$num}.value_decimal",['type' => 'hidden','value' => '0']);?>
        <?= $this->Form->input("info_append_items.{$num}.image",['type' => 'hidden','value' => '']);?>

        <?php $_column = 'file'; ?>
        <div class="manu">
      <ul>
     
        <?php if (!empty($data['info_append_items'][$num]['attaches'][$_column]['0'])) :?>
        <?php
        $file_data = $data['info_append_items'][$num]['attaches'][$_column];
        ?>
        <li class="<?= h($file_data['extention']); ?>">
          <?= $this->Form->input("info_append_items.{$num}.file_name", ['type' => 'hidden']); ?>
          <?= h($data['info_append_items'][$num]['file_name']); ?>.<?= h($data['info_append_items'][$num]['file_extension']); ?>
          <?= $this->Form->input("info_append_items.{$num}.file_size", ['type' => 'hidden', 'value' => h($data['info_append_items'][$num]['file_size'])]); ?>
          <div><?= $this->Html->link('ダウンロード', $file_data['0'], array('target' => '_blank'))?></div>
        </li>
        <?= $this->Form->input("info_append_items.{$num}._old_{$_column}", array('type' => 'hidden', 'value' => h($data['info_append_items'][$num][$_column]))); ?>
        <?= $this->Form->input("info_append_items.{$num}.file_extension", ['type' => 'hidden']); ?>
      <?php endif;?>

        <li>
          <?= $this->Form->input("info_append_items.{$num}.file", array('type' => 'file', 'class' => 'attaches', 'accept' => '.pdf'));?>
          <div class="remark">※PDFファイルのみ</div>
          <div>※ファイルサイズ５MB以内</div>
        </li>

      
      </ul>
      <?= $this->Form->error("{$slug}.{$append['slug']}") ?>
    </div>
    </td>

</tr>