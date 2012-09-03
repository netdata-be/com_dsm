<?php

/**
 * Joomla! 1.5 component dsm
 * @version 0.8
 * @author DSM
 * @package com_dsm
 **/
// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );
jimport('joomla.application.component.model');
/**
  * dsm Component Item Model
  * @package dsm
  */
class DsmModelDsm extends JModel {

	function ReportError($error_message) {
		$to = "info@desitemakers.be";
		$subject = "Wijnstandaard mail";
		$message = "<p>Something wrong with imports of wijnstandaard:</p><p><b>".$error_message."</b></p>";
		$from = "no-reply@wijnstandaard.be";
		$headers = "From:" . $from;
		mail($to,$subject,$message,$headers);
	}
	
	function ListProducts($productlist) {
		print_r($productlist);
	}
	
	
	//--------------------------------------------------------------------------------------------------------------------
	// IMPORT STAGING
	//--------------------------------------------------------------------------------------------------------------------
  
	function LoadProducts($productlist,$load_datetime) {
	
		// Joomla database & application objects
		$db =& JFactory::getDBO();
		$app=& JFactory::getApplication();
		
		// Create staging table if it is not there
		$query = " CREATE TABLE IF NOT EXISTS ".$db->namequote('#__dsm_staging_products')." ( ".
				 $db->namequote('load_id')." INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY, ".
				 $db->namequote('load_datetime')." DATETIME NOT NULL default '0000-00-00 00:00:00', ".
				 $db->namequote('load_status')." VARCHAR(10) NOT NULL, ".
				 $db->namequote('product_sku')." VARCHAR(10) NOT NULL, ".
				 $db->namequote('product_name_nl')." VARCHAR(180) NOT NULL, ".
				 $db->namequote('product_slug_nl')." VARCHAR(180) NOT NULL, ".
				 $db->namequote('product_price')." DECIMAL(15,5) NOT NULL, ". 
				 $db->namequote('product_params')." TEXT NULL, ".
				 $db->namequote('manufacturer_name_nl')." VARCHAR(180) NULL, ".
				 $db->namequote('manufacturer_slug_nl')." VARCHAR(180) NULL, ".
				 $db->namequote('custom_JAAR')." VARCHAR(10) NULL, ".
				 $db->namequote('custom_LAND')." VARCHAR(20) NULL, ".
				 $db->namequote('custom_REGIO')." VARCHAR(180) NULL, ".
				 $db->namequote('custom_TYPE')." VARCHAR(200) NULL, ".
				 $db->namequote('custom_FOODMATCH')." VARCHAR(200) NULL, ".
				 $db->namequote('custom_DRUIVEN')." VARCHAR(200) NULL, ".
				 $db->namequote('custom_SMAAK')." VARCHAR(200) NULL, ".
				 $db->namequote('custom_AFSLUITING')." VARCHAR(100) NULL, ".
				 $db->namequote('custom_BEWAREN')." VARCHAR(100) NULL, ".
				 $db->namequote('custom_PRIJS')." VARCHAR(100) NULL, ".
				 $db->namequote('custom_SCORE')." DECIMAL(10,1) NULL, ".
				 $db->namequote('product_special')." BOOLEAN NULL, ".
				 $db->namequote('product_special_price')." DECIMAL(10,2) NULL   )
				";
		$db->setQuery($query);
		$result = $db->query();
		if ($result) {echo '<p>#__dsm_staging_products table is created.</p>';} 
		else {echo '<p>Creation of #__dsm_staging_products table failed..</p>';return false;}
		
		//Delete staging records older then 4 days
		$query =" DELETE FROM  ".$db->namequote('#__dsm_staging_products').
				" WHERE DATEDIFF(".$db->quote($load_datetime).",".$db->namequote('load_datetime').") > 4";

		$db->setQuery($query);
		$result = $db->query();
		$AffectedRows = $db->getAffectedRows();
		if ($result) {if($AffectedRows){echo '<p>#__dsm_staging_products table cleaned ('.$AffectedRows.' items) OK.</p>';}}
		else {echo '<p>Cleaning of #__dsm_staging_products table failed..</p>';return false;}
		
		//Load the products array in the staging table
		$query = " INSERT INTO ".$db->namequote('#__dsm_staging_products')." (".
				 $db->namequote('load_id').",".
				 $db->namequote('load_datetime').",".
				 $db->namequote('load_status').",".
				 $db->namequote('product_sku').",".
				 $db->namequote('product_name_nl').",".
				 $db->namequote('product_slug_nl').",".
				 $db->namequote('product_price').",".
				 $db->namequote('product_params').",".
				 $db->namequote('manufacturer_name_nl').",".
				 $db->namequote('manufacturer_slug_nl').",".
				 $db->namequote('custom_JAAR').",".
				 $db->namequote('custom_LAND').",".
				 $db->namequote('custom_REGIO').",".
				 $db->namequote('custom_TYPE').",".
				 $db->namequote('custom_FOODMATCH').",".
				 $db->namequote('custom_DRUIVEN').",".
				 $db->namequote('custom_SMAAK').",".
				 $db->namequote('custom_AFSLUITING').",".
				 $db->namequote('custom_BEWAREN').",".
				 $db->namequote('custom_PRIJS').",".
				 $db->namequote('custom_SCORE').",".
				 $db->namequote('product_special').",".
				 $db->namequote('product_special_price').
				 ") VALUES ";
		
			foreach ($productlist as $prod) {
				$query .= "( ".
						"'',".
						$db->quote($load_datetime).",".
						"'loaded',".
						"'".$prod['product_sku']."',".
						$db->quote($prod['product_name_nl']).",".
						$db->quote($prod['product_slug_nl']).",".
						$db->quote($prod['product_price']).",".
						$db->quote($prod['product_params']).",".
						$db->quote($prod['manufacturer_name_nl']).",".
						$db->quote($prod['manufacturer_slug_nl']).",".
						$db->quote($prod['custom_JAAR']).",".
						$db->quote($prod['custom_LAND']).",".
						$db->quote($prod['custom_REGIO']).",".
						$db->quote($prod['custom_TYPE']).",".
						$db->quote($prod['custom_FOODMATCH']).",".
						$db->quote($prod['custom_DRUIVEN']).",".
						$db->quote($prod['custom_SMAAK']).",".
						$db->quote($prod['custom_AFSLUITING']).",".
						$db->quote($prod['custom_BEWAREN']).",".
						$db->quote($prod['custom_PRIJS']).",".
						$db->quote($prod['custom_SCORE']).",".
						"".$prod["product_special"].",".
						$db->quote($prod['product_special_price']).
						"),";
			} 
			
			// remove last comma in query string
			$query = substr_replace($query ,"",-1);
		
		//print_r($query);
		$db->setQuery($query);
		$result = $db->query();
		$AffectedRows = $db->getAffectedRows();
		$CountProducts = count($productlist);
		
		//Check the loaded totals!
		if ($result && $CountProducts == $AffectedRows) {
			echo '<p>#__dsm_staging_products loaded ('.$AffectedRows.' items) OK.</p>';
		} else {
			echo '<p>Loading of #__dsm_staging_products table failed.</p>';
				print_r($query);				
			return false;
		}
		
		return true;
	}
	
	
	//--------------------------------------------------------------------------------------------------------------------
	// IMPORT PRE-LOAD PHASE
	//--------------------------------------------------------------------------------------------------------------------
	
