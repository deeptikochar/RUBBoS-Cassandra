<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
  <body>
    <?php
    $scriptName = "RegisterUser.php";
    include("PHPprinter.php");
    use Uuid\Uuid;
    $startTime = getMicroTime();
    
    $firstname = $_POST['firstname'];
    if ($firstname == null)
    {
      $firstname = $_GET['firstname'];
      if ($firstname == null)
      {
         printError($scriptName, $startTime, "Register user", "You must provide a first name!<br>");
         exit();
      }
    }
      
    $lastname = $_POST['lastname'];
    if ($lastname == null)
    {
      $lastname = $_GET['lastname'];
      if ($lastname == null)
      {
         printError($scriptName, $startTime, "Register user", "You must provide a last name!<br>");
         exit();
      }
    }
      
    $nickname = $_POST['nickname'];
    if ($nickname == null)
    {
      $nickname = $_GET['nickname'];
      if ($nickname == null)
      {
         printError($scriptName, $startTime, "Register user", "You must provide a nick name!<br>");
         exit();
      }
    }

    $email = $_POST['email'];
    if ($email == null)
    {
      $email = $_GET['email'];
      if ($email == null)
      {
         printError($scriptName, $startTime, "Register user", "You must provide an email address!<br>");
         exit();
      }
    }

    $password = $_POST['password'];
    if ($password == null)
    {
      $password = $_GET['password'];
      if ($password == null)
      {
         printError($scriptName, $startTime, "Register user", "You must provide a password!<br>");
         exit();
      }
    }

    getDatabaseLink($link);

    // Check if the nick name already exists
    $result = $link->query("SELECT * FROM user_logins WHERE nickname='".$nickname."';");
    if (count($result) > 0)
    {
      printError($scriptName, $startTime, "Register user", "The nickname you have choosen is already taken by someone else. Please choose a new nickname.<br>\n");
      exit();
    }

    // Add user to database
    $now = date("Y-m-d H:i:s");
    $timeuuid = Uuid::now();
    $result = $link->query("INSERT INTO users (id, firstname, lastname, nickname, password, email, rating, access, creation_date) VALUES ($timeuuid, '$firstname', '$lastname', '$nickname', '$password', '$email', 0, 0, '$now');") or die("ERROR: Failed to insert new user in database.");

    $result = $link->query("INSERT INTO user_logins (nickname, password, id, access) VALUES ('$nickname', '$password', $timeuuid, 0);") or die("ERROR: Failed to insert new user in database.");
    $result = $link->query("SELECT * FROM users WHERE id=$timeuuid;") or die("ERROR: Query user failed");
    $row = $result[0];

    printHTMLheader("RUBBoS: Welcome to $nickname");
    print("<h2>Your registration has been processed successfully</h2><br>\n");
    print("<h3>Welcome $nickname</h3>\n");
    print("RUBBoS has stored the following information about you:<br>\n");
    print("First Name : ".$row["firstname"]."<br>\n");
    print("Last Name  : ".$row["lastname"]."<br>\n");
    print("Nick Name  : ".$row["nickname"]."<br>\n");
    print("Email      : ".$row["email"]."<br>\n");
    print("Password   : ".$row["password"]."<br>\n");
    print("<br>The following information has been automatically generated by RUBBoS:<br>\n");
    print("User id       :".$row["id"]."<br>\n");
    print("Creation date :".date('m/d/Y', $row["creation_date"])."<br>\n");
    print("Rating        :".$row["rating"]."<br>\n");
    print("Access        :".$row["access"]."<br>\n");
    
    $link->disconnect();
    
    printHTMLfooter($scriptName, $startTime);
    ?>
  </body>
</html>
