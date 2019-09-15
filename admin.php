<?php
include_once('../commons/checkauth.php');
$_SESSION["db"]="recipes";
include_once('../commons/dbconnect.php');

if($_GET['action']=="listIngredients"){
  $query = "SELECT IngredientID, Name, TypeID FROM Ingredients";
  if (isset($_GET['ingType'])) {
    $ingType = htmlentities($_GET['ingType']);
    $query.=" WHERE TypeID=$ingType";
  }
  $result = mysql_query($query) or die(mysql_error());
  $output = array();
  while($row=mysql_fetch_Assoc($result)){
    $output[]=$row;
  }
  print json_encode($output);
  exit(0);
}else if($_GET['action']=="listIngTypes"){
  $query = "SELECT * FROM IngTypes";
  $result = mysql_query($query) or die(mysql_error());
  $output = array();
  while($row=mysql_fetch_Assoc($result)){
    $output[]=$row;
  }
  print json_encode($output);
  exit(0);
}else if($_GET['action']=="listMeasurements"){
  $se_measurements = "SELECT * FROM Measurements";
  $result = mysql_query($se_measurements) or die (mysql_error());
  $output = array();
  while($row=mysql_fetch_Assoc($result)){
    $output[]=$row;
  }
  print json_encode($output);
  exit(0);
}else if ($_GET['action']=="getCategories"){
  $query = "SELECT Category FROM RecipeToCategories";
  $result = mysql_query($query);
  $output = array();
  while($row=mysql_fetch_Assoc($result)){
    $output[]=$row;
  }
  print json_encode($output);
  exit(0);
}