	function PrepareLoad($load_datetime) {
	
		// Joomla database & application objects
		$db =& JFactory::getDBO();
		$app=& JFactory::getApplication();
		
		//Get # products to update
		//---------------------------------
		$query =" UPDATE ".$db->nameQuote('#__dsm_staging_products').
				" SET ".$db->nameQuote('load_status')." = 'to update' ".
				" WHERE ".$db->nameQuote('load_datetime')." = ".$db->quote($load_datetime).
				" AND ".$db->nameQuote('product_sku')." IN (".
					" SELECT DISTINCT ".$db->nameQuote('product_sku').
					" FROM ".$db->nameQuote('#__virtuemart_products').")";
		$db->setQuery($query);
		$result = $db->query();
		$AffectedRows = $db->getAffectedRows();
		if ($result) {echo '<p>'.$AffectedRows.' products detected to update</p>';} 
		else {echo '<p>Error detecting products to update.</p>';return false;}
		
		//Get # products to insert
		//---------------------------------
		$query =" UPDATE ".$db->nameQuote('#__dsm_staging_products').
				" SET ".$db->nameQuote('load_status')." = 'to insert' ".
				" WHERE ".$db->nameQuote('load_datetime')." = ".$db->quote($load_datetime).
				" AND ".$db->nameQuote('product_sku')." NOT IN (".
					" SELECT DISTINCT ".$db->nameQuote('product_sku').
					" FROM ".$db->nameQuote('#__virtuemart_products').")";
		$db->setQuery($query);
		$result = $db->query();
		$AffectedRows = $db->getAffectedRows();
		if ($result) {echo '<p>'.$AffectedRows.' products detected to insert</p>';} 
		else {echo '<p>Error detecting products to insert.</p>';return false;}
		
		return true;
		
	}
	
	
	
	//--------------------------------------------------------------------------------------------------------------------
	// IMPORT EXECUTION PHASE
	//--------------------------------------------------------------------------------------------------------------------
	
