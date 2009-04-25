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
		<div class="toolbar">
			<xsl:call-template name="simplesearch">
				<xsl:with-param name="form" select="$content/sbform[@id='searchAlbums']" />
			</xsl:call-template>
		</div>
		<div class="nav">
			<a href="/{$master/@uuid}">back</a>
		</div>
		<div class="content">
			<xsl:apply-templates select="response/errors" />
			<xsl:apply-templates select="$content/sbnode[@master]" />
		</div>
	</xsl:template>
	
	<xsl:template match="sbnode">
		
		<div class="th">
			<span class="albumdetails" style="float:right;">
				<xsl:call-template name="addtag">
					<xsl:with-param name="form" select="$content/sbform[@id='addTag']" />
				</xsl:call-template>
				<span style="margin-left: 15px;"></span>
				<xsl:call-template name="render_buttons" />
				<span style="margin-left: 15px;"></span>
				<xsl:call-template name="render_stars" />
			</span>
			<span class="type album"><xsl:value-of select="@label" /></span>
		</div>
		
		<script type="text/javascript" language="javascript">
			function hidecover(oCover) {
				oCover.firstChild.firstChild.style.visibility = 'hidden';
			}
			function showcover(oCover) {
				oCover.firstChild.firstChild.style.visibility = 'visible';
			}
		</script>
		
		<table class="default" width="100%">
			<tbody>
				<tr class="odd">
					<td style="padding: 20px" width="500">
						<img src="/{@uuid}/details/getCover/?size=500" style="width:500px; height:500px;" alt="" />
					</td>
					<td style="padding: 20px 0 20px 0;">
						<table class="default">
							<xsl:for-each select="$content/quilt/row">
								<tr>
									<xsl:for-each select="column">
										<td style="width:20px;height:20px;background-color: rgb({@red}, {@green}, {@blue}); padding:0;">
											<a class="imglink" href="/{@uuid}/-/buildQuilt" title="{@label}"><img src="/{@uuid}/details/getCover/?size=20" alt="" /></a>
										</td>
									</xsl:for-each>
								</tr>
							</xsl:for-each>
						</table>
					</td>
				</tr>
			</tbody>
		</table>
	</xsl:template>

</xsl:stylesheet>