<?php

class Router
{
	const TYPE_TOUR = 'tour';
	const TYPE_ALBUM = 'album';
	const TYPE_STATIC = 'page';
	
	const DIR_NAME = 'tours';
	
	public $type;
	public $tour;
	public $album;
	public $title;
	
	public function __construct($title) {
		$query = explode("&", $_SERVER['QUERY_STRING']);
		if (empty($query[0])) {
			$this->title = $title;
			return;
		}
		$tour = explode("=", $query[0]);
		if (self::TYPE_STATIC === $tour[0]) {
			$this->type = self::TYPE_STATIC;
			$this->title = urldecode($tour[1]).' - '.$title;
			return;
		}
		if (self::TYPE_TOUR !== $tour[0] || empty($tour[1])) {
			return;
		}
		$this->tour = urldecode($tour[1]);
		if (!empty($query[1])) {
			$album = explode("=", $query[1]);
			if (self::TYPE_ALBUM !== $album[0] || empty($album[1])) {
				return;
			}
			$this->type = self::TYPE_ALBUM;
			$this->album = urldecode($album[1]);
			$this->title = $this->album.' - '.$title;
		}
		else {
			$this->type = self::TYPE_TOUR;
			$this->title = $this->tour.' - '.$title;
		}
	}
	
	public function getBreadCrumbs()
	{
		$links = $backUrl = '';
		if (self::TYPE_TOUR === $this->type) {
			$links = '<i class="fa fa-angle-right"></i><span>'.$this->tour.'</span>';
			$backUrl = '/';
		}
		else if (self::TYPE_ALBUM === $this->type) {
			$links = '<i class="fa fa-angle-right"></i><a href="index.php?tour='.$this->tour.'">'.$this->tour.'</a><i class="fa fa-angle-right"></i><span>'.$this->album.'</span>';
			$backUrl = 'index.php?tour='.$this->tour;
		}
		$html = null !== $this->type ? '<div id="bread-crumbs"'.(self::TYPE_ALBUM === $this->type ? ' class="inalbum"' : '').'><a href="'.$backUrl.'" title="Назад"><i class="fa fa-arrow-left fa-lg"></i></a><a href="/" title="На главную"><i class="fa fa-home fa-lg"></i></a>'.$links.'</div>' : '';
		return $html;
	}
}

?>
