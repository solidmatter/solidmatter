<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet 
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform" 
	version="1.0" 
	exclude-result-prefixes="html sbform php" 
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
		<xsl:apply-templates select="$content/sbnode[@master]" />
	</xsl:template>
	
	<xsl:template match="sbnode">
			
			<table class="hidden" width="100%">
				<tbody>
				<xsl:choose>
					<xsl:when test="children[@mode='tracks']/sbnode">
						<xsl:for-each select="children[@mode='tracks']/sbnode">
							<tr>
								<!--<xsl:call-template name="colorize" />
								<td width="80">
									<xsl:call-template name="render_stars">
									</xsl:call-template>
								</td>-->
								<td width="10" style="text-align: right;">
									<xsl:value-of select="@info_index" />.
								</td>
								<td>
									<a href="/{@uuid}"><xsl:value-of select="@label" /></a>
								</td>
								<td width="10" style="text-align: right;">
									<xsl:value-of select="@info_playtime" />
								</td>
								<!--<td width="150" style="text-align: right;">
									<xsl:call-template name="render_buttons" />
								</td>-->
							</tr>
						</xsl:for-each>
					</xsl:when>
					<xsl:otherwise>
						<tr><td colspan="5"><xsl:value-of select="$locale/sbSystem/texts/no_subobjects" /></td></tr>
					</xsl:otherwise>
				</xsl:choose>
				
				</tbody>
			</table>
			<br />
		
	</xsl:template>

</xsl:stylesheet>