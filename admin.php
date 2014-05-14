<?php

include_once 'models/admin.php';

$admin = new Admin();
//var_dump($admin);

?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ru" lang="ru">
	<head>
		<meta charset="utf-8" />
		<meta http-equiv="Content-Language" content="ru" />
		<link rel="stylesheet" type="text/css" href="/css/bootstrap.css" />
		<link rel="stylesheet" type="text/css" href="/css/bootstrap-responsive.css" />
		<link rel="stylesheet" type="text/css" href="/css/admin.css" />
		<script type="text/javascript" src="/js/bootstrap.min.js"></script>
		<script type="text/javascript" src="http://code.jquery.com/jquery-1.7.2.min.js"></script>
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
					<h2>Экскурсии <a class="btn add" href="#">Добавить</a></h2>
					<ul id="tours-block">
						<?php foreach ($admin->tours as $tour => $albums) : ?>
							<li class="tour-block">
								<span class="title"><?=$tour?></span>
								<div class="buttons">
									<a class="btn add" href="#">Добавить</a>
									<a class="btn btn-info" href="#">Ред.</a>
									<a class="btn btn-danger" href="#">Удал.</a>
								</div>
								<?php if (!empty($albums)) : ?>
									<ul class="albums-block">
										<?php foreach ($albums as $album => $dir) : ?>
											<li>
												<span class="title"><?=$album?></span>
												<div class="buttons">
													<a class="btn add" href="#">Добавить</a>
													<a class="btn btn-info" href="#">Ред.</a>
													<a class="btn btn-danger" href="#">Удал.</a>
												</div>
												<?php if (!empty($dir[Album::IMAGES_DIR])) : ?>
													<ol class="images-block">
														<?php foreach ($dir[Album::IMAGES_DIR] as $image) : ?>
															<li>
																<?=Image::render(null, Router::DIR_NAME.'/'.$tour.'/'.$album.'/'.Album::IMAGES_DIR.'/'.$image, $image, true, true)?>
																<div class="buttons"><a class="btn btn-danger" href="#">Удал.</a></div>
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
	</body>
</html>