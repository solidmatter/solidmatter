<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet 
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform" 
	version="1.0" 
	exclude-result-prefixes="html" 
	xmlns:html="http://www.w3.org/1999/xhtml">
	
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
		<!--<link rel="stylesheet" href="{$stylesheets_css}/styles_default.css" type="text/css" media="all" />-->
		<xsl:for-each select="/response/metadata/modules/*">
			<link rel="stylesheet" href="/theme/{name()}/css/styles.css" type="text/css" media="all" />
		</xsl:for-each>
		<script type="text/javascript" src="{$scripts_js}/prototype/prototype.js"></script>
		<script type="text/javascript">
			
			function confirm (sURL) {
			
				//window.location = sURL;
				
				var myAjaxRequest = new Ajax.Request( 
					sURL, 
					{
						method: 'get', 
						parameters: null,
						asynchronous: false 
					}
				);
				window.opener.sbUtilities.closeModal(true);
			}
			
		</script>
	</head>
	<body class="confirm" onunload="window.opener.sbUtilities.closeModal();">
		<xsl:apply-templates select="response/errors" />
		<xsl:apply-templates select="response/content/confirm" />
	</body>
	</html>
	</xsl:template>
	
	<xsl:template match="response/content/confirm">
		<div>
		<xsl:choose>
			<xsl:when test="@type='delete'">
				<h1>Delete</h1> 
				<span class="type {child/sbnode/@displaytype}"><xsl:value-of select="child/sbnode/@label" /></span>
				from
				<span class="type {parent/sbnode/@displaytype}"><xsl:value-of select="parent/sbnode/@label" /></span>
				<br /><br />
				<xsl:if test="../references">
					<h2>References:</h2>
					<ul>
					<xsl:for-each select="../references/sbnode">
						<li><a href="/{@uuid}" class="type {@displaytype}"><xsl:value-of select="@label" /></a></li>
					</xsl:for-each>
					</ul>
				</xsl:if>
				<xsl:if test="../softlinks">
					<h2>Softlinks:</h2>
					<ul>
					<xsl:for-each select="../softlinks/sbnode">
						<li><a href="/{@uuid}" class="type {@displaytype}"><xsl:value-of select="@label" /></a></li>
					</xsl:for-each>
					</ul>
				</xsl:if>
				<a class="ok" href="javascript:confirm('{@url_confirm}/-/structure/deleteChild/?parentnode={parent/sbnode/@uuid}&amp;childnode={child/sbnode/@uuid}&amp;confirm=true');" style="float:left">OK</a>
				<!--<a class="ok" target="_blank" href="{@url_confirm}/-/structure/deleteChild/?parentnode={parent/sbnode/@uuid}&amp;childnode={child/sbnode/@uuid}&amp;confirm=true" style="float:left">OK</a>-->
				<a class="cancel" href="javascript:window.close();" style="float:right">Cancel</a>
				<hr class="clear" />
			</xsl:when>
		</xsl:choose>
		</div>
	</xsl:template>
			
</xsl:stylesheet>