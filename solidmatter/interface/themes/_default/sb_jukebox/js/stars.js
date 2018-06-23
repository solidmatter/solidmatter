
//------------------------------------------------------------------------------
/**
*	@package solidMatter[sbJukebox]
*	@author	()((() [Oliver Müller]
*	@version 1.00.00
*/
//------------------------------------------------------------------------------

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
	
	update_stars(sUUID, sVote);
	
}

//------------------------------------------------------------------------------
/**
* updates a star set
*/
function update_stars(sUUID, sVote) {
	
	//console.log('sVote:' + sVote + 'TotalStars:' + iTotalStars);
	
	if (sVote == "") {
		var iVote = -1;
	} else {
		var iVote = parseInt(sVote); 
	}
	
	var aOccurrences = document.getElementsByClassName("js-" + sUUID);
	
	if (aOccurrences == null) {
		console.log("js-" + sUUID + 'not found!??!');
		return;
	}
	
	for (var i=0; i<aOccurrences.length; i++) {
		for (var j=0; j<iTotalStars; j++) {
			oStarContainer = aOccurrences[i];
			oStarContainer.childNodes[j+1].className = '';
			oStarContainer.childNodes[j+1].classList.add(getStarSize(iVote, j));
			oStarContainer.childNodes[j+1].classList.add(getStarType(iVote, j));
			oStarContainer.childNodes[j+1].originalClasses = oStarContainer.childNodes[i+1].className;
		}
	}

}

//------------------------------------------------------------------------------
/**
* returns the correct star image based on type and step fraction
*/
function getStarSize(iVote, iPosition) {
	
	if (iVote == -1) { // no vote cast yet
		return ('none');
	}
	
	// first star data, update if possible
	var iRange = aStarDefinition[0]['vote'];
	var iModulo = 0;
	if (iPosition != 0) {
		iRange = aStarDefinition[iPosition]['vote'] - aStarDefinition[iPosition-1]['vote'];
		iModulo = iVote - aStarDefinition[iPosition-1]['vote'];
	} 
	
	if (sVotingStyle == 'HOTEL' || sVotingStyle == 'MARKED') {
		if (iPosition == 0) {
			return ('normal');
		}
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
	if (sVotingStyle == 'RELATIVE') {
		if (iPosition < Math.abs(iMinStars) && iVote > aStarDefinition[iPosition]['vote']) {
			return ('none');
		} else if (iPosition >= Math.abs(iMinStars) && iVote < aStarDefinition[iPosition]['vote']) {
			return ('none');
		} else {
			return ('normal');
		}
	}
	
}

//------------------------------------------------------------------------------
/**
* returns the correct star image based on type and step fraction
*/
function getStarType(iVote, iPosition) {
	
	if (sVotingStyle == 'HOTEL') {
		return('good');
	}
	if ((sVotingStyle == 'MARKED' || sVotingStyle == 'RELATIVE') && iPosition < Math.abs(iMinStars)) {
		return('bad');
	}
	
	return('good');
	
}

//------------------------------------------------------------------------------
/**
* highlights or resets all stars under and left to mouse hover 
*/
function highlight_star(oStarImage, bEnable) {
	
	// determine position (counting preceding siblings)
	var oTemp = oStarImage;
	for (var iPosition = 0; (oTemp=oTemp.previousSibling); iPosition++);
	
	if (sVotingStyle == 'HOTEL' || sVotingStyle == 'MARKED') {
		for (var i=0; i<=iPosition; i++) {
			oCurrentStar = oStarImage.parentNode.childNodes[i];
			if (bEnable) {
				oCurrentStar.src = sStarURL;
				oCurrentStar.classList.add('highlighted');
			} else {
				oCurrentStar.src = sBlankURL;
				oCurrentStar.classList.remove('highlighted');
			}
		}
		return;
	}
	
	if (sVotingStyle == 'RELATIVE') {
		for (var i=0; i<=iTotalStars; i++) {
			oCurrentStar = oStarImage.parentNode.childNodes[i];
			if (bEnable) {
				if (i <= Math.abs(iMinStars)) { // negative stars
					if (i < iPosition) {
						oCurrentStar.src = sBlankURL;
						oCurrentStar.classList.remove('highlighted');
					} else {
						oCurrentStar.src = sStarURL;
						oCurrentStar.classList.add('highlighted');
					}
				} else { // positive stars
					if (i <= iPosition) {
						oCurrentStar.src = sStarURL;
						oCurrentStar.classList.add('highlighted');
					} else {
						oCurrentStar.src = sBlankURL;
						oCurrentStar.classList.remove('highlighted');
					}
				}
			} else {
				oCurrentStar.src = sBlankURL;
				oCurrentStar.classList.remove('highlighted');
			}
			
		}
	}
}

//------------------------------------------------------------------------------
/**
* casts a vote on a node
*/
function vote(oStarImage) {
	
	var oStarContainer = oStarImage.parentNode;
	
	var sUUID = oStarContainer.className.replace(/stars js-/g, '');
	
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
			asynchronous: true,
			onComplete: function(response) {
    			update_stars(sUUID, response.getHeader('X-sbVote'));
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
