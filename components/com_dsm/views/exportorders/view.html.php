<?php 
/**
 * Joomla! 2.5.4 component dsm
 * @version 1.0
 * @author DSM
 * @package com_dsm
 **/
// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );
jimport('joomla.application.component.view');
 /**
  * @package dsm
  */

class DsmViewExportorders extends JView {
  function display($tpl = null) {
	
	$model = $this->GetModel();
	$xml = JFactory::getXMLParser('Simple');
	
	// Get the confirmed orders from Briljant
	$xmlfile='../data/sites/web/wine-budgetcom/sync/OS01.xml';
	if ($xml->loadFile($xmlfile)) {
		$orders_shipped = array();
		$i = 1;
		if(isset($xml->document->ORDER)) {
			foreach ($xml->document->ORDER as $order) {	
				if ($order->getElementByPath('ORDERBK') && $order->getElementByPath('ORDERNR') && $order->getElementByPath('X_WEBREF')) {
					$orders_shipped[$i]["virtuemart_order_id"] = trim($order->getElementByPath('X_WEBREF')->data());
					$orders_shipped[$i]["briljant_ordernr"] = trim($order->getElementByPath('ORDERNR')->data());
					$orders_shipped[$i]["briljant_orderbk"] = trim($order->getElementByPath('ORDERBK')->data());
				}
			$i++;
			}
		}
	} else {
		echo '<error>load of OS01.xml file failed</error>';
		return false;
	}
	
	// Set the confirmed shipped orders by briljant in virtuemart
	$model->SetShippedOrders($orders_shipped);
	
	// Get the remaining paid orders that need to be shipped
	$orders = $model->GetPaidOrders();

	// Output the paid orders in xml
	foreach ($orders as $order) {
		$orderitems = $model->GetOrderItems($order['virtuemart_order_id']);

		echo '<ORD>';
			echo '<ORDERBK>OT</ORDERBK>';
			echo '<ORDNR>'.$order['virtuemart_order_id'].'</ORDNR>';
			echo '<DATUM>'.date("d/m/Y", strtotime($order['created_on'])).'</DATUM>';
			echo '<KLANT>'.$order['virtuemart_user_id'].'</KLANT>';
			echo '<NAAM>'.$order['last_name'].' '.$order['first_name'].'</NAAM>';
			echo '<ADRES1></ADRES1>';
			echo '<ADRES2>'.$order['address_1'].'</ADRES2>';
			echo '<ADRES3>'.$order['address_2'].'</ADRES3>';
			echo '<LAND>'.$order['country_3_code'].'</LAND>';
			echo '<POSTNR>'.$order['zip'].'</POSTNR>';
			echo '<GEMEENTE>'.$order['city'].'</GEMEENTE>';
			//echo '<TOTPRIJS>'.$order['order_total'].'</TOTPRIJS>';
			
			foreach ($orderitems as $item) {
				echo '<ORDD>';
					echo '<ARTIKEL>'.$item['order_item_sku'].'</ARTIKEL>';
					echo '<OMSCHR><![CDATA['.$item['order_item_name'].']]></OMSCHR>';
					echo '<AANTAL>'.$item['product_quantity'].'</AANTAL>';
					echo '<STDPRIJS>'.str_replace('.',',',$item['product_final_price']).'</STDPRIJS>';
				echo '</ORDD>';
			}
			
			//winemiles korting			
			echo '<ORDD>'.
					'<ARTIKEL>WMKORTING</ARTIKEL>'.
					'<OMSCHR>Wine Miles Korting</OMSCHR>'.
					'<AANTAL>0</AANTAL>'.
					'<STDPRIJS>1</STDPRIJS>'.
				 '</ORDD>';
			
			//leveringskosten
			echo '<ORDD>'.
					'<ARTIKEL>TK</ARTIKEL>'.
					'<OMSCHR>Leveringskosten</OMSCHR>'.
					'<AANTAL>1</AANTAL>'.
					'<STDPRIJS>'.str_replace('.',',',$order['shipment_cost']).'</STDPRIJS>'.
				 '</ORDD>';
				 
			echo '<EXVORD>'.
					'<X_WEBREF>'.$order['virtuemart_order_id'].'</X_WEBREF>'.
					'<X_AAFL><![CDATA[Alternatief Leveradres 1]]></X_AAFL>'.
					'<X_AAFL2><![CDATA[Alternatief Leveradres 2]]></X_AAFL2>'.
				 '</EXVORD>';
		echo '</ORD>';
	}
  }
}
