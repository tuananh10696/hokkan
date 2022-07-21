<?php $this->start('css') ?>
<link rel="stylesheet" href="/assets/css/contact.css?v=d627c1c9c87f8fd61f7505f12a279ae4">
<?php $this->end() ?>
<main>
	<div class="b-contact">
		<div class="b-frm">
			<div class="row">
				<h2 class="ttl_01">お問い合わせ</h2>
				<!-- <div class="b-top">
					<h3 class="b-top__ttl">お電話でのお問い合わせはこちら</h3>
					<div class="b-top__tel"><a class="tel" href="tel:0282-27-9570">0282-27-9570 </a><em class="desc">営業時間 ｜ 00:00〜00:00</em></div>
				</div> -->
				<p class="txt__top">
					下記の入力フォームに必要事項をご入力の上、
					[入力内容を確認する] ボタンを
					クリックしてください。<br><span class="req">必須</span>はご入力必須項目です。
				</p>
				<?= $this->Form->create($contact, ['class' => 'frm frm__error', 'name' => 'contact']) ?>
				<?php $e = '';
				$array = 'frm__control';
				if (isset($error['inquiry']) && !empty($error['inquiry'])) {
					$e = '<em class="txt__error">' . array_values($error['inquiry'])[0] . '</em>';
					$array = 'frm__control bkg__error';
				}
				?>
				<div class="frm__group">
					<div class="frm__lbl">お問い合わせ種別<span class="req">必須</span><?= $e ?></div>
					<div class="frm__box">
						<ul class="frm__radio">

							<li>
								<label>
									<input class="<?= $array ?>" type="radio" name="inquiry" value="1" checked>
									<span class="rdo__lbl">採用</span>
								</label>
							</li>
							<li>
								<label>
									<input class="<?= $array ?>" type="radio" name="inquiry" value="2" <?= isset($data['inquiry']) && intval($data['inquiry']) == 2  ? 'checked="checked"' : '' ?>>
									<span class="rdo__lbl">商品・その他</span>
								</label>
							</li>
						</ul>
					</div>
				</div>

				<div class="frm__group">
					<?php $e = '';
					$array = 'frm__control';
					if (isset($error['name']) && !empty($error['name'])) {
						$e = '<em class="txt__error">' . array_values($error['name'])[0] . '</em>';
						$array = 'frm__control bkg__error';
					}
					?>
					<div class="frm__lbl">お名前<span class="req">必須</span><?= $e ?></div>
					<div class="frm__box">
						<?= $this->Form->text('name', ['class' => $array, 'maxlength' => '30']) ?>
					</div>
				</div>
				<div class="frm__group">
					<?php $e = '';
					$array = 'frm__control';
					if (isset($error['kana']) && !empty($error['kana'])) {
						$e = '<em class="txt__error">' . array_values($error['kana'])[0] . '</em>';
						$array = 'frm__control bkg__error';
					}
					?>
					<div class="frm__lbl">フリガナ<span class="req">必須</span><?= $e ?></div>
					<div class="frm__box">
						<?= $this->Form->text('kana', ['class' => $array, 'maxlength' => '30',]) ?>
					</div>
				</div>
				<div class="frm__group">
					<?php $e = '';
					$array = 'frm__control';
					if (isset($error['tel']) && !empty($error['tel'])) {
						$e = '<em class="txt__error">' . array_values($error['tel'])[0] . '</em>';
						$array = 'frm__control bkg__error';
					}
					?>
					<div class="frm__lbl"><span>電話番号<br><em style="opacity: 0.5;font-size:12px">※ハイフン必須</em></span><span class="req">必須</span><?= $e ?></div>

					<div class="frm__box">
						<?= $this->Form->text('tel', ['class' => $array]) ?>
					</div>
				</div>
				<?php $e = '';
				$array = 'frm__control';
				if (isset($error['email']) && !empty($error['email'])) {
					$e = '<em class="txt__error">' . array_values($error['email'])[0] . '</em>';
					$array = 'frm__control bkg__error';
				}
				?>
				<div class="frm__group">
					<div class="frm__lbl">E-mail<span class="req">必須</span><?= $e ?></div>
					<div class="frm__box">
						<?= $this->Form->text('email', ['class' => $array]) ?>
					</div>
				</div>
				<div class="frm__group">
					<?php $e = '';
					$array = 'frm__textarea';
					if (isset($error['content']) && !empty($error['content'])) {
						$e = '<em class="txt__error">' . array_values($error['content'])[0] . '</em>';
						$array = 'frm__textarea bkg__error';
					}
					?>
					<div class="frm__lbl">お問い合わせ内容<span class="req">必須</span><?= $e ?></div>
					<div class="frm__box">
						<?= $this->Form->textarea('content', ['class' => $array, 'maxlength' => '1000']) ?>
					</div>
				</div>
				<div class="frm__privacy">

					<?php $e = '';
					$array = 'frm__control';
					if (isset($error['is_accept']) && !empty($error['is_accept'])) {
						$e = '<em class="txt__error">' . array_values($error['is_accept'])[0] . '</em>';
						$array = 'frm__control bkg__error';
					}
					?>
					<div class="frm__checkbox">
						<?= $this->Form->checkbox('is_accept', ['label' => false, 'div' => false, 'id' => 'privacy', 'error' => false, 'required' => false]); ?>
						<label for="privacy"><a href="/privacy/" target="_blank">プライバシーポリシー</a>に同意する</label><?= $e ?>
					</div>
					<p class="txt_privacy">[入力内容を確認する] ボタンをクリックして入力内容のご確認をお願いします。</p>
				</div>
				<div class="frm__btn">
					<button class="btn__submit" type="button" onclick="document.contact.submit();">入力内容を確認する</button>
					<a href="/contact" class="btn__reset" rel="nofollow">入力内容をリセットする</a>
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