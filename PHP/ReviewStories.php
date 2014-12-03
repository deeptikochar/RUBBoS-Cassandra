<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
  <body>
    <?php
    $scriptName = "ReviewStories.php";
    include("PHPprinter.php");
    $startTime = getMicroTime();

    getDatabaseLink($link);

    printHTMLheader("RUBBoS: Review Stories");

    $now = date("Y:m:d H:i:s");
//    $result = mysql_query("SELECT * FROM submissions ORDER BY date DESC LIMIT 10", $link) or die("ERROR: Query failed");
    $result = $link->query("SELECT * FROM submissions LIMIT 10;");
//    if (mysql_num_rows($result) == 0)
    if (count($result) == 0)
      print("<h2>Sorry, but there is no submitted story available at this time.</h2><br>\n");

//    while ($row = mysql_fetch_array($result))
    foreach ($result as $row)
    {
      print("<br><hr>\n");
      printHTMLHighlighted($row["title"]);
      if ($row["writer"] == null)
        $username = "Anonymous Coward";
      else
        $username = getUserName($row["writer"], $link);
      print("<B>Posted by ".$username." on ".date("Y-m-d H:i:s", $row["date"])."</B><br>\n");
      print($row["body"]);
      print("<br><p><center><B>[ <a href=\"/rubbos/AcceptStory.php?storyId=".$row["id"]."\">Accept</a> | <a href=\"/rubbos/RejectStory.php?storyId=".$row["id"]."\">Reject</a> ]</B><p>\n");
    }
//    mysql_free_result($result);
//    mysql_close($link);
    $link->disconnect();
   
    printHTMLfooter($scriptName, $startTime);
    ?>
  </body>
</html>
