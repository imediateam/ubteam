jQuery( function($) {
	var cookie_aff   = get_cookie( 'slicewp_aff' );
	var cookie_visit = get_cookie( 'slicewp_visit' );
	var query_aff;
	query_aff = get_query_arg( slicewp.affiliate_keyword );
	query_aff = ( query_aff != '' ? query_aff : get_query_arg_pretty( slicewp.affiliate_keyword ) );
	if( query_aff == '' )
		return false;

	// Prepare the data to register the visit
	var data = {
		action 		 : 'slicewp_register_visit',
		aff    		 : query_aff,
		landing_url  : document.URL,
		referrer_url : document.referrer
	};

	$.post( slicewp_ajaxurl, data, function(response) {
		response = JSON.parse( response );
		if( response.success > 0 ) {
			if( ! cookie_aff || slicewp.affiliate_credit == 'last' ) {
				set_cookie( 'slicewp_aff', response.affiliate_id );
				set_cookie( 'slicewp_visit', response.visit_id );
			}
		} else {
			// For debugging purposes
			console.log( response );
		}
	});
	
	function get_query_arg( arg ) {
		var query = window.location.search.slice(1);
		var parts = query.split('&');
		var obj   = {};

		parts.map( function( part ) {
			part = part.split('=');
			obj[part[0]] = part[1];
		});
		return ( typeof obj[arg] != 'undefined' ? obj[arg] : '' );

	}

	function get_query_arg_pretty( arg ) {

		var path  = window.location.pathname;
		var parts = path.split( '/' );
		var val   = '';
		for( var i = 0; i < parts.length; i++ ) {
			if( parts[i] == arg ) {
				val = parts[i+1];
				break;
			}
		}
		return val;
	}

	function set_cookie( name, value ) {
	    var d = new Date();
	    d.setTime( d.getTime() + ( slicewp.cookie_duration * 24 * 60 * 60 * 1000 ) );
	    var expires = "expires=" + d.toUTCString();
	    document.cookie = name + "=" + value + "; " + expires + "; " + "path=/;";
	}

	function get_cookie( name ) {
	    var name = name + "=";
	    var ca 	 = document.cookie.split(';');
	    for( var i=0; i<ca.length; i++ ) {
	        var c = ca[i];
	        while( c.charAt(0)==' ' ) c = c.substring(1);
	        if(c.indexOf(name) == 0) return c.substring(name.length,c.length);
	    }
	    return false;
	}
});