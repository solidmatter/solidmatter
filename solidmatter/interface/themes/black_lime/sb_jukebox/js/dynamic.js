
//------------------------------------------------------------------------------
/**
*	@package solidMatter[sbJukebox]
*	@author	()((() [Oliver Müller]
*	@version 1.00.00
*/
//------------------------------------------------------------------------------

//------------------------------------------------------------------------------
/**
* 
*/
function add_playbutton(sUUID, oCover) {
	
	/*var ePlayButton = document.createElement('a');
	var ePlayImage = document.createElement('img');
	
	
	ePlayButton.setAttribute('href', '/' + sUUID + '/details/getM3U');
	ePlayImage.setAttribute('src', '/theme/sb_jukebox/icons/play.png');
	ePlayButton.setAttribute('style', 'position: absolute; top:0px; right:0;');
	
	ePlayButton.appendChild(ePlayImage);
	oCover.parentNode.insertBefore(ePlayButton, oCover.parentNode.firstChild);*/
	
}

//------------------------------------------------------------------------------
/**
* 
*/
function remove_playbutton(oCover) {
	
	//oCover.parentNode.removeChild(oCover.parentNode.firstChild);
	
}

//------------------------------------------------------------------------------
/**
* 
*/
function toggle_albumdetails(sAlbumUUID) {
	
	var oContainer = $('details_' + sAlbumUUID).parentNode;
	
	if (oContainer.style.display != 'none') {
		oContainer.style.display = 'none';
	} else {
		oContainer.style.display = 'table-row';
		if (!oContainer.loaded) {
			var sUrl = '/' + sAlbumUUID + '/details/displayInline';
			var sID = 'details_' + sAlbumUUID;
			var myAjaxOpener = new Ajax.Updater( 
				sID,
				sUrl,
				{
					method: 'get', 
					parameters: null 
				}
			);
			oContainer.loaded = true;
		}
	}
}