<?php

session_start();
include_once 'config.php';
include_once 'router.php';
include_once 'models/admin.php';
include_once 'models/image.php';
include_once 'models/album.php';
$admin = new Admin($config);

if (isset($_POST['password'])) {
	$isLogin = $admin->login();
}

if (!isset($_SESSION['login'])) {
	?>
	<!DOCTYPE html>
	<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ru" lang="ru">
		<head>
			<meta charset="utf-8" />
			<meta http-equiv="Content-Language" content="ru" />
			<link rel="stylesheet" type="text/css" href="/css/bootstrap.css" />
			<link rel="stylesheet" type="text/css" href="/css/bootstrap-responsive.css" />
			<title>Администраторская панель</title>
		</head>
		<body>
			<div class="container-fluid">
				<div class="row-fluid">
					<div class="span6 offset3 text-center">
						<form class="form-inline" action="admin.php" method="POST">
							<h3>Администраторская панель</h3>
							<input type="password" name="password" class="input-small" placeholder="Пароль">
							<button type="submit" class="btn">Войти</button>
							<p class="text-error"><?=isset($_POST['password']) && !$isLogin ? 'Неверный пароль!' : ''?></p>
						</form>
					</div>
				</div>
			</div>
		</body>
	</html>
	<?php
	die();
}

if (isset($_GET['logout'])) {
	$admin->logout() && header('Location: admin.php');
}

$errors = '';
$success = '';

if (isset($_POST['albumOrigTitle']) && !empty($_FILES)) {
	$editAlbum = Album::edit();
	if (true !== $editAlbum) {
		$errors = $editAlbum;
	}
	else {
		$success = 'Данные успешно сохранены!';
	}
}
else if (isset($_POST['albumTitle']) && !empty($_FILES)) {
	$newAlbum = Album::add();
	if (true !== $newAlbum) {
		$errors = $newAlbum;
	}
	else {
		$success = 'Данные успешно сохранены!';
	}
}
else if (isset($_POST['deleteAlbum'])) {
	echo (int)Album::delete($_POST['deleteAlbum']);
	die();
}
else if (isset($_POST['deleteImage'])) {
	echo (int)Image::delete($_POST['deleteImage']);
	die();
}
else if (isset($_POST['imageDir'])) {
	$imageError = !isset($_POST['isEditImage']) ? Image::add() : Image::edit();
	if (true !== $imageError) {
		$errors = $imageError;
	}
	else {
		$success = 'Данные успешно сохранены!';
	}
}