	function ImportProducts($load_datetime,$language_tables_suffixes,$custom_fields) {
	
		// Joomla database & application objects
		$db =& JFactory::getDBO();
		$app=& JFactory::getApplication();
		
		//1. New Products INSERT
		//---------------------------------
		$query = " INSERT INTO ".$db->namequote('#__virtuemart_products')." (".
				$db->namequote('virtuemart_product_id').",".
				$db->namequote('virtuemart_vendor_id').",".
				$db->namequote('product_parent_id').",".
				$db->namequote('product_sku').",".
				$db->namequote('product_weight').",".
				$db->namequote('product_weight_uom').",".
				$db->namequote('product_length').",".
				$db->namequote('product_width').",".
				$db->namequote('product_height').",".
				$db->namequote('product_lwh_uom').",".
				$db->namequote('product_url').",".
				$db->namequote('product_in_stock').",".
				$db->namequote('product_ordered').",".
				$db->namequote('low_stock_notification').",".
				$db->namequote('product_available_date').",".
				$db->namequote('product_availability').",".
				$db->namequote('product_special').",".
				$db->namequote('product_sales').",".
				$db->namequote('product_unit').",".
				$db->namequote('product_params').",".
				$db->namequote('hits').",".
				$db->namequote('intnotes').",".
				$db->namequote('metarobot').",".
				$db->namequote('metaauthor').",".
				$db->namequote('layout').",".
				$db->namequote('published').",".
				$db->namequote('created_on').",".
				$db->namequote('created_by').",".
				$db->namequote('modified_on').",".
				$db->namequote('modified_by').",".
				$db->namequote('locked_on').",".
				$db->namequote('locked_by').
				")";
		
		$query.=" SELECT ".
				$db->quote('')."as ".$db->namequote('virtuemart_product_id').",".
				$db->quote('1')."as ".$db->namequote('virtuemart_vendor_id').",".
				$db->quote('0')."as ".$db->namequote('product_parent_id').",".
				$db->namequote('product_sku')."as ".$db->namequote('product_sku').",".
				$db->quote('0')."as ".$db->namequote('product_weight').",".
				$db->quote('KG')."as ".$db->namequote('product_weight_uom').",".
				$db->quote('0')."as ".$db->namequote('product_length').",".
				$db->quote('0')."as ".$db->namequote('product_width').",".
				$db->quote('0')."as ".$db->namequote('product_height').",".
				$db->quote('M')."as ".$db->namequote('product_lwh_uom').",".
				$db->quote('')."as ".$db->namequote('product_url').",".
				$db->quote('0')."as ".$db->namequote('product_in_stock').",".
				$db->quote('0')."as ".$db->namequote('product_ordered').",".
				$db->quote('0')."as ".$db->namequote('low_stock_notification').",".
				$db->quote($load_datetime)."as ".$db->namequote('product_available_date').",".
				$db->quote('')."as ".$db->namequote('product_availability').",".
				$db->namequote('product_special')."as ".$db->namequote('product_special').",".
				$db->quote('0')."as ".$db->namequote('product_sales').",".
				$db->quote('')."as ".$db->namequote('product_unit').",".
				$db->namequote('product_params')."as ".$db->namequote('product_params').",".
				$db->quote('')."as ".$db->namequote('hits').",".
				$db->quote('')."as ".$db->namequote('intnotes').",".
				$db->quote('')."as ".$db->namequote('metarobot').",".
				$db->quote('')."as ".$db->namequote('metaauthor').",".
				$db->quote('default')."as ".$db->namequote('layout').",".
				$db->quote('1')."as ".$db->namequote('published').",".
				$db->quote($load_datetime)."as ".$db->namequote('created_on').",".
				$db->quote('42')."as ".$db->namequote('created_by').",".
				$db->quote($load_datetime)."as ".$db->namequote('modified_on').",".
				$db->quote('42')."as ".$db->namequote('modified_by').",".
				$db->quote('0000-00-00 00:00:00')."as ".$db->namequote('locked_on').",".
				$db->quote('0')."as ".$db->namequote('locked_by').
				" FROM ".$db->namequote('#__dsm_staging_products').
				" WHERE ".$db->namequote('load_datetime')." = ".$db->quote($load_datetime).
				" AND  ".$db->namequote('load_status')." = ".$db->quote('to insert');
		
		$db->setQuery($query);
		$result = $db->query();
		$AffectedRows = $db->getAffectedRows();
		
		//Check query execution
		if ($result) {if($AffectedRows){echo '<p>#__virtuemart_products inserted ('.$AffectedRows.' items) OK.</p>';}}
		else {echo '<p>insert in #__virtuemart_products table failed.</p>';print_r($query);return false;}
		
		
		// 2. New Product Names INSERT
		//---------------------------------
		foreach ($language_tables_suffixes as $xx_xx) {
			$query = " INSERT INTO ".$db->namequote('#__virtuemart_products_'.$xx_xx)." (".
					$db->namequote('virtuemart_product_id').",".
					$db->namequote('product_name').",".
					$db->namequote('slug').
					")";
			
			$query .= "SELECT ".
					 $db->namequote('A.virtuemart_product_id').",".
					 $db->namequote('B.product_name_nl').",".
					 $db->namequote('B.product_slug_nl').
					 " FROM ".$db->namequote('#__virtuemart_products')." ".$db->namequote('A').
					 " INNER JOIN ".$db->namequote('#__dsm_staging_products')." ".$db->namequote('B').
					 " ON ".$db->namequote('A.product_sku')." = ".$db->namequote('B.product_sku').
					 " WHERE ".$db->namequote('B.load_datetime')." = ".$db->quote($load_datetime).
					 " AND ".$db->namequote('A.virtuemart_product_id')." NOT IN(".
						" SELECT ".$db->namequote('virtuemart_product_id').
						" FROM ".$db->namequote('#__virtuemart_products_'.$xx_xx).")";
			
			$db->setQuery($query);
			$result = $db->query();
			$AffectedRows = $db->getAffectedRows();
			
			//Check query execution
			if ($result) {
				echo '<p>#__virtuemart_products_'.$xx_xx.' inserted ('.$AffectedRows.' items) OK.</p>';
			} else {
				echo '<p>insert in #__virtuemart_products_'.$xx_xx.' table failed.</p>';
					print_r($query);
				return false;
			}
		}
		
		//3. New Product Prices INSERT
		//---------------------------------
		$query = " INSERT INTO ".$db->namequote('#__virtuemart_product_prices')." (".
				$db->namequote('virtuemart_product_id').",".
				$db->namequote('product_price').",".
				$db->namequote('product_override_price').",".
				$db->namequote('product_tax_id').",".
				$db->namequote('product_discount_id').",".
				$db->namequote('product_currency').",".
				$db->namequote('created_on').",".
				$db->namequote('created_by').",".
				$db->namequote('modified_on').",".
				$db->namequote('modified_by').",".
				$db->namequote('locked_on').",".
				$db->namequote('locked_by').
				")";
		
		$query .= "SELECT ".
				 $db->namequote('P.virtuemart_product_id').",".
				 $db->namequote('S.product_price').",".
				 $db->quote('0').",".
				 $db->quote('0').",".
				 $db->quote('0').",".
				 $db->quote('47').",".
				 $db->quote($load_datetime).",".
				 $db->quote('42').",".
				 $db->quote($load_datetime).",".
				 $db->quote('42').",".
				 $db->quote('0000-00-00 00:00:00').",".
				 $db->quote('0').
				 " FROM ".$db->namequote('#__virtuemart_products')." ".$db->namequote('P').
				 " INNER JOIN ".$db->namequote('#__dsm_staging_products')." ".$db->namequote('S').
				 " ON ".$db->namequote('P.product_sku')." = ".$db->namequote('S.product_sku').
				 " WHERE ".$db->namequote('P.virtuemart_product_id')." NOT IN(".
					" SELECT ".$db->namequote('virtuemart_product_id').
					" FROM ".$db->namequote('#__virtuemart_product_prices').")".
				 " AND ".$db->namequote('S.load_datetime')." = ".$db->quote($load_datetime);
		
		$db->setQuery($query);
		$result = $db->query();
		$AffectedRows = $db->getAffectedRows();
		
		//Check query execution
		if ($result) {
			echo '<p>#__virtuemart_product_prices inserted ('.$AffectedRows.' items) OK.</p>';
		} else {
			echo '<p>insert in #__virtuemart_product_prices table failed.</p>';
				print_r($query);
			return false;
		}
		
		
		//4. New Products Customfields INSERT
		//---------------------------------
		
		// truncate the customfields table
		$query = " TRUNCATE TABLE ".$db->namequote('#__virtuemart_product_customfields');
		$db->setQuery($query);
		$db->query();
		sleep(5);
		
		foreach ($custom_fields as $cf) {
		
			$query = " INSERT INTO ".$db->namequote('#__virtuemart_product_customfields')." (".
					$db->namequote('virtuemart_product_id').",".
					$db->namequote('virtuemart_custom_id').",".
					$db->namequote('custom_value').",".
					$db->namequote('created_on').",".
					$db->namequote('created_by').",".
					$db->namequote('modified_on').",".
					$db->namequote('modified_by').
					")";
			
			// First customfield value
			$query .= " SELECT ".
					 $db->namequote('A.virtuemart_product_id').",".
					 $db->quote($cf['id']).",".
					 "TRIM(substring_index(".$db->namequote('B.'.$cf['name']).",',',1)),".
					 $db->quote($load_datetime).",".
					 $db->quote('42').",".
					 $db->quote($load_datetime).",".
					 $db->quote('42').
					 " FROM ".$db->namequote('#__virtuemart_products')." ".$db->namequote('A').
					 " INNER JOIN ".$db->namequote('#__dsm_staging_products')." ".$db->namequote('B')." ON ".$db->namequote('A.product_sku')." = ".$db->namequote('B.product_sku').
					 " WHERE ".
					 " ".$db->namequote('A.virtuemart_product_id')." NOT IN(SELECT DISTINCT ".$db->namequote('virtuemart_product_id')." FROM ".$db->namequote('#__virtuemart_product_customfields')." WHERE ".$db->namequote('virtuemart_custom_id')." = ".$db->quote($cf['id']).")".
					 " AND ".$db->namequote('B.'.$cf['name'])." NOT LIKE ".$db->quote('%\%%').
					 " AND ".$db->namequote('B.'.$cf['name'])." IS NOT NULL ".
					 " AND ".$db->namequote('B.load_datetime')." = ".$db->quote($load_datetime);
			// Last customfield value
			$query .= " UNION SELECT ".
					 $db->namequote('A.virtuemart_product_id').",".
					 $db->quote($cf['id']).",".
					 "TRIM(substring_index(".$db->namequote('B.'.$cf['name']).",',',-1)),".
					 $db->quote($load_datetime).",".
					 $db->quote('42').",".
					 $db->quote($load_datetime).",".
					 $db->quote('42').
					 " FROM ".$db->namequote('#__virtuemart_products')." ".$db->namequote('A').
					 " INNER JOIN ".$db->namequote('#__dsm_staging_products')." ".$db->namequote('B')." ON ".$db->namequote('A.product_sku')." = ".$db->namequote('B.product_sku').
					 " WHERE ".
					 " ".$db->namequote('A.virtuemart_product_id')." NOT IN(SELECT DISTINCT ".$db->namequote('virtuemart_product_id')." FROM ".$db->namequote('#__virtuemart_product_customfields')." WHERE ".$db->namequote('virtuemart_custom_id')." = ".$db->quote($cf['id']).")".
					 " AND ".$db->namequote('B.'.$cf['name'])." LIKE ".$db->quote('%,%').
					 " AND ".$db->namequote('B.'.$cf['name'])." NOT LIKE ".$db->quote('%\%%').
					 " AND ".$db->namequote('B.'.$cf['name'])." IS NOT NULL ".
					 " AND ".$db->namequote('B.load_datetime')." = ".$db->quote($load_datetime);					 

			$db->setQuery($query);
			$result = $db->query();
			$AffectedRows = $db->getAffectedRows();
			
			//Check query execution
			if ($result) {
				echo '<p>#__virtuemart_product_customfields '.$cf['name'].' inserted ('.$AffectedRows.' items) OK.</p>';
			} else {
				echo '<p>insert in #__virtuemart_product_customfields table, field '.$cf['name'].' failed.</p>';	
					print_r($query);
				return false;
			}
		}
		
		
		
		//Related products auto-generate script
		$query = " INSERT INTO ".$db->namequote('#__virtuemart_product_customfields')." (".
				$db->namequote('virtuemart_product_id').",".
				$db->namequote('virtuemart_custom_id').",".
				$db->namequote('custom_value').",".
				$db->namequote('created_on').",".
				$db->namequote('created_by').",".
				$db->namequote('modified_on').",".
				$db->namequote('modified_by').
				")";
				
		//auto generate related products on first characters of product_sku
		/*$query .= " SELECT ".
					$db->namequote('A.virtuemart_product_id').",".
					$db->quote('1').",".
					$db->namequote('B.virtuemart_product_id').",".
					$db->quote($load_datetime).",".
					$db->quote('42').",".
					$db->quote($load_datetime).",".
					$db->quote('42').
					" FROM ".$db->namequote('#__virtuemart_products')." ".$db->namequote('A').
					" LEFT JOIN ".$db->namequote('#__virtuemart_products')." ".$db->namequote('B').
					" ON LEFT(".$db->namequote('A.product_sku').",6) = LEFT(".$db->namequote('B.product_sku').",6)".
					" WHERE ".$db->namequote('A.virtuemart_product_id')."<>".$db->namequote('B.virtuemart_product_id');*/
		
		//auto generate related products on custom_SMAAK		
		$query .= " SELECT DISTINCT".
					$db->namequote('A.virtuemart_product_id').",".
					$db->quote('1').",".
					$db->namequote('B.virtuemart_product_id').",".
					$db->quote($load_datetime).",".
					$db->quote('42').",".
					$db->quote($load_datetime).",".
					$db->quote('42').
					" FROM ".$db->namequote('#__virtuemart_product_customfields')." ".$db->namequote('A').
					" LEFT JOIN ".$db->namequote('#__virtuemart_product_customfields')." ".$db->namequote('B').
					" ON ".$db->namequote('A.custom_value')." = ".$db->namequote('B.custom_value').
					" WHERE ".$db->namequote('A.virtuemart_product_id')."<>".$db->namequote('B.virtuemart_product_id').
						" AND ".$db->namequote('A.virtuemart_custom_id')."=9".
						" AND ".$db->namequote('B.virtuemart_custom_id')."=9";
				 
		$db->setQuery($query);
		$result = $db->query();
		$AffectedRows = $db->getAffectedRows();
		
		//Check query execution
		if ($result) {
			echo '<p>#__virtuemart_product_customfields related_products inserted ('.$AffectedRows.' items) OK.</p>';
		} else {
			echo '<p>insert in #__virtuemart_product_customfields table, field related_products failed.</p>';	
				print_r($query);
			return false;
		}
		
		//New Products Link to Categories
		/*---------------------------------
		$query = " INSERT INTO ".$db->namequote('#__virtuemart_product_categories')." (".
				$db->namequote('virtuemart_product_id').",".
				$db->namequote('virtuemart_category_id').",".
				$db->namequote('ordering').
				")";
		
		$query .= "SELECT ".
				 " ".$db->namequote('P.virtuemart_product_id').",".$db->namequote('C.virtuemart_category_id').",".$db->quote('0').",".$db->quote('0').",".$db->quote('0').",".$db->quote('47').",".$db->quote($load_datetime).",".$db->quote('42').",".$db->quote($load_datetime).",".$db->quote('42').",".$db->quote('0000-00-00 00:00:00').",".$db->quote('0').
				 " FROM ".$db->namequote('#__virtuemart_products')." ".$db->namequote('P').
				 " INNER JOIN ".$db->namequote('#__dsm_staging_products')." ".$db->namequote('S')." ON ".$db->namequote('P.product_sku')." = ".$db->namequote('S.product_sku').
				 " WHERE ".
				 " ".$db->namequote('P.virtuemart_product_id')." NOT IN(SELECT ".$db->namequote('virtuemart_product_id')." FROM ".$db->namequote('#__virtuemart_product_prices').")".
				 " AND ".$db->namequote('S.load_datetime')." = ".$db->quote($load_datetime);
		
		$db->setQuery($query);
		$result = $db->query();
		$AffectedRows = $db->getAffectedRows();
		
		//Check query execution
		if ($result) {
			echo '<p>#__virtuemart_product_prices inserted ('.$AffectedRows.' items) OK.</p>';
		} else {
			echo '<p>insert in #__virtuemart_product_prices table failed.</p>';
				print_r($query);
			return false;
		} */
		
		// New Manufacturers INSERT
		//---------------------------------	
			$query =" SELECT DISTINCT ".$db->namequote('manufacturer_name_nl').",".$db->namequote('manufacturer_slug_nl').
					" FROM ".$db->namequote('#__dsm_staging_products').
					" WHERE ".$db->namequote('load_datetime')." = ".$db->quote($load_datetime).
					" AND ".$db->namequote('manufacturer_name_nl')." <> '' ".
					" AND ".$db->namequote('manufacturer_name_nl')." NOT IN ( ".
								" SELECT DISTINCT ".$db->namequote('mf_name')." FROM ".$db->namequote('#__virtuemart_manufacturers_nl_nl').
								")";
			$db->setQuery($query);
			$result = $db->query();
			$AffectedRows = $db->getAffectedRows();
			$new_manufacturers = $db->loadAssocList();
			
			if ($result) {
				echo '<p>detected ('.$AffectedRows.' new manufacturers) OK.</p>';
			} else {
				echo '<p>detection new manufacturers failed.</p>';	
					print_r($query);
				return false;
			}
			
			foreach ($new_manufacturers as $man) {
			
				//insert new manufacturer id's
				$query = " INSERT INTO ".$db->namequote('#__virtuemart_manufacturers')." (".
						$db->namequote('virtuemart_manufacturer_id').",".
						$db->namequote('virtuemart_manufacturercategories_id').",".
						$db->namequote('hits').",".
						$db->namequote('published').",".
						$db->namequote('created_on').",".
						$db->namequote('created_by').",".
						$db->namequote('modified_on').",".
						$db->namequote('modified_by').",".
						$db->namequote('locked_on').",".
						$db->namequote('locked_by').
						")";
				$query .= "VALUES('','','0','1','".$load_datetime."','42','".$load_datetime."','42','0000-00-00 00:00:00','0')";
				
				$db->setQuery($query);
				$result = $db->query();
				$AffectedRows = $db->getAffectedRows();
				$insertid = $db->insertid();
				
				if ($result) {
					echo '<p>#__virtuemart_manufacturer '.$man['manufacturer_name_nl'].' inserted.</p>';
				} else {
					echo '<p>insert of #virtuemart_manufacturer '.$man['manufacturer_name_nl'].' failed.</p>';	
						print_r($query);
					return false;
				}
				
				//Insert new manufacturer names
				//---------------------------------
				foreach($language_tables_suffixes as $xx_xx) {
					$query = " INSERT INTO ".$db->namequote('#__virtuemart_manufacturers_'.$xx_xx)." (".
							$db->namequote('virtuemart_manufacturer_id').",".
							$db->namequote('mf_name').",".
							$db->namequote('mf_email').",".
							$db->namequote('mf_desc').",".
							$db->namequote('mf_url').",".
							$db->namequote('slug').
							")";
					
					$query .= "VALUES (".$db->quote($insertid).",".$db->quote($man['manufacturer_name_nl']).",'','','',".$db->quote($man['manufacturer_slug_nl']).")";				
					
					$db->setQuery($query);
					$result = $db->query();
					$AffectedRows = $db->getAffectedRows();
					
					if ($result) {
						echo '<p>#__virtuemart_manufacturer_'.$xx_xx.' '.$man['manufacturer_name_nl'].' inserted.</p>';
					} else {
						echo '<p>insert of #virtuemart_manufacturers_nl_nl'.$xx_xx.' '.$man['manufacturer_name_nl'].' failed.</p>';	
							print_r($query);
						return false;
					}
				}
			
			}
			
			// Link new manufacturers to their products
			// truncate the table
			$query = " TRUNCATE TABLE ".$db->namequote('#__virtuemart_product_manufacturers');
			$db->setQuery($query);
			$db->query();
			sleep(1);
			
			$query = " INSERT INTO ".$db->namequote('#__virtuemart_product_manufacturers')." (".
					$db->namequote('virtuemart_product_id').",".
					$db->namequote('virtuemart_manufacturer_id').
					")";
			$query.=" SELECT DISTINCT ".$db->namequote('P.virtuemart_product_id').",".$db->namequote('M.virtuemart_manufacturer_id').
					" FROM ".$db->namequote('#__dsm_staging_products')." ".$db->namequote('S').
						" INNER JOIN ".$db->namequote('#__virtuemart_products')." ".$db->namequote('P').
								" ON ".$db->namequote('P.product_sku')." = ".$db->namequote('S.product_sku').
						" INNER JOIN ".$db->namequote('#__virtuemart_manufacturers_nl_nl')." ".$db->namequote('M').
								" ON ".$db->namequote('M.mf_name')." = ".$db->namequote('S.manufacturer_name_nl').
					" WHERE ".$db->namequote('S.load_datetime')." = ".$db->quote($load_datetime).
					" AND ".$db->namequote('S.manufacturer_name_nl')." <> '' ".
					" AND ".$db->namequote('P.virtuemart_product_id')." NOT IN ( ".
								" SELECT DISTINCT ".$db->namequote('virtuemart_product_id')." FROM ".$db->namequote('#__virtuemart_product_manufacturers').
								")";
			$db->setQuery($query);
			$result = $db->query();
			$AffectedRows = $db->getAffectedRows();
			
			if ($result) {
				echo '<p>linked ('.$AffectedRows.' products to new manufacturers) OK.</p>';
			} else {
				echo '<p>linking new manufacturers to products failed.</p>';	
					print_r($query);
				return false;
			}
		
		return true;
	}
	
	
	//--------------------------------------------------------------------------------------------------------------------
	// UPDATE EXECUTION
	//--------------------------------------------------------------------------------------------------------------------
	