if ($_POST['title']){
  $title = htmlentities($_POST['title']);
  $bakeTime = htmlentities($_POST['bakeTime']);
  $directions = htmlentities($_POST['directions']);
  $source = htmlentities($_POST['source']);
  $ingAmounts = $_POST['ingAmounts'];
  $ingAmountLbls = $_POST['ingAmountLbls'];
  $ingredients = $_POST['ingredients'];
  $makesQty = htmlentities($_POST['makesQty']);
  $makesLbl = htmlentities($_POST['makesLbl']);
  $categories = $_POST['categories'];
  $error="";
  mysql_query("begin");
  $in_recQuery = "INSERT INTO Recipes (Title, Source, PrepTime, Directions,MakesQty,MakesLbl) VALUES('$title','$source',$bakeTime,'$directions',$makesQty,'$makesLbl')";
  $results = mysql_query($in_recQuery);
  if (mysql_error()){
    mysql_query("rollback");
    $error = "There was an error adding the Recipe: ".mysql_error()."  $in_recQuery";
  } else {
    $recipeID = mysql_insert_id();
    for ($i=0;$i<count($ingredients);$i++){
      $ingAmount = htmlentities($ingAmounts[$i]);
      $ing = htmlentities($ingredients[$i]);
      $ingAmountLbl = htmlentities($ingAmountLbls[$i]);
      $query = "INSERT INTO RecipeToIngredients (RecID, IngID, Quantity, QuantityLbl) VALUES ($recipeID, $ing, $ingAmount, '$ingAmountLbl')";
      $results = mysql_query($query);
      if (mysql_error()) {
        mysql_query("rollback");
        $error = "There was an error in your ingredients: ".mysql_error()."  $query";
        break;
      }
    }
    if ($error=="") {
      for ($i=0;$i<count($categories);$i++){
	$category = htmlentities($categories[$i]);
	$query = "INSERT INTO RecipeToCategories VALUES($recipeID,'$category')";
	$results = mysql_query($query);
        if (mysql_error()){
	  mysql_query("rollback");
	  $error = "There was an error in your categories: ".mysql_error()."  $query";
	  break;
	}
      }
    }
    }
  $referrer = $_POST['referrer'];
  if ($referrer){
    header("Location: $referrer");
  }
}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
  <head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8"/>
    <link rel="stylesheet" type="text/css" href="stylesheets/main.css" />
    <link rel="stylesheet" type="text/css" href="../commons/stylesheets/combobox.css" />  
    <script type="text/javascript" src="../commons/javascripts/jquery-1.4.2.js"></script>
    <script type="text/javascript" src="../commons/javascripts/json2.js"></script>
    <script type="text/javascript" src="../commons/javascripts/jquery.label_over.js"></script>
    <!--<script type="text/javascript" src="../commons/javascripts/combobox.js"></script>-->
    <script type="text/javascript" src="javascripts/addIngToTable.js"></script>
    <link href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8/themes/base/jquery-ui.css" rel="stylesheet" type="text/css"/>
    <!--<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.5/jquery.min.js"></script>-->
    <script src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8/jquery-ui.min.js"></script>
    <script type="text/javascript">

        var ingTypesArr = [];
        var allIngredients = [];
        var measurementsArr = [];
        var categories = [];
        var ingCount = 1;

	$(function() {
	    $('label.targetLabel').labelOver('labelover');

	    $.getJSON('admin.php','action=listIngredients', function(data) {
		allIngredients = data;
		$.getJSON('admin.php','action=listMeasurements', function(data) {
		    measurementsArr = data;
		    addIngTable();
		  });
	      });
	    $.getJSON('admin.php','action=getCategories', function(data) {
		for (i=0;i<data.length;i++){
		  categories[i] = data[i].Category;
		}
	      });
	    $.getJSON('admin.php','action=listIngTypes', function(data) {
		ingTypesArr = data;
		var opts = $("#newIngredientType").attr("options");
		for (i=0;i<ingTypesArr.length;i++){
		  opts[i] = new Option(ingTypesArr[i].Type, ingTypesArr[i].ID);
		}
	      });
	})

        function addIngTable(){
	  var TR = $('<tr id="ingRow'+ingCount+'"></tr>');
	  var ingTD = $("<td></td>");
	  var removeIMG = $('<a href="#" onclick="removeRow('+ingCount+')"><img src="images/remove.png" height="20" width="20"/></a>');

	  ingTD.append(removeIMG);
	  var ingSEL = $('<select name="ingredients[]" id="ing'+ingCount+'" ></select>');
	  var ingOpts = ingSEL.attr("options");
	  for (i=0;i<allIngredients.length;i++){
	    ingOpts[i] = new Option(allIngredients[i].Name,allIngredients[i].IngredientID);
	  }
	  ingTD.append(ingSEL);
	  TR.append(ingTD);
	  var amtTD = $("<td></td>");
	  var amtsELT = $('<input type="text" name="ingAmounts[]" />');
	  amtTD.append(amtsELT);
	  TR.append(amtTD);
          var msmtTD = $("<td></td>");
	  var msmtSEL = $('<select name="ingAmountLbls[]"></select>');
	  var opts = msmtSEL.attr("options");
	  for (i=0;i<measurementsArr.length;i++){
	    opts[i] = new Option(measurementsArr[i].name, measurementsArr[i].name);
	  }
	  msmtTD.append(msmtSEL);
	  TR.append(msmtTD);
	  $("#IngredientsTable").append(TR);
          $("#ing"+ingCount).combobox();
	  ingCount++;
        }

        function addCatTable(){
	  var TR = $('<tr id="catRow"/>');
	  var catTD = $('<td/>');
	  var catInp = $('<input name="categories[]" id="autocomplete"/>');
	  catInp.autocomplete({
	    source: categories
	   });

	  catTD.append(catInp);
	  TR.append(catTD);
	  $("#CategoriesTable").append(TR);
	}
		
	$(function() {
	    $("#addRecBtn").click(function() {
		document.frmAddRec.submit();
	    });
	})

        function removeRow(rowCount){
	  $("#ingRow"+rowCount).remove();
	}

        function addIngredientToDB(){
	  var ingName = $("#newIngredient").val();
	  var ingType = $("#newIngredientType").val();
	  $.ajax({
	    type: "POST",
	    url: "adminIng.php",
	    data: "name="+ingName+"&type="+ingType+"&stopExecution=true",
	    dataType: 'json',
            success: function(data) {
		$("#ingAdded").html("The Ingredient was Added to the DB");
		$("#newIngredient").val("");
	        allIngredients = data;
	      },
	    error: function(xhr, ajaxOptions, thrownError) {
		alert("HERE");
		alert(thrownError+" " + xhr.status);
	      }
	  });

	}
  (function( $ ) {
  $.widget( "ui.combobox", {
    _create: function() {
	var input,
	  self = this,
	  select = this.element.hide(),
	  selected = select.children( ":selected" ),
	  value = selected.val() ? selected.text() : "",
	  wrapper = this.wrapper = $( "<span>" )
	  .addClass( "ui-combobox" )
	  .insertAfter( select );

	input = $( "<input>" )
	  .appendTo( wrapper )
	  .val( value )
	  .addClass( "ui-state-default ui-combobox-input" )
	  .autocomplete({
	    delay: 0,
		minLength: 0,
		source: function( request, response ) {
		var matcher = new RegExp( $.ui.autocomplete.escapeRegex(request.term), "i" );
		response( select.children( "option" ).map(function() {
		      var text = $( this ).text();
		      if ( this.value && ( !request.term || matcher.test(text) ) )
			return {
			label: text.replace(
					    new RegExp(
						       "(?![^&;]+;)(?!<[^<>]*)(" +
						       $.ui.autocomplete.escapeRegex(request.term) +
						       ")(?![^<>]*>)(?![^&;]+;)", "gi"
						       ), "<strong>$1</strong>" ),
			    value: text,
			    option: this
			    };
		    }) );
	      },
		select: function( event, ui ) {
		ui.item.option.selected = true;
		self._trigger( "selected", event, {
		  item: ui.item.option
		      });
	      },
		change: function( event, ui ) {
		if ( !ui.item ) {
		  var matcher = new RegExp( "^" + $.ui.autocomplete.escapeRegex( $(this).val() ) + "$", "i" ),
		    valid = false;
		  select.children( "option" ).each(function() {
		      if ( $( this ).text().match( matcher ) ) {
			this.selected = valid = true;
			return false;
		      }
		    });
		  if ( !valid ) {
		    // remove invalid value, as it didn't match anything
		    $( this ).val( "" );
		    select.val( "" );
		    input.data( "autocomplete" ).term = "";
		    return false;
		  }
		}
	      }
	    })
	  .addClass( "ui-widget ui-widget-content ui-corner-left" );

	input.data( "autocomplete" )._renderItem = function( ul, item ) {
	  return $( "<li></li>" )
	  .data( "item.autocomplete", item )
	  .append( "<a>" + item.label + "</a>" )
	  .appendTo( ul );
	};

	$( "<a>" )
	  .attr( "tabIndex", -1 )
	  .attr( "title", "Show All Items" )
	  .appendTo( wrapper )
	  .button({
	    icons: {
	      primary: "ui-icon-triangle-1-s"
		  },
		text: false
		})
	  .removeClass( "ui-corner-all" )
	  .addClass( "ui-corner-right ui-combobox-toggle" )
	  .click(function() {
	      // close if already visible
	      if ( input.autocomplete( "widget" ).is( ":visible" ) ) {
		input.autocomplete( "close" );
		return;
	      }

	      // work around a bug (likely same cause as #5265)
	      $( this ).blur();

	      // pass empty string as value to search for, displaying all results
	      input.autocomplete( "search", "" );
	      input.focus();
	    });
      },

	destroy: function() {
	this.wrapper.remove();
	this.element.show();
	$.Widget.prototype.destroy.call( this );
      }
    });
})( jQuery );

