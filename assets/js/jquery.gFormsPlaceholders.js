// Utility
if (typeof Object.create !== 'function') {
    Object.create = function(obj) {
        function F() {};
        F.prototype = obj;
        return new F();
    };
}

(function($, window, document, undefined) {
    var gFormsPlaceholders = {

        init: function(options, elem) {
            var self = this;

            self.elem = elem;
            self.$elem = $(elem);

            self.options = $.extend({}, $.fn.gFormsPlaceholders.options, options);

            self.$fields = self.$elem.find('input[type=text],textarea');

            self.$fields.each(function() {
                $this = $(this);
                var $label = $this.closest('.gfield').find('label');
                var placeholder = $label.html().replace(new RegExp('<span.*span>', 'gm'), ''); // remove the required span
                $label.toggle(!self.options.hideLabels);
                $this.val('').attr('placeholder', placeholder);
            });

        },

    };

    $.fn.gFormsPlaceholders = function(options) {
        return this.each(function() {
            var instance = Object.create(gFormsPlaceholders);
            instance.init(options, this);
            $.data(this, 'gFormsPlaceholders', instance);
        });
    };

    $.fn.gFormsPlaceholders.options = {
        hideLabels: true
    };

    $('.gFormsPlaceholders').gFormsPlaceholders();

})(jQuery, window, document);
