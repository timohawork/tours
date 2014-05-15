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
			<title>Администраторская панель - <?=$config['title']?></title>
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
		<link href="//netdna.bootstrapcdn.com/font-awesome/4.1.0/css/font-awesome.min.css" rel="stylesheet">
		<link rel="stylesheet" type="text/css" href="/css/bootstrap.css" />
		<link rel="stylesheet" type="text/css" href="/css/bootstrap-responsive.css" />
		<link rel="stylesheet" type="text/css" href="/css/admin.css" />
		<script type="text/javascript" src="http://code.jquery.com/jquery-1.8.1.min.js"></script>
		<script type="text/javascript" src="/js/bootstrap.min.js"></script>
		<script type="text/javascript" src="/tinymce/tinymce.min.js"></script>
		<script type="text/javascript" src="/js/admin.js"></script>
		<title>Администраторская панель - <?=$config['title']?></title>
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
					
					<h2><a class="btn edit newTour" href="#"><i class="fa fa-plus fa-lg"></i></a> Экскурсии:</h2>
					<div id="tours-block">
						<?php foreach ($admin->getTours() as $tour => $albums) : ?>
							<div class="tour-block folding-block">
								<i class="fa <?=!empty($albums) ? 'fa-caret-right folding-caret fa-2x' : 'empty'?>"></i>&nbsp;
								<h4>
									<a href="#" class="edit editTour"><img class="img-rounded" src="tours/<?=$tour?>/cover.jpg" alt="<?=$tour?>" title="Редактировать"></a>
									<span class="title"><?=$tour?></span>
									<a class="btn edit newAlbum" href="#"><i class="fa fa-plus fa-lg"></i></a>
									<i class="fa fa-times-circle-o fa-lg delete" title="Удалить"></i>
								</h4>
								<div class="folding-toggle hide">
									<?php if (!empty($albums)) : ?>
										<div class="albums-block">
											<?php foreach ($albums as $album => $dir) : ?>
												<div class="album-block folding-block">
													<i class="fa <?=!empty($dir[Album::IMAGES_DIR]) ? 'fa-caret-right folding-caret fa-2x' : 'empty'?>"></i>&nbsp;
													<h4>
														<a href="#" class="edit editAlbum"><img class="img-rounded" src="tours/<?=$tour?>/<?=$album?>/cover.jpg" alt="<?=$album?>" title="Редактировать"></a>
														<span class="title"><?=$album?></span>
														<a class="btn newImage" href="#"><i class="fa fa-plus fa-lg"></i></a>
														<i class="fa fa-times-circle-o fa-lg delete" title="Удалить"></i>
													</h4>
													<div class="folding-toggle hide">
														<?php if (!empty($dir[Album::IMAGES_DIR])) : ?>
															<div class="images-block">
																<?php foreach ($dir[Album::IMAGES_DIR] as $imageName) : ?>
																	<div class="image-block">
																		<?php $image = new Image(Router::DIR_NAME.'/'.$tour.'/'.$album.'/'.Album::IMAGES_DIR.'/'.$imageName, $imageName); ?>
																		<a href="#" class="editImage">
																			<?=$image->render(array('class' => 'img-rounded'))?>
																			<i class="fa fa-times-circle-o fa-lg delete" title="Удалить" rel="<?=$imageName?>"></i>
																		</a>
																		<div class="imageDesc">
																			<?php
																				if (!empty($image->desc)) {
																					$imageDesc = strip_tags($image->desc);
																					echo 100 < strlen($imageDesc) ? substr($imageDesc, 0, 100).'...' : $imageDesc;
																				}
																				else {
																					echo 'Нет описания';
																				}
																			?>
																		</div>
																	</div>
																<?php endforeach; ?>
															</div>
														<?php endif; ?>
													</div>
												</div>
											<?php endforeach; ?>
										</div>
									<?php endif; ?>
								</div>
							</div>
						<?php endforeach; ?>
					</div>
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
				<h3></h3>
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