	function UpdateProducts($load_datetime,$language_tables_suffixes,$custom_fields) {
		
		// Joomla database & application objects
		$db =& JFactory::getDBO();
		$app=& JFactory::getApplication();
		
		
		// update products not in source (depublishing)
		$query = " UPDATE ".$db->namequote('#__virtuemart_products').
				 " SET ".
				 $db->namequote('published')." = ".$db->quote('0').
				 " WHERE ".$db->namequote('product_sku')." NOT IN ".
					"(SELECT ".$db->namequote('product_sku').
					" FROM ".$db->namequote('#__dsm_staging_products').
					" WHERE ".$db->namequote('load_datetime')." = ".$db->quote($load_datetime).")";
		
		$db->setQuery($query);
		$result = $db->query();
		$AffectedRows = $db->getAffectedRows();
		if ($result) {if($AffectedRows){echo '<p>#__virtuemart_products depublished ('.$AffectedRows.' items) OK.</p>';}}
		else {echo '<p>depublishing of #__virtuemart_products table failed.</p>';print_r($query);return false;}
		
		
		// update products in source
		$query = " UPDATE ".$db->namequote('#__virtuemart_products')." ".$db->namequote('P').
				 " INNER JOIN ".$db->namequote('#__dsm_staging_products')." ".$db->namequote('S').
				 " ON ".$db->namequote('S.product_sku')."=".$db->namequote('P.product_sku').
				 " SET ".
				 $db->namequote('P.product_special')." = ".$db->namequote('S.product_special').",".
				 $db->namequote('P.published')." = ".$db->quote('1').
				 " WHERE ".$db->namequote('load_status')." = ".$db->quote('to update').
				 " AND ".$db->namequote('load_datetime')." = ".$db->quote($load_datetime);
		
		$db->setQuery($query);
		$result = $db->query();
		$AffectedRows = $db->getAffectedRows();
		if ($result) {if($AffectedRows){echo '<p>#__virtuemart_products updated ('.$AffectedRows.' items) OK.</p>';}} 
		else {echo '<p>update of #__virtuemart_products table failed.</p>';print_r($query);return false;}
		
		
		// update product prices
		$query = " UPDATE ".$db->namequote('#__virtuemart_product_prices')." ".$db->namequote('R').
				 " INNER JOIN ".$db->namequote('#__virtuemart_products')." ".$db->namequote('P').
				 " ON ".$db->namequote('P.virtuemart_product_id')."=".$db->namequote('R.virtuemart_product_id').
				 " INNER JOIN ".$db->namequote('#__dsm_staging_products')." ".$db->namequote('S').
				 " ON ".$db->namequote('S.product_sku')."=".$db->namequote('P.product_sku').
				 " SET ".
				 $db->namequote('R.product_price')." = ".$db->namequote('S.product_price').
				 " WHERE ".$db->namequote('S.load_status')." = ".$db->quote('to update').
				 " AND ".$db->namequote('S.load_datetime')." = ".$db->quote($load_datetime);
		
		$db->setQuery($query);
		$result = $db->query();
		$AffectedRows = $db->getAffectedRows();
		if ($result) {if($AffectedRows){echo '<p>#__virtuemart_product_prices updated ('.$AffectedRows.' items) OK.</p>';}}
		else {echo '<p>update of #__virtuemart_product_prices table failed.</p>';print_r($query);return false;}
		
		
		// update product names in each language
		foreach ($language_tables_suffixes as $xx_xx) {
			$query = " UPDATE ".$db->namequote('#__virtuemart_products_'.$xx_xx)." ".$db->namequote('L').
					 " INNER JOIN ".$db->namequote('#__virtuemart_products')." ".$db->namequote('P').
					 " ON ".$db->namequote('P.virtuemart_product_id')." = ".$db->namequote('L.virtuemart_product_id').
					 " INNER JOIN ".$db->namequote('#__dsm_staging_products')." ".$db->namequote('S').
					 " ON ".$db->namequote('P.product_sku')." = ".$db->namequote('S.product_sku').
					 " SET ".
					 $db->namequote('L.product_name')." = ".$db->namequote('S.product_name_nl').",".
					 $db->namequote('L.slug')." = ".$db->namequote('S.product_slug_nl').
					 " WHERE ".$db->namequote('S.load_status')." = ".$db->quote('to update').
					 " AND ".$db->namequote('S.load_datetime')." = ".$db->quote($load_datetime);
			
			$db->setQuery($query);
			$result = $db->query();
			$AffectedRows = $db->getAffectedRows();
			if ($result) {if($AffectedRows){echo '<p>#__virtuemart_products_'.$xx_xx.' updated ('.$AffectedRows.' items) OK.</p>';}}
			else {echo '<p>update of #__virtuemart_products_'.$xx_xx.' table failed.</p>';print_r($query);return false;}
		}
		
		
		//Products CustomFields UPDATE
		//---------------------------------
		/*foreach ($custom_fields as $cf) {
			$query = " UPDATE ".$db->namequote('#__virtuemart_product_customfields')." ".$db->namequote('C').
				 " INNER JOIN ".$db->namequote('#__virtuemart_products')." ".$db->namequote('P')." ON ".$db->namequote('P.virtuemart_product_id')." = ".$db->namequote('C.virtuemart_product_id').
				 " INNER JOIN ".$db->namequote('#__dsm_staging_products')." ".$db->namequote('S')." ON ".$db->namequote('P.product_sku')." = ".$db->namequote('S.product_sku').
				 " SET ".
				 $db->namequote('C.custom_value')." = ".$db->namequote('S.'.$cf['name']).
				 " WHERE ".$db->namequote('S.load_status')." = ".$db->quote('to update')." AND ".$db->namequote('S.load_datetime')." = ".$db->quote($load_datetime).
				 " AND ".$db->namequote('C.virtuemart_custom_id')." = ".$cf['id'];
		
			$db->setQuery($query);
			$result = $db->query();
			$AffectedRows = $db->getAffectedRows();
		
			//Check query execution
			if ($result) {
				echo '<p>#__virtuemart_product_customfields '.$cf['name'].' updated ('.$AffectedRows.' items) OK.</p>';
			} else {
				echo '<p>update of #__virtuemart_product_customfields '.$cf['name'].' table failed.</p>';
					print_r($query);
				return false;
			}
		}*/
	return true;
	}	
	
	
	//--------------------------------------------------------------------------------------------------------------------
	// CATEGORIES
	// function that links Virtuemart Products to Virtuemart Categories, based on its customfields & the first metakeyword
	//--------------------------------------------------------------------------------------------------------------------
	
