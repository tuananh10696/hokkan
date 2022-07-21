<!DOCTYPE html>
<html lang="ja">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title><?= h($page_config['page_title']); ?></title>
    <meta name="Description" content="<?= h($page_config['description']); ?>">
    <meta name="Keywords" content="<?= h($page_config['keywords']); ?>">
    <meta property="og:type" content="website">
    <meta property="og:description" content="<?= h($page_config['description']); ?>">
    <meta property="og:title" content="<?= h($page_config['page_title']); ?>">
    <meta property="og:url" content="<?= $this->Url->build('/', true) . ($page_config['slug']? h($page_config['slug']) . DS : ''); ?>">
    <meta property="og:image" content="">
    <meta property="og:locale" content="ja_JP">
    <meta name="twitter:site" content="<?= h($page_config['page_title']); ?>">
    <meta name="twitter:title" content="<?= h($page_config['page_title']); ?>">
    <meta name="twitter:description" content="<?= h($page_config['description']); ?>">
    <link rel="shortcut icon" href="/favicon.ico">
    <link rel="stylesheet" href="/cms_assets/css/common.css">
    <link rel="stylesheet" href="/cms_assets/css/news_list.css">
  </head>
  <body>
    <div class="root" id="root">
      <header class="header">
        <h1 class="header__caption">
          <a href="/">
            <img src="/image/logo.png" alt="<?= h($page_config['page_title']); ?>">
          </a>
        </h1>
      </header>
      <main id="content">
      </main>
      <footer class="footer">
        <p class="footer__copyright">copyright © CATERS all rights reserved.</p>
      </footer>
    </div>
    <script> var rootPath = '<?= $rootPath; ?>';</script>
    <script src="/cms_assets/js/vendor.js" defer></script>
    <script src="/cms_assets/js/bundle.js" defer></script>
  </body>
</html>