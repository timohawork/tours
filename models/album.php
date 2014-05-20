<?php

class Album
{
	const COVER_TYPE = 'image/jpeg';
	const COVER_NAME = 'cover.jpg';
	const DESC_FILE = 'desc.txt';
	const IMAGES_DIR = 'images';
	
	const TYPE_COVERS = 1;
	const TYPE_IMAGES = 2;
	
	const COVER_WIDTH = 200;
	const COVER_HEIGHT = 130;
	
	public $dir;
	public $type;
	public $desc;
	
	public function __construct($dir)
	{
		$dir = Router::DIR_NAME.(empty($dir) ? '' : '/'.substr(urldecode($dir), 1));
		if (!is_dir($dir)) {
			return;
		}
		$this->dir = $dir;
		$dir = explode("/", $dir);
		if (3 == count($dir)) {
			$this->desc = self::getDescFile($this->dir);
			$this->type = self::TYPE_IMAGES;
		}
		else {
			$this->type = self::TYPE_COVERS;
		}
	}
	
	public static function getDescFile($path)
	{
		$path .= '/'.self::DESC_FILE;
		return is_file($path) ? file_get_contents($path) : '';
	}
	
	public function getList()
	{
		if (empty($this->dir) || empty($this->type)) {
			return false;
		}
		$dir = $this->dir.(self::TYPE_IMAGES == $this->type ? '/'.self::IMAGES_DIR : '');
		$list = array();
		foreach (scandir($dir) as $file) {
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
				$file = mb_convert_case($file, MB_CASE_LOWER, "UTF-8");
				if (false === strpos($file, '.jpg') || strpos($file, Image::BIG_NAME_PART.".jpg")) {
					continue;
				}
				$list[] = array(
					'name' => $file,
					'url' => $dir.'/'.$file
				);
			}
		}
		return $list;
	}
	
	public static function add()
	{
		if (!isset($_POST['albumTitle']) || empty($_FILES['albumCover']['name'])) {
			Admin::setMessage(Admin::TYPE_ERROR, 'Ошибка сохранения данных!');
			return false;
		}
		if (1 > strlen($_POST['albumTitle'])) {
			Admin::setMessage(Admin::TYPE_ERROR, 'Неверно введено название!');
			return false;
		}
		$path = Router::DIR_NAME.'/'.$_POST['albumPath'].'/'.$_POST['albumTitle'];
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
		if (!empty($_POST['albumPath']) && 1 == count(explode("/", $_POST['albumPath'])) && (!mkdir($path.'/'.Album::IMAGES_DIR) || !chmod($path.'/'.Album::IMAGES_DIR, 0755))) {
			Admin::setMessage(Admin::TYPE_ERROR, 'Ошибка создания директории!');
			return false;
		}
		$cover = new Imagick($_FILES['albumCover']['tmp_name']);
		$cover->cropthumbnailimage(self::COVER_WIDTH, self::COVER_HEIGHT);
		if (true !== $cover->writeimage($path.'/'.self::COVER_NAME)) {
			Admin::setMessage(Admin::TYPE_ERROR, 'Ошибка сохранения изображения!');
			return false;
		}
		Admin::setMessage(Admin::TYPE_SUCCESS, 'Данные успешно сохранены!', false);
	}
	
	public static function edit()
	{
		if (empty($_POST['albumTitle'])) {
			Admin::setMessage(Admin::TYPE_ERROR, 'Ошибка сохранения данных!');
			return false;
		}
		$origPath = Router::DIR_NAME.'/'.$_POST['albumPath'];
		$path = explode("/", $origPath);
		$path[count($path) - 1] = $_POST['albumTitle'];
		$path = implode("/", $path);
		if (!is_dir($origPath)) {
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
			if (true !== $cover->writeimage($origPath.'/'.self::COVER_NAME)) {
				Admin::setMessage(Admin::TYPE_ERROR, 'Ошибка сохранения изображения!');
				return false;
			}
		}
		if (isset($_POST['albumDesc']) && false === file_put_contents($origPath.'/'.self::DESC_FILE, $_POST['albumDesc'])) {
			Admin::setMessage(Admin::TYPE_ERROR, 'Ошибка сохранения описания!');
			return false;
		}
		if (!rename(dirname(__FILE__).'/../'.$origPath, dirname(__FILE__).'/../'.$path)) {
			Admin::setMessage(Admin::TYPE_ERROR, 'Ошибка переименования альбома!');
			return false;
		}
		Admin::setMessage(Admin::TYPE_SUCCESS, 'Данные успешно сохранены!', false);
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
