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
	
	public static function render($router, $url, $title, $withTitle = true)
	{
		if (empty($router) || empty($url)) {
			return false;
		}
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
		$html = '<div class="album-block"><a href="'.$href.'"><img src="'.$url.'" alt="'.(!empty($title) ? $title : '').'">';
		if (!empty($title) && $withTitle) {
			$html .= '<span class="title">'.$title.'</span>';
		}
		$html .= '</a></div>';
		echo $html;
	}
}

?>
