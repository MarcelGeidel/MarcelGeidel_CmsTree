<?php


/** @var Mage_Catalog_Model_Resource_Eav_Mysql4_Setup $installer */
$installer = $this;
$installer->startSetup();

$sql = <<<SQLTEXT

CREATE TABLE IF NOT EXISTS `marcelgeidel_cmstree` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `page_id` smallint(6) NOT NULL,
  `parent_id` smallint(6) NOT NULL DEFAULT '0',
  `store_id` smallint(6) NOT NULL,
  `title` varchar(255) NOT NULL,
  `position` smallint(6) NOT NULL,
  `include_in_menu` smallint(6) NOT NULL DEFAULT '0',
  `css_class` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

ALTER TABLE  `cms_page` ADD  `include_in_menu` SMALLINT( 6 ) NOT NULL DEFAULT  '0';
ALTER TABLE  `cms_page` ADD  `css_class` VARCHAR( 255 ) NULL ;

SQLTEXT;

$installer->run($sql);

Mage::app()->reinitStores();

foreach (Mage::helper('marcelgeidel_cmstree')->getStores() as $store) {
    Mage::getModel('marcelgeidel_cmstree/tree')->init($store->getId())->save();
}

$installer->endSetup();
