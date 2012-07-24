var _searchTerm;
window.monthFull = [];
monthFull[0]="January";
monthFull[1]="February";
monthFull[2]="March";
monthFull[3]="April";
monthFull[4]="May";
monthFull[5]="June";
monthFull[6]="July";
monthFull[7]="August";
monthFull[8]="September";
monthFull[9]="October";
monthFull[10]="November";
monthFull[11]="December";
window.monthShort = [];
monthShort[0]="Jan";
monthShort[1]="Feb";
monthShort[2]="Mar";
monthShort[3]="Apr";
monthShort[4]="May";
monthShort[5]="Jun";
monthShort[6]="Jul";
monthShort[7]="Aug";
monthShort[8]="Sep";
monthShort[9]="Oct";
monthShort[10]="Nov";
monthShort[11]="Dec";

/****************************************
        helpers
****************************************/
function pad(num, size) {
    var s = num+"";
    while (s.length < size) {
        s = "0" + s;
    }
    return s;
}
function inArray(needle, haystack) {
    var i = haystack.length;
    while(i--) {
        if(haystack[i] === needle) {
            return true;
        }
    }
    return false;
}
function daySuffix(d) {
    d = String(d);
    return d.substr(-(Math.min(d.length, 2))) > 3 && d.substr(-(Math.min(d.length, 2))) < 21 ? "th" : ["th", "st", "nd", "rd", "th"][Math.min(Number(d)%10, 4)];
}

/**
 * Credit
 * http://stackoverflow.com/questions/901115/get-query-string-values-in-javascript/901144#901144
 */
function getParameterByName( name ) {
    name = name.replace(/[\[]/, "\\\[").replace(/[\]]/, "\\\]");

    var regexS = "[\\?&]" + name + "=([^&#]*)";
    var regex = new RegExp(regexS);
    var results = regex.exec(window.location.search);

    if ( results === null ) {
        return '';
    } else {
        return decodeURIComponent(results[1].replace(/\+/g, " "));
    }
}

var _searchSort = function( a, b ){
    return b[1]-a[1];
};


