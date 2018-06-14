
//------------------------------------------------------------------------------
/**
*	@package solidMatter[sbJukebox]
*	@author	()((() [Oliver Müller]
*	@version 1.00.00
*/
//------------------------------------------------------------------------------

//var sStarURL = "/theme/sb_jukebox/images/star_set_normal.png";
//var sStarSmallURL = "/theme/sb_jukebox/images/star_set_small.png";
//var sStarSmallerURL = "/theme/sb_jukebox/images/star_set_smaller.png";
//var sCrapURL = "/theme/sb_jukebox/images/star_crap_normal.png";
//var sCrapSmallURL = "/theme/sb_jukebox/images/star_crap_small.png";
//var sCrapSmallerURL = "/theme/sb_jukebox/images/star_crap_smaller.png";

//var sClassGood = "goodstar_normal";
//var sClassGoodSmall = "goodstar_small";
//var sClassGoodSmaller = "goodstar_smaller";
//var sClassBad = "badstar_normal";
//var sClassBadSmall = "badstar_small";
//var sClassBadSmaller = "badstar_smaller";
//var sClassDot = "/theme/sb_jukebox/images/star_dot.png";
//var sHighlightURL = '/theme/sb_jukebox/images/star_select.png';
var sHighlightURL = '/theme/sb_jukebox/images/star_set_normal.png';

var sStarURL = '/theme/sb_jukebox/images/star_set_normal.png';
var sBlankURL = '/theme/sb_jukebox/images/star_blank.png';
var sDotHTML = '<img src="/theme/sb_jukebox/images/star_blank.png" onMouseOver="highlight_star(this, true)" onMouseOut="highlight_star(this, false)" onClick="vote(this)" />';

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
	
	console.log('sVote:' + sVote + 'TotalStars:' + iTotalStars);
	
	if (sVote == "") {
		var iVote = -1;
	} else {
		var iVote = parseInt(sVote); 
	}
	
	for (var i=0; i<iTotalStars; i++) {
//		oStarContainer.childNodes[i+1].src = getStarURL(iVote, i);
//		oStarContainer.childNodes[i+1].src_orig = oStarContainer.childNodes[i+1].src;
		oStarContainer.childNodes[i+1].className = '';
		oStarContainer.childNodes[i+1].classList.add(getStarSize(iVote, i));
		oStarContainer.childNodes[i+1].classList.add(getStarType(iVote, i));
		oStarContainer.childNodes[i+1].originalClasses = oStarContainer.childNodes[i+1].className;
	}

}

//------------------------------------------------------------------------------
/**
* returns the correct star image based on type and step fraction
*/
function getStarSize(iVote, iPosition) {
	
	if (iVote == -1) {
		return ('normal');
	}
	
	if (iPosition == 0) {
		return ('normal');
	}
	
	var iRange = aStarDefinition[iPosition]['vote'] - aStarDefinition[iPosition-1]['vote'];
	var iModulo = iVote - aStarDefinition[iPosition-1]['vote'];
	
	if (iModulo <= iRange / 8) {
		return ('none');
	} else if (iModulo <= iRange / 8 * 3) {
		return ('smaller');
	} else if (iModulo <= iRange / 8 * 5) {
		return ('small');
	} else {
		return ('normal');
	}
	
}

//------------------------------------------------------------------------------
/**
* returns the correct star image based on type and step fraction
*/
function getStarType(iVote, iPosition) {
	
//	if (iVote == -1) {
//		return ('good');
//	}
	if (sVotingStyle == 'HOTEL') {
		return('good');
	}
	
//	var iRange = aStarDefinition[iPosition]['vote'] - aStarDefinition[iPosition-1]['vote'];
	
	if (sVotingStyle == 'MARKED' && iPosition < Math.abs(iMinStars)) {
		return('bad');
	}
	
	return('good');
	
}

//------------------------------------------------------------------------------
/**
* highlights or resets all stars under and left to mouse hover 
*/
function highlight_star(oStarImage, bEnable) {
	
	if (bEnable) {
		oStarImage.src = sStarURL;
		oStarImage.classList.add('highlighted');
	} else {
		oStarImage.src = sBlankURL;
		oStarImage.classList.remove('highlighted');
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
