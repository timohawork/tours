<?php

include_once 'router.php';
include_once 'models/image.php';
include_once 'models/album.php';

class Admin
{
	public $tours;
	protected $password = 'admin';
	
	public function __construct()
	{
		
	}
	
	public function login()
	{
		if (isset($_POST['password'])) {
			if ($this->password === $_POST['password']) {
				$_SESSION['login'] = true;
				return true;
			}
			return false;
		}
	}
	
	public function logout()
	{
		if (isset($_SESSION['login'])) {
			unset($_SESSION['login']);
			return true;
		}
		return false;
	}
	
	public function getTours()
	{
		return $this->tours = $this->getDir(Router::DIR_NAME);
	}
	
	protected function getDir($dir)
	{
		$files = array();
		foreach (scandir($dir) as $file) {
			if ("." === $file || ".." === $file || Album::COVER_NAME === $file) {
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
