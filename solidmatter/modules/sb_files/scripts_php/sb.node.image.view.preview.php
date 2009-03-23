<?php

//------------------------------------------------------------------------------
/**
* @package	solidMatter[sbSystem]
* @author	()((() [Oliver Müller]
* @version	1.00.00
*/
//------------------------------------------------------------------------------

//------------------------------------------------------------------------------
/**
*/
class sbView_image_preview extends sbView {
	
	public function execute($sAction) {
		
		global $_RESPONSE;
		
		switch ($sAction) {
				
			case 'display':
				break;
			
			case 'outputoriginal':
				// generate image
				$sImageData = $this->nodeSubject->loadBinaryProperty('properties_content', TRUE);
				$imgCurrent = new Image(Image::FROMSTRING, $sImageData);
				// output
				$imgCurrent->output(JPG);
				break;
			
			case 'outputprocessed':
			case 'output':
				
				import('sb.image');
				$sFilterUUID = $this->nodeSubject->getProperty('config_filterstack');
				
				// NOTE: test
				if ($sFilterUUID == '') {
					$sImageData = $this->nodeSubject->loadBinaryProperty('properties_content', TRUE);
					$sMimetype = $this->nodeSubject->getProperty('properties_mimetype');
					if ($sMimetype != 'unknown/unknown') {
						header('Content-type: '.$sMimetype);	
					}
					echo $sImageData;
					exit();
				}
				
				// cache load
				if (Registry::getValue('sb.system.cache.images.enabled')) {
					$cacheImages = CacheFactory::getInstance('images');
					$sImageData = $cacheImages->loadImage(
						$this->nodeSubject->getProperty('jcr:uuid'), 
						$sFilterUUID,
						'full'
					);
					if ($sImageData) {
						$imgCurrent = new Image(Image::FROMSTRING, $sImageData);
						$imgCurrent->output(JPG);
					}
				}
				
				// generate image
				$sImageData = $this->nodeSubject->loadBinaryProperty('properties_content', TRUE);
				$imgCurrent = new Image(Image::FROMSTRING, $sImageData);
				if ($sFilterUUID != '') {
					$nodeFilter = $this->getNode($sFilterUUID);
					$nodeFilter->applyToImage($imgCurrent);
				}
				
				// cache store
				if (Registry::getValue('sb.system.cache.images.enabled')) {
					$cacheImages = CacheFactory::getInstance('images');
					$cacheImages->storeImage(
						$this->nodeSubject->getProperty('jcr:uuid'),
						$sFilterUUID,
						'full',
						$imgCurrent->getData()
					);
				}
				
				// output
				$imgCurrent->output(JPG);
				
				break;
			
			case 'outputresized':
				
				import('sb.image');
				$sFilterUUID = $this->nodeSubject->getProperty('config_filterstack');
				
				// cache load
				if (Registry::getValue('sb.system.cache.images.enabled')) {
					$cacheImages = CacheFactory::getInstance('images');
					$sImageData = $cacheImages->loadImage(
						$this->nodeSubject->getProperty('jcr:uuid'), 
						$sFilterUUID,
						'explorer'
					);
					if ($sImageData) {
						$imgCurrent = new Image(Image::FROMSTRING, $sImageData);
						$imgCurrent->output(JPG);
					}
				}
				
				// generate image
				$sImageData = $this->nodeSubject->loadBinaryProperty('properties_content', TRUE);
				$imgCurrent = new Image(Image::FROMSTRING, $sImageData);
				if ($sFilterUUID != '') {
					$nodeFilter = $this->crSession->getNode($sFilterUUID);
					$nodeFilter->applyToImage($imgCurrent);
				}
				
				$iMode = Image::DOWNSAMPLE;
				if (Registry::getValue('sb.files.explorer.image.alwaysfit')) {
					$iMode |= Image::UPSAMPLE;
				}
				$imgCurrent->resample(140, 140, Image::KEEPASPECT, $iMode);
				
				// cache store
				if (Registry::getValue('sb.system.cache.images.enabled')) {
					$cacheImages = CacheFactory::getInstance('images');
					$cacheImages->storeImage(
						$this->nodeSubject->getProperty('jcr:uuid'), 
						$sFilterUUID,
						'explorer',
						$imgCurrent->getData()
					);
				}
				
				// output
				$imgCurrent->output(JPG);
				
				break;
				
			default:
				throw new sbException(__CLASS__.': action not recognized ('.$sAction.')');
			
		}
		
		return ($this->nodeSubject);
		
	}
	
}


?>