<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet 
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform" 
	version="1.0" 
	exclude-result-prefixes="html" 
	xmlns:html="http://www.w3.org/1999/xhtml"
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
			<xsl:apply-templates select="response/content" />
		</div>
	</body>
	</html>
	</xsl:template>
	
	<xsl:template match="response/content">
		
		<a class="type sb_logs" href="/{sbnode/@uuid}/system/show_log/?log=access" target="details">Access log</a><br/>
		<a class="type sb_logs" href="/{sbnode/@uuid}/system/show_log/?log=exceptions" target="details">Exception log</a><br/>
		<a class="type sb_logs" href="/{sbnode/@uuid}/system/show_log/?log=database" target="details">Database log</a><br/><br/>
		
		<iframe name="details" src="" width="100%" height="80%" />
		
	</xsl:template>

</xsl:stylesheet>