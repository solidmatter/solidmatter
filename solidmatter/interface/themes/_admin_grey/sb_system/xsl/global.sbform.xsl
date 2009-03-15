<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet 
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform" 
	version="1.0" 
	exclude-result-prefixes="html sbform" 
	xmlns:html="http://www.w3.org/1999/xhtml"
	xmlns:sbform="http://www.solidbytes.net/sbform"
	xmlns:dyn="http://exslt.org/dynamic" extension-element-prefixes="dyn">

	<xsl:output 
		method="html"
		encoding="UTF-8"
		standalone="yes"
		indent="yes"
	/>
	
	<xsl:template name="sbform" match="//sbform">
		<xsl:param name="noLabel" select="false" />
		<form class="default" action="{@action}" method="post" enctype="multipart/form-data" accept-charset="utf-8">
			<table class="default">
				<xsl:if test="$noLabel != 'true'"></xsl:if>
					<tr><th colspan="2"><xsl:value-of select="dyn:evaluate(@label)" /></th></tr>
				
				<xsl:if test="@errorlabel"><br/><xsl:value-of select="dyn:evaluate(@errorlabel)" /></xsl:if>
				<xsl:apply-templates select="*" mode="complete" />
			</table>
		</form>
	</xsl:template>
	
	<!--<xsl:template match="sbform:group">
		<tr>
			<th class="th2" colspan="2"><legend><xsl:value-of select="@label" /></legend></th>
		</tr>
		<xsl:apply-templates select="sbform:*" />
	</xsl:template>-->
	
	
	
	<!-- string -->
	<xsl:template match="sbinput[@type='string']" mode="complete">
		<tr>
			<td width="30%"><label for="{@name}"><xsl:value-of select="dyn:evaluate(@label)" /></label></td>
			<td width="70%">
				<xsl:apply-templates select="." mode="inputonly" />
			</td>
		</tr>
	</xsl:template>
	<xsl:template match="sbinput[@type='string']" mode="inputonly">
		<input type="text" size="{@size}" maxlength="{@maxlength}" value="{@value}" name="{@name}" id="{@name}">
			<xsl:if test="@disabled"><xsl:attribute name="disabled">disabled</xsl:attribute></xsl:if>
			<xsl:if test="@errorlabel"><xsl:attribute name="class">formerror</xsl:attribute></xsl:if>
		</input>
		<xsl:if test="@errorlabel"><span class="formerror"><xsl:value-of select="concat(' ', dyn:evaluate(@errorlabel))" /></span></xsl:if>
	</xsl:template>
	
	<!-- email -->
	<xsl:template match="sbinput[@type='email']" mode="complete">
		<tr>
			<td width="30%"><label for="{@name}"><xsl:value-of select="dyn:evaluate(@label)" /></label></td>
			<td width="70%">
				<xsl:apply-templates select="." mode="inputonly" />
			</td>
		</tr>
	</xsl:template>
	<xsl:template match="sbinput[@type='email']" mode="inputonly">
		<input type="text" size="{@size}" maxlength="{@maxlength}" value="{@value}" name="{@name}" id="{@name}">
			<xsl:if test="@disabled"><xsl:attribute name="disabled">disabled</xsl:attribute></xsl:if>
			<xsl:if test="@errorlabel"><xsl:attribute name="class">formerror</xsl:attribute></xsl:if>
		</input>
		<xsl:if test="@errorlabel"><span class="formerror"><xsl:value-of select="concat(' ', dyn:evaluate(@errorlabel))" /></span></xsl:if>
	</xsl:template>
	
	<!-- datetime -->
	<xsl:template match="sbinput[@type='datetime']" mode="complete">
		<tr>
			<td width="30%"><label for="{@name}"><xsl:value-of select="dyn:evaluate(@label)" /></label></td>
			<td width="70%">
				<xsl:apply-templates select="." mode="inputonly" />
			</td>
		</tr>
	</xsl:template>
	<xsl:template match="sbinput[@type='datetime']" mode="inputonly">
		<input type="text" size="{@size}" maxlength="{@maxlength}" value="{@value}" name="{@name}" id="{@name}">
			<xsl:if test="@disabled"><xsl:attribute name="disabled">disabled</xsl:attribute></xsl:if>
			<xsl:if test="@errorlabel"><xsl:attribute name="class">formerror</xsl:attribute></xsl:if>
		</input>
		<xsl:if test="@errorlabel"><span class="formerror"><xsl:value-of select="concat(' ', dyn:evaluate(@errorlabel))" /></span></xsl:if>
	</xsl:template>
	
	<!-- password -->
	<xsl:template match="sbinput[@type='password']" mode="complete">
		<tr>
			<td width="30%"><label for="{@name}"><xsl:value-of select="dyn:evaluate(@label)" /></label></td>
			<td width="70%">
				<xsl:apply-templates select="." mode="inputonly" />
			</td>
		</tr>
	</xsl:template>
	<xsl:template match="sbinput[@type='password']" mode="inputonly">
		<input type="password" size="{@size}" maxlength="{@maxlength}" value="{@value}" name="{@name}" id="{@name}">
			<xsl:if test="@disabled"><xsl:attribute name="disabled">disabled</xsl:attribute></xsl:if>
			<xsl:if test="@errorlabel"><xsl:attribute name="class">formerror</xsl:attribute></xsl:if>
		</input>
		<xsl:if test="@errorlabel"><span class="formerror"><xsl:value-of select="concat(' ', dyn:evaluate(@errorlabel))" /></span></xsl:if>
	</xsl:template>
	
	
	
	<!-- text -->
	<xsl:template match="sbinput[@type='text']" mode="complete">
		<tr>
			<td width="30%"><label for="{@name}"><xsl:value-of select="dyn:evaluate(@label)" /></label></td>
			<td width="70%">
				<xsl:apply-templates select="." mode="inputonly" />
			</td>
		</tr>
	</xsl:template>
	<xsl:template match="sbinput[@type='text']" mode="inputonly">
		<textarea cols="{@columns}" rows="{@rows}" name="{@name}" id="{@name}">
			<xsl:if test="@disabled"><xsl:attribute name="disabled">disabled</xsl:attribute></xsl:if>
			<xsl:if test="@errorlabel"><xsl:attribute name="class">formerror</xsl:attribute></xsl:if>
			<xsl:value-of select="@value" />
		</textarea>
		<xsl:if test="@errorlabel"><span class="formerror"><xsl:value-of select="concat(' ', dyn:evaluate(@errorlabel))" /></span></xsl:if>
	</xsl:template>
	
	
	
	<!-- urlsafe -->
	<xsl:template match="sbinput[@type='urlsafe']" mode="complete">
		<tr>
			<td width="30%"><label for="{@name}"><xsl:value-of select="dyn:evaluate(@label)" /></label></td>
			<td width="70%">
				<xsl:apply-templates select="." mode="inputonly" />
			</td>
		</tr>
	</xsl:template>
	<xsl:template match="sbinput[@type='urlsafe']" mode="inputonly">
		<input type="text" size="{@size}" maxlength="{@maxlength}" value="{@value}" name="{@name}" id="{@name}">
			<xsl:if test="@disabled"><xsl:attribute name="disabled">disabled</xsl:attribute></xsl:if>
			<xsl:if test="@errorlabel"><xsl:attribute name="class">formerror</xsl:attribute></xsl:if>
		</input>
		<xsl:if test="@errorlabel"><span class="formerror"><xsl:value-of select="concat(' ', dyn:evaluate(@errorlabel))" /></span></xsl:if>
	</xsl:template>
	
	
	
	<!-- hexcolor -->
	<xsl:template match="sbinput[@type='color']" mode="complete">
		<tr>
			<td width="30%"><label for="{@name}"><xsl:value-of select="dyn:evaluate(@label)" /></label></td>
			<td width="70%">
				<xsl:apply-templates select="." mode="inputonly" />
			</td>
		</tr>
	</xsl:template>
	<xsl:template match="sbinput[@type='color']" mode="inputonly">
		<input type="text" size="6" maxlength="6" value="{@value}" name="{@name}" id="{@name}">
			<xsl:if test="@disabled"><xsl:attribute name="disabled">disabled</xsl:attribute></xsl:if>
			<xsl:if test="@errorlabel"><xsl:attribute name="class">formerror</xsl:attribute></xsl:if>
		</input>
		<xsl:if test="@errorlabel"><span class="formerror"><xsl:value-of select="concat(' ', dyn:evaluate(@errorlabel))" /></span></xsl:if>
	</xsl:template>
	
	
	
	<!-- integer -->
	<xsl:template match="sbinput[@type='integer']" mode="complete">
		<tr>
			<td width="30%"><label for="{@name}"><xsl:value-of select="dyn:evaluate(@label)" /></label></td>
			<td width="70%">
				<xsl:apply-templates select="." mode="inputonly" />
			</td>
		</tr>
	</xsl:template>
	<xsl:template match="sbinput[@type='integer']" mode="inputonly">
		<input type="text" size="{@size}" maxlength="{@maxlength}" value="{@value}" name="{@name}" id="{@name}">
			<xsl:if test="@disabled"><xsl:attribute name="disabled">disabled</xsl:attribute></xsl:if>
			<xsl:if test="@errorlabel"><xsl:attribute name="class">formerror</xsl:attribute></xsl:if>
		</input>
		<xsl:if test="@errorlabel"><span class="formerror"><xsl:value-of select="concat(' ', dyn:evaluate(@errorlabel))" /></span></xsl:if>
	</xsl:template>
	
	
	
	<!-- select -->
	<xsl:template match="sbinput[@type='select']" mode="complete">
		<tr>
			<td width="30%"><label for="{@name}"><xsl:value-of select="dyn:evaluate(@label)" /></label></td>
			<td width="70%">
				<xsl:apply-templates select="." mode="inputonly" />
			</td>
		</tr>
	</xsl:template>
	<xsl:template match="sbinput[@type='select']" mode="inputonly">
		<xsl:variable name="value" select="@value" />
		<select size="{@size}" name="{@name}" id="{@name}">
			<xsl:if test="@disabled"><xsl:attribute name="disabled">disabled</xsl:attribute></xsl:if>
			<xsl:if test="@errorlabel"><xsl:attribute name="class">formerror</xsl:attribute></xsl:if>
			<xsl:for-each select="option">
				<option value="{@value}">
					<xsl:if test="@value = $value">
						<xsl:attribute name="selected">selected</xsl:attribute>
					</xsl:if>
					<xsl:choose>
					<xsl:when test="@label">
						<xsl:value-of select="dyn:evaluate(@label)"/>
					</xsl:when>
					<xsl:otherwise>
						<xsl:value-of select="@text" />
					</xsl:otherwise>
					</xsl:choose>
				</option>
			</xsl:for-each>
		</select>
		<xsl:if test="@errorlabel"><span class="formerror"><xsl:value-of select="concat(' ', dyn:evaluate(@errorlabel))" /></span></xsl:if>
	</xsl:template>
	
	
	
	<!-- users -->
	<xsl:template match="sbinput[@type='users']" mode="complete">
		<tr>
			<td width="30%"><label for="{@name}"><xsl:value-of select="dyn:evaluate(@label)" /></label></td>
			<td width="70%">
				<xsl:apply-templates select="." mode="inputonly" />
			</td>
		</tr>
	</xsl:template>
	<xsl:template match="sbinput[@type='users']" mode="inputonly">
		<xsl:variable name="value" select="@value" />
		<xsl:variable name="includeself" select="@includeself" />
		<select size="{@size}" name="{@name}" id="{@name}">
			<xsl:if test="@disabled"><xsl:attribute name="disabled">disabled</xsl:attribute></xsl:if>
			<xsl:if test="@errorlabel"><xsl:attribute name="class">formerror</xsl:attribute></xsl:if>
			<xsl:for-each select="option">
				<xsl:if test="$includeself='TRUE' or @value != /response/metadata/system/userid">
				<option value="{@value}">
					<xsl:if test="@value = $value">
						<xsl:attribute name="selected">selected</xsl:attribute>
					</xsl:if>
					<xsl:choose>
					<xsl:when test="@label">
						<xsl:value-of select="dyn:evaluate(@label)"/>
					</xsl:when>
					<xsl:otherwise>
						<xsl:value-of select="@text" />
					</xsl:otherwise>
					</xsl:choose>
				</option>
				</xsl:if>
			</xsl:for-each>
		</select>
		<xsl:if test="@errorlabel"><span class="formerror"><xsl:value-of select="concat(' ', dyn:evaluate(@errorlabel))" /></span></xsl:if>
	</xsl:template>
	
	
	
	<!-- nodeselector -->
	<xsl:template match="sbinput[@type='nodeselector']" mode="complete">
		<tr>
			<td width="30%"><label for="{@name}"><xsl:value-of select="dyn:evaluate(@label)" /></label></td>
			<td width="70%">
				<xsl:apply-templates select="." mode="inputonly" />
			</td>
		</tr>
	</xsl:template>
	<xsl:template match="sbinput[@type='nodeselector']" mode="inputonly">
		<xsl:variable name="value" select="@value" />
		<select size="{@size}" name="{@name}" id="{@name}">
			<xsl:if test="@disabled"><xsl:attribute name="disabled">disabled</xsl:attribute></xsl:if>
			<xsl:if test="@errorlabel"><xsl:attribute name="class">formerror</xsl:attribute></xsl:if>
			<option value=""></option>
			<xsl:call-template name="nodeselector_slave">
				<xsl:with-param name="prefix" select="''" />
				<xsl:with-param name="value" select="$value" />
			</xsl:call-template>
		</select>
		<xsl:if test="@errorlabel"><span class="formerror"><xsl:value-of select="concat(' ', dyn:evaluate(@errorlabel))" /></span></xsl:if>
	</xsl:template>
	<xsl:template name="nodeselector_slave">
		<xsl:param name="prefix" />
		<xsl:param name="value" />
		<xsl:for-each select="sbnode">
			<option value="{@uuid}" class="type {@displaytype}">
				<xsl:if test="@uuid = $value">
					<xsl:attribute name="selected">selected</xsl:attribute>
				</xsl:if>
				<xsl:value-of select="concat($prefix, @label)" />
			</option>
			<xsl:if test="sbnode">
				<xsl:call-template name="nodeselector_slave">
					<xsl:with-param name="prefix" select="concat($prefix, '&#160;&#160;&#160;')" />
					<xsl:with-param name="value" select="$value" />
				</xsl:call-template>
			</xsl:if>
		</xsl:for-each>
	</xsl:template>
	
	
	
	<!-- autocomplete -->
	<xsl:template match="sbinput[@type='autocomplete']" mode="complete">
		<tr>
			<td width="30%"><label for="{@name}"><xsl:value-of select="dyn:evaluate(@label)" /></label></td>
			<td width="70%">
				<xsl:apply-templates select="." mode="inputonly" />
			</td>
		</tr>
	</xsl:template>
	<xsl:template match="sbinput[@type='autocomplete']" mode="inputonly">
		<input type="text" size="{@size}" maxlength="{@maxlength}" value="{@value}" name="{@name}" id="{@name}">
			<xsl:if test="@disabled"><xsl:attribute name="disabled">disabled</xsl:attribute></xsl:if>
			<xsl:if test="@errorlabel"><xsl:attribute name="class">formerror</xsl:attribute></xsl:if>
		</input>
		<div id="suggest_{@name}" class="ac_suggestions" style="display:none;"></div>
		<xsl:if test="@errorlabel"><span class="formerror"><xsl:value-of select="concat(' ', dyn:evaluate(@errorlabel))" /></span></xsl:if>
		<script language="Javascript" type="text/javascript">
			Event.observe(
				window,
				'load',
				function() {
					new Ajax.Autocompleter(
						'<xsl:value-of select="@name"/>',
						'suggest_<xsl:value-of select="@name"/>',
						'<xsl:value-of select="@url"/>',
						{ minChars: 3 }
					)
				}
			);
		</script>
	</xsl:template>
	
	
	
	<!-- relation -->
	<xsl:template match="sbinput[@type='relation']" mode="complete">
		<tr>
			<td width="30%"><label for="{@name}"><xsl:value-of select="dyn:evaluate(@label)" /></label></td>
			<td width="70%">
				<xsl:apply-templates select="." mode="inputonly" />
			</td>
		</tr>
	</xsl:template>
	<xsl:template match="sbinput[@type='relation']" mode="inputonly">
		<xsl:variable name="value" select="@value" />
		<select size="1" name="type_{@name}" id="type_{@name}">
			<xsl:for-each select="option">
				<option value="{@value}">
					<xsl:if test="@value = $value">
						<xsl:attribute name="selected">selected</xsl:attribute>
					</xsl:if>
					<xsl:choose>
					<xsl:when test="@label">
						<xsl:value-of select="dyn:evaluate(@label)"/>
					</xsl:when>
					<xsl:otherwise>
						<xsl:value-of select="@text" />
					</xsl:otherwise>
					</xsl:choose>
				</option>
			</xsl:for-each>
		</select>
		<input type="text" size="50" maxlength="250" value="{@value}" name="target_{@name}" id="target_{@name}">
			<xsl:if test="@disabled"><xsl:attribute name="disabled">disabled</xsl:attribute></xsl:if>
			<xsl:if test="@errorlabel"><xsl:attribute name="class">formerror</xsl:attribute></xsl:if>
		</input>
		<div id="suggest_{@name}" class="ac_suggestions" style="display:none;"></div>
		<xsl:if test="@errorlabel"><span class="formerror"><xsl:value-of select="concat(' ', dyn:evaluate(@errorlabel))" /></span></xsl:if>
		<script language="Javascript" type="text/javascript">
			Event.observe(
				window,
				'load',
				function() {
					var oType = $('type_<xsl:value-of select="@name"/>');
					new Ajax.Autocompleter(
						'target_<xsl:value-of select="@name"/>',
						'suggest_<xsl:value-of select="@name"/>',
						'<xsl:value-of select="@url"/>',
						{ 
							minChars: <xsl:value-of select="@minchars"/>, 
							//parameters: 'type_<xsl:value-of select="@name"/>='+oType.value 
							callback: function(oTextInput) {
								return ('target_<xsl:value-of select="@name"/>=' + oTextInput.value + '&amp;' + ' type_<xsl:value-of select="@name"/>=' + oType.value);
							}
						}
					)
				}
			);
		</script>
	</xsl:template>
	
	
	
	<!-- codeeditor -->
	<xsl:template match="sbinput[@type='codeeditor']" mode="complete">
		<tr>
			<td width="30%"><label for="{@name}"><xsl:value-of select="dyn:evaluate(@label)" /></label></td>
			<td width="70%">
				<xsl:apply-templates select="." mode="inputonly" />
			</td>
		</tr>
	</xsl:template>
	<xsl:template match="sbinput[@type='codeeditor']" mode="inputonly">
		<textarea style="height: 350px; width: 100%;" maxlength="{@maxlength}" name="{@name}" id="{@name}">
			<xsl:if test="@errorlabel"><xsl:attribute name="class">formerror</xsl:attribute></xsl:if>
			<xsl:value-of select="@value" />
		</textarea>
		<script language="Javascript" type="text/javascript">
			editAreaLoader.init({
				id: "<xsl:value-of select="@name" />"	// id of the textarea to transform		
				,start_highlight: true	// if start with highlight
				,allow_resize: "no"
				,allow_toggle: false
				,language: "en"
				,syntax: "<xsl:value-of select="@syntax" />"
			});
		</script>
		<xsl:if test="@errorlabel"><span class="formerror"><xsl:value-of select="concat(' ', dyn:evaluate(@errorlabel))" /></span></xsl:if>
	</xsl:template>
	
	
	
	<!-- checkbox -->
	<xsl:template match="sbinput[@type='checkbox']" mode="complete">
		<tr>
			<td><label for="{@name}"><xsl:value-of select="dyn:evaluate(@label)" /></label></td>
			<td>
				<xsl:apply-templates select="." mode="inputonly" />
			</td>
		</tr>
	</xsl:template>
	<xsl:template match="sbinput[@type='checkbox']" mode="inputonly">
		<input type="checkbox" name="{@name}" id="{@name}">
			<xsl:if test="@value='TRUE'"><xsl:attribute name="checked">checked</xsl:attribute></xsl:if>
		</input>
	</xsl:template>
	
	
	
	<!-- multifileupload -->
	<xsl:template match="sbinput[@type='multifileupload']" mode="complete">
		<tr>
			<td><label for="{@name}"><xsl:value-of select="dyn:evaluate(@label)" /></label></td>
			<td>
				<xsl:apply-templates select="." mode="inputonly" />
			</td>
		</tr>
	</xsl:template>
	<xsl:template match="sbinput[@type='multifileupload']" mode="inputonly">
		<div id="fileInputFrame"><input type="file" /></div>
		<div id="attachments" style="padding: 5px;">
			<!--<p>attached files will be shown here</p>-->
			<ul id="attachFileList" class="itemlist">
			</ul>
		</div>
		<script type="text/javascript" language="javascript">
			
			var sCheck = '';
			
			//------------------------------------------------------------------
			// 
			// note: thx to wooster for helping out with some stuff
			//
			function addFile() {
				
				// check if file changed
				var elemSlot = document.getElementById("fileInputFrame").firstChild;
				if (elemSlot.value == '' || elemSlot.value == sCheck) {
					return (false);
				}
				
				var elemList = document.getElementById("attachFileList");
				
				var elemEntry = document.createElement("li");
				
				elemSlot.name = "<xsl:value-of select="@name" />[]";
				elemSlot.style.display = "none";
				elemEntry.appendChild(elemSlot);
				
				var elemRemove = document.createElement('button');
				elemRemove.appendChild(document.createTextNode('X'));
				elemRemove.onclick = function() {
					elemEntry.removeChild(elemSlot);
					elemList.removeChild(elemEntry);
					li.parentNode.removeChild(li);
				};
				elemEntry.appendChild(elemRemove);
				
				var elemText = document.createTextNode(' ' + elemSlot.value);
				elemEntry.appendChild(elemText);
				
				sCheck = elemSlot.value;
			
				var elemNewSlot = document.createElement("input");
				elemNewSlot.type = "file";
				elemNewSlot.setAttribute("onclick", "addFile()");
				document.getElementById("fileInputFrame").appendChild(elemNewSlot);
				
				elemList.appendChild(elemEntry);
				
			}
			
			window.setInterval('addFile()', 20);
			
		</script>
	</xsl:template>
	
	
	
	<!-- submit -->
	<xsl:template match="submit" mode="complete">
		<tr class="lastline">
			<td></td>
			<td><xsl:apply-templates select="." mode="inputonly" /></td>
		</tr>
	</xsl:template>
	<xsl:template match="submit" mode="inputonly">
		<input type="submit" class="button" name="{@value}" value="{dyn:evaluate(@label)}" />
	</xsl:template>
	
	
</xsl:stylesheet>