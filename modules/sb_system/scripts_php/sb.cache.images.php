<?php

//------------------------------------------------------------------------------
/**
*	@package solidMatter[sbSystem]
*	@subpackage Cache
*	@author	()((() [Oliver Müller]
*	@version 1.00.00
*/
//------------------------------------------------------------------------------

//------------------------------------------------------------------------------
/**
*	This class is used to wrap the image caching mechanisms.
*	Currently it uses a workspace-based table in the database.
*/
class ImageCache {
	
	//--------------------------------------------------------------------------
	/**
	* Caches an image along with information on the currently used filterstack.
	* TODO: check for failures, only true is returned for now
	* TODO: the temporary file created cannot be deleted for some ridiculous 
	* reason, needs to be fixed
	* @param string the uuid of the image node
	* @param string the uuid of the currently active filterstack node (can have 
	* other values with custom mode)
	* @param string the mode to use ("full" = whole image, "explorer" = explorer 
	* view thumbnail, "custom" = any other sort of storage)
	* @param resource the image's binary data in gd2 format 
	* @return boolean true on success, false otherwise
	*/
	public function storeImage($sImageUUID, $sFilterstackUUID, $sMode, $imgData) {
		
		$sTempFile = Registry::getValue('sb.system.temp.dir').'/image_'.uuid().'.gd2';
		imagegd2($imgData, $sTempFile);
		$fpImage= fopen($sTempFile, 'rb');
		
		$stmtStore = System::getDatabase()->prepareKnown('sb_system/cache/images/store');
		$stmtStore->bindValue('image', $sImageUUID, PDO::PARAM_STR);
		$stmtStore->bindValue('filterstack', $sFilterstackUUID, PDO::PARAM_STR);
		$stmtStore->bindValue('mode', $sMode, PDO::PARAM_STR);
		$stmtStore->bindValue('content', $fpImage, PDO::PARAM_LOB);
		$stmtStore->execute();
		$stmtStore->closeCursor();
		
		// this loop is necessary because it can take some time to close the handle
		while (file_exists($sTempFile)) {
			fclose($fpImage);
			unlink($sTempFile);
		}
		
		return (TRUE);
	}
	
	//--------------------------------------------------------------------------
	/**
	* Loads an image from the cache, based on desired state (filterstack and mode).
	* @param string the uuid of the image node
	* @param string the uuid of the currently active filterstack node (can have 
	* other values with custom mode)
	* @param string the mode to use ("full" = whole image, "explorer" = explorer 
	* view thumbnail, "custom" = any other sort of storage)
	* @return resource the image's binary data in gd2 format if retrieval was 
	* successful, false otherwise
	*/
	public function loadImage($sImageUUID, $sFilterstackUUID, $sMode) {
		$stmtLoad = System::getDatabase()->prepareKnown('sb_system/cache/images/load');
		$stmtLoad->bindValue('image', $sImageUUID, PDO::PARAM_STR);
		$stmtLoad->bindValue('filterstack', $sFilterstackUUID, PDO::PARAM_STR);
		$stmtLoad->bindValue('mode', $sMode, PDO::PARAM_STR);
		$stmtLoad->execute();
		$mContent = FALSE;
		foreach ($stmtLoad as $aRow) {
			$mContent = $aRow['m_content'];
		}
		$stmtLoad->closeCursor();
		return ($mContent);
	}
	
	//--------------------------------------------------------------------------
	/**
	* Removes all cached states of an image, needs to be called if the image itself or it's visual properties have changed.
	* Usually this is the case if the image's binary data has been replaced or a 
	* different filterstack is applied. 
	* @param string the uuid of the image node
	*/
	public function clearImage($sSubjectUUID) {
		$stmtClear = System::getDatabase()->prepareKnown('sb_system/cache/images/clear/byImage');
		$stmtClear->bindParam('image', $sSubjectUUID, PDO::PARAM_STR);
		$stmtClear->execute();
		$stmtClear->closeCursor();
	}
	
	//--------------------------------------------------------------------------
	/**
	* Removes all cached states of images that have been stored with a specific 
	* filterstack.
	* This method needs to be called when a filterstack has changed.
	* @param string the uuid of the filterstack node
	*/
	public function clearFilterstack($sSubjectUUID) {
		$stmtClear = System::getDatabase()->prepareKnown('sb_system/cache/images/clear/byFilterstack');
		$stmtClear->bindParam('filterstack', $sSubjectUUID, PDO::PARAM_STR);
		$stmtClear->execute();
		$stmtClear->closeCursor();
	}
	
	//--------------------------------------------------------------------------
	/**
	* Purges the whole cache, regardless of states.
	*/
	public function clearAll() {
		$stmtClear = System::getDatabase()->prepareKnown('sb_system/cache/images/empty');
		$stmtClear->execute();
		$stmtClear->closeCursor();
	}
	
}

?>