window.feeds = {
    data: {},
    loadFeed: function(feedAlias, feedUrl) {
        $.ajax({
            url: feedUrl,
            dataType: 'json',
            type: 'GET',
            success: function( msg ){
                feeds.data[feedAlias] = msg;
                msg = '';
                var d, dgD, dgM, dgY, j, ii;
                var tmp_search_string = '';
                var date_fields = ['s_d', 'e_d'];
                // r = region
                // ta = tag
                var today = new Date();
                var search_fields = ['t', 'c', 's', 'tr', 'ta', 'r'];

                /**
                 * Create a temporay seach string based on the msg
                 * @todo check custom status i.e. msg.respons == 1
                 */
                for ( var i in feeds.data[feedAlias] ) {
                    tmp_search_string = '';

                    for ( ii in date_fields ) {
                        j = date_fields[ii];
                        if(typeof feeds.data[feedAlias][i][j] !== 'undefined') {
                            d = new Date(feeds.data[feedAlias][i][j] + 0);
                            dgM = d.getUTCMonth();
                            dgD = d.getUTCDate();
                            dgY = d.getUTCFullYear();

                            // January
                            tmp_search_string += (feeds.data[feedAlias][i][j + '_F'] = monthFull[dgM]) + ' ';
                            // Jan
                            tmp_search_string += (feeds.data[feedAlias][i][j + '_M'] = monthShort[dgM]) + ' ';
                            // 01
                            tmp_search_string += (feeds.data[feedAlias][i][j + '_m'] = pad(((dgM + 1) + ''), 2)) + ' ';
                            // 1
                            tmp_search_string += (feeds.data[feedAlias][i][j + '_n'] = (dgM + 1)) + ' ';
                            // 13
                            tmp_search_string += (feeds.data[feedAlias][i][j + '_j'] = dgD) + ' ';
                            // 13th
                            tmp_search_string += (feeds.data[feedAlias][i][j + '_jS'] = dgD + daySuffix(dgD + '')) + ' ';
                            // 2012
                            tmp_search_string += (feeds.data[feedAlias][i][j + '_Y'] = dgY) + ' ';
                            // 12
                            tmp_search_string += (feeds.data[feedAlias][i][j + '_y'] = (dgY + '').substring(2,2)) + ' ';
                        }
                    }
                    if(feeds.data[feedAlias][i].s_d === feeds.data[feedAlias][i].e_d) {
                        feeds.data[feedAlias][i].dateHTML = feeds.data[feedAlias][i].s_d_F + ' ' + feeds.data[feedAlias][i].s_d_j + ', ' + feeds.data[feedAlias][i].s_d_Y;
                    } else {
                        if(feeds.data[feedAlias][i].s_d_n === feeds.data[feedAlias][i].e_d_n) {
                            feeds.data[feedAlias][i].dateHTML = feeds.data[feedAlias][i].s_d_F + ' ' + feeds.data[feedAlias][i].s_d_j + ' - ' + feeds.data[feedAlias][i].e_d_j + ', ' + feeds.data[feedAlias][i].s_d_Y;
                        } else {
                            feeds.data[feedAlias][i].dateHTML = feeds.data[feedAlias][i].s_d_F + ' ' + feeds.data[feedAlias][i].s_d_j + ' - ' + feeds.data[feedAlias][i].e_d_F + ' ' + feeds.data[feedAlias][i].e_d_j + ', ' + feeds.data[feedAlias][i].s_d_Y;
                        }
                    }

                    for ( ii in search_fields ) {
                        j = search_fields[ii];
                        if(typeof feeds.data[feedAlias][i][j] !== 'undefined') {
                            tmp_search_string += feeds.data[feedAlias][i][j] + ' ';
                        }
                    }

                    // need trailing and ending blank space,
                    // so it includes the first and last term
                    // for an extact term match
                    feeds.data[feedAlias][i].search_string = ' ' + tmp_search_string.toLowerCase() + ' ';
                }
                $(document).trigger('feedLoaded-' + feedAlias);
            }
        });
    },
    doSearch: function(feedAlias, searchString) {
        var params = {
            showCurrent: true,
            showPast: false,
            limit: 0
        };
        if(typeof arguments[2] === "object") {
            $.extend(params, arguments[1]);
        }
        var searchObject = { 
            term_count: 0,
            results: [],
            terms: []
        };
        if(!searchString.length) {
            return searchObject;
        }
        var req = [];       // for terms with '+'
        var ignore = [];    // for terms with '-'
        var weight_minus;
        var j, i;   // loop vars
        var todate = new Date();
        todate.setDate(todate.getDate() - 1);
        todate.setHours(0);
        todate = todate.getTime();
        var current = true;

        /**
         * Split search into terms array, also split
         * on / for date format
         */

        var preParse = searchString.split('"');
        if(preParse.length > 1) {
            i = 1; while(i < preParse.length) { preParse[i] = preParse[i].replace(/ /g, "__").replace(/,/g, "_-"); i+=2; }
            searchString = preParse.join('');
        }
        searchString = searchString.replace(/;/, '').toLowerCase().split(/,| |\//);

        for ( i in searchString ) {
            if ( ! searchString[i].length ) {
                searchString.splice( i, 1 );
            } else {
                searchString[i] = searchString[i].replace("__", " ").replace("_-", ",");
                if(searchString[i][0] === "+" && searchString[i].length > 1) {
                    searchString[i] = searchString[i].substr(1);
                    req.push(' ' + searchString[i] + ' ');
                }
                if(searchString[i][0] === "-" && searchString[i].length > 1) {
                    searchString[i] = searchString[i].substr(1);
                    ignore.push(' ' + searchString[i] + ' ');
                    searchString.splice( i, 1 );
                }
            }
        }
        searchObject.term_count = searchString.length;
        searchObject.terms = searchString;
        searchObject.feed = feedAlias;

        var this_search_term, score;

        /**
         * Compare user input with feeds.data[feedAlias], i.e. search string
         */
        for ( i in feeds.data[feedAlias] ) {
            var ignoreThisOne = false;
            if (!params.showPast && feeds.data[feedAlias][i].e_d < todate) {
                ignoreThisOne = true;
            }
            if (!params.showCurrent && feeds.data[feedAlias][i].e_d >= todate) {
                ignoreThisOne = true;
            }
            if ( !ignoreThisOne || typeof feeds.data[feedAlias][i].search_string !== 'undefined' ){

                for(j in req) {
                    if ( feeds.data[feedAlias][i].search_string.indexOf( req[j] ) < 0 ) {
                        ignoreThisOne = true;
                        break;
                    }
                }
                for(j in ignore) {
                    if ( feeds.data[feedAlias][i].search_string.indexOf( ignore[j] ) > -1 ) {
                        ignoreThisOne = true;
                        break;
                    }
                }
                if(!ignoreThisOne) {
                    // js sort score
                    score = 0;
                    current = (feeds.data[feedAlias][i].e_d < todate);

                    // give a higher weight to items that are first
                    weight_minus = 0;

                    for ( j in searchString ) {

                        this_search_term = searchString[j];
                        _searchTerm = this_search_term;

                        if ( feeds.data[feedAlias][i].search_string.indexOf( this_search_term ) > -1 ) {
                            score += ( 0.5 - weight_minus );
                        }

                        this_search_term = ' ' + this_search_term + ' ';

                        if ( feeds.data[feedAlias][i].search_string.indexOf( this_search_term ) > -1 ) {
                            score += ( 0.5 - weight_minus );
                        }

                        weight_minus += 0.001;
                    }

                    if ( score ) {
                        searchObject.results.push([i,score,current]);
                    }
                }
            }
        }

        searchObject.results.sort( _searchSort );
        if(params.limit > 0) {
            searchObject.results.splice(params.limit);
        }
        return searchObject;
    }
};

jQuery(window).load(function(){
    feeds.loadFeed('events', _site_url + '/races/events.json');
    feeds.loadFeed('tracks', _site_url + '/races/tracks.json');

});