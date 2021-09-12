<?php
	$pathbdd = './../_local-connect/connect.php';
	$pathfunction = './core/editEvent-functions-core.php';
	include($pathbdd);
	include($pathfunction );
	
	if(modifAuthorization($conn)['success'])
	{
	// echo('<br />'.modifAuthorization($conn)['message'].'<br />');
?>
	<?php include('./core/editEvent-formulaire-core.php'); ?>
<script type="text/javascript">
	validate();
</script> 
<?php
	}
	else echo('<br />'.modifAuthorization($conn)['message'].'<br />');
?>

