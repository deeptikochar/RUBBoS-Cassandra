<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
  <body>
    <?php
    $scriptName = "Author.php";
    include("PHPprinter.php");
    $startTime = getMicroTime();
    
    $nickname = $_POST['nickname'];
    if ($nickname == null)
    {
      $nickname = $_GET['nickname'];
      if ($nickname == null)
      {
         printError($scriptName, $startTime, "Author", "You must provide a nick name!<br>");
         exit();
      }
    }

    $password = $_POST['password'];
    if ($password == null)
    {
      $password = $_GET['password'];
      if ($password == null)
      {
         printError($scriptName, $startTime, "Author", "You must provide a password!<br>");
         exit();
      }
    }

    getDatabaseLink($link);

    // Authenticate the user
    $userId = 0;
    $access = 0;
    if (($nickname != null) && ($password != null))
    {
      $result = $link->query("SELECT id, access from user_logins where nickname='$nickname' AND password='$password';");
      if (count($result) != 0)
      {
	  $row = $result[0];
          $userId = $row["id"];
          $access = $row["access"];
      }
    }
    if (($access == 0))
    {
      printHTMLheader("RUBBoS: Author page");
      print("<p><center><h2>Sorry, but this feature is only accessible by users with an author access.</h2></center><p>\n");
    }
    else
    {
      printHTMLheader("RUBBoS: Author page");
      print("<p><center><h2>Which administrative task do you want to do ?</h2></center>\n".
            "<p><p><a href=\"/rubbos/ReviewStories.php?authorId=$userId\">Review submitted stories</a><br>\n");
    }
   
    $link->disconnect(); 
    printHTMLfooter($scriptName, $startTime);
    ?>
  </body>
</html>
