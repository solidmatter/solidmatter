<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet 
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform" 
	version="1.0" 
	exclude-result-prefixes="html sbform php" 
	exclude-element-prefixes="html sbform" 
	xmlns:html="http://www.w3.org/1999/xhtml"
	xmlns:sbform="http://www.solidbytes.net/sbform"
	xmlns:php="http://php.net/xsl"
	xmlns:dyn="http://exslt.org/dynamic" 
	extension-element-prefixes="dyn">

	<xsl:import href="global.default.xsl" />
	
	<xsl:output 
		method="html"
		encoding="UTF-8"
		standalone="yes"
		indent="yes"
		doctype-system="http://www.w3.org/TR/html4/loose.dtd" 
		doctype-public="-//W3C//DTD HTML 4.01 Transitional//EN"
	/>
	
	<xsl:template match="/">
		<xsl:call-template name="layout" />
	</xsl:template>
	
	<xsl:template name="content">
		<div class="nav">
			<form action="{$content/sbform[@id='changeWeighting']/@action}" method="post" class="simplesearch">
				<xsl:value-of select="$locale/sbJukebox/labels/weighting" />:
				<xsl:apply-templates select="$content/sbform[@id='changeWeighting']/sbinput[@type='select']" mode="inputonly" /> 
				<xsl:value-of select="' '" />
				<xsl:apply-templates select="$content/sbform[@id='changeWeighting']/submit" mode="inputonly" />
			</form>
		</div>
		<div class="content">
			<xsl:apply-templates select="response/errors" />
			<xsl:apply-templates select="$content/sbnode[@master]" />
		</div>
	</xsl:template>
	
	<xsl:template match="sbnode">
		
		<table class="default" width="100%" summary="">
			<thead>
				<tr>
					<th colspan="2"><span class="type tag"><xsl:value-of select="$locale/sbJukebox/labels/all_tags" /></span></th>
				</tr>
			</thead>
			<tbody>
				<xsl:choose>
					<xsl:when test="branchtags/tag">
						<tr>
							<td>
								<div class="tagcontainer">
									<xsl:call-template name="render_tags">
										<xsl:with-param name="weighting" select="$content/weighting/weighting" />
									</xsl:call-template>
								</div>
							</td>
						</tr>
					</xsl:when>
					<xsl:otherwise>
						<tr><td colspan="5"><xsl:value-of select="$locale/sbSystem/texts/no_subobjects" /></td></tr>
					</xsl:otherwise>
				</xsl:choose>
			</tbody>
		</table>
	</xsl:template>
	
	<xsl:template name="render_tags">
		<xsl:param name="weighting" />
		<xsl:choose>
			<xsl:when test="$weighting='numItems'">
				<xsl:variable name="maxitems" select="php:function('max_value', branchtags/tag/@numitems)" />
				<xsl:variable name="minitems" select="php:function('min_value', branchtags/tag/@numitems)" />
				<xsl:for-each select="branchtags/tag">
					<a>
						<xsl:attribute name="href">/-/tags/listItems/?tagid=<xsl:value-of select="@id" /></xsl:attribute>
						<xsl:attribute name="style">font-size:<xsl:value-of select="round(12 + 10 * (@numitems) div $maxitems)" />px;</xsl:attribute>
						<xsl:value-of select="." />
					</a>
					<xsl:if test="position() != last()">
						<xsl:value-of select="' '" />
					</xsl:if>
				</xsl:for-each>
			</xsl:when>
			<xsl:otherwise>
				<xsl:variable name="maxpopularity" select="php:function('max_value', branchtags/tag/@popularity)" />
				<xsl:variable name="minpopularity" select="php:function('min_value', branchtags/tag/@popularity)" />
				<xsl:for-each select="branchtags/tag">
					<a>
						<xsl:attribute name="href">/-/tags/listItems/?tagid=<xsl:value-of select="@id" /></xsl:attribute>
						<xsl:attribute name="style">font-size:<xsl:value-of select="round(12 + 10 * (@popularity) div $maxpopularity)" />px;</xsl:attribute>
						<xsl:value-of select="." />
					</a>
					<xsl:if test="position() != last()">
						<xsl:value-of select="' '" />
					</xsl:if>
				</xsl:for-each>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>

</xsl:stylesheet>