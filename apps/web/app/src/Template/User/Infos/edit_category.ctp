<div class="title_area">
    <h1>カテゴリー管理</h1>
    <div class="pankuzu">
        <ul>
            <?= $this->element('pankuzu_home'); ?>
            <?php if ($this->Session->read('user_role') == User::ROLE_ADMIN) : ?>
                <li><a href="/user"><?= PAGE_TITLE ?>管理</a></li>
            <?php endif ?>
            <li><a href="<?= $this->Url->build(['action' => 'listCategory']); ?>">カテゴリー一覧</a></li>
            <li><span><?= (@$entity->id > 0) ? '編集' : '新規登録'; ?></span></li>
        </ul>
    </div>
</div>


<div class="content_inr">
    <div class="box">
        <h3><?= (@$entity->id > 0) ? '編集' : '新規登録'; ?></h3>
        <div class="table_area form_area">
            <?= $this->Form->create($entity, ['type' => 'file']); ?>
            <table class="vertical_table table__meta">
                <tr>
                    <td>記事番号</td>
                    <td><?= (@$entity->id) ? sprintf('No. %04d', $entity->id) : "新規" ?></td>
                </tr>
                <tr>
                    <td>掲載日<span class="attent">※必須</span></td>
                    <td>
                        <?= $this->Form->input('publish_at', ['type' => 'text', 'class' => 'date_picker', 'value' => $entity->publish_at ? (new DateTime($entity->publish_at))->format('Y-m-d') : date('Y-m-d'), 'style' => 'width: 180px;', 'readonly' => 'readonly']); ?>
                    </td>
                </tr>
                <tr>
                    <td>カテゴリー名<span class="attent">※必須</span></td>
                    <td>
                        <?= $this->Form->input('cat_name', ['type' => 'text', 'maxlength' => 100, 'style' => 'width:100%;']); ?>
                    </td>
                </tr>
                <tr>
                    <td>リンクカラー</td>
                    <td>
                        <?= $this->Form->radio('color', $list_color, ['hiddenField' => false, 'default' => 'col00']); ?>
                        <?= '' //$this->Form->input('color', ['type' => 'color', "id" => "link-color", 'style' => 'height:30px;', 'default' => '#000000']); 
                        ?>
                    </td>
                </tr>
            </table>

            <div class="btn_area btn_area--center" id="editBtnBlock">
                <?php if (@$entity->id > 0) { ?>
                    <a href="#" class="btn_confirm submitButton submitButtonPost" id="btnSave">変更する</a>
                    <a href="javascript:kakunin('データを完全に削除します。よろしいですか？','<?= $this->Url->build(['action' => 'deleteCategory', $entity->id, 'content']) ?>')" class="btn_delete">削除する</a>
                <?php } else { ?>
                    <a href="#" class="btn_confirm submitButton submitButtonPost" id="btnSave">登録する</a>
                <?php } ?>
            </div>

            <?= $this->Form->end(); ?>
        </div>
    </div>
</div>

<?php $this->start('beforeBodyClose'); ?>
<?= $this->Html->script([
    "/user/common/js/jquery-ui-1.9.2.custom.min.js",
    "/user/common/js/jquery.ui.datepicker-ja",
]); ?>
<script>
    function kakunin(msg, url) {
        if (confirm(msg)) {
            location.href = url;
        }
    }
    $(function() {
        $.datetimepicker.setLocale('ja');
        $('.datetimepicker').datetimepicker({
            format: 'Y-m-d H:i'
        });
        $('.date_picker').datepicker({
            dateFormat: 'yy-mm-dd'
        });
    });
</script>
<?php $this->end() ?>