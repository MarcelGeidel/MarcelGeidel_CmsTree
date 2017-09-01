<?php

class MarcelGeidel_CmsTree_Model_Observer
{
    public function updateMenu($observer)
	{
    	$tree = Mage::getModel('marcelgeidel_cmstree/tree')->load(Mage::app()->getStore()->getStoreId());
    	
    	$this->addMenuNodes($tree->getMenuNodes(), $observer->getMenu());
	}
	
	public function addMenuNodes($nodes, $menu)
	{
		foreach ($nodes as $node)
		{
			$menuNode = new Varien_Data_Tree_Node(array
			(
				'id'	=> $node['page_id'],
				'name'	=> $node['title'],
				'url'	=> Mage::helper('cms/page')->getPageUrl($node['page_id'])
			), 'id', $menu->getTree(), $menu);
			 
			$menu->addChild($menuNode);
			
			$this->addMenuNodes($node['children'], $menuNode);
		}
	}
	
	public function updateBreadcrumbs()
	{
		if ($breadcrumbs = Mage::getSingleton('core/layout')->getBlock('breadcrumbs'))
		{
			$breadcrumbs->clear();
	
			// Startseite
			$breadcrumbs->addCrumb('home', array
			(
				'label' => Mage::helper('catalog')->__('Home'),
				'title' => Mage::helper('catalog')->__('Go to Home Page'),
				'link'  => Mage::getBaseUrl()
			));
	
			// Eltern-Seiten
			$storeID = Mage::app()->getStore()->getStoreId();
			$pageID = Mage::getModel('cms/page')->checkIdentifier(Mage::getSingleton('cms/page')->getIdentifier(), $storeID);
			$page = Mage::getModel('marcelgeidel_cmstree/page')->load($pageID);
			$parents = array_reverse($page->getParents($storeID));
	
			foreach ($parents as $parentPage)
			{
				$breadcrumbs->addCrumb('cms_page_' . $parentPage->getId(), array
				(
					'label' => $parentPage->getTitle(),
					'title' => $parentPage->getTitle(),
					'link'  => Mage::getUrl($parentPage->getIdentifier())
				));
			}
	
			// Aktuelle Seite
			$breadcrumbs->addCrumb('cms_page', array
			(
				'label' => $page->getTitle(),
				'title' => $page->getTitle(),
				'last' => true
			));
		}
		 
		return $this;
	}
	
	public function updateCmsTree($observer)
	{
		$stores = Mage::helper('marcelgeidel_cmstree')->getStores();
		 
		foreach ($stores as $store)
		{
			$tree = Mage::getModel('marcelgeidel_cmstree/tree')->load($store->getId());
			$tree->update();
			$tree->save();
		}
	}
	
	public function addCmsPageField($observer)
	{
		$form = $observer->getForm();
	
		$fieldset = $form->addFieldset('cmstree_content_fieldset', array('legend' => 'CmsTree'));
	
		$fieldset->addField('include_in_menu', 'select', array
		(
			'id'        => 'include_in_menu',
			'name'      => 'include_in_menu',
			'label'     => Mage::helper('marcelgeidel_cmstree')->__('Show in navigation'),
			'title'     => Mage::helper('marcelgeidel_cmstree')->__('Show in navigation'),
			'class'     => 'input-select',
			'options'	=> array('1' => Mage::helper('adminhtml')->__('Yes'), '0' => Mage::helper('adminhtml')->__('No')),
			'value'     => Mage::registry('cms_page')->getData('include_in_menu'),
			'note'		=> Mage::helper('marcelgeidel_cmstree')->__('Flush Magento Cache'),
		));
		
		$fieldset->addField('css_class', 'text', array
		(
			'id'        => 'css_class',
			'name'      => 'css_class',
			'label'     => Mage::helper('marcelgeidel_cmstree')->__('CSS class'),
			'title'     => Mage::helper('marcelgeidel_cmstree')->__('CSS class'),
			'value'     => Mage::registry('cms_page')->getData('css_class'),
		));
	}
}
