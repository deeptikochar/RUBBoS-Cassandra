<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
  <body>
    <?php
    $scriptName = "StoriesOfTheDay.php";
    include("PHPprinter.php");
    $startTime = getMicroTime();

    getDatabaseLink($link);

    printHTMLheader("RUBBoS stories of the day");

    $bodySizeLimit = 512;
    $now = date("Y:m:d H:i:s");
    $result = $link->query("SELECT * FROM stories LIMIT 10;") or die("ERROR: Query failed");
    if (count($result) == 0)
      print("<h2>Sorry, but there is no story available at this time.</h2><br>\n");

 //   while ($row = mysql_fetch_array($result))
    foreach($result as $row)
    {
      print("<br><hr>\n");
      printHTMLHighlighted("<a href=\"/rubbos/ViewStory.php?storyId=".$row["id"]."\">".$row["title"]."</a>");
      $username = getUserName($row["writer"], $link);
      print("<B>Posted by ".$username." on ".date("Y-m-d H:i:s", $row["date"])."</B><br>\n");
      if (strlen($row["body"]) > $bodySizeLimit)
      {
        print(substr($row["body"], 1, $bodySizeLimit));
        print("<br><B>...</B>");
      }
      else
        print($row["body"]);
      print("<br>\n");
    }
  // mysql_free_result($result);
   $link->disconnect();
    
    printHTMLfooter($scriptName, $startTime);
    ?>
  </body>
</html>
