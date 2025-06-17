//	Account page login tab
jQuery(document).ready(function($) {
		//menu to Select
		$("<select />").appendTo(".woocommerce-MyAccount-navigation");
		$("<option />", {
			"selected": "selected",
			"value"   : "",
			"text"    : "Go to..."
		}).appendTo(".woocommerce-MyAccount-navigation select");
		$(".woocommerce-MyAccount-navigation > ul a").each(function() {
			var el = $(this);
			$("<option />", {
				"value"   : el.attr("href"),
				"text"    : el.text()
			}).appendTo(".woocommerce-MyAccount-navigation select");
		});
		$(".woocommerce-MyAccount-navigation select").change(function() {
			window.location = $(this).find("option:selected").val();
		});

	$( window ).on( 'hashchange', function( e ) {
		$( '#customer_login' ).trigger( 'display_active_tab' );
	} );

	$( '#customer_login' ).on( 'display_active_tab', function() {
		let tab_name = ( window.location.hash ? window.location.hash.replace( '#', '' ) : 'login' );
		$( '.woocommerce-account .fullrow.row-tab-content' ).hide();
		$( '.woocommerce-account .fullrow.row-' + tab_name ).fadeIn();
		$( '.woocommerce-account .tab-button' ).removeClass( 'active' );
		$( '.woocommerce-account .tab-button-' + tab_name ).addClass( 'active' );
	} );
	
	//	Initial run
	$( '#customer_login' ).trigger( 'display_active_tab' );

});


//	View order page
jQuery(document).ready(function($) {
	
	$( '.show-print-ticket-page' ).on( 'click', function ( e ) {
		e.preventDefault();

		let printHTML = $( this )
			.parents( '.ticket-row' )
			.next( '.ticket-row-detail' )
			.find( '.ticket-print-area' );

		let printStyle = $( '#print_style' ).html();

		let printPage = window.open();
			printPage.document.write( '<style>'+ printStyle +'</style>' );
			printPage.document.write( $(printHTML).html() );
			printPage.document.close();
		
		let is_chrome = Boolean(printPage.chrome);

			if ( is_chrome ) {
				printPage.onload = function() { // wait until all resources loaded 
					printPage.focus();// necessary for IE >= 10
					printPage.print();// change window to printPage
					printPage.close();// change window to printPage
				};
			}
			else {
				printPage.document.close(); // necessary for IE >= 10
				printPage.focus(); // necessary for IE >= 10
				printPage.print();
				printPage.close();
			}
	} )
	
	$( '.show-print-ticket-view' ).on( 'click', function ( e ) {
		e.preventDefault();
		$( '.ticket-column-detail' ).removeClass('open');
		$( this )
			.parents( '.ticket-row' )
			.next( '.ticket-row-detail' )
			.find( '.ticket-column-detail' )
			.addClass( 'open' );
	} )
	
	$( '.close-print-ticket-view' ).on( 'click', function ( e ) {
		e.preventDefault();
		$( this )
			.parents( '.ticket-row-detail' )
			.find( '.ticket-column-detail' )
			.removeClass('open');
	} )

});


//	Eticket page
jQuery(document).ready(function($) {

	if ( ! $( 'body' ).hasClass( 'woocommerce-etickets' ) )
		return false;


	let eticketModalData = {
		selected_id: null,
		order_id: null,
		order_item_id: null,
		ajax_nonce: woo_account.ajax_nonce,
		cancel_type: null,
	};


	const openEticketWindow = () => {
		$( '#eticket-modal' ).modal('show');
	};

	const deleteTicket = () => {

		$.ajax({
			url: woo_account.ajax_url,
			type: 'POST',
			dataType: 'json',
			data: {
				action: 'muse_cancel_ticket',
				data: eticketModalData,
			},
		})
		.done(function(data) {

			if ( data.success ) {

				removeCanceledTicketRow( data.ticket_id );

				showNotification( data.notification.title, data.notification.content );

			} else {

				showNotification( data.notification.title, data.notification.content );
			}
		})
		.fail(function() {
			console.log("error");
			// console.log(arguments);
		})
		.always(function() {
			console.log("complete");
			// console.log(arguments);
			closeEticketWindow();
		});
	};

	const closeEticketWindow = () => {
		$( '#eticket-modal' ).modal('hide');
		$( '#eticket-modal-confirmation' ).modal('hide');
	};

	const removeCanceledTicketRow = ( ticket_id ) => {
		$( '.ticket-row[data-ticket_id='+ ticket_id +']' ).remove();
	};

	const showNotification = ( title, content ) => {
		$( '#eticket-modal-notification .modal-title' ).text( title );
		$( '#eticket-modal-notification .modal-body' ).text( content );
		$( '#eticket-modal-notification' ).modal('show');
	};
	

	$( '#eticket-modal' ).on( 'hidden.bs.modal', function (e) {
		eticketModalData.selected_id = null;
		eticketModalData.order_item_id = null;
		eticketModalData.order_id = null;
		eticketModalData.cancel_type = null;
	});

	$( '.ticket-row-action-cancel' ).on( 'click', function( e ) {

		e.preventDefault();

		eticketModalData.selected_id = $( this ).parents( '.ticket-row' ).data( 'ticket_id' );
		eticketModalData.order_id = $( this ).parents( '.ticket-row' ).data( 'order_id' );
		eticketModalData.order_item_id = $( this ).parents( '.ticket-row' ).data( 'order_item_id' );

		const
			clonedTicketInfo1 = $( this ).parents( 'tr' ).clone(),
			clonedTicketInfo2 = $( this ).parents( 'tr' ).clone();

		$( clonedTicketInfo1 ).find( 'td' ).last().remove();
		$( clonedTicketInfo2 ).find( 'td' ).last().remove();

		$( '#eticket-modal table tbody' ).empty();
		$( '#eticket-modal-confirmation table tbody' ).empty();

		$( '#eticket-modal table tbody' ).append( clonedTicketInfo1 );
		$( '#eticket-modal-confirmation table tbody' ).append( clonedTicketInfo2 );

		openEticketWindow();

	} );

	$( '.eticket-modal-actions' ).on( 'click', function( e ) {

		e.preventDefault();

		if ( ! eticketModalData.selected_id ) return false;

		eticketModalData.cancel_type = $( this ).data( 'action' );

		$( '#eticket-modal-confirmation .text-cancel-info' ).hide();
		$( '#eticket-modal-confirmation .text-cancel-action' ).hide();

		$( '' + '#eticket-modal-confirmation .text-cancel-info-' + eticketModalData.cancel_type + '' ).show();
		$( '' + '#eticket-modal-confirmation .text-cancel-action-' + eticketModalData.cancel_type + '' ).show();

		$( '#eticket-modal-confirmation' ).modal('show');
		
	} );

	$( '.eticket-modal-confirmation-continue' ).on( 'click', function( e ) {

		e.preventDefault();

		if ( ! eticketModalData.selected_id ) return false;

		deleteTicket();
		
	} );

	$( '.ticket-row-action-print' ).on( 'click', function( e ) {

		e.preventDefault();

		let url = $( this ).attr( 'href' ),
			printWindow = window.open( url, 'Print', 'toolbar=0, resizable=0');

		printWindow.addEventListener('load', function(){
			printWindow.print();
			printWindow.close();
		}, true);

	} );

});
