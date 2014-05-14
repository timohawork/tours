<?php

class Image
{
	const TYPE = 'image/jpeg';
	
	public $url;
	public $title;
	
	public function __construct($url, $title)
	{
		$this->url = $url;
		$this->title = $title;
	}
	
	public static function render($router, $url, $title, $withTitle = true, $isPreview = false)
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
		if (empty($_POST['newImageDir']) || empty($_FILES)) {
			return false;
		}
		$path = Router::DIR_NAME.'/'.$_POST['newImageDir'];
		if (!is_dir($path)) {
			return false;
		}
		if (self::TYPE !== $_FILES['image']['type']) {
			return 'Неверный тип файла. Можно загружать только jpg-файлы.';
		}
		if (!move_uploaded_file($_FILES['image']['tmp_name'], dirname(__FILE__).'/../'.$path.'/'.Album::IMAGES_DIR.'/'.basename($_FILES['image']['name']))) {
			return 'Ошибка сохранения изображения!';
		}
		return true;
	}
}

?>
