<?php $this->start('css') ?>
<link rel="stylesheet" href="/assets/css/contact.css?v=d627c1c9c87f8fd61f7505f12a279ae4">
<?php $this->end() ?>
<main>

    <div class="b-contact">
        <div class="b-frm">
            <div class="row">
                <h2 class="ttl_01">お問い合わせ</h2>
                <p class="txt__top">入力内容ご確認の上、 <br class="show_sp">[送信する] ボタンをクリックしてください。</p>
                <?= $this->Form->create($contact, ['class' => 'frm__confirm']) ?>
                
                <input type="hidden" value="1" name="is_confirm_success">
                <?= $this->Form->control('desired', ['type' => 'hidden']) ?>
                <?= $this->Form->control('inquiry', ['type' => 'hidden']) ?>

                <div class="frm__group">
                    <div class="frm__lbl">お問い合わせ種別</div>
                    <div class="frm__box"><em class="txt__confirm"><?= $data['inquiry'] == 1 ? '採用' : '商品・その他' ?></em></div>
                </div>
                <div class="frm__group">
                    <div class="frm__lbl">お名前</div>
                    <div class="frm__box"><em class="txt__confirm"><?= h($data['name']) ?></em></div>
                    <?= $this->Form->control('name', ['type' => 'hidden']) ?>
                </div>
                <div class="frm__group">
                    <div class="frm__lbl">フリガナ</div>
                    <div class="frm__box"><em class="txt__confirm"><?= h($data['kana']) ?></em></div>
                    <?= $this->Form->control('kana', ['type' => 'hidden']) ?>
                </div>
                <div class="frm__group">
                    <div class="frm__lbl">電話番号</div>
                    <div class="frm__box"><em class="txt__confirm"><?= h($data['tel']) ?></em></div>
                    <?= $this->Form->control('tel', ['type' => 'hidden']) ?>
                </div>
                <div class="frm__group">
                    <div class="frm__lbl">E-mail</div>
                    <div class="frm__box"><em class="txt__confirm"><?= h($data['email']) ?></em></div>
                    <?= $this->Form->control('email', ['type' => 'hidden']) ?>
                </div>
                <div class="frm__group">
                    <div class="frm__lbl">お問い合わせ内容</div>
                    <div class="frm__box"><em class="txt__confirm"><?= nl2br(h($data['content'])) ?></em></div>
                    <?= $this->Form->control('content', ['type' => 'hidden']) ?>
                </div>
                <div class="frm__group">
                    <div class="frm__lbl">プライバシーポリシー</div>
                    <div class="frm__box"><em class="txt__confirm">同意する</em></div>
                </div>
                <div class="frm__btn">
                    <button class="btn__submit" type="submit">送信する</button>
                    <button class="btn__back" type="button" onclick="window.history.back();"><span>入力画面へ戻る</span></button>
                </div>
                <?= $this->Form->end() ?>
            </div>
        </div>
        <div class="breadcrumb">
            <div class="row">
                <ul class="breadcrumb_lst">
                    <li><a href="/">トップ</a></li>
                    <li>お問い合わせ</li>
                </ul>
            </div>
        </div>
    </div>
</main>