	function UpdateProductCategories() {
		$db =& JFactory::getDBO();
		$app=& JFactory::getApplication();
		
		// truncate the table linking products to categories
		$query = " TRUNCATE TABLE ".$db->namequote('#__virtuemart_product_categories');
		$db->setQuery($query);
		$db->query();

		// get all category id's and the first value in the metakeyword field
		$query =" SELECT DISTINCT virtuemart_category_id, substring_index(".$db->namequote('metakey').",',',1) as metakey".
				" FROM ".$db->namequote('#__virtuemart_categories_nl_nl').
				" WHERE metakey IS NOT NULL AND metakey <>''";
		$db->setQuery($query);
		$categories = $db->loadAssocList();
		
		// insert all links between products and categories based on customfieldvalue and first metakeyword
		$query = " INSERT INTO ".$db->namequote('#__virtuemart_product_categories')." (".
				$db->namequote('virtuemart_product_id').",".
				$db->namequote('virtuemart_category_id').",".
				$db->namequote('ordering').
				")";
		
				foreach($categories as $cat) {
					$query.=" SELECT DISTINCT ".$db->namequote('virtuemart_product_id').", ".$db->quote($cat['virtuemart_category_id']).", ".$db->quote('1').
							" FROM ".$db->namequote('#__virtuemart_product_customfields').
							" WHERE ".$db->namequote('custom_value')." = ".$db->quote($cat['metakey']).
							" UNION ALL ";
				}
				$query = substr_replace($query ,"",-10);
		
		$db->setQuery($query);
		$result = $db->query();
		$AffectedRows = $db->getAffectedRows();
		
		// check query execution
		if ($result) {
			echo '<p>#__virtuemart_product_categories updated ('.$AffectedRows.' items) OK.</p>';
		} else {
			echo '<p>update of #__virtuemart_product_categories table failed.</p>';
				print_r($query);
			return false;
		}
		
	return true;
	}
	
	
	
	
	function UpdateProductRatings($load_datetime) {
		$db =& JFactory::getDBO();
		$app=& JFactory::getApplication();
		
		// truncate the ratings table
		$query = " TRUNCATE TABLE ".$db->namequote('#__virtuemart_ratings');
		$db->setQuery($query);
		$db->query();
		
		// insert all links between products and categories based on customfieldvalue and first metakeyword
		$query = " INSERT INTO ".$db->namequote('#__virtuemart_ratings')." (".
				$db->namequote('virtuemart_product_id').",".
				$db->namequote('rates').",".
				$db->namequote('ratingcount').",".
				$db->namequote('rating').",".
				$db->namequote('published').",".
				$db->namequote('created_on').",".
				$db->namequote('created_by').",".
				$db->namequote('modified_on').",".
				$db->namequote('modified_by').
				")";
				
				$query.=" SELECT DISTINCT ".
							$db->namequote('P.virtuemart_product_id').",".
							$db->quote('5').",".
							$db->quote('1').",".
							$db->namequote('S.custom_SCORE').",".
							$db->quote('1').",".
							$db->quote($load_datetime).",".
							$db->quote('42').",".
							$db->quote($load_datetime).",".
							$db->quote('42').
							
						" FROM ".$db->namequote('#__dsm_staging_products')." ".$db->namequote('S').
							" INNER JOIN ".$db->namequote('#__virtuemart_products')." ".$db->namequote('P').
							" ON ".$db->namequote('P.product_sku')." = ".$db->namequote('S.product_sku').
						" WHERE ".$db->namequote('S.load_datetime')." = ".$db->quote($load_datetime);
		$db->setQuery($query);
		$result = $db->query();
		$AffectedRows = $db->getAffectedRows();
		
		// check query execution
		if ($result) {
			echo '<p>#__virtuemart_ratings updated ('.$AffectedRows.' items) OK.</p>';
		} else {
			echo '<p>update of #__virtuemart_ratings table failed.</p>';
				print_r($query);
			return false;
		}
		
	return true;
	}
	
	
	
	
	//--------------------------------------------------------------------------------------------------------------------
	// ORDERS
	//--------------------------------------------------------------------------------------------------------------------

