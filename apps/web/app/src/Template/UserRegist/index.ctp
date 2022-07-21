<h1>ユーザー登録画面</h1>

<?= $this->Form->create($entity, ['type' => 'post', 'name' => 'fm']); ?>
    <dl>
        <dt>希望するURL</dt>
        <dd>
            <?= $this->Form->input('username', ['type' => 'text']); ?>
        </dd>

        <dt>メールアドレス</dt>
        <dd>
            <?= $this->Form->input('email', ['type' => 'text']); ?>
        </dd>

        <dt>パスワード</dt>
        <dd>
            <?= $this->Form->input('password', ['type' => 'password']); ?>
        </dd>

        <dt>パスワード　確認</dt>
        <dd>
            <?= $this->Form->input('password_confirm', ['type' => 'password']); ?>
        </dd>

    </dl>
    
    <div>
        <a href="#" class='submit' id="btnSubmit">確認画面へ</a>
    </div>

<?= $this->Form->end(); ?>

<script>
    var btn = document.getElementById('btnSubmit');

    btn.addEventListener('click', function() {
        document.fm.submit();
        return false;
    });
</script>