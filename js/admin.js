/**
* @package     blocketdtopmenu
*
* @version     1.6
* @copyright   Copyright (C) 2015 Jean-Baptiste Alleaume. Tous droits réservés.
* @license     http://alleau.me/LICENSE
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

			var type = $('form.blocketdtopmenu select#type');
			if (type.length > 0 && type.val() != '') {
				this.loadType();
			}

			this.changeChildrenType();

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

			$('#desc--save').on('click', function(e) {
				e.preventDefault();
				$('form.blocketdtopmenu').submit();
			});

			$('input[name="children_type"]').on('change', this.changeChildrenType);

			return this;
		},

		loadType: function() {

			var self = window.blockEtdTopMenuAdmin;

			self.$container.empty();
			self.$container.append($('<div class="col-lg-9 col-lg-offset-3"><i class="icon-refresh icon-spin icon-fw"></i> Chargement en cours...</div>'));

			if ($('form.blocketdtopmenu select#type').val() != '') {

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
			}
		},

		changeChildrenType: function() {
			var selection = $('input[name="children_type"]:checked').val();
			if (selection == 'modules') {
				$('select#module_hooks').hide().parent('div').hide().prev('label').hide();
				$('select#modules').show().parent('div').show().prev('label').show();
			} else if (selection == 'modulehooks') {
				$('select#modules').hide().parent('div').hide().prev('label').hide();
				$('select#module_hooks').show().parent('div').show().prev('label').show();
			} else {
				$('select#modules, select#module_hooks').hide().parent('div').hide().prev('label').hide();
			}
		},

		_buildQueryString: function() {
			return $.param({
				'type': $('form.blocketdtopmenu select#type').val(),
				'id_link': $('form.blocketdtopmenu input#id_link').val()
			});
		}

	};

})(jQuery);