<?php

// Data functions (insert, update, delete, form) for table products

// This script and data application was generated by AppGini, https://bigprof.com/appgini
// Download AppGini for free from https://bigprof.com/appgini/download/

function products_insert(&$error_message = '') {
	global $Translation;

	// mm: can member insert record?
	$arrPerm = getTablePermissions('products');
	if(!$arrPerm['insert']) {
		$error_message = $Translation['no insert permission'];
		return false;
	}

	$data = [
		'ProductName' => Request::val('ProductName', ''),
		'SupplierID' => Request::lookup('SupplierID', ''),
		'CategoryID' => Request::lookup('CategoryID', ''),
		'QuantityPerUnit' => Request::val('QuantityPerUnit', ''),
		'UnitPrice' => Request::val('UnitPrice', '0'),
		'UnitsInStock' => Request::val('UnitsInStock', '0'),
		'UnitsOnOrder' => Request::val('UnitsOnOrder', '0'),
		'ReorderLevel' => Request::val('ReorderLevel', '0'),
		'Discontinued' => Request::checkBox('Discontinued', '0'),
		'TechSheet' => Request::fileUpload('TechSheet', [
			'maxSize' => 2048000,
			'types' => 'txt|doc|docx|docm|odt|pdf|rtf',
			'noRename' => false,
			'dir' => '',
			'success' => function($name, $selected_id) {
			},
			'failure' => function($selected_id, $fileRemoved) {
				if(!strlen(Request::val('SelectedID'))) return '';

				/* for empty upload fields, when saving a copy of an existing record, copy the original upload field */
				return existing_value('products', 'TechSheet', Request::val('SelectedID'));
			},
		]),
	];

	// record owner is current user
	$recordOwner = getLoggedMemberID();

	$recID = tableInsert('products', $data, $recordOwner, $error_message);

	// if this record is a copy of another record, copy children if applicable
	if(strlen(Request::val('SelectedID')) && $recID !== false)
		products_copy_children($recID, Request::val('SelectedID'));

	return $recID;
}

function products_copy_children($destination_id, $source_id) {
	global $Translation;
	$requests = []; // array of curl handlers for launching insert requests
	$eo = ['silentErrors' => true];
	$safe_sid = makeSafe($source_id);
	$currentUsername = getLoggedMemberID();
	$errorMessage = '';

	// launch requests, asynchronously
	curl_batch($requests);
}

function products_delete($selected_id, $AllowDeleteOfParents = false, $skipChecks = false) {
	// insure referential integrity ...
	global $Translation;
	$selected_id = makeSafe($selected_id);

	// mm: can member delete record?
	if(!check_record_permission('products', $selected_id, 'delete')) {
		return $Translation['You don\'t have enough permissions to delete this record'];
	}

	// hook: products_before_delete
	if(function_exists('products_before_delete')) {
		$args = [];
		if(!products_before_delete($selected_id, $skipChecks, getMemberInfo(), $args))
			return $Translation['Couldn\'t delete this record'] . (
				!empty($args['error_message']) ?
					'<div class="text-bold">' . strip_tags($args['error_message']) . '</div>'
					: '' 
			);
	}

	// child table: order_details
	$res = sql("SELECT `ProductID` FROM `products` WHERE `ProductID`='{$selected_id}'", $eo);
	$ProductID = db_fetch_row($res);
	$rires = sql("SELECT COUNT(1) FROM `order_details` WHERE `ProductID`='" . makeSafe($ProductID[0]) . "'", $eo);
	$rirow = db_fetch_row($rires);
	$childrenATag = '<a class="alert-link" href="order_details_view.php?filterer_ProductID=' . urlencode($ProductID[0]) . '">%s</a>';
	if($rirow[0] && !$AllowDeleteOfParents && !$skipChecks) {
		$RetMsg = $Translation["couldn't delete"];
		$RetMsg = str_replace('<RelatedRecords>', sprintf($childrenATag, $rirow[0]), $RetMsg);
		$RetMsg = str_replace(['[<TableName>]', '<TableName>'], sprintf($childrenATag, 'order_details'), $RetMsg);
		return $RetMsg;
	} elseif($rirow[0] && $AllowDeleteOfParents && !$skipChecks) {
		$RetMsg = $Translation['confirm delete'];
		$RetMsg = str_replace('<RelatedRecords>', sprintf($childrenATag, $rirow[0]), $RetMsg);
		$RetMsg = str_replace(['[<TableName>]', '<TableName>'], sprintf($childrenATag, 'order_details'), $RetMsg);
		$RetMsg = str_replace('<Delete>', '<input type="button" class="btn btn-danger" value="' . html_attr($Translation['yes']) . '" onClick="window.location = `products_view.php?SelectedID=' . urlencode($selected_id) . '&delete_x=1&confirmed=1&csrf_token=' . urlencode(csrf_token(false, true)) . (Request::val('Embedded') ? '&Embedded=1' : '') . '`;">', $RetMsg);
		$RetMsg = str_replace('<Cancel>', '<input type="button" class="btn btn-success" value="' . html_attr($Translation[ 'no']) . '" onClick="window.location = `products_view.php?SelectedID=' . urlencode($selected_id) . (Request::val('Embedded') ? '&Embedded=1' : '') . '`;">', $RetMsg);
		return $RetMsg;
	}

	// delete file stored in the 'TechSheet' field
	$res = sql("SELECT `TechSheet` FROM `products` WHERE `ProductID`='{$selected_id}'", $eo);
	if($row = @db_fetch_row($res)) {
		if($row[0] != '') {
			@unlink(getUploadDir('') . $row[0]);
		}
	}

	sql("DELETE FROM `products` WHERE `ProductID`='{$selected_id}'", $eo);

	// hook: products_after_delete
	if(function_exists('products_after_delete')) {
		$args = [];
		products_after_delete($selected_id, getMemberInfo(), $args);
	}

	// mm: delete ownership data
	sql("DELETE FROM `membership_userrecords` WHERE `tableName`='products' AND `pkValue`='{$selected_id}'", $eo);
}

