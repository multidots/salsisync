jQuery(document).ready(function ($) {
	var currentIndex = 0;
	var totalData = 0;
	var currentUpdateIndex = 0;
	var totalUpdateData = 0;
	var updateAvailableProducts = [];
	var currentNewIndex = 0;
	var totalNewData = 0;
	var newAvailableProducts = [];
	var isAjaxRunning = false; // Flag to track if AJAX is running
	$('.stop-insertion-button').hide();
	var bulkInsertObject;
	var isStopInsertion = false;
	var uniqueIdForLog;
	var bulkInsertLog = [];

	// Nested Tab Switch (Sync Tab)
	/**
	 * Data tab switch
	 */
	var getActiveTab = localStorage.getItem('activeTab');
	if (getActiveTab) {
		$('.salsisync-product__data-tab-wrap .salsisync-product__sub-tab').removeClass(
			'salsisync-product__sub-tab-active'
		);
		$('.salsisync-product__data-tab-wrap .salsisync-product__tab-panel').removeClass(
			'active'
		);
		$('#' + getActiveTab).addClass('active');
		$(
			'.salsisync-product__data-tab-wrap .salsisync-product__sub-tab[data-sub-tab="' +
				getActiveTab +
				'"]'
		).addClass('salsisync-product__sub-tab-active');
	}
	/**
	 * Reset the active tab to data tab when page is refreshed
	 */
	$('.nav-tab').click(function (e) {
		localStorage.setItem('activeTab', 'data-mapping');
	});

	$('.salsisync-product__data-tab-wrap .salsisync-product__sub-tab').click(function (
		e
	) {
		e.preventDefault();
		$('.salsisync-product__sub-tab').removeClass('salsisync-product__sub-tab-active');
		$('.salsisync-product__tab-panel').removeClass('active');
		$(this).addClass('salsisync-product__sub-tab-active');
		const subTabID = $(this).data('sub-tab');
		localStorage.setItem('activeTab', subTabID); // Store the active tab in localStorage
		$('#' + subTabID).addClass('active');
	});
	/**
	 * sync tab switch
	 */
	$('.salsisync-product__sync-wrap .salsisync-product__sub-tab').click(function (e) {
		e.preventDefault();
		$('.salsisync-product__sync-wrap .salsisync-product__sub-tab').removeClass(
			'salsisync-product__sub-tab-active'
		);
		$('.salsisync-product__sync-wrap .salsisync-product__tab-panel').removeClass(
			'active'
		);
		$(this).addClass('salsisync-product__sub-tab-active');
		const subTabID = $(this).data('sub-tab');
		$('#' + subTabID).addClass('active');
	});
	/**
	 * add repeater
	 */
	let rowCount = $('#repeater-wrapper .repeater-group').length;
	let lastRowIndex = $('#repeater-wrapper .repeater-group')
		.last()
		.data('index');
	lastRowIndex = lastRowIndex ? lastRowIndex : 0;

	// Add new row on button click
	$('#add-row').click(function () {
		lastRowIndex++;
		let newRow = `
            <div data-index="${lastRowIndex}" class="repeater-group">
                <input required type="text" name="repeater_fields[${lastRowIndex}][label]" placeholder="Label" />
                <input required type="text" name="repeater_fields[${lastRowIndex}][key]" placeholder="Key" />
                <button type="button" class="dashicons dashicons-dismiss remove-row"></button>
            </div>
        `;
		$('#repeater-wrapper').append(newRow);
		if ($('#repeater-wrapper .repeater-group').length > 0) {
			$('#data-custom-mapping input#submit').show();
		}
	});

	// Remove row on button click
	$(document).on('click', '.remove-row', function () {
		$(this).closest('.repeater-group').remove();
	});
	/**
	 * Hide save button for repeater if no repeater group is available
	 */
	if (rowCount == 0) {
		$('#data-custom-mapping input#submit').hide();
	}

	/**
	 * Remove api key error message if user start updating
	 */
	$('.repeater-group')
		.find('input:last')
		.on('keyup', function (e) {
			$(this).parent().find('.key__error_message').remove();
		});

	$('.accordion-header').on('click', function () {
		const $accordionItem = $(this).parent();
		const $accordionContent = $accordionItem.find('.accordion-content');

		// Collapse other sections
		$('.accordion-content').not($accordionContent).slideUp();
		$('.accordion-item').not($accordionItem).removeClass('active');

		// Toggle the clicked section
		$accordionContent.stop(true, true).slideToggle();
		$accordionItem.toggleClass('active');
	});

	// disable api connection submit button.
	var input1InitialLength = $('input[name="salsisync_api_token__key"]');
	var input2InitialLength = $('input[name="salsisync_orgs__key"]');

	if (input1InitialLength.length > 0 && input2InitialLength.length > 0) {
		if (
			input1InitialLength.val().length >= 5 &&
			input2InitialLength.val().length >= 5
		) {
			if ($('#api-connected-button').length > 0) {
				$('#api-connected-button').attr('disabled', true);
			} else {
				// $('#api-connection-button').attr('disabled', true);
			}
		} else {
			$('.api-connection-button').attr('disabled', true);
		}
	}

	// $('.api-connection-button').attr('disabled', true);
	function salsisyncCheckInputLength() {
		var input1Length = $('input[name="salsisync_api_token__key"]').val().length;
		var input2Length = $('input[name="salsisync_orgs__key"]').val().length;

		// Enable the button only if both inputs have at least 5 characters
		if (input1Length >= 5 && input2Length >= 5) {
			$('.api-connection-button').attr('disabled', false);
		} else {
			$('.api-connection-button').attr('disabled', true);
		}
	}

	// Attach keyup event to both input fields to check the length on every key press
	$('input[name="salsisync_api_token__key"], input[name="salsisync_orgs__key"]').on(
		'keyup',
		salsisyncCheckInputLength
	);

	$(document).ajaxStop(function () {
		$('#insert-data-btn').attr('disabled', false);
		$('#test-insert-data-btn').attr('disabled', false);
		$('#salsisync_product_sync_custom_input__submit').attr('disabled', false);
	});

	// $('#show_api_keys').
	$('#show_api_keys').change(function () {
		$('#api_keys_wrapper').toggleClass('show', $(this).is(':checked'));
	});

	// Button click event
	$('#insert-data-btn').click(function () {
		$('#progress').html('Starting the data insertion...');
		$('#sync-data .salsisync-product__sync-wrap-table').show();
		$(this).attr('data-bulk-insert', true);
		$('#report').html('');
		$('.salsisync-product__sync-note').hide();
		$('.sync-tab').css('pointer-events', 'none');
		isStopInsertion = false;
		// Get the total number of items in the PHP array (done on the server-side)
		isAjaxRunning = true;
		let apiType = 'default';
		let productFetchLimit = 100
        const fetchAll = $('form.salsisync-product_sync_custom_input__form input[name="salsisync_all_product_to_sync__key"]').is(':checked');
        const customLimit = parseInt($('form.salsisync-product_sync_custom_input__form input[name="salsisync_number_of_product_to_sync__key"]').val(), 10);

		if (fetchAll) {
            apiType = 'export';
			productFetchLimit = 'all';
        } else {
			if( ! fetchAll && customLimit >= 500 ){
				apiType = 'export';
				productFetchLimit = customLimit;
			}else{
				apiType = 'default';
				productFetchLimit = customLimit;
			}
		}

		bulkInsertObject = $.ajax({
			type: 'POST',
			url: salsisync_settings_ajax.ajax_url,
			data: {
				action: 'salsisync_get_product_data_count',
				nonce: salsisync_settings_ajax.nonce,
				params: '',
				limit: productFetchLimit,
				api_type: apiType,
				fetchAll: fetchAll,
			},
			beforeSend: function () {
				isAjaxRunning = true;
				$('#insert-data-btn').attr('disabled', true);
				$('#test-insert-data-btn').attr('disabled', true);
				$('#salsisync_product_sync_custom_input__submit').attr('disabled', true);
				$('.salsisync-product__sub-tab').css('pointer-events', 'none');
				salsisyncSetAJAXRunningStatus('true');
			},
			success: function (response) {
				// console.log(response);				
				totalData = parseInt(response.count); // Set the total data count
				uniqueIdForLog = response.unique_id;
				$('.stop-insertion-button').show();
				salsisyncInsertNextProduct(totalData > 100 ? 'all-page-response.json' : 'api-first-page-response.json'); // Start the insertion process
			},
			error: function () {
				$('#progress').html('Error retrieving data count.');
			},
		});
		// console.log('ajaxObject', ajaxObject);
	});
	/**
	 * Stop Insertion
	 */
	$('.stop-insertion-button').click(function (e) {
		e.preventDefault();
		var status = confirm('Are you sure to stop data insertion ?');
		if (status) {
			if (bulkInsertObject) {
				isStopInsertion = true;
				bulkInsertObject.abort();
				$('#progress').html('Data insertion stopped.');
				$('.stop-insertion-button').hide();
				$('.sync-tab').css('pointer-events', 'auto');
				isAjaxRunning = false;
				salsisyncSetAJAXRunningStatus('false');
				currentIndex = 0;
				$('.salsisync-product__sub-tab').css('pointer-events', 'auto');
			}
		}
	});
	/**
	 * Test Insert Data
	 * for test button
	 */
	$('#test-insert-data-btn').click(function () {
		$('#progress').html('Starting test data insertion.');
		$('#sync-data .salsisync-product__sync-wrap-table').show();
		$(this).attr('data-active', true);
		$('#report').html('');
		$('.salsisync-product__sync-note').hide();
		isStopInsertion = false;
		// Get the total number of items in the PHP array (done on the server-side)
		isAjaxRunning = true;
		currentIndex = 0;

		$.ajax({
			type: 'POST',
			url: salsisync_settings_ajax.ajax_url,
			data: {
				action: 'salsisync_test_insert_data_item',
				nonce: salsisync_settings_ajax.nonce,
			},
			beforeSend: function () {
				isAjaxRunning = true;
				isStopInsertion = false;
				$('#insert-data-btn').attr('disabled', true);
				$('#test-insert-data-btn').attr('disabled', true);
				$('#salsisync_product_sync_custom_input__submit').attr('disabled', true);
				salsisyncSetAJAXRunningStatus('true');
			},
			success: function (response) {
				totalData = 1; // Set the total data count
				salsisyncInsertNextProduct(); // Start the insertion process
				// console.log('totalData', totalData);
			},
			error: function () {
				$('#progress').html('Error retrieving data count.');
			},
			complete: function () {
				isAjaxRunning = false;
				currentIndex = 0;
				isStopInsertion = false;
				salsisyncSetAJAXRunningStatus('false');
			},
		});
	});
	if ($('insert-data-btn').attr('data-bulk-insert') == 'true') {
		// $('#test-insert-data-btn').click();
		console.log('bulk insert');
	}
	/**
	 * prevent page reload when ajax is running
	 */
	window.addEventListener('beforeunload', function (e) {
		// Call your AJAX function
		// sendAjaxBeforeUnload();
		if (isAjaxRunning) {
			e.preventDefault(); // If you prevent default behavior in Mozilla Firefox prompt will always be shown
			salsisyncSetAJAXRunningStatus('false');
			// Show an alert message (optional)
			var confirmationMessage = 'Are you sure you want to leave?';
			(e || window.event).returnValue = confirmationMessage; // For older browsers
			return confirmationMessage; // For modern browsers
		}
	});
	/**
	 * Disable all links when ajax is running
	 */
	$('a')
		.not('#insert-data-btn')
		.on('click', function (e) {
			if (isAjaxRunning) {
				e.preventDefault(); // Prevent the default action (navigation)
				alert('An operation is processing. Please wait.');
				return false; // Stop the click event
			}
		});

	$('.nav-tab.set-disabled').click(function (e) {
		e.preventDefault();
	});

	// Function to insert data one by one
	function salsisyncInsertNextProduct( responseFileName = 'api-first-page-response.json' ) {
		uniqueIdForLog = uniqueIdForLog ? uniqueIdForLog : '';
		if (isStopInsertion == true) {
			return;
		}

		if (currentIndex >= totalData) {
			// Reset the current index.
			if ($('#test-insert-data-btn').attr('data-active') == 'true') {
				currentIndex = 0;
			}
			// Update the progress text.
			$('#progress').html(
				'<span>Data insertion complete. <a href="/wp-admin/edit.php?post_type=product"> Click here </a></span>'
			);
			// salsisyncUpdateTestProductStatus();
			isAjaxRunning = false;
			$('#test-insert-data-btn').attr('data-active', false);
			$('.salsisync-product__sync-note').show();
			/**
			 * set ajax to false
			 */
			salsisyncSetAJAXRunningStatus('false');
			// Hide stop insertion button.
			$('.stop-insertion-button').hide();
			/**
			 * Reset custom data mapping value
			 */
			salsisyncResetCustomDataMappingValue();
			/**
			 * hide the button once data insert is complete
			 */
			if (totalData > 1) {
				$('#insert-data-btn').hide();
				$('#test-insert-data-btn').hide();
				$('.salsisync-product__sync-note').hide();
			}
			// console.log('bulkInsertLog', bulkInsertLog);
			// Action the buttons
			$('.salsisync-product__sub-tab').css('pointer-events', 'auto');
			return;
		}

		// Insert the next item
		$.ajax({
			type: 'POST',
			url: salsisync_settings_ajax.ajax_url,
			data: {
				action: 'salsisync_insert_data_item',
				nonce: salsisync_settings_ajax.nonce,
				index: currentIndex,
				unique_id: uniqueIdForLog,
				response_file_name: responseFileName,
			},
			success: function (response) {
				currentIndex++;
				var insertLog = {
					index: currentIndex,
					response: response,
				};
				bulkInsertLog.push(insertLog);
				var progress = Math.round((currentIndex / totalData) * 100);
				// Update the progress text.
				if (isStopInsertion == false) {
					$('#progress').html(
						'<span>' +
							' </span><span> ⏳ Inserting item ' +
							(+currentIndex+1) +
							' of ' +
							totalData +
							' (' +
							progress +
							'%)</span>'
					);
					$('#salsisync-product__sync-report-title').html(
						'<td style="width:125px"><h4>Item</h4></td><td><h4>Product Details</h4></td>'
					);
					$('#report').prepend(
						'<tr><td style="width:125px;height:33.2px">' +
							currentIndex +
							'</td><td>' +
							response +
							'</td></tr>'
					);
					salsisyncInsertNextProduct(totalData > 100 ? 'all-page-response.json' : 'api-first-page-response.json'); // Recursively call to insert the next item.
				}
			},
			error: function (xhr, status, error) {
				// Log the error but continue with the next item
				console.error('Error inserting item ' + currentIndex + ': ' + error);
				
				var errorMessage = 'Error: ' + error;
				var insertLog = {
					index: currentIndex,
					response: errorMessage,
				};
				bulkInsertLog.push(insertLog);
				
				// Add error information to the report
				$('#report').prepend(
					'<tr><td style="width:125px;height:33.2px">' +
						(+currentIndex+1) +
						'</td><td class="error-message">' +
						errorMessage +
						'</td></tr>'
				);
				
				// Increment the index and continue
				currentIndex++;
				var progress = Math.round((currentIndex / totalData) * 100);
				
				if (isStopInsertion == false) {
					$('#progress').html(
						'<span> ⏳ Inserting item ' +
							(+currentIndex+1) +
							' of ' +
							totalData +
							' (' +
							progress +
							'%)</span>'
					);
					// Continue to the next item despite the error
					salsisyncInsertNextProduct(totalData > 100 ? 'all-page-response.json' : 'api-first-page-response.json');
				}
			}
		});
	}
	/**
	 * Update test product status
	 */
	function salsisyncUpdateTestProductStatus() {
		// Insert the next item
		$.ajax({
			type: 'POST',
			url: salsisync_settings_ajax.ajax_url,
			data: {
				action: 'salsisync_update_test_data_insert_status',
				nonce: salsisync_settings_ajax.nonce,
			},
			success: function (response) {
				console.log('test response', response);
			},
			error: function () {
				console.log('Error updating test product status');
			},
		});
	}
	/**
	 * Set ajax running status to true
	 * Fire when [stop execution] button is clicked
	 */
	function salsisyncSetAJAXRunningStatus(status = 'true') {
		// Insert the next item
		console.log('setting ajax running status');
		$.ajax({
			type: 'POST',
			url: salsisync_settings_ajax.ajax_url,
			data: {
				action: 'salsisync_set_ajax_running_status',
				nonce: salsisync_settings_ajax.nonce,
				status: status,
				bulkInsertId: uniqueIdForLog,
				bulkInsertLog: bulkInsertLog,
			},
			success: function (response) {
				console.log('ajax running status', response.status);
				console.log('log_insert_status', response.log_insert_status);
			},
			error: function () {
				console.log(
					'❌ Something went wrong for setting ajax running status'
				);
			},
		});
	}
	function salsisyncResetCustomDataMappingValue(status = 'true') {
		$.ajax({
			type: 'POST',
			url: salsisync_settings_ajax.ajax_url,
			data: {
				action: 'salsisync__reset_custom_data_mapping_value',
				nonce: salsisync_settings_ajax.nonce,
			},
			success: function (response) {
				console.log(
					'Custom data mapping reset Status',
					response.status
				);
			},
			error: function () {
				console.log('❌ Something went wrong while resetting value');
			},
		});
	}
	/**
	 * Dismis notice
	 */
	$('.salsisyncnotice_message.is-dismissible').click(function (e) {
		e.preventDefault();
		$.ajax({
			type: 'POST',
			url: salsisync_settings_ajax.ajax_url,
			data: {
				action: 'salsisync_dismiss_success_notice',
				nonce: salsisync_settings_ajax.nonce,
			},
			success: function (response) {
				console.log('response', response);
			},
			error: function () {
				console.log('Error dismissing notice');
			},
		});
	});
	/**
	 * Check for updated products
	 * in sync tab
	 * Todo: disable bulk insert when check for update is running
	 */
	$('#check-for-update-button').click(function (e) {
		e.preventDefault();
		/**
		 * disable self button
		 */
		$(this).prop('disabled', true).text('Checking for Updates...');
		/**
		 * Get the number of products that needs to checked for update
		 */
		var productCountForUpdate = $('#check-for-update-notice').attr('data-needs-to-checked');
		productCountForUpdate = parseInt(productCountForUpdate);
		/**
		 * hide insert and test buttion
		 */
		$('#insert-data-btn').attr('disabled', true);
		$('#test-insert-data-btn').attr('disabled', true);
		$('#salsisync_product_sync_custom_input__submit').attr('disabled', true);
		$('#bulk-update').attr('disabled', true);
		$('#bulk-insert').attr('disabled', true);
		/**
		 * Run Ajax
		 */
		$.ajax({
			type: 'POST',
			url: salsisync_settings_ajax.ajax_url,
			data: {
				action: 'salsisync_check_updated_product_from_salsify_api',
				nonce: salsisync_settings_ajax.nonce,
				product_count_for_update: productCountForUpdate ? productCountForUpdate : 100,
			},
			beforeSend: function () {},
			success: function (response) {
				/**
				 * View the response
				 */
				addDataToTable(response);
				/**
				 * disable self button
				 */
				$('#check-for-update-button')
					.attr('disabled', false)
					.text('Recheck for update');

				/**
				 * hide insert and test buttion
				 */
				$('#insert-data-btn').attr('disabled', false);
				$('#test-insert-data-btn').attr('disabled', false);
				$('#salsisync_product_sync_custom_input__submit').attr('disabled', false);
				$('#bulk-update').attr('disabled', false);
				$('#bulk-insert').attr('disabled', false);
				/**
				 *
				 */
				if (Object.keys(response.data.changes.updated).length > 0) {
					$('#bulk-update')
						.removeClass('hide-bulk-button')
						.addClass('show-bulk-button');
				}
			},
			error: function () {},
		});
	});
	function addDataToTable(response) {
		console.log('response', response);
		// debugger;
		var updatedData = response.data.changes.updated;
		var newData = response.data.changes.new;
		console.log(jQuery.type(updatedData), Object.keys(updatedData).length);
		// debugger;
		/**
		 * Append data to the update table
		 */
		if (updatedData) {
			$('#updated_product tbody').html('');
			if (
				('object' === jQuery.type(updatedData) || 'array' === jQuery.type(updatedData) ) &&
				Object.keys(updatedData).length > 0
			) {
				Object.entries(updatedData).forEach((product) => {
					const [key, value] = product;
					$('#updated_product tbody').append(
						'<tr id="' +
							value['salsify:id'] +
							'"><td>' +
							value['salsify:id'] +
							'</td><td>' +
							value['Product Name'] +
							'</td><td>' +
							'Pending</td></tr>'
					);
				});

				/**
				 * Show the bulk update button
				 */
				if (Object.keys(updatedData).length > 0) {
					$('#bulk-update')
						.removeClass('hide-bulk-button')
						.addClass('show-bulk-button');
				}
			} else {
				$('#updated_product tbody').html('');
				$('#updated_product tbody').append(
					'<tr><td class="no-update-product" colspan="3">No Updated Products found</td></tr>'
				);
				$('#bulk-update')
					.removeClass('show-bulk-button')
					.addClass('hide-bulk-button');
			}
		}
		/**
		 * Append data to the new product table
		 */
		if (newData) {
			$('#new_product tbody').html('');
			if (
				('object' === jQuery.type(newData) || 'array' === jQuery.type(newData)) &&
				Object.keys(newData).length > 0
			) {
				Object.entries(newData).forEach((product) => {
					const [key, value] = product;
					$('#new_product tbody').append(
						'<tr id="' +
							value['salsify:id'] +
							'"><td>' +
							value['salsify:id'] +
							'</td><td>' +
							value['Product Name'] +
							'</td><td>' +
							'Pending</td></tr>'
					);
				});
				/**
				 * Show the bulk insert button
				 */
				if (Object.keys(newData).length > 0) {
					$('#bulk-insert')
						.removeClass('hide-bulk-button')
						.addClass('show-bulk-button');
				}
			} else {
				$('#new_product tbody').html('');
				$('#new_product tbody').append(
					'<tr><td class="no-new-product" colspan="3">No New Products found</td></tr>'
				);
				$('#bulk-insert')
					.removeClass('show-bulk-button')
					.addClass('hide-bulk-button');
			}
		}
	}
	/**
	 * Bulk update existing Products
	 * in sync tab
	 */
	$(document).on(
		'click',
		'.salsisync-product__updated-bulk-header #bulk-update',
		function (e) {
			e.preventDefault();
			// var productID = $(this).attr('data-product-id');
			$(this).attr('disabled', true);
			$.ajax({
				type: 'POST',
				url: salsisync_settings_ajax.ajax_url,
				data: {
					action: 'salsisync_count_products_for_update',
					nonce: salsisync_settings_ajax.nonce,
					// product_id: productID,
				},
				beforeSend: function () {},
				success: function (response) {
					// $('#updated_product tr#' + productID).remove();
					if (response.data.success == true) {
						totalUpdateData = parseInt(
							response.data.updated_products_count
						); // Set the total data count
						updateAvailableProducts =
							response.data.update_available_products_arr_keys;
						// call recursive function
						salsisyncSyncUpdatetNextProduct();
					}
				},
				error: function () {},
			});
		}
	);

	function salsisyncSyncUpdatetNextProduct() {
		if (currentUpdateIndex >= totalUpdateData) {
			return;
		}
		// Insert the next item
		$.ajax({
			type: 'POST',
			url: salsisync_settings_ajax.ajax_url,
			data: {
				action: 'salsisync_sync_update_product',
				nonce: salsisync_settings_ajax.nonce,
				index: currentUpdateIndex,
				product_id: updateAvailableProducts[currentUpdateIndex],
			},
			beforeSend: function () {
				$(
					'#updated_product tbody tr#' +
						updateAvailableProducts[currentUpdateIndex] +
						' td:last-child'
				).text('Updating...');
			},
			success: function (response) {
				var progress = Math.round(
					(currentUpdateIndex / totalUpdateData) * 100
				);
				console.log(response.data.product_url);
				if (response.data.success === true) {
					$(
						'#updated_product tbody tr#' +
							updateAvailableProducts[currentUpdateIndex] +
							' td:last-child'
					).html(
						'✅ done' +
							' <a href="' +
							response.data.product_url +
							'">View</a>'
					);
				} else {
					$(
						'#updated_product tbody tr#' +
							updateAvailableProducts[currentUpdateIndex] +
							' td:last-child'
					).html('❌ ' + response.data.message);
				}
				currentUpdateIndex++;
				// if (isStopInsertion == false) {
				salsisyncSyncUpdatetNextProduct(); // Recursively call to insert the next item
				// }
			},
			error: function () {
				$('#progress').html('Error inserting item ' + currentIndex);
			},
		});
	}

	/**
	 * Bulk Insert New Products
	 * in sync tab
	 */
	$(document).on(
		'click',
		'.salsisync-product__bulk-insert-header #bulk-insert',
		function (e) {
			e.preventDefault();
			// var productID = $(this).attr('data-product-id');
			$(this).attr('disabled', true);
			// console.log(productID);
			$.ajax({
				type: 'POST',
				url: salsisync_settings_ajax.ajax_url,
				data: {
					action: 'salsisync_count_products_for_insert',
					nonce: salsisync_settings_ajax.nonce,
					// product_id: productID,
				},
				beforeSend: function () {},
				success: function (response) {
					// $('#updated_product tr#' + productID).remove();
					if (response.data.success == true) {
						totalNewData = parseInt(
							response.data.new_products_count
						); // Set the total data count
						newAvailableProducts =
							response.data.new_available_products_arr_keys;
						// call recursive function
						salsisyncSyncInserttNextProduct();
					}
				},
				error: function () {},
			});
		}
	);

	/**
	 * Sync tab - new products
	 * Insert new products
	 * @returns
	 */
	function salsisyncSyncInserttNextProduct() {
		if (currentNewIndex >= totalNewData) {
			return;
		}

		// Insert the next item
		$.ajax({
			type: 'POST',
			url: salsisync_settings_ajax.ajax_url,
			data: {
				action: 'salsisync_sync_insert_product',
				nonce: salsisync_settings_ajax.nonce,
				index: currentNewIndex,
				product_id: newAvailableProducts[currentNewIndex],
			},
			beforeSend: function () {
				$(
					'#new_product tbody tr#' +
						newAvailableProducts[currentNewIndex] +
						' td:last-child'
				).text('Inserting...');
			},
			success: function (response) {
				var progress = Math.round(
					(currentNewIndex / totalNewData) * 100
				);
				if (response.data.success === true) {
					$(
						'#new_product tbody tr#' +
							newAvailableProducts[currentNewIndex] +
							' td:last-child'
					).html(
						'✅ done' +
							' <a href="' +
							response.data.product_url +
							// '#' +
							'">View</a>'
					);
				} else {
					$(
						'#new_product tbody tr#' +
							newAvailableProducts[currentNewIndex] +
							' td:last-child'
					).html('❌ ' + response.data.message);
				}
				currentNewIndex++;

				// if (isStopInsertion == false) {
				salsisyncSyncInserttNextProduct(); // Recursively call to insert the next item
				// }
			},
			error: function () {
				$('#progress').html('Error inserting item ' + currentIndex);
			},
		});
	}

	/**
	 * Show log data from dropdown
	 * It fetches the log data from the database
	 * and displays it in the table
	 */
	$('#bulk-log-dropdown').on('change', function () {
		/**
		 * Get the log ID from the dropdown
		 */
		var logId = $(this).val();
		/**
		 * Add loading text
		 */
		$('#log-data-output').html('Loading...');
		/**
		 * Fetch log data from the database
		 * based on demand
		 */
		if (logId) {
			$.ajax({
				url: ajaxurl, // WP AJAX URL provided by WordPress.
				type: 'POST',
				data: {
					nonce: salsisync_settings_ajax.nonce,
					action: 'salsisync__fetch_and_show_log_data',
					log_id: logId,
				},
				success: function (response) {
					if (response.status == 'success') {
						if(response.data == null){
							console.log('No log data found');
							$('#log-data-output').html('<p>No log data found.</p>');
							return;
						}
						/**
						 * Empty the table before appending new data
						 */
						$('#log-report-table tbody').empty();
						/**
						 * Append new data to the table
						 */
						for (var i = 0; i < response.data.length; i++) {
							$('#log-report-table tbody').append(
								'<tr id="' +
									response.data[i].index +
									'"><td>' +
									response.data[i].index +
									'</td><td>' +
									response.data[i].response +
									'</td></tr>'
							);
						}
						/**
						 * Empty the loading text
						 */
						$('#log-data-output').text('');
					} else {
						$('#log-data-output').html(
							'<p>' + response.data + '</p>'
						);
					}
				},
				error: function () {
					$('#log-data-output').html('<p>Error retrieving data.</p>');
				},
			});
		} else {
			$('#log-data-output').html('');
		}
	});
	/**
	 * If data mapping changed
	 * by clicking on that update
	 * 1. update local storage value
	 * 2. change the tab from data to sync
	 * 3. Fire the ajax to update the data
	 * 4. Show the progress in the table
	 *
	 */
	$('#data-mapping-changed').on('click', function (e) {
		e.preventDefault();
		/**
		 * update local storage data
		 */
		localStorage.setItem('syncActiveTab', 'sync-data');
		sessionStorage.setItem('runDataMappingSyncAjax', 'true'); // Set a flag in sessionStorage
		/**
		 * Get the current anchor(e) href url
		 */
		var currentUrl = $(this).attr('href');
		/**
		 * Extract the current URL parameters
		 */
		var urlParams = new URLSearchParams(currentUrl);
		/**
		 * url - admin.php%3Fpage=salsisync&tab=sync&action=update&limit=350&api_type=default&fetchAll=true
		 * extract limit, api_type and fetchAll
		 * and assign it to the variable
		 */
		var limitValue = urlParams.get('limit');
		var apiTypeValue = urlParams.get('api_type');
		var fetchAllValue = urlParams.get('fetchAll');
		/**
		 * Change the URL from data to sync
		 */
		var url = window.location.href;
		url = url.replace('data', 'sync');
		url = url + '&action=sync-custom-mapping-data' + '&limit=' + limitValue + '&api_type=' + apiTypeValue + '&fetchAll=' + fetchAllValue;
		window.location = url;
	});
	const urlParams = new URLSearchParams(window.location.search);
	const runDataMappingSyncAjax = sessionStorage.getItem(
		'runDataMappingSyncAjax'
	); // Retrieve sessionStorage flag
	/**
	 * Actions for data mapping sync update
	 */
	if (
		urlParams.has('action') &&
		urlParams.get('action') === 'sync-custom-mapping-data' &&
		runDataMappingSyncAjax === 'true'
	) {
		// Clear the sessionStorage flag so AJAX won't run again on refresh
		sessionStorage.removeItem('runDataMappingSyncAjax');
		// check limit value from the urlParams and assign it to the variable
		const limitValue = urlParams.get('limit');
		//  check fetchAll value from the urlParams and assign it to the variable
		const fetchAllValue = urlParams.get('fetchAll');
		// check api_type value from the urlParams and assign it to the variable
		const apiTypeValue = urlParams.get('api_type');
		/**
		 * Fire the ajax
		 */
		$('#progress').html('Starting the data insertion...');
		$('#sync-data .salsisync-product__sync-wrap-table').show();
		$(this).attr('data-bulk-insert', true);
		$('#report').html('');
		$('.salsisync-product__sync-note').hide();
		$('.sync-tab').css('pointer-events', 'none');
		isStopInsertion = false;
		// Get the total number of items in the PHP array (done on the server-side)
		isAjaxRunning = true;
		// debugger;
		bulkInsertObject = $.ajax({
			type: 'POST',
			url: salsisync_settings_ajax.ajax_url,
			data: {
				action: 'salsisync_get_product_data_count',
				nonce: salsisync_settings_ajax.nonce,
				params: 'custom_data_mapping_update',
				limit: limitValue,
				api_type: apiTypeValue,
				fetchAll: fetchAllValue,
			},
			beforeSend: function () {
				isAjaxRunning = true;
				$('#insert-data-btn').attr('disabled', true);
				$('#test-insert-data-btn').attr('disabled', true);
				$('#salsisync_product_sync_custom_input__submit').attr('disabled', true);
				salsisyncSetAJAXRunningStatus('true');
			},
			success: function (response) {
				console.log(response);
				totalData = parseInt(response.count); // Set the total data count
				uniqueIdForLog = response.unique_id;
				$('.stop-insertion-button').show();
				salsisyncInsertNextProduct(totalData > 100 ? 'all-page-response.json' : 'api-first-page-response.json'); // Start the insertion process
			},
			error: function () {
				$('#progress').html('Error retrieving data count.');
			},
		});
	}
	function moveTabContent() {
		if ($(window).width() <= 767) {
			// Find the active tab and append the content inside it
			$('.nav-tab-active').append($('.salsisync-product__tab-content'));
		} else {
			// Move the content back to its original position on larger screens
			$('.salsisync-product__nav-wrap').append(
				$('.salsisync-product__tab-content')
			);
		}
	}

	// Run on page load
	moveTabContent();

	// Hide product sync custom input option if select all option from the checkbox.
	$('.salsisync-product__sync-wrap .salsisync-product_sync_custom_input__form input[type="checkbox"]').on('change', function(){
		var customiInputBox = $('.salsisync-product__sync-wrap .salsisync-product_sync_custom_input__form input[name="salsisync_number_of_product_to_sync__key"]');
		if($(this).is(':checked')){
			// Hide the input number.
			$(customiInputBox).parent().parent().hide();
		}else{
			$(customiInputBox).parent().parent().show();
		}
	})
	// hide by default if checkbox is checked.
	if($('.salsisync-product__sync-wrap .salsisync-product_sync_custom_input__form input[type="checkbox"]').is(':checked')){
		$('.salsisync-product__sync-wrap .salsisync-product_sync_custom_input__form input[name="salsisync_number_of_product_to_sync__key"]').parent().parent().hide();
	}

	// Run when resizing the window
	$(window).resize(function () {
		moveTabContent();
	});
});
