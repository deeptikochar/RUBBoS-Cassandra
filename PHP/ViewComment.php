<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
  <body>
    <?php

// Display the nested comments
function display_follow_up($cid, $level, $display, $filter, $link, $comment_table, $separator)
{
  $follow = $link->query("SELECT * FROM $comment_table WHERE parent=$cid AND rating>=$filter ALLOW FILTERING;") or die("ERROR: Query failed");
  foreach ($follow as $follow_row)
  {
	if (!$separator)
	  {
	    print("<br><hr><br>");
	    $separator = true;
	  }
	if ($display == 1) // Preview nested comments
	  {
	    for ($i = 0 ; $i < $level ; $i++)
	      printf(" &nbsp &nbsp &nbsp ");
	    print("<a href=\"/rubbos/ViewComment.php?comment_table=$comment_table&storyId=".$follow_row["story_id"]."&commentId=".$follow_row["id"]."&filter=$filter&display=$display\">".$follow_row["subject"]."</a> by ".getUserName($follow_row["writer"], $link)." on ".$follow_row["date"]."<br>\n");
	  }
	else
	  {
	    $username = getUserName($follow_row["writer"], $link);
	    print("<TABLE bgcolor=\"#CCCCFF\"><TR>");
	    for ($i = 0 ; $i < $level ; $i++)
	      printf("<TD>&nbsp&nbsp&nbsp");
	    print("<TD><FONT size=\"4\" color=\"#000000\"><B><a href=\"/rubbos/ViewComment.php?comment_table=$comment_table&storyId=".$follow_row["story_id"]."&commentId=".$follow_row["id"]."&filter=$filter&display=$display\">".$follow_row["subject"]."</a></B>&nbsp</FONT> (Score:".$follow_row["rating"].")</TABLE>\n");
	    print("<TABLE>");
	    for ($i = 0 ; $i < $level ; $i++)
	      printf("<TD>&nbsp&nbsp&nbsp");
	    print("<TD><B>Posted by ".$username." on ".date("Y-m-d H:i:s", $follow_row["date"])."</B><p><TR>\n");
	    for ($i = 0 ; $i < $level ; $i++)
	      printf("<TD>&nbsp&nbsp&nbsp");
	    print("<TD>".$follow_row["comment"]."<TR>");
	    for ($i = 0 ; $i < $level ; $i++)
	      printf("<TD>&nbsp&nbsp&nbsp");
	    print("<TD><p>[ <a href=\"/rubbos/PostComment.php?comment_table=$comment_table&storyId=".$follow_row["story_id"]."&parent=".$follow_row["id"]."\">Reply to this</a>".
		  "&nbsp|&nbsp<a href=\"/rubbos/ViewComment.php?comment_table=$comment_table&storyId=".$follow_row["story_id"]."&commentId=".$follow_row["parent"].
		  "&filter=$filter&display=$display\">Parent</a>&nbsp|&nbsp<a href=\"/rubbos/ModerateComment.php?comment_table=$comment_table&commentId=".
		  $follow_row["id"]."\">Moderate</a> ]</TABLE><br>");
	  }
    if ($follow_row["childs"] > 0)
      display_follow_up($follow_row["id"], $level+1, $display, $filter, $link, $comment_table, $separator);
  }
}


    // Check parameters
    $scriptName = "ViewComment.php";
    include("PHPprinter.php");
    $startTime = getMicroTime();
    
    $filter = $_POST['filter'];
    if ($filter == null)
    {
      $filter = $_GET['filter'];
      if ($filter == null)
        $filter = 0;
    }

    $display = $_POST['display'];
    if ($display == null)
    {
      $display = $_GET['display'];
      if ($display == null)
        $display = 0;
    }

    $storyId = $_POST['storyId'];
    if ($storyId == null)
    {
      $storyId = $_GET['storyId'];
      if ($storyId == null)
      {
         printError($scriptName, $startTime, "Viewing comment", "You must provide a story identifier!<br>");
         exit();
      }
    }
      
    $commentId = $_POST['commentId'];
    if ($commentId == null)
    {
      $commentId = $_GET['commentId'];
      if ($commentId == null)
      {
         printError($scriptName, $startTime, "Viewing comment", "You must provide a comment identifier!<br>");
         exit();
      }
    }

    $comment_table = $_POST['comment_table'];
    if ($comment_table == null)
    {
      $comment_table = $_GET['comment_table'];
      if ($comment_table == null)
      {
         printError($scriptName, $startTime, "Viewing comment", "You must provide a comment table!<br>");
         exit();
      }
    }

    getDatabaseLink($link);
    if ($commentId == 0)

      $parent = '11111111-1111-1111-1111-111111111111';
    else
    {
      $result = $link->query("SELECT parent FROM $comment_table WHERE id=$commentId;");
      if (count($result) == 0)
        die("<h3>ERROR: Sorry, but this comment does not exist.</h3><br>\n");
      $row = $result[0];
      $parent = $row["parent"];
    }

    printHTMLheader("RUBBoS: Viewing comments");

    // Display comment filter chooser
    print("<center><form action=\"/rubbos/ViewComment.php\" method=GET>\n".
          "<input type=hidden name=commentId value=$commentId>\n".
          "<input type=hidden name=storyId value=$storyId>\n".
          "<input type=hidden name=comment_table value=$comment_table>\n".
          "<B>Filter :</B>&nbsp&nbsp<SELECT name=filter>\n");
    $count_result = $link->query("SELECT rating, count FROM comment_count WHERE story_id=$storyId ORDER BY rating;");
    $i = -1;

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
          "<OPTION value=\"0\">Main threads</OPTION>\n");
    if ($display == 1)
      print("<OPTION selected value=\"1\">Nested</OPTION>\n");
    else
      print("<OPTION value=\"1\">Nested</OPTION>\n");
    if ($display == 2)
      print("<OPTION selected value=\"2\">All comments</OPTION>\n");
    else
      print("<OPTION value=\"2\">All comments</OPTION>\n");
    print("</SELECT>&nbsp&nbsp&nbsp&nbsp<input type=submit value=\"Refresh display\"></center><p>\n");          

    // Display the comments
    $comment = $link->query("SELECT * from $comment_table WHERE story_id=$storyId AND parent=11111111-1111-1111-1111-111111111111 and rating>=$filter ALLOW FILTERING;");
    foreach ($comment as $comment_row)
    {
	  $separator=true;
	  print("<br><hr><br>");
	  $username = getUserName($comment_row["writer"], $link);
	  print("<TABLE width=\"100%\" bgcolor=\"#CCCCFF\"><TR><TD><FONT size=\"4\" color=\"#000000\"><B><a href=\"/rubbos/ViewComment.php?comment_table=$comment_table&storyId=$storyId&commentId=".$comment_row["id"]."&filter=$filter&display=$display\">".$comment_row["subject"]."</a></B>&nbsp</FONT> (Score:".$comment_row["rating"].")</TABLE>\n");
	  print("<TABLE><TR><TD><B>Posted by ".$username." on ".date("Y-m-d H:i:s", $comment_row["date"])."</B><p>\n");
	  print("<TR><TD>".$comment_row["comment"]);
	  print("<TR><TD><p>[ <a href=\"/rubbos/PostComment.php?comment_table=$comment_table&storyId=$storyId&parent=".$comment_row["id"]."\">Reply to this</a>&nbsp|&nbsp".
		"<a href=\"/rubbos/ViewComment.php?comment_table=$comment_table&storyId=$storyId&commentId=".$comment_row["parent"]."&filter=$filter&display=$display\">Parent</a>".
		"&nbsp|&nbsp<a href=\"/rubbos/ModerateComment.php?comment_table=$comment_table&commentId=".$comment_row["id"]."\">Moderate</a> ]</TABLE>\n");
      if (($display > 0) &&($comment_row["childs"] > 0))
        display_follow_up($comment_row["id"], 1, $display, $filter, $link, $comment_table, $separator);
    }

    $link->disconnect();
    
    printHTMLfooter($scriptName, $startTime);
    ?>
  </body>
</html>
