<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
  <body>
    <?php
    $scriptName = "SubmitStory.php";
    include("PHPprinter.php");
    use Uuid\Uuid;
    $startTime = getMicroTime();

    $nickname = $_POST['nickname'];
    if ($nickname == null)
    {
      $nickname = $_GET['nickname'];
    }

    $password = $_POST['password'];
    if ($password == null)
    {
      $password = $_GET['password'];
    }

    $title = $_POST['title'];
    if ($title == null)
    {
      $title = $_GET['title'];
      if ($title == null)
      {
         printError($scriptName, $startTime, "SubmitStory", "You must provide a story title!<br>");
         exit();
      }
    }

    $body = $_POST['body'];
    if ($body == null)
    {
      $body = $_GET['body'];
      if ($body == null)
      {
         printError($scriptName, $startTime, "SubmitStory", "<h3>You must provide a story body!<br></h3>");
         exit();
      }
    }
      
    $category = $_POST['category'];
    if ($category == null)
    {
      $category = $_GET['category'];
      if ($category == null)
      {
         printError($scriptName, $startTime, "SubmitStory", "<h3>You must provide a category !<br></h3>");
         exit();
      }
    }

    getDatabaseLink($link);

    printHTMLheader("RUBBoS: Story submission result");

    print("<center><h2>Story submission result:</h2></center><p>\n");

    // Authenticate the user
    $userId = null;
    $access = 0;
    if (($nickname != null) && ($password != null))
    {
      $result = $link->query("SELECT id, access FROM user_logins WHERE nickname='$nickname' AND password='$password';");
      if (count($result) != 0)
      {
	$row = $result[0];
        $userId = $row["id"];
        $access = $row["access"];
      }
    }

    $table = "submissions";
    if ($userId == null)
    {
      print("Story stored by the 'Anonymous Coward'<br>\n");
      $userId = 'null';
    }
    else
    {
      if ($access == 0)
        print("Story submitted by regular user #$userId<br>\n");
      else
      {
        print("Story posted by author #$userId<br>\n");
        $table = "stories";
      }
    }

    // Add story to database
    $now = date("Y-m-d H:i:s");
    
    $timeuuid = Uuid::now();
    $result = $link->query("INSERT INTO $table (id, title, body, date, writer, category) VALUES ($timeuuid, '$title', '$body', '$now', $userId, $category);") or die("ERROR: Failed to insert new story in database.");

    if($userId != 'null' && $access == 1)
    {
      $result = $link->query("INSERT INTO stories_users (story_id, nickname) VALUES ($timeuuid, '$nickname');") or die("ERROR: Failed to insert new story in database.");
    }

    print("Your story has been successfully stored in the $table database table<br>\n");
    
    $link->disconnect();
    
    printHTMLfooter($scriptName, $startTime);
    ?>
  </body>
</html>
