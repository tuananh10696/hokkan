
<?php $this->start('beforeHeaderClose'); ?>

<?php $this->end(); ?>

<div class="title_area">
  <h1>カテゴリ</h1>
  <div class="pankuzu">
    <ul>
      <?= $this->element('pankuzu_home'); ?>
      <li><a href="<?= $this->Url->build(['controller' => 'page-configs']); ?>">コンテンツ設定</a></li>
      <li><a href="<?= $this->Url->build(['controller' => 'categories', '?' => ['sch_page_id' => $sch_page_id]]); ?>">カテゴリ</a></li>
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
<?= $this->Form->input('id', array('type' => 'hidden', 'value' => $entity->id));?>
<?= $this->Form->input('position', array('type' => 'hidden'));?>
<?= $this->Form->input('page_config_id', array('type' => 'hidden', 'value' => $sch_page_id));?>
          <table class="vertical_table table__meta">

            <tr>
              <td>カテゴリ名<span class="attent">※必須</span></td>
              <td>
                <?= $this->Form->input('name', array('type' => 'text', 'maxlength' => 40,));?>
                <br><span>※40文字以内で入力してください</span>
              </td>
            </tr>

          <?php if (false): ?>
            <tr>
              <td>識別子</td>
              <td>
                <?= $this->Form->input('identifier', ['type' => 'text']); ?>
                <br><span>※30文字以内で入力してください</span>
              </td>
            </tr>
          <?php endif; ?>

            <tr>
              <td>有効/無効</td>
              <td>
                  <?= $this->Form->input('status', array('type' => 'select', 'options' => array('draft' => '無効', 'publish' => '有効')));?>
              </td>
            </tr>

        </table>

        <div class="btn_area">
        <?php if (!empty($data['id']) && $data['id'] > 0){ ?>
            <a href="#" class="btn_confirm submitButtonPost">変更する</a>
            <a href="javascript:kakunin('データを完全に削除します。よろしいですか？','<?= $this->Url->build(array('action' => 'delete', $data['id'], 'content'))?>')" class="btn_delete">削除する</a>
        <?php }else{ ?>
            <a href="#" class="btn_confirm submitButtonPost">登録する</a>
        <?php } ?>
        </div>

        <div id="deleteArea" style="display: hide;"></div>

        <?= $this->Form->end();?>

        </div> 
      </div>
    </div>


<?php $this->start('beforeBodyClose');?>
<link rel="stylesheet" href="/user/common/css/cms.css">
<script src="/user/common/js/jquery.ui.datepicker-ja.js"></script>
<script src="/user/common/js/cms.js"></script>

<?php $this->end();?>
