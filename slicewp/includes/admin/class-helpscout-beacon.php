<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * Handles the HelpScout Beacon in plugin admin pages
 *
 */
class SliceWP_HelpScout_Beacon {

	/**
	 * Constructor
	 *
	 */
	public function __construct() {

		add_action( 'admin_footer', array( $this, 'add_beacon' ) );

	}


	/**
	 * Adds the beacon to the admin footer
	 *
	 */
	public function add_beacon() {

		if( empty( $_GET['page'] ) )
			return;

		if( false === strpos( $_GET['page'], 'slicewp-' ) )
			return;

?>
		
		<!-- Beacon Script -->
		<script type="text/javascript">!function(e,t,n){function a(){var e=t.getElementsByTagName("script")[0],n=t.createElement("script");n.type="text/javascript",n.async=!0,n.src="https://beacon-v2.helpscout.net",e.parentNode.insertBefore(n,e)}if(e.Beacon=n=function(t,n,a){e.Beacon.readyQueue.push({method:t,options:n,data:a})},n.readyQueue=[],"complete"===t.readyState)return a();e.attachEvent?e.attachEvent("onload",a):e.addEventListener("load",a,!1)}(window,document,window.Beacon||function(){});</script>
		<script type="text/javascript">window.Beacon('init', '70d0867c-94bb-44c2-b627-8c81515eb8f2')</script>

		<!-- Beacon Styles -->
		<style>
			#wpadminbar { z-index: 999; }
			#adminmenuwrap { z-index: 998; }

			.slicewp-helpscout-message { position: fixed; z-index: 1100; bottom: 60px; right: 20px; background: #fff; border: 1px solid rgba( 200, 215, 225, 0.75 ); box-shadow: 0 2px 5px 2px rgba( 200, 215, 225, 0.75 ); border-radius: 7px; padding: 25px; max-width: 350px; box-sizing: border-box; transform: scale(0.85); opacity: 0; visibility: hidden; transition: all 0.2s ease-in-out; }
			.slicewp-helpscout-message.active { bottom: 80px; transform: scale(1); opacity: 1; visibility: visible; }

			.slicewp-helpscout-message p { font-family: 'Verdana'; font-size: 14px; }
			.slicewp-helpscout-message p:first-of-type { margin-top: 0; padding-top: 0; }
			.slicewp-helpscout-message p:last-of-type { margin-bottom: 0; padding-bottom: 0; }
		</style>

		<!-- Beacon Message -->
		<div id="slicewp-helpscout-message" class="slicewp-helpscout-message">
			<p><strong>How can we help you with SliceWP?</strong></p>
		</div>

		<!-- Beacon Welcome Message -->
		<?php if( false === get_option( 'slicewp_helpscout_beacon_welcome', false ) ): ?>
			<div id="slicewp-helpscout-message-welcome" class="slicewp-helpscout-message">
				<p><strong>Need any help with SliceWP?</strong></p>
				<p>If you're having difficulties with SliceWP, or want to know more about the plugin, you can contact us here.</p>
			</div>

			<?php update_option( 'slicewp_helpscout_beacon_welcome', '1' ); ?>
		<?php endif; ?>

		<!-- Beacon Message Scripts -->
		<script>
			jQuery( function($) {

				// Handle welcome message
				$(document).on( 'mouseleave', '.BeaconFabButtonFrame', function() {
					$('#slicewp-helpscout-message-welcome').removeClass( 'active' );
				});

				// Handle hover message
				$(document).on( 'mouseenter', '.BeaconFabButtonFrame', function() {
					if( ! $('#slicewp-helpscout-message').hasClass( 'open' ) && ! $('.slicewp-helpscout-message').hasClass( 'active' ) )
						$('#slicewp-helpscout-message').addClass( 'active' );
				});

				$(document).on( 'mouseleave', '.BeaconFabButtonFrame', function() {
					$('#slicewp-helpscout-message').removeClass( 'active' );
				});

				if( typeof Beacon != 'undefined' ) {

					// Handle welcome message
					if( $('#slicewp-helpscout-message-welcome').length > 0 ) {

						Beacon( 'on', 'ready', function() {
							$('#slicewp-helpscout-message-welcome').addClass( 'active' );
						});

						setTimeout( function() {
							$('#slicewp-helpscout-message-welcome').removeClass( 'active' );
						}, 10000 );

					}

					// Handle hover message
					Beacon( 'on', 'open', function() {
						$('#slicewp-helpscout-message-welcome').removeClass( 'active' );
						$('#slicewp-helpscout-message').removeClass( 'active' );
						$('#slicewp-helpscout-message').addClass( 'open' );
					});

					Beacon( 'on', 'close', function() {
						$('#slicewp-helpscout-message').removeClass( 'open' );
					});

				}
				
			});
		</script>

<?php

	}

}

new SliceWP_HelpScout_Beacon();