<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
  <body>
    <?php
    $scriptName = "StoriesOfTheDay.php";
    include("PHPprinter.php");
    $startTime = getMicroTime();

    $categoryName = $_POST['categoryName'];
    if ($categoryName == null)
    {
      $categoryName = $_GET['categoryName'];
      if ($categoryName == null)
      {
         printError($scriptName, $startTime, "Browse Stories By Category", "You must provide a category name!<br>");
         exit();
      }
    }
      
    $categoryId = $_POST['category'];
    if ($categoryId == null)
    {
      $categoryId = $_GET['category'];
      if ($categoryId == null)
      {
         printError($scriptName, $startTime, "Browse Stories By Category", "You must provide a category identifier!<br>");
         exit();
      }
    }
      
    $page = $_POST['page'];
    if ($page == null)
    {
      $page = $_GET['page'];
      if ($page == null)
        $page = 0;
    }
      
    $nbOfStories = $_POST['nbOfStories'];
    if ($nbOfStories == null)
    {
      $nbOfStories = $_GET['nbOfStories'];
      if ($nbOfStories == null)
        $nbOfStories = 25;
    }

    printHTMLheader("RUBBoS Browse Stories By Category");
    print("<br><h2>Stories in category $categoryName</h2><br>");

    getDatabaseLink($link);
    $result = $link->query("SELECT * FROM stories WHERE category=$categoryId LIMIT $nbOfStories ;") or die("ERROR: Query failed");

    if (count($result) == 0)
    {
      if ($page == 0)
        print("<h2>Sorry, but there is no story available in this category !</h2>");
      else
      {
        print("<h2>Sorry, but there are no more stories available at this time.</h2><br>\n");
        print("<p><CENTER>\n<a href=\"/PHP/BrowseStoriesByCategory.php?category=$categoryId".
              "&categoryName=".urlencode($categoryName)."&page=".($page-1)."&nbOfStories=$nbOfStories\">Previous page</a>\n</CENTER>\n");
      }
      $link->disconnect();
      printHTMLfooter($scriptName, $startTime);
      exit();
    }

    // Print the story titles and author
    foreach($result as $row)
    {
      $username = getUserName($row["writer"], $link);
      print("<a href=\"/rubbos/ViewStory.php?storyId=".$row["id"]."\">".$row["title"]."</a> by ".$username." on ".$row["date"]."<br>\n");
    }

    // Previous/Next links
    if ($page == 0)
      print("<p><CENTER>\n<a href=\"/rubbos/BrowseStoriesByCategory.php?category=$categoryId".
           "&categoryName=".urlencode($categoryName)."&page=".($page+1)."&nbOfStories=$nbOfStories\">Next page</a>\n</CENTER>\n");
    else
      print("<p><CENTER>\n<a href=\"/rubbos/BrowseStoriesByCategory.php?category=$categoryId".
            "&categoryName=".urlencode($categoryName)."&page=".($page-1)."&nbOfStories=$nbOfStories\">Previous page</a>\n&nbsp&nbsp&nbsp".
            "<a href=\"/rubbos/BrowseStoriesByCategory.php?category=$categoryId".
            "&categoryName=".urlencode($categoryName)."&page=".($page+1)."&nbOfStories=$nbOfStories\">Next page</a>\n\n</CENTER>\n");

    $link->disconnect();
    
    printHTMLfooter($scriptName, $startTime);
    ?>
  </body>
</html>
