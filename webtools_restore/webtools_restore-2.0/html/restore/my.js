function submitForm() {
    // sleep for .5 second while the last element has time to be validated
    setTimeout("reallySubmitForm()", 500);
}
function reallySubmitForm() {
    if (atleastOne())
        document.forms['myform'].submit();
}
function atleastOne() {
    var e = document.getElementsByName('path_arr[]');
    var i;
    for (i=0;i<e.length;i++) {
        if(e[i].checked)
            return true;
    }
    alert('At least one check is required in order to Submit.');
}
function showSearch(searchterm, spath) {
    var request = GetXmlHttpObject();
    var data_id = 'showsearch';
    var loader_id = 'loader_for_search';

    if (request == null) {
        alert ("Your browser does not support XMLHTTP!");
        return;
    }

    startLoading(loader_id);
    sendPostRequest(request, "showsearch.php", "searchterm="+searchterm+"&spath="+spath, data_id, loader_id);
}
function showAll(path) {
    var request = GetXmlHttpObject();
    var data_id = 'showall';
    var loader_id = 'loader_for_all';

    if (request == null) {
        alert ("Your browser does not support XMLHTTP!");
        return;
    }

    startLoading(loader_id);
    sendRequest(request, "showall.php?path="+path, data_id, loader_id);
}
