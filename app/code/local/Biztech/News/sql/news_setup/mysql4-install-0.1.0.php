<?php

    $installer = $this;

    $installer->startSetup();

    $installer->run("

        -- DROP TABLE IF EXISTS {$this->getTable('news')};
        CREATE TABLE {$this->getTable('news')} (
        `news_id` int(11) unsigned NOT NULL auto_increment,
        `title` varchar(255) NOT NULL default '',
        `news_content` text NOT NULL default '',
        `status` smallint(6) NOT NULL default '0',
        `created_time` datetime NULL,
        `update_time` datetime NULL,
        `intro` text NOT NULL default '',
        `date_to_publish` datetime NULL,
        `date_to_unpublish` datetime NULL,
        `browser_title` varchar(255) NOT NULL default '',
        `seo_keywords` varchar(255) NOT NULL default '',
        `seo_description` varchar(255) NOT NULL default '',
        PRIMARY KEY (`news_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;

        ");

    $installer->endSetup(); 