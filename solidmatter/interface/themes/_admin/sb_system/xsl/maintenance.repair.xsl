<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet 
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform" 
	version="1.0" 
	exclude-result-prefixes="html" 
	xmlns:html="http://www.w3.org/1999/xhtml"
	xmlns:sbform="http://www.solidbytes.net/sbform"
	xmlns:dyn="http://exslt.org/dynamic" extension-element-prefixes="dyn">

	<xsl:import href="global.default.xsl" />
	
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
			<a href="/{$subjectid}/repair/rebuildMaterializedPaths">Rebuild MaterializedPath</a><br/>
			<a href="/{$subjectid}/repair/rebuildAuthorisationCache">Rebuild AuthorisationCache</a><br/>
			<br/>
			<a href="/{$subjectid}/repair/gatherAbandonedNodes">Gather abandoned Nodes in Trashcan</a><br/>
			<a href="/{$subjectid}/repair/removeAbandonedProperties">remove abandoned Properties</a><br/>
			<a href="/{$subjectid}/repair/removeAbandonedNodes">remove abandoned Nodes</a><br/>
			<br/>
			<a href="/{$subjectid}/repair/optimizeUUIDs" target="_blank">optimize UUIDs (convert to sbUUIds)</a>
		</div>
	</body>
	</html>
	</xsl:template>

</xsl:stylesheet>