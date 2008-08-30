<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet 
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform" 
	version="1.0" 
	exclude-result-prefixes="html" 
	xmlns:html="http://www.w3.org/1999/xhtml"
	xmlns:sbform="http://www.solidbytes.net/sbform"
	xmlns:php="http://php.net/xsl">

	<xsl:import href="global.default.xsl" />
	<xsl:import href="global.views.xsl" />
	
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
		<xsl:call-template name="views" select="response/content/views" />
		<div class="workbench">
			<xsl:apply-templates select="response/errors" />
			<table class="invisible">
				<tr>
					<td width="50%">
						<xsl:apply-templates select="response/content/userinfo/row" />
					</td>
					<td><div class="spacer"></div></td>
					<td width="50%">
						<xsl:call-template name="inbox" />
						<xsl:call-template name="tasks" />
					</td>
				</tr>
				
			</table>
			
		</div>
	</body>
	</html>
	</xsl:template>
	
	<xsl:template match="response/content/userinfo/row">
		<div class="eyecandy"><div class="left"><div class="right">
			<h1>Welcome <xsl:value-of select="s_nickname"/></h1>
			your last login was on <xsl:value-of select="php:functionString('datetime_mysql2local', string(dt_lastlogin), string($locale/system/formats/datetime_long))"/>
			, you have <xsl:value-of select="n_totalfailedlogins"/> failed logins and 
			<xsl:value-of select="n_successfullogins"/> successful logins
			<br/><br/><a href="/-/login/logout" target="_top">Logout</a>
		</div></div></div>
	</xsl:template>
	
	<xsl:template name="inbox">
		<div class="eyecandy"><div class="left"><div class="right">
			<h1>Inbox</h1>
			Feature still needs to be implemented!
		</div></div></div>
	</xsl:template>
	
	<xsl:template name="tasks">
		<div class="eyecandy"><div class="left"><div class="right">
			<h1>Tasks</h1>
			Feature still needs to be implemented!
		</div></div></div>
	</xsl:template>
	
</xsl:stylesheet>