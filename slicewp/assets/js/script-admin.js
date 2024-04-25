jQuery( function($) {

	/**
     * Strips one query argument from a given URL string
     *
     */
    function remove_query_arg( key, sourceURL ) {

        var rtn = sourceURL.split("?")[0],
            param,
            params_arr = [],
            queryString = (sourceURL.indexOf("?") !== -1) ? sourceURL.split("?")[1] : "";

        if (queryString !== "") {
            params_arr = queryString.split("&");
            for (var i = params_arr.length - 1; i >= 0; i -= 1) {
                param = params_arr[i].split("=")[0];
                if (param === key) {
                    params_arr.splice(i, 1);
                }
            }

            rtn = rtn + "?" + params_arr.join("&");

        }

        if(rtn.split("?")[1] == "") {
            rtn = rtn.split("?")[0];
        }

        return rtn;
    }


    /**
     * Adds an argument name, value pair to a given URL string
     *
     */
    function add_query_arg( key, value, sourceURL ) {

        sourceURL = remove_query_arg( key, sourceURL );

        return sourceURL + '&' + key + '=' + value;

    }

    
	/**
	 * Initialize colorpicker
	 *
	 */
	$('.slicewp-colorpicker').wpColorPicker();

	/**
	 * Initialize Chosen
	 *
	 */
	if( typeof $.fn.chosen != 'undefined' ) {

		$('.slicewp-chosen').chosen();

	}

    /**
     * Initialize jQuery select2
     *
     */
    if( $.fn.select2 ) {
        $('.slicewp-select2').select2({
            minimumResultsForSearch : Infinity
        }).on('select2:open', function() {
            var container = $('.select2-container').last();
            container.addClass('slicewp-select2-container');
        });
    }

    /**
     * Initialize datepicker
     *
     */
    if( $.fn.datepicker ) {

        $('.slicewp-datepicker').datepicker({
            dateFormat : 'yy-mm-dd',
            beforeShow : function(i) { if ($(i).attr('readonly')) { return false; } }
        });

    }

    /**
     * Initializa datetimepicker
     *
     */
    if( $.fn.datetimepicker ) {

        $('.slicewp-datetimepicker').datetimepicker({
            dateFormat  : 'yy-mm-dd',
            timeFormat  : 'HH:mm:00',
            controlType : 'select'
        });

    }

	/**
	 * Tab Navigation
	 *
	 */
	$('.slicewp-nav-tab').on( 'click', function(e) {

        if ( typeof $(this).attr('data-tab')  != 'undefined' ) {            
            e.preventDefault();

            // Nav Tab activation
            $('.slicewp-nav-tab').removeClass('slicewp-active');
            $(this).addClass('slicewp-active');

            // Show tab
            $('.slicewp-tab').removeClass('slicewp-active');

            var nav_tab = $(this).attr('data-tab');
            $('.slicewp-tab[data-tab="' + nav_tab + '"]').addClass('slicewp-active');
            $('input[name=active_tab]').val( nav_tab );


            // Change "tab" query var
            url = window.location.href;
            url = remove_query_arg( 'tab', url );
            url = add_query_arg( 'tab', $(this).attr('data-tab'), url );

            window.history.replaceState( {}, '', url );

            // Change http referrer
            $_wp_http_referer = $('input[name=_wp_http_referer]');

            var _wp_http_referer = $_wp_http_referer.val();
            _wp_http_referer = remove_query_arg( 'tab', _wp_http_referer );
            $_wp_http_referer.val( add_query_arg( 'tab', $(this).attr('data-tab'), _wp_http_referer ) );

            // Change hidden tab input
            $(this).closest('form').find('input[name=active_tab]').val( $(this).attr('data-tab') );
        }
	});


    /**
     * Users autocomplete field
     *
     */
    $('.slicewp-field-users-autocomplete').each( function() {

        $this = $(this);

        $this.autocomplete({
            source    : ajaxurl + '?action=slicewp_action_ajax_get_users&term=' + $this.val() + '&affiliates=' + $this.data('affiliates') + '&return_value=' + ( typeof $this.data('return-value') != 'undefined' ? $this.data('return-value') : '' ) + '&slicewp_token=' + $('#slicewp_user_search_token').val(),
            minLength : 2,
            delay     : 350,
            search    : function( e, ui ) {
                $this.after( '<div class="spinner"></div>' )
            },
            response  : function( e, ui ) {
                $this.siblings( '.spinner' ).remove();

                if( ui.content.length == 0 )
                    ui.content.push( { value : '', label : 'No results found.' } );
                
            },
            select    : function( e, ui ) {
                e.preventDefault();

                if( ui.item.value != '' ) {
                    $this.val( ui.item.label );
                    $this.siblings('input[type=hidden]').first().val( ui.item.value );
                }

            }
        });

    });

    $(document).on( 'focus', '.slicewp-field-users-autocomplete', function() {

        if( $(this).siblings('[type=hidden]').first().val() == '' )
            $(this).autocomplete('search');

    });

    $(document).on( 'change', '.slicewp-field-users-autocomplete', function() {
        
        if ( $(this).val() == '' ) {

            $(this).siblings('input[type=hidden]').first().val('');

        }
        
    });

    /**
     * Show/hide commission rate when rate type isn't selected
     *
     */
    $(document).on( 'change', '.slicewp-field-wrapper-commission-rate select', function() {

        var $select = $(this);

        if( $select.find('option:selected').val() == '' )
            $select.closest('.slicewp-field-wrapper-commission-rate').find('input').hide();
        else
            $select.closest('.slicewp-field-wrapper-commission-rate').find('input').show();

    });

    $('.slicewp-field-wrapper-commission-rate select').trigger( 'change' );


    /**
     * Page: Add New Affiliate
     *
     */
    $(document).on( 'change', '.slicewp-wrap-add-affiliate #slicewp-affiliate-status', function() {

        if( $(this).val() == 'active' ) {

            $(this).closest('.slicewp-field-wrapper').removeClass('slicewp-last');
            $('#slicewp-affiliate-welcome-email').closest('.slicewp-field-wrapper').show();

        } else {

            $(this).closest('.slicewp-field-wrapper').addClass('slicewp-last');
            $('#slicewp-affiliate-welcome-email').closest('.slicewp-field-wrapper').hide();

        }

    });


    /**
     * Page: Add New Creative
     *
     */
    $(document).on( 'change', '#slicewp-creative-type', function() {

        $('#slicewp-creative-landing-url').closest('.slicewp-field-wrapper').show();

        if( $(this).val() == 'image' ) {

            $('#slicewp-creative-image').closest('.slicewp-field-wrapper').show();
            $('#slicewp-creative-alt-text').closest('.slicewp-field-wrapper').show();
            $('#slicewp-creative-text').closest('.slicewp-field-wrapper').hide();

        } else {

            $('#slicewp-creative-image').closest('.slicewp-field-wrapper').hide();
            $('#slicewp-creative-alt-text').closest('.slicewp-field-wrapper').hide();
            $('#slicewp-creative-text').closest('.slicewp-field-wrapper').show();

            $text_field_wrapper = $('#slicewp-creative-text').closest('.slicewp-field-wrapper');

            // Display text versus long-text
            if( $(this).val() == 'text' ) {

                $text_field_wrapper.find('input').attr( { 'id' : 'slicewp-creative-text', 'name' : 'text' } ).show();
                $text_field_wrapper.find('textarea').removeAttr( 'id name' ).hide();

            }

            if( $(this).val() == 'long_text' ) {

                $text_field_wrapper.find('textarea').attr( { 'id' : 'slicewp-creative-text', 'name' : 'text' } ).show();
                $text_field_wrapper.find('input').removeAttr( 'id name' ).hide();

                $('#slicewp-creative-landing-url').closest('.slicewp-field-wrapper').hide();

            }

        }

    });

    $('#slicewp-creative-type').trigger('change');


    /**
     * Page: Settings
     *
     */
    $(document).on( 'change', '#slicewp-email-template', function() {

        if( $(this).val() == '' )
            $('#slicewp-email-logo').closest( '.slicewp-field-wrapper' ).hide();
        else
            $('#slicewp-email-logo').closest( '.slicewp-field-wrapper' ).show();

    });

    $('#slicewp-email-template').trigger('change');


    /**
     * Page: Settings - shows/hides commission types based on the enabled integration
     *
     */
    $(document).on( 'change', '[id^="slicewp-integration-switch-"]', function() {

        var commission_types = [];

        // Get all commission types from all integrations
        $('[id^="slicewp-integration-switch-"]').each( function() {

            if( $(this).is( ':checked' ) ) {

                var supports = JSON.parse( $(this).attr( 'data-supports' ) );

                if( typeof supports['commission_types'] != 'undefined' ) {
                    commission_types = commission_types.concat( supports['commission_types'] );
                }

            }

        });

        // Remove duplicates
        commission_types = commission_types.filter( function( elem, pos, arr ) {
            return arr.indexOf( elem ) == pos;
        });

        // Make sure the "sale" type exists if the array is empty
        if( commission_types.length == 0 )
            commission_types.push( 'sale' );
        
        // Show/hide the commission types
        $('[id^="slicewp-commission-rate-"]').closest( '.slicewp-field-wrapper' ).hide();

        commission_types.forEach( function( commission_type ) {

            $('[id="slicewp-commission-rate-' + commission_type + '"]').closest( '.slicewp-field-wrapper' ).show();

        });

        // We need to trigger this change as there might be fields that have visibility dependent on this, for example the sale commission basis
        $('[id^="slicewp-commission-rate-"]').siblings('select').trigger( 'change' );

    });
    
    $('[id^="slicewp-integration-switch-"]').first().trigger( 'change' );


    /**
     * Page: Settings - show/hide fixed amount sale commission basis field
     *
     */
    $(document).on( 'change', '[name="settings[commission_rate_type_sale]"], [name="commission_rate_type_sale"]', function() {

        if( $(this).val() == 'fixed_amount' && $(this).closest('.slicewp-field-wrapper').css( 'display' ) != 'none' )
            $('#slicewp-fixed-amount-commission-basis').closest('.slicewp-field-wrapper').show();

        else
            $('#slicewp-fixed-amount-commission-basis').closest('.slicewp-field-wrapper').hide();

        $(this).closest('.slicewp-card').find('.slicewp-field-wrapper').removeClass('slicewp-last');
        $(this).closest('.slicewp-card').find('.slicewp-field-wrapper:visible').last().addClass('slicewp-last');

        console.log( $(this).closest('.slicewp-field-wrapper:visible') )

    });

    $('[name="settings[commission_rate_type_sale]"], [name="commission_rate_type_sale"]').trigger( 'change' );


    /**
     * Page: Settings - change the thousands and decimal separators on currency change
     *
     */
    $(document).on( 'change', '[name="settings[active_currency]"]', function(e) {

        if( typeof slicewp_currencies != 'undefined' && typeof slicewp_currencies[$(this).val()] != 'undefined' ) {

            $('[name="settings[currency_thousands_separator]"]').val( slicewp_currencies[$(this).val()]['thousands_separator'] );
            $('[name="settings[currency_decimal_separator]"]').val( slicewp_currencies[$(this).val()]['decimal_separator'] );

        }

    });


    /**
     * Media Library Browser
     *
     */

    var frame;

	$('.slicewp-image-select').on('click', function(e) {
		
		e.preventDefault();

		$btn_select = $(this);

		// If the media frame already exists, reopen it.
		if ( frame ) {
			frame.open();
			return;
		}

		// Create a new media frame
		frame = wp.media({
			title: 'Choose Image',
			button: {
				text: 'Use Image'
			},
			multiple: false
		});

		// Select image from media frame
		frame.on( 'select', function() {
      
			var attachment = frame.state().get('selection').first().toJSON();

            $btn_select.siblings('[type="text"]').val( attachment.url );

	    });

		frame.open();

    });


    /**
	 * Show/Hide email notification fields in Settings
	 *
	 */
    $(document).on( 'change', '#slicewp-email-notification', function() {
        
        var option_value = $(this).val().replace(/_/g, "-");
        var email_notification = $(this).val();
        
        $('.slicewp-settings-email-wrapper').append('<div class="slicewp-overlay"><div class="spinner"></div></div>');
        
        setTimeout( function() {

            url = window.location.href;
            url = remove_query_arg( 'email_notification', url );
            url = add_query_arg( 'email_notification', email_notification, url );

            window.history.replaceState( {}, '', url );

            $('.slicewp-settings-email-wrapper').find('.slicewp-overlay').remove();
            $('.slicewp-settings-email-wrapper').hide();
            $('#slicewp-settings-email-wrapper-' + option_value ).show();
            
        }, 500 );

    });


    /**
     * Page: Review Affiliate
     *
     */
    $(document).on( 'change', '#slicewp-affiliate-application-status', function() {

        if( $(this).val() == 'application_approved' ) {

            $('#slicewp-send-email-notification').prop('checked', true);
            $('#slicewp-affiliate-reject-reason').closest('.slicewp-field-wrapper').hide();
            $('#slicewp-approve-affiliate').show();
            $('#slicewp-reject-affiliate').hide();

            $('#slicewp-send-email-notification').closest('.slicewp-field-wrapper').addClass('slicewp-last');
            $('#slicewp-affiliate-reject-reason').closest('.slicewp-field-wrapper').removeClass('slicewp-last');
            
            $('#slicewp-link-approve-email-notification').show();
            $('#slicewp-link-reject-email-notification').hide();

        } else {

            $('#slicewp-send-email-notification').prop('checked', true);
            $('#slicewp-affiliate-reject-reason').closest('.slicewp-field-wrapper').show();
            $('#slicewp-approve-affiliate').hide();
            $('#slicewp-reject-affiliate').show();

            $('#slicewp-send-email-notification').closest('.slicewp-field-wrapper').removeClass('slicewp-last');
            $('#slicewp-affiliate-reject-reason').closest('.slicewp-field-wrapper').addClass('slicewp-last');

            $('#slicewp-link-approve-email-notification').hide();
            $('#slicewp-link-reject-email-notification').show();

        }

    });

    $('#slicewp-affiliate-application-status').trigger('change');


    $(document).on( 'change', '#slicewp-send-email-notification', function() {

        if( $(this).prop('checked') == false && $('#slicewp-affiliate-application-status').val() == 'application_rejected' ) {

            $('#slicewp-affiliate-reject-reason').closest('.slicewp-field-wrapper').hide();

            $('#slicewp-send-email-notification').closest('.slicewp-field-wrapper').addClass('slicewp-last');
            $('#slicewp-affiliate-reject-reason').closest('.slicewp-field-wrapper').removeClass('slicewp-last');

        }
        else if ( $(this).prop('checked') == true && $('#slicewp-affiliate-application-status').val() == 'application_rejected' )
        {
            
            $('#slicewp-affiliate-reject-reason').closest('.slicewp-field-wrapper').show();

            $('#slicewp-send-email-notification').closest('.slicewp-field-wrapper').removeClass('slicewp-last');
            $('#slicewp-affiliate-reject-reason').closest('.slicewp-field-wrapper').addClass('slicewp-last');

        }

    });
    

    $(document).on( 'change', '#slicewp-payment-status', function() {

        status = $('#slicewp-payment-status :selected').text();
        $('#slicewp-review-payment-button').attr( 'data-confirmation-message', "Are you sure you want to mark the payment as " + status + "?" );

    });

    
    /**
     * Register and deregister customer website from our servers
     *
     */
    $(document).on( 'click', '#slicewp-register-license-key', function(e) {

        e.preventDefault();

        if( $('#slicewp-is-website-registered').length == 0 )
            return false;

        var action = ( $('#slicewp-is-website-registered').val() == 'false' ? 'register' : 'deregister' );

        $button = $(this);

        // Exit if button is disabled
        if( $button.hasClass( 'slicewp-disabled' ) )
            return false;

        // Exit if the license key field is empty
        if( $button.siblings( 'input[type="text"]' ).val() == '' ) {
            $button.siblings( 'input[type="text"]' ).focus();
            return false;
        }

        // Disable license key field
        $button.siblings( 'input[type="text"]' ).attr( 'disabled', 'true' );

        // Disable the button
        $button.addClass( 'slicewp-disabled' );

        // Remove the label
        $button.find( 'span' ).hide();
        
        // Add a spinner
        if( $button.find( '.spinner' ).length == 0 )
            $button.append( '<div class="spinner"></div>' );

        // Prepare AJAX call data
        var data = {
            action        : 'slicewp_action_ajax_' + action + '_website',
            slicewp_token : $('#slicewp_token').val(),
            license_key   : $('#slicewp-license-key').val()
        }

        // Make AJAX call
        $.post( ajaxurl, data, function( response ) {

            // Remove API message
            $button.closest( '.slicewp-field-wrapper' ).find( '.slicewp-api-action-message' ).remove();

            // Re-enable the button
            $button.siblings( 'input[type="text"]' ).removeAttr( 'disabled' );

            // Re-enable the button
            $button.removeClass( 'slicewp-disabled' );

            // Remove spinner
            $button.find( '.spinner' ).remove();
            
            if( response.success == false ) {

                if( action == 'register' )
                    $button.find( 'span.slicewp-register' ).show();

                if( action == 'deregister' )
                    $button.find( 'span.slicewp-deregister' ).show();

                $button.closest( '.slicewp-field-wrapper' ).append( '<div class="slicewp-api-action-message slicewp-api-action-message-error">' + response.data.message + '</div>' );
                $button.closest( '.slicewp-field-wrapper' ).find( '.slicewp-api-action-message' ).fadeIn();

            } else {

                if( action == 'register' )
                    $button.find( 'span.slicewp-deregister' ).show();

                if( action == 'deregister' )
                    $button.find( 'span.slicewp-register' ).show();

                $button.closest( '.slicewp-field-wrapper' ).append( '<div class="slicewp-api-action-message slicewp-api-action-message-success">' + response.data.message + '</div>' );
                $button.closest( '.slicewp-field-wrapper' ).find( '.slicewp-api-action-message' ).fadeIn();

                if( action == 'register' )
                    $('#slicewp-is-website-registered').val( 'true' );

                if( action == 'deregister' )
                    $('#slicewp-is-website-registered').val( 'false' );

                if( action == 'deregister' )
                    $button.siblings( 'input[type="text"]' ).val( '' );

            }

        });

    });


    /**
     * Payout method selection
     *
     */
    $(document).on( 'change', '.slicewp-field-wrapper-payout-method select', function() {

        var $submit = $(this).siblings('button');

        // Disable the submit button
        if( $(this).val() != '' )
            $submit.attr( 'disabled', false );
        else
            $submit.attr( 'disabled', true );

        // Set the button's label
        $submit.find('.slicewp-button-label').hide();

        if( $(this).val() == '' || $(this).val() == 'manual' )
            $submit.find('.slicewp-button-label-manual').show();
        else
            $submit.find('.slicewp-button-label-other').show();

        // Set the onclick parameter for the button
        $submit.removeAttr( 'onclick' );

        if( typeof slicewp_payout_methods_messages != 'undefined' && typeof slicewp_payout_methods_messages[$(this).val()] != 'undefined' && typeof slicewp_payout_methods_messages[$(this).val()]['payout_form_confirmation_bulk_payments'] != 'undefined' )
            $submit.attr( 'data-confirmation-message', slicewp_payout_methods_messages[$(this).val()]['payout_form_confirmation_bulk_payments'] );            

    });

    $('.slicewp-field-wrapper-payout-method select').trigger( 'change' );


    /**
     * Handles the payout Pay Affiliates button confirmation
     *
     */
    $(document).on( 'click', '.slicewp-field-wrapper-payout-method .slicewp-button-primary', function() {

        if( $(this).hasClass( 'slicewp-disabled' ) )
            return false;

        var confirmation = confirm( $(this).attr( 'data-confirmation-message' ) );

        // Disable button, remove the button label and add a loading spinner
        if( confirmation ) {

            $(this).addClass( 'slicewp-disabled' );

            $(this).find( '.slicewp-button-label' ).hide();

            $(this).append( '<div class="spinner"></div>' );

        }

        return confirmation;

    });


    /**
     * Disable submit buttons and add spinners next to them
     *
     */
    $(document).on( 'click', '.slicewp-form-submit', function(e) {

        if( $(this).hasClass( 'slicewp-disabled' ) ) {
            e.preventDefault();
            return false;
        }

        if( $(this).next().hasClass( '.slicewp-form-submit-spinner' ) ) {
            e.preventDefault();
            return false;
        }

        // Handle confirmation cases
        var confirmation = true;

        if( typeof $(this).attr( 'data-confirmation-message' ) != 'undefined' ) {

            confirmation = confirm( $(this).attr( 'data-confirmation-message' ) );

        }

        if( confirmation == false ) {
            return false;
        }

        // Remove any onclick
        $(this).removeAttr( 'onclick' );

        // Disable the button
        $(this).addClass( 'slicewp-disabled' );

        // Add the spinner
        $(this).after( '<div class="spinner slicewp-form-submit-spinner"></div>' );

    });


    /**
     * Makes an AJAX call to insert a new note into the database
     *
     */
    $(document).on( 'click', '.slicewp-add-note', function(e) {

        e.preventDefault();

        var $button = $(this);

        if( $button.hasClass( 'slicewp-disabled' ) )
            return false;

        if( $('#slicewp-note-content').val() == '' ) {
            $('#slicewp-note-content').focus();
            return false;
        }

        // Add animations
        $button.addClass( 'slicewp-disabled' );
        $('#slicewp-note-content').attr( 'disabled', true );
        $button.siblings( '.spinner' ).css( 'visibility', 'visible' ).css( 'opacity', 1 );

        // Prepare AJAX call data
        var data = {
            action         : 'slicewp_action_ajax_insert_note',
            slicewp_token  : $('#slicewp_token_notes').val(),
            object_context : $('[name="note_object_context"]').val(),
            object_id      : $('[name="note_object_id"]').val(),
            note_content   : $('#slicewp-note-content').val()
        }

        // Make AJAX call
        $.post( ajaxurl, data, function( response ) {

            if( response == 0 )
                return false;

            // Remove the no notes message
            if( $('#slicewp-notes-wrapper .slicewp-notes-empty').is( ':visible' ) )
                $('#slicewp-notes-wrapper .slicewp-notes-empty').stop( true, false ).animate({ paddingTop: 0, paddingBottom: 0, height: 'toggle', opacity: 'toggle' }, 250 );

            // Wait for the remove notes animation to finish
            setTimeout( function() {

                // Add the note to the top of the list
                $('#slicewp-notes-wrapper .slicewp-card-header').after( response );
                $('#slicewp-notes-wrapper .slicewp-note').removeClass( 'slicewp-first' )
                    .first().addClass( 'slicewp-first' )
                    .css( 'padding-top', 0 ).css( 'padding-bottom', 0 )
                    .stop( true, false ).animate({ paddingTop: 20, paddingBottom: 20, height: 'toggle', opacity: 'toggle' }, 250 );

                // Remove the animations
                $button.removeClass( 'slicewp-disabled' );
                $('#slicewp-note-content').attr( 'disabled', false );
                $button.siblings( '.spinner' ).css( 'visibility', 'hidden' ).css( 'opacity', 0 );

                // Empty the textarea
                $('#slicewp-note-content').val( '' );

                // Update notes count
                $('#slicewp-notes-wrapper .slicewp-notes-count').html( parseInt( $('#slicewp-notes-wrapper .slicewp-note').length ) );

                // Set the IDs of the notes in the hidden fields
                var note_ids = [];

                $('#slicewp-notes-wrapper .slicewp-note').each( function() {
                    note_ids.push( parseInt( $(this).attr( 'data-note-id' ) ) )
                });

                $('[name="note_ids"]').val( note_ids.join( ',' ) );

            }, 250 );

        });

    });

    
    /**
     * Makes an AJAX call to delete a note from the database
     *
     */
    $(document).on( 'click', '.slicewp-note-delete', function(e) {

        e.preventDefault();

        // Handle confirmation cases
        var confirmation = true;

        if( typeof $(this).attr( 'data-confirmation-message' ) != 'undefined' ) {

            confirmation = confirm( $(this).attr( 'data-confirmation-message' ) );

        }

        if( confirmation == false ) {
            return false;
        }

        var $link = $(this);

        $link.blur();

        // Add animations
        if( $link.find( '.slicewp-note-loading-overlay' ).length == 0 ) {
            $link.closest( '.slicewp-note' ).append( '<div class="slicewp-note-loading-overlay"><div class="spinner"></div></div>' );
            $link.closest( '.slicewp-note' ).find( '.slicewp-note-loading-overlay' ).fadeIn( 100 );
            $link.closest( '.slicewp-note' ).find( '.spinner' ).fadeIn( 100 );
        }

        // Prepare AJAX call data
        var data = {
            action         : 'slicewp_action_ajax_delete_note',
            slicewp_token  : $('#slicewp_token_notes').val(),
            note_id        : $link.closest( '.slicewp-note' ).attr( 'data-note-id' )
        }

        // Make AJAX call
        $.post( ajaxurl, data, function( response ) {

            if( response == 0 )
                return false;

            // Animate the removal of the note
            $link.closest( '.slicewp-note' ).find( '.spinner' ).stop( true, false ).animate({ opacity: 0}, 100 );
            $link.closest( '.slicewp-note' ).stop( true, false ).animate({ paddingTop: 0, paddingBottom: 0, height: 'toggle', opacity: 'toggle' }, 250 );

            setTimeout( function() {

                // Remove the actual note
                $link.closest( '.slicewp-note' ).remove();

                // Add the "slicewp-first" class to the first note
                $('#slicewp-notes-wrapper .slicewp-note').removeClass( 'slicewp-first' ).first().addClass( 'slicewp-first' );

                // Update notes count
                $('#slicewp-notes-wrapper .slicewp-notes-count').html( parseInt( $('#slicewp-notes-wrapper .slicewp-note').length ) );

                if( $('#slicewp-notes-wrapper .slicewp-note').length == 0 )
                    $('#slicewp-notes-wrapper .slicewp-notes-empty').stop( true, false ).animate({ paddingTop: 20, paddingBottom: 20, height: 'toggle', opacity: 'toggle' }, 250 );

                // Set the IDs of the notes in the hidden fields
                var note_ids = [];

                $('#slicewp-notes-wrapper .slicewp-note').each( function() {
                    note_ids.push( parseInt( $(this).attr( 'data-note-id' ) ) )
                });

                $('[name="note_ids"]').val( note_ids.join( ',' ) );

            }, 250 );

        });

    });


    /**
     * Shows all notes
     *
     */
    $(document).on( 'click', '.slicewp-notes-view-all a', function(e) {

        e.preventDefault();

        $('.slicewp-note.slicewp-note-hidden')
            .css( 'padding-top', 0 ).css( 'padding-bottom', 0 )
            .stop( true, false ).animate({ paddingTop: 20, paddingBottom: 20, height: 'toggle', opacity: 'toggle' }, 100 );

        $('.slicewp-notes-view-all')
            .stop( true, false ).animate({ paddingTop: 0, paddingBottom: 0, height: 'toggle', opacity: 'toggle' }, 100 );

        setTimeout( function() {

            $('.slicewp-note.slicewp-note-hidden').removeClass( 'slicewp-note-hidden' );

        }, 100 );

    });


    /**
     * Integrations options fields: Shows/hides the commission rate based on the commission rate type selected
     *
     */
    $(document).on( 'change', '.slicewp-option-field-wrapper-commission-rate-type select', function() {

        var wrapper_classes = $(this).closest('.slicewp-option-field-wrapper')[0].className.split(' ');
        var commission_type = '';

        // Try to get the commission type
        for ( var i in wrapper_classes ) {

            if( wrapper_classes[i].indexOf( 'slicewp-commission-type-' ) == 0 )
                commission_type = wrapper_classes[i].replace( 'slicewp-commission-type-', '' );

        }

        // Hide or show the commission rate
        if ( commission_type != '' ) {

            // Grab the correct parent
            if ( $(this).closest('.slicewp-options-group').length == 0 )
                var $parent = $(this).closest('.slicewp-option-field-wrapper').parent();
            else
                var $parent = $(this).closest('.slicewp-options-group');

            if ( $(this).val() == '' ) {

                $parent.find( '.slicewp-option-field-wrapper-commission-rate.slicewp-commission-type-' + commission_type ).hide();

            } else {

                if ( ! $parent.find( '.slicewp-option-field-disable-commissions' ).is( ':checked' ) )
                    $parent.find( '.slicewp-option-field-wrapper-commission-rate.slicewp-commission-type-' + commission_type ).show();

            }

        }

    });

    /**
     * Integrations options fields: Shows/hides the elements with "slicewp-hide-if-disabled-commissions" class
     *                              when commissions are disabled for the options groups wrapper
     *
     */
    $(document).on( 'click', 'input[type="checkbox"].slicewp-option-field-disable-commissions', function() {

        show_hide_disabled_commissions_elements( $(this) );

    });

    $('input[type="checkbox"].slicewp-option-field-disable-commissions').each( function() {

        show_hide_disabled_commissions_elements( $(this) );

    });


    /**
     * Integrations options fields - WooCommerce: Show/hide the options groups if all commissions are
     *                                            disabled for the product when changing the product type
     *
     */
    $(document).on( 'change', 'body.post-type-product #product-type', function() {

        show_hide_product_subscription_elements( ( $(this).val() == 'subscription' || $(this).val() == 'variable-subscription' ? 'subscription' : 'product' ) );

    });

    show_hide_product_subscription_elements( ( $('body.post-type-product #product-type').val() == 'subscription' || $('body.post-type-product #product-type').val() == 'variable-subscription' ? 'subscription' : 'product' ) );


    /**
     * Integrations options fields - WooCommerce: Trigger the product type select when opening
     *
     */
    $(document).ajaxComplete( function( event, request, settings ) {

        if( typeof settings.data != 'undefined' ) {

            var params = new URLSearchParams( settings.data );

            if( params.get( 'action' ) == 'woocommerce_load_variations' ) {

                // Show/hide elements that are dependent on commissions being enabled
                $('.woocommerce_variable_attributes input[type="checkbox"].slicewp-option-field-disable-commissions').each( function() {

                    show_hide_disabled_commissions_elements( $(this) );

                });

                // Show/hide elements that depend on a product type
                show_hide_product_subscription_elements( ( $('#product-type').val() == 'subscription' || $('#product-type').val() == 'variable-subscription' ? 'subscription' : 'product' ) );

            }

        }

    });


    /**
     * Integrations options fields - EDD: Show/hide the options groups if all commissions are
     *                                    disabled for the product when changing the product type
     *
     */
    $(document).on( 'change', '#edd_recurring', function() {

        // Show/hide elements that depend on a product type
        show_hide_product_subscription_elements( ( $('#edd_recurring').val() == 'yes' ? 'subscription' : 'product' ) );

    });


    /**
     * Shows/hides elements that are dependent on commissions being enabled
     *
     */
    function show_hide_disabled_commissions_elements( $checkbox ) {

        // Grab the correct parent
        if( $checkbox.closest('.slicewp-options-groups-wrapper').length == 0 || $checkbox.closest('.slicewp-options-group').length == 0 )
            var $parent = $checkbox.closest('.slicewp-option-field-wrapper').parent();
        else
            var $parent = $checkbox.closest('.slicewp-options-groups-wrapper');

        if( $checkbox.is( ':checked' ) )
            $parent.find( '.slicewp-hide-if-disabled-commissions' ).hide();
        
        else {

            // Show all elements that should be hidden when commissions are disabled
            $parent.find( '.slicewp-hide-if-disabled-commissions' ).show();

            // Trigger the commission rate type change, so we hide the associated rates if needed
            $parent.find( '.slicewp-option-field-wrapper-commission-rate-type select' ).trigger( 'change' );

        }

    }


    /**
     * Shows/hides elements that depend on a product type
     *
     */
    function show_hide_product_subscription_elements( elements ) {

        if( elements == 'subscription' ) {

            $('.slicewp-show-if-product').addClass( 'slicewp-hidden' );
            $('.slicewp-show-if-subscription').removeClass( 'slicewp-hidden' );

        } else {

            $('.slicewp-show-if-product').removeClass( 'slicewp-hidden' );
            $('.slicewp-show-if-subscription').addClass( 'slicewp-hidden' );

        }

    }

    /**
     * Makes all cards on the Upgrade to Premium page the same height
     *
     */
    $(window).on( 'resize load', function() {

        var rows = $('.slicewp-wrap-upgrade-to-premium #slicewp-primary .slicewp-row');

        rows.each( function() {

            var min_height = 0;

            $(this).find( '.slicewp-card-inner' ).css( 'min-height', min_height );
            
            $(this).find( '.slicewp-card-inner' ).each( function() {

                if( $(this).height() > min_height )
                    min_height = $(this).height();

            });

            $(this).find( '.slicewp-card-inner' ).css( 'min-height', min_height );

        });

    });

});