<?php
$label_message = '';
if($slug == 'news'){
    if($append['slug'] == 'is_new'){
        $label_message = '表示する';
    }

}

?>
<tr id="append_block-<?= $append['slug'] ?>">
    <td>
        <?= h($append['name']);?>
        <?= ($append['is_required'] == 1)?'<span class="attent">※必須</span>':'';?>
    </td>
    <td>
        <?= $this->Form->input("info_append_items.{$num}.id",['type' => 'hidden','value' => empty($data['info_append_items'][$num]['id'])?'':$data['info_append_items'][$num]['id']]);?>
        <?= $this->Form->input("info_append_items.{$num}.append_item_id",['type' => 'hidden','value' => $append['id']]);?>
        <?= $this->Form->input("info_append_items.{$num}.is_required",['type' => 'hidden','value' => $append['is_required']]);?>
        <?= $this->Form->input("info_append_items.{$num}.value_textarea",['type' => 'hidden','value' => '']);?>
        <?= $this->Form->input("info_append_items.{$num}.value_date",['type' => 'hidden','value' => '0']);?>
        <?= $this->Form->input("info_append_items.{$num}.value_datetime",['type' => 'hidden','value' => '0000-00-00']);?>
        <?= $this->Form->input("info_append_items.{$num}.value_time",['type' => 'hidden','value' => '0']);?>
        <?= $this->Form->input("info_append_items.{$num}.value_int",['type' => 'hidden','value' => '0']);?>
        <?= $this->Form->input("info_append_items.{$num}.value_decimal",['type' => 'hidden','value' => '0']);?>
        <?= $this->Form->input("info_append_items.{$num}.file",['type' => 'hidden','value' => '']);?>
        <?= $this->Form->input("info_append_items.{$num}.file_size",['type' => 'hidden','value' => '0']);?>
        <?= $this->Form->input("info_append_items.{$num}.file_extension",['type' => 'hidden','value' => '']);?>
        <?= $this->Form->input("info_append_items.{$num}.image",['type' => 'hidden','value' => '']);?>
<?php 
if(empty($append['max_length']) || $append['max_length'] == 0){
$length = '';
}else{
$length = $append['max_length'];
}
?>
        <?= $this->Form->input("info_append_items.{$num}.value_decimal",['type'=>'checkbox', 'value' => '1', 'id' => "append_{$num}_check"]); ?>
        <label for="append_<?= h($num) ?>_check"><?= h($label_message) ?></label>
        <?= $this->Form->error("{$slug}.{$append['slug']}") ?>
    </td>
</tr>