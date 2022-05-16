<?php

// Data functions (insert, update, delete, form) for table order_details

// This script and data application were generated by AppGini 22.13
// Download AppGini for free from https://bigprof.com/appgini/download/

function order_details_insert(&$error_message = '') {
	global $Translation;

	// mm: can member insert record?
	$arrPerm = getTablePermissions('order_details');
	if(!$arrPerm['insert']) return false;

	$data = [
		'OrderID' => Request::lookup('OrderID', '0'),
		'ProductID' => Request::lookup('ProductID', '0'),
		'Category' => Request::lookup('ProductID'),
		'CatalogPrice' => Request::lookup('ProductID'),
		'UnitsInStock' => Request::lookup('ProductID'),
		'UnitPrice' => Request::val('UnitPrice', '0'),
		'Quantity' => Request::val('Quantity', '1'),
		'Discount' => Request::val('Discount', '0'),
	];


	// hook: order_details_before_insert
	if(function_exists('order_details_before_insert')) {
		$args = [];
		if(!order_details_before_insert($data, getMemberInfo(), $args)) {
			if(isset($args['error_message'])) $error_message = $args['error_message'];
			return false;
		}
	}

	$error = '';
	// set empty fields to NULL
	$data = array_map(function($v) { return ($v === '' ? NULL : $v); }, $data);
	insert('order_details', backtick_keys_once($data), $error);
	if($error) {
		$error_message = $error;
		return false;
	}

	$recID = db_insert_id(db_link());

	update_calc_fields('order_details', $recID, calculated_fields()['order_details']);

	// hook: order_details_after_insert
	if(function_exists('order_details_after_insert')) {
		$res = sql("SELECT * FROM `order_details` WHERE `odID`='" . makeSafe($recID, false) . "' LIMIT 1", $eo);
		if($row = db_fetch_assoc($res)) {
			$data = array_map('makeSafe', $row);
		}
		$data['selectedID'] = makeSafe($recID, false);
		$args = [];
		if(!order_details_after_insert($data, getMemberInfo(), $args)) { return $recID; }
	}

	// mm: save ownership data
	set_record_owner('order_details', $recID, getLoggedMemberID());

	// if this record is a copy of another record, copy children if applicable
	if(strlen(Request::val('SelectedID'))) order_details_copy_children($recID, Request::val('SelectedID'));

	return $recID;
}

function order_details_copy_children($destination_id, $source_id) {
	global $Translation;
	$requests = []; // array of curl handlers for launching insert requests
	$eo = ['silentErrors' => true];
	$safe_sid = makeSafe($source_id);

	// launch requests, asynchronously
	curl_batch($requests);
}

function order_details_delete($selected_id, $AllowDeleteOfParents = false, $skipChecks = false) {
	// insure referential integrity ...
	global $Translation;
	$selected_id = makeSafe($selected_id);

	// mm: can member delete record?
	if(!check_record_permission('order_details', $selected_id, 'delete')) {
		return $Translation['You don\'t have enough permissions to delete this record'];
	}

	// hook: order_details_before_delete
	if(function_exists('order_details_before_delete')) {
		$args = [];
		if(!order_details_before_delete($selected_id, $skipChecks, getMemberInfo(), $args))
			return $Translation['Couldn\'t delete this record'] . (
				!empty($args['error_message']) ?
					'<div class="text-bold">' . strip_tags($args['error_message']) . '</div>'
					: '' 
			);
	}

	sql("DELETE FROM `order_details` WHERE `odID`='{$selected_id}'", $eo);

	// hook: order_details_after_delete
	if(function_exists('order_details_after_delete')) {
		$args = [];
		order_details_after_delete($selected_id, getMemberInfo(), $args);
	}

	// mm: delete ownership data
	sql("DELETE FROM `membership_userrecords` WHERE `tableName`='order_details' AND `pkValue`='{$selected_id}'", $eo);
}

