function add_text(sInputfield, sText) {
	
	// init
	var myAgent   = navigator.userAgent.toLowerCase();
	var myVersion = parseInt(navigator.appVersion);
	var is_ie   = ((myAgent.indexOf("msie") != -1)  && (myAgent.indexOf("opera") == -1));
	var is_win   =  ((myAgent.indexOf("win")!=-1) || (myAgent.indexOf("16bit")!=-1));
	
	// get element
	var eInputField = getElem('name', sInputfield, 0);
	
	if(eInputField.textLength >= 0) { // mozilla, firebird, netscape
		
		eInputField.focus();
		iStart = eInputField.selectionStart;
		iEnd = eInputField.textLength;
		sEndText = eInputField.value.substring(eInputField.selectionEnd, iEnd);
		sStartText = eInputField.value.substring(0, iStart);
		eInputField.value = sStartText + sText + sEndText;
		eInputField.selectionStart = iStart;
		eInputField.selectionEnd = iStart;
		
		eInputField.selectionStart = eInputField.selectionStart + sText.length;
		
	} else if ((myVersion >= 4) && is_ie && is_win) {  // Internet Explorer
		
		if(eInputField.isTextEdit) {
			eInputField.focus();
			var oSelection = document.selection;
			var oRange = oSelection.createRange();
			oRange.colapse;
			oRange.text = sText;
		} else {
			eInputField.value += sText;
		}
		
	} else {
		
  		eInputField.value += sText;
  		
	}
	
	eInputField.focus();
	
	//getElem('name', sInputfield, 0).value = getElem('name', sInputfield, 0).value + sText;
}

function set_focus(sInputfield) {
	getElem('name', sInputfield, 0).focus();
}







// ripped example code here...


function smilie(theSmilie) {
	addText(" " + theSmilie, "", false, document.bbform);
}

function addText(theTag, theClsTag, isSingle, theForm)
{

	var isClose = false;
	var message = theForm.message;
	var set=false;
  	var old=false;
  	var selected="";

  	if(message.textLength>=0 ) { // mozilla, firebird, netscape
  		if(theClsTag!="" && message.selectionStart!=message.selectionEnd) {
  			selected=message.value.substring(message.selectionStart,message.selectionEnd);
  			str=theTag + selected+ theClsTag;
  			old=true;
  			isClose = true;
  		}
		else {
			str=theTag;
		}

		message.focus();
		start=message.selectionStart;
		end=message.textLength;
		endtext=message.value.substring(message.selectionEnd,end);
		starttext=message.value.substring(0,start);
		message.value=starttext + str + endtext;
		message.selectionStart=start;
		message.selectionEnd=start;

		message.selectionStart = message.selectionStart + str.length;
		if(old) { return false; }
		set=true;
		if(isSingle) {
			isClose = false;
		}
	}

	if ( (myVersion >= 4) && is_ie && is_win) {  // Internet Explorer
		if(message.isTextEdit) {
			message.focus();
			var sel = document.selection;
			var rng = sel.createRange();
			rng.colapse;
			if((sel.type == "Text" || sel.type == "None") && rng != null){
				if(theClsTag != "" && rng.text.length > 0)
					theTag += rng.text + theClsTag;
				else if(isSingle)
					isClose = true;
				rng.text = theTag;
			}
		} else {
			if(isSingle) isClose = true;
			if(!set) {
  				message.value += theTag;
  			}
		}
	} else {
		if(isSingle) isClose = true;
		if(!set) {
  			message.value += theTag;
  		}
	}

	message.focus();
	return isClose;

}

function getSelectedText(theForm) {
	var message = theForm.message;
	var selected = '';

	if(navigator.appName=="Netscape" &&  message.textLength>=0 && message.selectionStart!=message.selectionEnd ) 

  		selected=message.value.substring(message.selectionStart,message.selectionEnd);	


	else if( (myVersion >= 4) && is_ie && is_win ) {
		if(message.isTextEdit){ 
			message.focus();
			var sel = document.selection;
			var rng = sel.createRange();
			rng.colapse;
			if((sel.type == "Text" || sel.type == "None") && rng != null) {
				if(rng.text.length > 0) selected = rng.text;
			}
		}	
	}

  	return selected;

}






