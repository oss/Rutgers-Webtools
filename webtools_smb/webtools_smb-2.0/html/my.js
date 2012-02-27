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
    sendPostRequest(request, "showresult.php", "action="+action, data_id, loader_id);
}
