<?php

import('sb.system.debug');

class sbView_debug_nodetypes extends sbView {
	
	public function execute($sAction) {
		
		switch ($sAction) {
			
			case 'display':
				$DB = DBFactory::getInstance('system');
				$stmtGetNodetypes = $DB->prepareKnown('sb_system/debug/gatherNodetypes');
				$stmtGetNodetypes->execute();
				$elemNodetypes = $stmtGetNodetypes->fetchElements('nodetypes');
				$stmtGetViews = $DB->prepareKnown('sb_system/debug/gatherViews');
				$stmtGetViews->execute();
				$elemViews = $stmtGetViews->fetchElements('views');
				$stmtGetViewsActions = $DB->prepareKnown('sb_system/debug/gatherViewActions');
				$stmtGetViewsActions->execute();
				$elemViewActions = $stmtGetViewsActions->fetchElements('viewactions');
				$_RESPONSE = ResponseFactory::getInstance('global');
				$_RESPONSE->addData($elemNodetypes);
				$_RESPONSE->addData($elemViews);
				$_RESPONSE->addData($elemViewActions);
				break;
				
			default:
				throw new sbException(__CLASS__.': action not recognized ('.$sAction.')');
				
		}
	}
	
}

?>