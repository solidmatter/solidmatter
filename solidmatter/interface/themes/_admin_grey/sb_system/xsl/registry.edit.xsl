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
			<xsl:apply-templates select="response/content/sbform[@id='registry']" />
		</div>
	</body>
	</html>
	</xsl:template>
	
	<xsl:template match="response/content/sbform[@id='registry']">
		<form action="{@action}" method="post">
			<table class="default">
				<tr><th colspan="3"><xsl:value-of select="dyn:evaluate(@label)" /></th></tr>	
				<xsl:for-each select="sbinput">
					<tr>
						<xsl:call-template name="colorize" />
						<td width="30%">
							<span>
							<xsl:choose>
								<xsl:when test="@defaultvalue = ''">
									<xsl:attribute name="title">Default: [empty]</xsl:attribute>
								</xsl:when>
								<xsl:otherwise>
									<xsl:attribute name="title">Default: "<xsl:value-of select="@defaultvalue" />"</xsl:attribute>
								</xsl:otherwise>
							</xsl:choose>
							<xsl:choose>
								<xsl:when test="@default = 'FALSE'">
									<strong><xsl:value-of select="translate(@name, '_', '.')" /></strong>
								</xsl:when>
								<xsl:otherwise>
									<xsl:value-of select="translate(@name, '_', '.')" />
								</xsl:otherwise>
							</xsl:choose>
							</span>
						</td>
						<td width="10%"><xsl:value-of select="fg" /></td>
						<td width="60%">
							<xsl:apply-templates select="." mode="inputonly" /> 
						</td>
					</tr>
				</xsl:for-each>
				<tr class="lastline">
					<td colspan="2"></td>
					<td><xsl:apply-templates select="submit" mode="inputonly" /></td>
				</tr>
			</table>
		</form>
	</xsl:template>

</xsl:stylesheet>