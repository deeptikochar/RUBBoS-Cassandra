<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
  <body>
    <?php
    $scriptName = "Search.php";
    include("PHPprinter.php");
    $startTime = getMicroTime();

    $type = $_POST['type'];
    if ($type == null)
    {
      $type = $_GET['type'];
      if ($type == null)
        $type = 0;
    }

    $search = $_POST['search'];
    if ($search == null)
    {
      $search = $_GET['search'];
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

    printHTMLheader("RUBBoS search");

    // Display the search form
    print("<form action=\"/PHP/Search.php\" method=GET>\n".
          "<center><table>\n".
          "<tr><td><b>Search</b><td><input type=text size=50 name=search value=$search>\n".
          "<tr><td><b>in</b><td><SELECT name=type>\n");
    if ($type == 0)
    {
      print("<OPTION selected value=\"0\">Stories</OPTION>\n");
      $table = "stories";
      $title = "Stories";
    }
    else
      print("<OPTION value=\"0\">Stories</OPTION>\n");
    if ($type == 1)
    {
      print("<OPTION selected value=\"1\">Comments</OPTION>\n");
      $table = "comments";
      $title = "Comments";
    }
    else
      print("<OPTION value=\"1\">Comments</OPTION>\n");
    if ($type == 2)
    {
      print("<OPTION selected value=\"2\">Authors</OPTION>\n");
      $table = "users";
      $title = "Stories with author";
    }
    else
      print("<OPTION value=\"2\">Authors</OPTION>\n");
    print("</SELECT></table><p><br>\n".
          "<input type=submit value=\"Search now!\"></center><p>\n");


    // Display the results
    if ($search == null)
      print("<br><center><h2>Please select a text to search for</h2></center><br>");
    else
    {
      print("<br><h2>$title matching <i>$search</i></h2></center><br>");
      $search_end = $search;
      $search_end++;
      
      getDatabaseLink($link);
/*      if ($type == 0)
      { // Look for stories
        $result = mysql_query("SELECT id, title, date, writer FROM stories WHERE title LIKE '$search%' "." ORDER BY date DESC LIMIT ".$page*$nbOfStories.",$nbOfStories", $link) or die("ERROR: Query failed");
        if (mysql_num_rows($result) == 0)
          $result = mysql_query("SELECT id, title, date, writer FROM old_stories WHERE title LIKE '$search%' "." ORDER BY date DESC LIMIT ".$page*$nbOfStories.",$nbOfStories", $link) or die("ERROR: Query failed");
        if (mysql_num_rows($result) == 0)
        {
          if ($page == 0)
            print("<h2>Sorry, but there is no story matching <i>$search</i> !</h2>");
          else
          {
            print("<h2>Sorry, but there are no more stories available matching <i>$search</i>.</h2><br>\n");
            print("<p><CENTER>\n<a href=\"/PHP/Search.php?search=".urlencode($search)."&type=$type&page=".($page-1)."&nbOfStories=$nbOfStories\">Previous page</a>\n</CENTER>\n");
          }
          mysql_free_result($result);
          mysql_close($link);
          printHTMLfooter($scriptName, $startTime);
          exit();
        }
      }
      if ($type == 1)
      { // Look for comments
        $comment_table = "comments";
        $result = mysql_query("SELECT id,story_id,subject,writer,date FROM comments WHERE subject LIKE '$search%' "." GROUP BY story_id ORDER BY date DESC LIMIT ".$page*$nbOfStories.",$nbOfStories", $link) or die("ERROR: Query failed");
        if (mysql_num_rows($result) == 0)
        {
          $result = mysql_query("SELECT id,story_id,subject,writer,date FROM old_comments WHERE subject LIKE '$search%' "." ORDER BY date DESC LIMIT ".$page*$nbOfStories.",$nbOfStories", $link) or die("ERROR: Query failed");
          $comment_table = "old_comments";
        }
        if (mysql_num_rows($result) == 0)
        {
          if ($page == 0)
            print("<h2>Sorry, but there is no comment matching <i>$search</i> !</h2>");
          else
          {
            print("<h2>Sorry, but there are no more comments available matching <i>$search</i>.</h2><br>\n");
            print("<p><CENTER>\n<a href=\"/PHP/Search.php?search=".urlencode($search)."&type=$type&page=".($page-1)."&nbOfStories=$nbOfStories\">Previous page</a>\n</CENTER>\n");
          }
          mysql_free_result($result);
          mysql_close($link);
          printHTMLfooter($scriptName, $startTime);
          exit();
        }
        else
        {
          // Print the comment subject and author
          while ($row = mysql_fetch_array($result))
            print("<a href=\"/PHP/ViewComment.php?comment_table=$comment_table&storyId=".$row["story_id"]."&commentId=".$row["id"]."&filter=0&display=0\">".$row["subject"]."</a> by ".getUserName($row["writer"], $link)." on ".$row["date"]."<br>\n");
        }
        
      }
*/
      if ($type == 2)
      { // Look for stories of an author
        $result = $link->query("SELECT story_id FROM stories_users where nickname>='$search' AND nickname<'$search_end' LIMIT $nbOfStories ALLOW FILTERING;");
        $stories_table = "stories";

        if (count($result) == 0)
        {
            $stories_table = "old_stories";
            $result = $link->query("SELECT story_id FROM old_stories_users where nickname>='$search' and nickname<'$search_end' LIMIT $nbOfStories ALLOW FILTERING;");    
        }

        if (count($result) == 0)
        {
          if ($page == 0)
            print("<h2>Sorry, but there is no story with author matching <i>$search</i> !</h2>");
          else
          {
            print("<h2>Sorry, but there are no more stories available with author matching <i>$search</i>.</h2><br>\n");
            print("<p><CENTER>\n<a href=\"/PHP/Search.php?search=".urlencode($search)."&type=$type&page=".($page-1)."&nbOfStories=$nbOfStories\">Previous page</a>\n</CENTER>\n");
          }
          $link->disconnect();
          printHTMLfooter($scriptName, $startTime);
          exit();
        }
        $row = $result[0];
        $result = $link->query("SELECT id, title, date, writer FROM $stories_table where id=".$row["story_id"].";");
      }

      if ($type != 1)
      {
        // Print the story titles and author
        foreach ($result as $row)
        {
          $username = getUserName($row["writer"], $link);
          print("<a href=\"/PHP/ViewStory.php?storyId=".$row["id"]."\">".$row["title"]."</a> by ".$username." on ".$row["date"]."<br>\n");
        }
      }
          
      // Previous/Next links
      if ($page == 0)
        print("<p><CENTER>\n<a href=\"/PHP/Search.php?search=".urlencode($search)."&type=$type&page=".($page+1)."&nbOfStories=$nbOfStories\">Next page</a>\n</CENTER>\n");
      else
        print("<p><CENTER>\n<a href=\"/PHP/Search.php?search=".urlencode($search)."&type=$type&page=".($page-1)."&nbOfStories=$nbOfStories\">Previous page</a>\n&nbsp&nbsp&nbsp".
              "<a href=\"/PHP/Search.php?category=$search=".urlencode($search)."&type=$type&page=".($page+1)."&nbOfStories=$nbOfStories\">Next page</a>\n\n</CENTER>\n");
      
      $link->disconnect();        
    } 
   
    printHTMLfooter($scriptName, $startTime);
    ?>
  </body>
</html>
