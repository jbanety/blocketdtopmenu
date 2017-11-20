/**
 * @package     blocketdtopmenu
 *
 * @version     2.2.0
 * @copyright   Copyright (C) 2017 ETD Solutions. Tous droits réservés.
 * @license     https://raw.githubusercontent.com/jbanety/blocketdcustom/master/LICENSE
 * @author      Jean-Baptiste Alleaume http://alleau.me
 */

(function($) {

	var blockEtdTopMenuAdmin = window.blockEtdTopMenuAdmin = {

		initialized: false,
		baseURI: '',
		token: '',
		$container: null,

		init: function(config) {

			this
				.setOptions(config)
				.createContainer()
				.bindEvents()
				.initialized = true;

			this.changeColumns();

            if ($('form.blocketdtopmenu select#type').length > 0 && $('form.blocketdtopmenu select#type').val() != '') {
                this.loadType();
            }

		},

		setOptions: function(config) {

			var self = this;

			if (config) {
				$.each(config, function(k, v) {
					self[k] = v;
				});
			}

			return this;
		},

		createContainer: function() {

			var $typeFormGroup = $('#type').parents('.form-group');

			this.$container = $('<div id="ajax-container" style="min-height:42px"></div>');

			$typeFormGroup.after(this.$container);

			return this;
		},

		bindEvents: function() {

			$('form.blocketdtopmenu select#type').on('change', this.loadType);
			$('form.blocketdtopmenu select[name="params[columns]').on('change', this.changeColumns);

			$('#desc--save').on('click', function(e) {
				e.preventDefault();
				$('form.blocketdtopmenu').submit();
			});

			return this;
		},

		loadType: function() {

			var self = window.blockEtdTopMenuAdmin;

			self.$container.empty();

            if ($('form.blocketdtopmenu select#type').val() == '') {
            	return this;
            }

			self.$container.append($('<div class="col-lg-9 col-lg-offset-3"><i class="icon-refresh icon-spin icon-fw"></i> Chargement en cours...</div>'));

				// On envoi la demande.
				$.ajax(self.baseURI + 'adminajax.php', {
					cache: false,
					async: true,
					dataType: 'json',
					data: 'token=' + self.token + '&loadType&' + self._buildQueryString(),
					success: function(jsonData) {
						if (jsonData.hasError) {
							alert(jsonData.msg);
						} else {
							self.$container.empty();
							self.$container.append(jsonData.html);
						}
					}
				});


			return self;
		},

		changeColumns: function() {

            var selection = parseInt($('form.blocketdtopmenu select[name="params[columns]"]').val());
            var $divs = $('.columns_widths > div');
            $divs.hide();

            var w = (100 / selection).toFixed(5);

            $divs.each(function(i) {
            	if (i < selection) {
                    $(this).width(w+'%');
                    $(this).show();
				}
			});

		},

		_buildQueryString: function() {
			return $.param({
				'type': $('form.blocketdtopmenu select#type').val(),
				'id_link': $('form.blocketdtopmenu input#id_link').val()
			});
		}

	};

})(jQuery);