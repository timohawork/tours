<?php

class StaticPages
{
	const STATIC_DIR = 'static';
	const HEADER_HTML = 'templates/header.html';
	const FOOTER_HTML = 'templates/footer.html';
	
	const TITLE_CODE = '%%title%%';
	
	public static function getPages()
	{
		$pages = array();
		foreach (scandir(self::STATIC_DIR) as $file) {
			if ("." === $file || ".." === $file) {
				continue;
			}
			$pages[] = $file;
		}
		return $pages;
	}
	
	public static function edit($data)
	{
		if (empty($data['pageName'])) {
			return 'Необходимо ввести название страницы!';
		}
		if (!empty($data['origName']) && !self::isPage(self::STATIC_DIR.'/'.$data['origName'])) {
			return 'Такой страницы не существует';
		}
		if (false === file_put_contents(self::STATIC_DIR.'/'.$data['pageName'], $data['html'])) {
			return 'Ошибка сохранения страницы!';
		}
		if (!empty($data['origName']) && $data['origName'] !== $data['pageName'] && !rename(self::STATIC_DIR.'/'.$data['origName'], self::STATIC_DIR.'/'.$data['pageName'])) {
			return 'Не удалось переименовать страницу';
		}
		return true;
	}
	
	public static function delete($name)
	{
		if (!is_file(self::STATIC_DIR.'/'.$name)) {
			return false;
		}
		return unlink(self::STATIC_DIR.'/'.$name);
	}
	
	public static function getHtml($name)
	{
		if (!is_file(self::STATIC_DIR.'/'.$name)) {
			return false;
		}
		return file_get_contents(self::STATIC_DIR.'/'.$name);
	}
	
	public static function isPage($name)
	{
		return is_file(self::STATIC_DIR.'/'.$name);
	}
}

?>
