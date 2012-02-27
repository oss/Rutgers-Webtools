function addnewEntry(username, address) {
    // create the new vmail user w/ address
    var action = 'new';
    var request = GetXmlHttpObject();

    if (request == null) {
        alert ("Your browser does not support XMLHTTP!");
        return;
    }

    // no loader for add
    var loader_id = null;

    // response data for add
    var data_id = 'shownew';
    
    // send post request
    sendPostRequest(request, "showresults.php", "action="+action+"&username="+username+"&address="+address, data_id, loader_id);

    // wait a 1 sec and add the div IFF success
    setTimeout("addEntryDiv('"+username+"','"+address+"')", 1000);
}

function addnewAddress(entrynum, username, address) {
    // add the vmail address to username file
    var action = 'addaddress';
    var request = GetXmlHttpObject();

    if (request == null) {
        alert ("Your browser does not support XMLHTTP!");
        return;
    }

    // no loader for add
    var loader_id = null;

    // response data for add
    var data_id = 'entry'+entrynum+'-newaddress-error';
    
    // send post request
    sendPostRequest(request, "showresults.php", "action="+action+"&username="+username+"&address="+address, data_id, loader_id);

    // wait a 1 sec and add the div IFF success
    setTimeout("addAddressDiv('"+entrynum+"','"+username+"','"+address+"')", 1000);
}

