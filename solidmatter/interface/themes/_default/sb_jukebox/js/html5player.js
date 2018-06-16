




class HTML5Player {
	
//	logging = true;
	
//	var elemPlayer;
//	private var aTracks;
//	var iCurrentTrack;
	
	constructor(sID) {
		
		this.bLogging = true;
		
		this.elemPlayer = document.getElementById(sID);
		
		this.aTracks = [];
		this.aTrackLookup = [];
		
		this.iNowPlaying = 0;
		
		this.setVolume(this.loadVolume());
//		this.elemPlayer.onVolumeChange = this.saveVolume(); // why the fuck doesnt this work?!?!!
	
	}
	
	addTrack(sUUID, sURL, sTitle) {
		var aTrack = {
			"UUID": sUUID,
			"URL": sURL,
			"Title": sTitle
		};
		this.aTrackLookup[sUUID] = this.aTracks.length;
		this.aTracks.push(aTrack);
		this.log('added Track No. ' + this.aTracks.length + ': ' + aTrack.Title);
	}
	
	play(sUUID) {
		this.playIndex(this.aTrackLookup[sUUID]);
//		this.log('finding: highlight_'+sUUID);
//		$('highlight_'+sUUID).classList.add('nowplaying');
//		for ($('tracklist')[0].length) {
//			
//		}
//		$('highlight_'+sUUID).style.backgroundColor = "#828";
//		document.getElementById('highlight_'+sUUID).class = "nowplaying";
	}
	
	playIndex(iIndex) {
		$('tracklist').children[0].children[this.iNowPlaying].classList.remove('nowplaying');
		$('tracklist').children[0].children[iIndex].classList.add('nowplaying');
		this.iNowPlaying = iIndex;
		this.elemPlayer.src = this.aTracks[this.iNowPlaying].URL;
		this.elemPlayer.play();
		this.log('Playing Track ' + this.aTracks[iIndex].Title);
	}
	
	playNext() {
		if (this.iNowPlaying < this.aTracks.length-1) {
			this.playIndex(this.iNowPlaying+1);
		}
	}
	
	initPlayer() {
		
		
	}


	initVolume() {
		
	}
	
	setVolume(fVolume) {
		this.elemPlayer.volume = fVolume;
	}
	
	
	
	saveVolume() {
		localStorage.setItem('sbJukebox:volume', this.elemPlayer.volume);
		this.log("volume set to " + this.elemPlayer.volume);
	}
	
	
	loadVolume() {
		var fVolume = null;
		fVolume = localStorage.getItem('sbJukebox:volume'); 
		this.log("saved volume is " + fVolume);
		if (fVolume == null) {
			return (0.5);
		}
		return (fVolume);
		
	}
	
	log(sText) {
		if (this.bLogging) {
			console.log(sText);
		}
	}
	
	
}

