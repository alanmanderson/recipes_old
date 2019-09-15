<?php
include_once("dbconnect.php");
$search= ($_GET['search']) ? $_GET['search'] : "%";
$search=addslashes($search);
$query="SELECT * FROM Ingredients WHERE Name LIKE '%$search%'";
$results=mysql_query($query);
while ($row=mysql_fetch_array($results)){
      echo $row[1]."\n";
}
?>