/**
 * Nwdthemes Wunderadmin Extension
 *
 * @package     Wunderadmin
 * @author		Nwdthemes <mail@nwdthemes.com>
 * @link		http://nwdthemes.com/
 * @copyright   Copyright (c) 2014. Nwdthemes
 * @license     http://themeforest.net/licenses/terms/regular
 */

// calendar extend
var waCalendarIcons = [];
Calendar.setup = (function() {
	var _setup = Calendar.setup;
	return function(params) {
		var _button;
		if (typeof params.button != 'undefined')
		{
			_button = typeof params.button == 'object' ? params.button.id : params.button;
		}
		else
		{
			_button = {
				iconId: jQuery(params.inputField).next('img').attr('id'),
				inputId: jQuery(params.inputField).attr('id')
			};
		}
		waCalendarIcons.push(_button);
		return _setup(params);
	};
})();

// fix for toggle default values
if (typeof toggleValueElements != 'undefined')
{
	var toggleValueElements = (function() {
		var _toggleValueElements = toggleValueElements;
		return function(checkbox, container, excludedElements, checked) {
			if (typeof container == 'undefined')
			{
				container = Element.previous(checkbox.parentNode.parentNode);
			}
			if(container && checkbox){
				var ignoredElements = [checkbox];
				if (typeof excludedElements != 'undefined') {
					if (Object.prototype.toString.call(excludedElements) != '[object Array]') {
						excludedElements = [excludedElements];
					}
					for (var i = 0; i < excludedElements.length; i++) {
						ignoredElements.push(excludedElements[i]);
					}
				}
				var elems = Element.select(container, ['select', 'input', 'textarea', 'button', 'img']);
				var elemsTest = false;
				elems.each(function (elem) {
					if (checkByProductPriceType(elem)) {
						var i = ignoredElements.length;
						while (i-- && elem != ignoredElements[i]);
						if (i != -1) {
							return;
						}
						elemsTest = true;
					}
				});
				if ( ! elemsTest)
				{
					container = container.parentNode;
				}
			}
			return _toggleValueElements(checkbox, container, excludedElements, checked);
		}
	})();
}

// url key change
if (typeof onUrlkeyChanged != 'undefined')
{
	onUrlkeyChanged = (function() {
		return function(urlKey) {
			urlKey = $(urlKey);
			var hidden = urlKey.next('input[type=hidden]');
			var chbx = urlKey.next('input[type=checkbox]');
			if (chbx === undefined)
			{
				chbx = urlKey.next('label.wa_checkbox_wrapper').firstDescendant();
			}
			var oldValue = chbx.value;
			chbx.disabled = (oldValue == urlKey.value);
			hidden.disabled = chbx.disabled;
		}
	})();
}

var waMinFooterPadding;
var waMinFooterMargin;

