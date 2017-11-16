--
-- Структура таблицы `{PREFIX}modxredirect`
--

CREATE TABLE IF NOT EXISTS `{PREFIX}evoredirect` (
	`id` int(11) NOT NULL AUTO_INCREMENT, 
	`old_url` varchar(255),
	`new_url` varchar(255),
	`short_uri_crc` int(20) NOT NULL,
	`code` int(3) NOT NULL,
	`save_get` int(11) NOT NULL,
	`search_get` int(11) NOT NULL,
	`active` int(11) NOT NULL,
	PRIMARY KEY (`id`))
	ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;