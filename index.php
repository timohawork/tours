<?php 

include_once 'router.php';
include_once 'models/image.php';
include_once 'models/album.php';

$router = new Router();
$title = '';

switch ($router->type) {
	case Router::TYPE_TOUR:
		$title = $router->tour;
		$album = new Album($router->tour);
		$album->getList();
	break;

	case Router::TYPE_ALBUM:
		$title = $router->album;
	break;

	case null:
		$title = 'Наши экскурсии';
		$album = new Album('');
		$album->getList();
	break;
}

?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ru" lang="ru">
	<head>
		<meta charset="utf-8" />
		<meta http-equiv="Content-Language" content="ru" />
		<link rel="stylesheet" type="text/css" href="/css/main.css" />
		<script type="text/javascript" src="http://code.jquery.com/jquery-1.7.2.min.js"></script>
		<title>Title</title>
	</head>
	<body>
		<div id="wrapper">
			<div id="header">
				<h1>Шапка</h1>
			</div>
			<div id="container">
				<h3><?=$title?></h3>
				<div id="albums-block">
					
				</div>
			</div>
			<div id="footer">
				Подвал
			</div>
		</div>
	</body>
</html>