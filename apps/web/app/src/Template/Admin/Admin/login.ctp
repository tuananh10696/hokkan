<!DOCTYPE HTML>
<html lang="ja">

<head>
	<meta charset="utf-8">
	<meta name="viewport" content="<?php include WWW_ROOT . 'admin/common/include/viewport.inc' ?>">
	<title>北関酒造</title>
	<link rel="stylesheet" href="./common/css/normalize.css">
	<link rel="stylesheet" href="./common/css/font.css">
	<link rel="stylesheet" href="./common/css/common.css">
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css" integrity="sha384-B0vP5xmATw1+K9KRQjQERJvTumQW0nPEzvF6L/Z6nronJ3oUOFUFpCjEUQouq2+l" crossorigin="anonymous">
	<script src="https://code.jquery.com/jquery-3.5.1.min.js" integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script>
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-Piv4xVNRyMGpqkS2by6br4gNJ7DXjqk09RmUpJ8jgGtD7zP9yug3goQfGII0yAns" crossorigin="anonymous"></script>
</head>

<body>

	<div id="container">
		<header>
			<div class="status">
			</div>
		</header>

		<div id="content">
			<div class="title_area">
				<h1>北関酒造</h1>
			</div>
			<?= $this->element('error_message'); ?>
			<div class="content_inr">
				<div class="box" style="max-width:600px;margin-left:auto;margin-right:auto;">
					<h3>管理者ログイン</h3>
					<?= $this->Form->create('Admin', array('id' => 'AdminIndexForm')); ?>
					<div class="table_area form_area">
						<table class="vertical_table">
							<tr>
								<td>ユーザーID</td>
								<td><input name="username" type="text" id="id" /></td>
							</tr>
							<tr>
								<td>パスワード</td>
								<td><input name="password" type="password" id="pw" /></td>
							</tr>
						</table>
						<div class="btn_area">
							<a href="javascript:void(0);" class="btn_confirm">ログイン</a>
							<a href="javascript:void(0);" class="btn_back">リセット</a>
						</div>
					</div>
					<?= $this->Form->end(); ?>
				</div>
			</div>
			<?php include WWW_ROOT . 'admin/common/include/footer.inc' ?>
		</div>
	</div>

	<script src="./common/js/jquery.js"></script>
	<script src="./common/js/base.js"></script>
	<script src="https://kit.fontawesome.com/7a9d7e5bcd.js" crossorigin="anonymous"></script>
	<script>
		$(function() {
			$('a.btn_confirm').on('click', function() {
				$('#AdminIndexForm').submit();
			});
			$('a.btn_back').on('click', function() {
				document.getElementById("AdminIndexForm").reset();
			});
		})
	</script>

</body>

</html>