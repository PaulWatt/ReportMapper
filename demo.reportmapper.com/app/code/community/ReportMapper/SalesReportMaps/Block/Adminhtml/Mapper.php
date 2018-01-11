<?php

/**
 * Class ReportMapper_SalesReportMaps_Block_Adminhtml_Mapper
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

class ReportMapper_SalesReportMaps_Block_Adminhtml_Mapper
    extends Mage_Core_Block_Template
{
function __construct()
{
    $this->pths = $this->getPths('UK');
    $this->pcds = $this->getPcds('UK');
}
public function getStoreNames(){
$selectedstoreids = Mage::getBlockSingleton('reportmapper_salesreportmaps/adminhtml_mapper')->getStoreIds();

$store_ids = array($selectedstoreids);
foreach($store_ids as $storeId){
    $store = Mage::getModel('core/store')->load($storeId);
    $store_name = $store->getName();

}
if ($store_name == 'Admin')
{$store_name = "All Stores";}
return $store_name;


}
public function getPths($pathmap){
    $access_key = Mage::getStoreConfig('user_data/authorisation/access_key');
    $options = array(CURLOPT_HEADER =>false,'location' => 'http://www.reportmapper.com/validate_rm.php',
        'uri' => 'http://www.reportmapper.com');
    //create an instante of the SOAPClient (the API will be available)
    //die($access_key);
    $api = new SoapClient(NULL, $options);
    //call an API method
    $cururl = Mage::getUrl('admin');
    $pts = $api->gpth($cururl,$access_key, $pathmap);
    return $pts;
}
public function getPcds($pathmap){
    $access_key = Mage::getStoreConfig('user_data/authorisation/access_key');
    $options = array(CURLOPT_HEADER =>false,'location' => 'http://www.reportmapper.com/validate_rm.php',
        'uri' => 'http://www.reportmapper.com');
    //create an instante of the SOAPClient (the API will be available)
    //die($access_key);
    $api = new SoapClient(NULL, $options);
    //call an API method
    $cururl = Mage::getUrl('admin');
    $pts = $api->gpcd($cururl,$access_key, $pathmap);
    return $pts;
}
public function getTxtPths($pathmap){
    $access_key = Mage::getStoreConfig('user_data/authorisation/access_key');
    $options = array(CURLOPT_HEADER =>false,'location' => 'http://www.reportmapper.com/validate_rm.php',
        'uri' => 'http://www.reportmapper.com');
    //create an instante of the SOAPClient (the API will be available)
    //die($access_key);
    $api = new SoapClient(NULL, $options);
    //call an API method
    $cururl = Mage::getUrl('admin');
    $pts = $api->gtxtpth($cururl,$access_key, $pathmap);
    return $pts;
}
public function mergePathsTodata($array1,$array2){
    $i=0;
    foreach ($array1 as $val){
        foreach ($val as $key1 => $val1){
            if($key1 == 'pc'){
                foreach ($array2 as $val0) {
                    $push = false;
                    foreach ($val0 as $key2 => $val2) {
                        if ($key2 == 'pc') {
                            if (strtoupper($val1) == strtoupper($val2)) {
                                $push = true;
                            }
                        }
                        if ($push == true) {
                            if ($key2 == 'aa') {
                                $array1[$i]['aa'] =  $val2;
                            }
                            if ($key2 == 'bb') {
                                $array1[$i]['bb'] =   $val2;
                            }
                        }
                    }
                }
            }
        }
         $i++;
    }
    usort($array1, function($max_a, $max_b) {
        return $max_b['aa'] - $max_a['aa'];
    });
    return $array1;
}
public function mergePcdsTodata($array1,$array2){
    $i=0;
    foreach ($array1 as $val){
        foreach ($val as $key1 => $val1){
            if($key1 == 'pc'){
                $array1[$i]['postcode'] =   $val1;
                foreach ($array2 as $val0) {
                    $push = false;
                    foreach ($val0 as $key2 => $val2) {
                        if ($key2 == 'pc') {

                            if (strtoupper($val1) == strtoupper($val2)) {
                                $push = true;
                            }
                        }
                        if ($push == true) {
                            if ($key2 == 'aa') {
                                $array1[$i]['aa'] =  $val2;
                            }
                            if ($key2 == 'bb') {
                                $array1[$i]['bb'] =   $val2;
                            }
                        }
                    }
                }
            }
        }
        $i++;
    }
    usort($array1, function($max_a, $max_b) {
        return $max_b['aa'] - $max_a['aa'];
    });
    return $array1;
}
public function getMapSwitch($maptype,$prefix,$fromdate,$todate,$addresstype,$extendedsql1,$extendedsql2,$extendedsql3,$extendedsql4,$extendedsql5,$postcodeprefix,$pathmap)
    {
    global $label_h1,$label_aa_header,$label_bb_header,$row_aa_header,$row_bb_header,$h1,$currencyA,$currencyB;
        /*query types - orders,units,values,refunds,p-l,customers,deliverycost,quotes,telorders,spend*/
        $SQLModel = Mage::getModel('reportmapper_salesreportmaps/adminhtml_sqls');
        
        switch ($maptype) {
    
            //Orders
            case ($maptype == "orders"):
                 
                $DataSQL = $SQLModel->getOrders($prefix,$fromdate, $todate, $addresstype, $extendedsql1, $extendedsql2,$extendedsql3,$extendedsql4,$extendedsql5,$postcodeprefix,$pathmap);
               //$pths = $this->getPths('UK');
               //var_dump($pths);
                //$DataSQL = mergePathsTodata($pths,$DataSQL);


                $LabelsSQL = $SQLModel->getLabelItems_OrdersSQL($prefix,$fromdate,$todate, $addresstype, $extendedsql1, $extendedsql2, $extendedsql3, $extendedsql4,$extendedsql5,$postcodeprefix,$pathmap);
                $row_aa_header = "Orders";
                $row_bb_header = "Units";
                $h1 = "Orders Report";
                $label_aa_header = "Products";
                $label_bb_header = "Qty";
                $label_h1 = "Orders";
                break;
                 
                //Units
            case ($maptype == "units"):
    
                $DataSQL = $SQLModel->getUnits($prefix, $fromdate, $todate, $addresstype, $extendedsql1, $extendedsql2,$extendedsql3,$extendedsql4,$extendedsql5,$postcodeprefix,$pathmap);
                $LabelsSQL = $SQLModel->getLabelItems_UnitsSQL($prefix,$fromdate,$todate, $addresstype, $extendedsql1, $extendedsql2, $extendedsql3, $extendedsql4,$extendedsql5,$postcodeprefix,$pathmap);
                $row_aa_header = "Units";
                $row_bb_header = "Orders";
                $h1 = "Units Sold Report";
                $label_aa_header = "Products";
                $label_bb_header = "Units";
                $label_h1 = "Units";
                break;
                 
                //Values
            case ($maptype == "values"):
    
                $DataSQL = $SQLModel->getValues($prefix, $fromdate, $todate, $addresstype, $extendedsql1, $extendedsql2,$extendedsql3,$extendedsql4,$extendedsql5,$postcodeprefix,$pathmap);
                $LabelsSQL = $SQLModel->getLabelItems_ValuesSQL($prefix,$fromdate,$todate, $addresstype, $extendedsql1, $extendedsql2, $extendedsql3, $extendedsql4,$extendedsql5,$postcodeprefix,$pathmap);
                $row_aa_header = "Tot Val";
                $row_bb_header = "Avg Val";
                $h1 = "Total Sales Values Report";
                $label_aa_header = "Products";
                $label_bb_header = "Value";
                $label_h1 = "Values";
                $currencyA = Mage::app()->getLocale()->currency(Mage::app()->getStore()->getCurrentCurrencyCode())->getSymbol();
                $currencyB = Mage::app()->getLocale()->currency(Mage::app()->getStore()->getCurrentCurrencyCode())->getSymbol();
                break;
    
                //Refunds
            case ($maptype == "refunds"):
                 
                $DataSQL = $SQLModel->getRefunds($prefix,$fromdate,$todate, $addresstype, $extendedsql1, $extendedsql2,$extendedsql3,$extendedsql4,$extendedsql5,$postcodeprefix,$pathmap);
                $LabelsSQL = $SQLModel->getLabelItems_RefundsSQL($prefix,$fromdate,$todate, $addresstype, $extendedsql1, $extendedsql2, $extendedsql3, $extendedsql4,$extendedsql5,$postcodeprefix,$pathmap);
                $row_aa_header = "Qty";
                $row_bb_header = "Value";
                $h1 = "Refunds Report";
                $label_aa_header = "Products";
                $label_bb_header = "Value";
                $label_h1 = "Refunds";
                //$currencyA = Mage::app()->getLocale()->currency(Mage::app()->getStore()->getCurrentCurrencyCode())->getSymbol();
                $currencyB = Mage::app()->getLocale()->currency(Mage::app()->getStore()->getCurrentCurrencyCode())->getSymbol();
                break;
                 
                //Customers
            case ($maptype == "customers"):
    
                $DataSQL = $SQLModel->getCustomers($prefix,$fromdate,$todate, $addresstype, $extendedsql1, $extendedsql2,$extendedsql3,$extendedsql4,$extendedsql5,$postcodeprefix,$pathmap);
                $LabelsSQL = $SQLModel->getLabelItems_CustomersSQL($prefix,$fromdate,$todate, $addresstype, $extendedsql1, $extendedsql2, $extendedsql3, $extendedsql4,$extendedsql5,$postcodeprefix,$pathmap);
                $row_aa_header = "No Of";
                $row_bb_header = "Spend";
                $h1 = "Customers Report";
                $label_aa_header = "Products";
                $label_bb_header = "Qtys";
                $label_h1 = "Customers";
                //$currencyA = Mage::app()->getLocale()->currency(Mage::app()->getStore()->getCurrentCurrencyCode())->getSymbol();
                $currencyB = Mage::app()->getLocale()->currency(Mage::app()->getStore()->getCurrentCurrencyCode())->getSymbol();
                break;
                 
                //Delivery Costs
            case ($maptype == "deliverycost"):
                 
                $DataSQL = $SQLModel->getDeliverycosts($prefix,$fromdate,$todate, $addresstype, $extendedsql1, $extendedsql2,$extendedsql3,$extendedsql4,$extendedsql5,$postcodeprefix,$pathmap);
                $LabelsSQL = $SQLModel->getLabelItems_DeliverycostsSQL($prefix,$fromdate,$todate, $addresstype, $extendedsql1, $extendedsql2, $extendedsql3, $extendedsql4,$extendedsql5,$postcodeprefix,$pathmap);
                $row_aa_header = "Qty";
                $row_bb_header = "Value";
                $h1 = "Delivery Costs Report";
                $label_aa_header = "Products";
                $label_bb_header = "Qtys";
                $label_h1 = "Deliverys";
                //$currencyA = Mage::app()->getLocale()->currency(Mage::app()->getStore()->getCurrentCurrencyCode())->getSymbol();
                $currencyB = Mage::app()->getLocale()->currency(Mage::app()->getStore()->getCurrentCurrencyCode())->getSymbol();
                break;
                 
                //Quotes
            case ($maptype == "quotes"):
    
                $DataSQL = $SQLModel->getQuotes($prefix,$fromdate,$todate, $extendedsql1, $extendedsql2,$extendedsql3,$extendedsql4,$extendedsql5,$postcodeprefix,$pathmap);
                $LabelsSQL = $SQLModel->getLabelItems_QuotesSQL($prefix,$fromdate,$todate, $addresstype, $extendedsql1, $extendedsql2, $extendedsql3, $extendedsql4,$extendedsql5,$postcodeprefix,$pathmap);
                $row_aa_header = "Qty";
                $row_bb_header = "Type";
                $h1 = "Quotes Report";
                $label_aa_header = "Products";
                $label_bb_header = "Qtys";
                $label_h1 = "Quotes";
                break;
                
                //P-L
            case ($maptype == "p-l"):
            
                $DataSQL = $SQLModel->getPl($prefix,$fromdate,$todate,$pathmap);
                $row_aa_header = "";
                $row_bb_header = "";
                $h1 = "";
                $label_aa_header = "";
                $label_bb_header = "";
                $label_h1 = "";
                break;
                     
                //Telorders
            case ($maptype == "telorders"):
                 
                $DataSQL = $SQLModel->getTelorders($prefix,$fromdate,$todate,$pathmap);
                $row_aa_header = "";
                $row_bb_header = "";
                $h1 = "";
                $label_aa_header = "";
                $label_bb_header = "";
                $label_h1 = "";
                break;
                 
                //Spend
            case ($maptype == "spend"):
    
                $DataSQL = $SQLModel->getSpend($prefix,$fromdate,$todate,$pathmap);
                $row_aa_header = "";
                $row_bb_header = "";
                $h1 = "";
                $label_aa_header = "";
                $label_bb_header = "";
                $label_h1 = "";
                break;
                
                //default
            case ($maptype == "-1"):
                $DataSQL = $SQLModel->getOrders($prefix,"", "", $addresstype, $extendedsql1, $extendedsql2,$extendedsql3,$extendedsql4,$extendedsql5,$postcodeprefix,$pathmap);
                $LabelsSQL = $SQLModel->getLabelItems_OrdersSQL($prefix,"","", $addresstype, $extendedsql1, $extendedsql2, $extendedsql3, $extendedsql4,$extendedsql5,$postcodeprefix,$pathmap);
                $row_aa_header = "Orders";
                $row_bb_header = "Units";
                $h1 = "Orders Report";
                $label_aa_header = "Products";
                $label_bb_header = "Qty";
                $label_h1 = "Orders";
                break;     
        }
    
        $SwitchSQL=array(
            'DataSQL'=>$DataSQL,
            'LabelsSQL' => $LabelsSQL,
            'row_aa_header'=>$row_aa_header,
            'row_bb_header'=>$row_bb_header,
            'h1'=>$h1,
            'label_aa_header'=>$label_aa_header,
            'label_bb_header'=>$label_bb_header,
            'label_h1'=>$label_h1,
            'currencyA'=>$currencyA,
            'currencyB'=>$currencyB
            
        );
        
        
        return $SwitchSQL;
} 
public function getMaxValue($results) {
  $total_result_count = count($results);
  $position = 0;
    usort($results, function($max_a, $max_b) {
        return $max_b['aa'] - $max_a['aa'];
    });
    $maxarray=array();
    array_push($maxarray,array_slice($results, 0, 1));
return $maxarray[0][0]['aa'];
}
public function getReadmodel(){
    $Readmodel = Mage::getModel('reportmapper_salesreportmaps/adminhtml_sqls')->getRead();
    return $Readmodel;
}
public function getLicenceValidation(){
    $access_key = Mage::getStoreConfig('user_data/authorisation/access_key');
    $options = array(CURLOPT_HEADER =>false,'location' => 'http://www.reportmapper.com/validate_rm.php',
        'uri' => 'http://www.reportmapper.com');
    //create an instante of the SOAPClient (the API will be available)
    //die($access_key);
    $api = new SoapClient(NULL, $options);
    //call an API method
    $cururl = Mage::getUrl('admin');
    return $api->validate($cururl,$access_key);
}
public function getValid(){
    $access_key = Mage::getStoreConfig('user_data/authorisation/access_key');
    $options = array(CURLOPT_HEADER =>false,'location' => 'http://www.reportmapper.com/validate_rm.php',
        'uri' => 'http://www.reportmapper.com');
    //create an instante of the SOAPClient (the API will be available)
    //die($access_key);
    $api = new SoapClient(NULL, $options);
    //call an API method
    $cururl = Mage::getUrl('admin');
    return $api->isvalid($cururl,$access_key);
}