$(function() {
    $( "#combobox" ).combobox();
    $("select").combobox();
});
</script>
    <title>Alan and Heathers Recipe Database</title>
  </head>
  <body style="font-family: Arial;border: 0 none;">
      <?php
          include("navBar.php");
      ?>
    <form method="POST" name="frmAddRec" action="<?php echo htmlentities($PHP_SELF); ?>">
      <table>
	<tr>
	  <td>
	    <table width="800px">
	      <tr>
		<td colspan="2">
		  <div class="labeledfield">
		    <label class="targetLabel" for="recTitle">Title</label>
		    <input type="text" id="recTitle" name="title"/>
		  </div>
		</td>
	      </tr>
	      <tr>
		<td colspan="2">
		  <div class="labeledfield">
		    <label class="targetLabel" for="bakeTime">Preparation Time</label>
		    <input type="text" id="bakeTime" name="bakeTime"/>
		  </div>
		</td>
	      </tr>
	      <tr>
		<td colspan="2">
		  <table width="100%" id="IngredientsTable">
		  </table>
		</td>
	      </tr>
	      <tr><td colspan="2">
                <a href="#" onclick="addIngTable()">
                  <img src="images/add.png" height="20px" width="20px" />Add Ingredient
                </a>
              </td></tr>
              <tr height="100px" style="display:table; border: 1px solid black;">
	        <td>
	          Type:<select id="newIngredientType"></select> 
                </td>
	        <td>
                  <div class="labeledfield" height="100%">
		    <label class="targetLabel" for="newIngredient">New Ingredient</label>
		    <input type="text" id="newIngredient" name="newIngredient"/>
		  </div>
	          <input type="button" name="addButton" value="Add Ingredient to DB" onclick="addIngredientToDB()"/>
                </td>
              </tr>
              <tr>
	        <td id="ingAdded">
                </td>
              </tr>
	      <tr height="100px">
		<td colspan="2" height="100%">
		  <div class="labeledfield" height="100%">
		    <label class="targetLabel" for="directions">Directions</label>
		    <input type="text" id="directions" name="directions"/>
		  </div>
		</td>
	      </tr>
	      <tr height="100px">
		<td height="100%">
		  <div class="labeledfield" height="100%">
		    <label class="targetLabel" for="makesQty">Makes Quantity</label>
		    <input type="text" id="makesQty" name="makesQty"/>
		  </div>
		</td>
		<td height="100%">
		  <div class="labeledfield" height="100%">
		    <label class="targetLabel" for="makesLbl">Label</label>
		    <input type="text" id="makesLbl" name="makesLbl"/>
		  </div>
		</td>
	      </tr>
	      </tr>
	      <tr>
		<td colspan="2">
		  <div class="labeledfield">
		    <label class="targetLabel" for="source">Source</label>
		    <input type="text" id="source" name="source"/>
		  </div>
		</td>
	      </tr>
	      <tr>
                <td colsapn="2">
                  <table id="CategoriesTable">
                    <tr><td>
	            <a href="#" onclick="addCatTable()"><img height="20" width="20" src="images/add.png"/>AddCategory</a>
	            </td></tr>
	          </table>
                </td>
              </tr>
	      <tr>
		<td colspan="2">
		  <div class="button" id="addRecBtn">Add Recipe</div>
		</td>
	      </tr>
	    </table>
	  </td>
	</tr>
      </table>
    </form>
