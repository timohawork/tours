<?php

class Router
{
	const TYPE_ALBUM = 'album';
	const TYPE_GALLERY = 'gallery';
	const TYPE_STATIC = 'page';
	
	const DIR_NAME = 'tours';
	
	public $type;
	public $album;
	public $url;
	public $title;
	
	public function __construct($title) {
		$query = explode("&", $_SERVER['QUERY_STRING']);
		if (empty($query[0])) {
			$this->title = $title;
			return;
		}
		$param = explode("=", $query[0]);
		if (self::TYPE_STATIC === $param[0]) {
			$this->type = self::TYPE_STATIC;
			$this->title = urldecode($param[1]).' - '.$title;
			return;
		}
		if (self::TYPE_ALBUM !== $param[0] || empty($param[1])) {
			return;
		}
		$this->url = urldecode($param[1]);
		$this->type = self::TYPE_ALBUM;
		$albums = explode("/", $this->url);
		$this->album = $albums[count($albums) - 1];
		$this->title = $this->album.' - '.$title;
		if (3 == count($albums)) {
			$this->type = self::TYPE_GALLERY;

		}
	}
	
	public function getBreadCrumbs()
	{
		if (null === $this->type) {
			return '';
		}
		$links = '';
		$backUrl = 'index.php';
		if (!empty($this->url)) {
			$url = explode("/", substr($this->url, 1));
			array_pop($url);
			if (!empty($url)) {
				$linkUrl = '';
				foreach ($url as $album) {
					$linkUrl .= '/'.$album;
					$links .= '<i class="fa fa-angle-right"></i><a href="index.php?album='.$linkUrl.'">'.$album.'</a>';
				}
				$backUrl .= '?album=/'.implode("/", $url);
			}
		}
		$html = '<div id="bread-crumbs"'.(self::TYPE_GALLERY === $this->type ? ' class="inalbum"' : '').'><a href="'.$backUrl.'" title="Назад"><i class="fa fa-arrow-left fa-lg"></i></a>'.$links.'</div>';
		return $html;
	}
}

?>