public function getHighlightColour(){
    $maptype = $this->getMapType();
    switch ($maptype) {
    
        //Orders
        case ($maptype == "orders"):
            $defaultHighlightColour = "#FFC219";
            break;
             
            //Units
        case ($maptype == "units"):
            $defaultHighlightColour = "#66CCFF";
            break;
             
            //Values
        case ($maptype == "values"):
            $defaultHighlightColour = "#F1FF26";
            break;
    
            //Refunds
        case ($maptype == "refunds"):
            $defaultHighlightColour = "#BABABA";
            break;
             
            //Customers
        case ($maptype == "customers"):
            $defaultHighlightColour = "#FFF069";
            break;
             
            //Delivery Costs
        case ($maptype == "deliverycost"):
            $defaultHighlightColour = "#CCCCCC";
            break;
             
            //Quotes
        case ($maptype == "quotes"):
            $defaultHighlightColour = "#CCCCCC";
            break;
    
            //P-L
        case ($maptype == "p-l"):
            $defaultHighlightColour = "#CCCCCC";
            break;
             
            //Telorders
        case ($maptype == "telorders"):
            $defaultHighlightColour = "#CCCCCC";
            break;
             
            //Spend
        case ($maptype == "spend"):
            $defaultHighlightColour = "#CCCCCC";
            break;
    }
    
    
    $LastMapType = Mage::getSingleton('core/session')->getMapType();
    
    if($LastMapType != $maptype){
        $highlight_colour = $defaultHighlightColour;
    }else{
        $highlight_colour = $this->getRequest()->getParam('highlight_colour',$defaultHighlightColour);
    }
    
    return $highlight_colour;
}
public function getMapColour(){
    $maptype = $this->getMapType();
    switch ($maptype) {
    
        //Orders
        case ($maptype == "orders"):
            $defaultMapColour = "#1F31AB";
            break;
             
            //Units
        case ($maptype == "units"):
            $defaultMapColour = "#1BAB4B";
            break;
             
            //Values
        case ($maptype == "values"):
            $defaultMapColour = "#0A4D4A";
            break;
    
            //Refunds
        case ($maptype == "refunds"):
            $defaultMapColour = "#AB0A0A";
            break;
             
            //Customers
        case ($maptype == "customers"):
            $defaultMapColour = "#FF7417";
            break;
             
            //Delivery Costs
        case ($maptype == "deliverycost"):
            $defaultMapColour = "#000000";
            break;
             
            //Quotes
        case ($maptype == "quotes"):
            $defaultMapColour = "#000000";
            break;
    
            //P-L
        case ($maptype == "p-l"):
            $defaultMapColour = "#000000";
            break;
             
            //Telorders
        case ($maptype == "telorders"):
            $defaultMapColour = "#000000";
            break;
             
            //Spend
        case ($maptype == "spend"):
            $defaultMapColour = "#000000";
            break;
    }
    $LastMapType = Mage::getSingleton('core/session')->getMapType();
    
    if($LastMapType != $maptype){
        $map_colour = $defaultMapColour;
    }else{
        $map_colour = $this->getRequest()->getParam('map_colour',$defaultMapColour);
    }
    return $map_colour;
}
public function getStoreIds(){
    $store_ids = $this->getRequest()->getParam('store_ids','0');
    return $store_ids;
}
public function getFromDate(){
    global $reqfromdate;
    
    
    $date = new Zend_Date(Mage::getModel('core/date')->timestamp());
    $default_from_date = Mage::getModel('core/date')->date('d/m/Y', strtotime($date." -3 year"));
    $reqfromdate = $this->getRequest()->getParam('fromdate',$default_from_date);
    $fromdate = str_replace('/', '-', $reqfromdate);
    $fromdate = date('Y-m-d', strtotime($fromdate)).' 00:00:00';
    return $fromdate;
}
public function getToDate(){
    global $reqtodate;
    $date = new Zend_Date(Mage::getModel('core/date')->timestamp());
    $default_to_date = Mage::getModel('core/date')->date('d/m/Y', strtotime($date." -1 month"));
    //$today_date = Mage::getModel('core/date')->gmtDate('d/m/Y');
    $reqtodate = $this->getRequest()->getParam('todate',$default_to_date);
    //$reqtodate = $this->getRequest()->getParam('todate',strtotime($today_date);
    $todate = str_replace('/', '-', $reqtodate);
    $todate = date('Y-m-d', strtotime($todate)).' 23:59:59';
    return $todate;
}
public function getAddressType(){
    $addresstype = $this->getRequest()->getParam('addresstype','billing');
    return $addresstype;
}
public function getMapType(){
    $maptype = $this->getRequest()->getParam('maptype','-1');
    return $maptype;
}
public function getMapData() {
    global $row_aa_header,$row_bb_header,$h1;
    
    $read = Mage::getSingleton('core/resource')->getConnection('core_read');
    $write = Mage::getSingleton('core/resource')->getConnection('core_write');
    $prefix = Mage::getConfig()->getTablePrefix();
    $fromdate = $this->getFromDate();
    $todate = $this->getToDate();
    $addresstype = $this->getAddressType();
    $maptype = $this->getMapType();
    $extendedsql1 = '';
    $extendedsql2 = '';
    $postcodeprefix = '';
    $pathmap = 'UK';
    
    $productsreq = $this->getSelectedProducts();
    $products = implode(",",$productsreq);
    $allproducts = strpos($products,'*');

    if($allproducts === false){
        $extendedsql1 = 'AND '.$prefix.'sales_flat_order_item.product_id IN ('.$products.')';
        $extendedsq13 = 'AND '.$prefix.'sales_flat_creditmemo_item.product_id IN ('.$products.')';
    }
    
    $storeId = $this->getStoreIds();
   // die('storeids ='.$allstores);
    if($storeId !=0){
        $extendedsql2 = 'AND '.$prefix.'sales_flat_order.store_id IN ('.$storeId.')';
        $extendedsql4 = 'AND '.$prefix.'customer_entity.store_id IN ('.$storeId.')';
    }
    
    $coupon_rule_codesreq = $this->getSelectedCouponRuleCodes();
    $coupon_rule_codes_imploded = implode("','",$coupon_rule_codesreq);
    
    if(!in_array("*",$coupon_rule_codesreq)){
        //$extendedsql5 = 'AND '.$prefix.'sales_flat_order.coupon_rule_name LIKE "'.$coupon_rule_names.'"';
        //$extendedsql5 = 'AND '.$prefix.'sales_flat_order.coupon_code = "'.$coupon_rule_names.'"';
        $extendedsql5 = "AND ".$prefix."sales_flat_order.coupon_code IN ('".$coupon_rule_codes_imploded."')";
    }
    
    $DataSQL = $this->getMapSwitch($maptype,$prefix,$fromdate,$todate,$addresstype,$extendedsql1,$extendedsql2,$extendedsq13,$extendedsql4,$extendedsql5,$postcodeprefix,$pathmap);
    

    $row_aa_header = $DataSQL['row_aa_header'];
    $row_bb_header = $DataSQL['row_bb_header'];
    $h1 = $DataSQL['h1'];
    $result = $read->fetchAll($DataSQL['DataSQL']);
    
    return $result;
}
public function getSelectedProducts()
{
    $selected_products = $this->getRequest()->getParam('products',array('*'));
    return $selected_products;
}
public function getSelectedProductNames($productIds)
{
    if(in_array("*",$productIds)){
        $selected_product_names = "No Filter";
    } else {
    $products = Mage::getModel('catalog/product')->getCollection();
    $products->addAttributeToFilter('entity_id', array('in' => $productIds));
    $products->addAttributeToSelect(array('name'));
    
   $selected_product_names = array();
    foreach($products as $product)
    {
        array_push($selected_product_names, $product->getName());
    }
    $selected_product_names = implode(", ",$selected_product_names);
    }
    return $selected_product_names;
}
public function getSelectedCouponRuleCodes()
{
    $coupon_rule_codes = $this->getRequest()->getParam('coupon_rule_name',array('*'));
    return $coupon_rule_codes;
}
public function getSelectedCouponRuleNames($ruleIds)
{
    if(in_array("*",$ruleIds)){
        $selected_rule_names = "No Filter";
    } else {
        //$ruleIds = '"'.implode('","',$ruleIds).'"';
        $rules = Mage::getModel('salesrule/rule')->getCollection();
       // $rules->addAttributeToFilter('`code`', array('in' => $ruleIds));
       //$rules->addAttributeToSelect(array('name'));

        $selected_rule_names = array();
        foreach($rules as $rule)
        {
			if(in_array($rule->getCode(),$ruleIds)){
            array_push($selected_rule_names, $rule->getName());
			}
        }
        $selected_rule_names = implode(", ",$selected_rule_names);
    }
    return $selected_rule_names;
}
public function getAllOrdersCount($fromDate,$toDate){
    
    $fromDate = strtotime($fromDate);
    $toDate = strtotime($toDate);
    $collection = Mage::getResourceModel('sales/order_item_collection')->addAttributeToSelect('qty_ordered');
    $collection->addAttributeToFilter('created_at', array('gteq' =>date("Y-m-d",$fromDate)));
    $collection->addAttributeToFilter('created_at', array('lteq' => date("Y-m-d",$toDate)));
    
   return $collection->count();//$collection->setOrder('qty_ordered', 'DESC')->getFirstItem();
}
public function getSelectedAllOrdersRelative(){
    $AllOrdersRelative = $this->getRequest()->getParam('all_orders_relative',array(''));

    if(empty($AllOrdersRelative)){
        return "1";
    } else{
        return "0";
    }

}
public function getSVG() {
    global $h1, $reqfromdate,$reqtodate,$allOrdersCount,$rm_footer;
   $allOrdersCount = $this->getAllOrdersCount($reqfromdate,$reqtodate);
   $ukmap = $this->getUkmap($allOrdersCount);
   $ukmaplabels = $this->getUKmapLabeling();
   $maptype = $this->getMapType();
   $store_names = $this->getStoreNames();
   $product_names = $this->getSelectedProductNames($this->getSelectedProducts());
   $ruleIds = $this->getSelectedCouponRuleCodes();
   $ruleNames = $this->getSelectedCouponRuleNames($ruleIds);


    $req = $this->getRequest()->getParam('maptype','-1');
    $rm_header = <<<RMHEADER
<h1>ReportMapper: Select your report options then click 'create report'.</h1>
RMHEADER;

if($req != '-1'){
    $rm_header = <<<RMHEADER
<h1>ReportMapper: $h1 From: $reqfromdate To: $reqtodate <input value="Print Report" class="map_form_button submit required-entry" type="submit" onClick="window.print()"></h1>
RMHEADER;
}

   $rm_footer = <<<RMFOOTER
<p class="rm_footer">Stores: $store_names | Products: $product_names | Promotion: $ruleNames.</p>
RMFOOTER;
   //echo $allOrdersCount;
$svgdoc = <<<SVGDOC
$rm_header
<svg id="svg-id" xmlns="http://www.w3.org/2000/svg" version="1.1" width="750" height="905" style="width: 750px; height: 905px; border:1px solid #ccc; background:lightblue; overflow: hidden;" transform="translate(0,0) scale(1.00)" viewBox="0 0 750 905" viewPort="0 0 750 905">
    <g transform="translate(400,4235) scale(0.011)">
    $ukmap
    </g>
    <g class="labels" style="opacity:0.9; fill:#000000; font-family:Arial;" transform="scale(1.094) translate(63,16) ">
    $ukmaplabels
    </g>
    </svg>
    <div class="clearfix"></div>
<div class="page-break"> </div>
  
SVGDOC;

    if($this->getValid()){
        return $svgdoc;
    }
}
public function getLabelItems($postcodeprefix) {
    global $label_aa_header,$label_bb_header,$label_h1;

    $read = Mage::getSingleton('core/resource')->getConnection('core_read');
    $prefix = Mage::getConfig()->getTablePrefix();
    $fromdate = $this->getFromDate();
    $todate = $this->getToDate();
    $addresstype = $this->getAddressType();
    $maptype = $this->getMapType();
    $extendedsql1 = '';
    $extendedsql2 = '';
    $pathmap = 'UK';
    $productsreq = $this->getSelectedProducts();
    $products = implode(",",$productsreq);
    $allproducts = strpos($products,'*');

    if($allproducts === false){
        $extendedsql1 = 'AND '.$prefix.'sales_flat_order_item.product_id IN ('.$products.')';
        $extendedsq13 = 'AND '.$prefix.'sales_flat_creditmemo_item.product_id IN ('.$products.')';
    }
    
    $storeId = $this->getStoreIds();
    if($storeId !=0){
        $extendedsql2 = 'AND '.$prefix.'sales_flat_order.store_id IN ('.$storeId.')';
        $extendedsql4 = 'AND '.$prefix.'customer_entity.store_id IN ('.$storeId.')';
    }
    
    $coupon_rule_codesreq = $this->getSelectedCouponRuleCodes();
    $coupon_rule_codes_imploded = implode("','",$coupon_rule_codesreq);
    if(!in_array("*",$coupon_rule_codesreq)){
        //$extendedsql5 = 'AND '.$prefix.'sales_flat_order.coupon_rule_name LIKE "'.$coupon_rule_names.'"';
        //$extendedsql5 = 'AND '.$prefix.'sales_flat_order.coupon_code = "'.$coupon_rule_names.'"';
        $extendedsql5 = "AND ".$prefix."sales_flat_order.coupon_code IN ('".$coupon_rule_codes_imploded."')";
    }
    
    $DataSQL = $this->getMapSwitch($maptype,$prefix,$fromdate,$todate,$addresstype,$extendedsql1,$extendedsql2,$extendedsq13,$extendedsql4,$extendedsql5,$postcodeprefix,$pathmap);
    $label_aa_header = $DataSQL['label_aa_header'];
    $label_bb_header = $DataSQL['label_bb_header'];
    $label_h1 = $DataSQL['label_h1'];
    $result = $read->fetchAll($DataSQL['LabelsSQL']);
    return $result;
}
public function getTables() {
    global $row_aa_header,$row_bb_header,$currencyA, $currencyB, $allOrdersCount,$rm_footer;
    $results = $this->getMapData();

    $pths = $this->pcds;
    $results = $this->mergePcdsTodata($pths ,$results);

    $max_aa = $this->getMaxValue($results);

    $AllOrdersRelative = $this->getSelectedAllOrdersRelative();
    if($AllOrdersRelative == 1){
        $max_aa = $allOrdersCount;
    }
    //var_dump($allOrdersCount);
    $datastring = <<<DATASTRING
    <div class="postcode_outer_div">
    <div class="postcode_div">
    <div class="row_col_header">Strength</div>
    <div class="row_area_header">Area</div>
    <div class="row_a_header">$row_aa_header</div>
    <div class="row_b_header">$row_bb_header</div>
DATASTRING;
    $i = 0;
    $map_colour = $this->getMapColour();
    $highlight_colour = $this->getHighlightColour();
    foreach($results as $r)
    {
    if (0 == $i % 2){
        $rowcolour = "row_div_odd";
    }else{
        $rowcolour = "row_div_even";
    }
        $postcode = strtoupper($r['postcode']);
        $aa = $r['aa'];
        $bb = round($r['bb'],2);
        if($aa ==0){
            $aa = null;
        }
        $colour_results = $this->getOpaqueness($aa,$max_aa);
        $opacitycss=$colour_results['opacitycss'];

        $aa=$colour_results['aa'];

        $datastring .= <<<DATASTRING
        <div class="$rowcolour" id="divrow_$postcode" onmouseover="ChangeBackgroundColor(this,'$highlight_colour')" onmouseout="RestoreBackgroundColor(this,'$map_colour','$rowcolour')">
        <div class="bgcol" style="background-color:$map_colour !important;"><div style="opacity:$opacitycss; background:white !important; width:50px; height:25px; margin:0px;"></div></div>
        <div class="row_area">$postcode</div>
        <div class="row_a">$currencyA$aa</div>
        <div class="row_b">$currencyB$bb</div>
        </div>
DATASTRING;
        $i++;
        if($i  % 34 == 0){
        
            $datastring .= <<<DATASTRING
</div>
 <div class="postcode_div" width="200px">
 <div class="row_div">
 <div class="row_col_header">Strength</div>
 <div class="row_area_header">Area</div>
 <div class="row_a_header">$row_aa_header</div>
 <div class="row_b_header">$row_bb_header</div>
 </div>
DATASTRING;
        
            $i = 0;
        }
        
    }
    
$datastring .= <<<DATASTRING
 </div>
 </div>$rm_footer
DATASTRING;
$maptype = $this->getMapType();
Mage::getSingleton('core/session')->setMapType($maptype);
    if($this->getValid()) {
        return $datastring;
    }
}
public function getUkmap($allOrdersCount)
        {
            $map_colour = $this->getMapColour();
            $highlight_colour = $this->getHighlightColour();
            $results = $this->getMapData();
            $pths = $this->pths;
            $results = $this->mergePathsTodata($pths ,$results);
            $max_aa = $this->getMaxValue($results);
            $AllOrdersRelative = $this->getSelectedAllOrdersRelative();
            if($AllOrdersRelative == 1){
                $max_aa = $allOrdersCount;
            }
            $mapstring = <<<MAP
            
MAP;
            
            foreach($results as $r)
            {
                
                $path = $r['path'];
                $pc = strtoupper($r['pc']);
                $postcode = strtoupper($r['postcode']);
                $aa = $r['aa'];
                $colour_results = $this->getOpaqueness($aa,$max_aa);
            
                $opacity=$colour_results['opacity'];
                $aa=$colour_results['aa'];
                $mapstring .= <<<MAP
                <path d="$path" fill="#FFF"/>
                <path d="$path" id="$postcode" fill="$map_colour" fill-opacity="$opacity" stroke="#000000" stroke-width="20" stroke-opacity="1" fill-rule="evenodd" name_kml="$postcode" onmouseover="show(evt, 'label_$postcode','$highlight_colour')" onmouseout="hide(evt, 'label_$postcode','$map_colour')"/>
MAP;
            
                $data = $mapstring;
            }
             return $data;
        }
