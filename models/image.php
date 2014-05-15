<?php

class Image
{
	const TYPE = 'image/jpeg';
	
	public $url;
	public $title;
	public $desc;
	
	public function __construct($url, $title)
	{
		$this->url = $url;
		$this->title = $title;
		$path = self::getDescFile($this->url);
		$this->desc = is_file($path) ? file_get_contents($path) : '';
	}
	
	public static function getDescFile($imageUrl)
	{
		$path = explode("/", $imageUrl);
		$descFile = str_replace(".jpg", "_desc.txt", array_pop($path));
		$path[] = $descFile;
		return implode("/", $path);
	}
	
	public function render($options = array())
	{
		return '<img class="'.(!empty($options['class']) ? $options['class'] : '').'" src="'.$this->url.'" alt="'.$this->title.'"><div class="desc hidden" class="hidden">'.$this->desc.'</div>';
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
		if (self::TYPE !== $_FILES['image']['type']) {
			return 'Неверный тип файла. Можно загружать только jpg-файлы.';
		}
		if (!move_uploaded_file($_FILES['image']['tmp_name'], dirname(__FILE__).'/../'.$path.'/'.Album::IMAGES_DIR.'/'.basename($_FILES['image']['name']))) {
			return 'Ошибка сохранения изображения!';
		}
		if (isset($_POST['imageDesc'])) {
			$name = str_replace(".jpg", "_desc.txt", $_FILES['image']['name']);
			if (false === file_put_contents($path.'/'.Album::IMAGES_DIR.'/'.$name, $_POST['imageDesc'])) {
				return 'Ошибка сохранения описания изображения!';
			}
		}
		return true;
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
		return unlink(Router::DIR_NAME.'/'.$path);
	}
}

?>
