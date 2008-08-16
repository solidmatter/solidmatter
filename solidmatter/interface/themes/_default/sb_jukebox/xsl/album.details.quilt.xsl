<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet 
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform" 
	version="1.0" 
	exclude-result-prefixes="html sbform php" 
	exclude-element-prefixes="html sbform" 
	xmlns:html="http://www.w3.org/1999/xhtml"
	xmlns:sbform="http://www.solidbytes.net/sbform"
	xmlns:dyn="http://exslt.org/dynamic" 
	extension-element-prefixes="dyn"
	xmlns:php="http://php.net/xsl"
	>

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
				<xsl:with-param name="form" select="$content/sbform[@id='searchAlbums']" />
			</xsl:call-template>
			<xsl:call-template name="render_alphanum">
				<xsl:with-param name="url" select="'/-/albums/-/show='"/>
			</xsl:call-template>
		</div>
		<div class="content">
			<xsl:apply-templates select="response/errors" />
			<xsl:apply-templates select="$content/sbnode[@master]" />
		</div>
	</xsl:template>
	
	<xsl:template match="sbnode">
		
		<div class="albumcontainer">
			
			<h2>
				<div class="albumdetails" style="float:right;">
					<xsl:call-template name="render_buttons" />
					Vote: 
					<xsl:call-template name="render_stars">
						<xsl:with-param name="voting" select="1" />
					</xsl:call-template>
				</div>
				<span class="type album"><xsl:value-of select="@label" /></span>
			</h2>
			
			<table class="default" width="100%">
				<tbody>
					<tr class="odd">
						<td style="padding:10px;" width="160">
							<a class="imglink" target="_blank" href="/{$master/@uuid}/details/getCover"><img height="150" width="150" src="/{$master/@uuid}/details/getCover/size=150" alt="cover" /></a>
							<a href="/{$master/@uuid}/details">back</a>
						</td>
						<td style="padding:10px;">
							<xsl:for-each select="$content/quilt/row">
								<div style="clear:both;">
									<xsl:for-each select="column">
										<xsl:choose>
											<xsl:when test="@uuid">
												<a style="float:left;" class="imglink" href="/{@uuid}" title="{@label}"><img src="/{@uuid}/details/getCover/size=20" alt="cover" /></a>
											</xsl:when>
											<xsl:otherwise>
												<div style="float:left;width:20px;height:20px;background-color: rgb({@lightness}, {@lightness}, {@lightness});"></div>
											</xsl:otherwise>
										</xsl:choose>
									</xsl:for-each>
								</div>
							</xsl:for-each>
						</td>
					</tr>
				</tbody>
			</table>
			
		</div>
	</xsl:template>

</xsl:stylesheet>