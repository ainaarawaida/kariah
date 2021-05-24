(function( $, dashboardConfig ) {

	'use strict';

	Vue.component( 'jet-video-embed', {
		props: [ 'embed' ],
		template: `
			<div class="jet-engine-module-video"><iframe width="500" height="281" :src="embed" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe></div>
		`
	});

	window.JetEngineDashboard = new Vue( {
		el: '#jet_engine_dashboard',
		data: {
			availableModules: dashboardConfig.available_modules,
			activeModules: dashboardConfig.active_modules,
			componentsList: dashboardConfig.components_list,
			shortcode: {
				component: '',
				meta_field: '',
				page: '',
				field: '',
				form_id: '',
				fields_layout: 'row',
				fields_label_tag: 'div',
				submit_type: 'reload',
				cache_form: '',
				copied: false,
			},
			saving: false,
			result: false,
			errorMessage: '',
			successMessage: '',
			moduleDetails: false,
			showCopyShortcode: undefined !== navigator.clipboard && undefined !== navigator.clipboard.writeText,
		},
		mounted: function() {
			this.$el.className = 'is-mounted';
		},
		computed: {
			generatedShortcode: function() {

				var result = '[jet_engine ';

				if ( ! this.shortcode.component ) {
					return result + ']';
				}

				result += ' component="' + this.shortcode.component + '"';

				switch ( this.shortcode.component ) {

					case 'meta_field':
						result += ' field="' + this.shortcode.meta_field + '"';

						if ( this.shortcode.post_id ) {
							result += ' post_id="' + this.shortcode.post_id + '"';
						}

						break;

					case 'option':
						result += ' page="' + this.shortcode.page + '" field="' + this.shortcode.field + '"';
						break;

					case 'forms':
						result += ' _form_id="' + this.shortcode.form_id + '" fields_layout="' + this.shortcode.fields_layout + '"';
						result += ' fields_label_tag="' + this.shortcode.fields_label_tag + '" submit_type="' + this.shortcode.submit_type + '"';

						if ( this.shortcode.cache_form ) {
							result += ' cache_form="' + this.shortcode.cache_form + '"';
						}

						break;

				}

				result += ']';

				return result;

			},
		},
		methods: {
			isActive: function( module ) {
				return 0 <= this.activeModules.indexOf( module );
			},
			switchActive: function( input, module ) {

				if ( this.isActive( module.value ) ) {
					var index = this.activeModules.indexOf( module.value );
					this.activeModules.splice( index, 1 );
				} else {
					this.activeModules.push( module.value );
				}

			},
			saveModules: function() {

				var self = this;

				self.saving = true;

				jQuery.ajax({
					url: window.ajaxurl,
					type: 'POST',
					dataType: 'json',
					data: {
						action: 'jet_engine_save_modules',
						modules: self.activeModules,
					},
				}).done( function( response ) {

					self.saving = false;

					if ( response.success ) {
						self.result = 'success';

						if ( ! response.data.reload ) {
							self.successMessage = dashboardConfig.messages.saved;
						} else {

							self.successMessage = dashboardConfig.messages.saved_and_reload;

							setTimeout( function() {
								window.location.reload();
							}, 4000 );

						}

					} else {
						self.result = 'error';
						self.errorMessage = 'Error!';
					}

					self.hideNotice();

				} ).fail( function( e, textStatus ) {
					self.result       = 'error';
					self.saving       = false;
					self.errorMessage = e.statusText;
					self.hideNotice();
				} );

			},
			hideNotice: function() {
				var self = this;
				setTimeout( function() {
					self.result       = false;
					self.errorMessage = '';
				}, 8000 );
			},
			copyShortcodeToClipboard: function() {
				var self = this;

				navigator.clipboard.writeText( this.generatedShortcode ).then( function() {
					// clipboard successfully set
					self.shortcode.copied = true;
					setTimeout( function() {
						self.shortcode.copied = false;
					}, 2000 );
				}, function() {
					// clipboard write failed
				} );
			},
			getForms: function( query ) {
				return wp.apiFetch( {
					method: 'get',
					path: dashboardConfig.api_path_search + '?query=' + query + '&post_type=jet-engine-booking',
				} );

			},
		}
	} );

})( jQuery, window.JetEngineDashboardConfig );
