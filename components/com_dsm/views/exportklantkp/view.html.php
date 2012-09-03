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

class DsmViewExportklantkp extends JView {
  function display($tpl = null) {
	$model = $this->GetModel();
	$customers = $model->getCustomers();
  
	// Output root element.
	// niet meer nodig, echo '<BRILJANT>';
	
	// Output the data.
		foreach($customers as $customer) {
			echo '
			<KLANTKP>
				<AANSPREK></AANSPREK>
				<NAAM>'.$customer["first_name"].' '.$customer["last_name"].'</NAAM>
				<FUNCTIE></FUNCTIE>
				<TELEFOON>'.$customer["phone_1"].'</TELEFOON>
				<GSM>'.$customer["phone_2"].'</GSM>
				<FAX></FAX>
				<EMAIL></EMAIL>
				<VOLGNR>1</VOLGNR>
				<Aanspreking></Aanspreking>
				<ADRES1>'.$customer["first_name"].' '.$customer["last_name"].' '.$customer['company'].'</ADRES1>
				<ADRES2>'.$customer["address_1"].'</ADRES2>
				<ADRES3>'.$customer["address_2"].'</ADRES3>
				<LAND>'.$customer['country_3_code'].'</LAND>
				<POSTNR>'.$customer["zip"].'</POSTNR>
				<GEMEENTE>'.$customer["city"].'</GEMEENTE>
				<KLANT>'.$customer["virtuemart_user_id"].'</KLANT>
				<BRIEFCTP></BRIEFCTP>
				<EMAILMAN></EMAILMAN>
				<TAALKODE>N</TAALKODE>
				<EMAILFACT></EMAILFACT>
			</KLANTEN>
			';
		}
	// Terminate root element.
	// niet meer nodig, echo '</BRILJANT>';
		
  }
}
