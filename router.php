<?php

class Router
{
	const TYPE_TOUR = 'tour';
	const TYPE_ALBUM = 'album';
	
	const DIR_NAME = 'tours';
	
	public $type;
	public $tour;
	public $album;
	
	public function __construct() {
		$query = explode("&", $_SERVER['QUERY_STRING']);
		if (empty($query[0])) {
			return;
		}
		$tour = explode("=", $query[0]);
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
		}
		else {
			$this->type = self::TYPE_TOUR;
		}
	}
	
	public function getBreadCrumbs()
	{
		$html = null !== $this->type ? '<div id="bread-crumbs"><a href="/">Главная</a>' : '';
		if (self::TYPE_TOUR === $this->type) {
			$html .= '&nbsp;>&nbsp;<span>'.$this->tour.'</span>';
		}
		else if (self::TYPE_ALBUM === $this->type) {
			$html .= '&nbsp;>&nbsp;<a href="index.php?tour='.$this->tour.'">'.$this->tour.'</a>&nbsp;>&nbsp;<span>'.$this->album.'</span>';
		}
		$html .= !empty($html) ? '</div>' : '';
		return $html;
	}
}

?>
