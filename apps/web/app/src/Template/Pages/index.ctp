<?php $this->start('css') ?>
<link rel="stylesheet" href="/assets/css/top.css">
<?php $this->end() ?>
<?php $this->start('beforeCloseBody') ?>
<script src="/assets/js/index.js" defer></script>
<?php $this->end() ?>

<main>
    <div class="b-visual">
        <div class="swiper-container b-visual__slider">
            <div class="swiper-wrapper">
                <div class="swiper-slide">
                    <a href="#">
                        <div class="img-wrapper logo_1">
                            <picture>
                                <source media="(min-width: 769px)" srcset="/assets/images/top/visual-text.png" />
                                <source media="(max-width: 768px)" srcset="/assets/images/top/visual-text-sp.png" /><img class="fit" src="top/visual-text.png" srcset="top/visual-text.png" alt="北冠はまいにち美味い。" />
                            </picture>
                        </div>
                        <img class="fit" src="/assets/images/top/mv-01.jpg" alt="" width="2760" height="1720" loading="lazy" decoding="async" />
                    </a>
                </div>
                <div class="swiper-slide">
                    <a href="#">
                        <div class="img-wrapper logo_2">
                            <picture>
                                <source media="(min-width: 769px)" srcset="/assets/images/top/visual-text.png" />
                                <source media="(max-width: 768px)" srcset="/assets/images/top/visual-text-sp.png" /><img class="fit" src="top/visual-text.png" srcset="top/visual-text.png" alt="北冠はまいにち美味い。" />
                            </picture>
                        </div>
                        <img class="fit" src="/assets/images/top/mv-02.jpg" alt="" width="2760" height="1720" loading="lazy" decoding="async" />
                    </a>
                </div>
                <div class="swiper-slide">
                    <a href="#">
                        <div class="img-wrapper logo_3">
                            <picture>
                                <source media="(min-width: 769px)" srcset="/assets/images/top/visual-text.png" />
                                <source media="(max-width: 768px)" srcset="/assets/images/top/visual-text-sp.png" /><img class="fit" src="top/visual-text.png" srcset="top/visual-text.png" alt="北冠はまいにち美味い。" />
                            </picture>
                        </div><img class="fit" src="/assets/images/top/mv-03.jpg" alt="" width="2760" height="1720" loading="lazy" decoding="async" />
                    </a>
                </div>
                <div class="swiper-slide">
                    <a href="#">
                        <div class="img-wrapper logo_4">
                            <picture>
                                <source media="(min-width: 769px)" srcset="/assets/images/top/visual-text.png" />
                                <source media="(max-width: 768px)" srcset="/assets/images/top/visual-text-sp.png" /><img class="fit" src="top/visual-text.png" srcset="top/visual-text.png" alt="北冠はまいにち美味い。" />
                            </picture>
                        </div>
                        <img class="fit" src="/assets/images/top/mv-04.jpg" alt="" width="2760" height="1720" loading="lazy" decoding="async" />
                    </a>
                </div>
                <div class="swiper-slide">
                    <a href="#">
                        <div class="img-wrapper logo_5">
                            <picture>
                                <source media="(min-width: 769px)" srcset="/assets/images/top/visual-text.png" />
                                <source media="(max-width: 768px)" srcset="/assets/images/top/visual-text-sp.png" /><img class="fit" src="top/visual-text.png" srcset="top/visual-text.png" alt="北冠はまいにち美味い。" />
                            </picture>
                        </div>
                        <img class="fit" src="/assets/images/top/mv-05.jpg" alt="" width="2760" height="1720" loading="lazy" decoding="async" />
                    </a>
                </div>
            </div>
        </div>
        <div class="swiper-control">
            <div class="b-visual__slider__pagination"></div>
        </div>
        <div class="b-visual__scroll-down show_pc effect slideUp"><a href="#corporate"><span>Scroll</span></a></div>
    </div>
    <section class="c-sec b-corporate row" id="corporate">
        <div class="c-sec__inner">
            <h2 class="c-ttl__vertical effect slideUp"><span class="c-ttl__vertical__txt--large">企業情報</span><a class="c-ttl__vertical__txt--small" href="/company/"> <span>詳しく見る</span></a></h2>
            <div class="c-sec__ctn b-corporate__ctn">
                <figure class="b-corporate__photo effect slideUp"><img class="fit" src="/assets/images/top/img-01.jpg" alt="" width="1336" height="1070" loading="lazy" decoding="async" />
                </figure>
                <ul class="b-corporate__list effect slideUp">
                    <li><a href="/company/#greeting"><span>ごあいさつ</span></a></li>
                    <li><a href="/company/#kitaseki"><span>北関を知る</span></a></li>
                    <li><a href="/company/#profile"><span>会社概要</span></a></li>
                    <li><a href="/company/#voice"><span>杜氏の声</span></a></li>
                </ul>
            </div>
        </div>
    </section>
    <section class="c-sec b-ins row" id="instagram">
        <div class="c-sec__inner">
            <h2 class="c-ttl__vertical effect slideUp"><span class="c-ttl__vertical__txt--large">北関酒造のいま</span><a class="c-ttl__vertical__txt--small" href="https://www.instagram.com/hokkan_sake/" target="_blank"> <i class="ico-ins"></i><span class="text">インスタグラムで見る</span></a></h2>
            <div class="c-sec__ctn b-ins__ctn">
                <div class="swiper-container b-ins__slider effect slideUp">
                    <div class="swiper-wrapper js-instagram__list"></div>
                </div>
            </div>
            <div class="swiper-control effect slideUp">
                <div class="b-ins__slider__prev"></div>
                <div class="b-ins__slider__next"></div>
            </div>
        </div>
    </section>
    <section class="b-banner">
        <ul class="b-banner__list">
            <li class="effect slideUp"><a class="link__zoom" href="/item/">
                    <figure class="b-banner__photo"><img class="fit" src="/assets/images/top/img-02.jpg" alt="" width="749" height="459" loading="lazy" decoding="async" />
                    </figure>
                    <div class="b-banner__detail effect slideUp" data-delay="0.5">
                        <h2 class="c-ttl__type01">商品紹介</h2>
                        <div class="b-banner__more">more</div>
                    </div>
                </a></li>
            <li class="effect slideUp"><a class="link__zoom" href="/recruit/">
                    <figure class="b-banner__photo"><img class="fit" src="/assets/images/top/img-03.jpg" alt="" width="1410" height="919" loading="lazy" decoding="async" />
                    </figure>
                    <div class="b-banner__detail effect slideUp" data-delay="0.5">
                        <h2 class="c-ttl__type01">採用情報</h2>
                        <div class="b-banner__more">more</div>
                    </div>
                </a></li>
        </ul>
    </section>
    <section class="b-news row">
        <div class="b-news__inner effect slideUp">
            <h2 class="c-ttl__type02 effect slideUp">新着情報</h2>

            <ul class="b-news__list effect slideUp">
                <?php foreach ($info_model as $data) : ?>
                    <li><a href="<?= '/info/' . $data->id ?>"><span class="date"><?= (new DateTime($data->start_date))->format('Y.m.d'); ?></span><span class="text"><?= h($data->title) ?></span></a></li>
                <?php endforeach; ?>
            </ul>
            <?php if (!empty($data)) {  ?><div class="b-news__viewmore effect slideUp"><a class="c-viewmore" href="/info/"><span>一覧を見る</span></a></div><?php } ?>
        </div>
    </section>
    <section class="b-map row">
        <div class="b-map__inner">
            <h2 class="c-ttl__type02 effect slideUp">アクセス</h2>
            <p class="c-catch effect slideUp">〒328-0004 栃木県栃木市田村町480</p>
            <div class="b-map__ctn effect slideUp">
                <iframe src="https://www.google.com/maps/embed?pb=!1m14!1m8!1m3!1d102783.76607476987!2d139.78401!3d36.385171!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x1845346e955df815!2z5YyX6Zai6YWS6YCg!5e0!3m2!1sja!2sjp!4v1654582997641!5m2!1sja!2sjp" width="796" height="428" style="border:0;" allowfullscreen loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
            </div>
        </div>
    </section>
</main>