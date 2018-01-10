<?php
/**
 * Magento Community Edition
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    RM
 * @package     ReportMappe_SalesReportMaps
 * @created     10th January 2018 3.00pm
 * @author      ReportMapper magento team <support@reportmapper.com>
 * @purpose     ReportMapper block
 * @copyright   Copyright (c) 2018 ReportMapper.com
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License
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