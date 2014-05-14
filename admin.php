<?php

include_once 'models/admin.php';

$admin = new Admin();
$errors = '';
$success = '';
//var_dump($admin);

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
else if (isset($_POST['newImageDir'])) {
	$newImage = Image::add();
	if (true !== $newImage) {
		$errors = $newImage;
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
						<li><a href="#">Выход</a></li>
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
														<?php foreach ($dir[Album::IMAGES_DIR] as $image) : ?>
															<li class="image-block">
																<?=Image::render(null, Router::DIR_NAME.'/'.$tour.'/'.$album.'/'.Album::IMAGES_DIR.'/'.$image, $image, true, true)?>
																<div class="buttons"><a class="btn btn-danger delete" href="#" rel="<?=$image?>">Удал.</a></div>
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
					<div class="control-group desc-block hide">
						<label class="control-label" for="albumDesc">Описание</label>
						<div class="controls">
							<textarea class="span3" name="albumDesc" id="albumDesc"></textarea>
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
		
		<div class="modal hide fade" id="imageAdd">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h3>Добавить изображение</h3>
			</div>
			<div class="modal-body">
				<form enctype="multipart/form-data" class="form-horizontal" action="admin.php" method="POST">
					<div class="control-group">
						<label class="control-label" for="image">Изображение</label>
						<div class="controls">
							<input type="file" name="image" id="image">
							<p class="text-error"></p>
						</div>
					</div>
					<input type="hidden" id="newImageDir" name="newImageDir" value="">
				</form>
			</div>
			<div class="modal-footer">
				<a href="#" class="btn btn-primary">Сохранить</a>
			</div>
		</div>
	</body>
</html>