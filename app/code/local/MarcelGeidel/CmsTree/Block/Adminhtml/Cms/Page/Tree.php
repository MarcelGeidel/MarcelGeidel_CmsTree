<?php

class MarcelGeidel_CmsTree_Block_Adminhtml_Cms_Page_Tree extends Mage_Adminhtml_Block_Template
{
	public function getCurrentStoreID()
	{
		return Mage::app()->getRequest()->getParam('store_id', Mage::helper('marcelgeidel_cmstree')->getDefaultStoreView());
	}
	
	public function getCurrentPageID()
	{
		return Mage::app()->getRequest()->getParam('page_id', -1);
	}
	
	public function getCurrentTree()
	{
		return Mage::getModel('marcelgeidel_cmstree/tree')->load($this->getCurrentStoreID());
	}
	
	public function getTreeHtml($tree = null, $parentID = 0)
	{
		if (!$tree)
		{
			$tree = $this->getCurrentTree();
		}
		
		$nodes = $tree->getNodes($parentID);
		
		if ($nodes)
		{
			$html = '<ul>';
			
			foreach ($nodes as $node)
			{
				$class = $node['page_id'] == $this->getCurrentPageID() ? 'selected' : '';
				
				$html .= '<li data-id="' . $node['page_id'] . '" class="' . $class . '">';
				
				$link = Mage::helper('adminhtml')->getUrl('adminhtml/cms_page/edit', array('store_id' => $this->getCurrentStoreID(), 'page_id' => $node['page_id']));
				
				$html .= '<a href="' . $link . '" target="_top">' . $node['title'] . '</a>';
				
				$html .= $this->getTreeHtml($tree, $node['page_id']);
				
				$html .= '</li>';
			}
			
			$html .= '</ul>';
		}
		
		return $html;
	}
}
