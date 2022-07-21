<?php $this->start('css') ?>
<link rel="stylesheet" href="/assets/css/info.css">
<?php $this->end() ?>
<main>
	<div class="b-news-detail">
		<div class="row b-news-detail__row"><span class="b-news-detail__date"><?= (new DateTime($entity->start_date))->format('Y.m.d'); ?></span>
			<h2 class="b-news-detail__ttl"><?= h($entity->title) ?></h2>
			<div class="b-news-detail__ctn">
				<figure><img src="<?= '/upload/Infos/images/' . $entity->image ?>" alt="" width="900" height="550" loading="lazy" decoding="async" />
				</figure>
				<p><?= h($entity->notes) ?></p>
				<div class="link-back"><a href="/news/">一覧に戻る</a></div>
			</div>
		</div>
		<div class="breadcrumb">
			<div class="row">
				<ul class="breadcrumb_lst">
					<li><a href="/">トップ</a></li>
					<li>新着情報</li>
				</ul>
			</div>
		</div>
	</div>
</main>