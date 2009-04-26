
//------------------------------------------------------------------------------
/**
*	@package solidMatter[sbJukebox]
*	@author	()((() [Oliver Müller]
*	@author	hthiery [Heiko Thiery]
*	@version 1.00.00
*/
//------------------------------------------------------------------------------

var sStarURL        = "/theme/sb_jukebox/images/star_set_normal.png";
var sStarSmallURL   = "/theme/sb_jukebox/images/star_set_small.png";
var sStarSmallerURL = "/theme/sb_jukebox/images/star_set_smaller.png";

var sStarCrapURL        = "/theme/sb_jukebox/images/star_crap_normal.png";
var sStarSmallCrapURL   = "/theme/sb_jukebox/images/star_crap_small.png";
var sStarSmallerCrapURL = "/theme/sb_jukebox/images/star_crap_smaller.png";

var sDotURL         = "/theme/sb_jukebox/images/star_dot.png";
var sHighlightURL   = '/theme/sb_jukebox/images/star_select.png';

var sStarHTML        = '<img src="' + sStarURL + '" alt="star set" style="padding-right: 1px;" onMouseOver="highlight_star(this, true)" onMouseOut="highlight_star(this, false)" onClick="vote(this)" />';
var sStarSmallHTML   = '<img src="' + sStarSmallURL + '" alt="star set" style="padding-right: 1px;" onMouseOver="highlight_star(this, true)" onMouseOut="highlight_star(this, false)" onClick="vote(this)" />';
var sStarSmallerHTML = '<img src="' + sStarSmallerURL + '" alt="star set" style="padding-right: 1px;" onMouseOver="highlight_star(this, true)" onMouseOut="highlight_star(this, false)" onClick="vote(this)" />';
var sStarCrapHTML        = '<img src="' + sStarCrapURL + '" alt="star set" style="padding-right: 1px;" onMouseOver="highlight_star(this, true)" onMouseOut="highlight_star(this, false)" onClick="vote(this)" />';
var sStarSmallCrapHTML   = '<img src="' + sStarSmallCrapURL + '" alt="star set" style="padding-right: 1px;" onMouseOver="highlight_star(this, true)" onMouseOut="highlight_star(this, false)" onClick="vote(this)" />';
var sStarSmallerCrapHTML = '<img src="' + sStarSmallerCrapURL + '" alt="star set" style="padding-right: 1px;" onMouseOver="highlight_star(this, true)" onMouseOut="highlight_star(this, false)" onClick="vote(this)" />';
var sDotHTML         = '<img src="' + sDotURL + '" alt="star unset"  style="padding-right: 1px;" onMouseOver="highlight_star(this, true)" onMouseOut="highlight_star(this, false)" onClick="vote(this)" />';



var iTotalStars = 0;
var aStarDefinition = new Array();

/*
	iMinStars = -2
	iMaxStars = +2
	
	iTotalStars = iMaxStars + (iMinStars * (-1))
				= 5
	
	0   8   16  25  33  41  50  58  66  75  83  91  100
	X---O---o---X---O---o---X---o---O---X---o---O----X
	
	
	if vote is 50 or more at least one star is drawn full!!
*/ 

//------------------------------------------------------------------------------
/**
* init star display
*/
function init_stars() {
 
	iTotalStars = iMaxStars - iMinStars + 1;
	
	iVal = 100/(iTotalStars-1);
	var i=0;
	for (i=0; i<iTotalStars; i++) {
		aStarDefinition[i] = new Object();
		aStarDefinition[i]['value'] = iVal * i;
	}
	/* negative voting range */
	for (i=0; i<Math.abs(iMinStars); i++) {
		aStarDefinition[i] = new Object();
		aStarDefinition[i]['type'] = 'NEGATIVE';
		aStarDefinition[i]['threshStar'] = i * iVal;
		aStarDefinition[i]['threshStarSmall'] = (i * iVal) + ((iVal/3) * 1);
		aStarDefinition[i]['threshStarSmaller'] = (i * iVal) + ((iVal/3) * 2);
		aStarDefinition[i]['sStarHTML'] = sStarCrapHTML;
		aStarDefinition[i]['sStarSmallHTML'] = sStarSmallCrapHTML;
		aStarDefinition[i]['sStarSmallerHTML'] = sStarSmallerCrapHTML;
		aStarDefinition[i]['sStarURL'] = sStarCrapURL;
		aStarDefinition[i]['sStarSmallURL'] = sStarSmallCrapURL;
		aStarDefinition[i]['sStarSmallerURL'] = sStarSmallerCrapURL;
	}
	
	aStarDefinition[i] = new Object();
	aStarDefinition[i]['type'] = 'POSITIVE';
	aStarDefinition[i]['threshStar']        = 50;
	aStarDefinition[i]['threshStarSmall']   = 50;
	aStarDefinition[i]['threshStarSmaller'] = 50;
	aStarDefinition[i]['sStarHTML'] = sStarHTML;
	aStarDefinition[i]['sStarSmallHTML'] = sStarSmallHTML;
	aStarDefinition[i]['sStarSmallerHTML'] = sStarSmallerHTML;
	aStarDefinition[i]['sStarURL'] = sStarURL;
	aStarDefinition[i]['sStarSmallURL'] = sStarSmallURL;
	aStarDefinition[i]['sStarSmallerURL'] = sStarSmallerURL;
	
	for (i=Math.abs(iMinStars)+1; i<iTotalStars; i++) {
		aStarDefinition[i] = new Object();
		aStarDefinition[i]['type'] = 'POSITIVE';
		aStarDefinition[i]['threshStar'] = i * iVal;
		aStarDefinition[i]['threshStarSmall'] = ((i-1) * iVal) + ((iVal/3) * 2);
		aStarDefinition[i]['threshStarSmaller'] = ((i-1) * iVal) + ((iVal/3) * 1);
		aStarDefinition[i]['sStarHTML'] = sStarHTML;
		aStarDefinition[i]['sStarSmallHTML'] = sStarSmallHTML;
		aStarDefinition[i]['sStarSmallerHTML'] = sStarSmallerHTML;
		aStarDefinition[i]['sStarURL'] = sStarURL;
		aStarDefinition[i]['sStarSmallURL'] = sStarSmallURL;
		aStarDefinition[i]['sStarSmallerURL'] = sStarSmallerURL;
	}
	
}