function order_details_update(&$selected_id, &$error_message = '') {
	global $Translation;

	// mm: can member edit record?
	if(!check_record_permission('order_details', $selected_id, 'edit')) return false;

	$data = [
		'OrderID' => Request::lookup('OrderID', ''),
		'ProductID' => Request::lookup('ProductID', ''),
		'Category' => Request::lookup('ProductID'),
		'CatalogPrice' => Request::lookup('ProductID'),
		'UnitsInStock' => Request::lookup('ProductID'),
		'UnitPrice' => Request::val('UnitPrice', ''),
		'Quantity' => Request::val('Quantity', ''),
		'Discount' => Request::val('Discount', ''),
	];

	// get existing values
	$old_data = getRecord('order_details', $selected_id);
	if(is_array($old_data)) {
		$old_data = array_map('makeSafe', $old_data);
		$old_data['selectedID'] = makeSafe($selected_id);
	}

	$data['selectedID'] = makeSafe($selected_id);

	// hook: order_details_before_update
	if(function_exists('order_details_before_update')) {
		$args = ['old_data' => $old_data];
		if(!order_details_before_update($data, getMemberInfo(), $args)) {
			if(isset($args['error_message'])) $error_message = $args['error_message'];
			return false;
		}
	}

	$set = $data; unset($set['selectedID']);
	foreach ($set as $field => $value) {
		$set[$field] = ($value !== '' && $value !== NULL) ? $value : NULL;
	}

	if(!update(
		'order_details', 
		backtick_keys_once($set), 
		['`odID`' => $selected_id], 
		$error_message
	)) {
		echo $error_message;
		echo '<a href="order_details_view.php?SelectedID=' . urlencode($selected_id) . "\">{$Translation['< back']}</a>";
		exit;
	}


	$eo = ['silentErrors' => true];

	update_calc_fields('order_details', $data['selectedID'], calculated_fields()['order_details']);

	// hook: order_details_after_update
	if(function_exists('order_details_after_update')) {
		$res = sql("SELECT * FROM `order_details` WHERE `odID`='{$data['selectedID']}' LIMIT 1", $eo);
		if($row = db_fetch_assoc($res)) $data = array_map('makeSafe', $row);

		$data['selectedID'] = $data['odID'];
		$args = ['old_data' => $old_data];
		if(!order_details_after_update($data, getMemberInfo(), $args)) return;
	}

	// mm: update ownership data
	sql("UPDATE `membership_userrecords` SET `dateUpdated`='" . time() . "' WHERE `tableName`='order_details' AND `pkValue`='" . makeSafe($selected_id) . "'", $eo);
}

