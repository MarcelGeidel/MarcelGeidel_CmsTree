<?php

class MarcelGeidel_CmsTree_Model_System_Config_Source_Storeview
{
    public function toOptionArray()
    {
        $stores[] = array('value' => '', 'label' => '');

        foreach (Mage::helper('marcelgeidel_cmstree')->getStores() as $store) {
            $stores[] = array
            (
                'value' => $store->getId(),
                'label' => $store->getName(),
            );
        }

        return $stores;
    }
}
