<?php

class Album
{
	const COVER_NAME = 'cover.jpg';
	
	public $dir;
	
	public function __construct($dir)
	{
		$dir = Router::DIR_NAME.(empty($dir) ? '' : '/'.urldecode($dir));
		if (!is_dir($dir)) {
			return;
		}
		$this->dir = $dir;
	}
	
	public function getList()
	{
		if (empty($this->dir)) {
			return false;
		}
		$list = array();
		foreach (scandir($this->dir) as $file) {
			if ("." !== $file && ".." !== $file && is_dir($this->dir.'/'.$file)) {
				$list[] = array(
					'name' => $file,
					'cover' => $this->dir.'/'.$file.'/'.self::COVER_NAME
				);
			}
		}
		return $list;
	}
}

?>
