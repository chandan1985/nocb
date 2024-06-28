/**
 * Created by chris.meier on 3/7/17.
 */

//pull in GA object
(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
        (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
    m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
})(window,document,'script','https://www.google-analytics.com/analytics.js','ga');

//create trackers

//ga('create', 'TRACKER ID', 'auto', 'SiteGA');   //left blank for upgrade to universal if desired
ga('create', 'UA-141441-76', 'auto', 'DolanRollup');
ga('create', 'UA-51861146-1', 'auto', 'NewMediaTracker');

//send page views to trackers

// ga('SiteGA.send', 'page view');		//uncomment to enable page views on this tracker
ga('DolanRollup.send', 'pageview');
ga('NewMediaTracker.send', 'pageview');




// Marketting BS

function insert_mbs() {
    var ref = parse_referrer();
    if (ref) {
        process_cookie(ref);
    }
}

function parse_referrer() {

    var code = getQueryVariable('source');
    if (code == null) {
        code = getQueryVariable('source_code');
    }

    if (code != null) {
        return code;
    }
    else {
        url = document.referrer;
        url.replace('/\#.*/', '');  // strip # to end of line

        if (url != "") {
            if (url.match('/facebook/')) { code = 'FB:SOC'; }
            else if (url.match('/google/')) { code = 'GL'; }
            else if (url.match('/yahoo/')) { code = 'YA'; }
            else if (url.match('/linkedin/')) { code = 'LI:SOC'; }
            else if (url.match('/overt/')) { code = 'OV'; }
            else { return null; }
        }
        else {
            return null;
        }
    }
    return code;
}

function process_cookie(ref_code) {
    if (get_cookie('ips_scode') == null) {
        set_cookie('ips_scode', ref_code, 14);
        set_cookie('ips_stype', 'WEB', 14);
    }
}

insert_mbs();

// com score BS

var _comscore = _comscore || [];
_comscore.push({ c1: "2", c2: "9289482" });
(function() {
    var s = document.createElement("script"), el = document.getElementsByTagName("script")[0]; s.async = true;
    s.src = (document.location.protocol == "https:" ? "https://sb" : "http://b") + ".scorecardresearch.com/beacon.js";
    el.parentNode.insertBefore(s, el);
})();



// generic functions

function setCookie(cname, cvalue, exdays) {
    var d = new Date();
    d.setTime(d.getTime() + (exdays*24*60*60*1000));
    var expires = "expires="+ d.toUTCString();
    document.cookie = cname + "=" + cvalue + "; " + expires;
}

function getCookie(cname) {
    var name = cname + "=";
    var ca = document.cookie.split(';');
    for(var i = 0; i <ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0)==' ') {
            c = c.substring(1);
        }
        if (c.indexOf(name) == 0) {
            return c.substring(name.length,c.length);
        }
    }
    return null;
}

function getQueryVariable(variable) {
    var query = window.location.search.substring(1);
    var vars = query.split("&");
    for (var i=0;i<vars.length;i++) {
        var pair = vars[i].split("=");
        if(pair[0] == variable){return pair[1];}
    }
    return(false);
}