<!DOCTYPE html>
<html lang="ja">

<head>
    <?= $this->element('ga'); ?>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="format-detection" content="telephone=no">
    <title><?= $__title__ ?></title>
    <meta name="Description" content="栃木県栃木市の日本酒の酒蔵「北関酒造」のWEBサイト。毎日飲んでも飽きないお酒造りにこだわっています。">
    <meta property="og:type" content="website">
    <meta property="og:description" content="栃木県栃木市の日本酒の酒蔵「北関酒造」のWEBサイト。毎日飲んでも飽きないお酒造りにこだわっています。">
    <meta property="og:title" content="<?= $__title__ ?>">
    <meta property="og:url" content="https://www.hokkansyuzou.co.jp/">
    <meta property="og:image" content="https://www.hokkansyuzou.co.jp/og.png">
    <meta property="og:locale" content="ja_JP">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:description" content="栃木県栃木市の日本酒の酒蔵「北関酒造」のWEBサイト。毎日飲んでも飽きないお酒造りにこだわっています。">

    <?= $this->fetch('meta') ?>

    <link rel="shortcut icon" href="/favicon.ico">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Noto+Serif+JP:wght@400;500;600;700&amp;display=swap">
    <link rel="stylesheet" href="/assets/css/common.css">

    <?= $this->fetch('css') ?>
    <script>
        var bodyWidth = (document.body && document.body.clientWidth) || 0;
        document.documentElement.style.setProperty('--vw', (bodyWidth / 100) + 'px');
        document.documentElement.style.setProperty('--vh', (window.innerHeight / 100) + 'px');
    </script>
    <?= $this->fetch('js') ?>

    <?= $this->fetch('beforeCloseHead') ?>
</head>

<body>
    <?= $this->fetch('afterOpenBody') ?>
    <div class="root" id="root">
        <header class="header" id="header">
            <h1 class="header__logo"><a href="/"><img class="fit" src="/assets/images/common/logo.png" alt="LOGO" width="162" height="68" loading="lazy" decoding="async" /></a></h1>
            <div class="header__menu">
                <div class="header__main">
                    <nav class="header__nav" id="navPage">
                        <ul>
                            <li class="show_pc header__nav__item"><a href="/">トップ</a></li>
                            <li class="header__nav__item"><a href="/info/">新着情報</a></li>
                            <li class="header__nav__item"><a href="/item/">商品紹介</a></li>
                            <li class="header__nav__item"><a href="/company/">企業情報</a></li>
                            <li class="header__nav__item"><a href="/recruit/">採用情報</a></li>
                            <li class="show_sp o-long header__nav__item"><a href="/privacy/">プライバシーポリシー</a></li>
                            <li class="show_sp c-item__ins header__nav__item"><a href="https://www.instagram.com/hokkan2020/" target="_blank"><i class="ico-ins"></i><span>Instagram</span></a></li>
                        </ul>
                    </nav>
                    <div class="header__button">
                        <div class="header__button__item header__nav__item"><a class="c-btn" href="/contact/"><i class="ico-mail"></i><span>お問い合わせ</span></a></div>
                        <div class="header__button__item header__nav__item header__button--cart"><a class="c-btn" href="/shop/"><i class="ico-cart"></i><span>オンラインショップ</span></a></div>
                    </div>
                </div>
            </div>
            <div class="header__iconNav show_sp" id="iconNav">
                <div class="icon_menu"></div>
            </div>
        </header>

        <?= $this->fetch('content') ?>

        <footer class="footer" id="footer">
            <div class="pagetop" id="pagetop"><span>Page top</span></div>
            <div class="row">
                <div class="footer__inner">
                    <div class="footer__brand">
                        <a class="footer__brand__link" href="/">
                            <figure class="footer__brand__logo"><img class="fit" src="/assets/images/common/logo-footer.png" alt="logo" width="187" height="77" loading="lazy" decoding="async" /></figure>
                            <p class="footer__brand__text">北関酒造株式会社</p>
                        </a>
                    </div>
                    <div class="footer__info">
                        <ul class="footer__info__list">
                            <li><a href="/info/">新着情報</a></li>
                            <li><a href="/item/">商品紹介</a></li>
                            <li><a href="/company/">企業情報</a></li>
                            <li><a href="/recruit/">採用情報</a></li>
                            <li class="footer__info__sub footer__info__sub--privacy"><a href="/privacy/">プライバシーポリシー</a></li>
                            <li class="footer__info__sub footer__info__sub--ins"><a href="https://www.instagram.com/hokkan_sake/" target="_blank"><i class="ico-ins"></i><span>Instagram</span></a></li>
                        </ul>
                    </div>
                    <div class="footer__button"><a class="c-btn__type1" href="/contact/"><i class="ico-mail"></i><span>お問い合わせ</span></a><a class="c-btn__type1" href="/shop/"><i class="ico-cart"></i><span>オンラインショップ</span></a></div>
                </div>
                <div class="footer__warning">
                    <p>お酒は二十歳になってから。飲酒運転は絶対にやめましょう。<br class="show_sp">妊娠中や授乳期の飲酒は、胎児・乳児の発育に悪影響を与えるおそれがあります。</p>
                </div>
                <div class="footer__copy">
                    <p>&copy; HOKKAN SAKE BREWING CO., LTD. 2022</p>
                </div>
            </div>
        </footer>
    </div>
    <script src="/assets/js/vendor.js" defer></script>
    <script src="/assets/js/runtime.js" defer></script>
    <script src="/assets/js/bundle.js" defer></script>
    <?= $this->fetch('beforeCloseBody') ?>
</body>

</html>