	function SetShippedOrders($shipped_orders) {
	
		$db =& JFactory::getDBO();
		$app=& JFactory::getApplication();
		
		// create handled orders table
		$query = " CREATE TABLE IF NOT EXISTS ".$db->namequote('#__dsm_briljant_orders')." ( ".
				 $db->namequote('virtuemart_order_id')." INT(11) NOT NULL PRIMARY KEY, ".
				 $db->namequote('briljant_ordernr')." INT(11) NOT NULL, ".
				 $db->namequote('briljant_orderbk')." VARCHAR(20) NOT NULL)";
		$db->setQuery($query);
		$db->query();
		
		// create handled orders staging table
		$query = " CREATE TABLE IF NOT EXISTS ".$db->namequote('#__dsm_briljant_orders_staging')." ( ".
				 $db->namequote('virtuemart_order_id')." INT(11) NOT NULL PRIMARY KEY, ".
				 $db->namequote('briljant_ordernr')." INT(11) NOT NULL, ".
				 $db->namequote('briljant_orderbk')." VARCHAR(20) NOT NULL)";
		$db->setQuery($query);
		$db->query();
		
		// truncate the staging table
		$query = " TRUNCATE TABLE ".$db->namequote('#__dsm_briljant_orders_staging');
		$db->setQuery($query);
		$db->query();
		sleep(5);
		
		// insert shipped orders
		$query = " INSERT INTO ".$db->namequote('#__dsm_briljant_orders_staging')." (".
				$db->namequote('virtuemart_order_id').",".
				$db->namequote('briljant_ordernr').",".
				$db->namequote('briljant_orderbk').
				") VALUES ";
				foreach ($shipped_orders as $order) {
				$query .= "( ".
						$db->quote($order['virtuemart_order_id']).",".
						$db->quote($order['briljant_ordernr']).",".
						$db->quote($order['briljant_orderbk']).
						"),";
				} 
		$query = substr_replace($query ,"",-1);// remove last comma in query string
		$db->setQuery($query);
		$db->query();
		$AffectedRows = $db->getAffectedRows();
		//if ($result) {if($AffectedRows){echo '<p>#__dsm_briljant_orders inserted ('.$AffectedRows.' items) OK.</p>';}}
		//else {echo '<p>insert of #__dsm_briljant_orders table failed.</p>';print_r($query);return false;}
		
		//sync staging table with main table
		$query = " INSERT INTO ".$db->namequote('#__dsm_briljant_orders')." (".
				$db->namequote('virtuemart_order_id').",".
				$db->namequote('briljant_ordernr').",".
				$db->namequote('briljant_orderbk').
				") SELECT ".
					$db->namequote('virtuemart_order_id').",".
					$db->namequote('briljant_ordernr').",".
					$db->namequote('briljant_orderbk').
				" FROM ".$db->namequote('#__dsm_briljant_orders_staging').
				" WHERE ".$db->namequote('virtuemart_order_id')." NOT IN ".
				" (SELECT DISTINCT ".$db->namequote('virtuemart_order_id').
				" FROM ".$db->namequote('#__dsm_briljant_orders').")";
		$db->setQuery($query);
		$db->query();
		$AffectedRows = $db->getAffectedRows();
		
		// Set status of new shipped ordes
		$query =" UPDATE ".$db->namequote('#__virtuemart_orders').
				" SET order_status = ".$db->quote('S').
				" WHERE order_status = ".$db->quote('C').
				" AND ".$db->namequote('virtuemart_order_id')." IN (".
					" SELECT virtuemart_order_id ".
					" FROM ".$db->namequote('#__dsm_briljant_orders')." )";
		$db->setQuery($query);
		$db->query();
		$AffectedRows = $db->getAffectedRows();
		//if ($result) {if($AffectedRows){echo '<p>#__virtuemart_orders updated ('.$AffectedRows.' items) OK.</p>';}}
		//else {echo '<p>update of #__virtuemart_orders table failed.</p>';print_r($query);return false;}
		
		return true;
	
	}
	
