<?php 

include_once 'router.php';
include_once 'models/image.php';
include_once 'models/album.php';

$router = new Router();
$title = '';
$albumUrl = '';

switch ($router->type) {
	case Router::TYPE_TOUR:
		$title = $router->tour;
		$albumUrl = $router->tour;
	break;

	case Router::TYPE_ALBUM:
		$title = $router->album;
		$albumUrl = $router->tour.'/'.$router->album.'/'.Album::IMAGES_DIR;
	break;

	case null:
		$title = 'Наши экскурсии';
	break;
}

$album = new Album($albumUrl);
$list = $album->getList();
//var_dump($album);

?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ru" lang="ru">
	<head>
		<meta charset="utf-8" />
		<meta http-equiv="Content-Language" content="ru" />
		<link rel="stylesheet" type="text/css" href="/css/main.css" />
		<script type="text/javascript" src="http://code.jquery.com/jquery-1.8.1.min.js"></script>
		<title>Title</title>
	</head>
	<body>
		<div id="wrapper">
			<div id="header">
				<h1><a href="/">Шапка</a></h1>
			</div>
			<div id="container">
				<h3><?=$title?></h3>
				<?=$router->getBreadCrumbs()?>
				<?php if (!empty($album->desc)) : ?>
					<div class="description"><?=$album->desc?></div>
				<?php endif; ?>
				<div id="albums-block">
					<?php 
					if (!empty($list)) {
						foreach ($list as $block) {
							Image::render($router, $block['url'], $block['name'], Album::TYPE_COVERS == $album->type);
						}
					}
					?>
				</div>
			</div>
			<div id="footer">
				Подвал
			</div>
		</div>
	</body>
</html>