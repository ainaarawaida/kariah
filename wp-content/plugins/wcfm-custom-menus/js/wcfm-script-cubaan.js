$wcfm_subscriptions_manage_detail_table = '';
$customer_vendor = '';
$info = '' ; 

jQuery(document).ready(function($) {
	
	$wcfm_subscriptions_manage_detail_table = $('#wcfm-cubaan').DataTable( {
		"processing": true,
		"serverSide": false,
		"responsive": true,
		"lengthMenu": [
            [ 10, 25, 50, -1 ],
            [ '10 rows', '25 rows', '50 rows', 'Show all' ]
        ],
		"dom"       : 'Bfrtip',
		"buttons": [
           
            {
                extend: 'collection',
                text: 'Export',
                buttons: [
                    'copy',
                    'excel',
                    'csv',
                    'pdf',
                    'print'
					
                ]
            },'colvis', 'pageLength'
        ],
	"columnDefs": [{ "targets": 0, "orderable" : false, "visible": true}, { "targets": 1, "orderable" : false, "visible": true }, { "targets": 2, "orderable" : false, "visible": true }, { "targets": 3, "orderable" : false, "visible": true }, { "targets": 4, "orderable" : false, "visible": true }],
	"select": {
		"style": 'multi'
		},
	"order": [[1, 'asc']],

		'ajax': {
			"type"   : "POST",
			"url"    : wcfm_params.ajax_url,
			"data"   : function( d ) {
				d.action          = 'wcfm_ajax_controller',
				d.controller      = 'wcfm-cubaan'
			},
			"complete" : function () {
				// Fire wcfm-orders table refresh complete
				$( document.body ).trigger( 'updated_wcfm-cubaan' );
				
			}
		}
	} );


	
		// Delete Order
		$( document.body ).on( 'updated_wcfm-cubaan', function() {
			
			$('.wcfm_cubaan_delete').each(function() {
				
				$(this).click(function(event) {
					event.preventDefault();
					var rconfirm = confirm( wcfm_dashboard_messages.order_delete_confirm );
					if(rconfirm) deleteWCFMOrder($(this));
					return false;
				});
			});
		});



		function deleteWCFMOrder(item) {
			$('#wcfm-orders_wrapper').block({
				message: null,
				overlayCSS: {
					background: '#fff',
					opacity: 0.6
				}
			});
			var data = {
				action : 'delete_wcfm_order',
				orderid : item.data('orderid')
			}	
			$.ajax({
				type:		'POST',
				url: wcfm_params.ajax_url,
				data: data,
				success:	function(response) {
					$wcfm_subscriptions_manage_detail_table.ajax.reload();
					$('#wcfm-orders_wrapper').unblock();
				}
			});
		}


		
} );


