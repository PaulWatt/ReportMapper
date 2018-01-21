<?php

/**
 * Class ReportMapper_SalesReportMaps_Model_Observer
 * ReportMapper - Sales and Marketing Reporting Tool for Magento 1x
 * @category ReportMapper
 * @package ReportMapper_SalesReportMaps
 * @version 1.0.0
 * @created 10th January 2018 3.00pm
 * @author Paul Watt <support@reportmapper.com>
 * @purpose ReportMapper block
 * @copyright Copyright (c) 2018 ReportMapper.com
 * @license http://opensource.org/licenses/osl-3.0.php  Open Software License
 */

class ReportMapper_SalesReportMaps_Model_Observer
{
    public function controllerActionPredispatch($observer)
    {
        if (Mage::getSingleton('admin/session')->isLoggedIn()) {
            $model = Mage::getModel('reportmapper_salesreportmaps/feed');
            $model->checkUpdate();
        }
    }
}