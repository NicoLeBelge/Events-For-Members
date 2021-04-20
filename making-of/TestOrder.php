<?php
session_start();
?>
<!DOCTYPE html>
<html>
    <head>
    </head>

    <body >
	<div id="a"></div>
	<script type="text/javascript">

var UnfilteredArray = [
			{"name":"Smith",
			"nick":"Aldo",
			"age": 32
			},{
			"name":"Parker",
			"nick":"Kid",
			"age": 18
			},{
			"name":"Wayne",
			"nick":"Bull",
			"age": 42
			}
]
console.log("UnfilteredArray");
console.log(UnfilteredArray);
let FilteredArray = UnfilteredArray.filter(function(filter){
		return filter.age <35 ;
});
//console.log("FilteredArray");
//console.log(FilteredArray);
function showitems(item){
	console.log (item)
}

FilteredArray.forEach(showitems);

</script>

    </body>
</html>
