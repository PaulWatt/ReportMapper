<?php

/**
 * Class ReportMapper_SalesReportMaps_Block_Adminhtml_Mapsmenu
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

class ReportMapper_SalesReportMaps_Block_Adminhtml_Mapsmenu extends Mage_Adminhtml_Block_Widget_Form
{
    public function getFromDate()
    {
        $fromDate = Mage::getBlockSingleton('reportmapper_salesreportmaps/adminhtml_mapper')->getFromDate();
        $fromDate = date("d/m/Y", strtotime($fromDate));
        return $fromDate;
    }
    
    public function getToDate()
    {
        $toDate = Mage::getBlockSingleton('reportmapper_salesreportmaps/adminhtml_mapper')->getToDate();
        $toDate = date("d/m/Y", strtotime($toDate));
        return $toDate;
    }

    public function getPSKU()
    {
        $htmlMpr = Mage::getBlockSingleton('reportmapper_salesreportmaps/adminhtml_mapper');
        $accessKey = Mage::getStoreConfig('user_data/authorisation/access_key');
        $curURL = Mage::getUrl('admin');
        $p = $htmlMpr->getPSKU($curURL, $accessKey);
        return $p;
    }

  protected function _prepareForm()
  {
      $htmlMpr = Mage::getBlockSingleton('reportmapper_salesreportmaps/adminhtml_mapper');
      $form = new Varien_Data_Form(
          array(
              'id' => 'my_form',
              'action' => Mage::helper('core/url')->getCurrentUrl(),
              'method' => 'post'
          )
      );
      $psku = $this->getPSKU();
      $this->setForm($form);
      $form->setUseContainer(true);
      $fieldset = $form->addFieldset('my_form', array('legend'=>'Report Map Options','class' => 'mapform'));
      $fieldset->addField(
          'fromdate',
          'date',
          array(
          'class' => 'required-entry',
          'name' => 'fromdate',
          'after_element_html' => 'From',
          'tabindex' => 3,
          'image' => $this->getSkinUrl('images/grid-cal.gif'),
          'format' => Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT),
          'value' => $this->getFromDate()
          )
      );

      $fieldset->addField(
          'todate',
          'date',
          array(
          'class' => 'required-entry',
          'name' => 'todate',
          'after_element_html' => 'To',
          'tabindex' => 4,
          'image' => $this->getSkinUrl('images/grid-cal.gif'),
          'format' => Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT),
          'value' => $this->getToDate()
          )
      );

      $dropdownVals = Mage::getSingleton('adminhtml/system_store')->getStoreValuesForForm(false, true);
      if ($psku[1] > 0) {
          array_splice($dropdownVals, 0, 1);
          array_splice($dropdownVals, $psku[1]+1, 100);
      }

      if (!Mage::app()->isSingleStoreMode()) {
          $selectedStores = Mage::getBlockSingleton('reportmapper_salesreportmaps/adminhtml_mapper')->getStoreIds();
          $fieldset->addField(
              'store_ids',
              'select',
              array(

              'name'      => 'store_ids',
              'title'     => Mage::helper('cms')->__('Store View'),
              'required'  => true,
              'values'    => $dropdownVals,
              'value'   => $selectedStores,
              'tabindex' => 1
              )
          );
      } else {
          $fieldset->addField(
              'store_id',
              'hidden',
              array(
              'name'      => 'stores[]',
              'value'     => Mage::app()->getStore(true)->getId()
              )
          );
      }

      $maptype = Mage::getBlockSingleton('reportmapper_salesreportmaps/adminhtml_mapper')->getMapType();

      $dropdownVals = array(
              '-1' => 'Select Report Type',
              'orders' => 'Sales Orders',
              'units' => 'Units Sold',
              'totalvalues' => 'Total Values',
              'refunds' => 'Refunds',
              'customers' => 'Customers',
              'deliverycost' => 'Delivery Cost'
          );

      if ($psku[2] > 0) {
          array_splice($dropdownVals, $psku[2]+1, 10000);
      }

      $fieldset->addField(
          'maptype',
          'select',
          array(
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'maptype',
          'onclick' => "",
          'onchange' => "",
          'value'  => $maptype,
          'values' => $dropdownVals,
          'disabled' => false,
          'readonly' => false,
          'tabindex' => 2
          )
      );

     $seltdAllOrdRel = Mage::getBlockSingleton('reportmapper_salesreportmaps/adminhtml_mapper')->getSelectedAllOrdersRelative();
     $fieldset->addField(
         'all_orders_relative',
         'checkbox',
         array(
          'name'      => 'all_orders_relative',
          'checked' => $seltdAllOrdRel,
          'onclick' => "",
          'onchange' => "",
          'disabled' => false,
          'after_element_html' => '  <small>% relative to all data</small>',
          'tabindex' => 5
         )
     );
      $seltdAddType = Mage::getBlockSingleton('reportmapper_salesreportmaps/adminhtml_mapper')->getAddressType();
      $fieldset->addField(
          'addresstype',
          'radios',
          array(
          'name'      => 'addresstype',
          'onclick' => "",
          'onchange' => "",
          'value'  => $seltdAddType,
          'values' => array(
              array('value'=>'billing','label'=>'Billing'),
              array('value'=>'shipping','label'=>'Shipping'),
          ),
          'disabled' => false,
          'readonly' => false,
          'tabindex' => 6
          )
      );

      $productList = array();
      $productList[] = array(
          'value' => '*', 'label' => Mage::helper('sales')->__('No Product Filter')
      );

      $products = Mage::getResourceModel('catalog/product_collection')
      ->addAttributeToSelect(array('entity_id','sku', 'name', 'description'))
      ->setOrder('name', 'asc');

      foreach ($products as $product) {
          $productList[] = array(
              'value' => $product->getId(), 'label' => $product->getName()
          );
      }

      if ($psku[4] > 0) {
          array_splice($productList, 0, 1);
          array_splice($productList, $psku[4]+1, 10000);
      }

      $selectedProducts = Mage::getBlockSingleton('reportmapper_salesreportmaps/adminhtml_mapper')->getSelectedProducts();
      $fieldset->addField(
          'products',
          'multiselect',
          array(
              'class' => 'required-entry',
              'required' => true,
              'value'  => $selectedProducts,
              'values' => $productList,
              'name' => 'products[]',
              'after_element_html' => '',
              'tabindex' => 7
          )
      );

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

      $selectedCouponRuleCodes = Mage::getBlockSingleton('reportmapper_salesreportmaps/adminhtml_mapper')->getSelectedCouponRuleCodes();
      $fieldset->addField(
          'coupon_rule_name',
          'multiselect',
          array(
              'class' => 'required-entry',
              'required' => true,
              'value' => $selectedCouponRuleCodes,
              'values' => $rulelist,
              'name' => 'coupon_rule_name[]',
              'after_element_html' => '',
              'tabindex' => 8

          )
      );

      $fieldset->addField(
          'map_colour',
          'text',
          array(
          'class'  => 'color {hash:true,required:false}',
          'required'  => false,
          'name'      => 'map_colour',
          'after_element_html' => 'Strength Colour',
          'value'  => $htmlMpr->getMapColour(),
          'tabindex' => 9
          )
      );

      $fieldset->addField(
          'highlight_colour',
          'text',
          array(
          'class'  => 'color {hash:true,required:false}',
          'required'  => false,
          'name'      => 'highlight_colour',
          'after_element_html' => 'Highlight Colour<br><br><span>Clear colour fields to reset to default.</span><br>',
          'value'  => $htmlMpr->getHighlightColour(),
          'tabindex' => 10
          )
      );

      $fieldset->addField(
          'submit',
          'submit',
          array(
          'required'  => true,
          'value'  => 'Create Report',
          'class' => 'map_form_button',
          'after_element_html' => '</br></br>',
          'tabindex' => 11
          )
      );

      return parent::_prepareForm();
  }
  
}