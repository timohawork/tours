<?php

class Album
{
	const COVER_NAME = 'cover.jpg';
	const IMAGES_DIR = 'images';
	const DESC_FILE = 'desc.txt';
	
	const TYPE_COVERS = 1;
	const TYPE_IMAGES = 2;
	
	public $dir;
	public $type;
	public $desc;
	
	public function __construct($dir)
	{
		$dir = Router::DIR_NAME.(empty($dir) ? '' : '/'.urldecode($dir));
		if (!is_dir($dir)) {
			return;
		}
		$this->dir = $dir;
		$this->type = false === strpos($this->dir, self::IMAGES_DIR) ? self::TYPE_COVERS : self::TYPE_IMAGES;
		
		if (self::TYPE_IMAGES == $this->type) {
			$descUrl = str_replace(self::IMAGES_DIR, self::DESC_FILE, $this->dir);
			$this->desc = file_get_contents($descUrl);
		}
	}
	
	public function getList()
	{
		if (empty($this->dir) || empty($this->type)) {
			return false;
		}
		$list = array();
		foreach (scandir($this->dir) as $file) {
			if ("." === $file || ".." === $file) {
				continue;
			}
			if (self::TYPE_COVERS == $this->type && is_dir($this->dir.'/'.$file)) {
				$list[] = array(
					'name' => $file,
					'url' => $this->dir.'/'.$file.'/'.self::COVER_NAME
				);
			}
			else if (self::TYPE_IMAGES == $this->type) {
				$list[] = array(
					'name' => $file,
					'url' => $this->dir.'/'.$file
				);
			}
		}
		return $list;
	}
}

?>
