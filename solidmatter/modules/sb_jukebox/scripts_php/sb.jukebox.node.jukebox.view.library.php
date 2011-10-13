<?php

//------------------------------------------------------------------------------
/**
* @package	solidMatter[sbJukebox]
* @author	()((() [Oliver Müller]
* @version	1.00.00
*/
//------------------------------------------------------------------------------

//------------------------------------------------------------------------------
/**
*/
class sbView_jukebox_jukebox_library extends sbJukeboxView {
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function execute($sAction) {
		
		global $_RESPONSE;
		
		switch ($sAction) {
			
			case 'info':
				
				// search form
				$formSearch = $this->buildSearchForm('jukebox');
				$formSearch->saveDOM();
				$_RESPONSE->addData($formSearch);
				
				// add latest played albums
				$this->addLatestPlayedAlbums();
				
				// add latest albums
				$this->addLatestAlbums();
				
				// add latest comments
				$this->addLatestComments();
				
				// add news
				$niNews = $this->nodeSubject->getChildren('comments');
				foreach ($niNews as $nodeNews) {
					$nodeNews->loadProperties();
				}
				$niNews->sortDescending('created');
				$niNews->crop(3);
				$_RESPONSE->addData($niNews, 'news');
				
				// add recommendations
				$this->addRecommendations($_RESPONSE);
				break;
				
			case 'search':
				$formSearch = $this->buildSearchForm('jukebox');
				$formSearch->recieveInputs();
				if ($formSearch->checkInputs()) {
					if ($_REQUEST->getParam('searchstring') == NULL) {
						return (NULL);
					}
					$sSearchString = '%'.$_REQUEST->getParam('searchstring').'%';
					if (true) { // search everything
						$stmtSearch = $this->nodeSubject->getSession()->prepareKnown('sbJukebox/jukebox/search/anything/byLabel');
						$stmtSearch->bindValue('jukebox_mpath', $this->nodeSubject->getMPath(), PDO::PARAM_STR);
						$stmtSearch->bindValue('searchstring', $sSearchString, PDO::PARAM_STR);
						$stmtSearch->execute();
						$_RESPONSE->addData($stmtSearch->fetchElements(), 'searchresult');
					}
				}
				$formSearch->saveDOM();
				$_RESPONSE->addData($formSearch);
				break;
			
			case 'displayCoverWall':
				// get all albums and store in response 
				$stmtGetAlbums = $this->nodeSubject->getSession()->prepareKnown('sbJukebox/jukebox/albums/getAll');
				$stmtGetAlbums->bindValue('jukebox_mpath', $this->nodeSubject->getMPath(), PDO::PARAM_STR);
				$stmtGetAlbums->execute();
				$_RESPONSE->addData($stmtGetAlbums->fetchElements(), 'allAlbums');
				// add default search form
				$formSearch = $this->buildSearchForm('jukebox');
				$formSearch->saveDOM();
				$_RESPONSE->addData($formSearch);
				break;
			
			default:
				throw new sbException(__CLASS__.': action not recognized ('.$sAction.')');
			
		}
		
	}
	
	//--------------------------------------------------------------------------
	/**
	 *
	 * @param
	 * @return
	 */
	protected function addLatestPlayedAlbums() {

		global $_RESPONSE;

		$iLimit = Registry::getValue('sb.jukebox.latestalbums.amount.default');
		if ($_REQUEST->getParam('expand') == 'latestPlayedAlbums') {
			$iLimit = Registry::getValue('sb.jukebox.latestalbums.amount.expanded');
		}
		$stmtGetLatest = $this->crSession->prepareKnown('sbJukebox/jukebox/albums/getLatestPlayed');
		$stmtGetLatest->bindValue('limit', (int) $iLimit, PDO::PARAM_INT);
		$stmtGetLatest->bindValue('user_uuid', $this->getPivotUUID(), PDO::PARAM_STR);
		$stmtGetLatest->execute();
		$_RESPONSE->addData($stmtGetLatest->fetchElements(), 'latestPlayedAlbums');
		
	}
	
