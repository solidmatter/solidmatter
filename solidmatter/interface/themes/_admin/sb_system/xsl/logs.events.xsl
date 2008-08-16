<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet 
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform" 
	version="1.0" 
	exclude-result-prefixes="html" 
	xmlns:html="http://www.w3.org/1999/xhtml">

	<xsl:import href="global.views.xsl" />
	<xsl:import href="global.default.xsl" />
	<xsl:import href="global.sbform.xsl" />
	
	<xsl:output 
		method="html"
		encoding="UTF-8"
		standalone="yes"
		indent="no"
	/>



	<xsl:template match="/">
	<html>
	<head>
		<xsl:apply-templates select="response/metadata" />
	</head>
	<body>
		<xsl:call-template name="views" />
		<div class="workbench">
			<xsl:apply-templates select="response/errors" />
			<xsl:apply-templates select="response/content" />
		</div>
	</body>
	</html>
	</xsl:template>
		
	<xsl:template match="response/content">
		
		<xsl:apply-templates select="sbform[@id='filter_events']" />
		
		<table class="default" width="100%">
			<thead>
				<tr>
					<th><xsl:value-of select="$locale/system/general/labels/event" /></th>
					<!--<th><xsl:value-of select="$locale/system/general/labels/module" /></th>-->
					<th width="50"><xsl:value-of select="$locale/system/general/labels/CHANGEME" /></th>
					<th><xsl:value-of select="$locale/system/general/labels/message" /></th>
					<th width="130"><xsl:value-of select="$locale/system/general/labels/created_at" /></th>
					<th><xsl:value-of select="$locale/system/general/labels/options" /></th>
				</tr>
			</thead>
			<tbody>
			<xsl:choose>
				<xsl:when test="$content/events/row">
					<xsl:for-each select="$content/events/row">
						<tr>
							<xsl:choose>
								<xsl:when test="position() mod 2 = 1">
									<xsl:attribute name="class">odd</xsl:attribute>
								</xsl:when>
								<xsl:otherwise>
									<xsl:attribute name="class">even</xsl:attribute>
								</xsl:otherwise>
							</xsl:choose>
							<td>
								<span>
									<xsl:attribute name="class">
										<xsl:choose>
											<xsl:when test="@e_type='INFO'">
												type sb_event_info
											</xsl:when>
											<xsl:when test="@e_type='DEBUG'">
												type sb_event_debug
											</xsl:when>
											<xsl:when test="@e_type='ERROR'">
												type sb_event_error
											</xsl:when>
											<xsl:when test="@e_type='MAINTENANCE'">
												type sb_event_maintenance
											</xsl:when>
											<xsl:when test="@e_type='SECURITY'">
												type sb_event_security
											</xsl:when>
											<xsl:when test="@e_type='WARNING'">
												type sb_event_warning
											</xsl:when>
										</xsl:choose>
									</xsl:attribute>
									<xsl:value-of select="@fk_module" />:<xsl:value-of select="@s_loguid" />
								</span>
							</td>
							<!--<td>
								<a href="/{@uuid}"><span class="type "></span></a>
							</td>-->
							<td>
								<a href="/{@fk_subject}" class="type {@s_subjectcsstype}"></a>
								<xsl:if test="@fk_user!=''">
									<a href="/{@fk_user}" class="type sb_user"></a>
								</xsl:if>
							</td>
							<td>
								<xsl:choose>
								<xsl:when test="string-length(@t_log)>50">
									<xsl:value-of select="substring(@t_log, 0, 50)" /><a onmouseover="document.getElementById('text_{@id}').style.display='inline';this.style.display='none';">...</a>
									<span id="text_{@id}" style="display:none;"><xsl:value-of select="substring(@t_log, 50)" /></span>
								</xsl:when>
								<xsl:otherwise>
									<xsl:value-of select="@t_log" />
								</xsl:otherwise>
								</xsl:choose>
							</td>
							<td>
								<xsl:value-of select="@dt_created" />
							</td>
							<td>
								
							</td>
						</tr>
					</xsl:for-each>
				</xsl:when>
				<xsl:otherwise>
					<tr><td colspan="6"><xsl:value-of select="$locale/system/general/texts/no_subobjects" /></td></tr>
				</xsl:otherwise>
			</xsl:choose>
			
			</tbody>
			<tfoot></tfoot>
		</table>	
		
	</xsl:template>

</xsl:stylesheet>