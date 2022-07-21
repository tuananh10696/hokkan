<!DOCTYPE html>
<html lang="ja">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title><?= strip_tags($entity->title); ?> | <?= strip_tags($page_config['page_title']); ?></title>
    <meta name="Description" content="<?= h($entity->meta_description); ?>">
    <meta name="Keywords" content="<?= h($entity->meta_keywords); ?>">
    <meta property="og:type" content="website">
    <meta property="og:description" content="<?= strip_tags($entity->meta_description); ?>">
    <meta property="og:title" content="<?= strip_tags($entity->title); ?> | <?= strip_tags($page_config['page_title']); ?>">
    <meta property="og:url" content="<?= $this->Url->build('/', true) . ($page_config['slug']? h($page_config['slug']) . DS : '') . h($entity->id) . '.html'; ?>">
  <?php if ($entity->image): ?>
    <meta property="og:image" content="<?= $this->Html->getFullUrl($entity->attaches['image']['0']);?>">
  <?php else: ?>
    <meta property="og:image" content="">
  <?php endif; ?>
    <meta property="og:locale" content="ja_JP">
    <meta name="twitter:site" content="<?= strip_tags($page_config['page_title']); ?>">
    <meta name="twitter:title" content="<?= strip_tags($entity->title); ?>">
    <meta name="twitter:description" content="<?= strip_tags($entity->meta_description); ?>">
    <link rel="shortcut icon" href="/favicon.ico">
    <link rel="stylesheet" href="/cms_assets/css/common.css">
    <link rel="stylesheet" href="/cms_assets/css/news_detail.css">
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