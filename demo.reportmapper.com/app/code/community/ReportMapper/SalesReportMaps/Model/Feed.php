<?php

/**
 * Class ReportMapper_SalesReportMaps_Model_Feed
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

class ReportMapper_SalesReportMaps_Model_Feed extends Mage_AdminNotification_Model_Feed
{
    public function getFeedUrl()
    {
        $protocol = (Mage::getStoreConfigFlag(self::XML_USE_HTTPS_PATH) ? 'https://' : 'http://');
        $feed = Mage::getStoreConfig('system/reportmapper_salesreportmaps/feed_url');
        return $protocol.$feed;
    }
    
    public function getLastUpdate()
    {
        return Mage::app()->loadCache('reportmapper_salesreportmaps_lastcheck');
    }

    public function setLastUpdate()
    {

        return Mage::app()->saveCache(time(), 'reportmapper_salesreportmaps_lastcheck');
    }
}