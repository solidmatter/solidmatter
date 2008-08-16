<?php

//------------------------------------------------------------------------------
/**
*	@package solidMatter[sbForum]
*	@author	()((() [Oliver Müller]
*	@version 0.00.00
*/
//------------------------------------------------------------------------------

//------------------------------------------------------------------------------
/**
*/
function build_forumicon($sIconCode = 'NONE') {
	
	global $PATH_GLOBALTHEME;
	global $PATH_LOCALTHEME;
	$sIconDir = '';
	
	//echo $sIconCode;
	switch ($sIconCode) {
		//case "NONE":	$sIconFile = "thumbsup.gif'";	break;
		case '1_ANOUNCEMENT':		$sIconFile = 'anouncement.gif';	break;
		case '2_STICKY':			$sIconFile = 'sticky.gif';		break;
		
		case 'I_PAPERCLIP':			$sIconFile = 'paperclip.png';	break;
		
		case 'I_IDEA':				$sIconFile = 'idea.gif';		break;
		case 'I_INFORMATION':		$sIconFile = 'information.gif';	break;
		case 'I_QUESTION':			$sIconFile = 'question.gif';	break;
		case 'I_EXCLAMATION':		$sIconFile = 'exclamation.gif';	break;
		case 'E_ANGRY':				$sIconFile = 'angry.gif';		break;
		case 'E_BIGGRIN':			$sIconFile = 'biggrin.gif';		break;
		case 'E_CONFUSED':			$sIconFile = 'confused.gif';	break;
		case 'E_EEK':				$sIconFile = 'eek.gif';			break;
		case 'E_RAYBAN':			$sIconFile = 'rayban.gif';		break;
		case 'E_ROLLEYES':			$sIconFile = 'rolleyes.gif';	break;
		case 'E_SAD':				$sIconFile = 'sad.gif';			break;
		case 'E_SHOCKED':			$sIconFile = 'shocked.gif';		break;
		case 'E_SMILE':				$sIconFile = 'smile.gif';		break;
		case 'E_SORRY':				$sIconFile = 'sorry.gif';		break;
		case 'E_TONGUE':			$sIconFile = 'tongue.gif';		break;
		case 'E_WINK':				$sIconFile = 'wink.gif';		break;
		case 'E_YAWN':				$sIconFile = 'yawn.gif';		break;
		
		case 'NONE':
		case '9_NORMAL':
		default:					$sIconFile = 'blank.gif';		break;
	}
	
	if (substr($sIconCode, 0, 1) == 'E') {
		$sIconDir = $PATH_GLOBALTHEME.'/images_smilies/';
	} else {
		$sIconDir = $PATH_LOCALTHEME.'/images_icons/';
	}
	
	$sIconTag = '<img src="'.$sIconDir.$sIconFile.'" alt="" align="top" />';
	return ($sIconTag);
}

//-----------------------------------------------------

function build_stars($iNumPosts) {
	
	global $PATH_LOCALTHEME;
	
	$sStarURLs = '';
	$iMax = get_config('forum', '5STAR_POSTCOUNT');
	$iStep = ceil(get_config('forum', '5STAR_POSTCOUNT') / 5);
	for($i=0; $i<$iMax; $i+=$iStep) {
		if ($iNumPosts-$i > $iStep) {
			$sStarURLs .= "<img src=\"$PATH_LOCALTHEME/images_stars/full.gif\" alt=\"\" />";
		} elseif ($iNumPosts-$i > ($iStep / 2)) {
			$sStarURLs .= "<img src=\"$PATH_LOCALTHEME/images_stars/half.gif\" alt=\"\" />";
		} else {
			$sStarURLs .= "<img src=\"$PATH_LOCALTHEME/images_stars/empty.gif\" alt=\"\" />";
		}
	}
	return ($sStarURLs);
}

//-----------------------------------------------------
/*
function build_avatar($iUserID) {
	if (file_exists("images_avatars/".$iUserID.".gif")) {
		$sAvatar = "<img src='images_avatars/".$iUserID.".gif'>";
	} elseif (file_exists("images_avatars/".$iUserID.".jpg")) {
		$sAvatar = "<img src='images_avatars/".$iUserID.".jpg'>";
	} elseif (file_exists("images_avatars/".$iUserID.".png")) {
		$sAvatar = "<img src='images_avatars/".$iUserID.".png'>";
	} else {
		$sAvatar = "<img src='images_avatars/noavatar.gif'>";
	}
	return ($sAvatar);
}
*/


?>