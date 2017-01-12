<?php

$installer = $this;
$installer->startSetup();
$installer->run("

-- DROP TABLE IF EXISTS {$installer->getTable('customerprice/customer_price')};
CREATE TABLE {$installer->getTable('customerprice/customer_price')} (
  `value_id` int(11) NOT NULL auto_increment,
  `entity_id` int(10) unsigned NOT NULL default '0',
  `all_groups` tinyint (1)unsigned NOT NULL DEFAULT '1',
  `customer_group_id` smallint(5) unsigned NOT NULL default '0',
  `value` decimal(12,4) NOT NULL default '0.0000',
  `website_id` smallint(5) unsigned NOT NULL,
  PRIMARY KEY  (`value_id`),
  KEY `FK_CATALOG_PRODUCT_ENTITY_CUSTOMER_PRICE_PRODUCT_ENTITY` (`entity_id`),
  KEY `FK_CATALOG_PRODUCT_ENTITY_CUSTOMER_PRICE_GROUP` (`customer_group_id`),
  KEY `FK_CATALOG_PRODUCT_CUSTOMER_WEBSITE` (`website_id`),
  CONSTRAINT `FK_CATALOG_PRODUCT_ENTITY_CUSTOMER_PRICE_PRODUCT_ENTITY` FOREIGN KEY (`entity_id`) REFERENCES {$installer->getTable('catalog_product_entity')} (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_CATALOG_PRODUCT_ENTITY_CUSTOMER_PRICE_GROUP` FOREIGN KEY (`customer_group_id`) REFERENCES {$installer->getTable('customer_group')} (`customer_group_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_CATALOG_PRODUCT_CUSTOMER_WEBSITE` FOREIGN KEY (`website_id`) REFERENCES `{$installer->getTable('core_website')}` (`website_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");


$installer->endSetup();
$installer->installEntities();
