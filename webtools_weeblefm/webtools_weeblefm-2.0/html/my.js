function submitForm(username, password, server) {
    var request = GetXmlHttpObject();

    if (request == null) {
        alert ("Your browser does not support XMLHTTP!");
        return;
    }

    // no loader needed
    var loader_id = null;

    var data_id = 'showerror';

    // send customized post request
    var params = "ftp_User="+username+"&ftp_Pass="+password+"&login_server="+server;
    request.open("POST", "check_login.php", true);
    request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    request.setRequestHeader("Content-length", params.length);
    request.setRequestHeader("Connection", "close");
    request.onreadystatechange = function () {
        var myid = document.getElementById(data_id);
        if (request.readyState == 4) {
            var rt = request.responseText;
            if (rt.substr(0,4) == 'SID=')
                window.location = 'ftp.php?'+rt;
            else
                myid.innerHTML=request.responseText;
        }
    }
    request.send(params);
}
