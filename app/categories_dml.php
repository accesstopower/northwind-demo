<?php

// Data functions (insert, update, delete, form) for table categories

// This script and data application were generated by AppGini 23.14
// Download AppGini for free from https://bigprof.com/appgini/download/

function categories_insert(&$error_message = '') {
	global $Translation;

	// mm: can member insert record?
	$arrPerm = getTablePermissions('categories');
	if(!$arrPerm['insert']) return false;

	$data = [
		'Picture' => Request::fileUpload('Picture', [
			'maxSize' => 204800,
			'types' => 'jpg|jpeg|gif|png|webp',
			'noRename' => false,
			'dir' => '',
			'success' => function($name, $selected_id) {
				createThumbnail($name, getThumbnailSpecs('categories', 'Picture', 'tv'));
				createThumbnail($name, getThumbnailSpecs('categories', 'Picture', 'dv'));
			},
			'failure' => function($selected_id, $fileRemoved) {
				if(!strlen(Request::val('SelectedID'))) return '';

				/* for empty upload fields, when saving a copy of an existing record, copy the original upload field */
				return existing_value('categories', 'Picture', Request::val('SelectedID'));
			},
		]),
		'CategoryName' => Request::val('CategoryName', ''),
		'Description' => Request::val('Description', ''),
	];


	// hook: categories_before_insert
	if(function_exists('categories_before_insert')) {
		$args = [];
		if(!categories_before_insert($data, getMemberInfo(), $args)) {
			if(isset($args['error_message'])) $error_message = $args['error_message'];
			return false;
		}
	}

	$error = '';
	// set empty fields to NULL
	$data = array_map(function($v) { return ($v === '' ? NULL : $v); }, $data);
	insert('categories', backtick_keys_once($data), $error);
	if($error) {
		$error_message = $error;
		return false;
	}

	$recID = db_insert_id(db_link());

	update_calc_fields('categories', $recID, calculated_fields()['categories']);

	// hook: categories_after_insert
	if(function_exists('categories_after_insert')) {
		$res = sql("SELECT * FROM `categories` WHERE `CategoryID`='" . makeSafe($recID, false) . "' LIMIT 1", $eo);
		if($row = db_fetch_assoc($res)) {
			$data = array_map('makeSafe', $row);
		}
		$data['selectedID'] = makeSafe($recID, false);
		$args = [];
		if(!categories_after_insert($data, getMemberInfo(), $args)) { return $recID; }
	}

	// mm: save ownership data
	set_record_owner('categories', $recID, getLoggedMemberID());

	// if this record is a copy of another record, copy children if applicable
	if(strlen(Request::val('SelectedID'))) categories_copy_children($recID, Request::val('SelectedID'));

	return $recID;
}

function categories_copy_children($destination_id, $source_id) {
	global $Translation;
	$requests = []; // array of curl handlers for launching insert requests
	$eo = ['silentErrors' => true];
	$safe_sid = makeSafe($source_id);

	// launch requests, asynchronously
	curl_batch($requests);
}

