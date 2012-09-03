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

class DsmViewDisplay extends JView {
  function display($tpl = null) {
		 
		// Output XML header.
		 
		// Output root element.
		echo '<BRILJANT>';
		 
		// Output the data.
		echo '<ORD>';
		echo '<KLANT>KLANT001</KLANT>';
		echo '<ORDD><ARTIKEL>ABRU103</ARTIKEL><AANTAL>10</AANTAL></ORDD>';
		echo '</ORD>';
		 
		// Terminate root element.
		echo '</BRILJANT>';
  }
}
