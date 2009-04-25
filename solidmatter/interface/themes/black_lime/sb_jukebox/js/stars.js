
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
var sDotURL = "/theme/sb_jukebox/images/star_dot.png";
var sHighlightURL = '/theme/sb_jukebox/images/star_select.png';

var sStarHTML = '<img src="' + sStarURL + '" alt="star set" style="padding-right: 1px;" onMouseOver="highlight_star(this, true)" onMouseOut="highlight_star(this, false)" onClick="vote(this)" />';
var sStarSmallHTML = '<img src="' + sStarSmallURL + '" alt="star set" style="padding-right: 1px;" onMouseOver="highlight_star(this, true)" onMouseOut="highlight_star(this, false)" onClick="vote(this)" />';
var sStarSmallerHTML = '<img src="' + sStarSmallerURL + '" alt="star set" style="padding-right: 1px;" onMouseOver="highlight_star(this, true)" onMouseOut="highlight_star(this, false)" onClick="vote(this)" />';
var sDotHTML = '<img src="' + sDotURL + '" alt="star unset"  style="padding-right: 1px;" onMouseOver="highlight_star(this, true)" onMouseOut="highlight_star(this, false)" onClick="vote(this)" />';

//------------------------------------------------------------------------------
/**
* init star display
*/
function init_stars() {
	
	if (sVotingStyle == 'HOTEL') {
		for (var i=0; i<iMaxStars; i++) {
			
		}
	}
	
	if (sVote == 'NaN') {
		var iNumStars = 0;
	} else {
		var iVote = parseInt(sVote);
		var iNumStars = iVote / 100 * (iMaxStars-1) + 1;	
	}
	
	for (var i=1; i<=iMaxStars; i++) {
		var iHelper = Math.round((iNumStars + 1 - i) * 3);
		if (iHelper >= 3) {
			document.write(sStarHTML);
		} else if (iHelper == 2) {
			document.write(sStarSmallHTML);
		} else if (iHelper == 1) {
			document.write(sStarSmallerHTML);
		} else {
			document.write(sDotHTML);
		}
	}
	
}

//------------------------------------------------------------------------------
/**
* display voting starset
*/
function render_stars(sVote, bVotingEnabled) {
	
	if (sVote == 'NaN') {
		var iNumStars = 0;
	} else {
		var iVote = parseInt(sVote);
		var iNumStars = iVote / 100 * (iMaxStars-1) + 1;	
	}
	
	for (var i=1; i<=iMaxStars; i++) {
		var iHelper = Math.round((iNumStars + 1 - i) * 3);
		if (iHelper >= 3) {
			document.write(sStarHTML);
		} else if (iHelper == 2) {
			document.write(sStarSmallHTML);
		} else if (iHelper == 1) {
			document.write(sStarSmallerHTML);
		} else {
			document.write(sDotHTML);
		}
	}
	
}

//------------------------------------------------------------------------------
/**
* updates a star set
*/
function update_stars(oStarContainer, iVote) {
	
	var iNumStars = iVote / 100 * (iMaxStars-1) + 1;
	
	for (var i=1; i<=iMaxStars; i++) {
		var iHelper = Math.round((iNumStars + 1 - i) * 3);
		if (iHelper >= 3) {
			oStarContainer.childNodes[i].src = sStarURL;
		} else if (iHelper == 2) {
			oStarContainer.childNodes[i].src = sStarSmallURL;
		} else if (iHelper == 1) {
			oStarContainer.childNodes[i].src = sStarSmallerURL;
		} else {
			oStarContainer.childNodes[i].src = sDotURL;
		}
		oStarContainer.childNodes[i].src_orig = oStarContainer.childNodes[i].src;
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
	
	var iVote = Math.round(100 / (iMaxStars-1) * (iSelectedStar-1));
	
	//alert(iVote);
	//alert(sUUID);
	var sURL = '/' + sUUID + '/votes/placeVote/?vote=' + iVote;
	var myAjaxVoter = new Ajax.Request( 
		sURL, 
		{
			method: 'get', 
			parameters: null,
			asynchronous: false,
			onComplete: function(response) {
    			update_stars(oStarContainer, response.getHeader('X-Vote'));
    		} 
		}
	);
	//window.location.reload();
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