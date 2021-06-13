<form action="editEventAction.php" method="post">
	<label for="name">Nom de l'event :</label>  <input type="text" id="name" name="name"onchange="validate()"/>
	<p>Date de début de l'évènement : <input type="date" name="datestart" onchange="validate()"/></p>
	<p>Date de fin de l'évènement : <input type="date" name="datelim" onchange="validate()"/></p>
	<p>Sécutisation de l'évènement :
		<input type="radio" id="yes" name="secured" value="yes" checked>
		<label for="yes">yes</label>

		<input type="radio" id="no" name="secured" value="no">
		<label for="no">no</label>
	</p> 
        <label for="mail">e-mail de contact :</label>
        <input type="email" id="mail" name="contact">

	<p>Nombre maximal d'inscrits : <input type="number" name="nbmax" onchange="validate()"/></p>
	<p><input type="submit" value="OK" id="submitButton"></p>
	<input id="id" name="id" type="hidden" value=<?php echo $_GET['id'] ?>>
</form>

