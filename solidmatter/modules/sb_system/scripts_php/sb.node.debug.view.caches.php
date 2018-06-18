<?php

//------------------------------------------------------------------------------
/**
 * @package	solidMatter[sbSystem]
 * @author	()((() [Oliver Müller]
 * @version	1.00.00
 */
//------------------------------------------------------------------------------

import('sb.system.debug');

//------------------------------------------------------------------------------
/**
 */
class sbView_debug_caches extends sbView {
	
	//--------------------------------------------------------------------------
	/**
	 *
	 * @param
	 * @return
	 */
	public function execute($sAction) {
		
		global $_RESPONSE;
		
		switch ($sAction) {
			
			case 'display':
				
				break;
				
			default:
				throw new sbException(__CLASS__.': action not recognized ('.$sAction.')');
				
		}
	}
	
}

?>