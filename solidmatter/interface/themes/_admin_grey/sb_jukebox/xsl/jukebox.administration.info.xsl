<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet 
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform" 
	version="1.0" 
	exclude-result-prefixes="html" 
	xmlns:html="http://www.w3.org/1999/xhtml"
	xmlns:sbform="http://www.solidbytes.net/sbform"
	xmlns:dyn="http://exslt.org/dynamic" extension-element-prefixes="dyn">

	<xsl:import href="../../sb_system/xsl/global.views.xsl" />
	<xsl:import href="../../sb_system/xsl/global.default.xsl" />
	<xsl:import href="../../sb_system/xsl/global.sbform.xsl" />
	
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
			Number of Albums: <xsl:value-of select="$content/library/library/albums" /><br/>
			Number of Artists: <xsl:value-of select="$content/library/library/artists" /><br/>
			Number of Tracks: <xsl:value-of select="$content/library/library/tracks" /><br/>
			Number of Playlists: <xsl:value-of select="$content/library/library/playlists" /><br/>
			<br/>
			<br/>
			<a href="/{$subjectid}/administration/clearQuilts">Quilts löschen</a>
			<br/>
			<br/>
			<a href="/{$subjectid}/administration/startImport" target="_blank">Import starten</a><br/>
			<a href="/{$subjectid}/administration/startImport/dry=true" target="_blank">Import starten (Trockenlauf)</a><br/>
			<a href="/{$subjectid}/administration/clearLibrary">Bibliothek löschen</a>
			<br/>
			<br/>
			<a href="/{$subjectid}/administration/storeUGC" target="_blank">User Generated Content speichern</a><br/>
			<a href="/{$subjectid}/administration/removeUGC" target="_blank">User Generated Content entfernen</a><br/>
		</div>
	</body>
	</html>
	</xsl:template>

</xsl:stylesheet>