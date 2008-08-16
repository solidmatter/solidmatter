<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet 
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform" 
	version="1.0" 
	exclude-result-prefixes="html" 
	xmlns:html="http://www.w3.org/1999/xhtml"
>

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
			<xsl:apply-templates select="response/content" />
		</div>
	</body>
	</html>
	</xsl:template>
	
	<xsl:template match="response/content">
		
		<table class="invisible">
			<tr>
				<td>
					<table class="default">
						<tr>
							<th colspan="2">Status</th>
						</tr>
						<tr class="th2">
							<th>Attribut</th>
							<th>Wert</th>
						</tr>
						<xsl:for-each select="status/row">
							<tr>
								<xsl:choose>
									<xsl:when test="position() mod 2 = 1">
										<xsl:attribute name="class">odd</xsl:attribute>
									</xsl:when>
									<xsl:otherwise>
										<xsl:attribute name="class">even</xsl:attribute>
									</xsl:otherwise>
								</xsl:choose>
								<td><xsl:value-of select="Variable_name" /></td>
								<td><xsl:value-of select="Value" /></td>
							</tr>
						</xsl:for-each>
					</table>
				</td>
				<td>
					<table class="default" style="width:40%; float:left;">
						<tr>
							<th colspan="2">Variablen</th>
						</tr>
						<tr class="th2">
							<th>Attribut</th>
							<th>Wert</th>
						</tr>
						<xsl:for-each select="variables/row">
							<tr>
								<xsl:choose>
									<xsl:when test="position() mod 2 = 1">
										<xsl:attribute name="class">odd</xsl:attribute>
									</xsl:when>
									<xsl:otherwise>
										<xsl:attribute name="class">even</xsl:attribute>
									</xsl:otherwise>
								</xsl:choose>
								<td><xsl:value-of select="Variable_name" /></td>
								<td><xsl:value-of select="Value" /></td>
							</tr>
						</xsl:for-each>
					</table>
				</td>
			</tr>
		</table>
		
	</xsl:template>
	
</xsl:stylesheet>