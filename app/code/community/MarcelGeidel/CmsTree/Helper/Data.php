<?php

class MarcelGeidel_CmsTree_Helper_Data extends Mage_Core_Helper_Abstract
{
	public function getDefaultStoreView()
	{
		$defaultStoreView = Mage::getStoreConfig('marcelgeidel_cmstree/general/default_storeview');
	
		if ($defaultStoreView)
		{
			return $defaultStoreView;
		}
	
		return Mage::app()->getDefaultStoreView()->getId();
	}
	
	public function getStores()
	{
		$stores = array();
		
		foreach (Mage::app()->getWebsites() as $website) 
		{
			foreach ($website->getGroups() as $group) 
			{
				foreach ($group->getStores() as $store)
				{
					$stores[] = $store;
				}
			}
		}
		
		return $stores;
	}
}
