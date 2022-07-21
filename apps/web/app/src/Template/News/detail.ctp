<?php $this->start('css') ?>
<link rel="stylesheet" href="/assets/css/info.css">
<?php $this->end() ?>
<main>
	<?php
	$style = 'width="900" height="550" loading="lazy" decoding="async"';
	$alt = "<?= h($detail->title) ?>";
	$photo = '/upload/Infos/images/' . $detail->image;
	if ($detail->image == null) {
		$style = '';
		$alt = '';
		$photo = '';
	}
	?>

	<div class="b-news-detail">
		<div class="row b-news-detail__row"><span class="b-news-detail__date"><?= (new DateTime($detail->start_date))->format('Y.m.d'); ?></span>
			<h2 class="b-news-detail__ttl"><?= h($detail->title) ?></h2>
			<div class="b-news-detail__ctn">
				<figure><img src="<?= $photo ?>" alt="<?= $alt ?>" <?= $style ?> />
				</figure>
				<p><?= nl2br(h($detail->notes)) ?></p>
				<div class="link-back"><a href="/info/">一覧に戻る</a></div>
			</div>
		</div>
		<div class="breadcrumb">
			<div class="row">
				<ul class="breadcrumb_lst">
					<li><a href="/">トップ</a></li>
					<li><a href="/info">新着情報</a></li>
					<li><?= h($detail->title) ?></li>
				</ul>
			</div>
		</div>
	</div>
</main>