<?php
  if ($error!=""){
    $ingAmounts = $_POST['ingAmounts'];
    $ingAmountLbls = $_POST['ingAmountLbls'];
    $ingredients = $_POST['ingredients'];
#    $categories = $_POST['categorieks'];
    echo <<<ERRSCRIPT
<script type="text/javascript">
    $("#recTitle").val("$title");
    $("#bakeTime").val("$bakeTime");
    $("#directions").val("$directions");
    $("#makesQty").val("$makesQty");
    $("#makesLbl").val("$makesLbl");
    $("#source").val("$source");

ERRSCRIPT;
    for ($i=0;$i<count($ingredients);$i++){
      if ($i>0){
	echo "addIngTable();\n";
      }
      //echo "$('input[name=\"ingredients[]\"]:eq($i) option:text=Raisin').attr('selected','selected');\n";
      echo "$('.ui-autocomplete-input').focus().val($ingredients[$i]);";
      echo "$('input[name=\"ingAmounts[]\"]:eq($i)').val('$ingAmounts[$i]');\n";
      echo "$('input[name=\"ingAmountLbls[]\"]:eq($i)').val('$ingAmountLbls[$i]');\n";
    }
    for ($i=0;$i<count($categories);$i++){
      echo "addCatTable();\n";
      echo "$('input[name=\"categories[]\"]:eq($i)').val('$categories[$i]');\n";
    }
    echo "</script>";

}
  include("navBar.php");
?>
  </body>
</html>