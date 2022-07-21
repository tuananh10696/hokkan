<div class="title_area">
      <h1>お知らせ</h1>
      <div class="pankuzu">
        <ul>
          <?= $this->element('pankuzu_home'); ?>
          <li><span>お知らせ </span></li>
        </ul>
      </div>
    </div>

<?php
//データの位置まで走査
$count = array('total' => 0,
               'enable' => 0,
               'disable' => 0);
$count['total'] = $query->count();
?>
    <div class="content_inr">
    <div class="box">
<?= $this->Form->create(false, array('type' => 'get', 'id' => 'fm_search', 'url' => array('action' => 'index'))); ?>
            <h3>条件</h3>
            <div class="table_area form_area">
                <table class="vertical_table">
                    <tr>
                        <td style="width:100px;">ユーザー</td>
                        <td style="text-align:left;">
                            <?= $this->Form->input('sch_user',array('type' => 'select',
                                                                      'class' => 'require',
                                                                      'options' => $user_list,
                                                                      'value' => $sch_user,
                                                                      'empty' => ['0' => '全て']
                                                                      )); ?>
                        </td>
                        <td colspan="2">　</td>
                    </tr>
                </table>

                <div class="btn_area" style="text-align:right;">
                    <a href="javascript:void(0);" class="btn_send btn_search" id="btnSearch" style="width:130px;text-align:center;">検索</a>
                    <a href="<?= $this->Url->build(array('action' => 'index')); ?>" class="btn_confirm" style="width:130px;text-align:center;">検索条件クリア</a>
                </div>
            </div>
<?= $this->Form->end(); ?>
        </div>

      <div class="box">
        <h3>登録一覧</h3>
        <span class="result"><?php echo $count['total']; ?>件の登録</span>
        <div class="table_area">
          <table>
            <tr>
            <th style="width:4em;">掲載</th>
            <th style="width:4em;">No</th>
            <th style="width:200px;">ユーザー名</th>
            <th style="width:12em;">掲載日</th>
            <th style="text-align:left;">タイトル</th>
            <th style="text-align:left;width:27px;">確認</th>
            </tr>

<?php
foreach ($query->toArray() as $key => $data):
$no = sprintf("%02d", $data->id);
$id = $data->id;
$scripturl = '';
if ($data['status'] === 'publish') {
    $count['enable']++;
    $status = true;
} else {
    $count['disable']++;
    $status = false;
}

$preview_url = $this->Url->build(array('admin' => false, 'action' => 'detail', $data->id, '?' => array('preview' => 'on')));
?>
            <a name="m_<?= $id ?>"></a>
            <tr class="<?= $status ? "visible" : "unvisible" ?>" id="content-<?= $data->id ?>">
              <td><div class="<?= $status ? "visi" : "unvisi" ?>"><?= $this->Html->link(($status? "掲載中" : "下書き" ), array('action' => 'enable', $data->id) )?></div>
              </td>

              <td title="表示順：<?= $data->position?>">
                <?= $no?>
              </td>

              <td style="text-align: center;">
                <?= !empty($data->start_date) ? $data->start_date->format('Y.m.d') : "&nbsp;" ?>
              </td>

              <td>
                <?= $this->Html->link($data->title, $this->Url->build(array('action' => 'edit', $data->id)))?>
              </td>

              <td ><div class="prev"><a href="<?=$preview_url?>" target="_blank">プレビュー</a></div></td>

            </tr>

<?php endforeach; ?>

          </table>

        </div>
    </div>

<?php $this->start('beforeBodyClose');?>
<link rel="stylesheet" href="/admin/common/css/cms.css">
<script>

$(function () {

  $("#btnSearch").on('click', function() {
    $('#fm_search').submit();
    return false;
  });

})
</script>
<?php $this->end();?>
