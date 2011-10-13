<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet 
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform" 
	version="1.0" 
	exclude-result-prefixes="html sbform" 
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
		<html>
		<head>
			<!--<xsl:apply-templates select="/response/metadata" />-->
			<link rel="stylesheet" href="/theme/sb_jukebox/css/styles.css" type="text/css" media="all" />
		</head>
		<body>
			<div class="login">
				<div class="logincontainer">
					<div class="logingrammo">
					</div>
					<xsl:apply-templates select="/response/content/sbform[@id='login_backend']" />
				</div>
			</div>
			<div class="footer" style="text-align:center;">
				solidbytes Jukebox 0.5beta | Share the music you like with the people you like | powered by solidMatter<br />
				use of a decent browser recommended | grammophone image used under CC-SA license
			</div>
			<xsl:apply-templates select="response/errors" />
		</body>
		</html>
	</xsl:template>
	
	<xsl:template match="sbform">
		<form class="login" action="{@action}" method="post" accept-charset="UTF-8">
			<xsl:apply-templates select="*" />
		</form>
	</xsl:template>
	
	<xsl:template match="sbinput[@type='string']">
		<label for="{@name}"><xsl:value-of select="dyn:evaluate(@label)" /></label> 
		<input type="text" size="{@size}" maxlength="{@maxlength}" value="{@value}" name="{@name}" id="{@name}">
			<xsl:if test="@errorlabel"><xsl:attribute name="class">formerror</xsl:attribute></xsl:if>
		</input><br />
		<xsl:if test="@errorlabel">
			<span class="formerror">
				<xsl:value-of select="concat(' ', dyn:evaluate(@errorlabel))" />
			</span><br />
		</xsl:if>
	</xsl:template>
	
	<xsl:template match="sbinput[@type='password']">
		<label for="{@name}"><xsl:value-of select="dyn:evaluate(@label)" /></label>
		<input type="password" size="{@size}" maxlength="{@maxlength}" value="{@value}" name="{@name}" id="{@name}">
			<xsl:if test="@errorlabel"><xsl:attribute name="class">formerror</xsl:attribute></xsl:if>
		</input><br />
		<xsl:if test="@errorlabel"><span class="formerror"><xsl:value-of select="concat(' ', dyn:evaluate(@errorlabel))" /></span><br /></xsl:if>
		<xsl:if test="@name='password' and ../@errorlabel">
			<span class="formerror">
				<xsl:value-of select="dyn:evaluate(../@errorlabel)" />
			</span><br />
		</xsl:if>
	</xsl:template>
	
	<xsl:template match="sbinput[@type='captcha']">
		<label for="{@name}"><xsl:value-of select="dyn:evaluate(@label)" /></label><br />
		<img src="/-/login/getCaptcha/uid=login_backend" /><br />
		<input type="text" size="{@size}" maxlength="{@length}" name="{@name}" id="{@name}"><br />
			<xsl:if test="@errorlabel"><xsl:attribute name="class">formerror</xsl:attribute><br /></xsl:if>
		</input>
	</xsl:template>
	
	<xsl:template match="submit">
		<input type="submit" name="{@value}" value="{dyn:evaluate(@label)}" style="border: 1px solid black; background-color: #DDE;"/>
		<!--<xsl:if test="@errorlabel">
			<br/><span class="formerror"><xsl:value-of select="concat(' ', dyn:evaluate(@errorlabel))" /></span>
		</xsl:if>-->
	</xsl:template>

</xsl:stylesheet>