function products_update(&$selected_id, &$error_message = '') {
	global $Translation;

	// mm: can member edit record?
	if(!check_record_permission('products', $selected_id, 'edit')) return false;

	$data = [
		'ProductName' => Request::val('ProductName', ''),
		'SupplierID' => Request::lookup('SupplierID', ''),
		'CategoryID' => Request::lookup('CategoryID', ''),
		'QuantityPerUnit' => Request::val('QuantityPerUnit', ''),
		'UnitPrice' => Request::val('UnitPrice', ''),
		'UnitsInStock' => Request::val('UnitsInStock', ''),
		'UnitsOnOrder' => Request::val('UnitsOnOrder', ''),
		'ReorderLevel' => Request::val('ReorderLevel', ''),
		'Discontinued' => Request::checkBox('Discontinued', ''),
		'TechSheet' => Request::fileUpload('TechSheet', [
			'maxSize' => 2048000,
			'types' => 'txt|doc|docx|docm|odt|pdf|rtf',
			'noRename' => false,
			'dir' => '',
			'id' => $selected_id,
			'success' => function($name, $selected_id) {
			},
			'removeOnSuccess' => true,
			'removeOnRequest' => true,
			'remove' => function($selected_id) {
				// delete old file from server
				$oldFile = existing_value('products', 'TechSheet', $selected_id);
				if(!$oldFile) return;

				@unlink(getUploadDir('') . $oldFile);
			},
			'failure' => function($selected_id, $fileRemoved) {
				if($fileRemoved) return '';
				return existing_value('products', 'TechSheet', $selected_id);
			},
		]),
	];

	// get existing values
	$old_data = getRecord('products', $selected_id);
	if(is_array($old_data)) {
		$old_data = array_map('makeSafe', $old_data);
		$old_data['selectedID'] = makeSafe($selected_id);
	}

	$data['selectedID'] = makeSafe($selected_id);

	// hook: products_before_update
	if(function_exists('products_before_update')) {
		$args = ['old_data' => $old_data];
		if(!products_before_update($data, getMemberInfo(), $args)) {
			if(isset($args['error_message'])) $error_message = $args['error_message'];
			return false;
		}
	}

	$set = $data; unset($set['selectedID']);
	foreach ($set as $field => $value) {
		$set[$field] = ($value !== '' && $value !== NULL) ? $value : NULL;
	}

	if(!update(
		'products', 
		backtick_keys_once($set), 
		['`ProductID`' => $selected_id], 
		$error_message
	)) {
		echo $error_message;
		echo '<a href="products_view.php?SelectedID=' . urlencode($selected_id) . "\">{$Translation['< back']}</a>";
		exit;
	}


	update_calc_fields('products', $data['selectedID'], calculated_fields()['products']);

	// hook: products_after_update
	if(function_exists('products_after_update')) {
		if($row = getRecord('products', $data['selectedID'])) $data = array_map('makeSafe', $row);

		$data['selectedID'] = $data['ProductID'];
		$args = ['old_data' => $old_data];
		if(!products_after_update($data, getMemberInfo(), $args)) return;
	}

	// mm: update record update timestamp
	set_record_owner('products', $selected_id);
}

