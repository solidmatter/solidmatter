
	// Making object from keywords, it makes a big perfomance overhead in IE
	// and slightly better perfomance in Mozilla
	// You can define any amount of keyword groups
	// Words should match ([a-z0-9][a-z0-9_]*) and should be separated by spaces
	hPHPKeywords = cacheKeywords( /// TODO: Add more keywords here 
		// language constructs		
		"if foreach else elseif function class new switch case return class continue global\
		break for as true false echo while do declare include require require_once include_once\
		and or not null xor\
		endfor endforeach endif die endswitch print",	
		// standart functions
		"array and argv as argc cfunction endwhile default  \
		empty enddeclare  \
		e_all e_parse e_error e_warning eval exit extends \
		http_cookie_vars http_get_vars http_post_vars \
		http_post_files http_env_vars http_server_vars  \
		list old_function or parent php_os php_self php_version \
		require require_once static switch stdclass\
		virtual  __file__ __line__ __sleep __wakeup isset abs acos acosh addcslashes \
		addslashes apache_child_terminate apache_get_modules apache_get_version \
		apache_getenv apache_lookup_uri apache_note apache_request_auth_name apache_request_auth_type \
		apache_request_discard_request_body apache_request_err_headers_out apache_request_headers \
		apache_request_headers_in apache_request_headers_out apache_request_is_initial_req \
		apache_request_log_error apache_request_meets_conditions apache_request_remote_host \
		apache_request_run apache_request_satisfies apache_request_server_port \
		apache_request_set_etag apache_request_set_last_modified apache_request_some_auth_required \
		apache_request_sub_req_lookup_file apache_request_sub_req_lookup_uri apache_request_sub_req_method_uri \
		apache_request_update_mtime apache_response_headers apache_setenv array_change_key_case array_chunk \
		array_combine array_count_values array_diff array_diff_assoc array_diff_uassoc \
		array_fill array_filter array_flip array_intersect array_intersect_assoc \
		array_intersect_uassoc array_key_exists array_keys array_map array_merge \
		array_merge_recursive array_multisort array_pad array_pop array_push array_rand \
		array_reduce array_reverse array_search array_shift array_slice array_splice \
		array_sum array_udiff array_udiff_assoc array_udiff_uassoc array_uintersect \
		array_uintersect_assoc array_uintersect_uassoc array_unique array_unshift \
		array_values array_walk array_walk_recursive arsort asXML([string asin \
		asinh asort assert assert_options atan atan2 atanh base64_decode base64_encode \
		base_convert basename bcadd bccomp bcdiv bcmod bcmul bcpow bcpowmod bcscale \
		bcsqrt bcsub bin2hex bind_textdomain_codeset  bindec bindtextdomain birdstep_autocommit \
		birdstep_close birdstep_commit birdstep_connect birdstep_exec birdstep_fetch \
		birdstep_fieldname birdstep_fieldnum birdstep_freeresult birdstep_off_autocommit \
		birdstep_result birdstep_rollback bzcompress \
		bzdecompress bzerrno bzerror bzerrstr bzopen bzread cal_days_in_month cal_from_jd \
		cal_info cal_to_jd call_user_func call_user_func_array call_user_method \
		call_user_method_array ceil chdir checkdate chgrp chmod chown  chr chroot \
		chunk_split class_exists clearstatcache closedir closelog com_create_guid \
		com_event_sink com_load_typelib com_message_pump com_print_typeinfo compact \
		confirm_extname_compiled connection_aborted connection_status constant \
		convert_cyr_string copy cos cosh count count_chars cpdf_add_annotation \
		cpdf_add_outline cpdf_arc cpdf_begin_text cpdf_circle cpdf_clip cpdf_close \
		cpdf_closepath cpdf_closepath_fill_stroke cpdf_closepath_stroke cpdf_continue_text \
		cpdf_curveto cpdf_end_text cpdf_fill cpdf_fill_stroke cpdf_finalize cpdf_finalize_page \
		cpdf_global_set_document_limits cpdf_import_jpeg cpdf_lineto cpdf_moveto \
		cpdf_newpath cpdf_open cpdf_output_buffer cpdf_page_init cpdf_place_inline_image \
		cpdf_rect cpdf_restore cpdf_rlineto cpdf_rmoveto cpdf_rotate cpdf_rotate_text \
		cpdf_save cpdf_save_to_file cpdf_scale cpdf_set_action_url cpdf_set_char_spacing \
		cpdf_set_creator cpdf_set_current_page cpdf_set_font cpdf_set_font_directories \
		cpdf_set_font_map_file cpdf_set_horiz_scaling cpdf_set_keywords cpdf_set_leading \
		cpdf_set_page_animation cpdf_set_subject cpdf_set_text_matrix cpdf_set_text_pos \
		cpdf_set_text_rendering cpdf_set_text_rise cpdf_set_title cpdf_set_viewer_preferences \
		cpdf_set_word_spacing cpdf_setdash cpdf_setflat cpdf_setgray cpdf_setgray_fill \
		cpdf_setgray_stroke cpdf_setlinecap cpdf_setlinejoin cpdf_setlinewidth \
		cpdf_setmiterlimit cpdf_setrgbcolor cpdf_setrgbcolor_fill cpdf_setrgbcolor_stroke \
		cpdf_show cpdf_show_xy cpdf_stringwidth cpdf_stroke cpdf_text cpdf_translate \
		crc32 create_function crypt ctype_alnum ctype_alpha ctype_cntrl ctype_digit \
		ctype_graph ctype_lower ctype_print ctype_punct ctype_space ctype_upper \
		ctype_xdigit curl_close curl_errno curl_error curl_exec curl_getinfo curl_init \
		curl_multi_add_handle curl_multi_close curl_multi_exec curl_multi_getcontent \
		curl_multi_info_read curl_multi_init curl_multi_remove_handle curl_multi_select \
		curl_setopt curl_version current date date_sunrise date_sunset dba_close \
		dba_delete dba_exists dba_fetch dba_firstkey dba_handlers dba_insert dba_key_split \
		dba_list dba_nextkey dba_open dba_optimize dba_popen dba_replace dba_sync \
		dbase_add_record dbase_close dbase_create dbase_delete_record dbase_get_header_info \
		dbase_get_record dbase_get_record_with_names dbase_numfields dbase_numrecords \
		dbase_open dbase_pack dbase_replace_record dbx_close dbx_compare dbx_connect \
		dbx_error dbx_escape_string dbx_fetch_row dbx_query dbx_sort dcgettext \
		dcngettext  debug_backtrace debug_print_backtrace debug_zval_dump decbin \
		dechex decoct define define_syslog_variables defined deg2rad dgettext dio_close \
		dio_fcntl dio_open dio_read dio_seek dio_stat dio_tcsetattr dio_truncate \
		dio_write dir dirname disk_free_space disk_total_space dl dngettext  dns_check_record \
		dns_get_mx dns_get_record dom_attr_attr dom_attr_is_id dom_cdatasection_cdatasection \
		dom_characterdata_append_data dom_characterdata_delete_data dom_characterdata_insert_data \
		dom_characterdata_replace_data dom_characterdata_substring_data \
		dom_document_adopt_node dom_document_create_attribute dom_document_create_attribute_ns \
		dom_document_create_cdatasection dom_document_create_comment dom_document_create_document_fragment \
		dom_document_create_element dom_document_create_element_ns dom_document_create_entity_reference \
		dom_document_create_processing_instruction dom_document_create_text_node \
		dom_document_document dom_document_get_element_by_id dom_document_get_elements_by_tag_name \
		dom_document_get_elements_by_tag_name_ns dom_document_import_node dom_document_normalize_document \
		dom_document_rename_node dom_document_save_html dom_document_save_html_file \
		dom_document_xinclude dom_documentfragment_documentfragment dom_domconfiguration_can_set_parameter \
		dom_domconfiguration_get_parameter dom_domconfiguration_set_parameter dom_domerrorhandler_handle_error \
		dom_domimplementation_create_document dom_domimplementation_create_document_type \
		dom_domimplementation_get_feature dom_domimplementation_has_feature dom_domimplementationlist_item \
		dom_domimplementationsource_get_domimplementation dom_domimplementationsource_get_domimplementations \
		dom_domstringlist_item dom_element_element dom_element_get_attribute dom_element_get_attribute_node \
		dom_element_get_attribute_node_ns dom_element_get_attribute_ns dom_element_get_elements_by_tag_name \
		dom_element_get_elements_by_tag_name_ns dom_element_has_attribute dom_element_has_attribute_ns \
		dom_element_remove_attribute dom_element_remove_attribute_node dom_element_remove_attribute_ns \
		dom_element_set_attribute dom_element_set_attribute_node dom_element_set_attribute_node_ns \
		dom_element_set_attribute_ns dom_element_set_id_attribute dom_element_set_id_attribute_node \
		dom_element_set_id_attribute_ns dom_entityreference_entityreference dom_import_simplexml \
		dom_namednodemap_get_named_item dom_namednodemap_get_named_item_ns dom_namednodemap_item \
		dom_namednodemap_remove_named_item dom_namednodemap_remove_named_item_ns \
		dom_namednodemap_set_named_item dom_namednodemap_set_named_item_ns dom_namelist_get_name \
		dom_namelist_get_namespace_uri dom_node_append_child dom_node_clone_node \
		dom_node_get_feature dom_node_get_user_data dom_node_has_attributes dom_node_has_child_nodes \
		dom_node_insert_before dom_node_is_default_namespace dom_node_is_equal_node \
		dom_node_is_same_node dom_node_is_supported dom_node_lookup_namespace_uri \
		dom_node_lookup_prefix dom_node_normalize dom_node_remove_child dom_node_replace_child \
		dom_node_set_user_data dom_nodelist_item dom_processinginstruction_processinginstruction \
		dom_string_extend_find_offset16 dom_string_extend_find_offset32 dom_text_is_whitespace_in_element_content \
		dom_text_replace_whole_text dom_text_split_text dom_userdatahandler_handle \
		dom_xpath_query dom_xpath_register_ns dom_xpath_xpath domdocument domelement \
		domnode _dom_document_schema_validate domnode dom_document_load domnode dom_document_load_html \
		domnode dom_document_load_html_file domnode dom_document_loadxml domnode dom_document_relaxNG_validate_file \
		domnode dom_document_relaxNG_validate_xml domnode dom_document_save domnode dom_document_savexml \
		domnode dom_document_validate domtext_text([string each easter_date easter_days \
		end ereg ereg_replace eregi eregi_replace error_log error_reporting escapeshellarg \
		escapeshellcmd exec exif_imagetype exif_read_data exif_tagname exif_thumbnail \
		exp explode expm1 extension_loaded extract ezmlm_hash fam_cancel_monitor \
		fam_close fam_monitor_collection fam_monitor_directory fam_monitor_file \
		fam_next_event fam_open fam_pending fam_resume_monitor fam_suspend_monitor \
		fbsql_affected_rows fbsql_autocommit fbsql_blob_size fbsql_change_user \
		fbsql_clob_size fbsql_close fbsql_commit fbsql_connect fbsql_create_blob \
		fbsql_create_clob fbsql_create_db fbsql_data_seek fbsql_database fbsql_database_password \
		fbsql_db_query fbsql_db_status fbsql_drop_db fbsql_errno fbsql_error fbsql_fetch_array \
		fbsql_fetch_assoc fbsql_fetch_field fbsql_fetch_lengths fbsql_fetch_object \
		fbsql_fetch_row fbsql_field_flags fbsql_field_len fbsql_field_name fbsql_field_seek \
		fbsql_field_table fbsql_field_type fbsql_free_result fbsql_get_autostart_info \
		fbsql_hostname fbsql_insert_id fbsql_list_dbs fbsql_list_fields fbsql_list_tables \
		fbsql_next_result fbsql_num_fields fbsql_num_rows fbsql_password fbsql_pconnect \
		fbsql_query fbsql_read_blob fbsql_read_clob fbsql_result fbsql_rollback \
		fbsql_select_db fbsql_set_lob_mode fbsql_set_transaction fbsql_start_db \
		fbsql_stop_db fbsql_table_name fbsql_username fbsql_warnings fclose fdf_add_doc_javascript \
		fdf_add_template fdf_close fdf_create fdf_enum_values fdf_errno fdf_error \
		fdf_get_ap fdf_get_attachment fdf_get_encoding fdf_get_file fdf_get_flags \
		fdf_get_opt fdf_get_status fdf_get_value fdf_get_version fdf_header fdf_next_field_name \
		fdf_open fdf_open_string fdf_remove_item fdf_save fdf_save_string fdf_set_ap \
		fdf_set_encoding fdf_set_file fdf_set_flags fdf_set_javascript_action fdf_set_on_import_javascript \
		fdf_set_opt fdf_set_status fdf_set_submit_form_action fdf_set_target_frame \
		fdf_set_value fdf_set_version feof fflush fgetc fgetcsv fgets fgetss file \
		file_exists file_get_contents file_put_contents fileatime filectime filegroup \
		fileinode filemtime fileowner fileperms filepro filepro_fieldcount filepro_fieldname \
		filepro_fieldtype filepro_fieldwidth filepro_retrieve filepro_rowcount \
		filesize filetype firstChild floatval flock floor flush fmod fnmatch fopen \
		fpassthru fprintf fread frenchtojd fscanf fseek fsockopen fstat ftell ftok \
		ftp_alloc ftp_cdup ftp_chdir ftp_chmod ftp_close ftp_connect ftp_delete \
		ftp_exec ftp_fget ftp_fput ftp_get ftp_get_option ftp_login ftp_mdtm ftp_mkdir \
		ftp_nb_continue ftp_nb_fget ftp_nb_fput ftp_nb_get ftp_nb_put ftp_nlist \
		ftp_pasv ftp_put ftp_pwd ftp_raw ftp_rawlist ftp_rename ftp_rmdir ftp_set_option \
		ftp_site ftp_size ftp_ssl_connect ftp_systype ftruncate func_get_arg func_get_args \
		func_num_args function_exists fwrite gd_info get_browser get_cfg_var get_class \
		get_class_methods get_class_vars get_current_user get_declared_classes \
		get_declared_interfaces get_defined_constants get_defined_functions get_defined_vars \
		get_extension_funcs get_headers get_html_translation_table get_include_path \
		get_included_files get_loaded_extensions get_magic_quotes_gpc get_magic_quotes_runtime \
		get_meta_tags get_object_vars get_parent_class get_resource_type getallheaders \
		getcwd getdate getenv gethostbyaddr gethostbyname gethostbynamel getimagesize \
		getlastmod getmygid getmyinode getmypid getmyuid getopt getprotobyname \
		getprotobynumber getrandmax getrusage getservbyname getservbyport gettext \
		gettimeofday gettype glob gmdate gmmktime gmp_abs gmp_add gmp_and gmp_clrbit \
		gmp_cmp gmp_com gmp_div_q gmp_div_qr gmp_div_r gmp_divexact gmp_fact gmp_gcd \
		gmp_gcdext gmp_hamdist gmp_init gmp_intval gmp_invert gmp_jacobi gmp_legendre \
		gmp_mod gmp_mul gmp_neg gmp_or gmp_perfect_square gmp_popcount gmp_pow \
		gmp_powm gmp_prob_prime gmp_random gmp_scan0 gmp_scan1 gmp_setbit gmp_sign \
		gmp_sqrt gmp_sqrtrem gmp_strval gmp_sub gmp_xor gmstrftime gregoriantojd \
		gzcompress gzdeflate gzencode gzfile gzinflate gzopen gzuncompress header \
		headers_list headers_sent hebrev hebrevc hexdec highlight_file highlight_string \
		html_entity_decode htmlentities htmlspecialchars http_build_query hypot \
		ibase_add_user ibase_affected_rows ibase_blob_add ibase_blob_cancel ibase_blob_close \
		ibase_blob_create ibase_blob_echo ibase_blob_get ibase_blob_import ibase_blob_info \
		ibase_blob_open ibase_close ibase_commit ibase_commit_ret ibase_connect \
		ibase_delete_user ibase_drop_db ibase_errcode ibase_errmsg ibase_execute \
		ibase_fetch_assoc ibase_fetch_object ibase_fetch_row ibase_field_info ibase_free_event_handler \
		ibase_free_query ibase_free_result ibase_gen_id ibase_modify_user ibase_name_result \
		ibase_num_fields ibase_num_params ibase_num_rows ibase_param_info ibase_pconnect \
		ibase_prepare ibase_query ibase_rollback ibase_rollback_ret ibase_set_event_handler \
		ibase_timefmt ibase_trans ibase_wait_event iconv iconv_get_encoding iconv_mime_decode \
		iconv_mime_decode_headers iconv_mime_encode iconv_set_encoding iconv_strlen \
		iconv_strpos iconv_strrpos iconv_substr idate ifx_affected_rows ifx_blobinfile_mode \
		ifx_byteasvarchar ifx_close ifx_connect ifx_copy_blob ifx_create_blob ifx_create_char \
		ifx_do ifx_error ifx_errormsg ifx_fetch_row ifx_fieldproperties ifx_fieldtypes \
		ifx_free_blob ifx_free_char ifx_free_result ifx_get_blob ifx_get_char ifx_getsqlca \
		ifx_htmltbl_result ifx_nullformat ifx_num_fields ifx_num_rows ifx_pconnect \
		ifx_prepare ifx_query ifx_textasvarchar ifx_update_blob ifx_update_char \
		ifxus_close_slob ifxus_create_slob ifxus_free_slob ifxus_open_slob ifxus_read_slob \
		ifxus_seek_slob ifxus_tell_slob ifxus_write_slob ignore_user_abort image2wbmp \
		image_type_to_extension image_type_to_mime_type imagealphablending imageantialias \
		imagearc imagechar imagecharup imagecolorallocate imagecolorallocatealpha \
		imagecolorat imagecolorclosest imagecolorclosestalpha imagecolorclosesthwb \
		imagecolordeallocate imagecolorexact imagecolorexactalpha imagecolormatch \
		imagecolorresolve imagecolorresolvealpha imagecolorset imagecolorsforindex \
		imagecolorstotal imagecolortransparent imagecopy imagecopymerge imagecopymergegray \
		imagecopyresampled imagecopyresized imagecreate imagecreatefromgd imagecreatefromgd2 \
		imagecreatefromgd2part imagecreatefromgif imagecreatefromjpeg imagecreatefrompng \
		imagecreatefromstring imagecreatefromwbmp imagecreatefromxbm imagecreatefromxpm \
		imagecreatetruecolor imagedashedline imagedestroy imageellipse imagefill \
		imagefilledarc imagefilledellipse imagefilledpolygon imagefilledrectangle \
		imagefilltoborder imagefilter imagefontheight imagefontwidth imageftbbox \
		imagefttext imagegammacorrect imagegd imagegd2 imagegif imageinterlace \
		imageistruecolor imagejpeg imagelayereffect imageline imageloadfont imagepalettecopy \
		imagepng imagepolygon imagepsbbox imagepscopyfont imagepsencodefont imagepsextendfont \
		imagepsfreefont imagepsloadfont imagepsslantfont imagepstext imagerectangle \
		imagerotate imagesavealpha imagesetbrush imagesetpixel imagesetstyle imagesetthickness \
		imagesettile imagestring imagestringup imagesx imagesy imagetruecolortopalette \
		imagettfbbox imagettftext imagetypes imagewbmp imagexbm imap_8bit imap_alerts \
		imap_append imap_base64 imap_binary imap_body imap_bodystruct imap_check \
		imap_clearflag_full imap_close imap_createmailbox imap_delete imap_deletemailbox \
		imap_errors imap_expunge imap_fetch_overview imap_fetchbody imap_fetchheader \
		imap_fetchstructure imap_get_quota imap_get_quotaroot imap_getacl imap_getmailboxes \
		imap_getsubscribed imap_headerinfo imap_headers imap_last_error imap_list \
		imap_lsub imap_mail imap_mail_compose imap_mail_copy imap_mail_move imap_mailboxmsginfo \
		imap_mime_header_decode imap_msgno imap_num_msg imap_num_recent imap_open \
		imap_ping imap_qprint imap_renamemailbox imap_reopen imap_rfc822_parse_adrlist \
		imap_rfc822_parse_headers imap_rfc822_write_address imap_scan imap_search \
		imap_set_quota imap_setacl imap_setflag_full imap_sort imap_status imap_subscribe \
		imap_thread imap_timeout imap_uid imap_undelete imap_unsubscribe imap_utf7_decode \
		imap_utf7_encode imap_utf8 implode import_request_variables in_array ingres_autocommit \
		ingres_close ingres_commit ingres_connect ingres_fetch_array ingres_fetch_object \
		ingres_fetch_row ingres_field_length ingres_field_name ingres_field_nullable \
		ingres_field_precision ingres_field_scale ingres_field_type ingres_num_fields \
		ingres_num_rows ingres_pconnect ingres_query ingres_rollback ini_get ini_get_all \
		ini_restore ini_set intval ip2long iptcembed iptcparse \
		ircg_channel_mode ircg_disconnect ircg_eval_ecmascript_params ircg_fetch_error_msg \
		ircg_get_username ircg_html_encode ircg_ignore_add ircg_ignore_del ircg_invite \
		ircg_is_conn_alive ircg_join ircg_kick ircg_list ircg_lookup_format_messages \
		ircg_lusers ircg_msg ircg_names ircg_nick ircg_nickname_escape ircg_nickname_unescape \
		ircg_notice ircg_oper ircg_part ircg_pconnect ircg_register_format_messages \
		ircg_set_current ircg_set_file ircg_set_on_die ircg_set_on_read_data ircg_topic \
		ircg_who ircg_whois is_a is_array is_bool is_callable is_dir is_executable \
		is_file is_finite is_float is_infinite is_link is_long is_nan is_null is_numeric \
		is_object is_readable is_resource is_scalar is_string is_subclass_of is_uploaded_file \
		is_writable jddayofweek jdmonthname jdtofrench jdtogregorian jdtojewish \
		jdtojulian jdtounix jewishtojd join jpeg2wbmp  juliantojd key krsort ksort \
		lcg_value ldap_8859_to_t61 ldap_add ldap_bind ldap_compare ldap_connect \
		ldap_count_entries ldap_delete ldap_dn2ufn ldap_err2str ldap_errno ldap_error \
		ldap_explode_dn ldap_first_attribute ldap_first_entry ldap_first_reference \
		ldap_free_result ldap_get_attributes ldap_get_dn ldap_get_entries ldap_get_option \
		ldap_get_values ldap_get_values_len ldap_list ldap_mod_add ldap_mod_del \
		ldap_mod_replace ldap_next_attribute ldap_next_entry ldap_next_reference \
		ldap_parse_reference ldap_parse_result ldap_read ldap_rename ldap_sasl_bind \
		ldap_search ldap_set_option ldap_set_rebind_proc ldap_sort ldap_start_tls \
		ldap_t61_to_8859 ldap_unbind leak length	unsigned levenshtein libxml_set_streams_context \
		link linkinfo localeconv localtime log log10 log1p long2ip lstat ltrim \
		mail max mb_convert_case mb_convert_encoding mb_convert_kana mb_convert_variables \
		mb_decode_mimeheader mb_decode_numericentity mb_detect_encoding mb_detect_order \
		mb_encode_mimeheader mb_encode_numericentity mb_ereg mb_ereg_match mb_ereg_replace \
		mb_ereg_search mb_ereg_search_getpos mb_ereg_search_getregs mb_ereg_search_init \
		mb_ereg_search_pos mb_ereg_search_regs mb_ereg_search_setpos mb_eregi mb_eregi_replace \
		mb_get_info mb_http_input mb_http_output mb_internal_encoding mb_language \
		mb_output_handler mb_parse_str mb_preferred_mime_name mb_regex_encoding \
		mb_regex_set_options mb_send_mail mb_split mb_strcut mb_strimwidth mb_strlen \
		mb_strpos mb_strrpos mb_strtolower mb_strtoupper mb_strwidth mb_substitute_character \
		mb_substr mb_substr_count mcrypt_cbc mcrypt_cfb mcrypt_create_iv mcrypt_decrypt \
		mcrypt_ecb mcrypt_enc_get_algorithms_name mcrypt_enc_get_block_size mcrypt_enc_get_iv_size \
		mcrypt_enc_get_key_size mcrypt_enc_get_modes_name mcrypt_enc_get_supported_key_sizes \
		mcrypt_enc_is_block_algorithm mcrypt_enc_is_block_algorithm_mode mcrypt_enc_is_block_mode \
		mcrypt_enc_self_test mcrypt_encrypt mcrypt_generic mcrypt_generic_deinit \
		mcrypt_generic_end mcrypt_generic_init mcrypt_get_block_size mcrypt_get_cipher_name \
		mcrypt_get_iv_size mcrypt_get_key_size mcrypt_list_algorithms mcrypt_list_modes \
		mcrypt_module_close mcrypt_module_get_algo_block_size mcrypt_module_get_algo_key_size \
		mcrypt_module_get_supported_key_sizes mcrypt_module_is_block_algorithm \
		mcrypt_module_is_block_algorithm_mode mcrypt_module_is_block_mode mcrypt_module_open \
		mcrypt_module_self_test mcrypt_ofb mcve_adduser mcve_adduserarg mcve_bt \
		mcve_checkstatus mcve_chkpwd mcve_chngpwd mcve_completeauthorizations mcve_connect \
		mcve_connectionerror mcve_deleteresponse mcve_deletetrans mcve_deleteusersetup \
		mcve_deluser mcve_destroyconn mcve_destroyengine mcve_disableuser mcve_edituser \
		mcve_enableuser mcve_force mcve_getcell mcve_getcellbynum mcve_getcommadelimited \
		mcve_getheader mcve_getuserarg mcve_getuserparam mcve_gft mcve_gl mcve_gut \
		mcve_initconn mcve_initengine mcve_initusersetup mcve_iscommadelimited \
		mcve_liststats mcve_listusers mcve_maxconntimeout mcve_monitor mcve_numcolumns \
		mcve_numrows mcve_override mcve_parsecommadelimited mcve_ping mcve_preauth \
		mcve_preauthcompletion mcve_qc mcve_responseparam mcve_return mcve_returncode \
		mcve_returnstatus mcve_sale mcve_setblocking mcve_setdropfile mcve_setip \
		mcve_setssl mcve_setssl_files mcve_settimeout mcve_settle mcve_text_avs \
		mcve_text_code mcve_text_cv mcve_transactionauth mcve_transactionavs mcve_transactionbatch \
		mcve_transactioncv mcve_transactionid mcve_transactionitem mcve_transactionssent \
		mcve_transactiontext mcve_transinqueue mcve_transnew mcve_transparam mcve_transsend \
		mcve_ub mcve_uwait mcve_verifyconnection mcve_verifysslcert mcve_void md5 \
		md5_file mdecrypt_generic memory_get_usage metaphone method_exists mhash \
		mhash_count mhash_get_block_size mhash_get_hash_name mhash_keygen_s2k microtime \
		mime_content_type min ming_setcubicthreshold  ming_setscale ming_useconstants \
		ming_useswfversion mkdir \
		mktime money_format move_uploaded_file msession_call msession_connect msession_count \
		msession_create msession_ctl msession_destroy msession_disconnect msession_exec \
		msession_find msession_get msession_get_array msession_get_data msession_inc \
		msession_list msession_listvar msession_lock msession_ping msession_plugin \
		msession_randstr msession_set msession_set_array msession_set_data msession_timeout \
		msession_uniq msession_unlock msg_get_queue msg_receive msg_remove_queue \
		msg_send msg_set_queue msg_stat_queue msql_affected_rows msql_close msql_connect \
		msql_create_db msql_data_seek msql_db_query msql_drop_db msql_error msql_fetch_array \
		msql_fetch_field msql_fetch_object msql_fetch_row msql_field_flags msql_field_len \
		msql_field_name msql_field_seek msql_field_table msql_field_type msql_free_result \
		msql_list_dbs msql_list_fields msql_list_tables msql_num_fields msql_num_rows \
		msql_pconnect msql_query msql_result msql_select_db mssql_bind mssql_close \
		mssql_connect mssql_data_seek mssql_execute mssql_fetch_array mssql_fetch_assoc \
		mssql_fetch_batch mssql_fetch_field mssql_fetch_object mssql_fetch_row \
		mssql_field_length mssql_field_name mssql_field_seek mssql_field_type mssql_free_result \
		mssql_free_statement mssql_get_last_message mssql_guid_string mssql_init \
		mssql_min_error_severity mssql_min_message_severity mssql_next_result mssql_num_fields \
		mssql_num_rows mssql_pconnect mssql_query mssql_result mssql_rows_affected \
		mssql_select_db mt_getrandmax mt_rand mt_srand mysql_affected_rows mysql_client_encoding \
		mysql_close mysql_connect mysql_create_db mysql_data_seek mysql_db_query \
		mysql_drop_db mysql_errno mysql_error mysql_escape_string mysql_fetch_array \
		mysql_fetch_assoc mysql_fetch_field mysql_fetch_lengths mysql_fetch_object \
		mysql_fetch_row mysql_field_flags mysql_field_len mysql_field_name mysql_field_seek \
		mysql_field_table mysql_field_type mysql_free_result mysql_get_client_info \
		mysql_get_host_info mysql_get_proto_info mysql_get_server_info mysql_info \
		mysql_insert_id mysql_list_dbs mysql_list_fields mysql_list_processes mysql_list_tables \
		mysql_num_fields mysql_num_rows mysql_pconnect mysql_ping mysql_query mysql_real_escape_string \
		mysql_result mysql_select_db mysql_stat mysql_thread_id mysql_unbuffered_query \
		mysqli_affected_rows mysqli_autocommit mysqli_bind_param mysqli_bind_result \
		mysqli_change_user mysqli_character_set_name mysqli_close mysqli_commit \
		mysqli_connect mysqli_connect_errno mysqli_connect_error mysqli_data_seek \
		mysqli_debug mysqli_disable_reads_from_master mysqli_disable_rpl_parse \
		mysqli_dump_debug_info mysqli_embedded_connect mysqli_enable_reads_from_master \
		mysqli_enable_rpl_parse mysqli_errno mysqli_error mysqli_execute mysqli_fetch \
		mysqli_fetch_array  mysqli_fetch_assoc  mysqli_fetch_field  mysqli_fetch_field_direct  \
		mysqli_fetch_fields  mysqli_fetch_lengths  mysqli_fetch_object  mysqli_fetch_row  \
		mysqli_field_count mysqli_field_seek mysqli_field_tell mysqli_free_result \
		mysqli_get_client_info mysqli_get_host_info  mysqli_get_metadata mysqli_get_proto_info \
		mysqli_get_server_info mysqli_get_server_version mysqli_info mysqli_init \
		mysqli_insert_id mysqli_kill mysqli_master_query mysqli_more_results mysqli_multi_query \
		mysqli_next_result mysqli_num_fields mysqli_num_rows mysqli_options mysqli_param_count \
		mysqli_ping mysqli_prepare mysqli_query mysqli_real_connect mysqli_real_escape_string \
		mysqli_real_query mysqli_rollback mysqli_rpl_parse_enabled mysqli_rpl_probe \
		mysqli_rpl_query_type mysqli_select_db mysqli_send_long_data mysqli_send_query \
		mysqli_server_end mysqli_server_init mysqli_slave_query mysqli_sqlstate \
		mysqli_ssl_set mysqli_stat mysqli_stmt_affected_rows mysqli_stmt_close \
		mysqli_stmt_data_seek mysqli_stmt_errno mysqli_stmt_error mysqli_stmt_num_rows \
		mysqli_stmt_sqlstate mysqli_stmt_store_result mysqli_store_result mysqli_thread_id \
		mysqli_thread_safe mysqli_use_result mysqli_warning_count  natcasesort \
		natsort ncurses_addch ncurses_addchnstr ncurses_addchstr ncurses_addnstr \
		ncurses_addstr ncurses_assume_default_colors ncurses_attroff ncurses_attron \
		ncurses_attrset ncurses_baudrate ncurses_beep ncurses_bkgd ncurses_bkgdset \
		ncurses_border ncurses_bottom_panel ncurses_can_change_color ncurses_cbreak \
		ncurses_clear ncurses_clrtobot ncurses_clrtoeol ncurses_color_content ncurses_color_set \
		ncurses_curs_set ncurses_def_prog_mode ncurses_def_shell_mode ncurses_define_key \
		ncurses_del_panel ncurses_delay_output ncurses_delch ncurses_deleteln ncurses_delwin \
		ncurses_doupdate ncurses_echo ncurses_echochar ncurses_end ncurses_erase \
		ncurses_erasechar ncurses_filter ncurses_flash ncurses_flushinp ncurses_getch \
		ncurses_getmaxyx ncurses_getmouse ncurses_getyx ncurses_halfdelay ncurses_has_colors \
		ncurses_has_ic ncurses_has_il ncurses_has_key ncurses_hide_panel ncurses_hline \
		ncurses_inch ncurses_init ncurses_init_color ncurses_init_pair ncurses_insch \
		ncurses_insdelln ncurses_insertln ncurses_insstr ncurses_instr ncurses_isendwin \
		ncurses_keyok ncurses_keypad ncurses_killchar ncurses_longname ncurses_meta \
		ncurses_mouse_trafo ncurses_mouseinterval ncurses_mousemask ncurses_move \
		ncurses_move_panel ncurses_mvaddch ncurses_mvaddchnstr ncurses_mvaddchstr \
		ncurses_mvaddnstr ncurses_mvaddstr ncurses_mvcur ncurses_mvdelch ncurses_mvgetch \
		ncurses_mvhline ncurses_mvinch ncurses_mvvline ncurses_mvwaddstr ncurses_napms \
		ncurses_new_panel ncurses_newpad ncurses_newwin ncurses_nl ncurses_nocbreak \
		ncurses_noecho ncurses_nonl ncurses_noqiflush ncurses_noraw ncurses_pair_content \
		ncurses_panel_above ncurses_panel_below ncurses_panel_window ncurses_pnoutrefresh \
		ncurses_prefresh ncurses_putp ncurses_qiflush ncurses_raw ncurses_refresh \
		ncurses_replace_panel ncurses_reset_prog_mode ncurses_reset_shell_mode \
		ncurses_resetty ncurses_savetty ncurses_scr_dump ncurses_scr_init ncurses_scr_restore \
		ncurses_scr_set ncurses_scrl ncurses_show_panel ncurses_slk_attr ncurses_slk_attroff \
		ncurses_slk_attron ncurses_slk_attrset ncurses_slk_clear ncurses_slk_color \
		ncurses_slk_init ncurses_slk_noutrefresh ncurses_slk_refresh ncurses_slk_restore \
		ncurses_slk_set ncurses_slk_touch ncurses_standend ncurses_standout ncurses_start_color \
		ncurses_termattrs ncurses_termname ncurses_timeout ncurses_top_panel ncurses_typeahead \
		ncurses_ungetch ncurses_ungetmouse ncurses_update_panels ncurses_use_default_colors \
		ncurses_use_env ncurses_use_extended_names ncurses_vidattr ncurses_vline \
		ncurses_waddch ncurses_waddstr ncurses_wattroff ncurses_wattron ncurses_wattrset \
		ncurses_wborder ncurses_wclear ncurses_wcolor_set ncurses_werase ncurses_wgetch \
		ncurses_whline ncurses_wmouse_trafo ncurses_wmove ncurses_wnoutrefresh \
		ncurses_wrefresh ncurses_wstandend ncurses_wstandout ncurses_wvline next \
		ngettext nl2br nl_langinfo nodeType	unsigned nsapi_request_headers nsapi_response_headers \
		nsapi_virtual number_format ob_clean ob_end_clean ob_end_flush ob_flush \
		ob_get_clean ob_get_contents ob_get_flush ob_get_length ob_get_level ob_get_status \
		ob_gzhandler ob_iconv_handler ob_implicit_flush ob_list_handlers ob_start \
		oci_bind_by_name oci_cancel oci_close oci_collection_append oci_collection_assign \
		oci_collection_element_assign oci_collection_element_get oci_collection_max \
		oci_collection_size oci_collection_trim oci_commit oci_connect oci_define_by_name \
		oci_error oci_execute oci_fetch oci_fetch_all oci_fetch_array oci_fetch_assoc \
		oci_fetch_object oci_fetch_row oci_field_is_null oci_field_name oci_field_precision \
		oci_field_scale oci_field_size oci_field_type oci_field_type_raw oci_free_collection \
		oci_free_descriptor oci_free_statement oci_internal_debug oci_lob_append \
		oci_lob_close oci_lob_copy oci_lob_eof oci_lob_erase oci_lob_export oci_lob_flush \
		oci_lob_import oci_lob_is_equal oci_lob_load oci_lob_read oci_lob_rewind \
		oci_lob_save oci_lob_seek oci_lob_size oci_lob_tell oci_lob_truncate oci_lob_write \
		oci_lob_write_temporary oci_new_collection oci_new_connect oci_new_cursor \
		oci_new_descriptor oci_num_fields oci_num_rows oci_parse oci_password_change \
		oci_pconnect oci_result oci_rollback oci_server_version oci_set_prefetch \
		oci_statement_type ocifetchinto ocigetbufferinglob ocisetbufferinglob octdec \
		odbc_autocommit odbc_binmode odbc_close odbc_close_all odbc_columnprivileges \
		odbc_columns odbc_commit odbc_connect odbc_cursor odbc_data_source odbc_error \
		odbc_errormsg odbc_exec odbc_execute odbc_fetch_array odbc_fetch_into odbc_fetch_object \
		odbc_fetch_row odbc_field_len odbc_field_name odbc_field_num odbc_field_scale \
		odbc_field_type odbc_foreignkeys odbc_free_result odbc_gettypeinfo odbc_longreadlen \
		odbc_next_result odbc_num_fields odbc_num_rows odbc_pconnect odbc_prepare \
		odbc_primarykeys odbc_procedurecolumns odbc_procedures odbc_result odbc_result_all \
		odbc_rollback odbc_setoption odbc_specialcolumns odbc_statistics odbc_tableprivileges \
		odbc_tables opendir openlog openssl_csr_export openssl_csr_export_to_file \
		openssl_csr_new openssl_csr_sign openssl_error_string openssl_open openssl_pkcs7_decrypt \
		openssl_pkcs7_encrypt openssl_pkcs7_sign openssl_pkcs7_verify openssl_pkey_export \
		openssl_pkey_export_to_file openssl_pkey_free openssl_pkey_get_private \
		openssl_pkey_get_public openssl_pkey_new openssl_private_decrypt openssl_private_encrypt \
		openssl_public_decrypt openssl_public_encrypt openssl_seal openssl_sign \
		openssl_verify openssl_x509_check_private_key openssl_x509_checkpurpose \
		openssl_x509_export openssl_x509_export_to_file openssl_x509_free openssl_x509_parse \
		openssl_x509_read ora_bind ora_close ora_columnname ora_columnsize ora_columntype \
		ora_commit ora_commitoff ora_commiton ora_do ora_error ora_errorcode ora_exec \
		ora_fetch ora_fetch_into ora_getcolumn ora_logoff ora_logon ora_numcols \
		ora_numrows ora_open ora_parse ora_plogon ora_rollback ord output_add_rewrite_var \
		output_reset_rewrite_vars ovrimos_autocommit ovrimos_close ovrimos_commit \
		ovrimos_connect ovrimos_cursor ovrimos_exec ovrimos_execute ovrimos_fetch_into \
		ovrimos_fetch_row ovrimos_field_len ovrimos_field_name ovrimos_field_num \
		ovrimos_field_type ovrimos_free_result ovrimos_longreadlen ovrimos_num_fields \
		ovrimos_num_rows ovrimos_prepare ovrimos_result ovrimos_result_all ovrimos_rollback \
		ovrimos_setoption pack parse_ini_file parse_str parse_url passthru pathinfo \
		pclose pcntl_alarm pcntl_exec pcntl_fork pcntl_getpriority pcntl_setpriority \
		pcntl_signal pcntl_wait pcntl_waitpid pcntl_wexitstatus pcntl_wifexited \
		pcntl_wifsignaled pcntl_wifstopped pcntl_wstopsig pcntl_wtermsig pdf_add_annotation \
		pdf_add_bookmark pdf_add_launchlink pdf_add_locallink pdf_add_note pdf_add_pdflink \
		pdf_add_thumbnail pdf_add_weblink pdf_arc pdf_arcn pdf_attach_file pdf_begin_page \
		pdf_begin_pattern pdf_begin_template pdf_circle pdf_clip pdf_close pdf_close_image \
		pdf_close_pdi pdf_close_pdi_page pdf_closepath pdf_closepath_fill_stroke \
		pdf_closepath_stroke pdf_concat pdf_continue_text pdf_curveto pdf_delete \
		pdf_end_page pdf_end_pattern pdf_end_template pdf_endpath pdf_fill pdf_fill_stroke \
		pdf_findfont pdf_get_buffer pdf_get_font pdf_get_fontname pdf_get_fontsize \
		pdf_get_image_height pdf_get_image_width pdf_get_majorversion pdf_get_minorversion \
		pdf_get_parameter pdf_get_pdi_parameter pdf_get_pdi_value pdf_get_value \
		pdf_initgraphics pdf_lineto pdf_makespotcolor pdf_moveto pdf_new pdf_open \
		pdf_open_ccitt pdf_open_file pdf_open_gif pdf_open_image pdf_open_image_file \
		pdf_open_jpeg pdf_open_memory_image pdf_open_pdi pdf_open_pdi_page pdf_open_png \
		pdf_open_tiff pdf_place_image pdf_place_pdi_page pdf_rect pdf_restore pdf_rotate \
		pdf_save pdf_scale pdf_set_border_color pdf_set_border_dash pdf_set_border_style \
		pdf_set_char_spacing pdf_set_duration pdf_set_font pdf_set_horiz_scaling \
		pdf_set_info pdf_set_info_author pdf_set_info_creator pdf_set_info_keywords \
		pdf_set_info_subject pdf_set_info_title pdf_set_leading pdf_set_parameter \
		pdf_set_text_pos pdf_set_text_rendering pdf_set_text_rise pdf_set_transition \
		pdf_set_value pdf_set_word_spacing pdf_setcolor pdf_setdash pdf_setflat \
		pdf_setfont pdf_setgray pdf_setgray_fill pdf_setgray_stroke pdf_setlinecap \
		pdf_setlinejoin pdf_setlinewidth pdf_setmatrix pdf_setmiterlimit pdf_setpolydash \
		pdf_setrgbcolor pdf_setrgbcolor_fill pdf_setrgbcolor_stroke pdf_show pdf_show_boxed \
		pdf_show_xy pdf_skew pdf_stringwidth pdf_stroke pdf_translate pfpro_cleanup \
		pfpro_init pfpro_process pfpro_process_raw pfpro_version pfsockopen pg_affected_rows \
		pg_cancel_query pg_client_encoding pg_close pg_connect pg_connection_busy \
		pg_connection_reset pg_connection_status pg_convert pg_copy_from pg_copy_to \
		pg_dbname pg_delete pg_end_copy pg_escape_bytea pg_escape_string pg_fetch_all \
		pg_fetch_array pg_fetch_assoc pg_fetch_object pg_fetch_result pg_fetch_row \
		pg_field_is_null pg_field_name pg_field_num pg_field_prtlen pg_field_size \
		pg_field_type pg_free_result pg_get_notify pg_get_pid pg_get_result pg_host \
		pg_insert pg_last_error pg_last_notice pg_last_oid pg_lo_close pg_lo_create \
		pg_lo_export pg_lo_import pg_lo_open pg_lo_read pg_lo_read_all pg_lo_seek \
		pg_lo_tell pg_lo_unlink pg_lo_write pg_meta_data pg_num_fields pg_num_rows \
		pg_options pg_parameter_status pg_pconnect pg_ping pg_port pg_put_line \
		pg_query pg_result_error pg_result_seek pg_result_status pg_select pg_send_query \
		pg_set_client_encoding pg_trace pg_tty pg_unescape_bytea pg_untrace pg_update \
		pg_version php_check_syntax php_egg_logo_guid php_ini_scanned_files php_logo_guid \
		php_real_logo_guid php_sapi_name php_snmpv3 php_strip_whitespace php_uname \
		phpcredits phpinfo phpversion pi png2wbmp  popen posix_ctermid posix_get_last_error \
		posix_getcwd posix_getegid posix_geteuid posix_getgid posix_getgrgid posix_getgrnam \
		posix_getgroups posix_getlogin posix_getpgid posix_getpgrp posix_getpid \
		posix_getppid posix_getpwnam posix_getpwuid posix_getrlimit posix_getsid \
		posix_getuid posix_isatty posix_kill posix_mkfifo posix_setegid posix_seteuid \
		posix_setgid posix_setpgid posix_setsid posix_setuid posix_strerror posix_times \
		posix_ttyname posix_uname pow preg_grep preg_match preg_match_all preg_quote \
		preg_replace preg_replace_callback preg_split prev print_r printf proc_close \
		proc_get_status proc_nice proc_open proc_terminate pspell_add_to_personal \
		pspell_add_to_session pspell_check pspell_clear_session pspell_config_create \
		pspell_config_ignore pspell_config_mode pspell_config_personal pspell_config_repl \
		pspell_config_runtogether pspell_config_save_repl pspell_new pspell_new_config \
		pspell_new_personal pspell_save_wordlist pspell_store_replacement pspell_suggest \
		putenv quoted_printable_decode quotemeta rad2deg rand range rawurldecode \
		rawurlencode readdir readfile readgzfile readline readline_add_history \
		readline_clear_history readline_completion_function readline_info readline_list_history \
		readline_read_history readline_write_history readlink realpath recode_file \
		recode_string register_shutdown_function register_tick_function rename \
		reset restore_error_handler restore_exception_handler restore_include_path \
		rewind rewinddir rmdir round rsort rtrim scandir sem_acquire sem_get sem_release \
		sem_remove serialize session_cache_expire session_cache_limiter session_decode \
		session_destroy session_encode session_get_cookie_params session_id session_is_registered \
		session_module_name session_name session_regenerate_id session_register \
		session_save_path session_set_cookie_params session_set_save_handler session_start \
		session_unregister session_unset session_write_close set_error_handler \
		set_exception_handler set_include_path set_magic_quotes_runtime set_socket_blocking \
		set_time_limit setcookie setlocale setrawcookie settype severity	unsigned \
		sha1 sha1_file shell_exec shm_attach shm_detach shm_get_var shm_put_var \
		shm_remove shm_remove_var shmop_close  shmop_delete  shmop_open  shmop_read  \
		shmop_size  shmop_write  short dom_node_compare_document_position shuffle \
		similar_text simplexml_import_dom simplexml_load_file simplexml_load_string \
		sin sinh sleep smfi_addheader smfi_addrcpt smfi_chgheader smfi_delrcpt \
		smfi_getsymval smfi_replacebody smfi_setflags smfi_setreply smfi_settimeout \
		snmp3_get snmp3_getnext snmp3_real_walk snmp3_set snmp3_walk snmp_get_quick_print \
		snmp_get_valueretrieval snmp_read_mib snmp_set_enum_print snmp_set_oid_numeric_print \
		snmp_set_quick_print snmp_set_valueretrieval snmpget snmpgetnext snmprealwalk \
		snmpset snmpwalk socket_accept socket_bind socket_clear_error socket_close \
		socket_connect socket_create socket_create_listen socket_create_pair socket_get_option \
		socket_getpeername socket_getsockname socket_last_error socket_listen socket_read \
		socket_recv socket_recvfrom socket_select socket_send socket_sendto socket_set_block \
		socket_set_nonblock socket_set_option socket_shutdown socket_strerror socket_write \
		solid_fetch_prev sort soundex split spliti sprintf sql_regcase sqlite_array_query \
		sqlite_busy_timeout sqlite_changes sqlite_close sqlite_column sqlite_create_aggregate \
		sqlite_create_function sqlite_current sqlite_error_string sqlite_escape_string \
		sqlite_factory sqlite_fetch_all sqlite_fetch_array sqlite_fetch_column_types \
		sqlite_fetch_object sqlite_fetch_single sqlite_field_name sqlite_has_more \
		sqlite_has_prev sqlite_last_error sqlite_last_insert_rowid sqlite_libencoding \
		sqlite_libversion sqlite_next sqlite_num_fields sqlite_num_rows sqlite_open \
		sqlite_popen sqlite_prev sqlite_query sqlite_rewind sqlite_seek sqlite_single_query \
		sqlite_udf_decode_binary sqlite_udf_encode_binary sqlite_unbuffered_query \
		sqrt srand sscanf stat \
		str_ireplace str_pad str_repeat str_replace str_rot13 str_shuffle str_split \
		str_word_count strcasecmp strchr strcmp strcoll strcspn stream_bucket_append \
		stream_bucket_make_writeable stream_bucket_new stream_bucket_prepend stream_context_create \
		stream_context_get_options stream_context_set_option stream_context_set_params \
		stream_copy_to_stream stream_filter_append stream_filter_prepend stream_filter_register \
		stream_get_contents stream_get_filters stream_get_line stream_get_meta_data \
		stream_get_transports stream_get_wrappers stream_select stream_set_blocking \
		stream_set_timeout stream_set_write_buffer stream_socket_accept stream_socket_client \
		stream_socket_get_name stream_socket_recvfrom stream_socket_sendto stream_socket_server \
		stream_wrapper_register strftime strip_tags stripcslashes stripos stripslashes \
		stristr strlen strnatcasecmp strnatcmp strncasecmp strncmp strpbrk strpos \
		strrchr strrev strripos strrpos strspn strstr strtok strtolower strtotime \
		strtoupper strtr strval substr substr_compare substr_count substr_replace \
		swfaction_init swfbitmap_getHeight swfbitmap_getWidth swfbitmap_init swfbutton_addAction \
		swfbutton_addShape swfbutton_init swfbutton_keypress swfbutton_setAction \
		swfbutton_setDown swfbutton_setHit swfbutton_setMenu swfbutton_setOver \
		swfbutton_setUp swfdisplayitem_addAction swfdisplayitem_addColor swfdisplayitem_endMask \
		swfdisplayitem_move swfdisplayitem_moveTo swfdisplayitem_multColor swfdisplayitem_rotate \
		swfdisplayitem_rotateTo swfdisplayitem_scale swfdisplayitem_scaleTo swfdisplayitem_setDepth \
		swfdisplayitem_setMaskLevel swfdisplayitem_setMatrix swfdisplayitem_setName \
		swfdisplayitem_setRatio swfdisplayitem_skewX swfdisplayitem_skewXTo swfdisplayitem_skewY \
		swfdisplayitem_skewYTo swffill_init swffill_moveTo swffill_rotateTo swffill_scaleTo \
		swffill_skewXTo swffill_skewYTo swffont_addChars swffont_getAscent swffont_getDescent \
		swffont_getLeading swffont_getUTF8Width swffont_getWideWidth swffont_getWidth \
		swffont_init swffontchar_addChars swfgradient_addEntry swfgradient_init \
		swfmorph_getShape1 swfmorph_getShape2 swfmorph_init swfmovie_add swfmovie_init \
		swfmovie_labelframe swfmovie_nextframe swfmovie_output swfmovie_save swfmovie_saveToFile \
		swfmovie_setBackground swfmovie_setDimension swfmovie_setFrames swfmovie_setRate \
		swfmovie_streamMp3 swfshape_addfill swfshape_drawarc swfshape_drawcircle \
		swfshape_drawcubic swfshape_drawcurve swfshape_drawcurveto swfshape_drawglyph \
		swfshape_drawline swfshape_drawlineto swfshape_init swfshape_movepen swfshape_movepento \
		swfshape_setleftfill swfshape_setline swfsound_init swfsprite_add swfsprite_init \
		swfsprite_labelFrame swfsprite_nextFrame swfsprite_remove swfsprite_setFrames \
		swftext_addString swftext_addUTF8String swftext_addWideString swftext_getAscent \
		swftext_getDescent swftext_getLeading swftext_getUTF8Width swftext_getWideWidth \
		swftext_getWidth swftext_init swftext_moveTo swftext_setColor swftext_setFont \
		swftext_setHeight swftext_setSpacing swftextfield_addChars swftextfield_addString \
		swftextfield_align swftextfield_init swftextfield_setBounds swftextfield_setColor \
		swftextfield_setFont swftextfield_setHeight swftextfield_setIndentation \
		swftextfield_setLeftMargin swftextfield_setLineSpacing swftextfield_setMargins \
		swftextfield_setName swftextfield_setPadding swftextfield_setRightMargin \
		sybase_affected_rows sybase_close sybase_connect sybase_data_seek sybase_deadlock_retry_count \
		sybase_fetch_array sybase_fetch_assoc sybase_fetch_field sybase_fetch_object \
		sybase_fetch_row sybase_field_seek sybase_free_result sybase_get_last_message \
		sybase_min_client_severity sybase_min_error_severity sybase_min_message_severity \
		sybase_min_server_severity sybase_num_fields sybase_num_rows sybase_pconnect \
		sybase_query sybase_result sybase_select_db sybase_set_message_handler \
		sybase_unbuffered_query symlink syslog system tan tanh tempnam textdomain \
		tidy_access_count tidy_clean_repair tidy_config_count tidy_diagnose tidy_error_count \
		tidy_get_body tidy_get_config tidy_get_error_buffer tidy_get_head tidy_get_html \
		tidy_get_html_ver tidy_get_output tidy_get_release tidy_get_root tidy_get_status \
		tidy_getopt tidy_is_xhtml tidy_parse_file tidy_parse_string tidy_repair_file \
		tidy_repair_string tidy_warning_count time time_nanosleep tmpfile token_get_all \
		token_name touch trigger_error trim uasort ucfirst ucwords udm_add_search_limit \
		udm_alloc_agent udm_alloc_agent_array udm_api_version udm_cat_list udm_cat_path \
		udm_check_charset udm_check_stored udm_clear_search_limits udm_close_stored \
		udm_crc32 udm_errno udm_error udm_find udm_free_agent udm_free_ispell_data \
		udm_free_res udm_get_doc_count udm_get_res_field udm_get_res_field_ex udm_get_res_param \
		udm_hash32 udm_load_ispell_data udm_make_excerpt udm_open_stored udm_parse_query_string \
		udm_set_agent_param udm_set_agent_param_ex uksort umask uniqid unixtojd \
		unlink unpack unregister_tick_function unserialize urldecode urlencode \
		usleep usort utf8_decode utf8_encode uudecode uuencode var_dump var_export \
		variant_abs variant_add variant_and variant_cast variant_cat variant_cmp \
		variant_date_from_timestamp variant_date_to_timestamp variant_div variant_eqv \
		variant_fix variant_get_type variant_idiv variant_imp variant_index_get \
		variant_int variant_mod variant_mul variant_neg variant_not variant_or \
		variant_pow variant_round variant_set variant_set_type variant_sub variant_xor \
		version_compare vfprintf vprintf \
		vsprintf wddx_add_vars wddx_deserialize wddx_packet_end wddx_packet_start \
		wddx_serialize_value wddx_serialize_vars wordwrap xml_error_string xml_get_current_byte_index \
		xml_get_current_column_number xml_get_current_line_number xml_get_error_code \
		xml_parse xml_parse_into_struct xml_parser_create xml_parser_create_ns \
		xml_parser_free xml_parser_get_option xml_parser_set_option xml_set_character_data_handler \
		xml_set_default_handler xml_set_element_handler xml_set_end_namespace_decl_handler \
		xml_set_external_entity_ref_handler xml_set_notation_decl_handler xml_set_object \
		xml_set_processing_instruction_handler xml_set_start_namespace_decl_handler \
		xml_set_unparsed_entity_decl_handler xmlrpc_decode xmlrpc_decode_request \
		xmlrpc_encode xmlrpc_encode_request xmlrpc_get_type xmlrpc_is_fault xmlrpc_parse_method_descriptions \
		xmlrpc_server_add_introspection_data xmlrpc_server_call_method xmlrpc_server_create \
		xmlrpc_server_destroy xmlrpc_server_register_introspection_callback xmlrpc_server_register_method \
		xmlrpc_set_type xsl_xsltprocessor_get_parameter xsl_xsltprocessor_import_stylesheet \
		xsl_xsltprocessor_remove_parameter xsl_xsltprocessor_set_parameter xsl_xsltprocessor_transform_to_doc \
		xsl_xsltprocessor_transform_to_uri xsl_xsltprocessor_transform_to_xml yaz_addinfo \
		yaz_ccl_conf yaz_ccl_parse yaz_close yaz_connect yaz_database  yaz_element \
		yaz_errno yaz_error yaz_es yaz_es_result yaz_get_option yaz_hits yaz_itemorder \
		yaz_present yaz_range yaz_record yaz_scan yaz_scan_result yaz_schema yaz_search \
		yaz_set_option yaz_sort yaz_syntax yaz_wait yp_all yp_cat yp_err_string \
		yp_errno yp_first yp_get_default_domain yp_master yp_match yp_next yp_order"
		);	
		hSmartyKeywords = cacheKeywords(
		// Block functions
		"if foreach capture section strip", 
		// Non-block functions
		"config_load foreachelse include\
		include_php insert elseif else ldelim rdelim literal php sectionelse\
		index index_prev index_next iteration first last rownum loop show total", 
		// Custom functions
		"assign  counter cycle debug eval fetch html_checkboxes html_image html_options\
		html_radios html_select_date html_select_time html_table math mailto popup_init\
		popup textformat\
		getprices getarticles",
		// Standart modifiers
		"capitalize count_characters cat count_paragraphs count_sentences count_words\
		date_format default escape indent lower nl2br regex_replace replace spacify\
		string_format strip strip_tags truncate upper wordwrap"
		); 
	
