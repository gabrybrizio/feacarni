<?php
	ini_set('opcache.enable', '0');

	require '../app-admin/lib/dorothy.php';
	require_once '../app-admin/vendor/php-saml/_toolkit_loader.php';
	require_once '../app-admin/vendor/php-saml/settings.php';

	$auth = new OneLogin_Saml2_Auth($settings);


	//---------- risposta di login inviata dall'IDP (Identity Provider) al SP (Service Provider)
	if (isset($_GET['login-response'])) {
		if (isset($_SESSION) && isset($_SESSION['AuthNRequestID'])) {
			$requestID = $_SESSION['AuthNRequestID'];
		} else {
			$requestID = null;
		}
	
		$auth->processResponse($requestID);
		$errors = $auth->getErrors();	
		//echo $auth->getLastErrorReason();
	
		if (!empty($errors)) {
			echo '<p>',implode(', ', $errors),'</p>';
		}
	 
		if (!$auth->isAuthenticated()) {
			echo "Utente NON loggato da IDP<br>";
			exit();
		}
	
		$_SESSION['samlUserdata'] = $auth->getAttributes();
		$_SESSION['samlNameId'] = $auth->getNameId();
		$_SESSION['samlNameIdFormat'] = $auth->getNameIdFormat();
		$_SESSION['samlNameIdNameQualifier'] = $auth->getNameIdNameQualifier();
		$_SESSION['samlNameIdSPNameQualifier'] = $auth->getNameIdSPNameQualifier();
		$_SESSION['samlSessionIndex'] = $auth->getSessionIndex();
		unset($_SESSION['AuthNRequestID']);

		//if (isset($_POST['RelayState']) && OneLogin_Saml2_Utils::getSelfURL() != $_POST['RelayState']) {
		//	$auth->redirectTo($_POST['RelayState']);
		//}

		$attributes = $auth->getAttributes();
		$username = $attributes["http://schemas.xmlsoap.org/ws/2005/05/identity/claims/emailaddress"][0];
		echo "Utente loggato da IDP: ", $username, "<br>";

		// Ricerco l'utente registrato
		$user = db::table('utenti')->where("username", "=", $username)->andWhere("abilitato", "=", "1")->first();
			
		if($user){
			// Recupero i data 
			$data = module::dataOf($user);
 
			// Login OK
			s::set('public_logged', true);
			s::set('public_id', $user->GUID());
			s::set('public_username',a::data('username'));
			s::set('public_name',a::data('cognome') . ' ' .a::data('nome'));
			s::set('public_tipologia',a::data('tipologia'));
			
			//echo var_dump($user);
			echo "Utente trovato su SP (Dorothy): ", $username, "<br>";
		}else{
			$login_fedback = "utente non trovato";
			echo "Utente NON trovato su SP (Dorothy): <br>";
		}


	//---------- richista di login dal SP all'IDP
	} else if (isset($_GET['login-request'])) { 

		//$returnTo = $spBaseUrl.'/demo1/attrs.php';
		$auth->login();

		# If AuthNRequest ID need to be saved in order to later validate it, do instead
		# $ssoBuiltUrl = $auth->login(null, array(), false, false, true);
		# $_SESSION['AuthNRequestID'] = $auth->getLastRequestID();
		# header('Pragma: no-cache');
		# header('Cache-Control: no-cache, must-revalidate');
		# header('Location: ' . $ssoBuiltUrl);
		# exit();

		
	//---------- richista di logout dal SP all'IDP
	} else if (isset($_GET['logout-request'])) {
		echo "$security[logoutResponseSigned]", $security['logoutResponseSigned'];
		$auth->logout('https://localhost:8000/pages/sso.php');
	
	
	//---------- risposta di logout dal SP all'IDP
	} else if (isset($_GET['logout-response'])) {
		if (isset($_SESSION) && isset($_SESSION['LogoutRequestID'])) {
			$requestID = $_SESSION['LogoutRequestID'];
		} else {
			$requestID = null;
		}

		echo "requestID: ", $requestID, "<br>";
	
		$auth->processSLO(true, $requestID);
		$errors = $auth->getErrors();
		echo $auth->getLastErrorReason();

		if (empty($errors)) {
			s::destroy();
			echo "Utente sloggato su IDP e SP (Dorothy)<br>";
		} else {
			echo "Utente NON sloggato su IDP e SP (Dorothy)<br>";
			echo '<p>', implode(', ', $errors), '</p>';
		}
	}
	
	if (user::isLogged()){
		echo 'Sei loggato su IDP e SP (Dorothy) come: ', s::get('public_username'), "<br>";
		echo '<br><a href="sso.php?logout-request">Logout</a>';
	}else{
		echo '<br><a href="sso.php?login-request">Login</a>';
	}
?>

