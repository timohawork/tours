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
		return str_replace(".jpg", self::BIG_NAME_PART.".jpg", strtolower($title));
	}

	public static function getDescFile($imageUrl)
	{
		$path = explode("/", $imageUrl);
		$descFile = str_replace(".jpg", "_desc.txt", strtolower(array_pop($path)));
		$path[] = $descFile;
		return implode("/", $path);
	}
	
	public function render($options = array())
	{
		$html = '<img class="'.(!empty($options['class']) ? $options['class'] : '').'" src="'.$this->url.'" alt="'.(!empty($options['title']) ? $options['title'] : $this->title).'"><div class="desc hidden" class="hidden">'.$this->desc.'</div>';
		return !empty($options['withViewLink']) ? '<a href="'.str_replace($this->title, $this->bigTitle, $this->url).'" rel="prettyPhoto" title="'.(!empty($options['title']) ? $options['title'] : '').'">'.$html.'</a>' : $html;
	}
	
	public static function renderBlock($router, $url, $title, $withTitle = true, $isPreview = false)
	{
		if (empty($url)) {
			return false;
		}
		if (empty($router)) {
			$href = '';
		}
		else {
			$href = 'index.php';
			switch ($router->type) {
				case Router::TYPE_TOUR:
					$href .= '?tour='.$router->tour.'&album='.$title;
				break;

				case Router::TYPE_ALBUM:
					$href .= '';
				break;

				case null:
					$href .= '?tour='.$title;
				break;
			}
		}
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
		if (empty($_POST['imageDir']) || empty($_FILES)) {
			return false;
		}
		$path = Router::DIR_NAME.'/'.$_POST['imageDir'];
		if (!is_dir($path)) {
			return false;
		}
		foreach ($_FILES['image']['type'] as $i => $imageType) {
			if (self::TYPE !== $imageType) {
				return 'Неверный тип файла "'.$_FILES['image']['name'][$i].'". Можно загружать только jpg-файлы.';
			}
		}
		foreach ($_FILES['image']['name'] as $i => $imageName) {
			$imageTmpName = $_FILES['image']['tmp_name'][$i];
			$image = new Imagick($imageTmpName);
			$bigImage = clone $image;
			$image->cropthumbnailimage(self::IMG_WIDTH, self::IMG_HEIGHT);
			$bigImage->cropthumbnailimage(self::BIG_WIDTH, self::BIG_HEIGHT);
			$fileName = strtolower(basename($imageName));
			if (!self::save($imageTmpName, $path.'/'.Album::IMAGES_DIR.'/'.$fileName) || !self::save($imageTmpName, $path.'/'.Album::IMAGES_DIR.'/'.self::getBitTitle($fileName), true)) {
				return 'Ошибка сохранения изображения "'.$imageName.'"!';
			}
		}
		return true;
	}
	
	protected static function save($file, $path, $isBig = false)
	{
		$image = new Imagick($file);
		$image->cropthumbnailimage(!$isBig ? self::IMG_WIDTH : self::BIG_WIDTH, !$isBig ? self::IMG_HEIGHT : self::BIG_HEIGHT);
		return true === $image->writeimage($path);
	}

	public static function edit()
	{
		if (empty($_POST['imageDir']) || !isset($_POST['imageDesc']) || !is_file(Router::DIR_NAME.'/'.$_POST['imageDir'])) {
			return false;
		}
		if (false === file_put_contents(self::getDescFile(Router::DIR_NAME.'/'.$_POST['imageDir']), $_POST['imageDesc'])) {
			return 'Ошибка сохранения описания изображения!';
		}
		return true;
	}


	public static function delete($path)
	{
		if (empty($path) || !is_file(Router::DIR_NAME.'/'.$path)) {
			return false;
		}
		$descFilePath = self::getDescFile(Router::DIR_NAME.'/'.$path);
		if (is_file($descFilePath) && !unlink($descFilePath)) {
			return false;
		}
		return unlink(Router::DIR_NAME.'/'.$path) && unlink(Router::DIR_NAME.'/'.self::getBitTitle($path));
	}
}

?>
