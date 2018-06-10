<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet 
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform" 
	version="1.0" 
	exclude-result-prefixes="html" 
	xmlns:html="http://www.w3.org/1999/xhtml"
>

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
				<td width="50%">
					<table class="default">
						<thead>
							<tr>
								<th colspan="2">Status</th>
							</tr>
							<tr class="th2">
								<th>Attribut</th>
								<th>Wert</th>
							</tr>
						</thead>
						<tbody>
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
								<td width="30%"><xsl:value-of select="Variable_name" /></td>
								<td width="70%"><xsl:value-of select="Value" /></td>
							</tr>
						</xsl:for-each>
						</tbody>
						<tfoot><tr><td colspan="2"></td></tr></tfoot>
					</table>
				</td>
				<td><div class="spacer"></div></td>
				<td width="50%">
					<table class="default">
						<thead>
							<tr>
								<th colspan="2">Variablen</th>
							</tr>
							<tr class="th2">
								<th>Attribut</th>
								<th>Wert</th>
							</tr>
						</thead>
						<tbody>
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
								<td width="30%"><xsl:value-of select="Variable_name" /></td>
								<td width="70%"><div style="word-wrap:break-word; overflow-wrap: break-word; width:400px;"><xsl:value-of select="Value" /></div></td>
							</tr>
						</xsl:for-each>
						</tbody>
						<tfoot><tr><td colspan="2"></td></tr></tfoot>
					</table>
				</td>
			</tr>
		</table>
		
	</xsl:template>
	
</xsl:stylesheet>