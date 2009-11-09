<?php
/**
 * W tym przykładzie uzyskujemy bardziej szczegółowe informacje 
 */
include_once('../identificationNumberValidator.class.php');
include_once('peselValidator.class.php');

$pesel=new peselValidator('82030909476');

	foreach($pesel->getErrors() as $error)
	{
		print $error.'<br />';
	}

	print 'Przetwarzny numer: '.$pesel->getPesel().'<br />';

	if($pesel->isValid()) // jeśli jest prawdłowy
	{
		print 'Data urodzenia: '.$pesel->getBirthDate().'<br />'; // Data urodzenia w formacie YYYYMMDD
		print '<ul>';
		print '<li>Podany numer PESEL jest poprawny</li>';

		if($pesel->isMale())
		{
			print '<li>Ten PESEL należy do mężczyzny</li>';
		}
		if($pesel->isFemale())
		{
			print '<li>Ten PESEL należy do kobiety</li>';
		}

		/*
		 * Sprawdzi, czy podana jako argument data pasuje do tej w numerze PESEL
		 * Można np. użyć daty przekazanej w formularzu
		 */
		if($pesel->matchDate('19820309'))
		{
			print '<li>Podana data urodzenia jest zgodna z datą urodzenia znalezioną w numerze PESEL</li>';
		
			$adult='niepełnoletni';
			if($pesel->isAdult())
			{
				$adult='pełnoletni';
			}
			print '<li>Posiadacz tego numeru PESEL jest '.$adult.'</li>';
			}
		else
		{
			print '<li>Podana data urodzenia NIE jest zgodna z datą urodzenia znalezioną w numerze PESEL</li>';
		}
	
		print '</ul>';
	}
else
{
	print 'Podany numer PESEL jest nieprawidłowy<br />';
	
	//opcjonalnie, jeśli chcemy bardziej szczegółowe informacje
	if($pesel->hasErrors())
	{
		foreach($pesel->getErrors() as $error)
		{
			//print$error.'<br />';
			print $error['identifier'].', '.$error['code'].', '.$error['message'].'<br />';
		}
	}
}




?>