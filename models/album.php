<?php

class Album
{
	const COVER_TYPE = 'image/jpeg';
	const COVER_NAME = 'cover.jpg';
	const IMAGES_DIR = 'images';
	
	const TYPE_COVERS = 1;
	const TYPE_IMAGES = 2;
	
	public $dir;
	public $type;
	
	public function __construct($dir)
	{
		$dir = Router::DIR_NAME.(empty($dir) ? '' : '/'.urldecode($dir));
		if (!is_dir($dir)) {
			return;
		}
		$this->dir = $dir;
		$this->type = false === strpos($this->dir, self::IMAGES_DIR) ? self::TYPE_COVERS : self::TYPE_IMAGES;
	}
	
	public function getList()
	{
		if (empty($this->dir) || empty($this->type)) {
			return false;
		}
		$list = array();
		foreach (scandir($this->dir) as $file) {
			if ("." === $file || ".." === $file) {
				continue;
			}
			if (self::TYPE_COVERS == $this->type && is_dir($this->dir.'/'.$file)) {
				$list[] = array(
					'name' => $file,
					'url' => $this->dir.'/'.$file.'/'.self::COVER_NAME
				);
			}
			else if (self::TYPE_IMAGES == $this->type) {
				if (false === strpos($file, '.jpg')) {
					continue;
				}
				$list[] = array(
					'name' => $file,
					'url' => $this->dir.'/'.$file
				);
			}
		}
		return $list;
	}
	
	public static function add()
	{
		if (!isset($_POST['albumTitle']) || empty($_FILES) || (isset($_POST['newTour']) && empty($_POST['newTour']))) {
			return false;
		}
		if (6 > strlen($_POST['albumTitle'])) {
			return 'Неверно введено название!';
		}
		if (isset($_POST['newTour']) && !is_dir(Router::DIR_NAME.'/'.$_POST['newTour'])) {
			return 'Альбома с названием "'.$_POST['newTour'].'" не существует!';
		}
		$path = Router::DIR_NAME.'/'.(isset($_POST['newTour']) ? $_POST['newTour'].'/' : '').$_POST['albumTitle'];
		if (is_dir($path)) {
			return 'Альбом с названием "'.$_POST['albumTitle'].'" уже существует!';
		}
		if (self::COVER_TYPE !== $_FILES['albumCover']['type']) {
			return 'Неверный тип файла. Можно загружать только jpg-файлы.';
		}
		if (!mkdir($path) || !chmod($path, 0755) || !mkdir($path.'/'.Album::IMAGES_DIR) || !chmod($path.'/'.Album::IMAGES_DIR, 0755)) {
			return 'Ошибка создания директории!';
		}
		if (!move_uploaded_file($_FILES['albumCover']['tmp_name'], dirname(__FILE__).'/../'.$path.'/'.self::COVER_NAME)) {
			return 'Ошибка сохранения изображения!';
		}
		return true;
	}
	
	public static function edit()
	{
		$path = Router::DIR_NAME.'/'.(isset($_POST['tourTitle']) ? $_POST['tourTitle'].'/' : '').$_POST['albumOrigTitle'];
		if (!isset($_POST['albumTitle']) || !isset($_POST['albumOrigTitle']) || !is_dir($path)) {
			return false;
		}
		if (6 > strlen($_POST['albumTitle'])) {
			return 'Неверно введено название!';
		}
		if (!empty($_FILES['albumCover']['name'])) {
			if (self::COVER_TYPE !== $_FILES['albumCover']['type']) {
				return 'Неверный тип файла. Можно загружать только jpg-файлы.';
			}
			if (!move_uploaded_file($_FILES['albumCover']['tmp_name'], dirname(__FILE__).'/../'.$path.'/'.self::COVER_NAME)) {
				return 'Ошибка сохранения изображения!';
			}
		}
		if (!rename(dirname(__FILE__).'/../'.$path, dirname(__FILE__).'/../'.Router::DIR_NAME.'/'.(isset($_POST['tourTitle']) ? $_POST['tourTitle'].'/' : '').$_POST['albumTitle'])) {
			return 'Ошибка переименования альбома!';
		}
		return true;
	}
	
	public static function delete($dir)
	{
		if (empty($dir) || !is_dir(Router::DIR_NAME.'/'.$dir)) {
			return false;
		}
		return self::removeDir(Router::DIR_NAME.'/'.$dir);
	}
	
	public static function removeDir($dir) {
		if ($objs = glob($dir."/*")) {
			foreach($objs as $obj) {
				is_dir($obj) ? self::removeDir($obj) : unlink($obj);
			}
		}
		return rmdir($dir);
	}
}

?>