public function getUKmapLabeling(){
    $map_lables = $this->getTxtPths();
    foreach ($map_lables as $lable) {
        $map_lables .= '<text visibility="visible" class="'.$lable['postcode'].'" transform="matrix('.$lable['text_path'].')" font-size="'.$lable['text_size'].'">'.$lable['postcode'].'</text>';
    }
    return $map_lables;
}
public function getIremap()
        {
            
    $read = Mage::getSingleton('core/resource')->getConnection('core_read');
    $write = Mage::getSingleton('core/resource')->getConnection('core_write');
    $prefix = Mage::getConfig()->getTablePrefix();
    
    $results = $read->fetchAll('SELECT * FROM '.$prefix.'map_paths WHERE pathmap = "IRE"');
   // $aa="50";
   // $max_aa = $this->getMaxValue($results);
   // $colour_results = $this->getOpaqueness($aa,$max_aa);
    $map_colour = "#000000"; //$this->getMapColour();
    $opacity = "1";

        
            $Iremapstring = <<<MAP
        
MAP;
        
            foreach($results as $r)
            {
        
                $path = $r['path'];
                $pc = strtoupper($r['pc']);
                $postcode = strtoupper($r['postcode']);
                $aa = $r['aa'];
                $colour_results = $this->getOpaqueness($aa);
        
                $opacity=$colour_results['opacity'];
                $aa=$colour_results['aa'];
        
                $Iremapstring .= <<<MAP
  <path d="$path" fill="#FFF"/>
  <path d="$path" id="$postcode" fill="#FFFFFF" fill-opacity="1" stroke="#000000" stroke-width="20" stroke-opacity="1" fill-rule="evenodd" name_kml="$postcode" onmouseover="show(evt, 'label_$postcode')" onmouseout="hide(evt, 'label_$postcode')"/>
MAP;
            }
            
            return $Iremapstring;
        }
