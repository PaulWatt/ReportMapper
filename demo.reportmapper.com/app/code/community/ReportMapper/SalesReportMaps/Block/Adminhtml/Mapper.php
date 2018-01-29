<?php

/**
 * Class ReportMapper_SalesReportMaps_Block_Adminhtml_Mapper
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

class ReportMapper_SalesReportMaps_Block_Adminhtml_Mapper extends Mage_Adminhtml_Block_Template
{

    public function __construct()
    {
        $this->rmURI = 'http://www.reportmapper.com';
        $this->rmEndpoint = 'http://www.reportmapper.com/validate_rm.php';
        $this->countryCode = $this->getMapByCountryCode();
        $this->paths = $this->getMapPaths();
        $this->areas = $this->getAreas();
        $this->svgConfig = $this->getSvgConfig();
    }

    public function getStoreNameFromId($storeId)
    {
        $store = Mage::getModel('core/store')->load($storeId);
        return $store->getName();
    }

    public function getStoreNames()
    {
    $selectedstoreids = Mage::getBlockSingleton('reportmapper_salesreportmaps/adminhtml_mapper')->getStoreIds();
    $storeIDs = array($selectedstoreids);
    foreach ($storeIDs as $storeId) {
        $storeName = $this->getStoreNameFromId($storeId);
    }
    
    if ($storeName == 'Admin') {
        $storeName = "All Stores";
    }
    
    return $storeName;
    }

    public function getPSKU()
    {
        $accessKey = Mage::getStoreConfig('user_data/authorisation/access_key');
        $options = array(CURLOPT_HEADER =>false,'location' => $this->rmEndpoint,
            'uri' => $this->rmURI);
        $api = new SoapClient(NULL, $options);
        $cururl = Mage::getUrl('admin');
        $psku = $api->psku($cururl, $accessKey);
        return $psku;
    }

    public function getMapByCountryCode()
    {
        $sku = $this->getPSKU();
        $countryCode = $sku[3];
        return $countryCode;
    }

    public function getMapPaths()
    {
        $accessKey = Mage::getStoreConfig('user_data/authorisation/access_key');
        $options = array(CURLOPT_HEADER =>false,'location' =>  $this->rmEndpoint,
            'uri' => $this->rmURI);
        $api = new SoapClient(NULL, $options);
        $cururl = Mage::getUrl('admin');
        $pts = $api->gpth($cururl, $accessKey, $this->countryCode);
        return $pts;
    }

    public function getAreas()
    {
        $accessKey = Mage::getStoreConfig('user_data/authorisation/access_key');
        $options = array(CURLOPT_HEADER =>false,'location' =>  $this->rmEndpoint,
            'uri' => $this->rmURI);
        $api = new SoapClient(NULL, $options);
        $cururl = Mage::getUrl('admin');
        $pts = $api->gpcd($cururl, $accessKey, $this->countryCode);
        return $pts;
    }

    public function getTxtPths()
    {
        $accessKey = Mage::getStoreConfig('user_data/authorisation/access_key');
        $options = array(CURLOPT_HEADER =>false,'location' =>  $this->rmEndpoint,
            'uri' => $this->rmURI);
        $api = new SoapClient(NULL, $options);
        $cururl = Mage::getUrl('admin');
        $pts = $api->gtxtpth($cururl, $accessKey, $this->countryCode);
        return $pts;
    }

    public function getSvgConfig()
    {
        $accessKey = Mage::getStoreConfig('user_data/authorisation/access_key');
        $options = array(CURLOPT_HEADER =>false,'location' =>  $this->rmEndpoint,
            'uri' => $this->rmURI);
        $api = new SoapClient(NULL, $options);
        $cururl = Mage::getUrl('admin');
        $svgconfig = $api->gsvg($cururl, $accessKey, $this->countryCode);
        return $svgconfig;
    }

    public function mrgPathData($arrayA, $arrayB)
    {
        $i=0;
        foreach ($arrayA as $val) {
            foreach ($val as $keyA => $valB) {
                $arrayA = $this->mrgPathDataSub($arrayA, $arrayB, $keyA, $valB, $i);
            }
            
             $i++;
        }
        
        usort(
            $arrayA, 
            function ($maxA, $maxB) {
            return $maxB['aa'] - $maxA['aa'];
            }
        );
        
        return $arrayA;
    }

    public function mrgPathDataSub($arrayA, $arrayB, $keyA, $valB, $i)
    {
                if ($keyA == 'pc') {
                    foreach ($arrayB as $valA) {
                        $push = false;
                        foreach ($valA as $keyB => $valC) {
                            if ($keyB == 'pc') {
                                if (strtoupper($valB) == strtoupper($valC)) {
                                    $push = true;
                                }
                            }

                            if ($push == true) {
                                if ($keyB == 'aa') {
                                    $arrayA[$i]['aa'] =  $valC;
                                }

                                if ($keyB == 'bb') {
                                    $arrayA[$i]['bb'] =   $valC;
                                }
                            }
                        }
                    }
                }


        return $arrayA;
    }

    public function mrgAreaData($arrayA, $arrayB) 
    {
        $i=0;
        foreach ($arrayA as $val) {
            foreach ($val as $keyA => $valB) {
                $arrayA = $this->mrgAreaDataSub($arrayA, $arrayB, $keyA, $valB, $i);
            }

            $i++;
        }

        usort(
            $arrayA, 
            function($maxA, $maxB) {
            return $maxB['aa'] - $maxA['aa'];
            }
        );
        return $arrayA;
    }

    public function mrgAreaDataSub($arrayA, $arrayB, $keyA, $valB, $i)
    {
        if ($keyA == 'pc') {
            $arrayA[$i]['postcode'] =   $valB;
            foreach ($arrayB as $valA) {
                $push = false;
                foreach ($valA as $keyB => $valC) {
                    if ($keyB == 'pc') {
                        if (strtoupper($valB) == strtoupper($valC)) {
                            $push = true;
                        }
                    }

                    if ($push == true) {
                        if ($keyB == 'aa') {
                            $arrayA[$i]['aa'] =  $valC;
                        }

                        if ($keyB == 'bb') {
                            $arrayA[$i]['bb'] =   $valC;
                        }
                    }
                }
            }
        }


        return $arrayA;
    }

    public function getMapSwitch(
        $mapType,
        $prefix,
        $fromDate,
        $toDate,
        $addressType,
        $extSqlA,
        $extSqlB,
        $extSqlC,
        $extSqlD,
        $extSqlE,
        $areaCodePrefix
    )
        {
        global $labelHOne, $labelAAHeader, $labelBBHeader, $rowAAHeader, $rowBBHeader, $hOne, $currencyA, $currencyB;
            $SQLModel = Mage::getModel('reportmapper_salesreportmaps/adminhtml_sqls');
            $countryCode = $this->countryCode;
            switch ($mapType)

            {
                //Orders
                case ($mapType == "orders"):

                    $dataSQL = $SQLModel->getOrders(
                        $prefix,
                        $fromDate,
                        $toDate,
                        $addressType,
                        $extSqlA,
                        $extSqlB,
                        $extSqlE,
                        $countryCode
                    );
                    $labelsSQL = $SQLModel->getLabelItems_OrdersSQL(
                        $prefix,
                        $fromDate,
                        $toDate,
                        $addressType,
                        $extSqlA,
                        $areaCodePrefix,
                        $countryCode
                    );
                    $rowAAHeader = "Orders";
                    $rowBBHeader = "Units";
                    $hOne = "Orders Report";
                    $labelAAHeader = "Products";
                    $labelBBHeader = "Qty";
                    $labelHOne = "Orders";
                    break;

                    //Units
                case ($mapType == "units"):

                    $dataSQL = $SQLModel->getUnits(
                        $prefix,
                        $fromDate,
                        $toDate,
                        $addressType,
                        $extSqlA,
                        $extSqlB,
                        $extSqlE,
                        $countryCode
                    );
                    $labelsSQL = $SQLModel->getLabelItems_UnitsSQL(
                        $prefix,
                        $fromDate,
                        $toDate,
                        $addressType,
                        $extSqlA,
                        $areaCodePrefix,
                        $countryCode
                    );
                    $rowAAHeader = "Units";
                    $rowBBHeader = "Orders";
                    $hOne = "Units Sold Report";
                    $labelAAHeader = "Products";
                    $labelBBHeader = "Units";
                    $labelHOne = "Units";
                    break;

                    //Values
                case ($mapType == "totalvalues"):

                    $dataSQL = $SQLModel->getTotalValues(
                        $prefix,
                        $fromDate,
                        $toDate,
                        $addressType,
                        $extSqlA,
                        $extSqlB,
                        $extSqlE,
                        $countryCode
                    );
                    $labelsSQL = $SQLModel->getLabelItems_TotalValuesSQL(
                        $prefix,
                        $fromDate,
                        $toDate,
                        $addressType,
                        $extSqlA,
                        $areaCodePrefix,
                        $countryCode
                    );
                    $rowAAHeader = "Tot Val";
                    $rowBBHeader = "Avg Val";
                    $hOne = "Total Sales Values Report";
                    $labelAAHeader = "Products";
                    $labelBBHeader = "Value";
                    $labelHOne = "Values";
                    $currencyA = Mage::app()->getLocale()
                        ->currency(Mage::app()->getStore()->getCurrentCurrencyCode())
                        ->getSymbol();
                    $currencyB = Mage::app()->getLocale()
                        ->currency(Mage::app()->getStore()->getCurrentCurrencyCode())
                        ->getSymbol();
                    break;

                    //Refunds
                case ($mapType == "refunds"):

                    $dataSQL = $SQLModel->getRefunds(
                        $prefix,
                        $fromDate,
                        $toDate,
                        $addressType,
                        $extSqlC,
                        $extSqlE,
                        $countryCode
                    );
                    $labelsSQL = $SQLModel->getLabelItems_RefundsSQL(
                        $prefix,
                        $fromDate,
                        $toDate,
                        $addressType,
                        $extSqlC,
                        $areaCodePrefix,
                        $countryCode
                    );
                    $rowAAHeader = "Qty";
                    $rowBBHeader = "Value";
                    $hOne = "Refunds Report";
                    $labelAAHeader = "Products";
                    $labelBBHeader = "Value";
                    $labelHOne = "Refunds";
                    $currencyA = Mage::app()->getLocale()
                        ->currency(Mage::app()->getStore()->getCurrentCurrencyCode())
                        ->getSymbol();
                    $currencyB = Mage::app()->getLocale()
                        ->currency(Mage::app()->getStore()->getCurrentCurrencyCode())
                        ->getSymbol();
                    break;

                    //Customers
                case ($mapType == "customers"):

                    $dataSQL = $SQLModel->getCustomers(
                        $prefix,
                        $fromDate,
                        $toDate,
                        $extSqlD,
                        $extSqlE
                    );
                    $labelsSQL = $SQLModel->getLabelItems_CustomersSQL(
                        $prefix,
                        $fromDate,
                        $toDate,
                        $addressType,
                        $extSqlD,
                        $areaCodePrefix,
                        $countryCode
                    );
                    $rowAAHeader = "No Of";
                    $rowBBHeader = "Spend";
                    $hOne = "Customers Report";
                    $labelAAHeader = "Products";
                    $labelBBHeader = "Qtys";
                    $labelHOne = "Customers";
                    $currencyA = NULL;
                    $currCode = Mage::app()->getStore()->getCurrentCurrencyCode();
                    $currencyB = Mage::app()->getLocale()->currency()->getSymbol($currCode);
                    break;

                    //Delivery Costs
                case ($mapType == "deliverycost"):

                    $dataSQL = $SQLModel->getDeliverycosts(
                        $prefix,
                        $fromDate,
                        $toDate,
                        $addressType,
                        $extSqlA,
                        $extSqlB,
                        $extSqlE,
                        $countryCode
                    );
                    $labelsSQL = $SQLModel->getLabelItems_DeliverycostsSQL(
                        $prefix,
                        $fromDate,
                        $toDate,
                        $addressType,
                        $extSqlA,
                        $areaCodePrefix,
                        $countryCode
                    );
                    $rowAAHeader = "Del's";
                    $rowBBHeader = "Value";
                    $hOne = "Delivery Costs Report";
                    $labelAAHeader = "Products";
                    $labelBBHeader = "Qtys";
                    $labelHOne = "Deliverys";
                    $currencyA = NULL;
                    $currCode = Mage::app()->getStore()->getCurrentCurrencyCode();
                    $currencyB = Mage::app()->getLocale()->currency()->getSymbol($currCode);
                    break;

                    //Dflt
                case ($mapType == "-1"):
                    $dataSQL = $SQLModel->getOrders(
                        $prefix,
                        "",
                        "",
                        $addressType,
                        $extSqlA,
                        $extSqlB,
                        $extSqlC,
                        $extSqlD,
                        $extSqlE,
                        $areaCodePrefix,
                        $countryCode
                    );
                    $labelsSQL = $SQLModel->getLabelItems_OrdersSQL(
                        $prefix,
                        "",
                        "",
                        $addressType,
                        $extSqlA,
                        $extSqlB,
                        $extSqlC,
                        $extSqlD,
                        $extSqlE,
                        $areaCodePrefix,
                        $countryCode
                    );
                    $rowAAHeader = "Orders";
                    $rowBBHeader = "Units";
                    $hOne = "Orders Report";
                    $labelAAHeader = "Products";
                    $labelBBHeader = "Qty";
                    $labelHOne = "Orders";
                    break;
            }

            $switchSQL=array(
                'DataSQL'=>$dataSQL,
                'LabelsSQL' => $labelsSQL,
                'row_aa_header'=>$rowAAHeader,
                'row_bb_header'=>$rowBBHeader,
                'h1'=>$hOne,
                'label_aa_header'=>$labelAAHeader,
                'label_bb_header'=>$labelBBHeader,
                'label_h1'=>$labelHOne,
                'currencyA'=>$currencyA,
                'currencyB'=>$currencyB

            );

            return $switchSQL;
    }

    public function getMaxValue($results) 
    {
        usort(
            $results, 
            function($maxA, $maxB) {
                return $maxB['aa'] - $maxA['aa'];
            }
        );
        $maxarray=array();
        array_push($maxarray, array_slice($results, 0, 1));
    return $maxarray[0][0]['aa'];
    }

    public function getReadmodel()
    {
        $readModel = Mage::getModel('reportmapper_salesreportmaps/adminhtml_sqls')->getRead();
        return $readModel;
    }

    public function getLicenceValidation()
    {
        $accessKey = Mage::getStoreConfig('user_data/authorisation/access_key');
        $options = array(CURLOPT_HEADER =>false,'location' =>  $this->rmEndpoint,
            'uri' => $this->rmURI);
        $api = new SoapClient(NULL, $options);
        $cururl = Mage::getUrl('admin');
        return $api->validate($cururl, $accessKey);
    }

    public function getValid()
    {
        $accessKey = Mage::getStoreConfig('user_data/authorisation/access_key');
        $options = array(CURLOPT_HEADER =>false,'location' =>  $this->rmEndpoint,
            'uri' => $this->rmURI);
        $api = new SoapClient(NULL, $options);
        $cururl = Mage::getUrl('admin');
        return $api->isvalid($cururl, $accessKey);
    }

    public function getHighlightColour()
    {
        $mapType = $this->getMapType();
        switch ($mapType) {
            //Orders
            case ($mapType == "orders"):
                $defaultHighlightColour = "#FFC219";
                break;

                //Units
            case ($mapType == "units"):
                $defaultHighlightColour = "#66CCFF";
                break;

                //Values
            case ($mapType == "totalvalues"):
                $defaultHighlightColour = "#F1FF26";
                break;

                //Refunds
            case ($mapType == "refunds"):
                $defaultHighlightColour = "#BABABA";
                break;

                //Customers
            case ($mapType == "customers"):
                $defaultHighlightColour = "#FFF069";
                break;

                //Delivery Costs
            case ($mapType == "deliverycost"):
                $defaultHighlightColour = "#CCCCCC";
                break;
                
            //Dflt
            case ($mapType == "-1"):
                $defaultHighlightColour = "";
                break;
        }

        $lastMapType = Mage::getSingleton('core/session')->getMapType();
        if ($lastMapType != $mapType) {
            $highlightColour = $defaultHighlightColour;
        } else {
            $highlightColour = $this->getRequest()->getParam('highlight_colour', $defaultHighlightColour);
        }

        return $highlightColour;
    }

    public function getMapColour()
    {
        $mapType = $this->getMapType();
        switch ($mapType) {
            //Orders
            case ($mapType == "orders"):
                $defaultMapColour = "#1F31AB";
                break;

                //Units
            case ($mapType == "units"):
                $defaultMapColour = "#1BAB4B";
                break;

                //Values
            case ($mapType == "totalvalues"):
                $defaultMapColour = "#0A4D4A";
                break;

                //Refunds
            case ($mapType == "refunds"):
                $defaultMapColour = "#AB0A0A";
                break;

                //Customers
            case ($mapType == "customers"):
                $defaultMapColour = "#FF7417";
                break;

                //Delivery Costs
            case ($mapType == "deliverycost"):
                $defaultMapColour = "#000000";
                break;

            //Dflt
            case ($mapType == "-1"):
                $defaultMapColour = "";
                break;
        }
        
        $lastMapType = Mage::getSingleton('core/session')->getMapType();

        if ($lastMapType != $mapType) {
            $mapColour = $defaultMapColour;
        } else {
            $mapColour = $this->getRequest()->getParam('map_colour', $defaultMapColour);
        }
        
        return $mapColour;
    }

    public function getStoreIds()
    {
            $storeIDs = $this->getRequest()->getParam('store_ids', '0');
        return $storeIDs;
    }

    public function getFromDate()
    {
        global $reqFromDate;


        $date = new Zend_Date(Mage::getModel('core/date')->timestamp());
        $defaultFromDate = Mage::getModel('core/date')->date('d/m/Y', strtotime($date." -3 year"));
        $reqFromDate = $this->getRequest()->getParam('fromdate', $defaultFromDate);
        $fromDate = str_replace('/', '-', $reqFromDate);
        $fromDate = date('Y-m-d', strtotime($fromDate)).' 00:00:00';
        return $fromDate;
    }

    public function getToDate()
    {
        global $reqToDate;
        $date = new Zend_Date(Mage::getModel('core/date')->timestamp());
        $defaultToDate = Mage::getModel('core/date')->date('d/m/Y', strtotime($date." -1 month"));
        $reqToDate = $this->getRequest()->getParam('todate', $defaultToDate);
        $toDate = str_replace('/', '-', $reqToDate);
        $toDate = date('Y-m-d', strtotime($toDate)).' 23:59:59';
        return $toDate;
    }

    public function getAddressType()
    {
        $addressType = $this->getRequest()->getParam('addresstype', 'billing');
        return $addressType;
    }

    public function getMapType()
    {
        $mapType = $this->getRequest()->getParam('maptype', '-1');
        return $mapType;
    }

    public function getMapData()
    {
        global $rowAAHeader, $rowBBHeader, $hOne;

        $read = Mage::getSingleton('core/resource')->getConnection('core_read');
        $prefix = Mage::getConfig()->getTablePrefix();
        $fromDate = $this->getFromDate();
        $toDate = $this->getToDate();
        $addressType = $this->getAddressType();
        $mapType = $this->getMapType();
        $extSqlA = '';
        $extSqlB = '';
        $areaCodePrefix = '';
        $productsReq = $this->getSelectedProducts();
        $products = implode(",", $productsReq);
        $allProducts = strpos($products, '*');

        if ($allProducts === false) {
            $extSqlA = 'AND '.$prefix.'sales_flat_order_item.product_id IN ('.$products.')';
            $extSqlC = 'AND '.$prefix.'sales_flat_creditmemo_item.product_id IN ('.$products.')';
        }

        $storeId = $this->getStoreIds();
        if ($storeId !=0) {
            $extSqlB = 'AND '.$prefix.'sales_flat_order.store_id IN ('.$storeId.')';
            $extSqlD = 'AND '.$prefix.'customer_entity.store_id IN ('.$storeId.')';
        }

        $couponRuleCodesReq = $this->getSelectedCouponRuleCodes();
        $couponRuleCodesImpl = implode("','", $couponRuleCodesReq);

        if (!in_array("*", $couponRuleCodesReq)) {
            $extSqlE = "AND ".$prefix."sales_flat_order.coupon_code IN ('".$couponRuleCodesImpl."')";
        }

        $dataSQL = $this->getMapSwitch(
            $mapType,
            $prefix,
            $fromDate,
            $toDate,
            $addressType,
            $extSqlA,
            $extSqlB,
            $extSqlC,
            $extSqlD,
            $extSqlE,
            $areaCodePrefix,
            $this->countryCode
        );
        $rowAAHeader = $dataSQL['row_aa_header'];
        $rowBBHeader = $dataSQL['row_bb_header'];
        $hOne = $dataSQL['h1'];
        $result = $read->fetchAll($dataSQL['DataSQL']);

        return $result;
    }

    public function getSelectedProducts()
    {
        $selectedProducts = $this->getRequest()->getParam('products', array('*'));
        return $selectedProducts;
    }

    public function getSelectedProductNames($productIds)
    {
        if (in_array("*", $productIds)) {
            $selectedProductNames = "No Filter";
        } else {
        $products = Mage::getModel('catalog/product')->getCollection();
        $products->addAttributeToFilter('entity_id', array('in' => $productIds));
        $products->addAttributeToSelect(array('name'));

       $selectedProductNames = array();
        foreach ($products as $product) {
            array_push($selectedProductNames, $product->getName());
        }
        
        $selectedProductNames = implode(", ", $selectedProductNames);
        }
        
        return $selectedProductNames;
    }

    public function getSelectedCouponRuleCodes()
    {
        $couponRuleCodes = $this->getRequest()->getParam('coupon_rule_name', array('*'));
        return $couponRuleCodes;
    }

    public function getSelectedCouponRuleNames($ruleIds)
    {
        if (in_array("*", $ruleIds)) {
            $selectedRuleNames = "No Filter";
        } else {
            $rules = Mage::getModel('salesrule/rule')->getCollection();
            $selectedRuleNames = array();
            foreach ($rules as $rule) {
                if (in_array($rule->getCode(), $ruleIds)) {
                array_push($selectedRuleNames, $rule->getName());
                }
            }

            $selectedRuleNames = implode(", ", $selectedRuleNames);
        }

        return $selectedRuleNames;
    }

    public function getAllOrdersCount($fromDate, $toDate)
    {

        $fromDate = strtotime($fromDate);
        $toDate = strtotime($toDate);
        $collection = Mage::getResourceModel('sales/order_item_collection')->addAttributeToSelect('qty_ordered');
        $collection->addAttributeToFilter('created_at', array('gteq' =>date("Y-m-d", $fromDate)));
        $collection->addAttributeToFilter('created_at', array('lteq' => date("Y-m-d", $toDate)));

       return $collection->getSize();
    }

    public function getSelectedAllOrdersRelative()
    {
        $allOrdersRelative = $this->getRequest()->getParam('all_orders_relative', array(''));
        if (empty($allOrdersRelative)) {
            return "1";
        } else {
            return "0";
        }
    }

    public function getSVG()
    {
        global $hOne, $reqFromDate, $reqToDate, $allOrdersCount, $rmFooter;
        $svgconfig = $this->svgConfig;
        $allOrdersCount = $this->getAllOrdersCount($reqFromDate, $reqToDate);
        $mapType = $this->getMapType();
        $storeNames = $this->getStoreNames();
        $productNames = $this->getSelectedProductNames($this->getSelectedProducts());
        $ruleIds = $this->getSelectedCouponRuleCodes();
        $ruleNames = $this->getSelectedCouponRuleNames($ruleIds);
        $rmHeader = "Set your Report Map Options then click 'Create Report'.";
        if ($mapType != '-1') {
        $rmHeader = "ReportMapper: $hOne From: $reqFromDate To: $reqToDate";
        }
        
        $rmFooter = "Stores: $storeNames | Products: $productNames | Promotion: $ruleNames";
        $svgDataArray = array(
            "svg_id" => $svgconfig[0]['map_svg_id'],
            "svg_xmlns" => $svgconfig[0]['map_svg_xmlns'],
            "svg_version" => $svgconfig[0]['map_svg_version'],
            "svg_width" => $svgconfig[0]['map_svg_width'],
            "svg_height" => $svgconfig[0]['map_svg_height'],
            "svg_style" => $svgconfig[0]['map_svg_style'],
            "svg_transform" => $svgconfig[0]['map_svg_transform'],
            "svg_viewbox" => $svgconfig[0]['map_svg_viewbox'],
            "svg_viewport" => $svgconfig[0]['map_svg_viewport'],
            "svg_g_transform" => $svgconfig[0]['map_svg_g_transform'],
            "svg_g_labels_transform" => $svgconfig[0]['map_svg_g_labels_transform'],
            "svg_header" => $rmHeader,
            "svg_footer"    => $rmFooter
        );
        if ($this->getValid()) {
            return $svgDataArray;
        }
    }

    public function getLabelItems($areaCodePrefix)
    {
        global $labelAAHeader, $labelBBHeader, $labelHOne;
        $countryCode = $this->countryCode;
        $read = Mage::getSingleton('core/resource')->getConnection('core_read');
        $prefix = Mage::getConfig()->getTablePrefix();
        $fromDate = $this->getFromDate();
        $toDate = $this->getToDate();
        $addressType = $this->getAddressType();
        $mapType = $this->getMapType();
        $extSqlA = '';
        $extSqlB = '';
        $productsReq = $this->getSelectedProducts();
        $products = implode(",", $productsReq);
        $allProducts = strpos($products, '*');

        if ($allProducts === false) {
            $extSqlA = 'AND '.$prefix.'sales_flat_order_item.product_id IN ('.$products.')';
            $extSqlC = 'AND '.$prefix.'sales_flat_creditmemo_item.product_id IN ('.$products.')';
        }

        $storeId = $this->getStoreIds();
        if ($storeId !=0) {
            $extSqlB = 'AND '.$prefix.'sales_flat_order.store_id IN ('.$storeId.')';
            $extSqlD = 'AND '.$prefix.'customer_entity.store_id IN ('.$storeId.')';
        }

        $couponRuleCodesReq = $this->getSelectedCouponRuleCodes();
        $couponRuleCodesImpl = implode("','", $couponRuleCodesReq);
        if (!in_array("*", $couponRuleCodesReq)) {
            $extSqlE = "AND ".$prefix."sales_flat_order.coupon_code IN ('".$couponRuleCodesImpl."')";
        }

        $dataSQL = $this->getMapSwitch(
            $mapType,
            $prefix,
            $fromDate,
            $toDate,
            $addressType,
            $extSqlA,
            $extSqlB,
            $extSqlC,
            $extSqlD,
            $extSqlE,
            $areaCodePrefix,
            $countryCode
        );
        $labelAAHeader = $dataSQL['label_aa_header'];
        $labelBBHeader = $dataSQL['label_bb_header'];
        $labelHOne = $dataSQL['label_h1'];
        $result = $read->fetchAll($dataSQL['LabelsSQL']);
        return $result;
    }

    public function getTables()
    {
        global $rowAAHeader, $rowBBHeader, $currencyA, $currencyB, $allOrdersCount;
        $results = $this->getMapData();
        $areas = $this->areas;
        $results = $this->mrgAreaData($areas, $results);
        $maxAa = $this->getMaxValue($results);
        $allOrdersRelative = $this->getSelectedAllOrdersRelative();
        if ($allOrdersRelative == 1) {
            $maxAa = $allOrdersCount;
        }
        
        $mapColour = $this->getMapColour();
        $highlightColour = $this->getHighlightColour();
        $tableData = array(
            "table_results" => $results,
            "table_max_aa" => $maxAa,
            "table_map_colour" => $mapColour,
            "table_highlight_colour" => $highlightColour,
            "table_row_aa_header" => $rowAAHeader,
            "table_row_bb_header" => $rowBBHeader,
            "table_currencyA" => $currencyA,
            "table_currencyB" => $currencyB,
            "table_allOrdersCount" => $allOrdersCount
        );

    $mapType = $this->getMapType();
    Mage::getSingleton('core/session')->setMapType($mapType);

        if ($this->getValid()) {
            return $tableData;
        }
    }

    public function getPaths()
    {
        global $allOrdersCount;
        $mapColour = $this->getMapColour();
        $highlightColour = $this->getHighlightColour();
        $results = $this->getMapData();
        $paths = $this->paths;
        $results = $this->mrgPathData($paths, $results);
        $maxAa = $this->getMaxValue($results);
        $allOrdersRelative = $this->getSelectedAllOrdersRelative();
        if ($allOrdersRelative == 1) {
            $maxAa = $allOrdersCount;
        }
        
        $pathData = array(
            "path_results" => $results,
            "path_max_aa" => $maxAa,
            "path_map_colour" => $mapColour,
            "path_highlight_colour" => $highlightColour

        );
         return $pathData;
    }

    public function getMapLabeling()
    {
        $countryCode = $this->countryCode;
        $mapLables = $this->getTxtPths($countryCode);
        return $mapLables;
    }

    public function getLabels()
    {
        global $labelAAHeader, $labelBBHeader, $labelHOne, $currencyA, $currencyB;
        $results = $this->getMapData();
        $labelData = array(
            "label_results" => $results,
            "label_aa_header" => $labelAAHeader,
            "label_bb_header" => $labelBBHeader,
            "label_h1" => $labelHOne,
            "currencyA" => $currencyA,
            "currencyB" => $currencyB
        );
            return $labelData;
    }

    public function getOpaqueness($aa, $max)
    {
        $percentage= round(($aa / $max) * 100, 2);
        switch ($percentage) {
            case ($percentage < "0.001"):

                $opacity = 0.25;
                $opacitycss = 0.75;
                break;
            case ($percentage < "10"):

                $opacity = 0.3;
                $opacitycss = 0.7;
                break;
            case ($percentage < "20"):

                $opacity = 0.35;
                $opacitycss = 0.65;
                break;
            case ($percentage < "30"):

                $opacity = 0.4;
                $opacitycss = 0.6;
                break;
            case ($percentage < "40"):

                $opacity = 0.5;
                $opacitycss = 0.5;
                break;
            case ($percentage < "50"):

                $opacity = 0.6;
                $opacitycss = 0.4;
                break;
            case ($percentage < "70"):

                $opacity = 0.7;
                $opacitycss = 0.3;
                break;
            case ($percentage < "80"):

                $opacity = 0.8;
                $opacitycss = 0.2;
                break;
            case ($percentage < "90"):

                $opacity = 0.9;
                $opacitycss = 0.1;
                break;
            case ($percentage < "100.01"):

                $opacity = 1;
                $opacitycss = 0;
                break;
        }

        if ((!isset($aa)) OR ($aa === null) OR ($aa == 0)) {
            $opacity = 0;
            $opacitycss = 1;
            $aa = 0;
        }

        $coloursArray=array(
            'opacity'=>$opacity,
            'opacitycss'=>$opacitycss,
            'aa'=>$aa
        );
        return $coloursArray;

    }


}