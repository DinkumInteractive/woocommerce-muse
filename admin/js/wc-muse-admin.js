jQuery(document).ready(function($) {
	if ( ! $( 'body' ).hasClass( 'woocommerce_page_wc-settings' ) ) return false;

	const showSelectedEnvironmentFields = function() {
		const env = $( '#wc-muse-environment' ).val();
		$( '.toggleable-environment' ).parents( 'tr' ).hide();
		$( '.toggleable-environment.environment-' + env ).parents( 'tr' ).show();
	}

	showSelectedEnvironmentFields();

	$( '#wc-muse-environment' ).on( 'change', function() {
		showSelectedEnvironmentFields();
	} );

});