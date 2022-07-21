<div class="title_area">
    <h1><?= h($page_title); ?></h1>
    <div class="pankuzu">
        <ul>
            <?= $this->element('pankuzu_home'); ?>
            <li><span><?= h($page_title); ?> </span></li>
        </ul>
    </div>
</div>

<?php
//データの位置まで走査
$count = array(
    'total' => 0,
    'enable' => 0,
    'disable' => 0
);
$count['total'] = $data_query->count();

?>

<?= $this->element('error_message'); ?>

<div class="content_inr">

    <div class="box">
        <h3 style="margin-bottom:20px;">オプション</h3>
        <div class="btn_area" style="text-align:left;margin-left: 20px;margin-bottom: 10px !important;">
            <a href="/user/Items/ListCategory" class="btn_send btn_search" style="width:130px;text-align:center;">カテゴリ一覧</a>
            <a href="<?= $this->Url->build(array('action' => 'edit', '?' => ['sch_page_id' => $sch_page_id, 'sch_category_id' => $sch_category_id])); ?>" class="btn_send btn_search">新規登録</a>
        </div>
    </div>

    <div class="box">
        <h3 class="box__caption--count"><span><?= h($page_title); ?> 登録一覧</span><span class="count"><?php echo $numrows; ?>件の登録</span></h3>

        <?= $this->element('pagination') ?>

        <div class="table_area">
            <table class="table__list">
                <colgroup>
                    <col style="width: 135px;">
                    <col style="width: 75px;">
                    <col style="width: 100px;">
                    <col style="width: 220px">
                    <col>
                    <col style="width: 150px">
                    <col style="width: 75px;">
                    <?php if ($this->Common->isViewSort($page_config, $sch_category_id)) : ?>
                        <col style="width: 150px">
                    <?php endif; ?>

                </colgroup>

                <tr>
                    <th>掲載</th>
                    <th>表示番号</th>
                    <th>掲載日</th>
                    <th>カテゴリー</th>
                    <th style="text-align:center;"><?php if ($this->Common->isCategoryEnabled($page_config)) {
                                                        echo 'カテゴリ/';
                                                    } ?>タイトル</th>
                    <th>写真</th>
                    <th style="text-align:left;">確認</th>
                    <?php if ($this->Common->isViewSort($page_config, $sch_category_id)) : ?>
                        <th>順序の変更</th>
                    <?php endif; ?>

                </tr>

                <?php
                foreach ($data_query->toArray() as $key => $data) :
                    $no = sprintf("%02d", $data->id);
                    $id = $data->id;
                    $scripturl = '';
                    if ($data['status'] === 'publish') {
                        $count['enable']++;
                        $status = true;
                        $status_text = '掲載中';
                        $status_class = 'visible';
                        $status_btn_class = 'visi';
                    } else {
                        $count['disable']++;
                        $status = false;
                        $status_text = '下書き';
                        $status_class = 'unvisible';
                        $status_btn_class = 'unvisi';
                    }

                    if ($page_config->is_public_date && $data->status == 'publish') {
                        $now = new \DateTime();
                        if ($data->start_date->format('Y-m-d') > $now->format('Y-m-d')) {
                            // 掲載待ち
                            $status_class = 'unvisible';
                            $status_text = '掲載待ち';
                        } elseif ((!empty($data->end_date) && $data->end_date->format('Y-m-d') != '0000-00-00') && $data->end_date->format('Y-m-d') < $now->format('Y-m-d')) {
                            // 掲載終了
                            $status_class = 'unvisible';
                            $status_text = '掲載終了';
                        }
                    }

                    $preview_url = __("{0}detail/{1}?preview=on", [$preview_slug_dir, $data->id]);
                    ?>
                    <a name="m_<?= $id ?>"></a>
                    <tr class="<?= $status_class; ?>" id="content-<?= $data->id ?>">
                        <td>
                            <div class="<?= $status_btn_class; ?>"><?= $this->Html->link($status_text, array('action' => 'enable', $data->id, '?' => $query), ['class' => 'scroll_pos']) ?></div>
                        </td>

                        <td title="表示順：<?= $data->position ?>">
                            <?= $data->id ?>
                        </td>

                        <td style="text-align: center;">
                            <?= !empty($data->start_date) ? $data->start_date : "&nbsp;" ?>
                        </td>

                        <td>

                        </td>

                        <td>
                            <?php if ($this->Common->isCategoryEnabled($page_config)) : ?>
                                <?= $this->Html->view((!empty($data->category->name) ? $data->category->name : '未設定'), ['before' => '【', 'after' => '】<br>']); ?>
                            <?php endif; ?>
                            <?= $this->Html->link(h($data->title), ['action' => 'edit', $data->id, '?' => $query], ['escape' => false, 'class' => 'btn btn-light w-100 text-left']) ?>
                        </td>

                        <td>

                        </td>

                        <td>
                            <div class="prev"><a href="<?= $preview_url ?>" target="_blank">プレビュー</a></div>
                        </td>

                        <?php
                            if ($this->Common->isViewSort($page_config, $sch_category_id)) : ?>
                            <td>
                                <ul class="ctrlis">
                                    <?php if (!$this->Paginator->hasPrev() && $key == 0) : ?>
                                        <li class="non">&nbsp;</li>
                                        <li class="non">&nbsp;</li>
                                    <?php else : ?>
                                        <li class="cttop"><?= $this->Html->link('top', array('action' => 'position', $data->id, 'top', '?' => $query), ['class' => 'scroll_pos']) ?></li>
                                        <li class="ctup"><?= $this->Html->link('top', array('action' => 'position', $data->id, 'up', '?' => $query), ['class' => 'scroll_pos']) ?></li>
                                    <?php endif; ?>

                                    <?php if (!$this->Paginator->hasNext() && $key == count($datas) - 1) : ?>
                                        <li class="non">&nbsp;</li>
                                        <li class="non">&nbsp;</li>
                                    <?php else : ?>
                                        <li class="ctdown"><?= $this->Html->link('top', array('action' => 'position', $data->id, 'down', '?' => $query), ['class' => 'scroll_pos']) ?></li>
                                        <li class="ctend"><?= $this->Html->link('bottom', array('action' => 'position', $data->id, 'bottom', '?' => $query), ['class' => 'scroll_pos']) ?></li>
                                    <?php endif; ?>
                                </ul>
                            </td>
                        <?php endif; ?>

                    </tr>

                <?php endforeach; ?>

            </table>

        </div>

        <div class="btn_area" style="margin-top:10px;"><a href="<?= $this->Url->build(array('action' => 'edit', '?' => ['sch_page_id' => $sch_page_id, 'sch_category_id' => $sch_category_id])); ?>" class="btn_confirm btn_post">新規登録</a></div>

        <?= $this->element('pagination') ?>
    </div>
</div>
<?php $this->start('beforeBodyClose'); ?>
<link rel="stylesheet" href="/admin/common/css/cms.css">
<script>
    $(window).on('load', function() {
        $(window).scrollTop("<?= empty($query['pos']) ? 0 : $query['pos'] ?>");
    })

    function change_category() {
        $("#fm_search").submit();

    }
    $(function() {

        $('.scroll_pos').on('click', function() {
            var sc = window.pageYOffset;
            var link = $(this).attr("href");

            window.location.href = link + "&pos=" + sc;


            return false;
        });

    })
</script>
<?php $this->end(); ?>