function categories_delete($selected_id, $AllowDeleteOfParents = false, $skipChecks = false) {
	// insure referential integrity ...
	global $Translation;
	$selected_id = makeSafe($selected_id);

	// mm: can member delete record?
	if(!check_record_permission('categories', $selected_id, 'delete')) {
		return $Translation['You don\'t have enough permissions to delete this record'];
	}

	// hook: categories_before_delete
	if(function_exists('categories_before_delete')) {
		$args = [];
		if(!categories_before_delete($selected_id, $skipChecks, getMemberInfo(), $args))
			return $Translation['Couldn\'t delete this record'] . (
				!empty($args['error_message']) ?
					'<div class="text-bold">' . strip_tags($args['error_message']) . '</div>'
					: '' 
			);
	}

	// child table: products
	$res = sql("SELECT `CategoryID` FROM `categories` WHERE `CategoryID`='{$selected_id}'", $eo);
	$CategoryID = db_fetch_row($res);
	$rires = sql("SELECT COUNT(1) FROM `products` WHERE `CategoryID`='" . makeSafe($CategoryID[0]) . "'", $eo);
	$rirow = db_fetch_row($rires);
	if($rirow[0] && !$AllowDeleteOfParents && !$skipChecks) {
		$RetMsg = $Translation["couldn't delete"];
		$RetMsg = str_replace('<RelatedRecords>', $rirow[0], $RetMsg);
		$RetMsg = str_replace('<TableName>', 'products', $RetMsg);
		return $RetMsg;
	} elseif($rirow[0] && $AllowDeleteOfParents && !$skipChecks) {
		$RetMsg = $Translation['confirm delete'];
		$RetMsg = str_replace('<RelatedRecords>', $rirow[0], $RetMsg);
		$RetMsg = str_replace('<TableName>', 'products', $RetMsg);
		$RetMsg = str_replace('<Delete>', '<input type="button" class="btn btn-danger" value="' . html_attr($Translation['yes']) . '" onClick="window.location = \'categories_view.php?SelectedID=' . urlencode($selected_id) . '&delete_x=1&confirmed=1&csrf_token=' . urlencode(csrf_token(false, true)) . '\';">', $RetMsg);
		$RetMsg = str_replace('<Cancel>', '<input type="button" class="btn btn-success" value="' . html_attr($Translation[ 'no']) . '" onClick="window.location = \'categories_view.php?SelectedID=' . urlencode($selected_id) . '\';">', $RetMsg);
		return $RetMsg;
	}

	// delete file stored in the 'Picture' field
	$res = sql("SELECT `Picture` FROM `categories` WHERE `CategoryID`='{$selected_id}'", $eo);
	if($row = @db_fetch_row($res)) {
		if($row[0] != '') {
			@unlink(getUploadDir('') . $row[0]);
			$thumbDV = preg_replace('/\.(jpg|jpeg|gif|png|webp)$/i', '_dv.$1', $row[0]);
			$thumbTV = preg_replace('/\.(jpg|jpeg|gif|png|webp)$/i', '_tv.$1', $row[0]);
			@unlink(getUploadDir('') . $thumbTV);
			@unlink(getUploadDir('') . $thumbDV);
		}
	}

	sql("DELETE FROM `categories` WHERE `CategoryID`='{$selected_id}'", $eo);

	// hook: categories_after_delete
	if(function_exists('categories_after_delete')) {
		$args = [];
		categories_after_delete($selected_id, getMemberInfo(), $args);
	}

	// mm: delete ownership data
	sql("DELETE FROM `membership_userrecords` WHERE `tableName`='categories' AND `pkValue`='{$selected_id}'", $eo);
}

function categories_update(&$selected_id, &$error_message = '') {
	global $Translation;

	// mm: can member edit record?
	if(!check_record_permission('categories', $selected_id, 'edit')) return false;

	$data = [
		'Picture' => Request::fileUpload('Picture', [
			'maxSize' => 204800,
			'types' => 'jpg|jpeg|gif|png|webp',
			'noRename' => false,
			'dir' => '',
			'id' => $selected_id,
			'success' => function($name, $selected_id) {
				createThumbnail($name, getThumbnailSpecs('categories', 'Picture', 'tv'));
				createThumbnail($name, getThumbnailSpecs('categories', 'Picture', 'dv'));
			},
			'removeOnSuccess' => true,
			'removeOnRequest' => true,
			'remove' => function($selected_id) {
				// delete old file from server
				$oldFile = existing_value('categories', 'Picture', $selected_id);
				if(!$oldFile) return;

				@unlink(getUploadDir('') . $oldFile);

				// delete thumbnails
				preg_match('/^[a-z0-9_]+\.(jpg|jpeg|gif|png|webp)$/i', $oldFile, $m);
				$thumbDV = str_replace(".{$m[1]}ffffgggg", "_dv.{$m[1]}", $oldFile . 'ffffgggg');
				$thumbTV = str_replace(".{$m[1]}ffffgggg", "_tv.{$m[1]}", $oldFile . 'ffffgggg');
				@unlink(getUploadDir('') . $thumbTV);
				@unlink(getUploadDir('') . $thumbDV);
			},
			'failure' => function($selected_id, $fileRemoved) {
				if($fileRemoved) return '';
				return existing_value('categories', 'Picture', $selected_id);
			},
		]),
		'CategoryName' => Request::val('CategoryName', ''),
		'Description' => Request::val('Description', ''),
	];

	// get existing values
	$old_data = getRecord('categories', $selected_id);
	if(is_array($old_data)) {
		$old_data = array_map('makeSafe', $old_data);
		$old_data['selectedID'] = makeSafe($selected_id);
	}

	$data['selectedID'] = makeSafe($selected_id);

	// hook: categories_before_update
	if(function_exists('categories_before_update')) {
		$args = ['old_data' => $old_data];
		if(!categories_before_update($data, getMemberInfo(), $args)) {
			if(isset($args['error_message'])) $error_message = $args['error_message'];
			return false;
		}
	}

	$set = $data; unset($set['selectedID']);
	foreach ($set as $field => $value) {
		$set[$field] = ($value !== '' && $value !== NULL) ? $value : NULL;
	}

	if(!update(
		'categories', 
		backtick_keys_once($set), 
		['`CategoryID`' => $selected_id], 
		$error_message
	)) {
		echo $error_message;
		echo '<a href="categories_view.php?SelectedID=' . urlencode($selected_id) . "\">{$Translation['< back']}</a>";
		exit;
	}


	$eo = ['silentErrors' => true];

	update_calc_fields('categories', $data['selectedID'], calculated_fields()['categories']);

	// hook: categories_after_update
	if(function_exists('categories_after_update')) {
		$res = sql("SELECT * FROM `categories` WHERE `CategoryID`='{$data['selectedID']}' LIMIT 1", $eo);
		if($row = db_fetch_assoc($res)) $data = array_map('makeSafe', $row);

		$data['selectedID'] = $data['CategoryID'];
		$args = ['old_data' => $old_data];
		if(!categories_after_update($data, getMemberInfo(), $args)) return;
	}

	// mm: update ownership data
	sql("UPDATE `membership_userrecords` SET `dateUpdated`='" . time() . "' WHERE `tableName`='categories' AND `pkValue`='" . makeSafe($selected_id) . "'", $eo);
}

