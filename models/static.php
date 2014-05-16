<?php

class StaticPages
{
	const STATIC_DIR = 'static';
	
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
			Admin::setMessage(Admin::TYPE_ERROR, 'Необходимо ввести название страницы!');
			return false;
		}
		if (!empty($data['origName']) && !self::isPage($data['origName'])) {
			Admin::setMessage(Admin::TYPE_ERROR, 'Такой страницы не существует');
			return false;
		}
		if (false === file_put_contents(self::STATIC_DIR.'/'.$data['pageName'], $data['html'])) {
			Admin::setMessage(Admin::TYPE_ERROR, 'Ошибка сохранения страницы!');
			return false;
		}
		if (!empty($data['origName']) && $data['origName'] !== $data['pageName'] && !rename(self::STATIC_DIR.'/'.$data['origName'], self::STATIC_DIR.'/'.$data['pageName'])) {
			Admin::setMessage(Admin::TYPE_ERROR, 'Не удалось переименовать страницу');
			return false;
		}
		Admin::setMessage(Admin::TYPE_SUCCESS, 'Данные успешно сохранены!', true, "admin.php?page=static");
		return true;
	}
	
	public static function delete($name)
	{
		if (!is_file(self::STATIC_DIR.'/'.$name) || !unlink(self::STATIC_DIR.'/'.$name)) {
			Admin::setMessage(Admin::TYPE_ERROR, 'Ошибка удаления данных!');
			return false;
		}
		Admin::setMessage(Admin::TYPE_SUCCESS, 'Данные успешно удалены!', false);
		return true;
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
