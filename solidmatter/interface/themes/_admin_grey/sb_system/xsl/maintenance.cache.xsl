<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet 
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform" 
	version="1.0" 
	exclude-result-prefixes="html" 
	xmlns:html="http://www.w3.org/1999/xhtml"
	xmlns:sbform="http://www.solidbytes.net/sbform"
	xmlns:dyn="http://exslt.org/dynamic" extension-element-prefixes="dyn">

	<xsl:import href="global.views.xsl" />
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
			<a href="/{$subjectid}/-/clearCache/?cache=paths">Clear PathCache</a><br/>
			<a href="/{$subjectid}/-/clearCache/?cache=images">Clear ImageCache</a><br/>
			<a href="/{$subjectid}/-/clearCache/?cache=authorisations">Clear AuthorisationCache</a><br/>
			<a href="/{$subjectid}/-/clearCache/?cache=misc">Clear MiscCache</a><br/>
			<a href="/{$subjectid}/-/clearCache/?cache=repository">Clear RepositoryCache</a><br/><br/>
			<a href="/{$subjectid}/-/clearCache/?cache=all">Clear ALL Caches</a><br/>
		</div>
	</body>
	</html>
	</xsl:template>

</xsl:stylesheet>