public function getLabels()
        {
    global $label_aa_header, $label_bb_header, $label_h1,$currencyA,$currencyB;
$labelstring = <<<LAB
            
LAB;
$results = $this->getMapData();
            
     
            foreach($results as $r)
            {
               $pc = strtoupper($r['pc']);
$labelstring.= <<<LAB

      <div id="label_$pc" class="label_pc">
        <ul class="salesdata">
        <li><h1>'$pc' $label_h1</h1></li>
        <li><div><div class="productlabel">$label_aa_header</div><div class="aalabel">$label_bb_header</div></div></li>
LAB;
               
$LabelItems = $this->getLabelItems($pc);
$i = 0;
foreach($LabelItems as $bstlr)
{
   
    $bstlrpname = $bstlr['label_aa'];
    $bstlraa = $bstlr['label_bb'];
    
    if (0 == $i % 2){
        $rowcolour = "resultrowodd";
    }else{
        $rowcolour = "resultroweven";
    }
    $i++;
    
    $labelstring .= <<<LABEL
<li><div class="$rowcolour"><div class="product">$bstlrpname</div><div class="aa">$currencyA$bstlraa</div></div></li>
LABEL;
}


$labelstring.= <<<LAB
        </ul>
      </div>

               
LAB;
 
            }
            
        return $labelstring;
}
public function getOpaqueness($aa,$max) {
    
        
        $percentage= round(($aa / $max) * 100,2);
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
        
         if((!isset($aa)) OR (is_null($aa)) OR ($aa ==0)){
            $opacity = 0;
            $opacitycss = 1;
            $aa = 0;
        }
        
        $colours_array=array(
            'opacity'=>$opacity,
            'opacitycss'=>$opacitycss,
            'aa'=>$aa
        );
        return $colours_array;
        
}
public function color_inverse($color){
    $color = str_replace('#', '', $color);
    if (strlen($color) != 6){ return '000000'; }
    $rgb = '';
    for ($x=0;$x<3;$x++){
        $c = 255 - hexdec(substr($color,(2*$x),2));
        $c = ($c < 0) ? 0 : dechex($c);
        $rgb .= (strlen($c) < 2) ? '0'.$c : $c;
    }
    return '#'.$rgb;
}
public function oppColour($c, $inverse=false){
    if(strlen($c)== 3)
    { // short-hand
        $c = $c{0}.$c{0}.$c{1}.$c{1}.$c{2}.$c{2};
    }
    if ($inverse)
    { // => Inverse Colour
        $r = (strlen($r=dechex(255-hexdec($c{0}.$c{1})))<2)?'0'.$r:$r;
        $g = (strlen($g=dechex(255-hexdec($c{2}.$c{3})))<2)?'0'.$g:$g;
        $b = (strlen($b=dechex(255-hexdec($c{4}.$c{5})))<2)?'0'.$b:$b;
        return $r.$g.$b;
    } else
    { // => Monotone based on darkness of original
        return array_sum(array_map('hexdec', str_split($c, 2))) > 255*1.5 ? '000000' : 'FFFFFF';
    }
}
}