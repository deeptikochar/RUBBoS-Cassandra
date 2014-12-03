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
    if (count($result) == 0)
      die("<h3>ERROR: Sorry, but this story does not exist.</h3><br>\n");
    $row = $result[0];

    // Add story to database    
    $timeuuid = Uuid::now();
    $row = str_replace("'", "''", $row);
    $result = $link->query("INSERT INTO stories (id, title, body, date, writer, category) VALUES (".$timeuuid.", '".$row["title"]."', '" .$row["body"]."', ". $row["date"].", ". $row["writer"].", ". $row["category"].");") or die("ERROR: Failed to insert new story in database.");

    $result = $link->query("SELECT nickname from users where id=".$row["writer"].";") or die("ERROR: Failed to insert new story in database.");
    $author = $result[0];
    $result = $link->query("INSERT INTO stories_users (story_id, nickname) VALUES ($timeuuid, '".$author["nickname"]."');") or die("ERROR: Failed to insert new story in database.");
    $link->query("DELETE FROM submissions WHERE id=$storyId;");

    $link->disconnect();    
    print("<h3>Success !</h3><br>");
    printHTMLfooter($scriptName, $startTime);
    ?>
  </body>
</html>
