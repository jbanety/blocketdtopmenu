/**!
 * @package     blocketdtopmenu
 *
 * @version     2.2.0
 * @copyright   Copyright (C) 2017 ETD Solutions. Tous droits réservés.
 * @license     https://raw.githubusercontent.com/jbanety/blocketdcustom/master/LICENSE
 * @author      Jean-Baptiste Alleaume http://alleau.me
 */

!function(root, factory) {
    if (typeof define === 'function' && define.amd) {
        define(['jquery'], factory);
    } else {
        factory(root.jQuery);
    }
}(this, function($) {
    'use strict';

    // Selectors cache
    var $body = $('body'),
        $window = $(window);

    // Default options
    var defaults = {
        subToggleSelector: '[data-toggle="subnavbar"]',
        itemDropdownSelector: 'li.dropdown',
        subNavBarSelector: '.subnavbar'
    };

    // Constructor, initialise everything you need here
    var EtdTopMenu = function(element, options) {

        this.$element = $(element);
        this.options = options;

        this.buildOverlay()
            .bind();

    };

    // EtdTopMenu methods and shared properties
    EtdTopMenu.prototype = {

        // Reset constructor - http://goo.gl/EcWdiy
        constructor: EtdTopMenu,

        buildOverlay: function() {

            var $overlay = $('#etdtopmenu-overlay');
            if ($overlay.length === 0) {
                $overlay = $('<div id="etdtopmenu-overlay"></div>');
                $overlay.appendTo($body).hide();
            }

            this.$overlay = $overlay;

            return this;

        },

        // Bind events
        bind: function() {

            this.$element.find(this.options.subToggleSelector).on('click', $.proxy(this.toggleSubMenu, this));
            this.$overlay.on('click', $.proxy(this.hide, this));

            return this;

        },

        // Open/Close a submenu
        toggleSubMenu: function(e) {
            e.preventDefault();

            var $a = $(e.target);

            if (!$a.is(this.options.subToggleSelector)) {
                $a = $a.parents(this.options.subToggleSelector);
            }

            var $dropdown  = $a.parents(this.options.itemDropdownSelector),
                $dropdowns = this.$element.find(this.options.itemDropdownSelector);

            if ($dropdown.hasClass('open')) {
                this.unlockScroll()
                    .hideOverlay();
                $dropdowns.removeClass('open');
            } else {
                this.lockScroll()
                    .reposition($dropdown)
                    .showOverlay();
                $dropdowns.removeClass('open');
                $dropdown.addClass('open');
            }

        },

        showOverlay: function() {
            this.$overlay.show();
            return this;
        },

        hideOverlay: function() {
            this.$overlay.hide();
            return this;
        },

        lockScroll: function() {
            $body.addClass('topmenu-open');
            return this;
        },

        unlockScroll: function() {
            $body.removeClass('topmenu-open');
            return this;
        },

        reposition: function($dropdown) {
            var $dropdowns = this.$element.find(this.options.itemDropdownSelector),
                top = $dropdowns.offset().top + $dropdowns.outerHeight() - $window.scrollTop();
            $dropdowns.find(this.options.subNavBarSelector).css('top', top);
            this.$overlay.css('top', top);
            return this;
        },

        hide: function() {
            this.hideOverlay()
                .unlockScroll();
            this.$element.find(this.options.itemDropdownSelector).removeClass('open');
            return this;
        }

    };

    // Create the jQuery plugin
    $.fn.etdtopmenu = function(options) {
        // Do a deep copy of the options - http://goo.gl/gOSSrg
        options = $.extend(true, {}, defaults, options);

        return this.each(function() {
            var $this = $(this);
            $this.data('etdtopmenu', new EtdTopMenu($this, options));
        });
    };

    // Expose defaults and Constructor (allowing overriding of prototype methods for example)
    $.fn.etdtopmenu.defaults = defaults;
    $.fn.etdtopmenu.EtdTopMenu = EtdTopMenu;
});