
//------------------------------------------------------------------------------
/**
*	@package solidMatter[sbJukebox]
*	@author	()((() [Oliver Müller]
*	@version 1.00.00
*/
//------------------------------------------------------------------------------

//------------------------------------------------------------------------------
/**
* display voting starset
*/
function render_stars(sVote, iMaxStars, bVotingEnabled) {
	
	if (sVote == 'NaN') {
		var iNumStars = 0;
	} else {
		var iVote = parseInt(sVote);
		var iNumStars = Math.round(iVote / 100 * (iMaxStars-1)) + 1;
	}
	
	var sStarHTML = '<img src="/theme/sb_jukebox/images/star_set_disabled.png" alt="star set" style="padding-right: 1px;" onMouseOver="highlight_star(this, true)" onMouseOut="highlight_star(this, false)" onClick="vote(this, ' + iMaxStars + ')" />';
	var sDotHTML = '<img src="/theme/sb_jukebox/images/star_dot.png" alt="star unset"  style="padding-right: 1px;" onMouseOver="highlight_star(this, true)" onMouseOut="highlight_star(this, false)" onClick="vote(this, ' + iMaxStars + ')" />';
	
	for (var i=0; i<iMaxStars; i++) {
		if (i < iNumStars) {
			document.write(sStarHTML);
		} else {
			document.write(sDotHTML);
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
		oStarImage.src = '/theme/sb_jukebox/images/star_select.png';
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
function vote(oStarImage, iMaxStars) {
	
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
    			update_stars(oStarContainer, iMaxStars, response.getHeader('X-Vote'));
    		} 
		}
	);
	//window.location.reload();
}

//------------------------------------------------------------------------------
/**
* updates a star set
*/
function update_stars(oStarContainer, iMaxStars, iVote) {
	
	var iNumStars = Math.round(iVote / 100 * (iMaxStars-1)) + 1;
	
	for (var i=1; i<=iMaxStars; i++) {
		if (i <= iNumStars) {
			oStarContainer.childNodes[i].src = '/theme/sb_jukebox/images/star_set_disabled.png';
		} else {
			oStarContainer.childNodes[i].src = '/theme/sb_jukebox/images/star_dot.png';
		}
		oStarContainer.childNodes[i].src_orig = oStarContainer.childNodes[i].src;
	}

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