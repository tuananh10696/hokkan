<div class="title_area">
    <h1>カテゴリー管理</h1>
    <div class="pankuzu">
        <ul>
            <?= $this->element('pankuzu_home'); ?>

            <li><span>カテゴリー一覧</span></li>
        </ul>
    </div>
</div>

<div class="content_inr">
    <div class="box">
        <h3 class="box__caption--count">
            <span>カテゴリー一覧</span>
            <span class="count"><?= @$count ?>件の登録</span>
        </h3>
        <div class="btn_area" style="margin-top:10px;">
            <a href="<?= $this->Url->build(['action' => 'editCategory']); ?>" class="btn_confirm btn_post">新規登録</a>
        </div>

        <div class="table_area">
            <table class="table__list">
                <colgroup>
                    <col style="width: 135px;">
                    <col style="width: 225px;">
                    <col style="width: 400px;">
                    <col>
                    <col style="width: 100px;">
                </colgroup>
                <tr>
                    <th style="text-align: center;">表示番号</th>
                    <th style="text-align: center;">作成日</th>
                    <th style="text-align: center;">カテゴリー名</th>
                    <th style="text-align: center;">説明</th>
                    <th style="text-align: center;">操作</th>
                </tr>
                <?php $i = 0 ?>
                <?php foreach ($list_category as $data) : ?>
                    <?php $i++ ?>
                    <td style="text-align: center;"><?= sprintf("%02d", $i); ?></td>
                    <td style="text-align: center;"><?= @$data->created ?></td>
                    <td><?= $this->Html->link(html_decode($data->name), ['action' => 'editCategory', $data->id]) ?></td>
                    <td><?= $this->Html->link(html_decode($data->describe), ['action' => 'editCategory', $data->id]) ?></td>
                    <td><a href="javascript:kakunin('データを完全に削除します。よろしいですか？','<?= $this->Url->build(array('action' => 'delete', $data['id'], 'content')) ?>')" class="btn_delete">削除する</a></td>
                    </tr>
                <?php endforeach; ?>
            </table>
        </div>
        <div class="btn_area" style="margin-top:10px;">
            <a href="<?= $this->Url->build(['action' => 'editCategory']); ?>" class="btn_confirm btn_post">新規登録</a>
        </div>
    </div>
</div>

<?php $this->start('beforeBodyClose'); ?>

<script>
    function kakunin(msg, url) {
        if (confirm(msg)) {
            location.href = url;
        }
    }

    function requestAjax(url, method, data, callback) {
        $.ajax({
            'url': url,
            'method': method,
            'data': {
                ...data,
                _csrfToken: csrfToken
            },
            'dataType': 'json',
            'success': function(resp) {
                if (callback) callback(resp);
            }
        });
    }

    function changeColor(e, id) {
        requestAjax(
            `/user/user-sites/edit-category/${id}`,
            'post', {
                'color': $(e).val()
            },
            false
        );
    }

    function changeStatus(status, id) {
        function _reload(resp) {
            if (resp.success) window.location.reload();
        }

        requestAjax(
            `/user/user-sites/edit-category/${id}`,
            'post', {
                'status': status
            },
            _reload
        );
    }
</script>

<?php $this->end(); ?>