	function GetPaidOrders() {
	
		$db =& JFactory::getDBO();
		$app=& JFactory::getApplication();
		
		$query =" SELECT ".
				$db->namequote('O.virtuemart_order_id').",".
				$db->namequote('O.created_on').",".
				$db->namequote('O.virtuemart_user_id').",".
				$db->namequote('O.order_total').",".
				$db->namequote('U.first_name').",".
				$db->namequote('U.last_name').",".
				$db->namequote('U.address_1').",".
				$db->namequote('U.address_2').",".
				$db->namequote('C.country_3_code').",".
				$db->namequote('U.zip').",".
				$db->namequote('U.city').",".
				$db->namequote('S.shipment_cost').
				" FROM ".$db->namequote('#__virtuemart_orders')." ".$db->namequote('O').
				" LEFT OUTER JOIN ".$db->namequote('#__virtuemart_order_userinfos')." ".$db->namequote('U').
					" ON ".$db->namequote('O.virtuemart_order_id')."=".$db->namequote('U.virtuemart_order_id').
				" LEFT OUTER JOIN ".$db->namequote('#__virtuemart_countries')." ".$db->namequote('C').
					" ON ".$db->namequote('U.virtuemart_country_id')."=".$db->namequote('C.virtuemart_country_id').
				" LEFT OUTER JOIN ".$db->namequote('#__virtuemart_shipment_plg_weight_countries')." ".$db->namequote('S').
					" ON ".$db->namequote('O.virtuemart_order_id')."=".$db->namequote('S.virtuemart_order_id').
				" WHERE ".$db->namequote('O.order_status')." = 'C' ";

		$db->setQuery($query);
		//print_r($query);
		$result = $db->loadAssocList();
		
		return $result;
	
	}
	