function addEntryDiv(username, address) {
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
    var showaddressresult_id = 'showaddressresult'+new_entrynum;
    var div_entry_address_id = 'div_entry'+new_entrynum+'-address1';
    var entry_address_id = 'entry'+new_entrynum+'-address1';
    var span_entry_newaddress_id = 'span_entry'+new_entrynum+'-newaddress';
    var div_entry_newaddress_parent_id = 'div_entry'+new_entrynum+'-newaddress-parent';
    var div_entry_newaddress_id = 'div_entry'+new_entrynum+'-newaddress';
    var entry_newaddress_error_id = 'entry'+new_entrynum+'-newaddress-error';
    var entry_newaddress_id = 'entry'+new_entrynum+'-newaddress';
    
    // the span_id we're replacing
    var span_add_entry_id = 'span_add_entry';

    // input id boxes to clear
    var input_username_id = 'username';
    var input_address_id = 'address';

    // success, then add the new entry div
    var insertHTML;

    insertHTML = "<div id='"+div_entry_id+"'>";
    insertHTML += "<div class='container'>";
    insertHTML += "<div class='float'>";
    insertHTML += "<p class='image'><a href='javascript:;' onclick=\"deleteEntry('"+div_entry_id+"', '"+username+"')\"><img src='/css/newimages/delete.png' alt='Delete virtual user "+username+"' title='Delete virtual user "+username+"' /></a></p>";
    insertHTML += "</div>";
    insertHTML += "<div class='float'>";
    insertHTML += "<p class='username'>"+username+"</p>";
    insertHTML += "</div>";
    insertHTML += "<div class='addressfloat'>";
    insertHTML += "<p class='address'>&nbsp;</p>";
    insertHTML += "</div>";
    insertHTML += "</div>";
    insertHTML += "<div id='"+showaddressresult_id+"' class='deleteaddress'></div>";
    insertHTML += "<div id='"+div_entry_address_id+"' class='container'>";
    insertHTML += "<div class='float'>";
    insertHTML += "<p class='image'>&nbsp;</p>";
    insertHTML += "</div>";
    insertHTML += "<div class='float'>";
    insertHTML += "<p class='username'>&nbsp;</p>";
    insertHTML += "</div>";
    insertHTML += "<div>";
    insertHTML += "<p class='address'>";
    insertHTML += "<input id='"+entry_address_id+"' size='25' maxlength='100' name='"+entry_address_id+"' type='text' value='"+address+"' readonly='readonly' />";
    insertHTML += "<a class='newaddress' href='javascript:;' onclick=\"deleteAddress('"+div_entry_address_id+"', '"+showaddressresult_id+"', '"+username+"', '"+address+"')\"><img src='/css/newimages/delete.png' alt='Delete address "+address+"' title='Delete address "+address+"' /></a>";
    insertHTML += "</p>";
    insertHTML += "</div>";
    insertHTML += "</div>";
    insertHTML += "<span id='"+span_entry_newaddress_id+"'></span>";
    insertHTML += "<div id='"+div_entry_newaddress_parent_id+"'>";
    insertHTML += "<div id='"+div_entry_newaddress_id+"' class='container'>";
    insertHTML += "<div class='float'>";
    insertHTML += "<p class='image'>&nbsp;</p>";
    insertHTML += "</div>";
    insertHTML += "<div class='float'>";
    insertHTML += "<p class='username'>&nbsp;</p>";
    insertHTML += "</div>";
    insertHTML += "<div>";
    insertHTML += "<p class='address'>";
    insertHTML += "<span id='"+entry_newaddress_error_id+"'></span>";
    insertHTML += "<input id='"+entry_newaddress_id+"' size='25' maxlength='100' name='"+entry_newaddress_id+"' type='text' />";
    insertHTML += "<a class='newaddress' href='javascript:;' onclick=\"addnewAddress('"+new_entrynum+"', '"+username+"', document.getElementById('"+entry_newaddress_id+"').value)\"><img src='/css/newimages/add.png' alt='Add address' title='Add address' /></a>";
    insertHTML += "</p>";
    insertHTML += "</div>";
    insertHTML += "</div>";
    insertHTML += "</div>";
    insertHTML += "</div>";

    // prepend the header information on first entry
    if (new_entrynum == 1) {
        insertHEADER = "<div class='container-header'>";
        insertHEADER += "<div class='float'>";
        insertHEADER += "<p class='image'>&nbsp;</p>";
        insertHEADER += "</div>";
        insertHEADER += "<div class='float'>";
        insertHEADER += "<p id='uheader' class='username'>Username</p>";
        insertHEADER += "</div>";
        insertHEADER += "<div class='float'>";
        insertHEADER += "<p id='aheader' class='address'>Address</p>";
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

    // clear the input box values
    document.getElementById(input_username_id).value = '';
    document.getElementById(input_address_id).value = '';

    return true;
}

function addAddressDiv(entrynum, username, address) {
    // verify no name='error' exist on the form
    if (document.getElementsByName('error').length > 0)
        return false;

    // figure out how many div_entry?_address? id's we have
    var div_id = 'div_entry'+entrynum+'-address';
    var div_id_length = div_id.length;
    var i = 0;
    var current_div_addressnum = 0;
    var alldivs = document.getElementsByTagName('div');
    var el;
    while (el = alldivs.item(i++)) {
        if (el.id.substr(0,div_id_length) == div_id)
            current_div_addressnum++;
    }

    // now add 1 for this new div
    new_div_addressnum = current_div_addressnum+1;

    // the div_id we're adding between the span_id below
    var newdiv_id = 'div_entry'+entrynum+'-address'+new_div_addressnum;

    // the span_id we're replacing
    var span_id = 'span_entry'+entrynum+'-newaddress';

    // input id box to clear
    var input_id = 'entry'+entrynum+'-newaddress';

    // success, then add the new div
    var insertHTML;
    insertHTML = "<div id='"+newdiv_id+"' class='container'>";
    insertHTML += "<div class='float'>";
    insertHTML += "<p class='image'>&nbsp;</p>";
    insertHTML += "</div>";
    insertHTML += "<div class='float'>";
    insertHTML += "<p class='username'>&nbsp;</p>";
    insertHTML += "</div>";
    insertHTML += "<div>";
    insertHTML += "<p class='address'>";
    insertHTML += "<input id='"+newdiv_id+"' size='25' maxlength='100' name='"+newdiv_id+"' type='text' value='"+address+"' readonly='readonly' />";
    insertHTML += "<a class='newaddress' href='javascript:;' onclick=\"deleteAddress('"+newdiv_id+"', 'showaddressresult"+entrynum+"', '"+username+"', '"+address+"')\"><img src='/css/newimages/delete.png' alt='Delete address "+address+"' title='Delete address "+address+"' /></a>";
    insertHTML += "</p>";
    insertHTML += "</div>";
    insertHTML += "</div>";

    // replace the span_id with our new html
    var save = document.getElementById(span_id).innerHTML;
    document.getElementById(span_id).innerHTML = save+insertHTML;

    // clear the add input box value
    document.getElementById(input_id).value = '';

    return true;
}

function deleteAddressDiv(id) {
    // remove the div
    var c = document.getElementById(id);
    c.parentNode.removeChild(c);

    return true;
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

function deleteAddress(id, data_id, username, address) {
    // confirm delete request
    var c = confirm("Confirm you want to delete "+address);
    if (c == false)
        return;

    // delete the username vmail address from file
    var action = 'deleteaddress';
    var request = GetXmlHttpObject();

    if (request == null) {
        alert ("Your browser does not support XMLHTTP!");
        return;
    }

    // no loader for deletes
    var loader_id = null;
    
    // send post request
    sendPostRequest(request, "showresults.php", "action="+action+"&username="+username+"&address="+address, data_id, loader_id);

    // wait a 1 sec and remove the div IFF success
    setTimeout("deleteAddressDiv('"+id+"')", 1000);
}

function deleteEntry(id, username) {

    // confirm delete request
    var c = confirm("Confirm you want to delete "+username);
    if (c == false)
        return;

    // delete the username vmail file
    var action = 'deleteuser';
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
    sendPostRequest(request, "showresults.php", "action="+action+"&username="+username, data_id, loader_id);

    // wait a 1 sec and remove the div IFF success
    setTimeout("deleteEntryDiv('"+id+"')", 1000);
}
