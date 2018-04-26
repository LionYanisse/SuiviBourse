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
if($email != "" && $motdepasse != "")
{
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
			echo "-1";
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
			$mois = "";
			$mois[1] = "septembre";
			$mois[2] = "octobre";
			$mois[3] = "novembre";
			$mois[4] = "decembre";
			$mois[5] = "janvier";
			$mois[6] = "fevrier";
			$mois[7] = "mars";
			$mois[8] = "avril";
			$mois[9] = "mai";
			$mois[10] = "juin";
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
							$lastmonth = strtolower($mois[$v]);
						}
					}
				}
				$idid = $pdo->retournerInformationsEtudiant($email, "id");
				$idEtudiant = $idid["id"];
				$lala = $pdo->retournerInformationsMensualite($idEtudiant, $lastmonth);
				$lastmonthviewed = $lala[$lastmonth];
				if($lastmonthviewed == 0)
				{
					echo "1";
					$pdo->mettreAJourMensualite($idEtudiant, $lastmonth);
				}
				else
				{
					echo "0";
				}
			}
			else
			{
				"-1";
			}
		}
}		
	
?>