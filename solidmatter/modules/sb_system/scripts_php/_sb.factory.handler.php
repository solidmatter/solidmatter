<?php

//------------------------------------------------------------------------------
/**
*	@package solidMatter[sbSystem]
*	@subpackage Core
*	@author	()((() [Oliver Müller]
*	@version 1.00.00
*/
//------------------------------------------------------------------------------

//------------------------------------------------------------------------------
/**
*/
class RequestHandlerFactory {
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public static function getInstance($sAccessType) {
		
		switch ($sAccessType) {
			
			case 'backend':
				import('sb.handler.request.backend');
				$hndBackend = new BackendRequestHandler();
				return ($hndBackend);
				//break;
			
			case 'frontend':
				import('sb.handler.request.frontend');
				$hndFrontend = new FrontendRequestHandler();
				return ($hndFrontend);
				//break;
			
			case 'application':
				import('sb.handler.request.application');
				$hndFrontend = new ApplicationRequestHandler();
				return ($hndFrontend);
				//break;
				
			case 'xsl':
				import('sb.handler.request.xsl');
				$hndXSL = new XSLRequestHandler();
				return ($hndXSL);
				//break;
			
			default:
				throw new Exception();
				break;
			
			
		}	
		
	}
	
}

?>