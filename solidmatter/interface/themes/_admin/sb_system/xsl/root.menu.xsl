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
	
	<xsl:variable name="menu_path" select="translate($content/menu/sbnode/@query, '/', ':')" />
	
	<xsl:template match="/">
		<xsl:apply-templates select="response/content/menu" />
		<xsl:apply-templates select="response/errors" />
	</xsl:template>
	
	<xsl:template match="menu">
		<xsl:choose>
			<xsl:when test="sbnode/@nodetype='sbSystem:Root'">
				<html>
					<head>
						<xsl:apply-templates select="/response/metadata" />
						<script type="text/javascript" src="{$scripts_js}/prototype/prototype.js"></script>
						<script type="text/javascript" src="{$scripts_js}/window/window.js"></script>
						<script type="text/javascript" src="{$scripts_js}/menu.js"></script>
					</head>
					<body onload="sbContextMenu.init();" style="padding:0;margin:0;">
						<div class="logo"><a style="height:38px" class="highlighted" href="/-/welcome" target="main"><img src="{$images}/logo_sm_banner.png" /></a></div>
						<div class="top_bar"></div>
						<div id="modalbackground"></div>
						<div id="contextmenu"></div>
						<div class="tree">
							<ul class="tree">
								<xsl:call-template name="root" />
							</ul>
						</div>
					</body>
				</html>
			</xsl:when>
			<xsl:when test="sbnode/@mode='tree_root'">
				<ul class="tree">
					<xsl:call-template name="menuentry">
						<xsl:with-param name="path" select="$menu_path" />
						<xsl:with-param name="firstnode" select="true" />
					</xsl:call-template>
				</ul>
			</xsl:when>
			<xsl:otherwise>
				<xsl:call-template name="reduced" />
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>
	
	<xsl:template name="root">
		<xsl:for-each select="*">
			<li>
				<xsl:call-template name="render_content">
					<xsl:with-param name="path" select="''" />
					<xsl:with-param name="mode" select="'tree_root'" />
				</xsl:call-template>
			</li>
		</xsl:for-each>
	</xsl:template>
	
	<xsl:template name="reduced">
		<xsl:for-each select="*">
			<xsl:call-template name="render_content">
				<xsl:with-param name="path" select="$menu_path" />
				<xsl:with-param name="firstnode" select="true" />
			</xsl:call-template>
		</xsl:for-each>
	</xsl:template>
	
	<xsl:template name="menuentry">
		<xsl:param name="path" />
		<xsl:param name="firstnode" />
		<xsl:for-each select="content[@mode='menu']/sbnode">
		<li>
			<xsl:choose>
				<xsl:when test="$firstnode=true">
					<xsl:attribute name="id">entry<xsl:value-of select="$path" /></xsl:attribute>
					<xsl:call-template name="render_content">
						<xsl:with-param name="path" select="$path" />
					</xsl:call-template>
				</xsl:when>
				<xsl:otherwise>
					<xsl:attribute name="id">entry<xsl:value-of select="$path" />:<xsl:value-of select="@name" /></xsl:attribute>
					<xsl:call-template name="render_content">
						<xsl:with-param name="path" select="concat($path, ':', @name)" />
					</xsl:call-template>
				</xsl:otherwise>
			</xsl:choose>
		</li>
		</xsl:for-each>
	</xsl:template>
	
	<xsl:template name="render_content">
		<xsl:param name="path" />
		<!-- icon -->
		<xsl:choose>
			<xsl:when test="@mode='tree_root'"></xsl:when>
			<xsl:when test="@subnodes = 0 and @nodetype != 'sb_system:trashcan'">
				<img src="/theme/sb_system/icons/tree_nothing.png" alt="" />
			</xsl:when>
			<xsl:otherwise>
				<a class="toggle" href="javascript:sbMenu.toggleMenuEntry('{$path}')">
					<xsl:choose>
					<xsl:when test="content/sbnode">
						<img id="icon{$path}" src="/theme/sb_system/icons/tree_close.gif" name="close" alt="close" />
					</xsl:when>
					<xsl:otherwise>
						<img id="icon{$path}" src="/theme/sb_system/icons/tree_open.gif" name="open" alt="open" />
					</xsl:otherwise>
					</xsl:choose>
				</a>
			</xsl:otherwise>
		</xsl:choose>
		<!-- label -->
		<xsl:choose>
			<xsl:when test="@displaytype ='sb_root'">
				<a href="/-/welcome" class="type sb_root" target="main">solidMatter</a>
			</xsl:when>
			<xsl:otherwise>
				
				<a class="label" target="main" id="{$path}">
				<xsl:attribute name="href">/<xsl:value-of select="@uuid" /></xsl:attribute>
					
					<!-- <xsl:if test="not(@primary='TRUE')">
						<img style="position: relative; left:-15; z-index: 1001;" src="/theme/sb_system/icons/link.png" />
					</xsl:if>
					<xsl:choose>
							<xsl:when test="not(@primary='TRUE')">
								<xsl:attribute name="src">/theme/sb_system/icons/link.png</xsl:attribute>
							</xsl:when>
							<xsl:otherwise>
								<xsl:attribute name="src">/theme/sb_system/icons/blank.gif</xsl:attribute>
							</xsl:otherwise>
						</xsl:choose> -->
					<img class="type {@displaytype}" style="padding-right: 3px; !important;">
						<xsl:if test="@custom_icon">
							<xsl:attribute name="style">background-image: url(../<xsl:value-of select="@custom_icon" />);</xsl:attribute>
						</xsl:if>
						<xsl:choose>
							<xsl:when test="not(@primary='TRUE')">
								<xsl:attribute name="src">/theme/sb_system/icons/link.png</xsl:attribute>
							</xsl:when>
							<xsl:otherwise>
								<xsl:attribute name="src">/theme/sb_system/icons/blank.gif</xsl:attribute>
							</xsl:otherwise>
						</xsl:choose>
					</img>
					
					<xsl:call-template name="localize"><xsl:with-param name="label" select="@label" /></xsl:call-template>					
					
				</a>
				<!-- <xsl:choose>
					<xsl:when test="not(@primary='TRUE')">
						<span style="padding:0;">
							<xsl:attribute name="class">type <xsl:value-of select="@displaytype" /></xsl:attribute>
							<xsl:if test="@custom_icon">
								<xsl:attribute name="style">background-image: url(../<xsl:value-of select="@custom_icon" />);</xsl:attribute>
							</xsl:if>
							<span
							<a class="link" target="main" id="{$path}">
								<xsl:attribute name="href">/<xsl:value-of select="@uuid" /></xsl:attribute>
								<xsl:call-template name="localize"><xsl:with-param name="label" select="@label" /></xsl:call-template>
							</a>
						</span>
					</xsl:when>
					<xsl:otherwise>
						<a target="main" id="{$path}">
							<xsl:attribute name="href">/<xsl:value-of select="@uuid" /></xsl:attribute>
							<span style="padding-right: 0; margin-right: 0;">
								<xsl:attribute name="class">type <xsl:value-of select="@displaytype" /></xsl:attribute>
								<xsl:if test="@custom_icon">
									<xsl:attribute name="style">background-image: url(../<xsl:value-of select="@custom_icon" />);</xsl:attribute>
								</xsl:if>
							</span>
							
							<xsl:call-template name="localize"><xsl:with-param name="label" select="@label" /></xsl:call-template>
						</a>
					</xsl:otherwise>
				</xsl:choose> -->
			</xsl:otherwise>
		</xsl:choose>
		<!-- children -->
		<xsl:if test="content[@mode='menu']/sbnode">
			<ul id="children{$path}">
				<xsl:call-template name="menuentry">
					<xsl:with-param name="path" select="$path" />
				</xsl:call-template>
			</ul>
		</xsl:if>
	</xsl:template>

</xsl:stylesheet>