function categories_form($selected_id = '', $AllowUpdate = 1, $AllowInsert = 1, $AllowDelete = 1, $separateDV = 0, $TemplateDV = '', $TemplateDVP = '') {
	// function to return an editable form for a table records
	// and fill it with data of record whose ID is $selected_id. If $selected_id
	// is empty, an empty form is shown, with only an 'Add New'
	// button displayed.

	global $Translation;
	$eo = ['silentErrors' => true];
	$noUploads = null;
	$row = $urow = $jsReadOnly = $jsEditable = $lookups = null;

	$noSaveAsCopy = true;

	// mm: get table permissions
	$arrPerm = getTablePermissions('categories');
	if(!$arrPerm['insert'] && $selected_id == '')
		// no insert permission and no record selected
		// so show access denied error unless TVDV
		return $separateDV ? $Translation['tableAccessDenied'] : '';
	$AllowInsert = ($arrPerm['insert'] ? true : false);
	// print preview?
	$dvprint = false;
	if(strlen($selected_id) && Request::val('dvprint_x') != '') {
		$dvprint = true;
	}


	// populate filterers, starting from children to grand-parents

	// unique random identifier
	$rnd1 = ($dvprint ? rand(1000000, 9999999) : '');

	if($selected_id) {
		if(!check_record_permission('categories', $selected_id, 'view'))
			return $Translation['tableAccessDenied'];

		// can edit?
		$AllowUpdate = check_record_permission('categories', $selected_id, 'edit');

		// can delete?
		$AllowDelete = check_record_permission('categories', $selected_id, 'delete');

		$res = sql("SELECT * FROM `categories` WHERE `CategoryID`='" . makeSafe($selected_id) . "'", $eo);
		if(!($row = db_fetch_array($res))) {
			return error_message($Translation['No records found'], 'categories_view.php', false);
		}
		$urow = $row; /* unsanitized data */
		$row = array_map('safe_html', $row);
	} else {
		$filterField = Request::val('FilterField');
		$filterOperator = Request::val('FilterOperator');
		$filterValue = Request::val('FilterValue');
	}

	ob_start();
	?>

	<script>
		// initial lookup values

		jQuery(function() {
			setTimeout(function() {
			}, 50); /* we need to slightly delay client-side execution of the above code to allow AppGini.ajaxCache to work */
		});
	</script>
	<?php

	$lookups = str_replace('__RAND__', $rnd1, ob_get_clean());


	// code for template based detail view forms

	// open the detail view template
	if($dvprint) {
		$template_file = is_file("./{$TemplateDVP}") ? "./{$TemplateDVP}" : './templates/categories_templateDVP.html';
		$templateCode = @file_get_contents($template_file);
	} else {
		$template_file = is_file("./{$TemplateDV}") ? "./{$TemplateDV}" : './templates/categories_templateDV.html';
		$templateCode = @file_get_contents($template_file);
	}

	// process form title
	$templateCode = str_replace('<%%DETAIL_VIEW_TITLE%%>', 'Add/Edit Product Categories', $templateCode);
	$templateCode = str_replace('<%%RND1%%>', $rnd1, $templateCode);
	$templateCode = str_replace('<%%EMBEDDED%%>', (Request::val('Embedded') ? 'Embedded=1' : ''), $templateCode);
	// process buttons
	if($arrPerm['insert'] && !$selected_id) { // allow insert and no record selected?
		if(!$selected_id) $templateCode = str_replace('<%%INSERT_BUTTON%%>', '<button type="submit" class="btn btn-success" id="insert" name="insert_x" value="1" onclick="return categories_validateData();"><i class="glyphicon glyphicon-plus-sign"></i> ' . $Translation['Save New'] . '</button>', $templateCode);
		$templateCode = str_replace('<%%INSERT_BUTTON%%>', '<button type="submit" class="btn btn-default" id="insert" name="insert_x" value="1" onclick="return categories_validateData();"><i class="glyphicon glyphicon-plus-sign"></i> ' . $Translation['Save As Copy'] . '</button>', $templateCode);
	} else {
		$templateCode = str_replace('<%%INSERT_BUTTON%%>', '', $templateCode);
	}

	// 'Back' button action
	if(Request::val('Embedded')) {
		$backAction = 'AppGini.closeParentModal(); return false;';
	} else {
		$backAction = '$j(\'form\').eq(0).attr(\'novalidate\', \'novalidate\'); document.myform.reset(); return true;';
	}

	if($selected_id) {
		if(!Request::val('Embedded')) $templateCode = str_replace('<%%DVPRINT_BUTTON%%>', '<button type="submit" class="btn btn-default" id="dvprint" name="dvprint_x" value="1" onclick="$j(\'form\').eq(0).prop(\'novalidate\', true); document.myform.reset(); return true;" title="' . html_attr($Translation['Print Preview']) . '"><i class="glyphicon glyphicon-print"></i> ' . $Translation['Print Preview'] . '</button>', $templateCode);
		if($AllowUpdate)
			$templateCode = str_replace('<%%UPDATE_BUTTON%%>', '<button type="submit" class="btn btn-success btn-lg" id="update" name="update_x" value="1" onclick="return categories_validateData();" title="' . html_attr($Translation['Save Changes']) . '"><i class="glyphicon glyphicon-ok"></i> ' . $Translation['Save Changes'] . '</button>', $templateCode);
		else
			$templateCode = str_replace('<%%UPDATE_BUTTON%%>', '', $templateCode);

		if($AllowDelete)
			$templateCode = str_replace('<%%DELETE_BUTTON%%>', '<button type="submit" class="btn btn-danger" id="delete" name="delete_x" value="1" title="' . html_attr($Translation['Delete']) . '"><i class="glyphicon glyphicon-trash"></i> ' . $Translation['Delete'] . '</button>', $templateCode);
		else
			$templateCode = str_replace('<%%DELETE_BUTTON%%>', '', $templateCode);

		$templateCode = str_replace('<%%DESELECT_BUTTON%%>', '<button type="submit" class="btn btn-default" id="deselect" name="deselect_x" value="1" onclick="' . $backAction . '" title="' . html_attr($Translation['Back']) . '"><i class="glyphicon glyphicon-chevron-left"></i> ' . $Translation['Back'] . '</button>', $templateCode);
	} else {
		$templateCode = str_replace('<%%UPDATE_BUTTON%%>', '', $templateCode);
		$templateCode = str_replace('<%%DELETE_BUTTON%%>', '', $templateCode);

		// if not in embedded mode and user has insert only but no view/update/delete,
		// remove 'back' button
		if(
			$arrPerm['insert']
			&& !$arrPerm['update'] && !$arrPerm['delete'] && !$arrPerm['view']
			&& !Request::val('Embedded')
		)
			$templateCode = str_replace('<%%DESELECT_BUTTON%%>', '', $templateCode);
		elseif($separateDV)
			$templateCode = str_replace(
				'<%%DESELECT_BUTTON%%>', 
				'<button
					type="submit" 
					class="btn btn-default" 
					id="deselect" 
					name="deselect_x" 
					value="1" 
					onclick="' . $backAction . '" 
					title="' . html_attr($Translation['Back']) . '">
						<i class="glyphicon glyphicon-chevron-left"></i> ' .
						$Translation['Back'] .
				'</button>',
				$templateCode
			);
		else
			$templateCode = str_replace('<%%DESELECT_BUTTON%%>', '', $templateCode);
	}

	// set records to read only if user can't insert new records and can't edit current record
	if(($selected_id && !$AllowUpdate) || (!$selected_id && !$AllowInsert)) {
		$jsReadOnly = '';
		$jsReadOnly .= "\tjQuery('#Picture').replaceWith('<div class=\"form-control-static\" id=\"Picture\">' + (jQuery('#Picture').val() || '') + '</div>');\n";
		$jsReadOnly .= "\tjQuery('#CategoryName').replaceWith('<div class=\"form-control-static\" id=\"CategoryName\">' + (jQuery('#CategoryName').val() || '') + '</div>');\n";
		$jsReadOnly .= "\tjQuery('.select2-container').hide();\n";

		$noUploads = true;
	} elseif(($AllowInsert && !$selected_id) || ($AllowUpdate && $selected_id)) {
		$jsEditable = "\tjQuery('form').eq(0).data('already_changed', true);"; // temporarily disable form change handler
		$jsEditable .= "\tjQuery('form').eq(0).data('already_changed', false);"; // re-enable form change handler
	}

	// process combos

	/* lookup fields array: 'lookup field name' => ['parent table name', 'lookup field caption'] */
	$lookup_fields = [];
	foreach($lookup_fields as $luf => $ptfc) {
		$pt_perm = getTablePermissions($ptfc[0]);

		// process foreign key links
		if($pt_perm['view'] || $pt_perm['edit']) {
			$templateCode = str_replace("<%%PLINK({$luf})%%>", '<button type="button" class="btn btn-default view_parent" id="' . $ptfc[0] . '_view_parent" title="' . html_attr($Translation['View'] . ' ' . $ptfc[1]) . '"><i class="glyphicon glyphicon-eye-open"></i></button>', $templateCode);
		}

		// if user has insert permission to parent table of a lookup field, put an add new button
		if($pt_perm['insert'] /* && !Request::val('Embedded')*/) {
			$templateCode = str_replace("<%%ADDNEW({$ptfc[0]})%%>", '<button type="button" class="btn btn-default add_new_parent" id="' . $ptfc[0] . '_add_new" title="' . html_attr($Translation['Add New'] . ' ' . $ptfc[1]) . '"><i class="glyphicon glyphicon-plus text-success"></i></button>', $templateCode);
		}
	}

	// process images
	$templateCode = str_replace('<%%UPLOADFILE(CategoryID)%%>', '', $templateCode);
	$templateCode = str_replace('<%%UPLOADFILE(Picture)%%>', ($noUploads ? '' : "<div>{$Translation['upload image']}</div>" . '<input type="file" name="Picture" id="Picture" data-filetypes="jpg|jpeg|gif|png|webp" data-maxsize="204800" style="max-width: calc(100% - 1.5rem);" accept="capture=camera,image/*">' . '<i class="text-danger clear-upload hidden pull-right" style="margin-top: -.1em; font-size: large;">&times;</i>'), $templateCode);
	if($AllowUpdate && $row['Picture'] != '') {
		$templateCode = str_replace('<%%REMOVEFILE(Picture)%%>', '<input type="checkbox" name="Picture_remove" id="Picture_remove" value="1"> <label for="Picture_remove" style="color: red; font-weight: bold;">'.$Translation['remove image'].'</label>', $templateCode);
	} else {
		$templateCode = str_replace('<%%REMOVEFILE(Picture)%%>', '', $templateCode);
	}
	$templateCode = str_replace('<%%UPLOADFILE(CategoryName)%%>', '', $templateCode);
	$templateCode = str_replace('<%%UPLOADFILE(Description)%%>', '', $templateCode);

	// process values
	if($selected_id) {
		if( $dvprint) $templateCode = str_replace('<%%VALUE(CategoryID)%%>', safe_html($urow['CategoryID']), $templateCode);
		if(!$dvprint) $templateCode = str_replace('<%%VALUE(CategoryID)%%>', html_attr($row['CategoryID']), $templateCode);
		$templateCode = str_replace('<%%URLVALUE(CategoryID)%%>', urlencode($urow['CategoryID']), $templateCode);
		$row['Picture'] = ($row['Picture'] != '' ? $row['Picture'] : 'blank.gif');
		if( $dvprint) $templateCode = str_replace('<%%VALUE(Picture)%%>', safe_html($urow['Picture']), $templateCode);
		if(!$dvprint) $templateCode = str_replace('<%%VALUE(Picture)%%>', html_attr($row['Picture']), $templateCode);
		$templateCode = str_replace('<%%URLVALUE(Picture)%%>', urlencode($urow['Picture']), $templateCode);
		if( $dvprint) $templateCode = str_replace('<%%VALUE(CategoryName)%%>', safe_html($urow['CategoryName']), $templateCode);
		if(!$dvprint) $templateCode = str_replace('<%%VALUE(CategoryName)%%>', html_attr($row['CategoryName']), $templateCode);
		$templateCode = str_replace('<%%URLVALUE(CategoryName)%%>', urlencode($urow['CategoryName']), $templateCode);
		if($AllowUpdate || $AllowInsert) {
			$templateCode = str_replace('<%%HTMLAREA(Description)%%>', '<textarea name="Description" id="Description" rows="5">' . safe_html(htmlspecialchars_decode($row['Description'])) . '</textarea>', $templateCode);
		} else {
			$templateCode = str_replace('<%%HTMLAREA(Description)%%>', '<div id="Description" class="form-control-static">' . $row['Description'] . '</div>', $templateCode);
		}
		$templateCode = str_replace('<%%VALUE(Description)%%>', nl2br($row['Description']), $templateCode);
		$templateCode = str_replace('<%%URLVALUE(Description)%%>', urlencode($urow['Description']), $templateCode);
	} else {
		$templateCode = str_replace('<%%VALUE(CategoryID)%%>', '', $templateCode);
		$templateCode = str_replace('<%%URLVALUE(CategoryID)%%>', urlencode(''), $templateCode);
		$templateCode = str_replace('<%%VALUE(Picture)%%>', 'blank.gif', $templateCode);
		$templateCode = str_replace('<%%VALUE(CategoryName)%%>', '', $templateCode);
		$templateCode = str_replace('<%%URLVALUE(CategoryName)%%>', urlencode(''), $templateCode);
		$templateCode = str_replace('<%%HTMLAREA(Description)%%>', '<textarea name="Description" id="Description" rows="5"></textarea>', $templateCode);
	}

	// process translations
	$templateCode = parseTemplate($templateCode);

	// clear scrap
	$templateCode = str_replace('<%%', '<!-- ', $templateCode);
	$templateCode = str_replace('%%>', ' -->', $templateCode);

	// hide links to inaccessible tables
	if(Request::val('dvprint_x') == '') {
		$templateCode .= "\n\n<script>\$j(function() {\n";
		$arrTables = getTableList();
		foreach($arrTables as $name => $caption) {
			$templateCode .= "\t\$j('#{$name}_link').removeClass('hidden');\n";
			$templateCode .= "\t\$j('#xs_{$name}_link').removeClass('hidden');\n";
		}

		$templateCode .= $jsReadOnly;
		$templateCode .= $jsEditable;

		if(!$selected_id) {
		}

		$templateCode.="\n});</script>\n";
	}

	// ajaxed auto-fill fields
	$templateCode .= '<script>';
	$templateCode .= '$j(function() {';


	$templateCode.="});";
	$templateCode.="</script>";
	$templateCode .= $lookups;

	// handle enforced parent values for read-only lookup fields
	$filterField = Request::val('FilterField');
	$filterOperator = Request::val('FilterOperator');
	$filterValue = Request::val('FilterValue');

	// don't include blank images in lightbox gallery
	$templateCode = preg_replace('/blank.gif" data-lightbox=".*?"/', 'blank.gif"', $templateCode);

	// don't display empty email links
	$templateCode=preg_replace('/<a .*?href="mailto:".*?<\/a>/', '', $templateCode);

	/* default field values */
	$rdata = $jdata = get_defaults('categories');
	if($selected_id) {
		$jdata = get_joined_record('categories', $selected_id);
		if($jdata === false) $jdata = get_defaults('categories');
		$rdata = $row;
	}
	$templateCode .= loadView('categories-ajax-cache', ['rdata' => $rdata, 'jdata' => $jdata]);

	// hook: categories_dv
	if(function_exists('categories_dv')) {
		$args = [];
		categories_dv(($selected_id ? $selected_id : FALSE), getMemberInfo(), $templateCode, $args);
	}

	return $templateCode;
}