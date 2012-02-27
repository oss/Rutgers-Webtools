function showResult(action, data_id, loader_id) {
    var request = GetXmlHttpObject();

    if (request == null) {
        alert ("Your browser does not support XMLHTTP!");
        return;
    }

    // clear previous span test (if any)
    document.getElementById(data_id).innerHTML='';

    // start loader image
    startLoading(loader_id);

    // send post request
    sendPostRequest(request, "showresults.php", "action="+action, data_id, loader_id);
}

function addPendingEntry(netid, dbname, privs, current_dbnames) {
    // create pending
    var a = new Array();
    var b = new Array();
    var realdbname = netid+'_'+dbname;
    var action = 'pending';
    var request = GetXmlHttpObject();

    if (request == null) {
        alert ("Your browser does not support XMLHTTP!");
        return;
    }

    // no loader for add
    var loader_id = null;

    // response data for add
    var data_id = 'showpending';

    // populate js array of privs
    var default_privs = true;
    for (var i=0;i<privs.length;i++) {
        if(privs[i].checked) {
            // uppercase first words
            a.push(privs[i].value.replace(/\w\S*/g, function(txt){return txt.charAt(0).toUpperCase() + txt.substring(1).toLowerCase();}));
        } else {
            // at least one unchecked, so we are not Default anymore
            default_privs = false;
        }
    }

    // are we using Default privs?
    if (! default_privs)
        realprivs = a;
    else
        realprivs = 'Default';

    // populate js array of current_dbnames
    for (var i=0;i<current_dbnames.length;i++) {
        if(current_dbnames[i].value) {
            b.push(current_dbnames[i].value);
        } 
    }

    // serialize the privs before passing to PHP
    var sa_privs = serialize(a);

    // serialize the current dbnames before passing to PHP
    var sb_privs = serialize(b);
    
    // send post request
    sendPostRequest(request, "showresults.php", "action="+action+"&dbname="+dbname+"&privs="+sa_privs+"&current_dbnames="+sb_privs, data_id, loader_id);

    // wait a 1 sec and add the div IFF success
    setTimeout("addEntryDiv('"+realdbname+"','"+realprivs+"')", 1000);
}

function addEntryDiv(dbname, privs) {
    // verify no name='error' exist on the form
    if (document.getElementsByName('error').length > 0)
        return false;

    // figure out how many entry? id's we have
    var div_id = 'entry';
    var div_id_length = div_id.length;
    var i = 0;
    var current_entrynum = 0;
    var alldivs = document.getElementsByTagName('div');
    var el;
    while (el = alldivs.item(i++)) {
        if (el.id.substr(0,div_id_length) == div_id)
            current_entrynum++;
    }

    // now add 1 for this new entry
    new_entrynum = current_entrynum+1;

    // the id's we're adding between the span_id below
    var div_entry_id = 'entry'+new_entrynum;
    
    // the span_id we're replacing
    var span_add_entry_id = 'span_add_entry';

    // success, then add the new entry div
    var insertHTML;

    insertHTML =  "<div id='"+div_entry_id+"'>";
    insertHTML += "<input type='hidden' name='current_dbnames[]' value='"+dbname+"'>";
    insertHTML += "<div class='container'>";
    insertHTML += "<div class='float'>";
    //insertHTML += "<p class='image'><a href='javascript:;' onclick=\"deleteEntry('"+div_entry_id+"', '"+dbname+"')\"><img src='/css/newimages/delete.png' alt='Delete database "+dbname+"' title='Delete database "+dbname+"' /></a></p>";
    insertHTML += "<p class='image'></p>";
    insertHTML += "</div>";
    insertHTML += "<div class='float'>";
    insertHTML += "<p class='database'>"+dbname+"</p>";
    insertHTML += "</div>";
    insertHTML += "<div class='float'>";
    insertHTML += "<p class='status'>Pending</p>";
    insertHTML += "</div>";
    insertHTML += "<div class='float'>";
    insertHTML += "<p class='privs'>"+privs+"</p>";
    insertHTML += "</div>";
    insertHTML += "</div>";

    // prepend the header information on first entry
    if (new_entrynum == 1) {
        insertHEADER = "<div class='container-header'>";
        insertHEADER += "<div class='float'>";
        insertHEADER += "<p class='image'>&nbsp;</p>";
        insertHEADER += "</div>";
        insertHEADER += "<div class='float'>";
        insertHEADER += "<p id='dheader' class='database'>Database</p>";
        insertHEADER += "</div>";
        insertHEADER += "<div class='float'>";
        insertHEADER += "<p id='sheader' class='status'>Status</p>";
        insertHEADER += "</div>";
        insertHEADER += "<div class='float'>";
        insertHEADER += "<p id='pheader' class='privs'>Privileges</p>";
        insertHEADER += "</div>";
        insertHEADER += "</div>";
        insertHEADER += "<span id='span_add_entry'></span>";

        // the span_id we're replacing
        var span_add_id = 'span_add';

        document.getElementById(span_add_id).innerHTML = insertHEADER+insertHTML;

    } else {
        // the span_id we're replacing
        var save = document.getElementById(span_add_entry_id).innerHTML;
        document.getElementById(span_add_entry_id).innerHTML = insertHTML+save;
    }

    // clear the input box value
    document.getElementById('dbname').value = '';

    // reset the privs to all be checked
    var origprivs = document.getElementsByName('db_privs')
    for (var i=0;i<origprivs.length;i++) {
        if(! origprivs[i].checked) {
            origprivs[i].checked = true;
        } 
    }

    return true;
}

