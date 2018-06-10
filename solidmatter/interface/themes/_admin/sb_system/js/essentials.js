//------------------------------------------------------------------------------
/**
* 
*/
function toggle(sID) {
	
	var oContainer = $(sID);
	
	if (oContainer.style.display != 'none') {
		oContainer.style.display = 'none';
	} else {
		if (oContainer.tagName == 'TR') {
			oContainer.style.display = 'table-row';
		} else {
			oContainer.style.display = 'block';
		}
	}
}