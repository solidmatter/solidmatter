<?php

//------------------------------------------------------------------------------
/**
* @package	solidMatter[sbSystem]
* @author	()((() [Oliver Müller]
* @version	1.00.00
*/
//------------------------------------------------------------------------------

//import('sb.system.debug');

//------------------------------------------------------------------------------
/**
*/
class sbView_logs_system extends sbView {
	
	public function execute($sAction) {
		
		global $_RESPONSE;
		global $_REQUEST;
		
		switch ($sAction) {
			
			case 'display':
				break;
				
			case 'show_log':
				
				switch ($_REQUEST->getParam('log')) {
					
					case 'database':
						$sFilename = 'none';
						break;
					
					case 'access':
						$sFilename = Registry::getValue('sb.system.log.access.file');
						break;
						
					case 'exceptions':
						$sFilename = Registry::getValue('sb.system.log.exceptions.file');
						break;
						
					case 'debug':
						$sFilename = CONFIG::LOGDIR.'debug.txt';
						break;
					
					default:
						throw new sbException(__CLASS__.': unknown log type ('.$_REQUEST->getParam('log').')');
				}
				echo '<html><body><pre>';
				if (!file_exists($sFilename)) {
					echo 'log file does not exist ('.$sFilename.')';	
				} else {
					echo (file_get_contents($sFilename));	
				}
				echo '</body></html>';
				break;
				
			default:
				throw new sbException(__CLASS__.': action not recognized ('.$sAction.')');
				
		}
	}
	
}

?>