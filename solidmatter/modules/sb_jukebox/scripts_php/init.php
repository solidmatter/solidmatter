<?php

//------------------------------------------------------------------------------
/**
* @package	solidMatter[sbJukebox]
* @author	()((() [Oliver Müller]
* @version	1.00.00
*/
//------------------------------------------------------------------------------

import('sbJukebox:sb.pdo.queries');

//------------------------------------------------------------------------------
/**
*/
class sbJukeboxView extends sbView {
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	protected function __init() {
		if ($this->getJukebox()->getProperty('config_islocked') == 'TRUE') {
			throw new sbException('this jukebox is currently locked');
		}
		$this->storeLibraryInfo();
		$this->storeNowPlaying();
		$this->storeCurrentPlaylist();
		parent::__init();
	}
	
	//--------------------------------------------------------------------------
	/**
	* FIXME: values from registry are not flushed when changed there (double caching!)
	* @param 
	* @return 
	*/
	public function storeLibraryInfo() {
		
		global $_RESPONSE;
		
		$nodeJukebox = $this->getJukebox();
		
		// check cache
		$sCacheKey = 'JBINFO:'.$nodeJukebox->getProperty('jcr:uuid');
		$cacheData = CacheFactory::getInstance('misc');
		if ($cacheData->exists($sCacheKey)) {
			$aData = $cacheData->loadData($sCacheKey);
			$_RESPONSE->addData($aData, 'library');
			return;
		}
		
		// query and build data array
		$stmtInfo = $this->crSession->prepareKnown('sbJukebox/jukebox/gatherInfo');
		$stmtInfo->bindValue('jukebox_mpath', $nodeJukebox->getMPath(), PDO::PARAM_STR);
		$stmtInfo->execute();
		
		foreach ($stmtInfo as $aRow) {
			$aData['albums'] = $aRow['n_numalbums'];
			$aData['artists'] = $aRow['n_numartists'];
			$aData['tracks'] = $aRow['n_numtracks'];
			$aData['playlists'] = $aRow['n_numplaylists'];
		}
		
		$aData['min_stars'] = Registry::getValue('sb.jukebox.voting.scale.min');
		$aData['max_stars'] = Registry::getValue('sb.jukebox.voting.scale.max');
		
		// store data
		$cacheData->storeData($sCacheKey, $aData);
		
		$_RESPONSE->addData($aData, 'library');
	
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	protected function storeNowPlaying() {
		$stmtClear = $this->crSession->prepareKnown('sbJukebox/nowPlaying/clear');
		$stmtClear->bindValue('seconds', Registry::getValue('sb.jukebox.nowplaying.refresh'), PDO::PARAM_INT);
		$stmtClear->execute();
		$stmtGet = $this->crSession->prepareKnown('sbJukebox/nowPlaying/get');
		$stmtGet->execute();
		global $_RESPONSE;
		$_RESPONSE->addData($stmtGet->fetchElements(), 'nowPlaying');
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	protected function storeCurrentPlaylist() {
		$sJukeboxUUID = $this->getJukebox()->getIdentifier();
		if (isset(sbSession::$aData['sbJukebox'][$sJukeboxUUID]['playlist'])) {
			$nodePlaylist = $this->crSession->getNodeByIdentifier(sbSession::$aData['sbJukebox'][$sJukeboxUUID]['playlist']);
			global $_RESPONSE;
			$_RESPONSE->addData($nodePlaylist, 'currentPlaylist');
		}
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function getJukebox() {
		// is subject node the jukebox?
		if ($this->nodeSubject->getPrimaryNodeType() == 'sbJukebox:Jukebox') {
			$nodeJukebox = $this->nodeSubject;
		} else {
			$nodeJukebox = $this->nodeSubject->getAncestorOfType('sbJukebox:Jukebox');
		}
		
		return ($nodeJukebox);
		
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function getPivotUUID() {
		$sJukeboxUUID = $this->getJukebox()->getProperty('jcr:uuid');
		if (!isset(sbSession::$aData['sbJukebox'][$sJukeboxUUID]['pivot'])) {
			if (Registry::getValue('sb.jukebox.voting.display.default') == 'average') {
				$sPivotUUID = $this->crSession->getRootNode()->getProperty('jcr:uuid');
			} else {
				$sPivotUUID = User::getUUID();
			}
			sbSession::$aData['sbJukebox'][$sJukeboxUUID]['pivot'] = $sPivotUUID;
		}
		return (sbSession::$aData['sbJukebox'][$sJukeboxUUID]['pivot']);
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function execute($sAction) {
		// nothing, has to be implemented in deriving class
		throw new sbException('method has to be implemented in deriving class!');
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	protected function buildCommentForm() {
		
		$formSearch = new sbDOMForm(
			'addComment',
			'$locale/sbSystem/labels/add_comment',
			System::getURL($this->nodeSubject, 'votes', 'addComment'),
			$this->crSession
		);
		
		$formSearch->addInput('comment;text;minlength=3;maxlength=2000;required=true;', '$locale/sbSystem/labels/comment');
		$formSearch->addSubmit('$locale/sbSystem/actions/save');
		
		return ($formSearch);
		
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	protected function buildTagForm() {
		
		$formTag = new sbDOMForm(
			'addTag',
			'$locale/sbSystem/labels/add_tag',
			System::getURL($this->nodeSubject, 'votes', 'addTag'),
			$this->crSession
		);
		
		$formTag->addInput('tag;string;minlength=2;maxlength=50;size=20;required=true;', '$locale/sbSystem/labels/tag');
		$formTag->addSubmit('$locale/sbSystem/actions/save');
		
		return ($formTag);
		
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	protected function buildSearchForm($sSubject) {
		
		switch ($sSubject) {
			
			case 'artists':
				$sID = 'searchArtists';
				$sTarget = System::getURL('-', 'artists', 'search');
				break;
			case 'albums':
				$sID = 'searchAlbums';
				$sTarget = System::getURL('-', 'albums', 'search');
				break;
			case 'jukebox':
				$sID = 'searchJukebox';
				$sTarget = System::getURL('-', 'library', 'search');
				break;
			case 'tracks':
				$sID = 'searchTracks';
				$sTarget = System::getURL('-', 'library', 'search');
				break;
			case 'tagspecific':
				$sID = 'searchTagSpecific';
				$sTarget = System::getURL('-', 'tags', 'listItems');
				break;
			default:
				throw new sbException('searchform subject not recognized: "'.$sSubject.'"');
			
		}
		
		$formSearch = new sbDOMForm(
			$sID,
			'$locale/sbSystem/labels/search/title',
			$sTarget,
			$this->crSession
		);
		
		$formSearch->addInput('searchstring;string;minlength=2;maxlength=20;', '$locale/sbSystem/labels/search/title');
		$formSearch->addSubmit('$locale/sbSystem/actions/search');
		
		// TODO: this behaviour is far from optimal, needs rework
		if ($sSubject == 'tagspecific') {
			$formSearch->addInput('tagid;hidden;');
			$formSearch->setValue('tagid', $_REQUEST->getParam('tagid'));
		}
		
		return ($formSearch);
		
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	function buildRelateForm() {
		
		$formRelate = new sbDOMForm(
			'addRelation',
			'$locale/sbSystem/labels/relate',
			System::getURL($this->nodeSubject, 'votes', 'addRelation'),
			$this->crSession
		);
		
		//$formRelate->addInput('relation;select;', '$locale/sbSystem/labels/relation');
		$formRelate->addInput('relation;relation;url=/'.$this->nodeSubject->getProperty('jcr:uuid').'/votes/getTargets;', '$locale/sbSystem/labels/comment');
		$formRelate->addSubmit('$locale/sbSystem/actions/save');
		
		$aRelations = $this->nodeSubject->getSupportedRelations();
		foreach ($aRelations as $sRelation => $unused) {
			$aOptions[$sRelation] = $sRelation;
		}
		$formRelate->setOptions('relation', $aOptions);
		
		return ($formRelate);
		
	}
	
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	protected function sendPlaylist($nodeSubject = NULL, $bRandom = FALSE) {
		import('sbJukebox:sb.jukebox.tools');
		$nodeJukebox = $this->getJukebox();
		if ($nodeSubject === NULL) {
			$nodeSubject = $this->nodeSubject;
		}
		$sName = $this->nodeSubject->getProperty('name');
		$sPlaylist = JukeboxTools::getPlaylist($nodeJukebox, $nodeSubject, $bRandom, 'M3U');
		headers('m3u', array(
			'filename' => $sName.'.m3u',
			'download' => false,
			'size' => strlen($sPlaylist),
		));
		echo $sPlaylist;
		exit();
	}
	
}

?>