function deleteEntry(id, dbname) {
    // confirm delete request
    var c = confirm("Confirm you want to delete "+dbname);
    if (c == false)
        return;

    // delete the dbname
    var action = 'delete';
    var request = GetXmlHttpObject();

    if (request == null) {
        alert ("Your browser does not support XMLHTTP!");
        return;
    }

    // no loader for deletes
    var loader_id = null;

    // no data response
    var data_id = null;

    // send post request
    sendPostRequest(request, "showresults.php", "action="+action+"&dbname="+dbname, data_id, loader_id);

    // wait a 1 sec and remove the div IFF success
    setTimeout("deleteEntryDiv('"+id+"')", 1000);
}

function deleteEntryDiv(id) {
    // remove the div
    var c = document.getElementById(id);
    c.parentNode.removeChild(c);

    // figure out how many entry? id's we have left
    var div_id = 'entry';
    var div_id_length = div_id.length;
    var i = 0;
    var current_entrynum = 0;
    var alldivs = document.getElementsByTagName('div');
    var el;
    while (el = alldivs.item(i++)) {
        if (el.id.substr(0,div_id_length) == div_id)
            current_entrynum++;
    }
    
    // if last entry deleted replace the header
    if (current_entrynum == 0)
        document.getElementById('span_add').innerHTML = 'Add some above...';

    return true;
}

