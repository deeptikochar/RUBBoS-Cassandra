<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
  <body>
    <?php
    $scriptName = "OlderStories.php";
    include("PHPprinter.php");
    $startTime = getMicroTime();

    $day = $_POST['day'];
    if ($day == null)
    {
      $day = $_GET['day'];
    }
      
    $month = $_POST['month'];
    if ($month == null)
    {
      $month = $_GET['month'];
    }
      
    $year = $_POST['year'];
    if ($year == null)
    {
      $year = $_GET['year'];
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

    printHTMLheader("RUBBoS Older Stories");

    // Display the date chooser
    print("<form action=\"/rubbos/OlderStories.php\" method=POST>\n");
    print("<center><B>Date (day/month/year):</B><SELECT name=day>\n");
    for ($i = 1 ; $i < 32 ; $i++)
      print("<OPTION value=\"$i\">$i</OPTION>\n");      
    print("</SELECT>&nbsp/&nbsp<SELECT name=month>\n");
    for ($i = 1 ; $i < 13 ; $i++)
      print("<OPTION value=\"$i\">$i</OPTION>\n");      
    print("</SELECT>&nbsp/&nbsp<SELECT name=year>\n");
    for ($i = 2001 ; $i < 2013 ; $i++)
      print("<OPTION value=\"$i\">$i</OPTION>\n");      
    print("</SELECT><p><input type=submit value=\"Retrieve stories from this date!\"><p>\n");

    // Display the results
    if (($day == null) || ($month == null) || ($year == null))
      print("<br><h2>Please select a date</h2><br>");
    else
    {
      print("<br><h2>Stories of the $day/$month/$year</h2></center><br>");

      getDatabaseLink($link);
      $before = $year."-".$month."-".$day." 0:0:0";
      $after = $year."-".$month."-".$day." 23:59:59";
      $result = $link->query("SELECT * FROM stories WHERE date>='$before' AND date<='$after' LIMIT $nbOfStories ALLOW FILTERING;");
      if (count($result) == 0)
          $result = $link->query("SELECT * FROM old_stories WHERE date>='$before' AND date<='$after' LIMIT $nbOfStories ALLOW FILTERING;");
      if (count($result) == 0)
      {
        if ($page == 0)
          print("<h2>Sorry, but there are no story available for this date !</h2>");
        else
        {
          print("<h2>Sorry, but there is no more stories available for this date.</h2><br>\n");
          print("<p><CENTER>\n<a href=\"/rubbos/OlderStories.php?day=$day&month=$month&year=$year&page=".($page-1)."&nbOfStories=$nbOfStories\">Previous page</a>\n</CENTER>\n");
        }
        $link->disconnect();
        printHTMLfooter($scriptName, $startTime);
        exit();
      }

      // Print the story titles and author
      foreach ($result as $row)
      {
        if ($row["writer"] != null)
          $username = getUserName($row["writer"], $link);
        else
          $username = "Anonymous Coward";
        print("<a href=\"/rubbos/ViewStory.php?storyId=".$row["id"]."\">".$row["title"]."</a> by ".$username." on ".date("Y-m-d H:i:s",$row["date"])."<br>\n");
      }
      
      // Previous/Next links
      if ($page == 0)
        print("<p><CENTER>\n<a href=\"/rubbos/OlderStories.php?day=$day&month=$month&year=$year&page=".($page+1)."&nbOfStories=$nbOfStories\">Next page</a>\n</CENTER>\n");
      else
        print("<p><CENTER>\n<a href=\"/rubbos/OlderStories.php?day=$day&month=$month&year=$year&page=".($page-1)."&nbOfStories=$nbOfStories\">Previous page</a>\n&nbsp&nbsp&nbsp".
              "<a href=\"/rubbos/OlderStories.php?day=$day&month=$month&year=$year&page=".($page+1)."&nbOfStories=$nbOfStories\">Next page</a>\n\n</CENTER>\n");
      
        $link->disconnect();
    }

    printHTMLfooter($scriptName, $startTime);
    ?>
  </body>
</html>
