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
* 
* @param 
* @return 
*/
function display_tree_structure($sParentUUID = NULL) {
	
	if ($sParentUUID == NULL) {
		$nodeRoot = NodeFactory::getRootNode();
		$sParentUUID = $nodeRoot->getProperty('jcr:uuid');
	}
	
	$DB = DBFactory::getInstance('system');
	$stmtGetChildInfo = $DB->prepareKnown('sb_system/debug/gatherTree');
	$stmtGetChildInfo->bindParam('parent_uuid', $sParentUUID, PDO::PARAM_STR);
	$stmtGetChildInfo->execute();
	$aRows = $stmtGetChildInfo->fetchAll(PDO::FETCH_ASSOC);
	//var_dump($aRows);
	foreach ($aRows as $aRow) {
		echo '<li>'.$aRow['n_level'].'.'.$aRow['n_order'].' '.$aRow['s_name'].' ('.$aRow['s_mpath'].', ';
		if ($aRow['n_numchildren'] != 0) {
			echo '<ul>';
			display_tree_structure($aRow['uuid']);
			echo '</ul>';
		}
		echo '</li>';
	}
	
}

//------------------------------------------------------------------------------
/**
* NOTE: don't use this right now
* @param 
* @return 
*/
function get_nodetype_infos() {
	
	$aStructure = array();
	$DB = DBFactory::getInstance('system');
	/*$stmtGetNodetypes = $DB->prepareKnown('sb_system/debug/gatherNodetypes');
	$stmtGetNodetypes->execute();
	while ($aRow = $stmtGetNodetypes->fetch(PDO::FETCH_ASSOC)) {
		$aStructure[$aRow['s_type']] = $aRow;
	}
	$stmtGetViews = $DB->prepareKnown('sb_system/debug/gatherViews');
	$stmtGetViews->execute();
	while ($aRow = $stmtGetViews->fetch(PDO::FETCH_ASSOC)) {
		$aStructure[$aRow['fk_nodetype']]['views'][$aRow['s_view']] = $aRow;
	}
	$stmtGetViews = $DB->prepareKnown('sb_system/debug/gatherViewActions');
	$stmtGetViews->execute();
	while ($aRow = $stmtGetViews->fetch(PDO::FETCH_ASSOC)) {
		$aStructure[$aRow['fk_nodetype']]['views'][$aRow['s_view']]['actions'][$aRow['s_action']] = $aRow;
	}*/
	$stmtGetNodetypes = $DB->prepareKnown('sb_system/debug/gatherNodetypes');
	$stmtGetNodetypes->execute();
	$aStructure['nodetypes'] = $stmtGetNodetypes->fetchAll(PDO::FETCH_ASSOC);
	$stmtGetViews = $DB->prepareKnown('sb_system/debug/gatherViews');
	$stmtGetViews->execute();
	$aStructure['views'] = $stmtGetViews->fetchAll(PDO::FETCH_ASSOC);
	$stmtGetViewsActions = $DB->prepareKnown('sb_system/debug/gatherViewActions');
	$stmtGetViewsActions->execute();
	$aStructure['viewactions'] = $stmtGetViewsActions->fetchAll(PDO::FETCH_ASSOC);
	return ($aStructure);
	
}



?>