//------------------------------------------------------------------------------
/**
* display voting starset
*/
function helper_get_star_html(i, iVote)
{

	if (iVote < 50) {
		if (aStarDefinition[i]['type'] == 'POSITIVE') {
			return sDotHTML;
		}
		if(iVote <= aStarDefinition[i]['threshStar']) {
			return aStarDefinition[i]['sStarHTML'];
			
		} else if(iVote < aStarDefinition[i]['threshStarSmall']) {
			return aStarDefinition[i]['sStarSmallHTML'];
			
		} else if(iVote < aStarDefinition[i]['threshStarSmaller']) {
			return aStarDefinition[i]['sStarSmallerHTML'];
			
		} else {
			return sDotHTML;
		}
	} else {
		
		//positive voting range
		if (aStarDefinition[i]['type'] == 'NEGATIVE') {
			return sDotHTML;
		}
		//alert("helper_get_star_html: "+iVote + aStarDefinition[i]['threshStar']);
		if(iVote >= aStarDefinition[i]['threshStar']) {
			return aStarDefinition[i]['sStarHTML'];
			
		} else if(iVote > aStarDefinition[i]['threshStarSmall']) {
			return aStarDefinition[i]['sStarSmallHTML'];
			
		} else if(iVote > aStarDefinition[i]['threshStarSmaller']) {
			return aStarDefinition[i]['sStarSmallerHTML'];
			
		} else {
			return sDotHTML;
		}
	}
}

function helper_get_star_url(i, iVote)
{

	if (iVote < 50) {
		if (aStarDefinition[i]['type'] == 'POSITIVE') {
			return sDotURL;
		}
		if(iVote <= aStarDefinition[i]['threshStar']) {
			return aStarDefinition[i]['sStarURL'];
			
		} else if(iVote < aStarDefinition[i]['threshStarSmall']) {
			return aStarDefinition[i]['sStarSmallURL'];
			
		} else if(iVote < aStarDefinition[i]['threshStarSmaller']) {
			return aStarDefinition[i]['sStarSmallerURL'];
			
		} else {
			return sDotURL;
		}
	} else {
		
		//positive voting range
		if (aStarDefinition[i]['type'] == 'NEGATIVE') {
			return sDotURL;
		}
		
		if(iVote >= aStarDefinition[i]['threshStar']) {
			return aStarDefinition[i]['sStarURL'];
			
		} else if(iVote > aStarDefinition[i]['threshStarSmall']) {
			return aStarDefinition[i]['sStarSmallURL'];
			
		} else if(iVote > aStarDefinition[i]['threshStarSmaller']) {
			return aStarDefinition[i]['sStarSmallerURL'];
			
		} else {
			return sDotURL;
		}
	}
}

function render_stars(sVote, sUUID, bVotingEnabled) {

	if (sVote == "") {
		for (var i=0; i<iTotalStars; i++) {
			document.write(sDotHTML);
		}
		return;
	}
	
	var iVote = parseInt(sVote);
	for (var i=0; i<iTotalStars; i++) {
		//alert(iVote + " " + helper_get_star_html(i, iVote));
		document.write(helper_get_star_html(i, iVote));
	}
}


//------------------------------------------------------------------------------
/**
* updates a star set
*/

function update_stars(oStarContainer, iVote) {
	for (var i=0; i<iTotalStars; i++) {
		oStarContainer.childNodes[i+1].src =  helper_get_star_url(i, iVote);
		oStarContainer.childNodes[i+1].src_orig = oStarContainer.childNodes[i+1].src;
	}
}


//------------------------------------------------------------------------------
/**
* highlights or resets all stars under and left to mouse hover 
*/
function highlight_star(oStarImage, bEnable) {

	var i = 0;
	var oTemp = null;
	oTemp = oStarImage.previousSibling;
	for (i=0; oTemp != null; i++) {
		oTemp = oTemp.previousSibling;
	};
	//documentWrite is also a sibling ... remove
	i--;
	
	if (bEnable) {
		oStarImage.src_orig = oStarImage.src;
		oStarImage.src = aStarDefinition[i]['sStarURL'];
	} else {
		oStarImage.src = oStarImage.src_orig;
	}
	
	if (aStarDefinition[i]['type'] == 'NEGATIVE' 
		&& aStarDefinition[i+1]['type'] == 'NEGATIVE') {
		if (oStarImage.nextSibling != null) {
			highlight_star(oStarImage.nextSibling, bEnable);
		}
	} else if (aStarDefinition[i]['type'] == 'POSITIVE' && aStarDefinition[i-1]['type'] == 'POSITIVE') {
		if (oStarImage.previousSibling != null)
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
	
	var iVote = Math.round(100 / (iTotalStars-1) * (iSelectedStar-1));

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
