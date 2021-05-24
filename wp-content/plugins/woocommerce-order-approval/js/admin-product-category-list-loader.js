jQuery(document).ready(function()
{
	wcoa_initProductSelector(".wcoa_product_selector");
	wcoa_initCategorySelector(".wcoa_category_selector");
	wcoa_initTagSelector(".wcoa_tag_selector");
});
function wcoa_initProductSelector(elem)
{
	jQuery(elem).select2(
			{
			  width: "resolve",
			  closeOnSelect: false,
			  allowClear: true,
			 //placeholder: wcpfc.select2_selected_value == "" ? wcpfc.select2_placeholder : wcpfc.selected_user_info_label+wcpfc.select2_selected_value,
			  ajax: {
						url: ajaxurl,
						dataType: 'json',
						delay: 250,
						tags: "true",
						multiple: true,
						data: function (params) {
						  return {
							search_string: params.term, // search term
							page: params.page || 1,
							action: 'wcoa_get_product_list'
						  };
						},
						processResults: function (data, params) 
						{
						  //console.log(params);
						 
						   return {
							results: jQuery.map(data.results, function(obj) 
							{
								const sku = obj.product_sku == null ? "N/A" : obj.product_sku;
								return { id: obj.id, text: "<strong>(SKU: "+sku+" ID: "+obj.id+")</strong> "+obj.product_name };
							}),
							pagination: {
										  'more': typeof data.pagination === 'undefined' ? false : data.pagination.more
										}
							};
						},
						cache: true
			  },
			  escapeMarkup: function (markup) { return markup; }, // let our custom formatter work
			  minimumInputLength: 0,
			  templateResult: wcoa_formatRepo,  //product-fields-configurator-misc.js
			  templateSelection:  wcoa_formatRepoSelection  //product-fields-configurator-misc.js
			});
		
	//Needed to trigger the event to resize product/category selection box
	jQuery(elem).on('select2:select', function (e) {
				window.scrollBy(0, 1);
	});
}
function wcoa_initCategorySelector(elem)
{
	jQuery(elem).select2(
		{
			 width: "resolve",
			closeOnSelect: false,
			allowClear: true,
			ajax: {
					url: ajaxurl,
					dataType: 'json',
					delay: 250,
					multiple: true,
					data: function (params) {
					  return {
						product_category: params.term, // search term
						page: params.page,
						action: 'wcoa_get_category_list'
					  };
					},
					processResults: function (data, page) 
					{
				   
					   return {
						results: jQuery.map(data, function(obj) {
							return { id: obj.id, text: obj.category_name };
							}),
						pagination: {
									  'more': typeof data.pagination === 'undefined' ? false : data.pagination.more
									}
						
						};
					},
					cache: true
		  },
		  escapeMarkup: function (markup) { return markup; }, // let our custom formatter work
		  minimumInputLength: 0,
		  templateResult: wcoa_formatRepo, 
		  templateSelection: wcoa_formatRepoSelection  
		});
		
	//Needed to trigger the event to resize product/category selection box
	jQuery(elem).on('select2:select', function (e) {
				window.scrollBy(0, 1);
	});
}
function wcoa_initTagSelector(elem)
{
	jQuery(elem).select2(
		{
			width: "resolve",
			closeOnSelect: false,
			allowClear: true,
			ajax: {
					url: ajaxurl,
					dataType: 'json',
					delay: 250,
					multiple: true,
					data: function (params) {
					  return {
						product_tag: params.term, // search term
						page: params.page,
						action: 'wcoa_get_tag_list'
					  };
					},
					processResults: function (data, page) 
					{
				   
					   return {
						results: jQuery.map(data, function(obj) {
							return { id: obj.id, text: obj.category_name };
							}),
						pagination: {
									  'more': typeof data.pagination === 'undefined' ? false : data.pagination.more
									}
						
						};
					},
					cache: true
		  },
		  escapeMarkup: function (markup) { return markup; }, // let our custom formatter work
		  minimumInputLength: 0,
		  templateResult: wcoa_formatRepo, 
		  templateSelection: wcoa_formatRepoSelection  
		});
		
	//Needed to trigger the event to resize product/category selection box
	jQuery(elem).on('select2:select', function (e) {
				window.scrollBy(0, 1);
	});
}
function wcoa_formatRepo (repo) 
{
	if (repo.loading) return repo.text;
	
	var markup = '<div class="clearfix">' +
			'<div class="col-sm-12">' + repo.text + '</div>';
    markup += '</div>'; 
	
    return markup;
}

function wcoa_formatRepoSelection (repo) 
{
  return repo.full_name || repo.text;
}