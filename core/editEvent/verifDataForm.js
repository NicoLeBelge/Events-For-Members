function checkDateStart(date_form)
{
	const date = new Date(); 
	const date1 = new Date(date_form);
	if(date1.getYear() >= date.getYear())
	{
		if(date1.getMonth() >= date.getMonth())
		{
			if(date1.getDate() >= date.getDate())
			{
				return(true);
			}
			else
			{
				console.log("Veuillez préciser un jour supérieur");
			}
		}
		else
		{
			console.log("Veuillez préciser un mois supérieur");
		}
	}
	else
	{
		console.log(date.getMonth());
		console.log(date1);
		console.log("Veuillez préciser une année supérieure");
	}
	return(false);
}


function checkDateEnd(dateStart, dateEnd)
{
	const date1 = new Date(dateStart);
	const date2 = new Date(dateEnd);

	if(date1 > date2)
	{
		console.log("Veuillez préciser un jour de fin supérieur");
		return(false)
	}
	return(true);
}



function validate()
{
	var datestart = document.getElementsByName('datestart');
	var datelim = document.getElementsByName('datelim');
	var secured = document.getElementsByName('secured');
	var nbmax = document.getElementsByName('nbmax');
	var nameEvent = document.getElementsByName("name");
	var email = document.getElementsByName("contact");
	const button = document.getElementById('submitButton');

	if (datestart[0].value != "" || datelim[0].value !="" || nbmax[0].value !="" ||  nameEvent[0].value != "" || email[0].value != "") 
	{
		console.log("teste des paramètres !"+nbmax[0].value);
		var error = false;
		if(datestart[0].value != "")
		{
			if(!checkDateStart(datestart[0].value))
			{	
				error=true;
			}
			if(datelim[0].value !="")
			{
				if(! checkDateEnd(datestart[0].value, datelim[0].value))
				{
					error=true;
				}
			}
		}

		/*
		Faut-il autoiser la modification de datelim si on ne renseigne pas datestart ? 
		OUI ! 
		*/
		if(nbmax[0].value<0)
		{
			error=true;
			console.log("Veuillez préciser un nombre positif");
		}

		if(error == true)
		{
			button.disabled = true;
			console.log("erreur");
		}
		else
		{
			button.disabled = false;
		}
	}
	else
	{
		button.disabled=true;
	}
}