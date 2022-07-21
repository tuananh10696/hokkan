<div id="side">
  <div style="text-align: center;">
      <?= $this->Form->input("site", ['type' => 'select',
                                      'options' => $user_site_list,
                                      'style' => 'width:100%;background-color:#FFF;color:#000;',
                                      'id' => 'selectSite',
                                      'value' => $current_site_id]); ?>
  </div>

  <nav>
    <ul class="menu scrollbar">

  <?php foreach ($user_menu_site_list as $name => $sub): ?>
    <li>
      <span class="parent_link"><?= $name; ?></span>
      <ul class="submenu">
      <?php foreach($sub as $sub_name => $link): ?>
        <li><a href="<?= $link; ?>"><?= $sub_name; ?></a></li>
      <?php endforeach; ?>
    </ul>
    </li>
  <?php endforeach; ?>
    </ul>
  </nav>
</div>
