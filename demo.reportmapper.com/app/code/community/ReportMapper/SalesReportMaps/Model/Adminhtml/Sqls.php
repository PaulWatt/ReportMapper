<?php

/**
 * Class ReportMapper_SalesReportMaps_Model_Adminhtml_Sqls
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

class ReportMapper_SalesReportMaps_Model_Adminhtml_Sqls
{

    public function getOrders(
        $prefix, 
        $fromDate, 
        $toDate, 
        $addressType, 
        $extSqlA, 
        $extSqlB, 
        $extSqlE, 
        $countryCode
    )
    {
        $orders = 'SELECT REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(
                    UPPER(left(postcode,3)),"0",""),"1",""),"2",""),"3",""),"4",""),"5",""),"6",""),"7",""),"8",""),"9","") AS pc,
                    COUNT('.$prefix.'sales_flat_order_address.postcode) AS aa,
                    SUM(qty_ordered) AS bb
                    FROM '.$prefix.'sales_flat_order_address
                    LEFT JOIN '.$prefix.'sales_flat_order 
                    ON '.$prefix.'sales_flat_order_address.parent_id = '.$prefix.'sales_flat_order.entity_id
                    RIGHT JOIN '.$prefix.'sales_flat_order_item 
                    ON '.$prefix.'sales_flat_order.entity_id = '.$prefix.'sales_flat_order_item.order_id
                    WHERE '.$prefix.'sales_flat_order_address.country_id = "'.$countryCode.'" 
                    AND address_type = "' . $addressType . '"
                    ' . $extSqlA . $extSqlB . $extSqlE . '
                    AND '.$prefix.'sales_flat_order.created_at BETWEEN "' . $fromDate . '" AND "' . $toDate . '"
                    GROUP BY UPPER(pc)
                    ORDER BY aa DESC';
        return $orders;
    }

    public function getLabelItems_OrdersSQL(
        $prefix, 
        $fromDate, 
        $toDate, 
        $addressType, 
        $extSqlA, 
        $areaCodePrefix, 
        $countryCode
    )
    {
        $labelItemsSQL = 'SELECT REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(
                        UPPER(left(postcode,3)),"0",""),"1",""),"2",""),"3",""),"4",""),"5",""),"6",""),"7",""),"8",""),"9","") AS pc,
                        '.$prefix.'sales_flat_order_item.product_id AS pid,
                        '.$prefix.'sales_flat_order_item.name AS label_aa,
                        ROUND(SUM('.$prefix.'sales_flat_order_item.qty_ordered),0) AS label_bb
                        FROM '.$prefix.'sales_flat_order_address
                        LEFT JOIN '.$prefix.'sales_flat_order 
                        ON '.$prefix.'sales_flat_order_address.parent_id = '.$prefix.'sales_flat_order.entity_id
                        LEFT JOIN '.$prefix.'sales_flat_order_item 
                        ON '.$prefix.'sales_flat_order_address.parent_id = '.$prefix.'sales_flat_order_item.order_id
                        WHERE '.$prefix.'sales_flat_order_address.country_id = "'.$countryCode.'" 
                        AND address_type = "' . $addressType . '" 
                        AND REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(
                        UPPER(LEFT(postcode,3)),"0",""),"1",""),"2",""),"3",""),"4",""),"5",""),"6",""),"7",""),"8",""),"9","") = 
                        UPPER("' . $areaCodePrefix . '") 
                        ' . $extSqlA . '
                        AND '.$prefix.'sales_flat_order_item.created_at 
                        BETWEEN "' . $fromDate . '" AND "' . $toDate . '"
                        GROUP BY pc, '.$prefix.'sales_flat_order_item.product_id
                        ORDER BY label_bb DESC';
        return $labelItemsSQL;
    }

    public function getUnits($prefix, $fromDate, $toDate, $addressType, $extSqlA, $extSqlB, $extSqlE, $countryCode)
    {
        $units = 'SELECT REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(
                UPPER(left(`postcode`,3)),"0",""),"1",""),"2",""),"3",""),"4",""),"5",""),"6",""),"7",""),"8",""),"9","") AS pc,
                COUNT('.$prefix.'sales_flat_order_address.postcode) AS bb,
                ROUND(SUM(qty_ordered),0) AS aa
                FROM '.$prefix.'sales_flat_order_address
                LEFT JOIN '.$prefix.'sales_flat_order 
                ON '.$prefix.'sales_flat_order_address.parent_id = '.$prefix.'sales_flat_order.entity_id
                RIGHT JOIN '.$prefix.'sales_flat_order_item 
                ON '.$prefix.'sales_flat_order.entity_id = '.$prefix.'sales_flat_order_item.order_id
                WHERE '.$prefix.'sales_flat_order_address.country_id = "'.$countryCode.'" 
                AND address_type = "' . $addressType . '"
                ' . $extSqlA . $extSqlB . $extSqlE . '
                AND '.$prefix.'sales_flat_order.created_at BETWEEN "' . $fromDate . '" AND "' . $toDate . '" 
                GROUP BY UPPER(pc)';
        return $units;
    }

    public function getLabelItems_UnitsSQL(
        $prefix, 
        $fromDate, 
        $toDate, 
        $addressType, 
        $extSqlA, 
        $areaCodePrefix, 
        $countryCode
    )
    {
        $labelItemsSQL = 'SELECT REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(
                        UPPER(left(postcode,3)),"0",""),"1",""),"2",""),"3",""),"4",""),"5",""),"6",""),"7",""),"8",""),"9","") AS pc,
                        '.$prefix.'sales_flat_order_item.product_id AS pid,
                        '.$prefix.'sales_flat_order_item.name AS label_aa,
                        ROUND(SUM('.$prefix.'sales_flat_order_item.qty_ordered),0) AS label_bb
                        FROM '.$prefix.'sales_flat_order_address
                        LEFT JOIN '.$prefix.'sales_flat_order_item 
                        ON '.$prefix.'sales_flat_order_address.parent_id = '.$prefix.'sales_flat_order_item.order_id
                        WHERE '.$prefix.'sales_flat_order_address.country_id = "'.$countryCode.'" 
                        AND address_type = "' . $addressType . '" 
                        AND REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(
                        UPPER(LEFT(postcode,3)),"0",""),"1",""),"2",""),"3",""),"4",""),"5",""),"6",""),"7",""),"8",""),"9","") = UPPER("' . $areaCodePrefix . '") 
                        ' . $extSqlA . '
                        AND '.$prefix.'sales_flat_order_item.created_at 
                        BETWEEN "' . $fromDate . '" AND "' . $toDate . '"
                        GROUP BY pc, '.$prefix.'sales_flat_order_item.product_id
                        ORDER BY label_bb DESC';

        return $labelItemsSQL;
    }

    public function getTotalValues(
        $prefix, 
        $fromDate, 
        $toDate, 
        $addressType, 
        $extSqlA, 
        $extSqlB, 
        $extSqlE, 
        $countryCode
    )
    {
        $totalValues = 'SELECT REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(
                    UPPER(left(`postcode`,3)),"0",""),"1",""),"2",""),"3",""),"4",""),"5",""),"6",""),"7",""),"8",""),"9","") AS pc,
                    ROUND(SUM('.$prefix.'sales_flat_order.total_paid),2) AS aa,
                    SUM('.$prefix.'sales_flat_order.total_paid) / COUNT('.$prefix.'sales_flat_order_address.postcode) as bb 
                    FROM '.$prefix.'sales_flat_order_address
                    LEFT JOIN '.$prefix.'sales_flat_order 
                    ON '.$prefix.'sales_flat_order_address.parent_id = '.$prefix.'sales_flat_order.entity_id
                    RIGHT JOIN '.$prefix.'sales_flat_order_item 
                    ON '.$prefix.'sales_flat_order.entity_id = '.$prefix.'sales_flat_order_item.order_id
                    WHERE '.$prefix.'sales_flat_order_address.country_id = "'.$countryCode.'" 
                    AND address_type = "' . $addressType . '"
                    ' . $extSqlA . $extSqlB . $extSqlE . '
                    AND '.$prefix.'sales_flat_order.created_at BETWEEN "' . $fromDate . '" AND "' . $toDate . '"
                    GROUP BY pc
                    ORDER BY aa DESC';

        return $totalValues;
    }

    public function getLabelItems_TotalValuesSQL(
        $prefix, 
        $fromDate, 
        $toDate, 
        $addressType, 
        $extSqlA, 
        $areaCodePrefix, 
        $countryCode
    )
    {
        $labelItemsSQL = 'SELECT REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(
                        UPPER(left(postcode,3)),"0",""),"1",""),"2",""),"3",""),"4",""),"5",""),"6",""),"7",""),"8",""),"9","") AS pc,
                        '.$prefix.'sales_flat_order_item.product_id AS pid,
                        '.$prefix.'sales_flat_order_item.name AS label_aa,
                        ROUND(SUM('.$prefix.'sales_flat_order_item.row_total_incl_tax),2) AS label_bb
                        FROM '.$prefix.'sales_flat_order_address
                        LEFT JOIN '.$prefix.'sales_flat_order_item 
                        ON '.$prefix.'sales_flat_order_address.parent_id = '.$prefix.'sales_flat_order_item.order_id
                        WHERE '.$prefix.'sales_flat_order_address.country_id = "'.$countryCode.'" 
                        AND address_type = "' . $addressType . '"
                        AND REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(
                        UPPER(LEFT(postcode,3)),"0",""),"1",""),"2",""),"3",""),"4",""),"5",""),"6",""),"7",""),"8",""),"9","") = UPPER("' . $areaCodePrefix . '") 
                        ' . $extSqlA . '
                        AND '.$prefix.'sales_flat_order_item.created_at 
                        BETWEEN "' . $fromDate . '" AND "' . $toDate . '" 
                        GROUP BY pc, '.$prefix.'sales_flat_order_item.product_id
                        ORDER BY label_bb DESC';

        return $labelItemsSQL;
    }

    public function getRefunds(
        $prefix, 
        $fromDate, 
        $toDate, 
        $addressType, 
        $extSqlC, 
        $extSqlE, 
        $countryCode
    )
    {
        $refunds = 'SELECT REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(
                        UPPER(left(`postcode`,3)),"0",""),"1",""),"2",""),"3",""),"4",""),"5",""),"6",""),"7",""),"8",""),"9","") AS pc,
                        COUNT('.$prefix.'sales_flat_creditmemo.grand_total) AS aa,
                        SUM('.$prefix.'sales_flat_creditmemo.grand_total) AS bb                                  
                        FROM '.$prefix.'sales_flat_order_address
                        LEFT JOIN '.$prefix.'sales_flat_order 
                        ON '.$prefix.'sales_flat_order_address.parent_id = '.$prefix.'sales_flat_order.entity_id
                        LEFT JOIN '.$prefix.'sales_flat_creditmemo 
                        ON '.$prefix.'sales_flat_order.entity_id = '.$prefix.'sales_flat_creditmemo.order_id
                        LEFT JOIN '.$prefix.'sales_flat_creditmemo_item 
                        ON '.$prefix.'sales_flat_creditmemo.entity_id = '.$prefix.'sales_flat_creditmemo_item.parent_id
                        WHERE '.$prefix.'sales_flat_order_address.country_id = "'.$countryCode.'" 
                        AND address_type = "' . $addressType . '" 
                        ' . $extSqlC . $extSqlE . '
                        AND '.$prefix.'sales_flat_creditmemo.created_at BETWEEN "' . $fromDate . '" 
                        AND "' . $toDate . '" 
                        GROUP BY UPPER(pc)
                        ORDER BY aa DESC';
        return $refunds;
    }

    public function getLabelItems_RefundsSQL(
        $prefix, 
        $fromDate, 
        $toDate, 
        $addressType, 
        $extSqlC, 
        $areaCodePrefix, 
        $countryCode
    )
    {
        $labelItemsSQL = 'SELECT REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(
                        UPPER(LEFT(postcode,3)),"0",""),"1",""),"2",""),"3",""),"4",""),"5",""),"6",""),"7",""),"8",""),"9","") AS pc, 
                        '.$prefix.'sales_flat_creditmemo_item.product_id AS pid, 
                        '.$prefix.'sales_flat_creditmemo_item.name AS label_aa,
                        '.$prefix.'sales_flat_creditmemo_item.qty AS label_qty, 
                        ROUND(SUM('.$prefix.'sales_flat_creditmemo_item.row_total_incl_tax),2) AS label_bb 
                        FROM '.$prefix.'sales_flat_order_address 
                        LEFT JOIN '.$prefix.'sales_flat_creditmemo 
                        ON '.$prefix.'sales_flat_order_address.parent_id = '.$prefix.'sales_flat_creditmemo.order_id 
                        LEFT JOIN '.$prefix.'sales_flat_creditmemo_item 
                        ON '.$prefix.'sales_flat_creditmemo.entity_id = '.$prefix.'sales_flat_creditmemo_item.parent_id 
                        WHERE '.$prefix.'sales_flat_order_address.country_id = "'.$countryCode.'" 
                        AND address_type = "' . $addressType . '" 
                        AND REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(
                        UPPER(LEFT(postcode,3)),"0",""),"1",""),"2",""),"3",""),"4",""),"5",""),"6",""),"7",""),"8",""),"9","") = UPPER("' . $areaCodePrefix . '") 
                        ' . $extSqlC . '
                        AND '.$prefix.'sales_flat_creditmemo.created_at 
                        BETWEEN "' . $fromDate . '" AND "' . $toDate . '" 
                        GROUP BY pc, '.$prefix.'sales_flat_creditmemo_item.product_id 
                        ORDER BY label_bb DESC';
        return $labelItemsSQL;
    }

    public function getCustomers(
        $prefix, 
        $fromDate, 
        $toDate, 
        $extSqlD, 
        $extSqlE
    )
    {
        $customers = 'SELECT REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(
                    UPPER(LEFT(`value`,3)),"0",""),"1",""),"2",""),"3",""),"4",""),"5",""),"6",""),"7",""),"8",""),"9","") AS pc,
                    '.$prefix.'customer_address_entity_varchar.entity_id AS aa,
                    SUM(total_paid)  AS bb
                    FROM '.$prefix.'customer_address_entity_varchar
                    INNER JOIN '.$prefix.'customer_entity 
                    ON '.$prefix.'customer_address_entity_varchar.entity_id = '.$prefix.'customer_entity.entity_id
                    INNER JOIN '.$prefix.'sales_flat_order 
                    ON '.$prefix.'customer_address_entity_varchar.entity_id = '.$prefix.'sales_flat_order.customer_id
                    WHERE '.$prefix.'customer_address_entity_varchar.attribute_id = "30" 
                    ' . $extSqlD . $extSqlE . '
                    AND '.$prefix.'customer_entity.created_at BETWEEN "' . $fromDate . '" AND "' . $toDate . '" 
                    GROUP BY UPPER(pc)
                    ORDER BY aa DESC, bb DESC';
        return $customers;
    }

    public function getLabelItems_CustomersSQL(
        $prefix, 
        $fromDate, 
        $toDate, 
        $addressType, 
        $extSqlD, 
        $areaCodePrefix, 
        $countryCode
    )
    {
        $labelItemsSQL = 'SELECT REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(
                        UPPER(left(postcode,3)),"0",""),"1",""),"2",""),"3",""),"4",""),"5",""),"6",""),"7",""),"8",""),"9","") AS pc,
                        '.$prefix.'sales_flat_order_item.product_id AS pid,
                        '.$prefix.'sales_flat_order_item.name AS label_aa,
                        ROUND(SUM('.$prefix.'sales_flat_order_item.qty_ordered),0) AS label_bb
                        FROM '.$prefix.'sales_flat_order_address
                        LEFT JOIN '.$prefix.'sales_flat_order_item 
                        ON '.$prefix.'sales_flat_order_address.parent_id = '.$prefix.'sales_flat_order_item.order_id
                        WHERE address_type = "' . $addressType . '" 
                        AND '.$prefix.'sales_flat_order_address.country_id = "'.$countryCode.'" 
                        AND REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(
                        UPPER(LEFT(postcode,3)),"0",""),"1",""),"2",""),"3",""),"4",""),"5",""),"6",""),"7",""),"8",""),"9","") = UPPER("' . $areaCodePrefix . '") 
                        ' . $extSqlD . '
                        AND '.$prefix.'sales_flat_order_item.created_at 
                        BETWEEN "' . $fromDate . '" AND "' . $toDate . '" 
                        GROUP BY pc, '.$prefix.'sales_flat_order_item.product_id
                        ORDER BY label_bb DESC';
        return $labelItemsSQL;
    }

    public function getDeliveryCosts(
        $prefix, 
        $fromDate, 
        $toDate, 
        $addressType, 
        $extSqlA, 
        $extSqlB, 
        $extSqlE, 
        $countryCode
    )
    {
        $deliveryCosts = 'SELECT REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(
                          UPPER(left(`postcode`,3)),"0",""),"1",""),"2",""),"3",""),"4",""),"5",""),"6",""),"7",""),"8",""),"9","") AS pc,
                          COUNT('.$prefix.'sales_flat_order.shipping_incl_tax) AS aa,
                          ROUND(SUM('.$prefix.'sales_flat_order.shipping_incl_tax),2) AS bb
                          FROM '.$prefix.'sales_flat_order_address
                          LEFT JOIN '.$prefix.'sales_flat_order_item 
                          ON '.$prefix.'sales_flat_order_address.parent_id = '.$prefix.'sales_flat_order_item.order_id
                          LEFT JOIN '.$prefix.'sales_flat_order 
                          ON '.$prefix.'sales_flat_order_address.parent_id = '.$prefix.'sales_flat_order.entity_id
                          WHERE '.$prefix.'sales_flat_order_address.country_id = "'.$countryCode.'" 
                          AND address_type = "' . $addressType . '"
                          ' . $extSqlA . $extSqlB . $extSqlE . '
                          AND '.$prefix.'sales_flat_order.created_at BETWEEN "' . $fromDate . '" AND "' . $toDate . '" 
                          GROUP BY UPPER(pc)
                          ORDER BY aa DESC';
        return $deliveryCosts;
    }

    public function getLabelItems_DeliverycostsSQL(
        $prefix, 
        $fromDate, 
        $toDate, 
        $addressType, 
        $extSqlA, 
        $areaCodePrefix, 
        $countryCode
    )
    {
        $labelItemsSQL = 'SELECT REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(
                        UPPER(left(postcode,3)),"0",""),"1",""),"2",""),"3",""),"4",""),"5",""),"6",""),"7",""),"8",""),"9","") AS pc,
                        '.$prefix.'sales_flat_order_item.product_id AS pid,
                        '.$prefix.'sales_flat_order_item.name AS label_aa,
                        ROUND(SUM('.$prefix.'sales_flat_order_item.qty_ordered),0) AS label_bb
                        FROM '.$prefix.'sales_flat_order_address
                        LEFT JOIN '.$prefix.'sales_flat_order_item 
                        ON '.$prefix.'sales_flat_order_address.parent_id = '.$prefix.'sales_flat_order_item.order_id
                        WHERE '.$prefix.'sales_flat_order_address.country_id = "'.$countryCode.'" 
                        AND address_type = "' . $addressType . '"
                        AND REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(
                        UPPER(LEFT(postcode,3)),"0",""),"1",""),"2",""),"3",""),"4",""),"5",""),"6",""),"7",""),"8",""),"9","") = UPPER("' . $areaCodePrefix . '") 
                        ' . $extSqlA . '
                        AND '.$prefix.'sales_flat_order_item.created_at 
                        BETWEEN "' . $fromDate . '" AND "' . $toDate . '" 
                        GROUP BY pc, '.$prefix.'sales_flat_order_item.product_id
                        ORDER BY label_bb DESC';

        return $labelItemsSQL;
    }

}