<?php

    $installer = $this;

    $installer->startSetup();

    $installer->run("

        -- DROP TABLE IF EXISTS {$this->getTable('news_data')};
        CREATE TABLE {$this->getTable('news_data')} (
        `text_id` int(11) unsigned NOT NULL auto_increment,
        `news_id` int(11) unsigned  NOT NULL ,
        `store_id` int(11) NOT NULL default '0',
        `title` varchar(255) NOT NULL default '',
        `news_content` text NOT NULL default '',
        `status` smallint(6) NOT NULL default '0',
        `intro` text NOT NULL default '',
        `date_to_publish` datetime NULL,
        `date_to_unpublish` datetime NULL,
        `browser_title` varchar(255) NOT NULL default '',
        `seo_keywords` varchar(255) NOT NULL default '',    
        `seo_description` varchar(255) NOT NULL default '',

        PRIMARY KEY (`text_id`)
        ) ENGINE=InnoDB;

        ALTER TABLE `{$this->getTable('news')}` DROP `title`, DROP `news_content`, DROP `status`, DROP `intro`, DROP `date_to_publish`, DROP `date_to_unpublish`, DROP `browser_title`, DROP `seo_keywords`, DROP `seo_description`;

        ALTER TABLE `{$this->getTable('news_data')}` ADD INDEX ( `news_id` );

        ALTER TABLE `{$this->getTable('news_data')}` ADD FOREIGN KEY ( `news_id` ) REFERENCES `{$this->getTable('news')}` (`news_id`) ON DELETE CASCADE ON UPDATE CASCADE ;");


    $installer->endSetup(); 