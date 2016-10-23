jQuery( document ).ready( function($) {

	/* Var */
	var this_iframe = $( "#fx-email-log-iframe" );

	/* Add Reset CSS
	------------------------------------------ */
	var reset_css = '<link type="text/css" rel="stylesheet" href="' + fx_email_log.reset_css + '" />';
	this_iframe.contents().find( 'head' ).html( reset_css );
	$( this_iframe ).on( 'load', function() {
		$( this ).contents().find( 'head' ).html( reset_css );
	});

	/* Open Modal
	------------------------------------------ */
	$( ".fx-email-log-view-email" ).click( function(e) {
		e.preventDefault();
		var email_id = $( this ).data( 'id' );
		$( "#fx-email-log-modal-overlay,#fx-email-log-modal" ).show();
		$( "#fx-email-log-modal-content" ).css( "height", ( $( '#fx-email-log-modal' ).height() - $( '.fx-email-log-modal-close' ).height() ) + "px" );
		$( window ).resize( function(){
			$( "#fx-email-log-modal-content" ).css( "height", ( $( '#fx-email-log-modal' ).height() - $( '.fx-email-log-modal-close' ).height() ) + "px" );
		});

		/* Ajax */
		$.ajax({
			type: "POST",
			url: ajaxurl,
			data:{
				action     : 'fx_email_log_view_content',
				nonce      : fx_email_log.nonce,
				email_id   : email_id,
			},
			success: function( data ){
				this_iframe.contents().find( 'body' ).html( data );
				$( this_iframe ).on( 'load', function() {
					$( this ).contents().find( 'body' ).html( data );
				});
				return;
			},
		});
		
	});

	/* Close Modal
	------------------------------------------ */
	$( "#fx-email-log-modal-overlay,.fx-email-log-modal-close" ).click( function(e) {
		e.preventDefault();
		$( "#fx-email-log-modal-overlay,#fx-email-log-modal" ).hide();
		this_iframe.contents().find( 'body' ).html( '' );
		$( this_iframe ).on( 'load', function() {
			$( this ).contents().find( 'body' ).html( '' );
		});
	});
});