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
	
	protected $nodeJukebox;
	
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
			
		} else {
		
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
			
			$cacheData->storeData($sCacheKey, $aData);
			
		}
		
		// user token
		if (User::isLoggedin()) {
			$sJukeboxUUID = $nodeJukebox->getProperty('jcr:uuid');
			if (!isset(sbSession::$aData['sbJukebox'][$sJukeboxUUID]['usertoken'])) {
				import('sbJukebox:sb.jukebox.tools');
				$sTokenUUID = JukeboxTools::getToken();
				sbSession::$aData['sbJukebox'][$sJukeboxUUID]['usertoken'] = $sTokenUUID;
			}
			$aData['usertoken'] = sbSession::$aData['sbJukebox'][$sJukeboxUUID]['usertoken'];
		}
		
		// add volatile data
		$aData['minstars'] = Registry::getValue('sb.jukebox.voting.scale.min');
		$aData['maxstars'] = Registry::getValue('sb.jukebox.voting.scale.max');
		$aData['votingstyle'] = Registry::getValue('sb.jukebox.voting.style');
		$aData['adminmode'] = Registry::getValue('sb.jukebox.adminmode.enabled');
		$aData['quiltcovers'] = Registry::getValue('sb.jukebox.quilts.coveramount');
		$aData['quiltcoversize'] = Registry::getValue('sb.jukebox.quilts.coversize');
		
		
		// store in response
		foreach ($aData as $sKey => $sValue) {
			$_RESPONSE->addMetadata('sb_jukebox', $sKey, $sValue);
		}
	
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
		
		if ($this->nodeJukebox == NULL) { 
			// is subject node the jukebox?
			if ($this->nodeSubject->getPrimaryNodeType() == 'sbJukebox:Jukebox') {
				$this->nodeJukebox = $this->nodeSubject;
			} else {
				$this->nodeJukebox = $this->nodeSubject->getAncestorOfType('sbJukebox:Jukebox');
			}
		}
		
		return ($this->nodeJukebox);
		
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
	protected function addCommentForm() {
		
		if (!Registry::getValue('sb.jukebox.comments.enabled')) {
			return (FALSE);
		}
		if (!User::isAuthorised('comment', $this->nodeSubject)) {
			return (FALSE);
		}
		
		$formComment = $this->buildCommentForm();
		$formComment->saveDOM();
		global $_RESPONSE;
		$_RESPONSE->addData($formComment);
		
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
			System::getRequestURL($this->nodeSubject, 'votes', 'addComment'),
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
	protected function addComments() {
		
		if (!Registry::getValue('sb.jukebox.comments.enabled')) {
			return (FALSE);
		}
		
		$niComments = $this->nodeSubject->loadChildren('comments', TRUE, TRUE, TRUE);
		foreach ($niComments as $nodeComment) {
			// TODO: check user existence, might be deleted
			$nodeUser = $this->crSession->getNodeByIdentifier($nodeComment->getProperty('jcr:createdBy'));
			$nodeComment->setAttribute('username', $nodeUser->getProperty('label'));
		}
		
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	protected function addTagForm() {
		
		if (!User::isAuthorised('tag', $this->nodeSubject)) {
			return (FALSE);
		}
		
		$formTag = $this->buildTagForm();
		$formTag->saveDOM();
		global $_RESPONSE;
		$_RESPONSE->addData($formTag);
		
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
			System::getRequestURL($this->nodeSubject, 'votes', 'addTag'),
			$this->crSession
		);
		
		$sAutocompleteURL = System::getRequestURL($this->nodeSubject, 'votes', 'getTags');
		$formTag->addInput('tag;autocomplete;minchars=2;minlength=2;maxlength=50;size=20;required=true;url='.$sAutocompleteURL, '$locale/sbSystem/labels/tag');
		$formTag->addSubmit('$locale/sbSystem/actions/save');
		
		return ($formTag);
		
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	protected function addSearchForm($sSubject) {
		
		$formSearch = $this->buildSearchForm($sSubject);
		$formSearch->saveDOM();
		global $_RESPONSE;
		$_RESPONSE->addData($formSearch);
		
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	protected function buildSearchForm($sSubject) {
		
		$sSubjectNode = '-';
		if ($_REQUEST->getHandler() != 'application') {
			$sSubjectNode = $this->getJukebox()->getProperty('jcr:uuid');
		}
		
		switch ($sSubject) {
			
			case 'artists':
				$sID = 'searchArtists';
				$sTarget = System::getRequestURL($sSubjectNode, 'artists', 'search');
				break;
			case 'albums':
				$sID = 'searchAlbums';
				$sTarget = System::getRequestURL($sSubjectNode, 'albums', 'search');
				break;
			case 'jukebox':
				$sID = 'searchJukebox';
				$sTarget = System::getRequestURL($sSubjectNode, 'library', 'search');
				break;
			case 'tracks':
				$sID = 'searchTracks';
				$sTarget = System::getRequestURL($sSubjectNode, 'library', 'search');
				break;
			case 'tagspecific':
				$sID = 'searchTagSpecific';
				$sTarget = System::getRequestURL($sSubjectNode, 'tags', 'listItems');
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
	protected function addRelateForm() {
		
		if (!User::isAuthorised('relate', $this->nodeSubject)) {
			return (FALSE);
		}
	
		$formRelate = $this->buildRelateForm();
		$formRelate->saveDOM();
		global $_RESPONSE;
		$_RESPONSE->addData($formRelate);
		
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
			System::getRequestURL($this->nodeSubject, 'votes', 'addRelation'),
			$this->crSession
		);
		
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