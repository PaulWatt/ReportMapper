<?php

/**
 * Class ReportMapper_SalesReportMaps_Block_Adminhtml_Version
 * ReportMapper - Sales and Marketing Reporting Tool for Magento 1x
 * @category ReportMapper
 * @package	ReportMapper_SalesReportMaps
 * @version	1.0.0
 * @created	10th January 2018 3.00pm
 * @author		Paul Watt <support@reportmapper.com>
 * @purpose	ReportMapper block
 * @copyright	Copyright (c) 2018 ReportMapper.com
 * @license	http://opensource.org/licenses/osl-3.0.php  Open Software License
 */

class ReportMapper_SalesReportMaps_Block_Adminhtml_Version
extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    public function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        return (string) Mage::helper('reportmapper_salesreportmaps')->getExtensionVersion();
    }
}