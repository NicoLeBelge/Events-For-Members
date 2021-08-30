<?php
	$pathbdd = './../_local-connect/connect.php';
	$pathfunction = './core/editEvent-functions-core.php';
	include($pathbdd);
	include($pathfunction );
	
	if(modifAuthorization($conn)['success'])
	{
	// echo('<br />'.modifAuthorization($conn)['message'].'<br />');
?>
<div style="border: 4mm ridge grey; padding: 1em;">
	<?php include('./core/editEvent-formulaire-core.php'); ?>
</div>
<script type="text/javascript">
	validate();
</script> 
<?php
	}
	else echo('<br />'.modifAuthorization($conn)['message'].'<br />');
?>