// http://phpjs.org/functions/serialize:508
function serialize (mixed_value) {
    // http://kevin.vanzonneveld.net
    // +   original by: Arpad Ray (mailto:arpad@php.net)
    // +   improved by: Dino
    // +   bugfixed by: Andrej Pavlovic
    // +   bugfixed by: Garagoth
    // +      input by: DtTvB (http://dt.in.th/2008-09-16.string-length-in-bytes.html)
    // +   bugfixed by: Russell Walker (http://www.nbill.co.uk/)
    // +   bugfixed by: Jamie Beck (http://www.terabit.ca/)
    // +      input by: Martin (http://www.erlenwiese.de/)
    // +   bugfixed by: Kevin van Zonneveld (http://kevin.vanzonneveld.net/)
    // +   improved by: Le Torbi (http://www.letorbi.de/)
    // +   improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net/)
    // +   bugfixed by: Ben (http://benblume.co.uk/)
    // -    depends on: utf8_encode
    // %          note: We feel the main purpose of this function should be to ease the transport of data between php & js
    // %          note: Aiming for PHP-compatibility, we have to translate objects to arrays
    // *     example 1: serialize(['Kevin', 'van', 'Zonneveld']);
    // *     returns 1: 'a:3:{i:0;s:5:"Kevin";i:1;s:3:"van";i:2;s:9:"Zonneveld";}'
    // *     example 2: serialize({firstName: 'Kevin', midName: 'van', surName: 'Zonneveld'});
    // *     returns 2: 'a:3:{s:9:"firstName";s:5:"Kevin";s:7:"midName";s:3:"van";s:7:"surName";s:9:"Zonneveld";}'
    var _utf8Size = function (str) {
        var size = 0,
            i = 0,
            l = str.length,
            code = '';
        for (i = 0; i < l; i++) {
            code = str.charCodeAt(i);
            if (code < 0x0080) {
                size += 1;
            } else if (code < 0x0800) {
                size += 2;
            } else {
                size += 3;
            }
        }
        return size;
    };
    var _getType = function (inp) {
        var type = typeof inp,
            match;
        var key;

        if (type === 'object' && !inp) {
            return 'null';
        }
        if (type === "object") {
            if (!inp.constructor) {
                return 'object';
            }
            var cons = inp.constructor.toString();
            match = cons.match(/(\w+)\(/);
            if (match) {
                cons = match[1].toLowerCase();
            }
            var types = ["boolean", "number", "string", "array"];
            for (key in types) {
                if (cons == types[key]) {
                    type = types[key];
                    break;
                }
            }
        }
        return type;
    };
    var type = _getType(mixed_value);
    var val, ktype = '';

    switch (type) {
    case "function":
        val = "";
        break;
    case "boolean":
        val = "b:" + (mixed_value ? "1" : "0");
        break;
    case "number":
        val = (Math.round(mixed_value) == mixed_value ? "i" : "d") + ":" + mixed_value;
        break;
    case "string":
        val = "s:" + _utf8Size(mixed_value) + ":\"" + mixed_value + "\"";
        break;
    case "array":
    case "object":
        val = "a";
/*
            if (type == "object") {
                var objname = mixed_value.constructor.toString().match(/(\w+)\(\)/);
                if (objname == undefined) {
                    return;
                }
                objname[1] = this.serialize(objname[1]);
                val = "O" + objname[1].substring(1, objname[1].length - 1);
            }
            */
        var count = 0;
        var vals = "";
        var okey;
        var key;
        for (key in mixed_value) {
            if (mixed_value.hasOwnProperty(key)) {
                ktype = _getType(mixed_value[key]);
                if (ktype === "function") {
                    continue;
                }

                okey = (key.match(/^[0-9]+$/) ? parseInt(key, 10) : key);
                vals += this.serialize(okey) + this.serialize(mixed_value[key]);
                count++;
            }
        }
        val += ":" + count + ":{" + vals + "}";
        break;
    case "undefined":
        // Fall-through
    default:
        // if the JS object has a property which contains a null value, the string cannot be unserialized by PHP
        val = "N";
        break;
    }
    if (type !== "object" && type !== "array") {
        val += ";";
    }
    return val;
}

// http://phpjs.org/functions/utf8_encode:577
function utf8_encode (argString) {
    // http://kevin.vanzonneveld.net
    // +   original by: Webtoolkit.info (http://www.webtoolkit.info/)
    // +   improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // +   improved by: sowberry
    // +    tweaked by: Jack
    // +   bugfixed by: Onno Marsman
    // +   improved by: Yves Sucaet
    // +   bugfixed by: Onno Marsman
    // +   bugfixed by: Ulrich
    // +   bugfixed by: Rafal Kukawski
    // *     example 1: utf8_encode('Kevin van Zonneveld');
    // *     returns 1: 'Kevin van Zonneveld'

    if (argString === null || typeof argString === "undefined") {
        return "";
    }

    var string = (argString + ''); // .replace(/\r\n/g, "\n").replace(/\r/g, "\n");
    var utftext = "",
        start, end, stringl = 0;

    start = end = 0;
    stringl = string.length;
    for (var n = 0; n < stringl; n++) {
        var c1 = string.charCodeAt(n);
        var enc = null;

        if (c1 < 128) {
            end++;
        } else if (c1 > 127 && c1 < 2048) {
            enc = String.fromCharCode((c1 >> 6) | 192) + String.fromCharCode((c1 & 63) | 128);
        } else {
            enc = String.fromCharCode((c1 >> 12) | 224) + String.fromCharCode(((c1 >> 6) & 63) | 128) + String.fromCharCode((c1 & 63) | 128);
        }
        if (enc !== null) {
            if (end > start) {
                utftext += string.slice(start, end);
            }
            utftext += enc;
            start = end = n + 1;
        }
    }

    if (end > start) {
        utftext += string.slice(start, stringl);
    }

    return utftext;
}
