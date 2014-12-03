<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
  <body>
    <?php
    $scriptName = "AcceptStory.php";
    include("PHPprinter.php");
    use Uuid\Uuid;
    $startTime = getMicroTime();

    $storyId = $_POST['storyId'];
    if ($storyId == null)
    {
      $storyId = $_GET['storyId'];
      if ($storyId == null)
      {
         printError($scriptName, $startTime, "AcceptStory", "<h3>You must provide a story identifier !<br></h3>");
         exit();
      }
    }

    getDatabaseLink($link);

    printHTMLheader("RUBBoS: Story submission result");

    print("<center><h2>Story submission result:</h2></center><p>\n");

    $result = $link->query("SELECT * FROM submissions WHERE id=$storyId;") ;
//    $result = mysql_query("SELECT * FROM submissions WHERE id=$storyId") or die("ERROR: Query failed");
//    if (mysql_num_rows($result) == 0)
    if (count($result) == 0)
      die("<h3>ERROR: Sorry, but this story does not exist.</h3><br>\n");
//    $row = mysql_fetch_array($result);
    $row = $result[0];

    // Add story to database
//    $result = mysql_query("INSERT INTO stories VALUES (NULL, \"".$row["title"]."\", \"".$row["body"]."\", '".$row["date"]."', ".$row["writer"].", ".$row["category"].")", $link) or die("ERROR: Failed to insert new story in database.");
//    mysql_query("DELETE FROM submissions WHERE id=$storyId", $link); 
    
    $timeuuid = Uuid::now();
    //echo(" djjj " . $timeuuid ." fffvsd");
    //var_dump($row);
    $row = str_replace("'", "''", $row);
    $result = $link->query("INSERT INTO stories (id, title, body, date, writer, category) VALUES (".$timeuuid.", '".$row["title"]."', '" .$row["body"]."', ". $row["date"].", ". $row["writer"].", ". $row["category"].");") or die("ERROR: Failed to insert new story in database.");

    //echo($timeuuid);

    $result = $link->query("SELECT nickname from users where id=".$row["writer"].";") or die("ERROR: Failed to insert new story in database.");
    $author = $result[0];
    //var_dump($author);
    $result = $link->query("INSERT INTO stories_users (story_id, nickname) VALUES ($timeuuid, '".$author["nickname"]."');") or die("ERROR: Failed to insert new story in database.");
    //echo("INSERT INTO stories_users (story_id, nickname) VALUES ($timeuuid, '".$author["nickname"]."');");
    $link->query("DELETE FROM submissions WHERE id=$storyId;");
    //var_dump($storyId);
    //var_dump($timeuuid);
    //mysql_close($link);
    $link->disconnect();    
    print("<h3>Success !</h3><br>");
    printHTMLfooter($scriptName, $startTime);
    ?>
  </body>
</html>
