<?php
include_once("../commons/checkauth.php");
$_SESSION["db"]="recipes";
include_once("../commons/dbconnect.php");

if (isset($_POST["name"])){
  $name = htmlentities($_POST['name']);
  $type = htmlentities($_POST['type']);
  $referrer = htmlentities($_POST['referrer']);
  $stopExecution = htmlentities($_POST['stopExecution']);
  $insertQuery = "INSERT INTO Ingredients (Name, TypeID) VALUES ('$name','$type')";
  mysql_query($insertQuery) or die(mysql_error());
  if (isset($referrer) && $referrer!=""){  
    header("Location: $referrer");
  } 
  if ($stopExecution=="true") {
    $query = "SELECT IngredientID, Name, TypeID FROM Ingredients";
    $results = mysql_query($query) or die(mysql_error());
    $rows=array();
    while($row=mysql_fetch_assoc($results)){
      $rows[] = $row;
    }
    print json_encode($rows);
    exit(0);
  }
  echo "$name correctly added to the database!";
}


?>
2
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
  <head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8"/>
    <link rel="stylesheet" type="text/css" href="stylesheets/main.css" />
    <script type="text/javascript" src="../commons/javascripts/jquery.js"></script>
    <script type="text/javascript" src="../commons/javascripts/jquery.label_over.js"></script>
    <script type="text/javascript">
      $(function() {
        $('label.targetLabel').labelOver('labelover'); 
      });
      $(function() {
	$("#addIngBtn").click(function() {
	  if (validateInp()){
	    document.frmAddIng.submit();
	  } else {
            $('#error').html("Either Title or ingredient type has no value.  Please enter a value for both.");
	  }
	});
      });

      function validateInp(){
        return document.getElementById('ingName').value;
      }
    </script>
    <title>Alan and Heathers Recipe Database</title>
  </head>
  <body style="font-family: Arial;border: 0 none;">
    <form method="POST" name="frmAddIng" action="<?php echo htmlentities($PHP_SELF);?>">
      <div id="error">
      </div>
      <table>
	<tr>
	  <td>
	    <table width="800px">
	      <tr>
		<td colspan="2">
		  <div class="labeledfield">
		    <label class="targetLabel" for="ingName">Title</label>
		      <input type="text" id="ingName" name="name"/>
		  </div>
		</td>
	      </tr>
	      <tr>
	        <td colspan="2">							

<?php
	$se_types = "SELECT * FROM IngTypes ORDER BY Type";
	$result = mysql_query($se_types) or die(mysql_error());
	$opt=1;
	echo("<select name=\"type\" size=\"".(mysql_num_rows($result))."\">");
	while($row = mysql_fetch_array($result)){
		echo("<option value=\"".$row['ID']."\" ");
		if ($opt) {$opt = 0; echo("selected"); }
		echo(">".$row['Type']."</option>");
	}
	echo("</select>");
	
?>
	        </td>
	      </tr>									
	      <tr>
	        <td colsapn="2">
	          <div class="button" id="addIngBtn">Add Ingredient</div>
	        </td>
	      </tr>
            </table>
          </td>
        </tr>
      </table>
    </form>
  </body>
</html>
	

