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
				$file = mb_convert_case($file, MB_CASE_LOWER, "UTF-8");
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
	
	public static function getBlockHtml($title, $img, $path, $desc = null, $withAdd = true)
	{
		return array(
			'header' => '<div class="album-block folding-block" rel="'.$path.'">
				<i class="fa fa-caret-right folding-caret fa-2x"></i>&nbsp;
				<h4>
					<img class="img-rounded" src="'.$img.'" alt="'.$title.'" title="Редактировать">
					<span class="title" rel="'.$title.'">'.$title.'</span>
					'.(null !== $desc ? '<div class="description hide">'.$desc.'</div>' : '').'
				</h4>
				<div class="album-buttons hide">
					<a href="#" class="edit album-edit" title="Редактировать"><i class="fa fa-pencil fa-lg"></i></a>
					<i class="fa fa-times-circle-o fa-lg delete" title="Удалить"></i>
					'.($withAdd ? '<a class="btn edit album-add" href="#"><i class="fa fa-plus fa-lg"></i></a>' : '').'
				</div>
				<div class="folding-toggle hide">',
			'footer' => '</div>
			</div>'
		);
	}
}

?>
