<?php

set_time_limit(0);
session_start();

include_once 'config.php';
include_once 'router.php';
include_once 'models/admin.php';
include_once 'models/static.php';
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
						<form class="form-inline" action="" method="POST">
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

if (isset($_GET['page']) && isset($_GET['do']) && 'static' === $_GET['page'] && 'delete' === $_GET['do'] && isset($_POST['name'])) {
	echo (int)StaticPages::delete($_POST['name']);
	die();
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
						<li><a href="/" target="_blank">На сайт</a></li>
						<li><a href="/admin.php">Главная</a></li>
						<li><a href="/admin.php?page=static">Страницы</a></li>
						<li><a href="/admin.php?logout=1">Выход</a></li>
					</ul>
				</div>
				<div id="container" class="span10">
<?php 

if (!isset($_GET['page'])) {
	if (isset($_POST['albumTitle'])) {
		$_POST['isEdit'] ? Album::edit() : Album::add();
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
		!empty($_FILES['image']) ? Image::add() : Image::edit();
	}
?>
					<?=Admin::renderMessage()?>
					
					<h2><a class="btn edit newTour" href="#"><i class="fa fa-plus fa-lg"></i></a> Экскурсии:</h2>
					<div class="albums-block">
						<?php foreach ($admin->getTours() as $album1 => $albums1) : ?>
							<?php $album1Html = Admin::getBlockHtml($album1, 'tours/'.$album1.'/cover.jpg', $album1, empty($albums1)); ?>
							<?=$album1Html['header']?>
							<?php if (!empty($albums1)) : ?>
								<div class="albums-block">
									<?php foreach ($albums1 as $album2 => $images) : ?>
										<?php $album2Html = Admin::getBlockHtml($album2, 'tours/'.$album1.'/'.$album2.'/cover.jpg', $album1.'/'.$album2, false, false); ?>
										<?=$album2Html['header']?>
										<div class="images-block">
											<?php foreach ($images[Album::IMAGES_DIR] as $imageName) : ?>
												<div class="image-block">
													<?php $image = new Image(Router::DIR_NAME.'/'.$album1.'/'.$album2.'/'.Album::IMAGES_DIR.'/'.$imageName, $imageName); ?>
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
											<div class="image-block newImage" title="Добавить изображение" rel="<?=$album1.'/'.$album2?>">
												<div class="new-image-block">
													<i class="fa fa-plus fa-4x"></i>
												</div>
											</div>
										</div>
										<?=$album2Html['footer']?>
									<?php endforeach; ?>
								</div>
							<?php endif; ?>
							<?=$album1Html['footer']?>
						<?php endforeach; ?>
					</div>
		
					<div class="modal hide fade" id="albumEdit">
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
							<h3></h3>
						</div>
						<div class="modal-body">
							<form enctype="multipart/form-data" class="form-horizontal" action="" method="POST">
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
								<input type="hidden" id="albumPath" name="albumPath" value="">
									<input type="hidden" id="isEdit" name="isEdit" value="0">
							</form>
						</div>
						<div class="modal-footer">
							<a href="#" class="btn btn-primary">Сохранить</a>
						</div>
					</div>
					
					<div class="modal hide fade" id="imageAdd">
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
							<h3>Загрузка изображений</h3>
						</div>
						<div class="modal-body">
							<form enctype="multipart/form-data" class="form-horizontal" action="" method="POST">
								<div class="control-group file-block">
									<label class="control-label" for="image">Выберите изображение</label>
									<div class="controls">
										<input type="file" name="image[]" multiple="true" id="image">
										<p class="text-error"></p>
									</div>
								</div>
								<input type="hidden" class="imageDir" name="imageDir" value="">
							</form>
						</div>
						<div class="modal-footer">
							<a href="#" class="btn btn-primary">Сохранить</a>
						</div>
					</div>

					<div class="modal hide fade" id="imageEdit">
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
							<h3>Редактирование изображения</h3>
						</div>
						<div class="modal-body">
							<form enctype="multipart/form-data" class="form-horizontal" action="" method="POST">
								<div class="control-group">
									<label class="control-label" for="imageDesc">Описание</label>
									<div class="controls">
										<textarea class="span3" name="imageDesc" id="imageDesc"></textarea>
									</div>
								</div>
								<input type="hidden" class="imageDir" name="imageDir" value="">
							</form>
						</div>
						<div class="modal-footer">
							<a href="#" class="btn btn-primary">Сохранить</a>
						</div>
					</div>
<?php
}
else if ('static' === $_GET['page']) {
	
	$pages = StaticPages::getPages();
	
	if (!isset($_GET['do'])) {
?>
					<?=Admin::renderMessage()?>

					<h2><a class="btn" href="admin.php?page=static&do=edit" title="Создать"><i class="fa fa-plus fa-lg"></i></a> Страницы:</h2>
					<?php if (!empty($pages)) : ?>
						<ul id="pages-block">
							<?php foreach ($pages as $page) : ?>
								<li class="page-block">
									<h3><a class="title" href="admin.php?page=static&do=edit&name=<?=$page?>"><?=$page?></a> <a href="index.php?page=<?=$page?>" target="_blank"><i class="fa fa-arrow-right"></i></a> <i class="fa fa-times-circle-o fa-lg deletePage" title="Удалить"></i></h3>
								</li>
							<?php endforeach; ?>
						</ul>
					<?php endif; ?>
<?php 
	}
	else if ('edit' === $_GET['do']) {
		
		if (!empty($_POST)) {
			StaticPages::edit($_POST);
		}
?>
					<?=Admin::renderMessage()?>
					
					<h2><?=!isset($_GET['name']) ? 'Создание' : 'Редактирование'?> страницы</h2>
					<form id="edit-page" class="form-horizontal" action="" method="POST">
						<div class="control-group">
							<label class="control-label" for="pageName">Название</label>
							<div class="controls">
								<input type="text" name="pageName" id="pageName" value="<?=isset($_POST['pageName']) ? $_POST['pageName'] : (isset($_GET['name']) ? $_GET['name'] : '')?>">
								<p class="text-error"></p>
							</div>
						</div>
						<textarea name="html" id="html"><?=isset($_POST['html']) ? $_POST['html'] : (isset($_GET['name']) ? StaticPages::getHtml($_GET['name']) : '')?></textarea>
						<input type="hidden" name="origName" value="<?=!empty($success) ? $_POST['pageName'] : (isset($_GET['name']) ? $_GET['name'] : '')?>">
						<input type="submit" class="btn btn-primary" value="Сохранить">
					</form>
<?php 
	}
}
?>
				</div>
			</div>
		</div>
	</body>
</html>