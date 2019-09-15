<?php
$mydb = mysql_connect("sql.mit.edu", "alanma", "daBrav3s") or die(mysql_error());
mysql_select_db("alanma+recipes") or die(mysql_error());
if (md5($_POST['passcode'])!="f0dc8941e43f33a1ea92e0425228151e"){
  exit("incorrect Password: ".$_POST['passcode']);
}
$name = $_POST['name'];
$type = $_POST['type'];

$in_ing = "INSERT INTO Ingredients (IngredientID, Name, TypeID) VALUES (NULL,'$name',$type)";
echo($in_ing);
mysql_query($in_ing) or die(mysql_error());
$from = $_POST['from'];
if ($from) {header("Location: $from");}
?>