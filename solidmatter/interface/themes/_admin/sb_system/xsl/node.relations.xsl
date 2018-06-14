<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet 
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform" 
	version="1.0" 
	exclude-result-prefixes="html" 
	xmlns:html="http://www.w3.org/1999/xhtml"
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
			<xsl:apply-templates select="$content/sbform[@id='addRelation']" />
			<xsl:apply-templates select="$content/sbnode[@master]" />
		</div>
	</body>
	</html>
	</xsl:template>
		
	<xsl:template match="sbnode">
		<form name="massaction" action="/sdsdds">
		<table class="default" width="100%" id="list">
			<thead>
				<tr>
					<!-- <th></th> -->
					<th width="30%"><xsl:value-of select="$locale/sbSystem/labels/type" /></th>
					<th><xsl:value-of select="$locale/sbSystem/labels/name" /></th>
					<th width="1px"><xsl:value-of select="$locale/sbSystem/labels/options" /></th>
				</tr>
			</thead>
			<tbody>
			<xsl:choose>
				<xsl:when test="existingRelations/relation">
					<xsl:for-each select="existingRelations/relation">
						<tr>
							<xsl:call-template name="colorize" />
							<!-- <td width="1"><input type="checkbox" name="marker" id="marker_{@uuid}" /></td> -->
							<td>
								<xsl:choose>
								<xsl:when test="dyn:evaluate(concat('$locale//relations/relation[@id=&quot;', @id, '&quot;]'))">
									<span title="{@id}"><xsl:value-of select="dyn:evaluate(concat('$locale//relations/relation[@id=&quot;', @id, '&quot;]'))"/></span>
								</xsl:when>
								<xsl:otherwise>
									<xsl:value-of select="@id" />
								</xsl:otherwise>
								</xsl:choose>
							</td>
							<td>
								<a href="/{@target_uuid}" class="type {translate(@target_nodetype, ':', '_')}">
									<xsl:value-of select="@target_label" />
								</a>
							</td>
							<td>
								<xsl:if test="boolean('true')">
									<a class="option warning" href="/{$master/@uuid}/relations/remove/?type_relation={@id}&amp;target_relation={@target_uuid}" title="{$locale/sbJukebox/actions/remove}"><img src="/theme/sb_system/icons/doc_delete.gif" /></a>
								</xsl:if>
							</td>
						</tr>
					</xsl:for-each>
				</xsl:when>
				<xsl:otherwise>
					<tr><td colspan="6"><xsl:value-of select="$locale/sbSystem/texts/no_subobjects" /></td></tr>
				</xsl:otherwise>
			</xsl:choose>
			</tbody>
			<tfoot><tr><td colspan="3"></td></tr></tfoot>
		</table>	
		</form>
	</xsl:template>

</xsl:stylesheet>