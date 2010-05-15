
//------------------------------------------------------------------------------
/**
*	@package solidMatter[sbJukebox]
*	@author	()((() [Oliver Müller]
*	@version 1.00.00
*/
//------------------------------------------------------------------------------

var sStarURL = "/theme/sb_jukebox/images/star_set_normal.png";
var sStarSmallURL = "/theme/sb_jukebox/images/star_set_small.png";
var sStarSmallerURL = "/theme/sb_jukebox/images/star_set_smaller.png";
var sCrapURL = "/theme/sb_jukebox/images/star_crap_normal.png";
var sCrapSmallURL = "/theme/sb_jukebox/images/star_crap_small.png";
var sCrapSmallerURL = "/theme/sb_jukebox/images/star_crap_smaller.png";
var sDotURL = "/theme/sb_jukebox/images/star_dot.png";
var sHighlightURL = '/theme/sb_jukebox/images/star_select.png';

var sDotHTML = '<img src="' + sDotURL + '" alt="star unset"  style="padding-right: 1px;" onMouseOver="highlight_star(this, true)" onMouseOut="highlight_star(this, false)" onClick="vote(this)" />';

var aStarDefinition = new Array();
var iTotalStars = 0;

//------------------------------------------------------------------------------
/**
* init star display
*/
function init_stars() {
	
	iTotalStars = iMaxStars - iMinStars + 1;
	
	for (var i=0; i<Math.abs(iMinStars); i++) {
		aStarDefinition[i] = new Object();
		aStarDefinition[i]['vote'] = Math.round( i * 50 / (Math.abs(iMinStars)) );
	}
	
	for (var i=0; i<=iMaxStars; i++) {
		aStarDefinition[i+Math.abs(iMinStars)] = new Object();
		aStarDefinition[i+Math.abs(iMinStars)]['vote'] = Math.round( 50 + i * 50 / (iMaxStars) );
	}
	
}

//------------------------------------------------------------------------------
/**
* display voting starset
*/
function render_stars(sVote, sUUID, bVotingEnabled) {
	
	for (var i=0; i<iTotalStars; i++) {
		document.write(sDotHTML);
	}
	
	var oContainer = document.getElementById('stars_' + sUUID);
	update_stars(oContainer, sVote);
	
}

//------------------------------------------------------------------------------
/**
* updates a star set
*/
function update_stars(oStarContainer, sVote) {
	
	if (sVote == "") {
		var iVote = -1;
	} else {
		var iVote = parseInt(sVote);
	}
	
	for (var i=0; i<iTotalStars; i++) {
		oStarContainer.childNodes[i+1].src = getStarURL(iVote, i);
		oStarContainer.childNodes[i+1].src_orig = oStarContainer.childNodes[i+1].src;
	}

}

//------------------------------------------------------------------------------
/**
* returns the correct star image based on type and step fraction
*/
function getStarURL(iVote, iPosition) {
	
	if (sVotingStyle == 'HOTEL' || iVote >= 37) {
		var sType = 'good';
	} else {
		//var sType = aStarDefinition[iPosition]['type'];
		var sType = 'bad';
	}
	
	if (iVote == -1) {
		return (sDotURL);
	}
	
	if (iPosition == 0) {
		if (sType == 'good') {
			return (sStarURL);
		} else {
			return (sCrapURL);
		}
	}
	
	var iRange = aStarDefinition[iPosition]['vote'] - aStarDefinition[iPosition-1]['vote'];
	var iModulo = iVote - aStarDefinition[iPosition-1]['vote'];
	
	if (iModulo <= iRange / 8) {
		return (sDotURL);
	} else if (iModulo <= iRange / 8 * 3) {
		if (sType == 'good') {
			return (sStarSmallerURL);
		} else {
			return (sCrapSmallerURL);
		}
	} else if (iModulo <= iRange / 8 * 5) {
		if (sType == 'good') {
			return (sStarSmallURL);
		} else {
			return (sCrapSmallURL);
		}
	} else {
		if (sType == 'good') {
			return (sStarURL);
		} else {
			return (sCrapURL);
		}
	}
	
}

//------------------------------------------------------------------------------
/**
* highlights or resets all stars under and left to mouse hover 
*/
function highlight_star(oStarImage, bEnable) {
	
	if (bEnable) {
		oStarImage.src_orig = oStarImage.src;
		oStarImage.src = sHighlightURL;
	} else {
		oStarImage.src = oStarImage.src_orig;
	}
	
	if (oStarImage.previousSibling != null) {
		highlight_star(oStarImage.previousSibling, bEnable);
	}
}

//------------------------------------------------------------------------------
/**
* casts a vote on a node
*/
function vote(oStarImage) {
	
	var oStarContainer = oStarImage.parentNode;
	
	var sID = oStarContainer.getAttribute('id').toString();
	sUUID = sID.replace(/stars_/g, '');
	
	var iSelectedStar = 0;
	var oCurrentStar = oStarImage;
	while (oCurrentStar.previousSibling != null) {
		iSelectedStar++;
		oCurrentStar = oCurrentStar.previousSibling;
	}
	
	var iVote = aStarDefinition[iSelectedStar-1]['vote'];
	
	var sURL = '/' + sUUID + '/votes/placeVote/?vote=' + iVote;
	var myAjaxVoter = new Ajax.Request( 
		sURL, 
		{
			method: 'get', 
			parameters: null,
			asynchronous: false,
			onComplete: function(response) {
    			update_stars(oStarContainer, response.getHeader('X-sbVote'));
    		}
		}
	);
	
}

//------------------------------------------------------------------------------
/**
* display times played dots
*/
function render_timesplayed(sTimesPlayed, iMaxDots) {
	
	if (sTimesPlayed == 'NaN') {
		var iTimesPlayed = 0;
	} else {
		var iTimesPlayed = parseInt(sTimesPlayed);
		}
	
	var sDotHTML = '|';
	var sNoDotHTML = '';
	
	for (var i=0; i<iMaxDots; i++) {
		if (i < iTimesPlayed) {
			document.write(sDotHTML);
		} else {
			document.write(sNoDotHTML);
		}
	}
	
}
