<!DOCTYPE html>
<html lang="ja">

<head>
	<?php echo $this->Html->charset(); ?>
	<meta name="viewport" content="<?php include WWW_ROOT . "admin/common/include/viewport.inc" ?>">
	<title>北関酒造管理画面</title>

	<link rel="stylesheet" href="/admin/common/css/normalize.css">
	<link rel="stylesheet" href="/admin/common/css/font.css">
	<link rel="stylesheet" href="/admin/common/css/common.css">
	<link rel="stylesheet" href="/admin/common/css/jquery.mCustomScrollbar.min.css">
	<link rel="stylesheet" href="/admin/common/css/jquery-ui-1.9.2.custom/css/smoothness/jquery-ui-1.9.2.custom.min.css">
	<link rel="stylesheet" href="/admin/common/css/colorbox.css">
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css" integrity="sha384-B0vP5xmATw1+K9KRQjQERJvTumQW0nPEzvF6L/Z6nronJ3oUOFUFpCjEUQouq2+l" crossorigin="anonymous">
	<script src="https://code.jquery.com/jquery-3.5.1.min.js" integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script>
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-Piv4xVNRyMGpqkS2by6br4gNJ7DXjqk09RmUpJ8jgGtD7zP9yug3goQfGII0yAns" crossorigin="anonymous"></script>
	<script src="/admin/common/js/jquery.js"></script>
	<script src="/admin/common/js/jquery-ui-1.9.2.custom.min.js"></script>
	<script src="/admin/common/js/base.js"></script>
	<script src="/admin/common/js/jquery.mCustomScrollbar.concat.min.js"></script>
	<script src="/admin/common/js/jquery.colorbox-min.js"></script>
	<script src="/admin/common/js/colorbox.js"></script>
	<script src="https://kit.fontawesome.com/7a9d7e5bcd.js" crossorigin="anonymous"></script>
	<?php echo $this->fetch('beforeHeaderClose'); ?>
</head>

<body>
	<?php echo $this->fetch('afterBodyStart'); ?>

	<div id="container">
		<?php echo $this->element('header'); ?>
		<?php echo $this->element('side'); ?>

		<?php echo $this->fetch('beforeContentStart'); ?>

		<div id="content">

			<?php echo $this->fetch('content'); ?>

			<?php include WWW_ROOT . "admin/common/include/footer.inc" ?>
		</div>
		<?php echo $this->fetch('afterContentClose'); ?>
	</div>

	<?php echo $this->fetch('beforeBodyClose'); ?>
</body>
<script type="text/javascript">
	$(function() {
		$(".scrollbar").mCustomScrollbar({
			scrollInertia: 0,
			mouseWheelPixels: 50
		});
		var re = document.getElementById('clock');
		var item = function() {
			var items = new Date();
			h = ('0' + items.getHours()).slice(-2);
			m = ('0' + items.getMinutes()).slice(-2);
			s = ('0' + items.getSeconds()).slice(-2);
			re.innerHTML = h + ':' + m;
			setTimeout(item, 100);
		}
		setTimeout(item, 100);
	});
</script>

</html>