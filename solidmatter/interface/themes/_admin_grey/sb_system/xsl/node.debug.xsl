<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet 
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform" 
	version="1.0" 
	exclude-result-prefixes="html" 
	xmlns:html="http://www.w3.org/1999/xhtml">

	<xsl:import href="global.views.xsl" />
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
		<xsl:apply-templates select="/response/metadata" />
	</head>
	<body>
		<xsl:call-template name="views" />
		<div class="workbench">
			<xsl:apply-templates select="response/errors" />
			<xsl:apply-templates select="response/content/sbnode" />
		</div>
	</body>
	</html>
	</xsl:template>
	
	<xsl:template match="sbnode">
		<table class="invisible">
			<tr>
				<td width="50%">
					<div class="eyecandy"><div class="left"><div class="right">
						<h1>Properties:</h1>
						<ul>
						<xsl:for-each select="@*">
							<li>
								<xsl:value-of select="name()" /> (<xsl:value-of select="." />)<br/>
							</li>
						</xsl:for-each>
						</ul>
					</div></div></div>
					<div class="eyecandy"><div class="left"><div class="right">
						<h1>Hierarchy</h1>
						<ul>
							<li>
								<strong>Children</strong>
								<ul>
								<xsl:for-each select="children/*">
									<li style="margin: 2px 0;">
										<span class="type {@csstype}"><xsl:value-of select="@label" /> (<xsl:value-of select="@nodetype" />)</span><br/>
									</li>
								</xsl:for-each>
								</ul>
							</li>
							<li>
								<strong>Ancestors</strong>
								<ul>
								<xsl:for-each select="ancestors/*">
									<li style="margin: 2px 0;">
										<span class="type {@csstype}"><xsl:value-of select="@label" /> (<xsl:value-of select="@nodetype" />)</span><br/>
									</li>
								</xsl:for-each>
								</ul>
							</li>
							<li>
								<strong>Parents</strong>
								<ul>
								<xsl:for-each select="parents/*">
									<li style="margin: 2px 0;">
										<span class="type {@csstype}"><xsl:value-of select="@label" /> (<xsl:value-of select="@nodetype" />)</span><br/>
									</li>
								</xsl:for-each>
								</ul>
							</li>
						</ul>
					</div></div></div>
					<div class="eyecandy"><div class="left"><div class="right">
						<h1>Tags</h1>
						<ul>
						<xsl:for-each select="tags/tag">
							<li>
								<xsl:value-of select="." /> (<xsl:value-of select="@id" />)<br/>
							</li>
						</xsl:for-each>
						</ul>
					</div></div></div>
				</td>
				<td><div class="spacer"></div></td>
				<td width="50%">
					<div class="eyecandy"><div class="left"><div class="right">
						<h1>Supertypes</h1>
						<ul>
						<xsl:for-each select="supertypes/nodetype">
							<li>
								<xsl:value-of select="@name" />
							</li>
						</xsl:for-each>
						</ul>
					</div></div></div>
					<div class="eyecandy"><div class="left"><div class="right">
						<h1>Views</h1>
						<ul>
						<xsl:for-each select="views/view">
							<li>
								<xsl:value-of select="@name" /> (<!--<xsl:value-of select="@class" /> | <xsl:value-of select="@file" /> | --><xsl:value-of select="@module" />)<br/>
							</li>
						</xsl:for-each>
						</ul>
					</div></div></div>
					<div class="eyecandy"><div class="left"><div class="right">
						<h1>Supported Authorisations</h1>
						<ul>
						<xsl:for-each select="supported_authorisations/authorisation">
							<li>
								<xsl:value-of select="@name" /> (<xsl:value-of select="@parent" />)<br/>
							</li>
						</xsl:for-each>
						</ul>
					</div></div></div>
					<div class="eyecandy"><div class="left"><div class="right">
						<h1>User Authorisations</h1>
						<ul>
						<xsl:for-each select="user_authorisations/authorisation">
							<li>
								<xsl:value-of select="@name" /> (<xsl:value-of select="@grant_type" />)<br/>
							</li>
						</xsl:for-each>
						</ul>
					</div></div></div>
					<div class="eyecandy"><div class="left"><div class="right">
						<h1>Local Authorisations</h1>
						<ul>
						<xsl:for-each select="local_authorisations/*">
							<li>
								<xsl:value-of select="@name" /> (<xsl:value-of select="@grant_type" /> | 
								<xsl:choose>
								<xsl:when test="name()='user'">
									<span class="type sb_user"><xsl:value-of select="@uuid" /></span>
								</xsl:when>
								<xsl:otherwise>
									<span class="type sb_usergroup"><xsl:value-of select="@uuid" /></span>
								</xsl:otherwise>
								</xsl:choose> )
							</li>
						</xsl:for-each>
						</ul>
					</div></div></div>
					<div class="eyecandy"><div class="left"><div class="right">
						<h1>Inherited Authorisations</h1>
						<ul>
						<xsl:for-each select="inherited_authorisations/*">
							<li>
								<xsl:value-of select="@name" /> (<xsl:value-of select="@grant_type" /> | 
								<xsl:choose>
								<xsl:when test="name()='user'">
									<span class="type sb_user"><xsl:value-of select="@uuid" /></span>
								</xsl:when>
								<xsl:otherwise>
									<span class="type sb_usergroup"><xsl:value-of select="@uuid" /></span>
								</xsl:otherwise>
								</xsl:choose> )
							</li>
						</xsl:for-each>
						</ul>
					</div></div></div>
				</td>
			</tr>
		</table>
		
	</xsl:template>

</xsl:stylesheet>