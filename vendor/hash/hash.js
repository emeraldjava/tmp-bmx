/* BEGIN Hash Tag Stuff */
var _filters = {};

// @todo if we have a hash store it to filter on later
function addHash( hash ) {
        
    if( typeof arguments[1] !== "undefined" && arguments[1] == true) {
        _filters = {};
    }
    if ( hash ) {
        var thishash, theseterms;
        var thesehashes = hash.split('/');

        for(var i = 0; i < thesehashes.length; i++) {
            if(thesehashes[i].indexOf('_') > -1) {
                thishash = thesehashes[i].split('_'); 
                theseterms = thishash[1].split(',');
                for(var j = 0; j < theseterms.length; j++ ) {
                    if(typeof _filters[ thishash[0] ] !== "object") {
                        _filters[ thishash[0] ] = [];
                    }
                    _filters[ thishash[0] ].push(theseterms[j]);
                    jQuery("#" + thishash[0] + "-" + theseterms[j].toLowerCase()).prop("checked", true);
                }
                jQuery("#select_" + thishash[0] + " option[data-value=" + thishash[1].toLowerCase() + "]").attr("selected", "selected");
            }
        }
    }
}
function changeHash() {
    var hash = "/";
    for(var j in _filters) {
        hash += j + "_" + _filters[j].join(",") + "/";
    }
    window.location.hash = hash;
}

function filterRows() {
    var showhide;
    var noResults = true;

    if ( typeof( _data ) == "undefined" ) {        
        return false;
    }

    for( var i in _data ) {
        showhide = true;
        for ( var j in _filters ) {
            if ( jQuery.inArray(_data[i][j], _filters[j]) === -1) {
                showhide = false;
            }
        }

        if ( showhide ) {
            noResults = false;
            jQuery( ".post-" + i ).fadeIn();
        } else {
            jQuery( ".post-" + i ).fadeOut();
        }
    }

    if ( noResults ) {
        if( jQuery("#archive_table tbody tr.no-results").length ) {
            jQuery("#archive_table tbody tr.no-results").fadeIn();
        } else {
            var colspan = jQuery("td", jQuery("#archive_table tbody tr").eq(0)).length;
            jQuery("#archive_table tbody")
                .append('<tr class="no-results"><td colspan="' + colspan + '"><em>No Results.</em></td></tr>');
        }
    } else {        
        jQuery("#archive_table tbody tr.no-results").fadeOut();
    }
    changeHash();
}

function build_filters( _filter_selector ) {
    var searchClasses = '';
    _filters = {};

    jQuery( _filter_selector + " select" ).each(function() { 
        
        var filter_name = this.name.replace('[]', '');

        if(jQuery(this).val()) {
            jQuery('option:selected', this ).each(function(){
                if(typeof _filters[ filter_name ] !== "object") {
                    _filters[ filter_name ] = [];
                }
                _filters[ filter_name ].push(jQuery(this).attr("data-value"));    
            });
        }
    });

    jQuery( _filter_selector + " input[type=checkbox]").each(function() {
        if(jQuery(this).prop('checked')) {
            if(typeof _filters[this.name] !== "object") {
                _filters[this.name] = [];
            }
            _filters[this.name].push(jQuery(this).attr("data-value"));
        } 
    });
    filterRows();
}

addHash(window.location.hash, false);

jQuery('a[href*="http://' + location.host + location.pathname + '#/"]').live('click', function() {
    addHash(
        jQuery(this).attr('href').replace('http://' + location.host + location.pathname, ''), true
    );
    filterRows();
    return false;
});
/* END Hash Tag Stuff */

jQuery( document ).ready(function( $ ){
    $( window ).load(function(){
        filterRows();
    });
});