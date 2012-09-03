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

class DsmViewImport extends JView {

  function display($tpl = null) {
  
	$model = $this->GetModel();
	
	$xml = JFactory::getXMLParser('Simple');
	$xmlfile='../data/sites/web/wine-budgetcom/sync/art2si.xml';
	if (!$xml->loadFile($xmlfile)) {echo "File Open Error: ".$xmlfile;return false;}
	
	$products = array();
	$i = 1;
	
	foreach ($xml->document->ARTIKEL as $artikel) {
		
		if ($artikel->getElementByPath('ARTIKELNUMMER')) {
			$products[$i]["product_sku"] = trim($artikel->getElementByPath('ARTIKELNUMMER')->data());
			if ($products[$i]["product_sku"]=='' OR is_null($products[$i]["product_sku"])){
				unset($products[$i]);
				continue;
			}
			
			// alles wordt per 6 flessen verkocht, behalve proefpakketten.
			if (substr($products[$i]["product_sku"],0,2) == 'PP') {
				$products[$i]["product_params"] = 'min_order_level="1"|max_order_level="0"|';
			} else {
				$products[$i]["product_params"] = 'min_order_level="6"|max_order_level="0"|';
			}
		}
		
		if ($artikel->getElementByPath('ARTNAAMNL')) {
			$products[$i]["product_name_nl"] = trim($artikel->getElementByPath('ARTNAAMNL')->data());
		}
		
		//Generate an article slug!			
		if ($artikel->getElementByPath('ARTNAAMNL')&&$artikel->getElementByPath('ARTIKELNUMMER')) {
			$artnaamnl = trim($artikel->getElementByPath('ARTNAAMNL')->data());
			$artikelnummer = trim($artikel->getElementByPath('ARTIKELNUMMER')->data());
			if ($artnaamnl<>''&&$artikelnummer<>'') {
				$products[$i]["product_slug_nl"] = JApplication::stringURLSafe($artnaamnl.'-'.$artikelnummer);
			} else {
				echo '<p>artikelnaam en artikelnummer zijn voor elk artikel verplicht! Controleer bronbestand. '.$artikelnummer.'</p>';
				return false;
			}
		} else {
			echo '<p>artikelnaam en artikelnummer zijn voor elk artikel verplicht! Controleer bronbestand.</p>';
			return false;
		}
		
		if ($artikel->getElementByPath('VOLPRIJS')) {
			$prijs = trim($artikel->getElementByPath('VOLPRIJS')->data());
			$prijs = str_replace(",",".",$prijs);
			$products[$i]["product_price"] = 0+number_format($prijs/1.21,5);
			if ($products[$i]["product_price"]==0){
				echo '<p>prijs kan niet 0 zijn! Controleer bronbestand. '.$products[$i]["product_sku"].'</p>';
				$products[$i]["product_price"] = 0+number_format(999999/1.21,5);
			}
			$prijs = 0 + $prijs;
			if ($prijs > 0.000001 && $prijs <=5) {$products[$i]["custom_PRIJS"] = '<5 euro';}
			if ($prijs > 5 && $prijs <=10) {$products[$i]["custom_PRIJS"] = '5-10 euro';}
			if ($prijs > 10 && $prijs <=15) {$products[$i]["custom_PRIJS"] = '10-15 euro';}
			if ($prijs > 15 && $prijs <=20) {$products[$i]["custom_PRIJS"] = '15-20 euro';}
			if ($prijs > 20 && $prijs <=25) {$products[$i]["custom_PRIJS"] = '20-25 euro';}
			if ($prijs > 25 && $prijs <=99999999) {$products[$i]["custom_PRIJS"] = '>25 euro';}
		} else {
			echo '<p>prijs is voor elk artikel verplicht! Controleer bronbestand. '.$products[$i]["product_sku"].'</p>';
			$products[$i]["product_price"] = 0+number_format(999999/1.21,5);
			$products[$i]["custom_PRIJS"] = '>25 euro';
		}
		
		if ($artikel->getElementByPath('PRODUCENT')) {
			$products[$i]["manufacturer_name_nl"] = trim($artikel->getElementByPath('PRODUCENT')->data());
			$products[$i]["manufacturer_slug_nl"] = JApplication::stringURLSafe($products[$i]["manufacturer_name_nl"]);
		} else {
			echo '<p>producent is voor elk artikel verplicht! Controleer bronbestand. '.$products[$i]["product_sku"].'</p>';
			return false;
		}
		
		if ($artikel->getElementByPath('JAAR')) {
			$products[$i]["custom_JAAR"] = trim($artikel->getElementByPath('JAAR')->data());
		}
		
		if ($artikel->getElementByPath('LAND')) {
			$products[$i]["custom_LAND"] = trim($artikel->getElementByPath('LAND')->data());
		}
		
		if ($artikel->getElementByPath('REGIO')) {
			$products[$i]["custom_REGIO"] = trim($artikel->getElementByPath('REGIO')->data());
		}
		
		if ($artikel->getElementByPath('TYPE')) {
			$products[$i]["custom_TYPE"] = trim($artikel->getElementByPath('TYPE')->data());
			if($products[$i]["custom_TYPE"] == 'rood zoet') {$products[$i]["custom_TYPE"]='rood';$products[$i]["custom_SMAAK"]='zoet';}
			if($products[$i]["custom_TYPE"] == 'wit zoet') {$products[$i]["custom_TYPE"]='wit';$products[$i]["custom_SMAAK"]='zoet';}
		}
		
		if ($artikel->getElementByPath('FOODMATCH')) {
			$products[$i]["custom_FOODMATCH"] = trim($artikel->getElementByPath('FOODMATCH')->data());
			$products[$i]["custom_FOODMATCH"] = str_replace(' en ',',',$products[$i]["custom_FOODMATCH"]);
		}
		
		if ($artikel->getElementByPath('DRUIVEN')) {
			$products[$i]["custom_DRUIVEN"] = trim($artikel->getElementByPath('DRUIVEN')->data());
			$products[$i]["custom_DRUIVEN"] = str_replace('&',',',$products[$i]["custom_DRUIVEN"]);
			$products[$i]["custom_DRUIVEN"] = str_replace(' - ',',',$products[$i]["custom_DRUIVEN"]);
		}
		
		if ($artikel->getElementByPath('SMAAK')) {
				$products[$i]["custom_SMAAK"] = trim($artikel->getElementByPath('SMAAK')->data());
		}
		
		if ($artikel->getElementByPath('AFSLUITING')) {
			$products[$i]["custom_AFSLUITING"] = trim($artikel->getElementByPath('AFSLUITING')->data());
		}
		
		if ($artikel->getElementByPath('BEWAREN')) {
			$products[$i]["custom_BEWAREN"] = trim($artikel->getElementByPath('BEWAREN')->data());
		}
		
		if ($artikel->getElementByPath('ISPROMO')) {
			$products[$i]["product_special"] = trim($artikel->getElementByPath('ISPROMO')->data());
			} else {
			$products[$i]["product_special"] = "FALSE";
		}
		
		if ($artikel->getElementByPath('PROMOTIEPRIJS')) {
			$products[$i]["product_special_price"] = 0+$artikel->getElementByPath('PROMOTIEPRIJS')->data();
			} else {
			$products[$i]["product_special_price"] = '0';
			$products[$i]["product_special"] = "FALSE";
		}
		
		if ($artikel->getElementByPath('SCORES')) {
			$score = trim($artikel->getElementByPath('SCORES')->data());
			$score = str_replace(",",".",$score);
			$products[$i]["custom_SCORE"] = 0+number_format($score/2,1);
			} else {
			$products[$i]["custom_SCORE"] = '0';
		}
		
		$i++;
	}
	
	//TEST print_r($products);
	//$model->ListProducts($products);	
	
	//Parameters
	$language_tables_suffixes=array("nl_nl","nl_be");
	$custom_fields = array(
					array('name'=>'custom_JAAR',		'id'=>3),
					array('name'=>'custom_LAND',		'id'=>4),
					array('name'=>'custom_REGIO',		'id'=>5),
					array('name'=>'custom_TYPE',		'id'=>6),
					array('name'=>'custom_FOODMATCH',	'id'=>7),
					array('name'=>'custom_DRUIVEN',		'id'=>8),
					array('name'=>'custom_SMAAK',		'id'=>9),
					array('name'=>'custom_AFSLUITING',	'id'=>10),
					array('name'=>'custom_BEWAREN',		'id'=>11),
					array('name'=>'custom_PRIJS',		'id'=>13),
					array('name'=>'custom_SCORE',		'id'=>15)
				);
	
	//Verwerk de producten in de webshop!
	$load_datetime = date("Y-m-d h:i:s");
	
	echo '<hr/>';
	echo '<p>--- LoadProducts started ---</p>';
	If ($model->LoadProducts($products,$load_datetime)) {
		echo '<p>--- LoadProducts ended succesful ---</p><hr/>';
	} else {echo '<p>ABORTED LoadProducts operation</p>';return false;}
	
	echo '<p>--- PrepareLoad started ---</p>';
	If ($model->PrepareLoad($load_datetime)) {
		echo '<p>--- PrepareLoad ended succesful ---</p><hr/>';
	} else {echo '<p>ABORTED PrepareLoad operation</p>';return false;}
	
	echo '<p>--- ImportProducts started ---</p>';
	If ($model->ImportProducts($load_datetime,$language_tables_suffixes,$custom_fields)) {
		echo '<p>--- ImportProducts ended succesful ---</p><hr/>';
	} else {echo '<p>ABORTED ImportProducts operation</p>';return false;}
	
	echo '<p>--- UpdateProducts started ---</p>';
	If ($model->UpdateProducts($load_datetime,$language_tables_suffixes,$custom_fields)) {
		echo '<p>--- UpdateProducts ended succesful ---</p><hr/>';
	} else {echo '<p>ABORTED UpdateProducts operation</p>';return false;}

	echo '<p>--- UpdateProductCategories started ---</p>';
	If ($model->UpdateProductCategories()) {
		echo '<p>--- UpdateProductCategories ended succesful ---</p><hr/>';
	} else {echo '<p>ABORTED UpdateProductCategories operation</p>';return false;}
	
	echo '<p>--- UpdateProductRatings started ---</p>'; 
	If ($model->UpdateProductRatings($load_datetime)) {
		echo '<p>--- UpdateProductRatings ended succesful ---</p><hr/>';
	} else {echo '<p>ABORTED UpdateProductRatings operation</p>';return false;}
	
  }
}