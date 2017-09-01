<?php

class MarcelGeidel_CmsTree_Model_Page extends Mage_Cms_Model_Page
{
    public function getParents($storeID)
    {
        $query    = 'SELECT parent_id FROM marcelgeidel_cmstree WHERE page_id = :page_id and store_id = :store_id';
        $binds    = array('page_id' => $this->getId(), 'store_id' => $storeID);
        $parentID = $this->getReader()->fetchOne($query, $binds);

        if ($parentID) {
            $parentPage = Mage::getModel('marcelgeidel_cmstree/page')->load($parentID);

            $parents[] = $parentPage;

            return array_merge($parents, $parentPage->getParents($storeID));
        }

        return array();
    }

    protected function getReader()
    {
        return Mage::getSingleton('core/resource')->getConnection('core_read');
    }
}
