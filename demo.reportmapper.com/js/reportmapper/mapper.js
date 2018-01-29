/**
 * ReportMapper - Sales and Marketing Reporting Tool for Magento 1x
 * @category ReportMapper
 * @package	ReportMapper_SalesReportMaps
 * @version	1.0.0
 * @created	10th January 2018 3.00pm
 * @author Paul Watt <support@reportmapper.com>
 * @purpose	ReportMapper block
 * @copyright	Copyright (c) 2018 ReportMapper.com
 * @license	http://opensource.org/licenses/osl-3.0.php  Open Software License
 */

	 var $j = jQuery.noConflict();
     $j(function() {
        "use strict";
        var svgpanzoom = $j("svg").svgPanZoom();
     });


    function show(evt,node,highlight_colour)
    {
        var svgdoc = evt.target.ownerDocument;
        var obj = svgdoc.getElementById(node);
        var objid = obj.id;
        var tr_element = objid.substring('label_'.length);
        var divrow = document.getElementById(tr_element).id;
        obj.setAttribute("display", "visible");
        evt.target.setAttributeNS(null,"fill",highlight_colour);
        document.getElementById(objid).style.visibility="visible";
        document.getElementById("divrow_" +divrow).style.backgroundColor = highlight_colour;
    }

    function hide(evt,node,map_colour)
    {
        var svgdoc = evt.target.ownerDocument;
        var obj = svgdoc.getElementById(node);
        var objid = obj.id;
        var tr_element = objid.substring('label_'.length);
        var divrow = document.getElementById(tr_element).id;
        obj.setAttribute("display" , "none");
        evt.target.setAttributeNS(null,"fill",map_colour);
        document.getElementById(objid).style.visibility="hidden";
        document.getElementById("divrow_" +divrow).style.backgroundColor = "";
    }

    function ChangeBackgroundColor(row,highlight_colour) {
    	var divrowid = row.id;
    	var rowid = divrowid.substring('divrow_'.length);
    	var gid = rowid;
    	var stringgid = "label_" +gid;
        row.style.backgroundColor = highlight_colour;
        document.getElementById(rowid).setAttributeNS(null,"fill",highlight_colour);
        document.getElementById(stringgid).style.visibility="visible";
    }

    function RestoreBackgroundColor(row,map_colour,styles) {
    	var divrowid = row.id;
    	var rowid = divrowid.substring('divrow_'.length);
    	var gid = rowid;
    	var stringgid = "label_" +gid;
        row.style = styles;
        document.getElementById(rowid).setAttributeNS(null,"fill",map_colour);
        document.getElementById(stringgid).style.visibility="hidden";
        document.getElementById("divrow_" +rowid).style.backgroundColor = "";
    }


