<?php
/**
 * W tym przykładzie sprawdzamy na raz kilka numerów PESEL
 *
 */
include_once('../identificationNumberValidator.class.php');
include_once('peselValidator.class.php');

//definijemy kilka peseli - wpisz swój
$pesels=array("82030909476", "82030900000");

// wywołujemy statycznie
$multiple=peselValidator::checkStack($pesels);

foreach($multiple as $pesel)
{
	print 'Przetwarzny numer: '.$pesel->getPesel().'<br />';

	if($pesel->isValid()) // jeśli jest prawdłowy
	{
		print 'Data urodzenia: '.$pesel->getBirthDate().'<br />'; // Data urodzenia w formacie YYYYMMDD
		print '<ul>';
		print '<li>Podany numer PESEL jest poprawny</li>';

		if($pesel->isMale())
		{
			print '<li>Ten PESEL należy do kobiety</li>';
		}
		if($pesel->isFemale())
		{
			print '<li>Ten PESEL należy do mężczyzny</li>';
		}

		/*
		 * Sprawdzi, czy podana jako argument data pasuje do tej w numerze PESEL
		 * Można np. użyć daty przekazanej w formularzu
		 */
		if($pesel->matchDate('1982'))
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
		print '<li>Podany numer PESEL jest nieprawidłowy</li>';
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
	print '<br /><br />';
}




?>