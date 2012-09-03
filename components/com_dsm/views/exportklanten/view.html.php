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

class DsmViewExportklanten extends JView {
  function display($tpl = null) {
	$model = $this->GetModel();
	$customers = $model->getCustomers();
  
	// Output root element.
	// niet meer nodig, echo '<BRILJANT>';
	
	// Output the data.
		foreach($customers as $customer) {
			echo '
			<KLANTEN>
				<NUMMER>'.$customer["virtuemart_user_id"].'</NUMMER>
				<AANSPREK></AANSPREK>
				<NAAM>'.$customer["first_name"].' '.$customer["last_name"].'</NAAM>
				<ADRES1>'.$customer["first_name"].' '.$customer["last_name"].' '.$customer['company'].'</ADRES1>
				<ADRES2>'.$customer["address_1"].'</ADRES2>
				<ADRES3>'.$customer["address_2"].'</ADRES3>
				<LAND>'.$customer['country_3_code'].'</LAND>
				<POSTNR>'.$customer["zip"].'</POSTNR>
				<GEMEENTE>'.$customer["city"].'</GEMEENTE>
				<TELEFOON1>'.$customer["phone_1"].'</TELEFOON1>
				<TELEFOON2>'.$customer["phone_2"].'</TELEFOON2>
				<TELEFAX></TELEFAX>
				<URL></URL>
				<TAALKODE></TAALKODE>
				<BTWREGIME></BTWREGIME>
				<BTWNR></BTWNR>
				<BANK></BANK>
				<BANKNR></BANKNR>
				<BANKNAAM></BANKNAAM>
				<BANKADRES1></BANKADRES1>
				<BANKADRES2></BANKADRES2>
				<BANKPLAATS></BANKPLAATS>
				<VERTEGENW></VERTEGENW>
				<GROEP1></GROEP1>
				<GROEP2></GROEP2>
				<GROEP3></GROEP3>
				<MANINGEN></MANINGEN>
				<DOMICIL></DOMICIL>
				<KREDLIMIET></KREDLIMIET>
				<BETVOORW></BETVOORW>
				<VALUTA></VALUTA>
				<ISSTDBOEK></ISSTDBOEK>
				<STDBOEK></STDBOEK>
				<TEGENBOEK></TEGENBOEK>
				<PRIJSCAT></PRIJSCAT>
				<KORTING1></KORTING1>
				<KORTING2></KORTING2>
				<FACTPERBON></FACTPERBON>
				<TEKST></TEKST>
				<KORTCONT></KORTCONT>
				<GLOBKORT></GLOBKORT>
				<CENTRREKNR></CENTRREKNR>
				<MELDING></MELDING>
				<ZNDIVK></ZNDIVK>
				<FACTKLANT></FACTKLANT>
				<METADMKOST></METADMKOST>
				<DOMBANKNR></DOMBANKNR>
				<ALTCODE></ALTCODE>
				<ZNDKLKRT></ZNDKLKRT>
				<VERLTAR></VERLTAR>
				<VRIJBEROEP></VRIJBEROEP>
				<DAGBOEK></DAGBOEK>
				<FACTAFDRUK></FACTAFDRUK>
				<GOEDKEURDAT></GOEDKEURDAT>
				<GOEDKEURNAAM></GOEDKEURNAAM>
				<GLNCODE></GLNCODE>
				<EXVKLA>
					<NUMMER>'.$customer["virtuemart_user_id"].'</NUMMER>
					<X_WINEMILES>200</X_WINEMILES>
				</EXVKLA>
			</KLANTEN>
			';
		}
	// Terminate root element.
	// niet meer nodig, echo '</BRILJANT>';
		
  }
}
