<?php
require_once('/usr/lib64/webtools/config/config.php');
head();
right_boxes('news', $LOGIN_BOX_ARRAY);
content();
?>

<table>
    <tr>
        <td><img src="/css/newimages/file-manager.png" alt="file manager image" /></td>
        <td><strong>Weeble File Manager</strong> (aka WeebleFM) is a php file manager run through an ftp connection. It offers an easy to use interface for its users. Among its many user friendly tasks are allowing you to download files from <?=$CLUSTER?>, changing permissions of a file or directory, and creating/deleting/editing files or directories.</td>
    </tr>
    <tr>
        <td><img src="/css/newimages/help.png" alt="help image" /></td>
        <td><strong>Need Help?</strong> Contact the Computing Help Desk on your campus:<br />Camden: 856-225-6274<br />New Brunswick: 732-445-HELP (4357) or by e-mail to help@<?=$CLUSTER?>.rutgers.edu<br />Newark: 973-353-5083</td>
    </tr>
</table>

<?php
foot();
?>
