<?php

class MarcelGeidel_CmsTree_Block_Breadcrumbs extends Mage_Page_Block_Html_Breadcrumbs
{
    public function clear()
    {
    	$this->_crumbs = array();
    }
}