function products_form($selectedId = '', $allowUpdate = true, $allowInsert = true, $allowDelete = true, $separateDV = true, $templateDV = '', $templateDVP = '') {
	// function to return an editable form for a table records
	// and fill it with data of record whose ID is $selectedId. If $selectedId
	// is empty, an empty form is shown, with only an 'Add New'
	// button displayed.

	global $Translation;
	$eo = ['silentErrors' => true];
	$noUploads = $row = $urow = $jsReadOnly = $jsEditable = $lookups = null;
	$noSaveAsCopy = true;
	$hasSelectedId = strlen($selectedId) > 0;

	// mm: get table permissions
	$arrPerm = getTablePermissions('products');
	$allowInsert = ($arrPerm['insert'] ? true : false);
	$allowUpdate = $hasSelectedId && check_record_permission('products', $selectedId, 'edit');
	$allowDelete = $hasSelectedId && check_record_permission('products', $selectedId, 'delete');

	if(!$allowInsert && !$hasSelectedId)
		// no insert permission and no record selected
		// so show access denied error -- except if TVDV: just hide DV
		return $separateDV ? $Translation['tableAccessDenied'] : '';

	if($hasSelectedId && !check_record_permission('products', $selectedId, 'view'))
		return $Translation['tableAccessDenied'];

	// print preview?
	$dvprint = $hasSelectedId && Request::val('dvprint_x') != '';

	$showSaveNew = !$dvprint && ($allowInsert && !$hasSelectedId);
	$showSaveChanges = !$dvprint && $allowUpdate && $hasSelectedId;
	$showDelete = !$dvprint && $allowDelete && $hasSelectedId;
	$showSaveAsCopy = !$dvprint && ($allowInsert && $hasSelectedId && !$noSaveAsCopy);
	$fieldsAreEditable = !$dvprint && (($allowInsert && !$hasSelectedId) || ($allowUpdate && $hasSelectedId) || $showSaveAsCopy);

	$filterer_SupplierID = Request::val('filterer_SupplierID');
	$filterer_CategoryID = Request::val('filterer_CategoryID');

	// populate filterers, starting from children to grand-parents

	// unique random identifier
	$rnd1 = ($dvprint ? rand(1000000, 9999999) : '');
	// combobox: SupplierID
	$combo_SupplierID = new DataCombo;
	// combobox: CategoryID
	$combo_CategoryID = new DataCombo;

	if($hasSelectedId) {
		if(!($row = getRecord('products', $selectedId))) {
			return error_message($Translation['No records found'], 'products_view.php', false);
		}
		$combo_SupplierID->SelectedData = $row['SupplierID'];
		$combo_CategoryID->SelectedData = $row['CategoryID'];
		$urow = $row; /* unsanitized data */
		$row = array_map('safe_html', $row);
	} else {
		$filterField = Request::val('FilterField');
		$filterOperator = Request::val('FilterOperator');
		$filterValue = Request::val('FilterValue');
		$combo_SupplierID->SelectedData = $filterer_SupplierID;
		$combo_CategoryID->SelectedData = $filterer_CategoryID;
	}
	$combo_SupplierID->HTML = '<span id="SupplierID-container' . $rnd1 . '"></span><input type="hidden" name="SupplierID" id="SupplierID' . $rnd1 . '" value="' . html_attr($combo_SupplierID->SelectedData) . '">';
	$combo_SupplierID->MatchText = '<span id="SupplierID-container-readonly' . $rnd1 . '"></span><input type="hidden" name="SupplierID" id="SupplierID' . $rnd1 . '" value="' . html_attr($combo_SupplierID->SelectedData) . '">';
	$combo_CategoryID->HTML = '<span id="CategoryID-container' . $rnd1 . '"></span><input type="hidden" name="CategoryID" id="CategoryID' . $rnd1 . '" value="' . html_attr($combo_CategoryID->SelectedData) . '">';
	$combo_CategoryID->MatchText = '<span id="CategoryID-container-readonly' . $rnd1 . '"></span><input type="hidden" name="CategoryID" id="CategoryID' . $rnd1 . '" value="' . html_attr($combo_CategoryID->SelectedData) . '">';

	ob_start();
	?>

	<script>
		// initial lookup values
		AppGini.current_SupplierID__RAND__ = { text: "", value: "<?php echo addslashes($hasSelectedId ? $urow['SupplierID'] : htmlspecialchars($filterer_SupplierID, ENT_QUOTES)); ?>"};
		AppGini.current_CategoryID__RAND__ = { text: "", value: "<?php echo addslashes($hasSelectedId ? $urow['CategoryID'] : htmlspecialchars($filterer_CategoryID, ENT_QUOTES)); ?>"};

		$j(function() {
			setTimeout(function() {
				if(typeof(SupplierID_reload__RAND__) == 'function') SupplierID_reload__RAND__();
				if(typeof(CategoryID_reload__RAND__) == 'function') CategoryID_reload__RAND__();
			}, 50); /* we need to slightly delay client-side execution of the above code to allow AppGini.ajaxCache to work */
		});
		function SupplierID_reload__RAND__() {
		<?php if($fieldsAreEditable) { ?>

			$j("#SupplierID-container__RAND__").select2({
				/* initial default value */
				initSelection: function(e, c) {
					$j.ajax({
						url: 'ajax_combo.php',
						dataType: 'json',
						data: { id: AppGini.current_SupplierID__RAND__.value, t: 'products', f: 'SupplierID' },
						success: function(resp) {
							c({
								id: resp.results[0].id,
								text: resp.results[0].text
							});
							$j('[name="SupplierID"]').val(resp.results[0].id);
							$j('[id=SupplierID-container-readonly__RAND__]').html('<span class="match-text" id="SupplierID-match-text">' + resp.results[0].text + '</span>');
							if(resp.results[0].id == '<?php echo empty_lookup_value; ?>') { $j('.btn[id=suppliers_view_parent]').hide(); } else { $j('.btn[id=suppliers_view_parent]').show(); }


							if(typeof(SupplierID_update_autofills__RAND__) == 'function') SupplierID_update_autofills__RAND__();
						}
					});
				},
				width: '100%',
				formatNoMatches: function(term) { return '<?php echo addslashes($Translation['No matches found!']); ?>'; },
				minimumResultsForSearch: 5,
				loadMorePadding: 200,
				ajax: {
					url: 'ajax_combo.php',
					dataType: 'json',
					cache: true,
					data: function(term, page) { return { s: term, p: page, t: 'products', f: 'SupplierID' }; },
					results: function(resp, page) { return resp; }
				},
				escapeMarkup: function(str) { return str; }
			}).on('change', function(e) {
				AppGini.current_SupplierID__RAND__.value = e.added.id;
				AppGini.current_SupplierID__RAND__.text = e.added.text;
				$j('[name="SupplierID"]').val(e.added.id);
				if(e.added.id == '<?php echo empty_lookup_value; ?>') { $j('.btn[id=suppliers_view_parent]').hide(); } else { $j('.btn[id=suppliers_view_parent]').show(); }


				if(typeof(SupplierID_update_autofills__RAND__) == 'function') SupplierID_update_autofills__RAND__();
			});

			if(!$j("#SupplierID-container__RAND__").length) {
				$j.ajax({
					url: 'ajax_combo.php',
					dataType: 'json',
					data: { id: AppGini.current_SupplierID__RAND__.value, t: 'products', f: 'SupplierID' },
					success: function(resp) {
						$j('[name="SupplierID"]').val(resp.results[0].id);
						$j('[id=SupplierID-container-readonly__RAND__]').html('<span class="match-text" id="SupplierID-match-text">' + resp.results[0].text + '</span>');
						if(resp.results[0].id == '<?php echo empty_lookup_value; ?>') { $j('.btn[id=suppliers_view_parent]').hide(); } else { $j('.btn[id=suppliers_view_parent]').show(); }

						if(typeof(SupplierID_update_autofills__RAND__) == 'function') SupplierID_update_autofills__RAND__();
					}
				});
			}

		<?php } else { ?>

			$j.ajax({
				url: 'ajax_combo.php',
				dataType: 'json',
				data: { id: AppGini.current_SupplierID__RAND__.value, t: 'products', f: 'SupplierID' },
				success: function(resp) {
					$j('[id=SupplierID-container__RAND__], [id=SupplierID-container-readonly__RAND__]').html('<span class="match-text" id="SupplierID-match-text">' + resp.results[0].text + '</span>');
					if(resp.results[0].id == '<?php echo empty_lookup_value; ?>') { $j('.btn[id=suppliers_view_parent]').hide(); } else { $j('.btn[id=suppliers_view_parent]').show(); }

					if(typeof(SupplierID_update_autofills__RAND__) == 'function') SupplierID_update_autofills__RAND__();
				}
			});
		<?php } ?>

		}
		function CategoryID_reload__RAND__() {
		<?php if($fieldsAreEditable) { ?>

			$j("#CategoryID-container__RAND__").select2({
				/* initial default value */
				initSelection: function(e, c) {
					$j.ajax({
						url: 'ajax_combo.php',
						dataType: 'json',
						data: { id: AppGini.current_CategoryID__RAND__.value, t: 'products', f: 'CategoryID' },
						success: function(resp) {
							c({
								id: resp.results[0].id,
								text: resp.results[0].text
							});
							$j('[name="CategoryID"]').val(resp.results[0].id);
							$j('[id=CategoryID-container-readonly__RAND__]').html('<span class="match-text" id="CategoryID-match-text">' + resp.results[0].text + '</span>');
							if(resp.results[0].id == '<?php echo empty_lookup_value; ?>') { $j('.btn[id=categories_view_parent]').hide(); } else { $j('.btn[id=categories_view_parent]').show(); }


							if(typeof(CategoryID_update_autofills__RAND__) == 'function') CategoryID_update_autofills__RAND__();
						}
					});
				},
				width: '100%',
				formatNoMatches: function(term) { return '<?php echo addslashes($Translation['No matches found!']); ?>'; },
				minimumResultsForSearch: 5,
				loadMorePadding: 200,
				ajax: {
					url: 'ajax_combo.php',
					dataType: 'json',
					cache: true,
					data: function(term, page) { return { s: term, p: page, t: 'products', f: 'CategoryID' }; },
					results: function(resp, page) { return resp; }
				},
				escapeMarkup: function(str) { return str; }
			}).on('change', function(e) {
				AppGini.current_CategoryID__RAND__.value = e.added.id;
				AppGini.current_CategoryID__RAND__.text = e.added.text;
				$j('[name="CategoryID"]').val(e.added.id);
				if(e.added.id == '<?php echo empty_lookup_value; ?>') { $j('.btn[id=categories_view_parent]').hide(); } else { $j('.btn[id=categories_view_parent]').show(); }


				if(typeof(CategoryID_update_autofills__RAND__) == 'function') CategoryID_update_autofills__RAND__();
			});

			if(!$j("#CategoryID-container__RAND__").length) {
				$j.ajax({
					url: 'ajax_combo.php',
					dataType: 'json',
					data: { id: AppGini.current_CategoryID__RAND__.value, t: 'products', f: 'CategoryID' },
					success: function(resp) {
						$j('[name="CategoryID"]').val(resp.results[0].id);
						$j('[id=CategoryID-container-readonly__RAND__]').html('<span class="match-text" id="CategoryID-match-text">' + resp.results[0].text + '</span>');
						if(resp.results[0].id == '<?php echo empty_lookup_value; ?>') { $j('.btn[id=categories_view_parent]').hide(); } else { $j('.btn[id=categories_view_parent]').show(); }

						if(typeof(CategoryID_update_autofills__RAND__) == 'function') CategoryID_update_autofills__RAND__();
					}
				});
			}

		<?php } else { ?>

			$j.ajax({
				url: 'ajax_combo.php',
				dataType: 'json',
				data: { id: AppGini.current_CategoryID__RAND__.value, t: 'products', f: 'CategoryID' },
				success: function(resp) {
					$j('[id=CategoryID-container__RAND__], [id=CategoryID-container-readonly__RAND__]').html('<span class="match-text" id="CategoryID-match-text">' + resp.results[0].text + '</span>');
					if(resp.results[0].id == '<?php echo empty_lookup_value; ?>') { $j('.btn[id=categories_view_parent]').hide(); } else { $j('.btn[id=categories_view_parent]').show(); }

					if(typeof(CategoryID_update_autofills__RAND__) == 'function') CategoryID_update_autofills__RAND__();
				}
			});
		<?php } ?>

		}
	</script>
	<?php

	$lookups = str_replace('__RAND__', $rnd1, ob_get_clean());


	// code for template based detail view forms

	// open the detail view template
	if($dvprint) {
		$template_file = is_file("./{$templateDVP}") ? "./{$templateDVP}" : './templates/products_templateDVP.html';
		$templateCode = @file_get_contents($template_file);
	} else {
		$template_file = is_file("./{$templateDV}") ? "./{$templateDV}" : './templates/products_templateDV.html';
		$templateCode = @file_get_contents($template_file);
	}

	// process form title
	$templateCode = str_replace('<%%DETAIL_VIEW_TITLE%%>', 'Detail View', $templateCode);
	$templateCode = str_replace('<%%RND1%%>', $rnd1, $templateCode);
	$templateCode = str_replace('<%%EMBEDDED%%>', (Request::val('Embedded') ? 'Embedded=1' : ''), $templateCode);
	// process buttons
	if($showSaveNew) {
		$templateCode = str_replace('<%%INSERT_BUTTON%%>', '<button type="submit" class="btn btn-success" id="insert" name="insert_x" value="1"><i class="glyphicon glyphicon-plus-sign"></i> ' . $Translation['Save New'] . '</button>', $templateCode);
	} elseif($showSaveAsCopy) {
		$templateCode = str_replace('<%%INSERT_BUTTON%%>', '<button type="submit" class="btn btn-default" id="insert" name="insert_x" value="1"><i class="glyphicon glyphicon-plus-sign"></i> ' . $Translation['Save As Copy'] . '</button>', $templateCode);
	} else {
		$templateCode = str_replace('<%%INSERT_BUTTON%%>', '', $templateCode);
	}

	// 'Back' button action
	if(Request::val('Embedded')) {
		$backAction = 'AppGini.closeParentModal(); return false;';
	} else {
		$backAction = 'return true;';
	}

	if($hasSelectedId) {
		if(!Request::val('Embedded')) $templateCode = str_replace('<%%DVPRINT_BUTTON%%>', '<button type="submit" class="btn btn-default" id="dvprint" name="dvprint_x" value="1" title="' . html_attr($Translation['Print Preview']) . '"><i class="glyphicon glyphicon-print"></i> ' . $Translation['Print Preview'] . '</button>', $templateCode);
		if($allowUpdate)
			$templateCode = str_replace('<%%UPDATE_BUTTON%%>', '<button type="submit" class="btn btn-success btn-lg" id="update" name="update_x" value="1" title="' . html_attr($Translation['Save Changes']) . '"><i class="glyphicon glyphicon-ok"></i> ' . $Translation['Save Changes'] . '</button>', $templateCode);
		else
			$templateCode = str_replace('<%%UPDATE_BUTTON%%>', '', $templateCode);

		if($allowDelete)
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
			$allowInsert
			&& !$allowUpdate && !$allowDelete && !$arrPerm['view']
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
	if(!$fieldsAreEditable) {
		$jsReadOnly = '';
		$jsReadOnly .= "\t\$j('#ProductName').replaceWith('<div class=\"form-control-static\" id=\"ProductName\">' + (\$j('#ProductName').val() || '') + '</div>');\n";
		$jsReadOnly .= "\t\$j('#SupplierID').prop('disabled', true).css({ color: '#555', backgroundColor: '#fff' });\n";
		$jsReadOnly .= "\t\$j('#SupplierID_caption').prop('disabled', true).css({ color: '#555', backgroundColor: 'white' });\n";
		$jsReadOnly .= "\t\$j('#CategoryID').prop('disabled', true).css({ color: '#555', backgroundColor: '#fff' });\n";
		$jsReadOnly .= "\t\$j('#CategoryID_caption').prop('disabled', true).css({ color: '#555', backgroundColor: 'white' });\n";
		$jsReadOnly .= "\t\$j('#QuantityPerUnit').replaceWith('<div class=\"form-control-static\" id=\"QuantityPerUnit\">' + (\$j('#QuantityPerUnit').val() || '') + '</div>');\n";
		$jsReadOnly .= "\t\$j('#UnitPrice').replaceWith('<div class=\"form-control-static\" id=\"UnitPrice\">' + (\$j('#UnitPrice').val() || '') + '</div>');\n";
		$jsReadOnly .= "\t\$j('#UnitsInStock').replaceWith('<div class=\"form-control-static\" id=\"UnitsInStock\">' + (\$j('#UnitsInStock').val() || '') + '</div>');\n";
		$jsReadOnly .= "\t\$j('#UnitsOnOrder').replaceWith('<div class=\"form-control-static\" id=\"UnitsOnOrder\">' + (\$j('#UnitsOnOrder').val() || '') + '</div>');\n";
		$jsReadOnly .= "\t\$j('#ReorderLevel').replaceWith('<div class=\"form-control-static\" id=\"ReorderLevel\">' + (\$j('#ReorderLevel').val() || '') + '</div>');\n";
		$jsReadOnly .= "\t\$j('#Discontinued').prop('disabled', true);\n";
		$jsReadOnly .= "\t\$j('#TechSheet').parent().replaceWith(`<div class=\"form-control-static\" id=\"TechSheet\">\${\$j('#TechSheet').val() || ''}\${\$j('#TechSheet').val() ? '<a target=\"_blank\" class=\"hspacer-lg\" href=\"' + \$j('#TechSheet').val() + '\" target=\"_blank\"><i class=\"glyphicon glyphicon-globe\"></i></a>' : ''}</div>`);\n";
		$jsReadOnly .= "\t\$j('.select2-container').hide();\n";

		$noUploads = true;
	} else {
		// temporarily disable form change handler till time and datetime pickers are enabled
		$jsEditable = "\t\$j('form').eq(0).data('already_changed', true);";
		$jsEditable .= "\t\$j('form').eq(0).data('already_changed', false);"; // re-enable form change handler
	}

	// process combos
	$templateCode = str_replace('<%%COMBO(SupplierID)%%>', $combo_SupplierID->HTML, $templateCode);
	$templateCode = str_replace('<%%COMBOTEXT(SupplierID)%%>', $combo_SupplierID->MatchText, $templateCode);
	$templateCode = str_replace('<%%URLCOMBOTEXT(SupplierID)%%>', urlencode($combo_SupplierID->MatchText), $templateCode);
	$templateCode = str_replace('<%%COMBO(CategoryID)%%>', $combo_CategoryID->HTML, $templateCode);
	$templateCode = str_replace('<%%COMBOTEXT(CategoryID)%%>', $combo_CategoryID->MatchText, $templateCode);
	$templateCode = str_replace('<%%URLCOMBOTEXT(CategoryID)%%>', urlencode($combo_CategoryID->MatchText), $templateCode);

	/* lookup fields array: 'lookup field name' => ['parent table name', 'lookup field caption'] */
	$lookup_fields = ['SupplierID' => ['suppliers', 'Supplier'], 'CategoryID' => ['categories', 'Category'], ];
	foreach($lookup_fields as $luf => $ptfc) {
		$pt_perm = getTablePermissions($ptfc[0]);

		// process foreign key links
		if(($pt_perm['view'] && isDetailViewEnabled($ptfc[0])) || $pt_perm['edit']) {
			$templateCode = str_replace("<%%PLINK({$luf})%%>", '<button type="button" class="btn btn-default view_parent" id="' . $ptfc[0] . '_view_parent" title="' . html_attr($Translation['View'] . ' ' . $ptfc[1]) . '"><i class="glyphicon glyphicon-eye-open"></i></button>', $templateCode);
		}

		// if user has insert permission to parent table of a lookup field, put an add new button
		if($pt_perm['insert'] /* && !Request::val('Embedded')*/) {
			$templateCode = str_replace("<%%ADDNEW({$ptfc[0]})%%>", '<button type="button" class="btn btn-default add_new_parent" id="' . $ptfc[0] . '_add_new" title="' . html_attr($Translation['Add New'] . ' ' . $ptfc[1]) . '"><i class="glyphicon glyphicon-plus text-success"></i></button>', $templateCode);
		}
	}

	// process images
	$templateCode = str_replace('<%%UPLOADFILE(ProductID)%%>', '', $templateCode);
	$templateCode = str_replace('<%%UPLOADFILE(ProductName)%%>', '', $templateCode);
	$templateCode = str_replace('<%%UPLOADFILE(SupplierID)%%>', '', $templateCode);
	$templateCode = str_replace('<%%UPLOADFILE(CategoryID)%%>', '', $templateCode);
	$templateCode = str_replace('<%%UPLOADFILE(QuantityPerUnit)%%>', '', $templateCode);
	$templateCode = str_replace('<%%UPLOADFILE(UnitPrice)%%>', '', $templateCode);
	$templateCode = str_replace('<%%UPLOADFILE(UnitsInStock)%%>', '', $templateCode);
	$templateCode = str_replace('<%%UPLOADFILE(UnitsOnOrder)%%>', '', $templateCode);
	$templateCode = str_replace('<%%UPLOADFILE(ReorderLevel)%%>', '', $templateCode);
	$templateCode = str_replace('<%%UPLOADFILE(Discontinued)%%>', '', $templateCode);
	$templateCode = str_replace('<%%UPLOADFILE(TotalSales)%%>', '', $templateCode);
	$templateCode = str_replace('<%%UPLOADFILE(TechSheet)%%>', ($noUploads ? '' : "<div>{$Translation['upload image']}</div>" . '<input type="file" name="TechSheet" id="TechSheet" data-filetypes="txt|doc|docx|docm|odt|pdf|rtf" data-maxsize="2048000" style="max-width: calc(100% - 1.5rem);" accept=".txt,.doc,.docx,.docm,.odt,.pdf,.rtf">' . '<i class="text-danger clear-upload hidden pull-right" style="margin-top: -.1em; font-size: large;">&times;</i>'), $templateCode);
	if($allowUpdate && $row['TechSheet'] != '') {
		$templateCode = str_replace('<%%REMOVEFILE(TechSheet)%%>', '<input type="checkbox" name="TechSheet_remove" id="TechSheet_remove" value="1"> <label for="TechSheet_remove" style="color: red; font-weight: bold;">'.$Translation['remove image'].'</label>', $templateCode);
	} else {
		$templateCode = str_replace('<%%REMOVEFILE(TechSheet)%%>', '', $templateCode);
	}

	// process values
	if($hasSelectedId) {
		if( $dvprint) $templateCode = str_replace('<%%VALUE(ProductID)%%>', safe_html($urow['ProductID']), $templateCode);
		if(!$dvprint) $templateCode = str_replace('<%%VALUE(ProductID)%%>', html_attr($row['ProductID']), $templateCode);
		$templateCode = str_replace('<%%URLVALUE(ProductID)%%>', urlencode($urow['ProductID']), $templateCode);
		if( $dvprint) $templateCode = str_replace('<%%VALUE(ProductName)%%>', safe_html($urow['ProductName']), $templateCode);
		if(!$dvprint) $templateCode = str_replace('<%%VALUE(ProductName)%%>', html_attr($row['ProductName']), $templateCode);
		$templateCode = str_replace('<%%URLVALUE(ProductName)%%>', urlencode($urow['ProductName']), $templateCode);
		if( $dvprint) $templateCode = str_replace('<%%VALUE(SupplierID)%%>', safe_html($urow['SupplierID']), $templateCode);
		if(!$dvprint) $templateCode = str_replace('<%%VALUE(SupplierID)%%>', html_attr($row['SupplierID']), $templateCode);
		$templateCode = str_replace('<%%URLVALUE(SupplierID)%%>', urlencode($urow['SupplierID']), $templateCode);
		if( $dvprint) $templateCode = str_replace('<%%VALUE(CategoryID)%%>', safe_html($urow['CategoryID']), $templateCode);
		if(!$dvprint) $templateCode = str_replace('<%%VALUE(CategoryID)%%>', html_attr($row['CategoryID']), $templateCode);
		$templateCode = str_replace('<%%URLVALUE(CategoryID)%%>', urlencode($urow['CategoryID']), $templateCode);
		if( $dvprint) $templateCode = str_replace('<%%VALUE(QuantityPerUnit)%%>', safe_html($urow['QuantityPerUnit']), $templateCode);
		if(!$dvprint) $templateCode = str_replace('<%%VALUE(QuantityPerUnit)%%>', html_attr($row['QuantityPerUnit']), $templateCode);
		$templateCode = str_replace('<%%URLVALUE(QuantityPerUnit)%%>', urlencode($urow['QuantityPerUnit']), $templateCode);
		if( $dvprint) $templateCode = str_replace('<%%VALUE(UnitPrice)%%>', safe_html($urow['UnitPrice']), $templateCode);
		if(!$dvprint) $templateCode = str_replace('<%%VALUE(UnitPrice)%%>', html_attr($row['UnitPrice']), $templateCode);
		$templateCode = str_replace('<%%URLVALUE(UnitPrice)%%>', urlencode($urow['UnitPrice']), $templateCode);
		if( $dvprint) $templateCode = str_replace('<%%VALUE(UnitsInStock)%%>', safe_html($urow['UnitsInStock']), $templateCode);
		if(!$dvprint) $templateCode = str_replace('<%%VALUE(UnitsInStock)%%>', html_attr($row['UnitsInStock']), $templateCode);
		$templateCode = str_replace('<%%URLVALUE(UnitsInStock)%%>', urlencode($urow['UnitsInStock']), $templateCode);
		if( $dvprint) $templateCode = str_replace('<%%VALUE(UnitsOnOrder)%%>', safe_html($urow['UnitsOnOrder']), $templateCode);
		if(!$dvprint) $templateCode = str_replace('<%%VALUE(UnitsOnOrder)%%>', html_attr($row['UnitsOnOrder']), $templateCode);
		$templateCode = str_replace('<%%URLVALUE(UnitsOnOrder)%%>', urlencode($urow['UnitsOnOrder']), $templateCode);
		if( $dvprint) $templateCode = str_replace('<%%VALUE(ReorderLevel)%%>', safe_html($urow['ReorderLevel']), $templateCode);
		if(!$dvprint) $templateCode = str_replace('<%%VALUE(ReorderLevel)%%>', html_attr($row['ReorderLevel']), $templateCode);
		$templateCode = str_replace('<%%URLVALUE(ReorderLevel)%%>', urlencode($urow['ReorderLevel']), $templateCode);
		$templateCode = str_replace('<%%CHECKED(Discontinued)%%>', ($row['Discontinued'] ? "checked" : ""), $templateCode);
		$templateCode = str_replace('<%%VALUE(TotalSales)%%>', safe_html($urow['TotalSales']), $templateCode);
		$templateCode = str_replace('<%%URLVALUE(TotalSales)%%>', urlencode($urow['TotalSales']), $templateCode);
		if( $dvprint) $templateCode = str_replace('<%%VALUE(TechSheet)%%>', safe_html($urow['TechSheet']), $templateCode);
		if(!$dvprint) $templateCode = str_replace('<%%VALUE(TechSheet)%%>', html_attr($row['TechSheet']), $templateCode);
		$templateCode = str_replace('<%%URLVALUE(TechSheet)%%>', urlencode($urow['TechSheet']), $templateCode);
	} else {
		$templateCode = str_replace('<%%VALUE(ProductID)%%>', '', $templateCode);
		$templateCode = str_replace('<%%URLVALUE(ProductID)%%>', urlencode(''), $templateCode);
		$templateCode = str_replace('<%%VALUE(ProductName)%%>', '', $templateCode);
		$templateCode = str_replace('<%%URLVALUE(ProductName)%%>', urlencode(''), $templateCode);
		$templateCode = str_replace('<%%VALUE(SupplierID)%%>', '', $templateCode);
		$templateCode = str_replace('<%%URLVALUE(SupplierID)%%>', urlencode(''), $templateCode);
		$templateCode = str_replace('<%%VALUE(CategoryID)%%>', '', $templateCode);
		$templateCode = str_replace('<%%URLVALUE(CategoryID)%%>', urlencode(''), $templateCode);
		$templateCode = str_replace('<%%VALUE(QuantityPerUnit)%%>', '', $templateCode);
		$templateCode = str_replace('<%%URLVALUE(QuantityPerUnit)%%>', urlencode(''), $templateCode);
		$templateCode = str_replace('<%%VALUE(UnitPrice)%%>', '0', $templateCode);
		$templateCode = str_replace('<%%URLVALUE(UnitPrice)%%>', urlencode('0'), $templateCode);
		$templateCode = str_replace('<%%VALUE(UnitsInStock)%%>', '0', $templateCode);
		$templateCode = str_replace('<%%URLVALUE(UnitsInStock)%%>', urlencode('0'), $templateCode);
		$templateCode = str_replace('<%%VALUE(UnitsOnOrder)%%>', '0', $templateCode);
		$templateCode = str_replace('<%%URLVALUE(UnitsOnOrder)%%>', urlencode('0'), $templateCode);
		$templateCode = str_replace('<%%VALUE(ReorderLevel)%%>', '0', $templateCode);
		$templateCode = str_replace('<%%URLVALUE(ReorderLevel)%%>', urlencode('0'), $templateCode);
		$templateCode = str_replace('<%%CHECKED(Discontinued)%%>', '', $templateCode);
		$templateCode = str_replace('<%%VALUE(TotalSales)%%>', '', $templateCode);
		$templateCode = str_replace('<%%URLVALUE(TotalSales)%%>', urlencode(''), $templateCode);
		$templateCode = str_replace('<%%VALUE(TechSheet)%%>', '', $templateCode);
		$templateCode = str_replace('<%%URLVALUE(TechSheet)%%>', urlencode(''), $templateCode);
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

		if(!$hasSelectedId) {
			$templateCode.="\n\tif(document.getElementById('TechSheetEdit')) { document.getElementById('TechSheetEdit').style.display='inline'; }";
			$templateCode.="\n\tif(document.getElementById('TechSheetEditLink')) { document.getElementById('TechSheetEditLink').style.display='none'; }";
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
	$rdata = $jdata = get_defaults('products');
	if($hasSelectedId) {
		$jdata = get_joined_record('products', $selectedId);
		if($jdata === false) $jdata = get_defaults('products');
		$rdata = $row;
	}
	$templateCode .= loadView('products-ajax-cache', ['rdata' => $rdata, 'jdata' => $jdata]);

	// hook: products_dv
	if(function_exists('products_dv')) {
		$args = [];
		products_dv(($hasSelectedId ? $selectedId : FALSE), getMemberInfo(), $templateCode, $args);
	}

	return $templateCode;
}