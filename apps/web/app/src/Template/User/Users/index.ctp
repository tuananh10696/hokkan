<?php $menu_list = $this->UserAdmin->getUserMenu('main'); ?>

<div class="title_area">
  <h1>管理メニュー</h1>
  <div class="pankuzu">
    <ul>
      <?= $this->element('pankuzu_home'); ?>
      <li><span>管理メニュー</span></li>
    </ul>
  </div>
</div>

<?= $this->element('error_message'); ?>

<div class="content_inr">

  <?php foreach ($user_menu_list as $title => $menu) : ?>
    <div class="box">

      <h3 style="margin-bottom:20px;"><?= $title; ?></h3>
      <div class="btn_area" style="text-align:left;margin-left: 20px;margin-bottom: 10px !important;">
        <a href="/user/infos/?sch_page_id=4" class="btn_send btn_search" style="width:130px;text-align:center;">新着情報</a>
        <a href="/user/items/?sch_page_id=6" class="btn_send btn_search" style="width:130px;text-align:center;">商品紹介</a>
      </div>
    </div>
  <?php endforeach; ?>

</div>