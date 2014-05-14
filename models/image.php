<?php

class Image
{
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
}

?>
