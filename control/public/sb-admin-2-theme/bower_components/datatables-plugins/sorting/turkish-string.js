/**
 * Sorting in Javascript for Turkish Characters. This plug-in will replace the special
 * turkish letters (non english characters) and replace in English.
 *
 *  
 *  @name Turkish
 *  @summary Sort Turkish characters
 *  @author [Yuksel Beyti](http://yukselbeyti.com)
 *
 *  @example
 *    $('#example').dataTable({
 *       'aoColumns' : [
 *                       {'sType' : 'turkish'}
 *       ]
 *   });
 */

jQuery.extend( jQuery.fn.dataTableExt.oSort, {
	"turkish-pre": function ( a ) {
		var special_letters = { "Р”В°": "ib", "I": "ia", "Р•С›": "sa", "Р”С›": "ga", "Р“Сљ": "ua", "Р“вЂ“": "oa", "Р“вЂЎ": "ca", "i": "ia", "Р”В±": "ia", "Р•Сџ": "sa", "Р”Сџ": "ga", "Р“С�": "ua", "Р“В¶": "oa", "Р“В§": "ca" };
        for (var val in special_letters)
           a = a.split(val).join(special_letters[val]).toLowerCase();
        return a;
	},

	"turkish-asc": function ( a, b ) {
		return ((a < b) ? -1 : ((a > b) ? 1 : 0));
	},

	"turkish-desc": function ( a, b ) {
		return ((a < b) ? 1 : ((a > b) ? -1 : 0));
	}
} );
