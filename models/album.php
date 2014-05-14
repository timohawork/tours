<?php

class Album
{
	const COVER_TYPE = 'image/jpeg';
	const COVER_NAME = 'cover.jpg';
	const IMAGES_DIR = 'images';
	const DESC_FILE = 'desc.txt';
	
	const TYPE_COVERS = 1;
	const TYPE_IMAGES = 2;
	
	public $dir;
	public $type;
	public $desc;
	
	public function __construct($dir)
	{
		$dir = Router::DIR_NAME.(empty($dir) ? '' : '/'.urldecode($dir));
		if (!is_dir($dir)) {
			return;
		}
		$this->dir = $dir;
		$this->type = false === strpos($this->dir, self::IMAGES_DIR) ? self::TYPE_COVERS : self::TYPE_IMAGES;
		
		if (self::TYPE_IMAGES == $this->type) {
			$descUrl = str_replace(self::IMAGES_DIR, self::DESC_FILE, $this->dir);
			$this->desc = file_get_contents($descUrl);
		}
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
		if (!mkdir($path) || !chmod($path, 0777)) {
			return 'Ошибка создания директории!';
		}
		if (!move_uploaded_file($_FILES['albumCover']['tmp_name'], dirname(__FILE__).'/../'.$path.'/'.self::COVER_NAME)) {
			return 'Ошибка сохранения изображения!';
		}
		return true;
	}
	
	public static function edit()
	{
		$path = Router::DIR_NAME.'/'.$_POST['albumOrigTitle'];
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
		if (!rename(dirname(__FILE__).'/../'.$path, dirname(__FILE__).'/../'.Router::DIR_NAME.'/'.$_POST['albumTitle'])) {
			return 'Ошибка переименования альбома!';
		}
		return true;
	}
}

?>
