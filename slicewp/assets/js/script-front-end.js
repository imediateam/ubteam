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
    function add_query_arg( param, value, url ) {

        var re = new RegExp("[\\?&]" + param + "=([^&#]*)"), match = re.exec(url), delimiter, newString;

	    if (match === null) {
	        // append new param
	        var hasQuestionMark = /\?/.test(url); 
	        delimiter = hasQuestionMark ? "&" : "?";
	        newString = url + delimiter + param + "=" + value;
	    } else {
	        delimiter = match[0].charAt(0);
	        newString = url.replace(re, delimiter + param + "=" + value);
	    }

	    return newString;
    }

    /**
	 * Tab Navigation
	 *
	 */
	$('.slicewp-nav-tab').on( 'click', function(e) {

		e.preventDefault();

		// Nav Tab activation
		$('.slicewp-nav-tab').removeClass('slicewp-active');
		$(this).addClass('slicewp-active');

		// Show tab
		$('.slicewp-tab').removeClass('slicewp-active');

		var nav_tab = $(this).attr('data-slicewp-tab');
		$('.slicewp-tab[data-slicewp-tab="' + nav_tab + '"]').addClass('slicewp-active');
		$('input[name=active_tab]').val( nav_tab );


        // Change "tab" query var
        url = window.location.href;
        url = remove_query_arg( 'affiliate-account-tab', url );
        url = add_query_arg( 'affiliate-account-tab', $(this).attr('data-slicewp-tab'), url );

        window.history.replaceState( {}, '', url );

        // Change hidden tab input
        $(this).closest('form').find('input[name=active_tab]').val( $(this).attr('data-slicewp-tab') );

	});


    /**
	 * Copy Creative textarea
	 *
	 */
    $('.slicewp-input-copy').on( 'click', function(e) {

        e.preventDefault();

        $(this).siblings('textarea, input[type=text]').select();

        document.execCommand('copy');

    });


    /**
	 * Checks if the provided URL is valid
     *
     * @param string url
     *
     * @return bool
	 *
	 */
    function is_valid_url( url ) {

        var regex = new RegExp( /^(https?|s):\/\/(((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:)*@)?(((\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5]))|((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?)(:\d*)?)(\/((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)+(\/(([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)*)*)?)?(\?((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)|[\uE000-\uF8FF]|\/|\?)*)?(#((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)|\/|\?)*)?$/i );

        return regex.test( url );

    }

    /**
     * Checks if the two provided URLs are from the same domain
     *
     * @param string base_url
     * @param string custom_url
     *
     * @return bool
     *
     */
    function is_same_domain( base_url, custom_url ) {

        base_url   = base_url.replace('http://','').replace('https://','').replace('www.','').split(/[/?#]/)[0];
        custom_url = custom_url.replace('http://','').replace('https://','').replace('www.','').split(/[/?#]/)[0];

        return ( base_url == custom_url );

    }

    /**
	 * Generate Affiliate Link
	 *
	 */
    $(document).on( 'click', '.slicewp-generate-affiliate-link', function(e) {

        e.preventDefault();

        var site_url   = window.location.origin.replace(/(^\w+:|^)\/\//, '');
        var custom_url = $('#slicewp-affiliate-custom-link-input').val();

        $('#slicewp-affiliate-custom-link-input-empty').hide();
        $('#slicewp-affiliate-custom-link-input-invalid-url').hide();
        $('.slicewp-affiliate-custom-link-output').hide();

        if( ! custom_url ) {

            $('#slicewp-affiliate-custom-link-input-empty').show();

        } else if ( ( ! is_valid_url( custom_url ) ) || ( ! is_same_domain( site_url, custom_url ) ) ) {

            $('#slicewp-affiliate-custom-link-input-invalid-url').show();

        } else {

            var affiliate_keyword = $('#slicewp-affiliate-account').attr('data-affiliate-keyword');
            var affiliate_id      = $('#slicewp-affiliate-account').attr('data-affiliate-id');

            $('#slicewp-affiliate-custom-link-output').val( add_query_arg( affiliate_keyword, affiliate_id, custom_url ) );
            $('.slicewp-affiliate-custom-link-output').show();

        }

    });

});