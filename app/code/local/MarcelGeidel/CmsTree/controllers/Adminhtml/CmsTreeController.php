<?php 

class MarcelGeidel_CmsTree_Adminhtml_CmsTreeController extends Mage_Adminhtml_Controller_Action
{	
	public function saveTreeAction()
    {
		$storeID = $this->getRequest()->getParam('store_id');
		$nodeID = $this->getRequest()->getParam('node_id');
		$targetNodeID = $this->getRequest()->getParam('target_node_id');
		$hitMode = $this->getRequest()->getParam('hit_mode');

		$tree = Mage::getModel('marcelgeidel_cmstree/tree')->load($storeID);
		$tree->move($nodeID, $targetNodeID, $hitMode);
		$result = $tree->save();
		
		$this->getResponse()->setHeader('Content-type', 'application/json');
		$this->getResponse()->setBody(Mage::helper('core')->jsonEncode(array('result' => $result)));
    }
    
    public function resetTreeAction()
    {
    	foreach (Mage::helper('marcelgeidel_cmstree')->getStores() as $store)
    	{
    		Mage::getModel('marcelgeidel_cmstree/tree')->init($store->getId())->save();
    	}
    }
}