function order_details_form($selected_id = '', $AllowUpdate = 1, $AllowInsert = 1, $AllowDelete = 1, $separateDV = 0, $TemplateDV = '', $TemplateDVP = '') {
	// function to return an editable form for a table records
	// and fill it with data of record whose ID is $selected_id. If $selected_id
	// is empty, an empty form is shown, with only an 'Add New'
	// button displayed.

	global $Translation;
	$eo = ['silentErrors' => true];
	$noUploads = null;
	$row = $urow = $jsReadOnly = $jsEditable = $lookups = null;

	$noSaveAsCopy = false;

	// mm: get table permissions
	$arrPerm = getTablePermissions('order_details');
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

	$filterer_OrderID = Request::val('filterer_OrderID');
	$filterer_ProductID = Request::val('filterer_ProductID');

	// populate filterers, starting from children to grand-parents

	// unique random identifier
	$rnd1 = ($dvprint ? rand(1000000, 9999999) : '');
	// combobox: OrderID
	$combo_OrderID = new DataCombo;
	// combobox: ProductID
	$combo_ProductID = new DataCombo;

	if($selected_id) {
		// mm: check member permissions
		if(!$arrPerm['view']) return $Translation['tableAccessDenied'];

		// mm: who is the owner?
		$ownerGroupID = sqlValue("SELECT `groupID` FROM `membership_userrecords` WHERE `tableName`='order_details' AND `pkValue`='" . makeSafe($selected_id) . "'");
		$ownerMemberID = sqlValue("SELECT LCASE(`memberID`) FROM `membership_userrecords` WHERE `tableName`='order_details' AND `pkValue`='" . makeSafe($selected_id) . "'");

		if($arrPerm['view'] == 1 && getLoggedMemberID() != $ownerMemberID) return $Translation['tableAccessDenied'];
		if($arrPerm['view'] == 2 && getLoggedGroupID() != $ownerGroupID) return $Translation['tableAccessDenied'];

		// can edit?
		$AllowUpdate = 0;
		if(($arrPerm['edit'] == 1 && $ownerMemberID == getLoggedMemberID()) || ($arrPerm['edit'] == 2 && $ownerGroupID == getLoggedGroupID()) || $arrPerm['edit'] == 3) {
			$AllowUpdate = 1;
		}

		$res = sql("SELECT * FROM `order_details` WHERE `odID`='" . makeSafe($selected_id) . "'", $eo);
		if(!($row = db_fetch_array($res))) {
			return error_message($Translation['No records found'], 'order_details_view.php', false);
		}
		$combo_OrderID->SelectedData = $row['OrderID'];
		$combo_ProductID->SelectedData = $row['ProductID'];
		$urow = $row; /* unsanitized data */
		$row = array_map('safe_html', $row);
	} else {
		$filterField = Request::val('FilterField');
		$filterOperator = Request::val('FilterOperator');
		$filterValue = Request::val('FilterValue');
		$combo_OrderID->SelectedData = $filterer_OrderID;
		$combo_ProductID->SelectedData = $filterer_ProductID;
	}
	$combo_OrderID->HTML = '<span id="OrderID-container' . $rnd1 . '"></span><input type="hidden" name="OrderID" id="OrderID' . $rnd1 . '" value="' . html_attr($combo_OrderID->SelectedData) . '">';
	$combo_OrderID->MatchText = '<span id="OrderID-container-readonly' . $rnd1 . '"></span><input type="hidden" name="OrderID" id="OrderID' . $rnd1 . '" value="' . html_attr($combo_OrderID->SelectedData) . '">';
	$combo_ProductID->HTML = '<span id="ProductID-container' . $rnd1 . '"></span><input type="hidden" name="ProductID" id="ProductID' . $rnd1 . '" value="' . html_attr($combo_ProductID->SelectedData) . '">';
	$combo_ProductID->MatchText = '<span id="ProductID-container-readonly' . $rnd1 . '"></span><input type="hidden" name="ProductID" id="ProductID' . $rnd1 . '" value="' . html_attr($combo_ProductID->SelectedData) . '">';

	ob_start();
	?>

	<script>
		// initial lookup values
		AppGini.current_OrderID__RAND__ = { text: "<?php echo ($selected_id ? '' : '0'); ?>", value: "<?php echo addslashes($selected_id ? $urow['OrderID'] : htmlspecialchars($filterer_OrderID, ENT_QUOTES)); ?>"};
		AppGini.current_ProductID__RAND__ = { text: "<?php echo ($selected_id ? '' : '0'); ?>", value: "<?php echo addslashes($selected_id ? $urow['ProductID'] : htmlspecialchars($filterer_ProductID, ENT_QUOTES)); ?>"};

		jQuery(function() {
			setTimeout(function() {
				if(typeof(OrderID_reload__RAND__) == 'function') OrderID_reload__RAND__();
				if(typeof(ProductID_reload__RAND__) == 'function') ProductID_reload__RAND__();
			}, 50); /* we need to slightly delay client-side execution of the above code to allow AppGini.ajaxCache to work */
		});
		function OrderID_reload__RAND__() {
		<?php if(($AllowUpdate || $AllowInsert) && !$dvprint) { ?>

			$j("#OrderID-container__RAND__").select2({
				/* initial default value */
				initSelection: function(e, c) {
					$j.ajax({
						url: 'ajax_combo.php',
						dataType: 'json',
						<?php if(!$selected_id && !$filterer_OrderID) { ?>
							data: { text: '0', t: 'order_details', f: 'OrderID' },
						<?php } else { ?>
							data: { id: AppGini.current_OrderID__RAND__.value, t: 'order_details', f: 'OrderID' },
						<?php } ?>

						success: function(resp) {
							c({
								id: resp.results[0].id,
								text: resp.results[0].text
							});
							$j('[name="OrderID"]').val(resp.results[0].id);
							$j('[id=OrderID-container-readonly__RAND__]').html('<span class="match-text" id="OrderID-match-text">' + resp.results[0].text + '</span>');
							if(resp.results[0].id == '<?php echo empty_lookup_value; ?>') { $j('.btn[id=orders_view_parent]').hide(); } else { $j('.btn[id=orders_view_parent]').show(); }


							if(typeof(OrderID_update_autofills__RAND__) == 'function') OrderID_update_autofills__RAND__();
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
					data: function(term, page) { return { s: term, p: page, t: 'order_details', f: 'OrderID' }; },
					results: function(resp, page) { return resp; }
				},
				escapeMarkup: function(str) { return str; }
			}).on('change', function(e) {
				AppGini.current_OrderID__RAND__.value = e.added.id;
				AppGini.current_OrderID__RAND__.text = e.added.text;
				$j('[name="OrderID"]').val(e.added.id);
				if(e.added.id == '<?php echo empty_lookup_value; ?>') { $j('.btn[id=orders_view_parent]').hide(); } else { $j('.btn[id=orders_view_parent]').show(); }


				if(typeof(OrderID_update_autofills__RAND__) == 'function') OrderID_update_autofills__RAND__();
			});

			if(!$j("#OrderID-container__RAND__").length) {
				$j.ajax({
					url: 'ajax_combo.php',
					dataType: 'json',
					data: { id: AppGini.current_OrderID__RAND__.value, t: 'order_details', f: 'OrderID' },
					success: function(resp) {
						$j('[name="OrderID"]').val(resp.results[0].id);
						$j('[id=OrderID-container-readonly__RAND__]').html('<span class="match-text" id="OrderID-match-text">' + resp.results[0].text + '</span>');
						if(resp.results[0].id == '<?php echo empty_lookup_value; ?>') { $j('.btn[id=orders_view_parent]').hide(); } else { $j('.btn[id=orders_view_parent]').show(); }

						if(typeof(OrderID_update_autofills__RAND__) == 'function') OrderID_update_autofills__RAND__();
					}
				});
			}

		<?php } else { ?>

			$j.ajax({
				url: 'ajax_combo.php',
				dataType: 'json',
				data: { id: AppGini.current_OrderID__RAND__.value, t: 'order_details', f: 'OrderID' },
				success: function(resp) {
					$j('[id=OrderID-container__RAND__], [id=OrderID-container-readonly__RAND__]').html('<span class="match-text" id="OrderID-match-text">' + resp.results[0].text + '</span>');
					if(resp.results[0].id == '<?php echo empty_lookup_value; ?>') { $j('.btn[id=orders_view_parent]').hide(); } else { $j('.btn[id=orders_view_parent]').show(); }

					if(typeof(OrderID_update_autofills__RAND__) == 'function') OrderID_update_autofills__RAND__();
				}
			});
		<?php } ?>

		}
		function ProductID_reload__RAND__() {
		<?php if(($AllowUpdate || $AllowInsert) && !$dvprint) { ?>

			$j("#ProductID-container__RAND__").select2({
				/* initial default value */
				initSelection: function(e, c) {
					$j.ajax({
						url: 'ajax_combo.php',
						dataType: 'json',
						<?php if(!$selected_id && !$filterer_ProductID) { ?>
							data: { text: '0', t: 'order_details', f: 'ProductID' },
						<?php } else { ?>
							data: { id: AppGini.current_ProductID__RAND__.value, t: 'order_details', f: 'ProductID' },
						<?php } ?>

						success: function(resp) {
							c({
								id: resp.results[0].id,
								text: resp.results[0].text
							});
							$j('[name="ProductID"]').val(resp.results[0].id);
							$j('[id=ProductID-container-readonly__RAND__]').html('<span class="match-text" id="ProductID-match-text">' + resp.results[0].text + '</span>');
							if(resp.results[0].id == '<?php echo empty_lookup_value; ?>') { $j('.btn[id=products_view_parent]').hide(); } else { $j('.btn[id=products_view_parent]').show(); }


							if(typeof(ProductID_update_autofills__RAND__) == 'function') ProductID_update_autofills__RAND__();
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
					data: function(term, page) { return { s: term, p: page, t: 'order_details', f: 'ProductID' }; },
					results: function(resp, page) { return resp; }
				},
				escapeMarkup: function(str) { return str; }
			}).on('change', function(e) {
				AppGini.current_ProductID__RAND__.value = e.added.id;
				AppGini.current_ProductID__RAND__.text = e.added.text;
				$j('[name="ProductID"]').val(e.added.id);
				if(e.added.id == '<?php echo empty_lookup_value; ?>') { $j('.btn[id=products_view_parent]').hide(); } else { $j('.btn[id=products_view_parent]').show(); }


				if(typeof(ProductID_update_autofills__RAND__) == 'function') ProductID_update_autofills__RAND__();
			});

			if(!$j("#ProductID-container__RAND__").length) {
				$j.ajax({
					url: 'ajax_combo.php',
					dataType: 'json',
					data: { id: AppGini.current_ProductID__RAND__.value, t: 'order_details', f: 'ProductID' },
					success: function(resp) {
						$j('[name="ProductID"]').val(resp.results[0].id);
						$j('[id=ProductID-container-readonly__RAND__]').html('<span class="match-text" id="ProductID-match-text">' + resp.results[0].text + '</span>');
						if(resp.results[0].id == '<?php echo empty_lookup_value; ?>') { $j('.btn[id=products_view_parent]').hide(); } else { $j('.btn[id=products_view_parent]').show(); }

						if(typeof(ProductID_update_autofills__RAND__) == 'function') ProductID_update_autofills__RAND__();
					}
				});
			}

		<?php } else { ?>

			$j.ajax({
				url: 'ajax_combo.php',
				dataType: 'json',
				data: { id: AppGini.current_ProductID__RAND__.value, t: 'order_details', f: 'ProductID' },
				success: function(resp) {
					$j('[id=ProductID-container__RAND__], [id=ProductID-container-readonly__RAND__]').html('<span class="match-text" id="ProductID-match-text">' + resp.results[0].text + '</span>');
					if(resp.results[0].id == '<?php echo empty_lookup_value; ?>') { $j('.btn[id=products_view_parent]').hide(); } else { $j('.btn[id=products_view_parent]').show(); }

					if(typeof(ProductID_update_autofills__RAND__) == 'function') ProductID_update_autofills__RAND__();
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
		$template_file = is_file("./{$TemplateDVP}") ? "./{$TemplateDVP}" : './templates/order_details_templateDVP.html';
		$templateCode = @file_get_contents($template_file);
	} else {
		$template_file = is_file("./{$TemplateDV}") ? "./{$TemplateDV}" : './templates/order_details_templateDV.html';
		$templateCode = @file_get_contents($template_file);
	}

	// process form title
	$templateCode = str_replace('<%%DETAIL_VIEW_TITLE%%>', 'Detail View', $templateCode);
	$templateCode = str_replace('<%%RND1%%>', $rnd1, $templateCode);
	$templateCode = str_replace('<%%EMBEDDED%%>', (Request::val('Embedded') ? 'Embedded=1' : ''), $templateCode);
	// process buttons
	if($AllowInsert) {
		if(!$selected_id) $templateCode = str_replace('<%%INSERT_BUTTON%%>', '<button type="submit" class="btn btn-success" id="insert" name="insert_x" value="1" onclick="return order_details_validateData();"><i class="glyphicon glyphicon-plus-sign"></i> ' . $Translation['Save New'] . '</button>', $templateCode);
		$templateCode = str_replace('<%%INSERT_BUTTON%%>', '<button type="submit" class="btn btn-default" id="insert" name="insert_x" value="1" onclick="return order_details_validateData();"><i class="glyphicon glyphicon-plus-sign"></i> ' . $Translation['Save As Copy'] . '</button>', $templateCode);
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
		if($AllowUpdate) {
			$templateCode = str_replace('<%%UPDATE_BUTTON%%>', '<button type="submit" class="btn btn-success btn-lg" id="update" name="update_x" value="1" onclick="return order_details_validateData();" title="' . html_attr($Translation['Save Changes']) . '"><i class="glyphicon glyphicon-ok"></i> ' . $Translation['Save Changes'] . '</button>', $templateCode);
		} else {
			$templateCode = str_replace('<%%UPDATE_BUTTON%%>', '', $templateCode);
		}
		if(($arrPerm['delete'] == 1 && $ownerMemberID == getLoggedMemberID()) || ($arrPerm['delete'] == 2 && $ownerGroupID == getLoggedGroupID()) || $arrPerm['delete'] == 3) { // allow delete?
			$templateCode = str_replace('<%%DELETE_BUTTON%%>', '<button type="submit" class="btn btn-danger" id="delete" name="delete_x" value="1" onclick="return confirm(\'' . $Translation['are you sure?'] . '\');" title="' . html_attr($Translation['Delete']) . '"><i class="glyphicon glyphicon-trash"></i> ' . $Translation['Delete'] . '</button>', $templateCode);
		} else {
			$templateCode = str_replace('<%%DELETE_BUTTON%%>', '', $templateCode);
		}
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
	if(($selected_id && !$AllowUpdate && !$AllowInsert) || (!$selected_id && !$AllowInsert)) {
		$jsReadOnly = '';
		$jsReadOnly .= "\tjQuery('#OrderID').prop('disabled', true).css({ color: '#555', backgroundColor: '#fff' });\n";
		$jsReadOnly .= "\tjQuery('#OrderID_caption').prop('disabled', true).css({ color: '#555', backgroundColor: 'white' });\n";
		$jsReadOnly .= "\tjQuery('#ProductID').prop('disabled', true).css({ color: '#555', backgroundColor: '#fff' });\n";
		$jsReadOnly .= "\tjQuery('#ProductID_caption').prop('disabled', true).css({ color: '#555', backgroundColor: 'white' });\n";
		$jsReadOnly .= "\tjQuery('#UnitPrice').replaceWith('<div class=\"form-control-static\" id=\"UnitPrice\">' + (jQuery('#UnitPrice').val() || '') + '</div>');\n";
		$jsReadOnly .= "\tjQuery('#Quantity').replaceWith('<div class=\"form-control-static\" id=\"Quantity\">' + (jQuery('#Quantity').val() || '') + '</div>');\n";
		$jsReadOnly .= "\tjQuery('#Discount').replaceWith('<div class=\"form-control-static\" id=\"Discount\">' + (jQuery('#Discount').val() || '') + '</div>');\n";
		$jsReadOnly .= "\tjQuery('.select2-container').hide();\n";

		$noUploads = true;
	} elseif($AllowInsert) {
		$jsEditable = "\tjQuery('form').eq(0).data('already_changed', true);"; // temporarily disable form change handler
		$jsEditable .= "\tjQuery('form').eq(0).data('already_changed', false);"; // re-enable form change handler
	}

	// process combos
	$templateCode = str_replace('<%%COMBO(OrderID)%%>', $combo_OrderID->HTML, $templateCode);
	$templateCode = str_replace('<%%COMBOTEXT(OrderID)%%>', $combo_OrderID->MatchText, $templateCode);
	$templateCode = str_replace('<%%URLCOMBOTEXT(OrderID)%%>', urlencode($combo_OrderID->MatchText), $templateCode);
	$templateCode = str_replace('<%%COMBO(ProductID)%%>', $combo_ProductID->HTML, $templateCode);
	$templateCode = str_replace('<%%COMBOTEXT(ProductID)%%>', $combo_ProductID->MatchText, $templateCode);
	$templateCode = str_replace('<%%URLCOMBOTEXT(ProductID)%%>', urlencode($combo_ProductID->MatchText), $templateCode);

	/* lookup fields array: 'lookup field name' => ['parent table name', 'lookup field caption'] */
	$lookup_fields = ['OrderID' => ['orders', 'Order ID'], 'ProductID' => ['products', 'Product'], ];
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
	$templateCode = str_replace('<%%UPLOADFILE(odID)%%>', '', $templateCode);
	$templateCode = str_replace('<%%UPLOADFILE(OrderID)%%>', '', $templateCode);
	$templateCode = str_replace('<%%UPLOADFILE(ProductID)%%>', '', $templateCode);
	$templateCode = str_replace('<%%UPLOADFILE(UnitPrice)%%>', '', $templateCode);
	$templateCode = str_replace('<%%UPLOADFILE(Quantity)%%>', '', $templateCode);
	$templateCode = str_replace('<%%UPLOADFILE(Discount)%%>', '', $templateCode);
	$templateCode = str_replace('<%%UPLOADFILE(Subtotal)%%>', '', $templateCode);

	// process values
	if($selected_id) {
		if( $dvprint) $templateCode = str_replace('<%%VALUE(odID)%%>', safe_html($urow['odID']), $templateCode);
		if(!$dvprint) $templateCode = str_replace('<%%VALUE(odID)%%>', html_attr($row['odID']), $templateCode);
		$templateCode = str_replace('<%%URLVALUE(odID)%%>', urlencode($urow['odID']), $templateCode);
		if( $dvprint) $templateCode = str_replace('<%%VALUE(OrderID)%%>', safe_html($urow['OrderID']), $templateCode);
		if(!$dvprint) $templateCode = str_replace('<%%VALUE(OrderID)%%>', html_attr($row['OrderID']), $templateCode);
		$templateCode = str_replace('<%%URLVALUE(OrderID)%%>', urlencode($urow['OrderID']), $templateCode);
		if( $dvprint) $templateCode = str_replace('<%%VALUE(ProductID)%%>', safe_html($urow['ProductID']), $templateCode);
		if(!$dvprint) $templateCode = str_replace('<%%VALUE(ProductID)%%>', html_attr($row['ProductID']), $templateCode);
		$templateCode = str_replace('<%%URLVALUE(ProductID)%%>', urlencode($urow['ProductID']), $templateCode);
		if( $dvprint) $templateCode = str_replace('<%%VALUE(UnitPrice)%%>', safe_html($urow['UnitPrice']), $templateCode);
		if(!$dvprint) $templateCode = str_replace('<%%VALUE(UnitPrice)%%>', html_attr($row['UnitPrice']), $templateCode);
		$templateCode = str_replace('<%%URLVALUE(UnitPrice)%%>', urlencode($urow['UnitPrice']), $templateCode);
		if( $dvprint) $templateCode = str_replace('<%%VALUE(Quantity)%%>', safe_html($urow['Quantity']), $templateCode);
		if(!$dvprint) $templateCode = str_replace('<%%VALUE(Quantity)%%>', html_attr($row['Quantity']), $templateCode);
		$templateCode = str_replace('<%%URLVALUE(Quantity)%%>', urlencode($urow['Quantity']), $templateCode);
		if( $dvprint) $templateCode = str_replace('<%%VALUE(Discount)%%>', safe_html($urow['Discount']), $templateCode);
		if(!$dvprint) $templateCode = str_replace('<%%VALUE(Discount)%%>', html_attr($row['Discount']), $templateCode);
		$templateCode = str_replace('<%%URLVALUE(Discount)%%>', urlencode($urow['Discount']), $templateCode);
		$templateCode = str_replace('<%%VALUE(Subtotal)%%>', safe_html($urow['Subtotal']), $templateCode);
		$templateCode = str_replace('<%%URLVALUE(Subtotal)%%>', urlencode($urow['Subtotal']), $templateCode);
	} else {
		$templateCode = str_replace('<%%VALUE(odID)%%>', '', $templateCode);
		$templateCode = str_replace('<%%URLVALUE(odID)%%>', urlencode(''), $templateCode);
		$templateCode = str_replace('<%%VALUE(OrderID)%%>', '0', $templateCode);
		$templateCode = str_replace('<%%URLVALUE(OrderID)%%>', urlencode('0'), $templateCode);
		$templateCode = str_replace('<%%VALUE(ProductID)%%>', '0', $templateCode);
		$templateCode = str_replace('<%%URLVALUE(ProductID)%%>', urlencode('0'), $templateCode);
		$templateCode = str_replace('<%%VALUE(UnitPrice)%%>', '0', $templateCode);
		$templateCode = str_replace('<%%URLVALUE(UnitPrice)%%>', urlencode('0'), $templateCode);
		$templateCode = str_replace('<%%VALUE(Quantity)%%>', '1', $templateCode);
		$templateCode = str_replace('<%%URLVALUE(Quantity)%%>', urlencode('1'), $templateCode);
		$templateCode = str_replace('<%%VALUE(Discount)%%>', '0', $templateCode);
		$templateCode = str_replace('<%%URLVALUE(Discount)%%>', urlencode('0'), $templateCode);
		$templateCode = str_replace('<%%VALUE(Subtotal)%%>', '', $templateCode);
		$templateCode = str_replace('<%%URLVALUE(Subtotal)%%>', urlencode(''), $templateCode);
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

	$templateCode .= "\tProductID_update_autofills$rnd1 = function() {\n";
	$templateCode .= "\t\t\$j.ajax({\n";
	if($dvprint) {
		$templateCode .= "\t\t\turl: 'order_details_autofill.php?rnd1=$rnd1&mfk=ProductID&id=' + encodeURIComponent('".addslashes($row['ProductID'])."'),\n";
		$templateCode .= "\t\t\tcontentType: 'application/x-www-form-urlencoded; charset=" . datalist_db_encoding . "',\n";
		$templateCode .= "\t\t\ttype: 'GET'\n";
	} else {
		$templateCode .= "\t\t\turl: 'order_details_autofill.php?rnd1=$rnd1&mfk=ProductID&id=' + encodeURIComponent(AppGini.current_ProductID{$rnd1}.value),\n";
		$templateCode .= "\t\t\tcontentType: 'application/x-www-form-urlencoded; charset=" . datalist_db_encoding . "',\n";
		$templateCode .= "\t\t\ttype: 'GET',\n";
		$templateCode .= "\t\t\tbeforeSend: function() { \$j('#ProductID$rnd1').prop('disabled', true); },\n";
		$templateCode .= "\t\t\tcomplete: function() { " . (($arrPerm['insert'] || (($arrPerm['edit'] == 1 && $ownerMemberID == getLoggedMemberID()) || ($arrPerm['edit'] == 2 && $ownerGroupID == getLoggedGroupID()) || $arrPerm['edit'] == 3)) ? "\$j('#ProductID$rnd1').prop('disabled', false); " : "\$j('#ProductID$rnd1').prop('disabled', true); ")." \$j(window).resize(); }\n";
	}
	$templateCode .= "\t\t});\n";
	$templateCode .= "\t};\n";
	if(!$dvprint) $templateCode .= "\tif(\$j('#ProductID_caption').length) \$j('#ProductID_caption').click(function() { ProductID_update_autofills$rnd1(); });\n";


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
	$rdata = $jdata = get_defaults('order_details');
	if($selected_id) {
		$jdata = get_joined_record('order_details', $selected_id);
		if($jdata === false) $jdata = get_defaults('order_details');
		$rdata = $row;
	}
	$templateCode .= loadView('order_details-ajax-cache', ['rdata' => $rdata, 'jdata' => $jdata]);

	// hook: order_details_dv
	if(function_exists('order_details_dv')) {
		$args = [];
		order_details_dv(($selected_id ? $selected_id : FALSE), getMemberInfo(), $templateCode, $args);
	}

	return $templateCode;
}