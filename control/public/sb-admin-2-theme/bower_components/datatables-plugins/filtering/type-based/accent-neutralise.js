/**
 * When search a table with accented characters, it can be frustrating to have
 * an input such as _Zurich_ not match _ZР“С�rich_ in the table (`u !== Р“С�`). This
 * type based search plug-in replaces the built-in string formatter in
 * DataTables with a function that will remove replace the accented characters
 * with their unaccented counterparts for fast and easy filtering.
 *
 * Note that with the accented characters being replaced, a search input using
 * accented characters will no longer match. The second example below shows
 * how the function can be used to remove accents from the search input as well,
 * to mitigate this problem.
 *
 *  @summary Replace accented characters with unaccented counterparts
 *  @name Accent neutralise
 *  @author Allan Jardine
 *
 *  @example
 *    $(document).ready(function() {
 *        $('#example').dataTable();
 *    } );
 *
 *  @example
 *    $(document).ready(function() {
 *        var table = $('#example').dataTable();
 *
 *        // Remove accented character from search input as well
 *        $('#myInput').keyup( function () {
 *          table
 *            .search(
 *              jQuery.fn.DataTable.ext.type.search.string( this )
 *            )
 *            .draw()
 *        } );
 *    } );
 */

jQuery.fn.DataTable.ext.type.search.string = function ( data ) {
    return ! data ?
        '' :
        typeof data === 'string' ?
            data
                .replace( /РћВ­/g, 'РћВµ')
                .replace( /РџРЊ/g, 'РџвЂ¦')
                .replace( /РџРЉ/g, 'РћС—')
                .replace( /РџР‹/g, 'РџвЂ°')
                .replace( /РћВ¬/g, 'РћВ±')
                .replace( /РћР‡/g, 'Рћв„–')
                .replace( /РћВ®/g, 'РћВ·')
                .replace( /\n/g, ' ' )
                .replace( /Р“РЋ/g, 'a' )
                .replace( /Р“В©/g, 'e' )
                .replace( /Р“В­/g, 'i' )
                .replace( /Р“С–/g, 'o' )
                .replace( /Р“С”/g, 'u' )
                .replace( /Р“Р„/g, 'e' )
                .replace( /Р“В®/g, 'i' )
                .replace( /Р“Т‘/g, 'o' )
                .replace( /Р“РЃ/g, 'e' )
                .replace( /Р“Р‡/g, 'i' )
                .replace( /Р“С�/g, 'u' )
                .replace( /Р“В§/g, 'c' )
                .replace( /Р“В¬/g, 'i' ) :
            data;
};
