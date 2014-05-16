<?php

class Album
{
	const COVER_TYPE = 'image/jpeg';
	const COVER_NAME = 'cover.jpg';
	const IMAGES_DIR = 'images';
	
	const TYPE_COVERS = 1;
	const TYPE_IMAGES = 2;
	
	const COVER_WIDTH = 200;
	const COVER_HEIGHT = 130;
	
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
				$file = strtolower($file);
				if (false === strpos($file, '.jpg') || strpos($file, Image::BIG_NAME_PART.".jpg")) {
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
			Admin::setMessage(Admin::TYPE_ERROR, 'Ошибка сохранения данных!');
			return false;
		}
		if (1 > strlen($_POST['albumTitle'])) {
			Admin::setMessage(Admin::TYPE_ERROR, 'Неверно введено название!');
			return false;
		}
		if (isset($_POST['newTour']) && !is_dir(Router::DIR_NAME.'/'.$_POST['newTour'])) {
			Admin::setMessage(Admin::TYPE_ERROR, 'Альбома с названием "'.$_POST['newTour'].'" не существует!');
			return false;
		}
		$path = Router::DIR_NAME.'/'.(isset($_POST['newTour']) ? $_POST['newTour'].'/' : '').$_POST['albumTitle'];
		if (is_dir($path)) {
			Admin::setMessage(Admin::TYPE_ERROR, 'Альбом с названием "'.$_POST['albumTitle'].'" уже существует!');
			return false;
		}
		if (self::COVER_TYPE !== $_FILES['albumCover']['type']) {
			Admin::setMessage(Admin::TYPE_ERROR, 'Неверный тип файла. Можно загружать только jpg-файлы.');
			return false;
		}
		if (!mkdir($path) || !chmod($path, 0755)) {
			Admin::setMessage(Admin::TYPE_ERROR, 'Ошибка создания директории!');
			return false;
		}
		if (isset($_POST['newTour']) && (!mkdir($path.'/'.Album::IMAGES_DIR) || !chmod($path.'/'.Album::IMAGES_DIR, 0755))) {
			Admin::setMessage(Admin::TYPE_ERROR, 'Ошибка создания директории!');
			return false;
		}
		$cover = new Imagick($_FILES['albumCover']['tmp_name']);
		$cover->cropthumbnailimage(self::COVER_WIDTH, self::COVER_HEIGHT);
		if (true !== $cover->writeimage($path.'/'.self::COVER_NAME)) {
			Admin::setMessage(Admin::TYPE_ERROR, 'Ошибка сохранения изображения!');
			return false;
		}
		Admin::setMessage(Admin::TYPE_SUCCESS, 'Данные успешно сохранены!');
	}
	
	public static function edit()
	{
		$path = Router::DIR_NAME.'/'.(isset($_POST['tourTitle']) ? $_POST['tourTitle'].'/' : '').$_POST['albumOrigTitle'];
		if (!isset($_POST['albumTitle']) || !isset($_POST['albumOrigTitle']) || !is_dir($path)) {
			Admin::setMessage(Admin::TYPE_ERROR, 'Ошибка сохранения данных!');
			return false;
		}
		if (1 > strlen($_POST['albumTitle'])) {
			Admin::setMessage(Admin::TYPE_ERROR, 'Неверно введено название!');
			return false;
		}
		if (!empty($_FILES['albumCover']['name'])) {
			if (self::COVER_TYPE !== $_FILES['albumCover']['type']) {
				Admin::setMessage(Admin::TYPE_ERROR, 'Неверный тип файла. Можно загружать только jpg-файлы.');
				return false;
			}
			$cover = new Imagick($_FILES['albumCover']['tmp_name']);
			$cover->cropthumbnailimage(self::COVER_WIDTH, self::COVER_HEIGHT);
			if (true !== $cover->writeimage($path.'/'.self::COVER_NAME)) {
				Admin::setMessage(Admin::TYPE_ERROR, 'Ошибка сохранения изображения!');
				return false;
			}
		}
		if (!rename(dirname(__FILE__).'/../'.$path, dirname(__FILE__).'/../'.Router::DIR_NAME.'/'.(isset($_POST['tourTitle']) ? $_POST['tourTitle'].'/' : '').$_POST['albumTitle'])) {
			Admin::setMessage(Admin::TYPE_ERROR, 'Ошибка переименования альбома!');
			return false;
		}
		Admin::setMessage(Admin::TYPE_SUCCESS, 'Данные успешно сохранены!');
		return true;
	}
	
	public static function delete($dir)
	{
		if (empty($dir) || !is_dir(Router::DIR_NAME.'/'.$dir) || !self::removeDir(Router::DIR_NAME.'/'.$dir)) {
			Admin::setMessage(Admin::TYPE_ERROR, 'Ошибка удаления данных!');
			return false;
		}
		Admin::setMessage(Admin::TYPE_SUCCESS, 'Данные успешно удалены!', false);
		return true;
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
