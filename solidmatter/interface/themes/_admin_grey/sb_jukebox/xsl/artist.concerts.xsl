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
		<xsl:apply-templates select="response/metadata" />
	</head>
	<body>
		<xsl:call-template name="views" />
		<div class="workbench">
			<xsl:apply-templates select="$errors" />
			<xsl:apply-templates select="$content/sbnode[@master]" />
		</div>
	</body>
	</html>
	</xsl:template>
	
	<xsl:template match="sbnode">
		
		<table class="default" width="100%">
			<thead>
				<tr>
					<th>@SongkickArtist</th>
					<th>@hasIdentifier</th>
					<th>@onTourUntil</th>
					<th>@active?</th>
					<th>@change!</th>
				</tr>
			</thead>
			<tbody>
			<xsl:choose>
				<xsl:when test="$content/sk_artists/resultsPage/results/artist">
					<xsl:for-each select="$content/sk_artists/resultsPage/results/artist">
						<tr>
							<xsl:call-template name="colorize" />
							<td>
								<xsl:value-of select="@displayName" />
							</td>
							<td>
								<xsl:if test="identifier">X</xsl:if>
							</td>
							<td>
								<xsl:value-of select="@onTourUntil" />
							</td>
							<td>
								<xsl:if test="@id=$content/sbnode[@master]/@songkick_id">X</xsl:if>
							</td>
							<td>
								<a href="/{$master/@uuid}/concerts/linkToSongkick?songkick_id={@id}">CHANGE</a>
							</td>
						</tr>
					</xsl:for-each>
				</xsl:when>
				<xsl:otherwise>
					<tr><td colspan="5"><xsl:value-of select="$locale/sbSystem/texts/no_subobjects" /></td></tr>
				</xsl:otherwise>
			</xsl:choose>
			
			</tbody>
			<tfoot></tfoot>
		</table>
		
		<xsl:if test="$content/sk_concerts">
			<table class="default" width="100%">
				<thead>
					<tr>
						<th><xsl:value-of select="$locale/sbSystem/labels/name" /></th>
					</tr>
				</thead>
				<tbody>
				<xsl:choose>
					<xsl:when test="$content/sk_concerts/resultsPage/results/event">
						<xsl:for-each select="$content/sk_concerts/resultsPage/results/event">
							<xsl:sort select="start/@date" />
							<xsl:sort select="@displayName" />
							<tr>
								<xsl:call-template name="colorize" />
								<td>
									<xsl:value-of select="start/@date" /> (<xsl:value-of select="start/@time" />)
									<xsl:if test="end">
										<xsl:value-of select="end/@date" /> <xsl:value-of select="end/@time" />
									</xsl:if>
								</td>
								<td>
									<xsl:value-of select="venue/metroArea/@displayName" />, <xsl:value-of select="venue/metroArea/country/@displayName" />
								</td>
								<td>
									<!-- <xsl:value-of select="@displayName" /> -->
									<xsl:value-of select="venue/@displayName" />
								</td>
								<td>
									<xsl:value-of select="@type" />
								</td>
								<td>
									<xsl:value-of select="@onTourUntil" />
								</td>
							</tr>
						</xsl:for-each>
					</xsl:when>
					<xsl:otherwise>
						<tr><td colspan="5"><xsl:value-of select="$locale/sbSystem/texts/no_subobjects" /></td></tr>
					</xsl:otherwise>
				</xsl:choose>
				
				</tbody>
				<tfoot></tfoot>
			</table>
		</xsl:if>
		
	</xsl:template>

</xsl:stylesheet>