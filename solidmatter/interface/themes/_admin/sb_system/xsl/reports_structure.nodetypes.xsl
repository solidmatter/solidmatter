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
		
		<table class="default">
			<thead>
				<tr>
					<th colspan="4">Knotentypen</th>
				</tr>
				<tr class="th2">
					<th>Knotentyp</th>
					<th>interner Knotentyp</th>
					<th>Anzahl</th>
					<th>Unverlinkt</th>
				</tr>
			</thead>
			<tbody>
			<xsl:for-each select="nodetypes/row">
				<xsl:variable name="nodetype" select="@s_type" />
				<tr>
					<xsl:choose>
						<xsl:when test="position() mod 2 = 1">
							<xsl:attribute name="class">odd</xsl:attribute>
						</xsl:when>
						<xsl:otherwise>
							<xsl:attribute name="class">even</xsl:attribute>
						</xsl:otherwise>
					</xsl:choose>
					<td><span class="type {@s_displaytype}"><xsl:call-template name="localize"><xsl:with-param name="label">$locale//nodetypes/type[@id='<xsl:value-of select="$nodetype" />']</xsl:with-param></xsl:call-template></span></td>
					<td><xsl:value-of select="@s_type" /></td>
					<td><xsl:value-of select="@num_nodes" /></td>
					<td><xsl:value-of select="@num_lostnodes" /></td>
				</tr>
			</xsl:for-each>
			</tbody>
			<tfoot><tr><td colspan="4"></td></tr></tfoot>
		</table>
	</xsl:template>
	
</xsl:stylesheet>