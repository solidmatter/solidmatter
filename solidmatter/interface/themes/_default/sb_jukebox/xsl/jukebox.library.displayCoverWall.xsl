<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet 
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform" 
	version="1.0" 
	exclude-result-prefixes="html sbform" 
	exclude-element-prefixes="sbform xmlns" 
	xmlns:html="http://www.w3.org/1999/xhtml"
	xmlns:sbform="http://www.solidbytes.net/sbform"
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
			<xsl:call-template name="simplesearch">
				<xsl:with-param name="form" select="$content/sbform[@id='searchJukebox']" />
			</xsl:call-template>
		</div>
		<div class="content">
			<xsl:apply-templates select="response/errors" />
			<xsl:apply-templates select="$content/allAlbums" />
		</div>
	</xsl:template>
	
	<xsl:template match="allAlbums">
		
		<table class="default" width="100%" summary="CHANGEME">
			<thead>
				<tr>
					<th colspan="2"><span class="type album"><xsl:value-of select="$locale/sbJukebox/labels/all_albums" /></span></th>
				</tr>
			</thead>
			<tbody>
			<xsl:choose>
				<xsl:when test="resultset/row">
					<tr class="odd">
						<td style="text-align: center;">
							<xsl:for-each select="resultset/row">
								<div class="albumcover_wall">
									<a class="imglink" href="/{@uuid}" >
										<img height="100" width="100" src="/{@uuid}/details/getCover/size=100" alt="{@label}" title="{@label}" />
									</a><br />
								</div>
							</xsl:for-each>
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
	
</xsl:stylesheet>