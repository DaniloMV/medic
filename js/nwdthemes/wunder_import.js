/**
 * Nwdthemes Wunderadmin Extension
 *
 * @package     Wunderadmin
 * @author		Nwdthemes <mail@nwdthemes.com>
 * @link		http://nwdthemes.com/
 * @copyright   Copyright (c) 2014. Nwdthemes
 * @license     http://themeforest.net/licenses/terms/regular
 */

var wunderImport;

(function(jQuery) {

wunderImport = {
    openDialog: function(wunderImportUrl) {
        if ($('wunderimport_window') && typeof(Windows) != 'undefined') {
            Windows.focus('wunderimport_window');
            return;
        }
        this.dialogWindow = Dialog.info(null, {
            draggable:true,
            resizable:false,
            closable:true,
            className:'magento',
            windowClassName:"popup-window",
            title:Translator.translate('Import Color Scheme...'),
            top:50,
            width:950,
            zIndex:1000,
            recenterAuto:false,
            hideEffect:Element.hide,
            showEffect:Element.show,
            id:'wunderimport_window',
            onClose: this.closeDialog.bind(this)
        });
        new Ajax.Updater('modal_dialog_message', wunderImportUrl, {evalScripts: true});
    },
    closeDialog: function(window) {
        if (!window) {
            window = this.dialogWindow;
        }
        if (window) {
            // IE fix - hidden form select fields after closing dialog
            WindowUtilities._showSelect();
            window.close();
        }
    }
}

})($nwd_jQuery);