<?php
require_once('/usr/lib64/webtools/config/config.php');
head();
right_boxes('news');
content();
?>

<?php
/*
  Weeble File Manager (c) Christopher Michaels & Jonathan Manna
  This software is released under the BSD License.  For a copy of
  the complete licensing agreement see the LICENSE file.
*/

  require_once ("settings.php");
  require_once ("tools/compat.php");
  require_once ("functions-ftp.php");
  require_once ("header.php");

  if ( !isset ($Filename) ) {
    echo "<P>";
    echo "No file was selected\n";
    echo "</P>";
    foot();
    exit;
  }

  if ( isset ($Dir) )
    ftp_chdir ( $fp, $Dir );

  if ( isset ($search) ) {
    if ( phpversion() >= "4.0.5" ) {
      $search_term = explode ( " ", rawurldecode ( $search ) );
      foreach ( $search_term as $key => $val )
        $search_replace[$key] = "<span style=\"background-color: yellow\">$val</span>";
    } else {
      $search_term = $search;
      $search_replace = "<span style=\"background-color: yellow\">$search</span>";
    }
  } else
    $search = "";

  $file["name"] = $Filename;
  $file["size"] = ftp_size ($fp, $file["name"]);
  $tp = tmpfile ();
  $result = @ftp_fget ($fp, $tp, $file["name"], FTP_BINARY);
  if ( $result ) {
    rewind ($tp);
    // Check to see if the ext is one listed in $preview_html_ext.
    $ext = explode( ".", strtolower($Filename) );
    $ext = $ext[count($ext)-1];

    if ( (!$editor_prefs["allow_html"]) || (substr_count( $editor_prefs["html_ext"], $ext) == 0) ) {
      $file["content"] = "<FORM ACTION=\"$PHP_SELF\" METHOD=\"get\">Search Terms:\n";
      $file["content"] .= "<INPUT TYPE=\"text\" NAME=\"search\" value=\"$search\">\n";
      $file["content"] .= "<INPUT TYPE=\"submit\" name=\"submit\" VALUE=\"Search\">\n";
      $file["content"] .= "<INPUT TYPE=\"hidden\" name=\"Filename\" VALUE=\"" . $file["name"] . "\">\n";
      $file["content"] .= "<INPUT TYPE=\"hidden\" name=\"Dir\" VALUE=\"". ftp_pwd ($fp) ."\">\n";
      $file["content"] .= "<INPUT TYPE=\"hidden\" name=\"SID\" VALUE=\"$SID\">\n";
      $file["content"] .= "</FORM>\n";
      $file["content"] .= "<PRE><OL>";
      while ( !feof ($tp) ) {
        $string = htmlentities ( fgets ($tp, 4096) );
        if ( isset ( $search_term ) ) {
          $string = str_replace ( $search_term, $search_replace, $string );
        }
        $file["content"] .= "<li class=\"pre\">$string</li>";
      }
      $file["content"] .= "</OL></PRE>";
    } else {
      $file["content"] = fread ($tp, $file["size"]);
    }
    fclose ($tp);
  } else {
    $file["content"] = "Error: An error occurred while trying to retrieve \"" . $file["name"] . "\".";
  }
  echo $file["content"];
?>

<?php
foot(NULL, $validxhtml=false);
?>
