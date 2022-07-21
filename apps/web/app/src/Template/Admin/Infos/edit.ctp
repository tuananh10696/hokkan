<div class="title_area">
      <h1>お知らせ</h1>
      <div class="pankuzu">
        <ul>
          <?= $this->element('pankuzu_home'); ?>
          <li><a href="<?= $this->Url->build(array('action' => 'index')); ?>">お知らせ</a></li>
<!--            $mainCategory-->
          <li><span><?= ($data['id'] > 0)? '編集': '新規登録'; ?></span></li>
        </ul>
      </div>
    </div>

    <?= $this->element('error_message'); ?>
    <div class="content_inr">
      <div class="box">
        <h3><?= ($data["id"] > 0)? '編集': '新規登録'; ?></h3>
        <div class="table_area form_area">
<?= $this->Form->create($entity, array('type' => 'file', 'context' => ['validator' => 'default']));?>
<?= $this->Form->hidden('position'); ?>
<?php if ($data['id'] > 0): ?>
<?= $this->Form->input('id', array('type' => 'hidden'));?>
<?php endif; ?>
          <table class="vertical_table">

            <tr>
              <td>No</td>
              <td><?= ($data["id"])? sprintf('No. %04d', $data["id"]) : "新規" ?></td>
            </tr>

            <tr>
              <td>掲載日</td>
              <td><?= $this->Form->input('date', array('type' => 'text', 'class' => 'datepicker', 'data-auto-date' => '1', 'default' => date('Y-m-d')));?></td>
            </tr>

            <tr>
              <td>タイトル<span class="attent">※必須</span></td>
              <td>
                <?= $this->Form->input('title', array('type' => 'text', 'maxlength' => 40,));?>
              </td>
            </tr>

<?php if (true): ?>
            <tr>
              <td>カテゴリ</td>
              <td><?= $this->Form->input('category_id', array('type' => 'select',
                                                              'options' => $category_list,
                                                              'empty' => ['0' => '選択してください']
                                                              )); ?>
                                                                
              </td>
            </tr>
<?php endif; ?>
            <tr>
              <td>内容</td>
              <td>
                <div class="content_box1"><?= $this->Form->input('content', array('type' => 'textarea', 'style' => 'width:600px;height:270px;','cols' => '30','rows' => '6', 'id'=>'content_type1'));?></div>
              </td>
            </tr>

            <tr>
              <td>画像</td>
              <td>
                <?php $image_column = 'image'; ?>
                <?= $this->Form->input($image_column, array('type' => 'file'));?>
                <div class="remark">※jpeg , jpg , gif , png ファイルのみ</div>
                <br />
                <?php if (!empty($data['attaches'][$image_column]['0'])) :?>
                  <img src="<?= $this->Url->build($data['attaches'][$image_column]['0'])?>"><br >
                  <?= $this->Form->input("_old_{$image_column}", array('type' => 'hidden', 'value' => $data[$image_column])); ?>
                  <div class="btn_area"><a href="javascript:kakunin('画像を削除します。よろしいですか？','<?= $this->Url->build(array('action' => 'delete', $data['id'], 'image', $image_column)) ?>')" class="btn_delete">画像の削除</a></div>
                <?php endif;?>
              </td>
            </tr>

            <tr>
              <td>ファイル</td>
              <td class="field">
              <?php $_column = 'file'; ?>
                <div class="manu">
                  <?= $this->Form->input('file', array('type' => 'file'));?>
                  <div class="remark">※PDF、Office(.doc, .docx, .xls, .xlsx)ファイルのみ</div>

                  <?php if (!empty($data['attaches'][$_column]['0'])) :?>
                  <ul class="file">
                  <li class="<?=$data['attaches'][$_column]['extention']?>"><?= $this->Html->link($data[$_column.'_name'], $data['attaches'][$_column]['0'], array('target' => '_blank'))?></li>
                  </ul>
                  <?= $this->Form->input("_old_{$_column}", array('type' => 'hidden', 'value' => $data[$_column])); ?>
                  <ul class="return"><li class="reset"><a href="javascript:kakunin('ファイルを削除します。よろしいですか？','<?= $this->Url->build(array('action' => 'delete', $data['id'], 'file', $_column)) ?>')">ファイルの削除</a></li></ul>
                  <?php endif;?>

                </div>
              </td>
            </tr>

            <tr>
              <td>記事表示</td>
              <td>
                  <?= $this->Form->input('status', array('type' => 'select', 'options' => array('draft' => '下書き', 'publish' => '掲載する')));?>
              </td>
            </tr>
        </table>

        <div class="btn_area">
        <?php if (!empty($data['id']) && $data['id'] > 0){ ?>
            <a href="#" class="btn_confirm submitButton">変更する</a>
            <a href="javascript:kakunin('データを完全に削除します。よろしいですか？','<?= $this->Url->build(array('action' => 'delete', $data['id'], 'content'))?>')" class="btn_delete">削除する</a>
        <?php }else{ ?>
            <a href="#" class="btn_confirm submitButton">登録する</a>
        <?php } ?>
        </div>

        </div>
        <?= $this->Form->end();?>
      </div>
    </div>


<?php $this->start('beforeBodyClose');?>
<link rel="stylesheet" href="/admin/common/css/cms.css">
<link rel="stylesheet" href="/admin/common/css/jquery-ui-1.9.2.custom/css/smoothness/jquery-ui-1.9.2.custom.min.css">
<script src="/admin/common/js/jquery-ui-1.9.2.custom.min.js"></script>
<script src="/admin/common/js/jquery.ui.datepicker-ja.js"></script>
<script src="/admin/common/js/cms.js"></script>
<!-- redactor -->
<link rel="stylesheet" href="/admin/common/css/redactor/redactor.css">
<script src="/admin/common/js/redactor/redactor.js"></script>
<!-- redactor plugins -->
<script src="/admin/common/js/redactor/ja.js"></script>
<script src="/admin/common/js/redactor/fontcolor.js"></script>
<script src="/admin/common/js/redactor/fontsize.js"></script>
<script src="/admin/common/js/redactor/alignment.js"></script>
<script src="/admin/common/js/redactor/source.js"></script>
<!-- <script src="/admin/common/js/redactor/imagemanager.js"></script> -->

<script>
$(function () {
    // 本文
    $("#content_type1").redactor({
      minHeight: 200, // pixels
      focus:false,
      lang:"ja",
      //imageUpload: '/admin/news_images/image_upload.json',
      //imageManagerJson: '/admin/news_images/image_list.json',
      plugins: ['fontcolor', 'fontsize','alignment', 'source' /*'imagemanager'*/],
      buttons: [ 'html', 'formatting', 'bold', 'italic', 'deleted',
       'unorderedlist', 'orderedlist', 
      //  'outdent', 'indent',
      //  'image', 'file', 'link', 
      //  'alignment',
       'horizontalrule'],
      formatting: ['p', 'blockquote'],
      formattingAdd: [
        {
          title: 'Clear format',
          tag: 'p',
        }
      ]
  });


});
</script>
<?php $this->end();?>
