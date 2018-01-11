<?php

/**
 * Class ReportMapper_SalesReportMaps_Adminhtml_indexController
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

class ReportMapper_SalesReportMaps_Adminhtml_indexController extends Mage_Adminhtml_Controller_Action
{
    protected function _isAllowed()
    {
        return true;
    }
    public function mapperAction() {

        $this->loadLayout();
        $this->getLayout()->getBlock('head')->setTitle($this->__('ReportMapper - Sales Report Maps'));
        $this->_addLeft($this->getLayout()->createBlock('reportmapper_salesreportmaps/adminhtml_mapsmenu'));
        $this->renderLayout();

    }
}