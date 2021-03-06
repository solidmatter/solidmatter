<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet 
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform" 
	version="1.0" 
	exclude-result-prefixes="html" 
	xmlns:html="http://www.w3.org/1999/xhtml">

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
			<ul>
				<xsl:apply-templates select="response/content/array" />
			</ul>
		</div>
	</body>
	</html>
	</xsl:template>
	
	<xsl:template match="array">
		<li>
			[<xsl:value-of select="@name"/>] => 
			<ul>
				<xsl:apply-templates />
			</ul>
		</li>
	</xsl:template>
	
	<xsl:template match="item">
		<li>
			[<xsl:value-of select="@name" />] => "<xsl:value-of select="@value" />"
		</li>		
	</xsl:template>
	
				
</xsl:stylesheet>