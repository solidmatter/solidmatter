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
		<div class="toolbar">
			<form action="{$content/sbform[@id='changeWeighting']/@action}" method="post" class="simplesearch">
				<xsl:value-of select="$locale/sbJukebox/labels/weighting" />:
				<xsl:apply-templates select="$content/sbform[@id='changeWeighting']/sbinput[@type='select']" mode="inputonly" /> 
				<xsl:value-of select="' '" />
				<xsl:apply-templates select="$content/sbform[@id='changeWeighting']/submit" mode="inputonly" />
			</form>
		</div>
		<div class="nav">
			
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
				<xsl:call-template name="render_tags">
					<xsl:with-param name="group" select="'Genre:'" />
					<xsl:with-param name="tags" select="branchtags/tag[contains(., 'Genre:')]" />
					<xsl:with-param name="weighting" select="$content/weighting/weighting" />
				</xsl:call-template>
				<tr><th colspan="5" style="padding:4px 8px;"><hr style="border:1px solid #222;" /></th></tr>
				<xsl:call-template name="render_tags">
					<xsl:with-param name="group" select="''" />
					<xsl:with-param name="tags" select="branchtags/tag[not(contains(., 'Year:') or contains(., 'Genre:') or contains(., 'Encoding:') or contains(., 'Defects:') or contains(., 'Series:'))]" />
					<xsl:with-param name="weighting" select="$content/weighting/weighting" />
				</xsl:call-template>
				<!--<tr><th colspan="5" style="padding:4px 8px;"><hr style="border:1px solid #222;" /></th></tr>
				<xsl:call-template name="render_tags">
					<xsl:with-param name="group" select="'Series:'" />
					<xsl:with-param name="tags" select="branchtags/tag[contains(., 'Series:')]" />
					<xsl:with-param name="weighting" select="$content/weighting/weighting" />
				</xsl:call-template>-->
				<tr><th colspan="5" style="padding:4px 8px;"><hr style="border:1px solid #222;" /></th></tr>
				<xsl:call-template name="render_tags">
					<xsl:with-param name="group" select="'Year:'" />
					<xsl:with-param name="tags" select="branchtags/tag[contains(., 'Year:')]" />
					<xsl:with-param name="weighting" select="$content/weighting/weighting" />
				</xsl:call-template>
				<tr><th colspan="5" style="padding:4px 8px;"><hr style="border:1px solid #222;" /></th></tr>
				<xsl:call-template name="render_tags">
					<xsl:with-param name="group" select="'Encoding:'" />
					<xsl:with-param name="tags" select="branchtags/tag[contains(., 'Encoding:')]" />
					<xsl:with-param name="weighting" select="$content/weighting/weighting" />
				</xsl:call-template>
				<tr><th colspan="5" style="padding:4px 8px;"><hr style="border:1px solid #222;" /></th></tr>
				<xsl:call-template name="render_tags">
					<xsl:with-param name="group" select="'Defects:'" />
					<xsl:with-param name="tags" select="branchtags/tag[contains(., 'Defects:')]" />
					<xsl:with-param name="weighting" select="$content/weighting/weighting" />
				</xsl:call-template>
			</tbody>
		</table>
	</xsl:template>
	
	<xsl:template name="render_tags">
		<xsl:param name="title" />
		<xsl:param name="tags" />
		<xsl:param name="group" />
		<xsl:param name="weighting" />
		<xsl:if test="count($tags) > 0">
			<tr>
			<td>
				<div class="tagcontainer">
					<xsl:choose>
						<xsl:when test="$weighting='numItems'">
							<xsl:variable name="maxitems">
								<xsl:for-each select="$tags/@numitems">
									<xsl:sort data-type="number" order="descending" />
									<xsl:if test="position()=1"><xsl:value-of select="."/></xsl:if>
								</xsl:for-each>
							</xsl:variable>
							<xsl:variable name="minitems">
								<xsl:for-each select="$tags/@numitems">
									<xsl:sort data-type="number" order="ascending" />
									<xsl:if test="position()=1"><xsl:value-of select="."/></xsl:if>
								</xsl:for-each>
							</xsl:variable>
							<xsl:for-each select="$tags">
								<a>
									<xsl:attribute name="href">/-/tags/listItems/?tagid=<xsl:value-of select="@id" /></xsl:attribute>
									<xsl:attribute name="style">font-size:<xsl:value-of select="round(11 + 12 * (@numitems) div $maxitems)" />px;</xsl:attribute>
									<xsl:value-of select="substring-after(., $group)" />
								</a>
								<xsl:if test="position() != last()">
									<xsl:value-of select="' '" />
								</xsl:if>
							</xsl:for-each>
						</xsl:when>
						<xsl:otherwise>
							<xsl:variable name="maxpopularity">
								<xsl:for-each select="$tags/@popularity">
									<xsl:sort data-type="number" order="descending" />
									<xsl:if test="position()=1"><xsl:value-of select="."/></xsl:if>
								</xsl:for-each>
							</xsl:variable>
							<xsl:variable name="minpopularity">
								<xsl:for-each select="$tags/@popularity">
									<xsl:sort data-type="number" order="ascending" />
									<xsl:if test="position()=1"><xsl:value-of select="."/></xsl:if>
								</xsl:for-each>
							</xsl:variable>
							<xsl:for-each select="$tags">
								<a>
									<xsl:attribute name="href">/-/tags/listItems/?tagid=<xsl:value-of select="@id" /></xsl:attribute>
									<xsl:attribute name="style">font-size:<xsl:value-of select="round(11 + 12 * (@popularity) div $maxpopularity)" />px;</xsl:attribute>
									<xsl:value-of select="substring-after(., $group)" />
								</a>
								<xsl:if test="position() != last()">
									<xsl:value-of select="' '" />
								</xsl:if>
							</xsl:for-each>
						</xsl:otherwise>
					</xsl:choose>
				</div>
				</td>
			</tr>
		</xsl:if>
	</xsl:template>

</xsl:stylesheet>