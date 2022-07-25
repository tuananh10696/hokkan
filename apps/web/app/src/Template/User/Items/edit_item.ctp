<?php $this->start('beforeHeaderClose'); ?>
<link href="https://fonts.googleapis.com/css2?family=Kosugi+Maru&family=Noto+Sans+JP:wght@300&family=Noto+Serif+JP&display=swap" rel="stylesheet">
<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
<?php

?>
<link rel="stylesheet" href="/user/common/css/info.css">
<?php $this->end(); ?>

<div class="title_area">
    <h1><?= h($page_title); ?></h1>
    <div class="pankuzu">
        <ul>
            <?= $this->element('pankuzu_home'); ?>
            <li><a href="<?= $this->Url->build(array('action' => 'index', '?' => $query)); ?>"><?= h($page_title); ?></a></li>
            <li><span><?= (@$data['id'] > 0) ? '編集' : '新規登録'; ?></span></li>
        </ul>
    </div>
</div>

<?= $this->element('error_message'); ?>
<div class="content_inr">
    <div class="box">
        <h3><?= (@$data["id"] > 0) ? '編集' : '新規登録'; ?></h3>
        <div class="table_area form_area">
            <?= $this->Form->create($entity, array('type' => 'file', 'context' => ['validator' => 'default'], 'name' => 'fm')); ?>
            <?= $this->Form->hidden('position'); ?>
            <?= $this->Form->input('id', array('type' => 'hidden', 'value' => h($entity->id))); ?>
            <?= $this->Form->input('page_config_id', ['type' => 'hidden']); ?>
            <?= $this->Form->input('meta_keywords', ['type' => 'hidden']); ?>
            <?= $this->Form->input('postMode', ['type' => 'hidden', 'value' => 'save', 'id' => 'idPostMode']); ?>
            <input type="hidden" name="MAX_FILE_SIZE" value="<?= (1024 * 1024 * 5); ?>">
            <table class="vertical_table table__meta">

                <tr>
                    <td>記事番号</td>
                    <td><?= (@$data["id"]) ? sprintf('No. %04d', h($data["id"])) : "新規" ?></td>
                </tr>

                <?php if ($page_config->is_public_date) : ?>
                    <tr>
                        <td>掲載期間<span class="attent">※必須</span></td>
                        <td>
                            <?= $this->Form->input('start_date', array('type' => 'text', 'class' => 'datepicker', 'data-auto-date' => '1', 'default' => date('Y-m-d'), 'style' => 'width: 120px;')); ?> ～
                            <?= $this->Form->input('end_date', array('type' => 'text', 'class' => 'datepicker', 'style' => 'width: 120px;')); ?>
                            <div>※開始日のみ必須。終了日を省略した場合は下書きにするまで掲載されます。</div>
                        </td>
                    </tr>
                <?php else : ?>
                    <tr>
                        <td>掲載日<span class="attent">※必須</span></td>
                        <td>
                            <?= $this->Form->input('start_date', array('type' => 'text', 'class' => 'datepicker', 'data-auto-date' => '1', 'default' => date('Y-m-d'), 'style' => 'width: 120px;')); ?>

                        </td>
                    </tr>
                <?php endif; ?>


                <tr>
                    <td>カテゴリ<span class="attent">※必須</span></td>
                    <td>
                        <?= $this->Form->input('category_id', ['type' => 'select', 'options' => $category_list, 'empty' => ['0' => '選択してください']]); ?>
                    </td>
                </tr>


                <tr>
                    <td>タイトル<span class="attent">※必須</span></td>
                    <td>
                        <?= $this->Form->input('title', array('type' => 'text', 'maxlength' => 100, 'style' => 'width:100%;')); ?>
                        <br><span>※100文字以内で入力してください</span>
                    </td>
                </tr>

                <?php if (false) : ?>
                    <tr>
                        <td class="head m-0 p-0" colspan="2">
                            <button class="btn w-100 btn-light" type="button" data-toggle="collapse" data-target="#optionHeaderItem" aria-expanded="false">
                                <span>詳細項目</span> <i class="fas fa-angle-down"></i>
                            </button>
                        </td>
                    </tr>

                    <tbody id="optionHeaderItem" class="collapse">
                    <?php endif; ?>
                    <?php if (true) : ?>
                        <tr>
                            <td>内容
                                <div>(一覧と詳細に表示)</div>
                            </td>
                            <td>
                                <?= $this->Form->input('notes', ['type' => 'textarea', 'maxlength' => '1000', 'style' => '', 'rows' => '20']); ?>
                                <br><span>※1000文字まで <br></span>
                            </td>
                        </tr>
                    <?php endif; ?>

                    <?php $image_column = 'image'; ?>

                    <?php if (true) : ?>
                        <tr>
                            <td>メイン画像
                                <?php if ($page_config->list_style == $PageConfig::LIST_STYLE_THUMBNAIL) : ?>
                                    <div>(一覧と詳細に表示)</div>
                                <?php else : ?>
                                    <div>(詳細に表示)</div>
                                <?php endif; ?>
                            </td>
                            <td class="edit_image_area">

                                <ul>
                                    <?php if (!empty($data['attaches'][$image_column]['0'])) : ?>
                                        <li>
                                            <a href="<?= $data['attaches'][$image_column]['0']; ?>" class="pop_image_single">
                                                <img src="<?= $this->Url->build($data['attaches'][$image_column]['0']) ?>" style="width: 300px;">
                                                <?= $this->Form->input("attaches.{$image_column}.0", ['type' => 'hidden']); ?>
                                            </a><br>
                                            <?= $this->Form->input("_old_{$image_column}", array('type' => 'hidden', 'value' => h($data[$image_column]))); ?>
                                            <div class="btn_area" style="width: 300px;">
                                                <a href="javascript:kakunin('画像を削除します。よろしいですか？','<?= $this->Url->build(array('action' => 'delete', $data['id'], 'image', $image_column)) ?>')" class="btn_list_delete">画像の削除</a>
                                            </div>
                                        </li>
                                    <?php endif; ?>

                                    <li>
                                        <?= $this->Form->input($image_column, array('type' => 'file', 'accept' => 'image/jpeg,image/png,image/gif', 'id' => 'idMainImage', 'class' => 'attaches')); ?>
                                        <div class="remark">※jpeg , jpg , gif , png ファイルのみ</div>
                                        <div><?= $this->Form->getRecommendSize('Infos', 'image', ['before' => '※', 'after' => '']); ?></div>
                                        <div>※ファイルサイズ５MB以内</div>
                                        <br />
                                    </li>

                                </ul>
                            </td>
                        </tr>
                    <?php else : ?>
                        <?= $this->Form->input($image_column, array('type' => 'hidden', 'value' => '')); ?>
                    <?php endif; ?>

                    <tr>
                        <td>記事表示</td>
                        <td>
                            <?= $this->Form->input('status', array('type' => 'select', 'options' => array('draft' => '下書き', 'publish' => '掲載する'))); ?>
                        </td>
                    </tr>


            </table>

            <div id="blockWork"></div>

            <div class="btn_area btn_area--center" id="editBtnBlock">
                <?php if (!empty($data['id']) && $data['id'] > 0) { ?>
                    <a href="#" class="btn_confirm submitButton" id="btnSave">変更する</a>
                    <a href="javascript:kakunin('データを完全に削除します。よろしいですか？','<?= $this->Url->build(array('action' => 'delete', $data['id'], 'content')) ?>')" class="btn_delete">削除する</a>
                <?php } else { ?>
                    <a href="#" class="btn_confirm submitButton" id="btnSave">登録する</a>
                <?php } ?>
                <!-- <a href="#" class="btn btn-info" id="btnPreview" style="line-height: 2.5rem;">保存前プレビュー</a>
        </div> -->

                <div id="deleteArea" style="display: hide;"></div>

                <?= $this->Form->end(); ?>

            </div>
        </div>
    </div>


    <?php $this->start('beforeBodyClose'); ?>
    <link rel="stylesheet" href="/user/common/css/cms.css">
    <script src="/user/common/js/jquery.ui.datepicker-ja.js"></script>
    <script src="/user/common/js/cms.js"></script>

    <!-- redactor -->
    <link rel="stylesheet" href="/user/common/css/redactor/redactor.min.css">
    <!-- <link rel="stylesheet" href="/user/common/css/redactor/inlinestyle.css"> -->
    <script src="/user/common/js/redactor/redactor-custom-min.js"></script>
    <!-- redactor plugins -->
    <script src="/user/common/js/redactor/ja.js"></script>
    <script src="/user/common/js/redactor/alignment.js"></script>
    <script src="/user/common/js/redactor/counter.js"></script>
    <script src="/user/common/js/redactor/fontcolor.js"></script>
    <script src="/user/common/js/redactor/fontsize.js"></script>
    <!-- <script src="/user/common/js/redactor/inlinestyle-ja.js"></script> -->

    <?= $this->Html->script('/user/common/js/system/pop_box'); ?>


    <script>
        var rownum = 0;
        var tag_num = <?= $info_tag_count; ?>;
        var max_row = 100;
        var pop_box = new PopBox();
        var out_waku_list = <?= json_encode($out_waku_list); ?>;
        var block_type_waku_list = <?= json_encode($block_type_waku_list); ?>;
        var block_type_relation = 14;
        var block_type_relation_count = 0;
        var max_file_size = <?= (1024 * 1024 * 5); ?>;
        var total_max_size = <?= (1024 * 1024 * 30); ?>;
        var form_file_size = 0;
        var page_config_id = <?= $page_config->id; ?>;
    </script>

    <script>
        function changeTargetType() {
            var type = $('#append_block-target_type td [type="radio"]:checked').val();
            if (type == 1) { // PDF
                $("#append_block-link").hide();
                $("#append_block-file").show();
            } else {
                $("#append_block-link").show();
                $("#append_block-file").hide();
            }
        }
        $(function() {
            <?php if (false) : //radioによる表示変更
                ?>
                changeTargetType();
                $('#append_block-target_type td [type="radio"]').on('change', function() {
                    changeTargetType();
                });
            <?php endif; ?>

            var custom_uploader = wp.media({
                title: 'Choose Image',
                library: {
                    type: 'image'
                },
                button: {
                    text: 'Choose Image'
                },
                multiple: false
            });

            var slug = null;

            $(".content_inr").on("click", " .media-upload", function(e) {
                slug = $(this).data('slug');
                e.preventDefault();
                custom_uploader.open();
            });

            custom_uploader.on("select", function() {
                var images = custom_uploader.state().get('selection');

                images.each(function(file) {
                    $("#append_block_image_" + slug + " .image-url").val(file.toJSON().url);

                    $("#append_block_image_" + slug + " .image-view-block").html('<img class="image-view" src="" width="260">');

                    $("#append_block_image_" + slug + " .image-view").attr("src", file.toJSON().url);
                });
            });
        });
    </script>

    <?= $this->Html->script('/user/common/js/info/edit'); ?>

    <?php $this->end(); ?>