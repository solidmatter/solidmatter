<?php

//------------------------------------------------------------------------------
/**
* @package	solidMatter[sbJukebox]
* @author	()((() [Oliver Müller]
* @version	1.00.00
*/
//------------------------------------------------------------------------------

import('sb.tools.filesystem');
import('sb.tools.mime');

//------------------------------------------------------------------------------
/**
*/
class sbView_jukebox_album_details extends sbJukeboxView {
	
	protected $aRequiredAuthorisations = array(
		'display' => array('read'),
		'getM3U' => array('read'),
		'download' => array('download'),
		'getCover' => array('read'),
		'buildQuilt' => array('read'),
	);
	
	public function execute($sAction) {
		
		global $_RESPONSE;
		$this->checkRequirements($sAction);
		
		switch ($sAction) {
			
			case 'display':
				
				// return playlist if user agent is a mediaplayer
				// FIXME: doesn't work because winamp expects a *.m3u filename
				/*$sUserAgent = $_REQUEST->getParam('HTTP_USER_AGENT', 'SERVER');
				$sPattern = '/^(Winamp|NSPlayer|Windows-Media-Player)(.*)/';
				DEBUG('sbJukebox:album:display', 'UserAgent='.$sUserAgent);
				if (preg_match($sPattern, $sUserAgent)) {
					$this->execute('getM3U');
				}*/
				
				// search form
				$formSearch = $this->buildSearchForm('albums');
				$formSearch->saveDOM();
				$_RESPONSE->addData($formSearch);
				
				// comment form
				$formComment = $this->buildCommentForm();
				$formComment->saveDOM();
				$_RESPONSE->addData($formComment);
				
				// add tracks
				$niTracks = $this->nodeSubject->loadChildren('tracks', TRUE, TRUE, TRUE);
				foreach ($niTracks as $nodeTrack) {
					$nodeTrack->getVote($this->getPivotUUID());
				}
				
				$niComments = $this->nodeSubject->loadChildren('comments', TRUE, TRUE, TRUE);
				foreach ($niComments as $nodeComment) {
					// TODO: check user existence, might be deleted
					$nodeUser = $this->crSession->getNodeByIdentifier($nodeComment->getProperty('jcr:createdBy'));
					$nodeComment->setAttribute('username', $nodeUser->getProperty('label'));
				}
				$this->nodeSubject->storeChildren();
				
				$this->nodeSubject->loadProperties();
				$this->nodeSubject->getTags();
				
				// add vote
				$this->nodeSubject->getVote(User::getUUID());
				
				return;
				
			case 'getM3U':
				$sName = $this->nodeSubject->getProperty('name');
				$sPlaylist = $this->getPlaylist($this->nodeSubject, FALSE, 'M3U');
				headers('m3u', array(
					'filename' => $sName.'.m3u',
					'download' => false,
					'size' => strlen($sPlaylist),
				));
				echo $sPlaylist;
				exit();
			
			case 'download':
				import('sb_system:external:pclzip/pclzip.lib');
				$sTempFile = Registry::getValue('sb.system.temp.dir').'/'.$this->nodeSubject->getProperty('name').'.zip';
				// CAUTION: PCLZip (2.6) needs a modification because it strips away the drive letter part!!!
				$zipAlbum = new PclZip($sTempFile);
				$aFileList = $this->getDownloadItems($this->nodeSubject);
				$zipAlbum->create($aFileList, PCLZIP_OPT_REMOVE_ALL_PATH, PCLZIP_OPT_NO_COMPRESSION);
				
//				var_dumpp($aFileList);
//				var_dumpp($sTempFile);
//				var_dumpp($zipAlbum);
//				die();
				
				$aOptions = array();
				$aOptions['filename'] = $this->nodeSubject->getProperty('name').'.zip';
				headers('download', $aOptions);
				$hTempFile = fopen($sTempFile, 'r');
				fpassthru($hTempFile);
				// this loop is necessary because it can take some time to close the handle
				while (file_exists($sTempFile)) {
					fclose($hTempFile);
					unlink($sTempFile);
				}
				exit();
				break;
			
			case 'getCover':
				parent::getCover($this->nodeSubject);
				break;
			
			case 'buildQuilt':
			
				// search form
				$formSearch = $this->buildSearchForm('albums');
				$formSearch->saveDOM();
				$_RESPONSE->addData($formSearch);
				
				// basic init
				$nodeJukebox = $this->getJukebox();
				
				// check cache first
				$cacheQuilts = CacheFactory::getInstance('misc');
				$sCacheKey = 'JBQUILT:'.$this->nodeSubject->getIdentifier();
				if ($aQuilt = $cacheQuilts->loadData($sCacheKey)) { // already rendered 
					
					// nothing to do, dom elements are created later
					
				} else { // render based on image
				
					// init rendering
					$iColumns = 35;
					$iRows = 35;
					$iTolerance = 10;
					$iNumSamples = 100;
					$imgCover = new Image(Image::FROMFILE, $this->getCoverFilename($this->nodeSubject));
					
					$aQuilt = array();
					for ($i=0; $i<$iRows; $i++) {
						for ($j=0; $j<$iColumns; $j++) {
							$aHSL = $imgCover->getHSL($iNumSamples, $iColumns, $iRows, $j, $i);
							$stmtFindCover = $this->crSession->prepareKnown('sbJukebox/album/quilt/findCover');
							$stmtFindCover->bindValue(':hue', $aHSL['h'], PDO::PARAM_INT);
							$stmtFindCover->bindValue(':saturation', $aHSL['s'], PDO::PARAM_INT);
							$stmtFindCover->bindValue(':lightness', $aHSL['l'], PDO::PARAM_INT);
							//$stmtFindCover->bindValue(':tolerance', $iTolerance, PDO::PARAM_INT);
							$stmtFindCover->bindValue(':jukebox_uuid', $nodeJukebox->getIdentifier(), PDO::PARAM_STR);
							$stmtFindCover->execute();
							foreach ($stmtFindCover as $aRow) {
								$aQuilt[$i][$j]['uuid'] = $aRow['uuid'];
								$aQuilt[$i][$j]['label'] = $aRow['label'];
							}
							$aQuilt[$i][$j]['h'] = $aHSL['h'];
							$aQuilt[$i][$j]['s'] = $aHSL['s'];
							$aQuilt[$i][$j]['l'] = $aHSL['l'];
						}
					}
					// store in cache
					$cacheQuilts->storeData($sCacheKey, $aQuilt);
				}
				
				// build quilt dom
				$elemQuilt = $_RESPONSE->createElement('quilt');
				foreach ($aQuilt as $aRow) {
					$elemRow = $_RESPONSE->createElement('row');
					foreach ($aRow as $aColumn) {
						$elemColumn = $_RESPONSE->createElement('column');
						$elemColumn->setAttribute('uuid', $aColumn['uuid']);
						$elemColumn->setAttribute('label', $aColumn['label']);
						$elemColumn->setAttribute('hue', $aColumn['h']);
						$elemColumn->setAttribute('saturation', $aColumn['s']);
						$elemColumn->setAttribute('lightness', $aColumn['l']);
						$elemRow->appendChild($elemColumn);
					}
					$elemQuilt->appendChild($elemRow);
				}
				
				$_RESPONSE->addData($elemQuilt);
				break;
			
			default:
				throw new sbException(__CLASS__.': action not recognized ('.$sAction.')');
				
		}
		
	}
	
}

?>