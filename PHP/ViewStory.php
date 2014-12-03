<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
  <body>
    <?php

// Display the nested comments
function display_follow_up($cid, $level, $display, $filter, $link, $comment_table)
{
 // $follow = mysql_query("SELECT story_id,id,subject,writer,date FROM $comment_table WHERE parent=$cid", $link) or die("ERROR: Query failed");
  $follow = $link->query("SELECT story_id,id,subject,writer,date FROM $comment_table WHERE parent=$cid;");
 // while ($follow_row = mysql_fetch_array($follow))
  foreach($follow as $follow_row)
  {
    for ($i = 0 ; $i < $level ; $i++)
      printf("&nbsp&nbsp&nbsp");
    print("<a href=\"/rubbos/ViewComment.php?comment_table=$comment_table&storyId=".$follow_row["story_id"]."&commentId=".$follow_row["id"]."&filter=$filter&display=$display\">".$follow_row["subject"]."</a> by ".getUserName($follow_row["writer"], $link)." on ".$follow_row["date"]."<br>\n");
    if ($follow_row["childs"] > 0)
      display_follow_up($follow_row["id"], $level+1, $display, $filter, $link, $comment_table);
  }
}

    $scriptName = "ViewStory.php";
    include("PHPprinter.php");
    $startTime = getMicroTime();

    // Check parameters
    $storyId = $_POST['storyId'];
    if ($storyId == null)
    {
      $storyId = $_GET['storyId'];
      if ($storyId == null)
      {
         printError($scriptName, $startTime, "Viewing story", "You must provide a story identifier!<br>");
         exit();
      }
    }
      
    getDatabaseLink($link);
//    $result = mysql_query("SELECT * FROM stories WHERE id=$storyId") or die("ERROR: Query failed");
    $result = $link->query("SELECT * FROM stories WHERE id=$storyId;");
    if (count($result) == 0)
    {
     // $result = mysql_query("SELECT * FROM old_stories WHERE id=$storyId") or die("ERROR: Query failed");
      $result = $link->query("SELECT * FROM old_stories WHERE id=$storyId;");  
      $comment_table = "old_comments";
    }
    else
      $comment_table = "comments";
    if (count($result) == 0)
      die("<h3>ERROR: Sorry, but this story does not exist.</h3><br>\n");
    $row = $result[0];
    $username = getUserName($row["writer"], $link);

    // Display the story
    printHTMLheader("RUBBoS: Viewing story ".$row["title"]);
    printHTMLHighlighted($row["title"]);
    print("Posted by ".$username." on ".date("Y-m-d H:i:s", $row["date"])."<br>\n");
    print($row["body"]."<br>\n");
      print("<p><center><a href=\"/rubbos/PostComment.php?comment_table=$comment_table&storyId=$storyId&parent=11111111-1111-1111-1111-111111111111\">Post a comment on this story</a></center><p>");

    // Display filter chooser header
    print("<br><hr><br>");
    print("<center><form action=\"/rubbos/ViewComment.php\" method=POST>\n".
          "<input type=hidden name=commentId value=0>\n".
          "<input type=hidden name=storyId value=$storyId>\n".
          "<input type=hidden name=comment_table value=$comment_table>\n".
          "<B>Filter :</B>&nbsp&nbsp<SELECT name=filter>\n");
    //$count_result = mysql_query("SELECT rating, COUNT(rating) AS count FROM $comment_table WHERE story_id=$storyId GROUP BY rating ORDER BY rating", $link) or die("ERROR: Query failed");
    $count_result = $link->query("SELECT rating, count FROM comment_count WHERE story_id=$storyId ORDER BY rating;");
    $i = -1;
  //  while ($count_row = mysql_fetch_array($count_result))
     foreach ($count_result as $count_row)
     {
      while (($i < 6) && ($count_row["rating"] != $i))
      {
        if ($i == $filter)
          print("<OPTION selected value=\"$i\">$i: 0 comment</OPTION>\n");
        else
          print("<OPTION value=\"$i\">$i: 0 comment</OPTION>\n");
        $i++;
      }
      if ($count_row["rating"] == $i)
      {
        if ($i == $filter)
          print("<OPTION selected value=\"$i\">$i: ".$count_row["count"]." comments</OPTION>\n");
        else
          print("<OPTION value=\"$i\">$i: ".$count_row["count"]." comments</OPTION>\n");
        $i++;
      }
    }
    while ($i < 6)
    {
      print("<OPTION value=\"$i\">$i: 0 comment</OPTION>\n");
      $i++;
    }

    print("</SELECT>&nbsp&nbsp&nbsp&nbsp<SELECT name=display>\n".
          "<OPTION value=\"0\">Main threads</OPTION>\n".
          "<OPTION selected value=\"1\">Nested</OPTION>\n".
          "<OPTION value=\"2\">All comments</OPTION>\n".
          "</SELECT>&nbsp&nbsp&nbsp&nbsp<input type=submit value=\"Refresh display\"></center><p>\n");          
    $display = 1;
    $filter = 0;
    // Display the comments
   // $comment = mysql_query("SELECT * FROM $comment_table WHERE story_id=$storyId AND parent=0 AND rating>=$filter", $link) or die("ERROR: Query failed");
   $comment = $link->query("SELECT * FROM $comment_table WHERE story_id=$storyId AND parent=11111111-1111-1111-1111-111111111111 AND rating>=$filter allow filtering;");
  //  while ($comment_row = mysql_fetch_array($comment))
    foreach($comment as $comment_row)
    {
      print("<br><hr><br>");
    // var_dump($comment);

      $username = getUserName($comment_row["writer"], $link);
      print("<TABLE width=\"100%\" bgcolor=\"#CCCCFF\"><TR><TD><FONT size=\"4\" color=\"#000000\"><B><a href=\"/rubbos/ViewComment.php?comment_table=$comment_table&storyId=$storyId&commentId=".$comment_row["id"]."&filter=$filter&display=$display\">".$comment_row["subject"]."</a></B>&nbsp</FONT> (Score:".$comment_row["rating"].")</TABLE>\n");
      print("<TABLE><TR><TD><B>Posted by ".$username." on ".date("Y-m-d H:i:s", $comment_row["date"])."</B><p>\n");
      print("<TR><TD>".$comment_row["comment"]);
      print("<TR><TD><p>[ <a href=\"/rubbos/PostComment.php?comment_table=$comment_table&storyId=$storyId&parent=".$comment_row["id"]."\">Reply to this</a>&nbsp|&nbsp".
            "<a href=\"/rubbos/ViewComment.php?comment_table=$comment_table&storyId=$storyId&commentId=".$comment_row["parent"]."&filter=$filter&display=$display\">Parent</a>".
            "&nbsp|&nbsp<a href=\"/rubbos/ModerateComment.php?comment_table=$comment_table&commentId=".$comment_row["id"]."\">Moderate</a> ]</TABLE>\n");
      if ($comment_row["childs"] > 0)
        display_follow_up($comment_row[id], 1, $display, $filter, $link, $comment_table);
    }

 //   mysql_free_result($result);
    $link->disconnect();
    
    printHTMLfooter($scriptName, $startTime);
    ?>
  </body>
</html>