?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ru" lang="ru">
	<head>
		<meta charset="utf-8" />
		<meta http-equiv="Content-Language" content="ru" />
		<link rel="stylesheet" type="text/css" href="/css/bootstrap.css" />
		<link rel="stylesheet" type="text/css" href="/css/bootstrap-responsive.css" />
		<link rel="stylesheet" type="text/css" href="/css/admin.css" />
		<script type="text/javascript" src="http://code.jquery.com/jquery-1.8.1.min.js"></script>
		<script type="text/javascript" src="/js/bootstrap.min.js"></script>
		<script type="text/javascript" src="/js/admin.js"></script>
		<title>Администраторская панель</title>
	</head>
	<body>
		<div class="container-fluid">
			<div class="row-fluid">
				<div id="menu" class="span2">
					<h2>Меню</h2>
					<ul>
						<li><a href="/">На сайт</a></li>
						<li><a href="/admin.php">Главная</a></li>
						<li><a href="/admin.php?logout=1">Выход</a></li>
					</ul>
				</div>
				<div id="container" class="span10">
					
					<?php if (!empty($errors)) : ?>
						<div class="alert alert-block alert-error fade in">
							<button type="button" class="close" data-dismiss="alert">×</button>
							<h4 class="alert-heading">Ошибка</h4>
							<p><?=$errors?></p>
						</div>
					<?php endif; ?>
					
					<?php if (!empty($success)) : ?>
						<div class="alert alert-block alert-success fade in">
							<button type="button" class="close" data-dismiss="alert">×</button>
							<h4 class="alert-heading">Сообщение</h4>
							<p><?=$success?></p>
						</div>
					<?php endif; ?>
					
					<h2>Экскурсии <a class="btn edit newTour" href="#">Добавить</a></h2>
					<ul id="tours-block">
						<?php foreach ($admin->getTours() as $tour => $albums) : ?>
							<li class="tour-block">
								<span class="title"><?=$tour?></span>
								<div class="buttons">
									<a class="btn edit newAlbum" href="#">Добавить</a>
									<a class="btn btn-info edit editTour" href="#">Ред.</a>
									<a class="btn btn-danger delete" href="#">Удал.</a>
								</div>
								<?php if (!empty($albums)) : ?>
									<ul class="albums-block">
										<?php foreach ($albums as $album => $dir) : ?>
											<li class="album">
												<span class="title"><?=$album?></span>
												<div class="buttons">
													<a class="btn newImage" href="#">Добавить</a>
													<a class="btn btn-info edit editAlbum" href="#">Ред.</a>
													<a class="btn btn-danger delete" href="#">Удал.</a>
												</div>
												<?php if (!empty($dir[Album::IMAGES_DIR])) : ?>
													<ol class="images-block">
														<?php foreach ($dir[Album::IMAGES_DIR] as $imageName) : ?>
															<li class="image-block">
																<?php $image = new Image(Router::DIR_NAME.'/'.$tour.'/'.$album.'/'.Album::IMAGES_DIR.'/'.$imageName, $imageName); ?>
																<div class="album-block">
																	<?=$image->render(array('class' => 'img-rounded preview'))?>
																	<span class="title"><?=$imageName?></span>
																</div>
																<div class="buttons">
																	<a class="btn btn-info editImage" href="#">Ред.</a>
																	<a class="btn btn-danger delete" href="#" rel="<?=$imageName?>">Удал.</a>
																</div>
															</li>
														<?php endforeach; ?>
													</ol>
												<?php endif; ?>
											</li>
										<?php endforeach; ?>
									</ul>
								<?php endif; ?>
							</li>
						<?php endforeach; ?>
					</ul>
				</div>
			</div>
		</div>
		
		<div class="modal hide fade" id="albumEdit">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h3></h3>
			</div>
			<div class="modal-body">
				<form enctype="multipart/form-data" class="form-horizontal" action="admin.php" method="POST">
					<div class="control-group title-block">
						<label class="control-label" for="albumTitle">Название</label>
						<div class="controls">
							<input class="span3" type="text" name="albumTitle" id="albumTitle" placeholder="Введите название..">
							<p class="text-error"></p>
						</div>
					</div>
					<div class="control-group file-block">
						<label class="control-label" for="albumCover">Обложка</label>
						<div class="controls">
							<input type="file" name="albumCover" id="albumCover">
							<div id="coverImg"></div>
							<p class="text-error"></p>
						</div>
					</div>
				</form>
			</div>
			<div class="modal-footer">
				<a href="#" class="btn btn-primary">Сохранить</a>
			</div>
		</div>
		
		<div class="modal hide fade" id="imageEdit">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h3>Добавить изображение</h3>
			</div>
			<div class="modal-body">
				<form enctype="multipart/form-data" class="form-horizontal" action="admin.php" method="POST">
					<div class="control-group file-block">
						<label class="control-label" for="image">Изображение</label>
						<div class="controls">
							<input type="file" name="image" id="image">
							<p class="text-error"></p>
						</div>
					</div>
					<div class="control-group">
						<label class="control-label" for="imageDesc">Описание</label>
						<div class="controls">
							<textarea class="span3" name="imageDesc" id="imageDesc"></textarea>
						</div>
					</div>
					<input type="hidden" id="imageDir" name="imageDir" value="">
				</form>
			</div>
			<div class="modal-footer">
				<a href="#" class="btn btn-primary">Сохранить</a>
			</div>
		</div>
	</body>
</html>