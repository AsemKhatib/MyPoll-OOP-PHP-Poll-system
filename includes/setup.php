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
            `user_pass` varchar (300) NOT NULL,
            `email` varchar (300) NOT NULL,
            PRIMARY KEY (`id`)
            )
            ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1;

<<<<<<< HEAD
            INSERT INTO `users` VALUES (1, 'admin', "\$2y\$10\$xdB/UcprN3.7g.K7F.dKFOTsPGg/vVYQYh7OcL.k0.te7x8h8rKEG", 'your@email.com');
=======
            INSERT INTO `users` VALUES (1, 'admin', "\$2\y$10\$xdB/UcprN3.7g.K7F.dKFOTsPGg/vVYQYh7OcL.k0.te7x8h8rKEG", 'your@email.com');
>>>>>>> 7462641052c111949c80b1e29007fd8201355d94

            CREATE table `settings` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `site_name` varchar (300) NOT NULL,
            `site_resultsnumber` int (11) NOT NULL,
            `site_cookies` int (11) NOT NULL,
            `site_cache` int (11) NOT NULL,
            PRIMARY KEY (`id`)
            )
            ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1;

            INSERT INTO `settings` VALUES (1, 'MyPoll System V 0.1', '10', '0', '0');
STR;

try {
    \RedBeanPHP\Facade::exec($aquery);
    echo 'Database Created';
} catch (Exception $e) {
    echo 'Error : ' . $e->getMessage();
}
