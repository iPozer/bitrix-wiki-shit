(function ($, APP, undefined) {
    'use strict';
    /**
    * Ajax Page
    */
    APP.Controls.Page.AjaxPage = can.Control.extend({
        defaults: {
            component: '',
            dataSelector: 'class'
        }
    },{
        init: function() {
            if (typeof BX != 'undefined' || typeof BX.addCustomEvent == 'function') {
                // регистрирем обработкичи событий на ajax
                BX.addCustomEvent('onAjaxSuccess', this.bxAjaxSuccess.bind(this));
                BX.addCustomEvent('onComponentAjaxHistorySetState', this.bxHistoryState.bind(this));
                BX.showWait = this.bxShowWait.bind(this);
                BX.closeWait = this.bxCloseWait.bind(this);
            }
        },

        bxHistoryState: function(obj) {
            if (typeof obj != 'undefined' && typeof obj.data != 'undefined') {
                this.initComponent();
            }
        },

        bxShowWait: function(container) {
            if (container == undefined) {
                return false;
            }

            // тут логика показа loader'a
        },

        bxCloseWait: function(container) {
            if (container == undefined) {
                return false;
            }

            // тут логика скрытия loader'a
        },

        bxAjaxSuccess: function(result, config) {
            if (typeof config != 'undefined' && !!config && config.dataType == 'html') {
                this.initComponent();
            }
        },

        initComponent: function() {
            //инициализируем js на компонент
            var $component = this.element.find(this.options.component);
            var componentClass = $component.data(this.options.dataSelector);

            var pagePlugin = can.capitalize(can.camelize(componentClass));
            if (APP.Controls.Page[pagePlugin]) {
                new APP.Controls.Page[pagePlugin](this.element, {$component: $component});
            }
        }
    });

})(jQuery, APP);