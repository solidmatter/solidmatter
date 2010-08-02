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
			<xsl:apply-templates select="response/content/tags" />
		</div>
	</body>
	</html>
	</xsl:template>
	
	<xsl:template match="response/content/tags">
		
		
		
		<table class="default">
			<tr><th colspan="4">Tags</th>
				<th colspan="2">
					<span style="float:right;"><a href="/{$master/@uuid}/manage/clearUnused">Clear Unused Tags</a></span>
				</th></tr>	
			<tr class="th2">
				<th>Tag</th>
				<th>NumItems</th>
				<th>Popularity</th>
				<th>Custom Weight</th>
				<th>Visibility</th>
				<th></th>
			</tr>
			<xsl:for-each select="row">
				<tr>
					<xsl:call-template name="colorize" />
					<td width="20%"><xsl:value-of select="@s_tag" /></td>
					<td width="20%"><xsl:value-of select="@n_numitemstagged" /></td>
					<td width="20%"><xsl:value-of select="@n_popularity" /></td>
					<td width="20%"><xsl:value-of select="@n_customweight" /></td>
					<td width="20%"><xsl:value-of select="@e_visibility" /></td>
					<td width="0%"><a href="/{$master/@uuid}/manage/edit/?tagid={@id}" class="option"><img src="/theme/sb_system/icons/doc_edit.gif" /></a></td>
				</tr>
			</xsl:for-each>
		</table>
	</xsl:template>

</xsl:stylesheet>