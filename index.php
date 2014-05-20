<?php 

include_once 'config.php';
include_once 'models/static.php';
include_once 'router.php';

$router = new Router($config['title']);

if (!isset($_GET['page'])) {

	include_once 'models/image.php';
	include_once 'models/album.php';

	$title = '';
	$albumUrl = '';

	if (null !== $router->type) {
		$title = $router->album;
		$albumUrl = $router->url;
	}
	else {
		$title = 'Наши экскурсии';
	}

	$album = new Album($albumUrl);
	$list = $album->getList();
}
else {
	!StaticPages::isPage($_GET['page']) && die();
	$title = $_GET['page'];
}

$pages = StaticPages::getPages();

?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ru" lang="ru">
	<head>
		<meta charset="utf-8" />
		<meta http-equiv="Content-Language" content="ru" />
		<link href="//netdna.bootstrapcdn.com/font-awesome/4.1.0/css/font-awesome.min.css" rel="stylesheet">
		<link rel="stylesheet" type="text/css" href="/css/main.css" />
		<link rel="stylesheet" type="text/css" href="/css/jcarousel.basic.css" />
		<link rel="stylesheet" type="text/css" href="/css/prettyPhoto.css" />
		<script type="text/javascript" src="http://code.jquery.com/jquery-1.8.1.min.js"></script>
		<script type="text/javascript" src="/js/imageload.js"></script>
		<script type="text/javascript" src="/js/jquery.jcarousel.js"></script>
		<script type="text/javascript" src="/js/jquery.prettyPhoto.js"></script>
		<script type="text/javascript" src="/js/index.js"></script>
		<title><?=$router->title?></title>
	</head>
	<body>
		<div id="wrapper">
			<div id="header">
				<a href="/"><img src="/img/logo.png"></a>
			</div>
			<div id="container">
				<h2 id="pageTitle">
					<?php if (!empty($pages)) : ?>
						<?php foreach($pages as $page) : ?>
							<a href="/page/<?=$page?>"><?=$page?></a>
						<?php endforeach; ?>
					<?php endif; ?>
					<br /><br />
					<?=$title?>
				</h2>
				<?php if (!isset($_GET['page'])) : ?>
					<div class="centered">
						<?=$router->getBreadCrumbs()?>
						<?php if (Album::TYPE_COVERS == $album->type) : ?>
							<div id="albums-block">
								<?php 
								if (!empty($list)) {
									foreach ($list as $block) {
										Image::renderBlock($router, '/'.$block['url'], $block['name'], Album::TYPE_COVERS == $album->type);
									}
								}
								?>
							</div>
						<?php else : ?>
							<?php if (!empty($list)) : ?>
								<div class="jcarousel-wrapper">
									<div class="jcarousel">
										<ul>
											<?php 
												$activeDesc = '';
												$isFirst = true;
												foreach ($list as $block) {
													$image = new Image($block['url'], $block['name']);
													echo '<li>'.$image->render(array(
														'withViewLink' => true,
														'title' => $image->desc
													)).'</li>';
													if ($isFirst) {
														$activeDesc = $image->desc;
														$isFirst = false;
													}
												}
											?>
										</ul>
									</div>
									<a href="#" class="jcarousel-control-prev">&lsaquo;</a>
									<a href="#" class="jcarousel-control-next">&rsaquo;</a>
									<p class="jcarousel-pagination"></p>
								</div>
								<div id="description"><?=$album->desc?></div>
							<?php endif; ?>
						<?php endif; ?>
					</div>
				<?php else : ?>
					<div class="page-container">
						<?=StaticPages::getHtml($_GET['page'])?>
					</div>
				<?php endif; ?>
			</div>
			<div id="footer">&copy; <?=$config['title']?>, 2014</div>
		</div>
	</body>
</html>