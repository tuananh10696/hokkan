<?php $this->start('css') ?>
<link rel="stylesheet" href="/assets/css/info.css">
<?php $this->end() ?>
<main>
	<div class="b-news">
		<div class="row b-news__row">
			<h2 class="ttl_01">新着情報</h2>
			<ul class="b-news__list">
				<?php foreach ($datas as $data) : ?>
					<?php
						$photo = '/upload/Infos/images/' . $data->image;
						if ($data->image == null) {
							$photo = '/assets/images/common/img02.jpeg';
						}
						?>
					<li class="b-news__item">
						<a href="<?= '/info/' . $data->id ?>">
							<figure class="b-news__img"><img class="fit" src="<?= $photo ?>" alt="" width="178" height="169" loading="lazy" decoding="async" />
							</figure>
							<div class="b-news__caption"> <span class="date"> <?= (new DateTime($data->start_date))->format('Y.m.d'); ?></span>
								<h3 class="ttl"><?= h($data->title) ?> </h3>
								<p class="txt"><?= h(trim(mb_convert_kana($data->notes, "s", 'UTF-8'))) ?></p>
							</div>
						</a>
					</li>
				<?php endforeach; ?>
			</ul>
			<ul class="pagination">
				<?php if ($this->Paginator->hasPrev() || $this->Paginator->hasNext()) : ?>
					<?php if ($this->Paginator->hasPrev()) : ?><?= $this->Paginator->prev('<') ?><?php endif; ?>
					<?= $this->Paginator->numbers(); ?>
					<?php if ($this->Paginator->hasNext()) : ?><?= $this->Paginator->next('>') ?><?php endif; ?>
				<?php endif; ?>
			</ul>
		</div>
		<div class="breadcrumb">
			<div class="row">
				<ul class="breadcrumb_lst">
					<li><a href="/">TOP</a></li>
					<li>新着情報</li>
				</ul>
			</div>
		</div>
	</div>
</main>