<?php
require_once("../commons/setup.php");
setup(false,"recipes");
$recID = htmlentities($_GET["recipeID"]);
if ($recID==""){
  $recID = 1;
}

$decConversion = array(
                ".125"=>"1/8",
		".25"=>"1/4",
                ".333"=>"1/3",
		".375"=>"3/8",
		".5"=>"1/2",
		".625"=>"5/8",
		".666"=>"2/3",
		".667"=>"2/3",
		".75"=>"3/4",
		".875"=>"7/8"
		       );

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
  <head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8"/>
    <link rel="stylesheet" type="text/css" href="stylesheets/main.css"/>
    <style type="text/css">
      @import '../commons/stylesheets/css.php?scheme=recipe';
    </style>
    <script type="text/javascript" src="../commons/stylesheets/jquery-1.4.2.js"></script>
    <script type="text/javascript" src="../commons/stylesheets/jquery.label_over.js"></script>
    <script type="text/javascript">
      $(function() {
        $('label.targetLabel').labelOver('labelover'); 
      })
		
      $(function() {
	$("#adjustBtn").click(function() {
	    document.frmRecipe.submit();
	});
      })
			
    </script>
    <title>Alan and Heathers Recipe Database</title>
  </head>
  <body style="font-family: Arial;border: 0 none;">
  <div id="content">
    <form method="POST" name="frmRecipe" action="<?php echo($_SERVER['PHP_SELF']."?recipeID=$recID"); ?>">
      <div id="ingPanel">
        <table class="recipeTable">
<?php
	$factor = $_POST['factor'];
	if (!$factor){
		$factor = 1;
	}
	$query = "SELECT * FROM Recipes WHERE RecipeID=$recID";
	$resRecipies = mysql_query($query) or die(mysql_error());
	while($row = mysql_fetch_array($resRecipies)){
	  $recID = $row['RecipeID'];
	  $makes = $factor * $row['MakesQty'];
	  echo("<tr><td colspan=\"5\">".$row['Title']." (".$makes." ".$row['MakesLbl'].")</td></tr>");
	  echo("<th colspan=\"5\">Ingredients</th>");
	  $query = "SELECT * FROM RecipeToIngredients JOIN Ingredients ON RecipeToIngredients.IngID=Ingredients.IngredientID".
	    " WHERE RecipeToIngredients.RecID=$recID";
	  $ingResult = mysql_query($query);
	  while($ingredients = mysql_fetch_array($ingResult)){
	    $qty = $factor * $ingredients['Quantity'];
	    $whole = floor($qty);
	    $decimal = $qty-$whole;
	    #round the decimal, turn it into a string and remove the leading 0
	    $decimal = substr((string) round($decimal, 3),1);
	    if (isset($decConversion[$decimal])){
	      $decimal = $decConversion[$decimal];
	    }
	    if ($whole==0){
	      unset($whole);
	    }
	    if ($decimal==".0"){
	      unset($decimal);
	    }
	    echo("<tr><td colspan=\"3\">".$ingredients['Name']."</td>");
	    echo("<td>$whole $decimal</td>");
	    echo("<td>".$ingredients['QuantityLbl']."</td>");
	    echo("</tr>");
	  }
	  echo("</table>");
	  echo("</div>"); # End ingPanel Div
	  echo("<div id=\"dirPanel\">");
	  echo("<table>");
	  echo("<th colspan=\"5\">Directions</th>");
	  echo("<tr><td colspan=\"5\">".$row['Directions']."</td></tr>");
	  echo("<tr></tr>");
	  echo("</table></div>");
	}
?>
        <div id="adjustment" style="clear:both; text-align:center;">
          <input type="text" style="width: 30px; height: 100%;" name="factor" value="<?php echo $_POST['factor']; ?>" />
          <div class="button" id="adjustBtn" style="display:inline;">Adjust recipe</div>
        </div>
      </form>
    </div>
  </body>
</html>
	

