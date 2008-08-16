function toggle_display(sAreaID) {
	if (DOM || MS) {
		if (getElem('id', sAreaID, 0).style.display == 'none') {
			getElem('id', sAreaID, 0).style.display = '';
		} else {
			getElem('id', sAreaID, 0).style.display = 'none';
		}
	} else if (NS) {
		if (getElem('id', sAreaID, 0).display == 'none') {
			getElem('id', sAreaID, 0).display = '';
		} else {
			getElem('id', sAreaID, 0).display = 'none';
		}
	}
}

// mode: 'on' or 'off'
function set_display(sAreaID, sMode) {
	if (DOM || MS) {
		if (sMode == 'on') {
			getElem('id', sAreaID, 0).style.display = '';
		} else {
			getElem('id', sAreaID, 0).style.display = 'none';
		}
	} else if (NS) {
		if (sMode == 'on') {
			getElem('id', sAreaID, 0).display = '';
		} else {
			getElem('id', sAreaID, 0).display = 'none';
		}
	}
}
