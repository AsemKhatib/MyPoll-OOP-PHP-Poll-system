<?php

require __DIR__ . '/config.php';

$aquery = <<<STR
            CREATE table `questions` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `question` varchar (300) NOT NULL,
            PRIMARY KEY (`id`)
            )
            ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1;

            CREATE table `answers` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `qid` int (11) NOT NULL,
            `answer` varchar (300) NOT NULL,
            `votes` int (11) NOT NULL,
            PRIMARY KEY (`id`)
            )
            ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1;

            CREATE table `users` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `user_name` varchar (300) NOT NULL,
            `user_pass` varchar (32) NOT NULL,
            `email` varchar (300) NOT NULL,
            PRIMARY KEY (`id`)
            )
            ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1;

            INSERT INTO `users` VALUES (1, 'admin', '21232f297a57a5a743894a0e4a801fc3', 'your@email.com');
STR;

try {
    \RedBeanPHP\Facade::exec($aquery);
    echo 'Database Created';
} catch (Exception $e) {
    echo 'Error : ' . $e->getMessage();
}
