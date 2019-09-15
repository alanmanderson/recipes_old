<html>
<head>
<link rel="stylesheet" type="text/css" href="search.css" media="screen"/>
<script type="text/javascript" src="search.js"></script>
<script type="text/javascript">
  function addIngredient(){
    elts=document.getElementsByName("ingredient");
    values=new Array();
    for (i=0;i<elts.length;i++){
		 values[i]=elts[i];
	}
    document.getElementById('ingredients').innerHTML += '<BR /><input id="txtSearch" name=\
"txtSearch" type="text" onkeyup="searchSuggest();" autocomplete="off"/>'
	       elts=document.getElementsByName("ingredient")
	       for (i=0;i<values.length;i++){
			    elts[i].value=values[i];
			    }
  }
</script>
</head>
<body> 
<form name="addRecipe">
<div id="ingredients">
  <input name="ingredient" type="text" onkeyup="searchSuggest();" autocomplete="off"/>
</div>
<input id="addIngredientBtn" name="addIngredientBtn" type="button" value="+" onclick="addIngredient()"/>
<div id="search_suggest">
</div>
</form>
</body>
</html>

