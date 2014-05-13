<?php

class Router
{
	const TYPE_TOUR = 'tour';
	const TYPE_ALBUM = 'album';
	
	const DIR_NAME = 'tours';
	
	public $pageType;
	public $pageName;
	
	public function __construct() {
		$query = explode("&", $_SERVER['QUERY_STRING']);
		if (empty($query[0])) {
			return;
		}
		$tour = explode("=", $query[0]);
		if (self::TYPE_TOUR !== $tour[0] || empty($tour[1]) || !is_dir(self::DIR_NAME.'/'.urldecode($tour[1]))) {
			return;
		}
		if (!empty($query[1])) {
			$album = explode("=", $query[1]);
			if (self::TYPE_ALBUM !== $album[0] || empty($album[1])) {
				return;
			}
			$this->pageType = self::TYPE_ALBUM;
			$this->pageName = urldecode($album[1]);
		}
		else {
			$this->pageType = self::TYPE_TOUR;
			$this->pageName = urldecode($tour[1]);
		}
	}
}

?>
