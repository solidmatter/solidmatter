<?php
//------------------------------------------------------------------------------
/**
* @package	solidMatter:sb_system
* @author	()((() [Oliver Müller]
* @version	1.00.00
*/
//------------------------------------------------------------------------------

import('sb.handler.request');

if (!defined('BACKEND')) define('BACKEND', '1001');
if (!defined('FRONTEND')) define('FRONTEND', '1002');
if (!defined('INTERNAL')) define('INTERNAL', '1003');

//------------------------------------------------------------------------------
/**
*/
class ActionQueue {
	
	private $elemSubject = NULL;
	private $aQueue = array();
	
	
	public function __construct($elemSubject, $sView, $sAction, $eContext) {
		
		
		
		
		
	}
	
	public function process() {
		
		foreach ($this->aQueue as $aEntry) {
			
			$aEntry['subject']->callView($aEntry['view'], $aEntry['action']);
			
			
		}
		
		
		
	}
	
	
	
}








?>