<?php

class MarcelGeidel_CmsTree_Model_Page extends Mage_Cms_Model_Page
{
	protected function getReader()
	{
		return Mage::getSingleton('core/resource')->getConnection('core_read');
	}
	
	public function getParents($storeID)
	{
		$query = 'SELECT parent_id FROM marcelgeidel_cmstree WHERE page_id = :page_id and store_id = :store_id';
		$binds = array('page_id' => $this->getId(), 'store_id' => $storeID);
		$parentID = $this->getReader()->fetchOne($query, $binds);

		if ($parentID)
		{
			$parentPage = Mage::getModel('marcelgeidel_cmstree/page')->load($parentID);
			
			$parents = array($parentPage);
			
			return array_merge($parents, $parentPage->getParents($storeID));
		}
		
		return array();
	}
	
	public function getRootParent($storeID)
	{
		$parents = $this->getParents($storeID);
		
		if ($parents)
		{
			return end($parents);
		}
		
		return $this;
	}
	
	public function getParent($storeID)
	{
	    $parents = $this->getParents($storeID);
	    
	    if ($parents)
	    {
	        return reset($parents);
	    }
	    
	    return $this;
	}
	
	public function getChildren($storeID)
	{
		$query = 'SELECT page_id FROM marcelgeidel_cmstree WHERE parent_id = :parent_id and store_id = :store_id';
		$binds = array('parent_id' => $this->getId(), 'store_id' => $storeID);
		$result = $this->getReader()->query($query, $binds);
	
		if ($result)
		{
		    $children = array();
		    
			while ($row = $result->fetch())
			{
				$children[] = Mage::getModel('marcelgeidel_cmstree/page')->load($row['page_id']);
			}
			
			return $children;
		}
	
		return array();
	}
}
