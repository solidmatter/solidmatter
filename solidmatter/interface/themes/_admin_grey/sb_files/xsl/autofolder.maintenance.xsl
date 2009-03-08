<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet 
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform" 
	version="1.0" 
	exclude-result-prefixes="html" 
	xmlns:html="http://www.w3.org/1999/xhtml"
	xmlns:sbform="http://www.solidbytes.net/sbform"
	extension-element-prefixes="sbform">

	<xsl:import href="../../sb_system/xsl/global.default.xsl" />
	<xsl:import href="../../sb_system/xsl/global.views.xsl" />
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
		<!--<script type="text/javascript" src="{$scripts_js}/prototype/prototype.js"></script>
		<script type="text/javascript" src="{$scripts_js}/scriptaculous/scriptaculous.js"></script>-->
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
		<a href="/{$subjectid}/maintenance/check">Check</a><br />
		<a href="/{$subjectid}/maintenance/update">Update</a><br />
		<a href="/{$subjectid}/maintenance/clear">Clear</a>
		<xsl:apply-templates select="update_log" />
	</xsl:template>

	<xsl:template match="update_log">
		<ul>
		<xsl:for-each select="update_log/entry">
			<li>
				<xsl:value-of select="filename" /> (<xsl:value-of select="type" />)
			</li>
		</xsl:for-each>
		</ul>
	</xsl:template>
	
</xsl:stylesheet>