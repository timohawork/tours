<?php 

$siteName = 'Title';

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

?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ru" lang="ru">
	<head>
		<meta charset="utf-8" />
		<meta http-equiv="Content-Language" content="ru" />
		<link rel="stylesheet" type="text/css" href="/css/main.css" />
		<link rel="stylesheet" type="text/css" href="/css/jcarousel.basic.css" />
		<script type="text/javascript" src="http://code.jquery.com/jquery-1.8.1.min.js"></script>
		<script type="text/javascript" src="js/jquery.jcarousel.js"></script>
		<script type="text/javascript" src="js/index.js"></script>
		<title><?=$siteName?></title>
	</head>
	<body>
		<div id="wrapper">
			<div id="header">
				<a href="/"><img src="/img/logo.png" alt="<?=$siteName?>"></a>
			</div>
			<div id="container">
				<h3><?=$title?></h3>
				<div class="centered">
					<?=$router->getBreadCrumbs()?>
					<?php if (!empty($album->desc)) : ?>
						<div class="description"><?=$album->desc?></div>
					<?php endif; ?>
					<?php if (Album::TYPE_COVERS == $album->type) : ?>
						<div id="albums-block">
							<?php 
							if (!empty($list)) {
								foreach ($list as $block) {
									Image::render($router, $block['url'], $block['name'], Album::TYPE_COVERS == $album->type);
								}
							}
							?>
						</div>
					<?php else : ?>
						<?php if (!empty($list)) : ?>
							<div class="jcarousel-wrapper">
								<div class="jcarousel">
									<ul>
										<?php foreach ($list as $block) : ?>
											<li><img src="<?=$block['url']?>" alt="<?=$block['name']?>"></li>
										<?php endforeach; ?>
									</ul>
								</div>
								<a href="#" class="jcarousel-control-prev">&lsaquo;</a>
								<a href="#" class="jcarousel-control-next">&rsaquo;</a>
								<p class="jcarousel-pagination"></p>
							</div>
						<?php endif; ?>
					<?php endif; ?>
				</div>
			</div>
			<div id="footer">&copy; <?=$siteName?>, 2014</div>
		</div>
	</body>
</html>