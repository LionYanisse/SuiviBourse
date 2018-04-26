<?php 
define('ERROR_LOG_FILE', 'error.log');
header('Access-Control-Allow-Origin: *');
include('simple_html_dom.php');
include('fonctions.php');
$pdo = PdoLien::getPdoLien();
//informations confidentielles
$email = @$_GET['email'];
$COOKIE_FILE = dirname(__FILE__) . '/'.$email.'.txt';
$motdepasse = @$_GET['password'];
session_start();
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, "https://www.portail-vie-etudiante.fr/pveredirect/index.php");
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
	curl_setopt($ch, CURLOPT_COOKIESESSION, TRUE);
	curl_setopt($ch, CURLOPT_COOKIEJAR, $COOKIE_FILE);
	curl_setopt($ch, CURLOPT_COOKIEFILE, $COOKIE_FILE);
	curl_setopt($ch, CURLOPT_USERAGENT,
		"Mozilla/5.0 (Windows; U; Windows NT 6.0; en-US; rv:1.7.12) Chrome/43.0.2357.65 Safari/537.36");
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_REFERER, "https://www.portail-vie-etudiante.fr/envole/portal/index.php");
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
	$html = curl_exec($ch);
	
	curl_setopt($ch, CURLOPT_URL, "https://idp.portail-vie-etudiante.fr/idp/Authn/UserPassword");
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, "j_username=$email&j_password=$motdepasse");
	$html = curl_exec($ch);

	$html = str_get_html($html);
	$s = "";
	$r = "";
	foreach($html->find('input') as $input) {
		if($input->name == "RelayState")
		{
			$test = explode(";", $input->value);
			$r = "ss:mem:".$test[2];
		}
		if($input->name == "SAMLResponse")
		{
			$s = $input->value;
		}
	}
	if($s == "")
	{
		echo "0";
		unlink($COOKIE_FILE);
	}
	else
	{
		curl_setopt($ch, CURLOPT_URL, "https://www.portail-vie-etudiante.fr/Shibboleth.sso/SAML2/POST");
		curl_setopt($ch, CURLOPT_POST, 1);
		$args = array(
		'SAMLRequest' => $s,
		'RelayState' => $r
		);
		curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($args));
		$html = curl_exec($ch);
		curl_setopt($ch, CURLOPT_URL, "https://www.portail-vie-etudiante.fr/pveredirect/index.php");
		$html = curl_exec($ch);
		curl_setopt($ch, CURLOPT_URL, "https://www.portail-vie-etudiante.fr/pve-login");
		$html = curl_exec($ch);
		curl_setopt($ch, CURLOPT_URL, "https://www.portail-vie-etudiante.fr/pve-login");
		$html = curl_exec($ch);
		curl_setopt($ch, CURLOPT_URL, "https://www.portail-vie-etudiante.fr/suiviPVE/");
		$html = curl_exec($ch);
		curl_setopt($ch, CURLOPT_URL, "https://www.portail-vie-etudiante.fr/suiviPVE/notif?rqRang=0");
		$html = curl_exec($ch);
		$html = str_get_html($html);
		$compteur = 0;
		$compteur2 = 0;
		$mois = array('1'=> 'Septembre',
		'2'=>'Octobre',
		'3'=>'Novembre',
		'4'=>'Décembre',
		'5'=>'Janvier',
		'6'=>'Février',
		'7'=>'Mars',
		'8'=>'Avril',
		'9'=>'Mai',
		'10'=>'Juin');
		$bourse = false;
		if($html->find('td[align=center]'))
		{
			$bourse = true;
		}
		if($bourse)
		{
			foreach($html->find('td[align=center]') as $e)
			{
				$arr[$compteur2][$compteur] = trim($e->innertext);
				$compteur++;
				if($compteur >= 4)
				{
					$compteur2++;
					$compteur=0;
				}
			}
			$echelon = 0;
			$lastmonth = "";
			foreach($arr as $k)
			{
				foreach($k as $index=>$v)
				{
					if($index == 0)
					{
						echo "Mensualité : <strong>$mois[$v]</strong><br/>";
						$lastmonth = strtolower($mois[$v]);
					}
					else if($index == 1)
					{
						echo "Echelon : <strong>$v</strong><br/>";
						$echelon = $v;
					}
					else if($index == 2)
					{
						echo "Montant : <strong>$v</strong><br/>";
					}
					else if($index == 3)
					{
						echo "Paiement : <strong>réalisé</strong>";
						echo "<br/><br/>";
					}
				}
			}
			$test = $pdo->Connexion($email, $echelon, $lastmonth);
			echo "<font color='red'>La mention <strong>'réalisée'</strong> signifie que la bourse sera versée sur votre compte dans 7 à 10 jours.</font>";
		}
		else
		{
			"Aucune informations à propos de votre bourse :'((";
		}
	}
	
	
?>