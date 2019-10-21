<?php

class MarcelGeidel_CmsTree_Block_Adminhtml_System_Config_Form_Button extends Mage_Adminhtml_Block_System_Config_Form_Field
{
	protected function _construct()
    {
        parent::_construct();
        
        $this->setTemplate('marcelgeidel/cmstree/system/config/button.phtml');
    }
    
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        return $this->_toHtml();
    }
    
    public function getAjaxUrl()
    {
    	return Mage::helper('adminhtml')->getUrl('adminhtml/cmsTree/resetTree');
    }
    
    public function getButtonHtml()
    {
    	$button = $this->getLayout()->createBlock('adminhtml/widget_button')->setData(array
    	(
    		'id'		=> 'reset_tree',
            'label'		=> $this->helper('marcelgeidel_cmstree')->__('Reset'),
            'onclick'	=> 'javascript:resetTree(); return false;'
        ));
    	
        return $button->toHtml();
    }
}
