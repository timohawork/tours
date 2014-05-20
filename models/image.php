<?php

class Image
{
	const TYPE = 'image/jpeg';
	const BIG_NAME_PART = '_big';
	
	const IMG_WIDTH = 600;
	const IMG_HEIGHT = 450;
	
	const BIG_WIDTH = 800;
	const BIG_HEIGHT = 600;
	
	public $url;
	public $title;
	public $bigTitle;
	public $desc;
	
	public function __construct($url, $title)
	{
		$this->url = $url;
		$this->title = $title;
		$this->bigTitle = self::getBitTitle($this->title);
		$path = self::getDescFile($this->url);
		$this->desc = is_file($path) ? file_get_contents($path) : '';
	}
	
	protected static function getBitTitle($title)
	{
		return str_replace(".jpg", self::BIG_NAME_PART.".jpg", $title);
	}

	public static function getDescFile($imageUrl)
	{
		$path = explode("/", $imageUrl);
		$descFile = str_replace(".jpg", "_desc.txt", mb_convert_case(array_pop($path), MB_CASE_LOWER, "UTF-8"));
		$path[] = $descFile;
		return implode("/", $path);
	}
	
	public function render($options = array())
	{
		$html = '<img class="'.(!empty($options['class']) ? $options['class'] : '').'" src="'.$this->url.'" alt="'.(!empty($options['title']) ? $options['title'] : $this->title).'">'.(!empty($this->desc) ? '<div class="desc">'.$this->desc.'</div>' : '');
		return !empty($options['withViewLink']) ? '<a href="'.str_replace($this->title, $this->bigTitle, $this->url).'" rel="prettyPhoto" title="'.(!empty($options['title']) ? $options['title'] : '').'">'.$html.'</a>' : $html;
	}
	
	public static function renderBlock($router, $url, $title, $withTitle = true, $isPreview = false)
	{
		if (empty($url)) {
			return false;
		}
		$href = empty($router) ? '' : 'index.php'.(Router::TYPE_GALLERY !== $router->type ? '?album='.$router->url.'/'.$title : '');
		$html = '<div class="album-block">';
		if (!empty($href)) {
			$html .= '<a href="'.$href.'">';
		}
		$html .= '<img'.($isPreview ? ' class="img-rounded preview"' : '').' src="'.$url.'" alt="'.(!empty($title) ? $title : '').'">';
		if (!empty($title) && $withTitle) {
			$html .= '<span class="title">'.$title.'</span>';
		}
		$html .= (!empty($href) ? '</a>' : '').'</div>';
		echo $html;
	}
	
	public static function add()
	{
		if (empty($_POST['imageDir']) || empty($_FILES['image'])) {
			Admin::setMessage(Admin::TYPE_ERROR, 'Ошибка сохранения данных!');
			return false;
		}
		$path = Router::DIR_NAME.'/'.$_POST['imageDir'].'/'.Album::IMAGES_DIR;
		if (!is_dir($path)) {
			Admin::setMessage(Admin::TYPE_ERROR, 'Неверный альбом!');
			return false;
		}
		foreach ($_FILES['image']['type'] as $i => $imageType) {
			if (self::TYPE !== $imageType) {
				Admin::setMessage(Admin::TYPE_ERROR, 'Неверный тип файла "'.$_FILES['image']['name'][$i].'". Можно загружать только jpg-файлы.');
				return false;
			}
		}
		foreach ($_FILES['image']['name'] as $i => $imageName) {
			$imageTmpName = $_FILES['image']['tmp_name'][$i];
			$image = new Imagick($imageTmpName);
			$bigImage = clone $image;
			$image->cropthumbnailimage(self::IMG_WIDTH, self::IMG_HEIGHT);
			$bigImage->cropthumbnailimage(self::BIG_WIDTH, self::BIG_HEIGHT);
			$fileName = mb_convert_case($imageName, MB_CASE_LOWER, "UTF-8");
			if (!self::save($imageTmpName, $path.'/'.$fileName) || !self::save($imageTmpName, $path.'/'.self::getBitTitle($fileName), true)) {
				Admin::setMessage(Admin::TYPE_ERROR, 'Ошибка сохранения изображения "'.$imageName.'"!');
				return false;
			}
		}
		Admin::setMessage(Admin::TYPE_SUCCESS, 'Данные успешно сохранены!', false);
	}
	
	protected static function save($file, $path, $isBig = false)
	{
		$image = new Imagick($file);
		$image->cropthumbnailimage(!$isBig ? self::IMG_WIDTH : self::BIG_WIDTH, !$isBig ? self::IMG_HEIGHT : self::BIG_HEIGHT);
		return true === $image->writeimage($path);
	}

	public static function edit()
	{
		if (empty($_POST['imageDir']) || !isset($_POST['imageDesc']) || !is_file($_POST['imageDir'])) {
			Admin::setMessage(Admin::TYPE_ERROR, 'Ошибка сохранения данных!');
			return false;
		}
		if (false === file_put_contents(self::getDescFile($_POST['imageDir']), $_POST['imageDesc'])) {
			Admin::setMessage(Admin::TYPE_ERROR, 'Ошибка сохранения описания изображения!');
			return false;
		}
		Admin::setMessage(Admin::TYPE_SUCCESS, 'Данные успешно сохранены!', false);
	}


	public static function delete($path)
	{
		if (empty($path) || !is_file($path)) {
			Admin::setMessage(Admin::TYPE_ERROR, 'Ошибка удаления данных!');
			return false;
		}
		$descFilePath = self::getDescFile($path);
		if (is_file($descFilePath) && !unlink($descFilePath)) {
			Admin::setMessage(Admin::TYPE_ERROR, 'Ошибка удаления данных!');
			return false;
		}
		if (!unlink($path) || !unlink(self::getBitTitle($path))) {
			Admin::setMessage(Admin::TYPE_ERROR, 'Ошибка удаления данных!');
			return false;
		}
		Admin::setMessage(Admin::TYPE_SUCCESS, 'Данные успешно удалены!', false);
		return true;
	}
}

?>
