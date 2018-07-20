<?php

//------------------------------------------------------------------------------
/**
*	@package solidMatter
*	@subpackage sbPDO
*	@author	()((() [Oliver Müller]
*	@version 1.00.00
*/
//------------------------------------------------------------------------------

import('sb.pdo');
import('sb.pdo.system.queries');
import('sb.system.errors');

//------------------------------------------------------------------------------
/**
* 
*/
class sbPDOSystem extends sbPDO {
	
	//--------------------------------------------------------------------------
	/**
	 *
	 * @param
	 * @return
	 */
	public function __construct(string $sDatabaseID) {
		
		$elemDB = CONFIG::getDatabaseConfig($sDatabaseID);
		$sDSN = 'mysql:host='.$elemDB['host'].';port='.$elemDB['port'].';dbname='.$elemDB['schema'];
		parent::__construct($sDSN, $elemDB['user'], $elemDB['pass']);
		if (isset($elemDB->log)) {
			if ((string) $elemDB->log['enabled'] == 'true') {
				$this->bLogEnabled = TRUE;
				if ((string) $elemDB->log['verbose'] == 'true') {
					$this->bLogVerbose = TRUE;
				}
			}
			if (!CONFIG::LOGDIR_ABS) { // log directory is not absolute path
				$this->sLogFile = System::getDir().'/'.CONFIG::LOGDIR.$elemDB->log['file'];
			} else {
				$this->sLogFile = CONFIG::LOGDIR.$elemDB->log['file'];
			}
			$this->sLogSize = $elemDB->log['size'];
			if ($this->bLogEnabled) {
				$this->lgLog = new Logger(get_class($this), (string) $elemDB->log['file']);
				$this->log('Database "'.$sDatabaseID.'" connected to schema "'.$elemDB['schema'].'" in "'.$elemDB['host'].':'.$elemDB['port'].'"');
			}
		}
		
		$this->query('SET NAMES '.$elemDB['charset']);
		
		$this->addRewrite('{PREFIX_SYSTEM}', 'global');
		
	}
	
}

?>