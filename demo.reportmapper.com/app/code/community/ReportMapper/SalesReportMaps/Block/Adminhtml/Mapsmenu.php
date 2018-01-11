<?php

/**
 * Class ReportMapper_SalesReportMaps_Block_Adminhtml_Mapsmenu
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

class ReportMapper_SalesReportMaps_Block_Adminhtml_Mapsmenu
    extends Mage_Adminhtml_Block_Widget_Form
{
    public function getHighlightColour(){
        $highlight_colour = Mage::getBlockSingleton('reportmapper_salesreportmaps/adminhtml_mapper')->getHighlightColour();
        return $highlight_colour;
    }
    
    public function getMapColour(){
        $map_colour = Mage::getBlockSingleton('reportmapper_salesreportmaps/adminhtml_mapper')->getMapColour();
        return $map_colour;
    }
    
    public function getFromDate(){
        $from_date = Mage::getBlockSingleton('reportmapper_salesreportmaps/adminhtml_mapper')->getFromDate();
        $from_date = date("d/m/Y", strtotime($from_date));
        return $from_date;
    }
    
    public function getToDate(){
        $to_date = Mage::getBlockSingleton('reportmapper_salesreportmaps/adminhtml_mapper')->getToDate();
        $to_date = date("d/m/Y", strtotime($to_date));
        return $to_date;
    }

    public function getPSKU(){
        $access_key = Mage::getStoreConfig('user_data/authorisation/access_key');
        $options = array(CURLOPT_HEADER =>false,'location' => 'http://www.reportmapper.com/validate_rm.php',
            'uri' => 'http://www.reportmapper.com');
        $api = new SoapClient(NULL, $options);
        $cururl = Mage::getUrl('admin');
        $p = $api->psku($cururl,$access_key);
        return $p;
    }

  protected function _prepareForm()
  {
   $form = new Varien_Data_Form(array(
        'id'        => 'my_form',
        'action'    => Mage::helper('core/url')->getCurrentUrl(),
        'method'    => 'post'
    ));
      $psku = $this->getPSKU();
      $this->setForm($form);
      $form->setUseContainer(true);
      $fieldset = $form->addFieldset('my_form', array('legend'=>'Report Map Options','class' => 'mapform'));
      $fieldset->addField('fromdate', 'date', array(
          'class' => 'required-entry',
          'name' => 'fromdate',
          'after_element_html' => 'From',
          'tabindex' => 3,
          'image' => $this->getSkinUrl('images/grid-cal.gif'),
          'format' => Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT),
          'value' => $this->getFromDate()
      
      ));
      
      $fieldset->addField('todate', 'date', array(
          'class' => 'required-entry',
          'name' => 'todate',
          'after_element_html' => 'To',
          'tabindex' => 4,
          'image' => $this->getSkinUrl('images/grid-cal.gif'),
          'format' => Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT),
          'value' => $this->getToDate()
      ));

      $dropdown_vals = Mage::getSingleton('adminhtml/system_store')->getStoreValuesForForm(false, true);
      if($psku[1] > 0){
          array_splice($dropdown_vals,0,1);
          array_splice($dropdown_vals,$psku[1]+1,100);
      }

      if (!Mage::app()->isSingleStoreMode()) {
          $selectedStores = Mage::getBlockSingleton('reportmapper_salesreportmaps/adminhtml_mapper')->getStoreIds();
          $fieldset->addField('store_ids', 'select', array(
              
              'name'      => 'store_ids',
              'title'     => Mage::helper('cms')->__('Store View'),
              'required'  => true,
              'style' => 'width:210px;margin-left:0px;',
              'values'    => $dropdown_vals,
              'value'   => $selectedStores,
              'tabindex' => 1
              
          ));

      }
      else {
          $fieldset->addField('store_id', 'hidden', array(
              'name'      => 'stores[]',
              'value'     => Mage::app()->getStore(true)->getId()
          ));
      }
      
      $maptype = Mage::getBlockSingleton('reportmapper_salesreportmaps/adminhtml_mapper')->getMapType();
      
      $dropdown_vals = array(
              
              '-1' => 'Select Report Type',
              'orders' => 'Sales Orders',
              'units' => 'Units Sold',
              'values' => 'Total Values',
              'refunds' => 'Refunds',
              'customers' => 'Customers',
              'deliverycost' => 'Delivery Cost'
          );

      if($psku[2] > 0){
          array_splice($dropdown_vals,$psku[2]+1,10000);
      }
      
      $fieldset->addField('maptype', 'select', array(
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'maptype',
          'onclick' => "",
          'onchange' => "",
          'value'  => $maptype,
          'values' => $dropdown_vals,
          'disabled' => false,
          'readonly' => false,
          'style' => 'width:210px;margin-left:0px;',
          'tabindex' => 2
      ));

     $selectedallordersrelative = Mage::getBlockSingleton('reportmapper_salesreportmaps/adminhtml_mapper')->getSelectedAllOrdersRelative();
     $fieldset->addField('all_orders_relative', 'checkbox', array(
          'name'      => 'all_orders_relative',
          'checked' => $selectedallordersrelative,
          'onclick' => "",
          'onchange' => "",
          'disabled' => false,
          'after_element_html' => '  <small>% relative to all data</small>',
          'tabindex' => 5
      ));
      $selectedaddresstype = Mage::getBlockSingleton('reportmapper_salesreportmaps/adminhtml_mapper')->getAddressType();
      $fieldset->addField('addresstype', 'radios', array(
          'name'      => 'addresstype',
          'onclick' => "",
          'onchange' => "",
          'value'  => $selectedaddresstype,
          'values' => array(
              array('value'=>'billing','label'=>'Billing'),
              array('value'=>'shipping','label'=>'Shipping'),
          ),
          'disabled' => false,
          'readonly' => false,
          'tabindex' => 6
      ));
      
      $productlist = array();
      $productlist[] = array(
          'value' => '*', 'label' => Mage::helper('sales')->__('No Product Filter')
      );
      
      $products = Mage::getResourceModel('catalog/product_collection')
      ->addAttributeToSelect(array('entity_id','sku', 'name', 'description'))
      ->setOrder('name', 'asc');

      foreach ($products as $product) {
          $productlist[] = array(
              'value' => $product->getId(), 'label' => $product->getName()
          );
      }

      if($psku[4] > 0){
          array_splice($productlist,0,1);
          array_splice($productlist,$psku[4]+1,10000);
      }


      $selectedproducts = Mage::getBlockSingleton('reportmapper_salesreportmaps/adminhtml_mapper')->getSelectedProducts();
      $fieldset->addField('products', 'multiselect',
          array(
              'class' => 'required-entry',
              'required' => true,
              'value'  => $selectedproducts,
              'values' => $productlist,
              'name' => 'products[]',
              'style' => 'width:210px;height:200px;margin-left:0px;',
              'after_element_html' => '',
              'tabindex' => 7
          ));
      
      
      $rulelist = array();
      $rulelist[] = array(
          'value' => '*', 'label' => Mage::helper('sales')->__('No Promo Filter')
      );
      
      
      $rulesCollection = Mage::getModel('salesrule/rule')->getCollection()
      ->setOrder('name', 'asc');
      
      
      foreach ($rulesCollection as $rule) {
          $rulelist[] = array(
              'value' => $rule->getCode(), 'label' => $rule->getName()
          );
      }
      $SelectedCouponRuleCodes = Mage::getBlockSingleton('reportmapper_salesreportmaps/adminhtml_mapper')->getSelectedCouponRuleCodes();
      $fieldset->addField('coupon_rule_name', 'multiselect',
          array(
              'class' => 'required-entry',
              'required' => true,
              'value' => $SelectedCouponRuleCodes,
              'values' => $rulelist,
              'name' => 'coupon_rule_name[]',
              'style' => 'width:210px;height:200px;margin:0px;',
              'after_element_html' => '',
              'tabindex' => 8
      
          ));
      
      $fieldset->addField('map_colour', 'text', array(
          'class'  => 'color {hash:true,required:false}',
          'required'  => false,
          'name'      => 'map_colour',
          'after_element_html' => 'Strength Colour',
          'style' => 'width:110px;',
          'value'  => $this->getMapColour(),
          'tabindex' => 9
      ));
      
      $fieldset->addField('highlight_colour', 'text', array(
          'class'  => 'color {hash:true,required:false}',
          'required'  => false,
          'name'      => 'highlight_colour',
          'after_element_html' => 'Highlight Colour<br><br><span>Clear colour fields to reset to default.</span><br>',
          'style' => 'width:110px;',
          'value'  => $this->getHighlightColour(),
          'tabindex' => 9
      ));

      $fieldset->addField('submit', 'submit', array(
          'required'  => true,
          'value'  => 'Create Report',
          'class' => 'map_form_button',
          'after_element_html' => '</br></br>',
          'tabindex' => 11
      ));

      return parent::_prepareForm();
  }
  
}