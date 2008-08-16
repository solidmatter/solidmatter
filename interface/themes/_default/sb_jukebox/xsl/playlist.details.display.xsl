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
				<xsl:with-param name="form" select="$content/sbform[@id='searchPlaylists']" />
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
					<xsl:call-template name="render_stars">
						<xsl:with-param name="voting" select="1" />
					</xsl:call-template>
				</div>
				<span class="type playlist"><xsl:value-of select="@label" /></span>
			</h2>
			
			<!--<table class="default" width="100%">
				<thead>
					<tr>
						<th colspan="3">
							<span style="float: right;">
								<a class="type play" href="/{@uuid}/details/getM3U/playlist.m3u?sid={$sessionid}">Play</a>
							</span>
							<span class="type track">Tracks</span>
						</th>
					</tr>
				</thead>
				<tbody>
				<xsl:choose>
					<xsl:when test="children[@mode='tracks']/sbnode">
						<xsl:for-each select="children[@mode='tracks']/sbnode">
							<tr>
								<xsl:call-template name="colorize" />
								<td style="position:relative;top:0;left:0;">
									<a style="float:right;" class="type remove" href="/{$master/@uuid}/details/removeItem/item={@uuid}">remove</a>
									<a href="/{@uuid}"><xsl:value-of select="@label" /></a>
								</td>
							</tr>
						</xsl:for-each>
					</xsl:when>
					<xsl:otherwise>
						<tr><td colspan="5"><xsl:value-of select="$locale/system/general/texts/no_subobjects" /></td></tr>
					</xsl:otherwise>
				</xsl:choose>
				
				</tbody>
			</table>-->
			
			<table class="default" width="100%">
				<thead>
					<tr>
						<th colspan="3">
							<span style="float: right;">
								<a class="type play" href="/{@uuid}/details/getM3U/playlist.m3u?sid={$sessionid}">Play</a>
							</span>
							<span class="type track">Tracks</span>
						</th>
					</tr>
				</thead>
			</table>
			
			<ul class="sortable" width="100%" id="playlist">
				<xsl:choose>
					<xsl:when test="children[@mode='tracks']/sbnode">
						<xsl:for-each select="children[@mode='tracks']/sbnode">
							<li style="position:relative;top:0;left:0;" id="item_{@uuid}">
								<xsl:call-template name="colorize" />
								<a style="float: right;" class="type remove" href="javascript:remove('{@uuid}')">remove</a>
								<a href="/{@uuid}"><xsl:value-of select="@label" /></a>
							</li>
						</xsl:for-each>
					</xsl:when>
					<xsl:otherwise>
						<li><xsl:value-of select="$locale/system/general/texts/no_subobjects" /></li>
					</xsl:otherwise>
				</xsl:choose>
			</ul>
		
			<script language="Javascript">
				
				//Sortable.create('playlist', { onUpdate: redraw } );
				Sortable.create('playlist', { onChange: redraw } );
				
				function remove(sUUID) {
					
					var sURL = "/<xsl:value-of select="$master/@uuid" />/details/removeItem/item=" + sUUID + '&amp;silent';
					var myAjaxRemover = new Ajax.Request(
						sURL, 
						{
							method: 'get', 
							parameters: null
						}
					);
					$('item_' + sUUID).fade({ afterFinish: redraw });
					
				}
				
				function redraw() {
					//alert('redraw');
					var sClass = 'odd';
					var eChildren = $('playlist').childElements();
					for (var i=0; i&lt;eChildren.length; i++) {
						if (eChildren[i].style.display == 'none') {
							continue;
						}
						eChildren[i].className = sClass;
						if (sClass == 'odd') {
							sClass = 'even';
						} else {
							sClass = 'odd';
						}
					}
				}
				
			</script>
			
		</div>
			
		<xsl:call-template name="comments" />
		
	</xsl:template>

</xsl:stylesheet>