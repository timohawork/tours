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
}

?>
