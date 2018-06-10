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
					<th colspan="13">Tables</th>
				</tr>
				<tr class="th2">
					<th>Name</th>
					<th>Engine</th>
					<th>Row_format</th>
					<th>Rows</th>
					<th>Avg_row_Length</th>
					<th>Data_length</th>
					<!--<th>Max_Data_Length</th>-->
					<th>Index_length</th>
					<th>Data_free</th>
					<th>Collation</th>
				</tr>
			</thead>
			<tbody>
			<xsl:for-each select="tables/row">
				<tr>
					<xsl:choose>
						<xsl:when test="position() mod 2 = 1">
							<xsl:attribute name="class">odd</xsl:attribute>
						</xsl:when>
						<xsl:otherwise>
							<xsl:attribute name="class">even</xsl:attribute>
						</xsl:otherwise>
					</xsl:choose>
					<td><xsl:value-of select="Name" /></td>
					<td><xsl:value-of select="Engine" /></td>
					<td><xsl:value-of select="Row_format" /></td>
					<td><xsl:value-of select="Rows" /></td>
					<td><xsl:value-of select="Avg_row_length" /></td>
					<td><xsl:value-of select="Data_length" /></td>
					<!--<td><xsl:value-of select="Max_data_length" /></td>-->
					<td><xsl:value-of select="Index_length" /></td>
					<td><xsl:value-of select="Data_free" /></td>
					<td><xsl:value-of select="Collation" /></td>
				</tr>
			</xsl:for-each>
			</tbody>
			<tfoot><tr><td colspan="9"></td></tr></tfoot>
		</table>
	</xsl:template>
	
</xsl:stylesheet>