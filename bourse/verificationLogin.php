<?php 
header('Access-Control-Allow-Origin: *');
include('simple_html_dom.php');
//informations confidentielles
$email = @$_GET['email'];
$COOKIE_FILE = dirname(__FILE__) . '/'.$email.'.txt';
$motdepasse = @$_GET['password'];
session_start();

$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, "https://www.portail-vie-etudiante.fr/envole");
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
	curl_setopt($ch, CURLOPT_COOKIESESSION, TRUE);
	curl_setopt($ch, CURLOPT_COOKIEJAR, $COOKIE_FILE);
	curl_setopt($ch, CURLOPT_COOKIEFILE, $COOKIE_FILE); 
	curl_setopt($ch, CURLOPT_USERAGENT,
		"Mozilla/5.0 (Windows; U; Windows NT 6.0; en-US; rv:1.7.12) Chrome/43.0.2357.65 Safari/537.36");
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_REFERER, $_SERVER['REQUEST_URI']);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
	$html = curl_exec($ch);	
	curl_close($ch);

	
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
	}
	else
	{
		echo "1";
	}