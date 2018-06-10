<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet 
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform" 
	version="1.0" 
	exclude-result-prefixes="html" 
	xmlns:html="http://www.w3.org/1999/xhtml"
	xmlns:sbform="http://www.solidbytes.net/sbform"
	xmlns:dyn="http://exslt.org/dynamic" extension-element-prefixes="dyn">

	<xsl:import href="global.default.xsl" />
	<xsl:import href="global.sbform.xsl" />
	
	<xsl:variable name="jukebox" select="/response/metadata/modules/sb_jukebox" />
	
	<xsl:output 
		method="html"
		encoding="UTF-8"
		standalone="yes"
		indent="no"
	/>
	
	<xsl:template match="/">
	<html>
	<head>
		<xsl:apply-templates select="/response/metadata" />
	</head>
	<body>
		<xsl:call-template name="views" />
		<div class="workbench">
			<xsl:apply-templates select="response/errors" />
			<table class="invisible">
			<tr><td>
				<div class="eyecandy">
					<h1>Aktionen</h1>
					<a class="warning" href="/{$subjectid}/administration/clearQuilts">Quilts löschen</a>
					<br/>
					<br/>
					<a class="highlighted" href="/{$subjectid}/administration/startImport" target="_blank">Import starten</a><br/>
					<a class="highlighted" href="/{$subjectid}/administration/startImport/?dry=true" target="_blank">Import starten (Trockenlauf)</a><br/>
					<a class="warning" href="/{$subjectid}/administration/clearLibrary">Bibliothek löschen</a>
					<br/>
					<br/>
					<a class="highlighted" href="/{$subjectid}/administration/findSongkickIDs" target="_blank">Songkick IDs finden</a><br/>
					<a class="warning" href="/{$subjectid}/administration/clearSongkickIDs" target="_blank">Songkick IDs löschen</a>
					<br/>
					<br/>
					<a class="highlighted" href="/{$subjectid}/administration/storeUGC" target="_blank">User Generated Content speichern</a><br/>
					<a class="warning" href="/{$subjectid}/administration/removeUGC" target="_blank">User Generated Content entfernen</a><br/>
				</div>
			</td><td width="15"></td><td>
				<div class="eyecandy">
					<h1>Infos</h1>
					Number of Albums: <xsl:value-of select="$jukebox/albums" /><br/>
					Number of Artists: <xsl:value-of select="$jukebox/artists" /><br/>
					Number of Tracks: <xsl:value-of select="$jukebox/tracks" /><br/>
					Number of Playlists: <xsl:value-of select="$jukebox/playlists" />
				</div>
			</td></tr>
			</table>
		</div>
	</body>
	</html>
	</xsl:template>

</xsl:stylesheet>