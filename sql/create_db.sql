use search_engine;

 CREATE TABLE  IF NOT EXISTS users (
  `id` int(11) NOT NULL,
  `username` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
 
CREATE TABLE IF NOT EXISTS news (
    id INTEGER PRIMARY KEY AUTO_INCREMENT,--设置为自增属性
    url TEXT,
    title TEXT,
    datetime TEXT,
    body TEXT
);

CREATE TABLE IF NOT EXISTS favorite_websites (
    user_id int(11) ,
    text_id INTEGER,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

CREATE TABLE IF NOT EXISTS text (
    text_id INTEGER PRIMARY KEY,
    star_num INT DEFAULT 0,
    FOREIGN KEY (text_id) REFERENCES news(id)
);



