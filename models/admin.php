<?php

class Admin
{
	const TYPE_ERROR = 0;
	const TYPE_SUCCESS = 1;
	
	public $tours;
	protected $password = 'admin';
	
	public function __construct($config)
	{
		$this->password = $config['adminPassword'];
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
				$file = strtolower($file);
				if (false === strpos($file, '.jpg') || strpos($file, Image::BIG_NAME_PART.".jpg")) {
					continue;
				}
				$files[] = $file;
			}
		}
		return $files;
	}
	
	public static function setMessage($type, $message, $redirect = true, $redirectUrl = 'admin.php')
	{
		$_SESSION['message'] = array(
			'type' => $type,
			'message' => $message
		);
		if (self::TYPE_SUCCESS === $type && $redirect) {
			header("Location: ".$redirectUrl);
			exit;
		}
	}
	
	public static function getMessage()
	{
		if (!isset($_SESSION['message'])) {
			return false;
		}
		$message = $_SESSION['message'];
		unset($_SESSION['message']);
		return $message;
	}
	
	public static function renderMessage()
	{
		$message = self::getMessage();
		if (false === $message) {
			return false;
		}
		echo '<div class="alert alert-block alert'.(self::TYPE_SUCCESS === $message['type'] ? '-success' : '-error').' fade in">
			<button type="button" class="close" data-dismiss="alert">×</button>
			<h4 class="alert-heading">'.(self::TYPE_SUCCESS === $message['type'] ? 'Сообщение' : 'Ошибка').'</h4>
			<p>'.$message['message'].'</p>
		</div>';
	}
}

?>
