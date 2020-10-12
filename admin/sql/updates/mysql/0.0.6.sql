DROP TABLE IF EXISTS `#__kungfusql`;

-- https://docs.joomla.org/J3.x:Developing_an_MVC_Component/Using_the_database

CREATE TABLE `#__kungfusql` (
	`id`       INT(11)       NOT NULL AUTO_INCREMENT,
        `filename` VARCHAR(300)  NOT NULL,
        `published` tinyint(4) NOT NULL DEFAULT '1',
	PRIMARY KEY (`id`)

)
	ENGINE =MyISAM
	AUTO_INCREMENT =0
	DEFAULT CHARSET =utf8;

INSERT INTO `#__kungfusql` (`filename`) VALUES
('poll_answer_all_with_link.sql'),
('poll_answer_all_without_link.sql'),
('poll_answer_self_with_link.sql'),
('poll_answer_self_without_link.sql'),
('poll_delete_self.sql'),
('dummy1.sql'),
('dummy2.sql'),
('dummy3.sql'),
('dummy4.sql'),
('dummy5.sql'),
('dummy6.sql')