(function(jQuery) {

	Ajax.Responders.register({ onComplete: function() {
		updateWunderadminDesign();
		setTimeout(updateWunderadminDesign, 500);
	} });

	window.addEventListener('resize', adjustFooter);

	jQuery(document).ready(function() {

		// Fix for ext events in Chrome
		if (typeof Ext != 'undefined' && typeof Ext.EventManager.onDocumentReady != 'undefined')
		{
			Ext.EventManager.onDocumentReady(function() { updateWunderadminDesign(); } );
		}

		waMinFooterPadding = parseInt( jQuery('.middle').css('padding-bottom') );
		waMinFooterMargin = parseInt( jQuery('.footer').css('margin-top') );

		// navigation accordeon
		if (waTheme == 'modern')
		{
			jQuery('#nav .parent > a').addClass('collapsed');
			jQuery('#nav ul').hide();
			jQuery('#nav .parent > a').on('click', function(e) {
				var $item = jQuery(this);

				var parentOffset = $item.parent().offset();
				var relX = e.pageX - parentOffset.left;
				var arrowClicked = relX > $item.outerWidth() - 50;

				if ($item.hasClass('collapsed'))
				{
					$item.siblings('ul').slideDown(400, adjustModernFooter);
					$item.parents('li').siblings('li').children('.parent a:not(.collapsed)')
						.addClass('collapsed')
						.siblings('ul').slideUp();
					$item.removeClass('collapsed');
				}
				else
				{
					$item.siblings('ul').slideUp(400, adjustModernFooter);
					$item.addClass('collapsed');
				}

				if (arrowClicked)
				{
					e.preventDefault();
				}
			});
			jQuery('#nav .parent a.active').trigger('click');

			jQuery('<a />')
				.addClass('fa')
				.addClass('fa-angle-double-left')
				.attr('href', '#')
				.attr('title', 'Toggle Menu')
				.attr('id', 'wa_toggle_navigation')
				.prependTo('.nav-bar');

			jQuery('#wa_toggle_navigation').tipsy({gravity: 'w', opacity: 1, offset: 4, title: function() {
				jQuery('body')
					.removeClass('tipsy_help')
					.addClass('tipsy_button');
				return this.getAttribute('original-title');
			}});

			jQuery('#wa_toggle_navigation').on('click', function(e) {
				var $nav = jQuery('body');
				if ($nav.hasClass('wa_navigation_collapsed'))
				{
					jQuery(this).removeClass('fa-angle-double-right').addClass('fa-angle-double-left');
					$nav.removeClass('wa_navigation_collapsed');
					if(window.localStorage)
					{
						localStorage.setItem('wa_navigation', '');
					}
				}
				else
				{
					jQuery(this).removeClass('fa-angle-double-left').addClass('fa-angle-double-right');
					$nav.addClass('wa_navigation_collapsed');
					if(window.localStorage)
					{
						localStorage.setItem('wa_navigation', 'collapsed');
					}
				}
				e.preventDefault();
			});

			if (window.localStorage && localStorage.getItem('wa_navigation') == 'collapsed')
			{
				jQuery('#wa_toggle_navigation').trigger('click');
			}
		}

		// tipsy for buttons
		if (waTheme == 'clean')
		{
			var $_item = jQuery('#page-help-link');
			$_item.prop('title', $_item.text());
			$_item.html('&nbsp;');
			$_item.data('tipsy_class', 'tipsy_help');
			$_item.tipsy({gravity: 'e', opacity: 1, offset: 4, title: function() {
				jQuery('body')
					.removeClass('tipsy_button')
					.addClass(jQuery(this).data('tipsy_class'));
				return this.getAttribute('original-title');
			}});
		}
		if (waTheme == 'clean' || waTheme == 'modern')
		{
			var $_item = jQuery('.wunderadmin .notification-global .f-right a');
			$_item.prop('title', $_item.text());
			$_item.html('&nbsp;');
			$_item.data('tipsy_class', 'tipsy_button');
			$_item.tipsy({gravity: 'e', opacity: 1, offset: 4, title: function() {
				jQuery('body')
					.removeClass('tipsy_help')
					.addClass(jQuery(this).data('tipsy_class'));
				return this.getAttribute('original-title');
			}});
		}

		updateWunderadminDesign();

		// system configs accordeon
		jQuery('#system_config_tabs dt').on('click', function() {
			var $item = jQuery(this);
			if ($item.hasClass('collapsed'))
			{
				$item.siblings('dd').slideDown();
				$item.removeClass('collapsed');
			}
			else
			{
				$item.siblings('dd').slideUp();
				$item.addClass('collapsed');
			}
		});

		// element insert observer
		Element.insert = (function() {
			var _insert = Element.insert;
			return function(element, params) {
				_insert(element, params);
				updateWunderadminDesign();
			};
		})();

		// add answer button fix
		if (typeof answer != 'undefined')
		{
			answer.add = (function() {
				answer._add = answer.add;
				return function(obj) {
					answer._add(obj);
					updateWunderadminDesign();
				};
			})();
		}

		// IWD ajax form checkboxes support
		if (typeof IWD != 'undefined' && typeof IWD.OrderManager.HideLoadingMask != 'undefined')
		{
			IWD.OrderManager.HideLoadingMask = (function() {
				IWD.OrderManager._HideLoadingMask = IWD.OrderManager.HideLoadingMask;
				return function() {
					updateWunderadminDesign();
					IWD.OrderManager._HideLoadingMask();
				};
			})();
		}
	});

	function updateWunderadminDesign() {
return ;
		// clear whitespaces
		jQuery('.pager,.form-buttons').cleanWhitespace();

		// set min width
		if (waTheme != 'modern')
		{
			jQuery('.wrapper').css('min-width', (jQuery('#nav').outerWidth() + jQuery('#page-help-link').outerWidth() + parseInt(jQuery('.nav-bar').css('padding-left')) + parseInt(jQuery('.nav-bar').css('padding-right')) + 10) + 'px');
		}

		// update scope labels
		jQuery('td.scope-label').each(function(key, item) {
			var $item = jQuery(item);
			if ($item.text().indexOf('[') != -1)
			{
				$item.html('<span><span>' + $item.text().replace(/[\[\]]/g, '') + '</span></span>');
			}
			if ($item.children('span').text() == ' ' || $item.children('span').text() == '')
			{
				$item.removeClass('scope-label');
			}
		});

		// add blank line row to grid
		if ( ! jQuery('.grid tr.wa_blank_line').length)
		{
			jQuery('<tr class="wa_blank_line"><td colspan="' + jQuery('.grid tr.headings th').length + '">&nbsp;</td></tr>').insertAfter('.grid tr.headings');
		}

		// add labels for checkboxes to reskin
		jQuery('input[type=checkbox]').each(function(key, item) {
			var $item = jQuery(item);
			if ( ! $item.parent('label.wa_checkbox_wrapper').length && ! $item.parent('span.wa_checkbox_wrapper').length )
			{
				$item.wrap(
					$item.prop('onclick') == null
					&& ! ($item.parents('.delete-image,.x-tree-node-el,#_newsletterbase_fieldset,#product_info_tabs_super_settings_content,.website-name,#order-sidebar,.order-address,.order-totals,.price,.package-add-products,#_accountbase_fieldset,.advancedstock-products-edit,.advancedstock-misc-massstockeditor,.advancedstock-warehouse-edit,#order_history_block,.ordered_item_remove').length
						|| $item.attr('id') == 'url_key_create_redirect')
					? '<span class="wa_checkbox_wrapper"></span>'
					: '<label class="wa_checkbox_wrapper"></label>');
				jQuery('<span class="wa_checkbox_helper"></span>').insertAfter($item);
			}
		});

		// tipsy for buttons
		if (waTheme == 'clean' || waTheme == 'modern')
		{
			var $_item = jQuery(waTheme == 'clean' ? 'button:not(#general_store_information_validate_vat_number,.adminhtml-cache-index .form-list button,.template button,.validate-vat button,.with-button button,#moneybookers_multifuncbutton)' : '.categories-side-col button');
			$_item.each(function(key, _item) {
				var $_button = jQuery(_item);
				if ( ($_button.attr('title') == '' || $_button.attr('title') == undefined) && ! $_button.data('wa_title_set') )
				{
					$_button.data('wa_title_set', true);
					$_button.attr('title', $_button.text());
					$_button.attr('original-title', $_button.text());
				}
			});
			$_item.children('span').html('&nbsp;');
			$_item.data('tipsy_class', 'tipsy_button');
			$_item.tipsy({gravity: 'n', opacity: 1, offset: 4, title: function() {
				jQuery('body')
					.removeClass('tipsy_help')
					.addClass(jQuery(this).data('tipsy_class'));
				return this.getAttribute('original-title');
			}});
			$_item.data('wa_title_set', true);
		}

		// add classes to buttons
		jQuery.each(waButtons, function(key, waButton) {
			jQuery('button[title*="' + waButton.title + '"],button[original-title*="' + waButton.title + '"]').addClass(waButton.class);
		});

		// wrap global notifications
		if (waTheme == 'modern' && jQuery('.wa_notifications_wrap').length == 0)
		{
			jQuery('.notification-global').wrap('<div class="wa_notifications_wrap"></div>');
			jQuery('.wa_notifications_wrap:first').addClass('first');
		}

		// replace calendar icons
		jQuery.each(waCalendarIcons, function(key, icon) {
			var _icon, _target;
			if (typeof icon == 'object')
			{
				_icon = icon.iconId.replace( /(:|\.|\[|\])/g, "\\$1");
				_target = icon.inputId;
			}
			else
			{
				_icon = icon.replace( /(:|\.|\[|\])/g, "\\$1");
				_target = _icon;
			}
			var $newIcon = jQuery('<span>&#xf073;</span>');
			$newIcon.addClass('wa_calendar_icon');
			$newIcon.data('target', _target);
			$newIcon.on('click', function() {
				jQuery('#' + jQuery(this).data('target')).trigger('click');
			});
			$newIcon.insertAfter('#' + _icon);
			jQuery('#' + _icon).css({opacity: 0, width: '1px', height: '1px'});
		});
		waCalendarIcons = [];

		// fix to remove tipsy on delete package
		if (typeof packaging != 'undefined' && typeof packaging._deleteItem == 'undefined')
		{
			packaging.deleteItem = (function() {
				packaging._deleteItem = packaging.deleteItem;
				return function(obj) {
					jQuery('.tipsy').remove();
					return packaging._deleteItem(obj);
				};
			})();
		}

		// adjust footer
		adjustModernFooter();
	}

	function adjustModernFooter() {
		if (waTheme == 'modern')
		{
			var navHeight = jQuery('.nav-bar').outerHeight();
			var contentHeight = jQuery('.middle').outerHeight() + jQuery('.footer').outerHeight() - parseInt( jQuery('.middle').css('padding-bottom') );
			jQuery('.wa_notifications_wrap').each(function(_key, _item) {
				contentHeight += jQuery(_item).outerHeight();
			});
			var newFooterPadding = navHeight - contentHeight;
			if (newFooterPadding < waMinFooterPadding)
			{
				newFooterPadding = waMinFooterPadding;
			}
			jQuery('.middle').css('padding-bottom', newFooterPadding + 'px');
		}
	}

	function adjustFooter() {
		if (waTheme == 'clean' || waTheme == 'improved')
		{
			var offset = jQuery(window).height() - jQuery('body').outerHeight();
			var footerMargin = parseInt( jQuery('.footer').css('margin-top') );
			var newFooterMargin = offset + footerMargin;
			if (newFooterMargin < waMinFooterMargin)
			{
				newFooterMargin = waMinFooterMargin;
			}
			jQuery('.footer').css('margin-top', newFooterMargin + 'px');
		}
	}

	// fix for tipsy on closed popover
	if (typeof Dialog != 'undefined')
	{
		Dialog.cancelCallback = (function() {
			var _cancelCallback = Dialog.cancelCallback;
			return function() {
				jQuery('.tipsy').remove();
				return _cancelCallback();
			};
		})();
	}

	// clean whitespace
	jQuery.fn.cleanWhitespace = function() {
		textNodes = this.contents().filter(
			function() { return (this.nodeType == 3 && !/\S/.test(this.nodeValue)); })
			.remove();
		return this;
	}

})($nwd_jQuery);