	//--------------------------------------------------------------------------
	/**
	 *
	 * @param
	 * @return
	 */
	protected function addLatestAlbums() {
	
		global $_RESPONSE;

		$iLimit = Registry::getValue('sb.jukebox.latestalbums.amount.default');
		if ($_REQUEST->getParam('expand') == 'latestAlbums') {
			$iLimit = Registry::getValue('sb.jukebox.latestalbums.amount.expanded');
		}
		$stmtGetLatest = $this->crSession->prepareKnown('sbJukebox/jukebox/albums/getLatest');
		$stmtGetLatest->bindValue('jukebox_mpath', $this->nodeSubject->getMPath(), PDO::PARAM_STR);
		$stmtGetLatest->bindValue('limit', (int) $iLimit, PDO::PARAM_INT);
		$stmtGetLatest->bindValue('nodetype', 'sbJukebox:Album', PDO::PARAM_STR);
		$stmtGetLatest->bindValue('user_uuid', $this->getPivotUUID(), PDO::PARAM_STR);
		$stmtGetLatest->execute();
		$_RESPONSE->addData($stmtGetLatest->fetchElements(), 'latestAddedAlbums');
	
	}
	
	//--------------------------------------------------------------------------
	/**
	 *
	 * @param
	 * @return
	 */
	protected function addLatestComments() {
		
		global $_RESPONSE;

		$iLimit = Registry::getValue('sb.jukebox.latestcomments.amount.default');
		if ($_REQUEST->getParam('expand') == 'latestComments') {
			$iLimit = Registry::getValue('sb.jukebox.latestcomments.amount.expanded');
		}
		$stmtGetLatest = $this->crSession->prepareKnown('sbJukebox/jukebox/comments/getLatest');
		$stmtGetLatest->bindValue('jukebox_mpath', $this->nodeSubject->getMPath(), PDO::PARAM_STR);
		$stmtGetLatest->bindValue('jukebox_uuid', $this->nodeSubject->getProperty('jcr:uuid'), PDO::PARAM_STR);
		$stmtGetLatest->bindValue('limit', (int) $iLimit, PDO::PARAM_INT);
		$stmtGetLatest->execute();
		$_RESPONSE->addData($stmtGetLatest->fetchElements(), 'latestComments');
		
	}
	
	//--------------------------------------------------------------------------
	/**
	 *
	 * @param
	 * @return
	 */
	protected function addRecommendations(sbDOMResponse $domResponse) {

		$elemRecommendations = $domResponse->createElement('recommendations');
		$nodeUser = $this->crSession->getNodeByIdentifier(User::getUUID());
		$nodeInbox = $nodeUser->getNode('inbox');
		$nodeJukebox = $this->getJukebox();
		$niRecommendations = $nodeInbox->getChildren('recommendations');
		foreach ($niRecommendations as $nodeRecommendation) {
			//only list entries from this jukebox
			/*if (substr($nodeJukebox->getName(), 32) != substr($nodeRecommendation->getName(), 32)) {
			 continue;
			}*/
			// add relevant data
			$elemRecommendation = $domResponse->createElement('entry');
			try {
				$nodeSubject = $this->crSession->getNodeByIdentifier($nodeRecommendation->getProperty('subject'));
				$nodeRecommentor = $this->crSession->getNodeByIdentifier($nodeRecommendation->getProperty('jcr:createdBy'));
				$elemRecommendation->setAttribute('uuid', $nodeRecommendation->getProperty('jcr:uuid'));
				$elemRecommendation->setAttribute('comment', $nodeRecommendation->getProperty('comment'));
				$elemRecommendation->setAttribute('item_uuid', $nodeSubject->getProperty('jcr:uuid'));
				$elemRecommendation->setAttribute('label', $nodeSubject->getProperty('label'));
				$elemRecommendation->setAttribute('nodetype', $nodeSubject->getProperty('nodetype'));
				$elemRecommendation->setAttribute('username', $nodeRecommentor->getProperty('label'));
				$elemRecommendation->setAttribute('user_uuid', $nodeRecommentor->getProperty('jcr:uuid'));
			} catch (NodeNotFoundException $e) {
				$elemRecommendation->setAttribute('uuid', $nodeRecommendation->getProperty('jcr:uuid'));
				$elemRecommendation->setAttribute('comment', $nodeRecommendation->getProperty('comment'));
				$elemRecommendation->setAttribute('label', 'recommended object does not exist');
			}
			$elemRecommendations->appendChild($elemRecommendation);
		}
		$domResponse->addData($elemRecommendations);
		
	}
	
}

?>