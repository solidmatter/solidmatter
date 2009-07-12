<?php

//------------------------------------------------------------------------------
/**
* @package	solidMatter:sb_system
* @author	()((() [Oliver Müller]
* @version	1.00.00
*/
//------------------------------------------------------------------------------

import('sb.dom.rss');
import('sbJukebox:sb.pdo.queries');

//------------------------------------------------------------------------------
/**
*/
class JBRSSHandler {
	
	protected $crSession = NULL;
	protected $nodeTrack = NULL;
	
	//--------------------------------------------------------------------------
	/**
	* 
	* Request URI Format:
	* http://<site>/rss/<type>/<tokenid>
	* 
	* <type> = comments|albums|mostplayed
	* 
	* @param 
	* @return 
	*/
	public function handleRequest($crSession) {
		
		global $_REQUEST;
		global $_RESPONSE;
		
		$this->crSession = $crSession;
		$sRSSType = NULL;
		$sTokenID = NULL;
		
		// parse request
		$aStuff = explode('/', $_REQUEST->getPath(), 5);
		if (isset($aStuff[1])) {
			// fixed: "rss"
		}
		if (isset($aStuff[2])) {
			$sRSSType = $aStuff[2];
		}
		if (isset($aStuff[3])) {
			$sTokenID = $aStuff[3];
		}
		if ($sRSSType === NULL || $sTokenID === NULL) {
			die('rss type or token missing');
		}
		
		$sUserID = $this->getTokenOwner($sTokenID);
		if (!$sUserID) {
			die('token is invalid');
		}
		User::setUUID($sUserID);
		
		// TODO: check permissions
		
		$domFeed = $this->getRSS($sRSSType);
		$this->refreshToken();
		$domFeed->outputXML();
		exit();
		
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	protected function getRSS($sType) {
		
		$nodeJukebox = $this->crSession->getRootNode()->getNode($_REQUEST->getSubject());
		
		switch ($sType) {
			
			case 'latestalbums':
				
				$aChannelInfo = array(
					'title' => $nodeJukebox->getProperty('label').' (latest albums)',
					'link' => 'http://'.$_REQUEST->getDomain().'/-/library',
					'description' => FALSE
				);
				$domFeed = new RSSFeed($aChannelInfo);
			
				$iLimit = 10;
				$stmtGetLatest = $this->crSession->prepareKnown('sbJukebox/jukebox/albums/getLatest');
				$stmtGetLatest->bindValue('jukebox_mpath', $nodeJukebox->getMPath(), PDO::PARAM_STR);
				$stmtGetLatest->bindValue('limit', (int) $iLimit, PDO::PARAM_INT);
				$stmtGetLatest->bindValue('nodetype', 'sbJukebox:Album', PDO::PARAM_STR);
				$stmtGetLatest->bindValue('user_uuid', $this->crSession->getRootNode()->getProperty('jcr:uuid'), PDO::PARAM_STR);
				$stmtGetLatest->execute();
				foreach ($stmtGetLatest as $aRow) {
					$aItemInfo = array(
						'title' => $aRow['label'],
						'link' => 'http://'.$_REQUEST->getDomain().'/'.$aRow['uuid'],
						//'description' => 'dummy',
						//'author' => 'sbJukebox',
						//'category' => FALSE,
						//'comments' => FALSE,
						//'enclosure' => FALSE,
						'guid' => $aRow['uuid'],
						'pubDate' => $aRow['created'],
						//'source' => FALSE
					);
					$domFeed->addItem($aItemInfo);	
				}
				break;
				
			case 'latestcomments':
				
				$aChannelInfo = array(
					'title' => $nodeJukebox->getProperty('label').' (latest comments)',
					'link' => 'http://'.$_REQUEST->getDomain().'/-/library',
					'description' => FALSE
				);
				$domFeed = new RSSFeed($aChannelInfo);
				
				$iLimit = 10;
				$stmtGetLatest = $this->crSession->prepareKnown('sbJukebox/jukebox/comments/getLatest');
				$stmtGetLatest->bindValue('jukebox_mpath', $nodeJukebox->getMPath(), PDO::PARAM_STR);
				$stmtGetLatest->bindValue('limit', (int) $iLimit, PDO::PARAM_INT);
				$stmtGetLatest->execute();
				foreach ($stmtGetLatest as $aRow) {
					$aItemInfo = array(
						'title' => $aRow['username'].': '.$aRow['item_label'],
						'link' => 'http://'.$_REQUEST->getDomain().'/'.$aRow['item_uuid'],
						//'description' => 'dummy',
						'author' => $aRow['username'],
						//'category' => FALSE,
						//'comments' => FALSE,
						//'enclosure' => FALSE,
						'guid' => $aRow['uuid'],
						'pubDate' => $aRow['created'],
						//'source' => FALSE
					);
					$domFeed->addItem($aItemInfo);	
				}
				break;
				
			case 'mostplayed':
				throw new LazyBastardException('to be implemented');
				break;
				
			default:
				throw new sbException('unknown rss type ('.$sType.')');
			
		}
		
		return ($domFeed);
		
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	protected function getTokenOwner($sTokenID) {
		$stmtClear = $this->crSession->prepareKnown('sbJukebox/tokens/clear');
		$stmtClear->execute();
		$stmtGetOwner = $this->crSession->prepareKnown('sbJukebox/tokens/get/byToken');
		$stmtGetOwner->bindValue('token', $sTokenID, PDO::PARAM_STR);
		$stmtGetOwner->execute();
		$sUserUUID = FALSE;
		foreach ($stmtGetOwner as $aRow) {
			$sUserUUID = $aRow['user_uuid'];
		}
		return ($sUserUUID);
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	protected function refreshToken() {
		$stmtRefresh = $this->crSession->prepareKnown('sbJukebox/tokens/refresh');
		$stmtRefresh->bindValue('user_uuid', User::getUUID(), PDO::PARAM_STR);
		$stmtRefresh->execute();
	}
	
}

?>