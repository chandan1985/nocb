if ( typeof LD == "undefined" ) {
	var LD = {};
}
LD.utils = {};

LD.utils.convertToNumber = function( val, format ) {
	/* function that takes a number (as a string) and converts it to a new, formatted number */
	if ( val == null ) val = "";
	val += "";
	if ( val.length == 0 ) return ""; /* if empty string, keep it an empty string */
	var newVal = "";
	/* get the number format, if available ( and set defaults) */
	numFormat = ( typeof format  == "string" && format !== null ? format : "#,###.*" );
	/* FORMATS:
 * 	/  a comma before the . denotes comma notation
 * 		/  an * after the . denotes unlimited decimal places
 * 			/  a string of characters after the . will limit the number of decimals by their length */
	var commaNotation = ( numFormat.indexOf(",") !== -1 ? true : false );
	var decCount = ( numFormat.indexOf( "." ) == -1 ? "" : numFormat.split(".")[1] );
	decCount = (decCount == "*" || decCount == "") ? decCount : (decCount+"").length;
	var digits = "0123456789"; /* set the allowed characters */
	var chr;
	val = val.toLowerCase();
	var mult = 1;
	/* convert notation, so 12.3 million becomes 12,300,000 */
	if ( val.indexOf( ' mil' ) != -1 ) {
		mult = 1000000.0;
	} else if ( val.indexOf( ' bil') !=-1 ) {
		mult = 1000000000.0;
	} else if ( val.indexOf( ' thous' ) !=-1 ) {
		mult = 1000.0;
	}
	/* loop through the string and remove non-numbers, allow a single decimal */
	var isNeg = false;
	for ( var i=0; i<val.length;i++ ) {
		chr =  val.charAt( i );
		if ( digits.indexOf( chr ) != -1 ) {
			newVal += chr;
		} else if ( chr == "." && newVal.indexOf(".") === -1 ) {
			newVal += ".";
		} else if ( chr == "-" && i==0 ) {
			isNeg = true;
		}
	}
	if ( isNaN( newVal ) ) return "";
	var post;
	var decPow = 1.00;
	if ( post = newVal.split(".")[1] ) {
		decPow = Math.pow(10, (post+"").length );
	}
	newVal = ( Math.round(newVal  * decPow) * mult) / decPow;/*multiply by mult in case of notation, will also get rid of leading zeros*/
	/*rounding is to prevent javascript's imprecise number storage from surfacing*/
	newVal += "";
	var pre = newVal.split(".")[0]; 
	post = newVal.split(".")[1]; 
	if ( decCount !== "*" && (decCount+"").length ) {
		if ( post == null ) post = "";
		post = "1"+(post+"00000000000000000000").substring( 0, decCount+1 );
		post = (Math.round( post / 10 )+"").substring(1);
		
	}
	if ( pre.length > 3 ) {
		var counter = 0;
		val = "";
		for ( var p=pre.length-1; p>=0; p-- ) {
			if ( counter == 3 && commaNotation ) { /* if comma notation, insert commas */
				val = "," + val;
				counter = 0;
			}
			val = pre.charAt(p) + val;
			counter++;
		}
		
		pre = val;
	}
	if ( isNeg ) {
		pre = "-"+pre;
	}
	return (pre + (post?"."+post:"")); /*return the formatted number */
}

LD.utils.numericKeyCode = function( e ) {
	/* take an event keystroke and determine if allowable for a numeric-only field */
	var tgt = $(e.currentTarget);
	var code = e.keyCode || e.charCode;
	if ( code >=48 && code <= 57 ) { /*numbers*/
		return true;
	} else if ( code == 44 || ( code == 46 && tgt.val().indexOf(".") == -1 ) ) {
		return true; /*commas, one period*/
	} else if ( code == 45 && tgt.val().indexOf("-") == -1 ) {
		return true; /*one dash for negs */
	} else if ( code == 8 || 
		(e.ctrlKey == true && (code == 118 || code == 99) ) ) { /*backspace, paste, copy*/
		return true;
	} else {
		return false;
	}
}

LD.utils.setCookie = function(cname, cvalue, exphrs) {
    var d = new Date();
    d.setTime(d.getTime() + (exphrs*60*60*1000));
    var expires = "expires="+ d.toUTCString();
    document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
}

LD.utils.deleteCookie = function(cname) {
	var d = new Date();
	var expires = "expires=Thu, 01 Jan 1970 00:00:01 GMT;";
	document.cookie = cname + "=;" + expires + ";path=/";
}

LD.utils.getCookie = function(cname) {
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

LD.utils.postEncode = function( str ) {
	str = str.replace(/\+/g, '%2B');
	str = str.replace(/=/g, '%3D'); //protect against equals sign
	str = str.replace(/&/g, '%26'); //protect against ampersands
	str = str.replace(/%/g, '%25'); //protect against %
	return str;
}
