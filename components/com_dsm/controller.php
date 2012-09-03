<?php 
/**
 * Joomla! 1.5 component smell
 * @version 0.8
 * @author DSM
 * @package com_smell
 **/
// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );
jimport( 'joomla.application.component.controller' );

 /**
  * Smell Component Controller
  * @package Smell
  */

  class dsmController extends JController  {

    //Default Display View
    function display() {
		$view = & $this->getView( 'display', 'html' );
		$view->display();
    }
	
    // Sync Views
    function exportorders() {
		$app =& JFactory::getApplication(); 
		$app->setTemplate('blank');
		
		$view =& $this->getView( 'exportorders', 'html' );
		$view->setModel( $this->getModel( 'Dsm' ), true );	
		$view->display();
    }
	
    // Sync Views
    function exportklanten() {
		$app =& JFactory::getApplication(); 
		$app->setTemplate('blank');
		
		$view =& $this->getView( 'exportklanten', 'html' );
		$view->setModel( $this->getModel( 'Dsm' ), true );	
		$view->display();
    }
	
    // Sync Views
    function exportklantkp() {
		$app =& JFactory::getApplication(); 
		$app->setTemplate('blank');
		
		$view =& $this->getView( 'exportklantkp', 'html' );
		$view->setModel( $this->getModel( 'Dsm' ), true );	
		$view->display();
    }
	
    function import() {
		$view =& $this->getView( 'import', 'html' );
		$view->setModel( $this->getModel( 'Dsm' ), true );
		$view->display();
    }

  }