	function GetOrderItems($vm_order_id) {
	
		$db =& JFactory::getDBO();
		$app=& JFactory::getApplication();
		
		
		$query =" SELECT * ".
				" FROM ".$db->namequote('#__virtuemart_order_items')." ".$db->namequote('I').
				" WHERE ".$db->namequote('I.virtuemart_order_id')." = ".$db->quote($vm_order_id)
				;

		$db->setQuery($query);
		//print_r($query);
		$result = $db->loadAssocList();
		
		return $result;
	
	}
	
	//--------------------------------------------------------------------------------------------------------------------
	// CUSTOMERS
	//--------------------------------------------------------------------------------------------------------------------
	
	function GetCustomers() {
	
		$db =& JFactory::getDBO();
		$app=& JFactory::getApplication();
		
		$query =" SELECT * ".
				" FROM ".$db->namequote('#__virtuemart_userinfos')." ".$db->namequote('U').
				" LEFT OUTER JOIN ".$db->namequote('#__virtuemart_countries')." ".$db->namequote('C').
				" ON ".$db->namequote('U.virtuemart_country_id')."=".$db->namequote('C.virtuemart_country_id');

		$db->setQuery($query);
		//print_r($query);
		$result = $db->loadAssocList();
		
		return $result;
	
	}
	
	
	//--------------------------------------------------------------------------------------------------------------------
	// GENERAL FUNCTIONS
	//--------------------------------------------------------------------------------------------------------------------
	
	function EmailWrap($body,$subject) {
		$email ='<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">'.
				'<html>'.
				'<head>'.
					'<meta http-equiv="Content-Type" content="text/html; charset=utf-8">'.
					'<title>'.$subject.'</title>'.
				'</head>'.
				'<body class="body" style="text-align:left; margin: 0; padding: 0; color:#ffffff; background-color: #3F3B3B;" marginheight="0" topmargin="0" marginwidth="0" leftmargin="0">'.
				
				'<table width="100%" border="0" cellspacing="0" cellpadding="0">'.
					'<tr>'.
						'<td bgcolor="#3F3B3B" style="background-color: #3F3B3B;">'.
							'<table style="color: #3F3B3B; background-color: #ffffff;" bgcolor="#ffffff" width="578" border="0" align="center" cellpadding="20" cellspacing="0">'.
							'<tr><td>'.$body.'</td></tr>'.
							'</table>'.
						'</td>'.
					'</tr>'.
				'</table>'.
				
				'</body>'.
				'</html>';
		return $email;
	}
	
	function GetIP()  {
		if (isset($_SERVER)) {
				if (isset($_SERVER["HTTP_X_FORWARDED_FOR"])) {
						$ip_addr = $_SERVER["HTTP_X_FORWARDED_FOR"];
				} elseif (isset($_SERVER["HTTP_CLIENT_IP"])) {
						$ip_addr = $_SERVER["HTTP_CLIENT_IP"];
				} else {
						$ip_addr = $_SERVER["REMOTE_ADDR"];
				}
		} else {
				if ( getenv( 'HTTP_X_FORWARDED_FOR' ) ) {
						$ip_addr = getenv( 'HTTP_X_FORWARDED_FOR' );
				} elseif ( getenv( 'HTTP_CLIENT_IP' ) ) {
						$ip_addr = getenv( 'HTTP_CLIENT_IP' );
				} else {
						$ip_addr = getenv( 'REMOTE_ADDR' );
				}
		}
		return $ip_addr;
	}
	
}
