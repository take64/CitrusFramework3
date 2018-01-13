/**
 * citrus faces js
 *
 * @copyright   Copyright 2017, Citrus/besidesplus All Rights Reserved.
 * @author      take64 <take64@citrus.tk>
 */

(function(jQuery) {
    jQuery.extend({
        objectSize: function(object){
            var count = 0;
            $.each(object, function(){
                count++;
            });
            return count;
        },
        formatNumber: function(_options){
            var options = jQuery.extend(true, {
                number: 0,
                decimals: 0
            }, _options);
            
            var number = options.number;
            
            // remove ','
            number = String(number);
            number = number.replace(/,/g, '');
            
            // parse float
            number = parseFloat(number);
            
            // infinity
            number = String(number);
            
            // parse '123456'.'789'
            var numbers = number.split('.');
            
            // add ','
            numbers[0] = numbers[0].replace(/(\d{1,3})(?=(\d{3})+(?!\d))/g, "$1,");
            
            // concat
            number = numbers[0];
            if(numbers[1] !== undefined) {
                number += '.' + numbers[1];
            }
            
            return number;
        },
        
        uniqueID: function(_options){
            var options = jQuery.extend(true, {
                length: 16,
                prefix: '',
                suffix: '',
                retry: 16
            }, _options);
            
            var wk = 0;
            var result = '';
            for (var i = 0; i < options.retry; i++) {
                while (result.length < options.length) {
                    wk = Math.floor(Math.random() * 36);
                    if ((wk - 10) < 0) {
                        result += (wk + '');
                    }
                    else {
                        result += String.fromCharCode((wk - 10) + 65);
                    }
                }
                if ($('#' + result).length > 0) {
                    result = '';
                }
                else {
                    break;
                }
            }
            return options.prefix + result + options.suffix;
        }
    });
})(jQuery);

if (String.prototype.replaceAll === undefined) {
    String.prototype.replaceAll = function (org, dest) {
        return this.split(org).join(dest);
    };
}
if (String.prototype.format === undefined) {
    String.prototype.format = function(arg) {
        var replace_function = undefined;
        if (typeof arg === 'object') {
            replace_function = function(m, k) { return arg[k]; }
        }
        else {
            var args = arguments;
            replace_function = function(m, k) { return args[ parseInt(k) ]; }
        }

        return this.replace( /\{(\w+)\}/g, replace_function );
    };
}