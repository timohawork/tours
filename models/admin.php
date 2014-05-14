<?php

include_once 'router.php';
include_once 'models/image.php';
include_once 'models/album.php';

class Admin
{
	public $tours;
	
	public function __construct()
	{
		
	}
	
	public function getTours()
	{
		return $this->tours = $this->getDir(Router::DIR_NAME);
	}
	
	protected function getDir($dir)
	{
		$files = array();
		foreach (scandir($dir) as $file) {
			if ("." === $file || ".." === $file || Album::COVER_NAME === $file || Album::DESC_FILE === $file) {
				continue;
			}
			if (is_dir($dir.'/'.$file)) {
				$files[$file] = $this->getDir($dir.'/'.$file);
			}
			else {
				$files[] = $file;
			}
		}
		return $files;
	}
}

?>
