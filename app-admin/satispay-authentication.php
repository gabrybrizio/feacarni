<?php
	require 'inc/menu.php';

	$publicKey = '';
	$privateKey = '';
	$keyId = '';

  if (!empty($_POST["token-di-autenticazione"])) {
	require_once "vendor/satispay/gbusiness-api-php-sdk/init.php";

	try {
		\SatispayGBusiness\Api::setSandbox(config::satispay_is_sandbox());
		$authentication = \SatispayGBusiness\Api::authenticateWithToken($_POST["token-di-autenticazione"]);
	
		$publicKey = $authentication->publicKey;
		$privateKey = $authentication->privateKey;
		$keyId = $authentication->keyId;
	
		//file_put_contents("satispay-authentication.json", json_encode([
		//"public_key" => $publicKey,
		//"private_key" => $privateKey,
		//"key_id" => $keyId
		//], JSON_PRETTY_PRINT));
	} catch (\Throwable $th) {
		
	}
  }
?>
  
<h1>Autenticazione Satispay</h1>

<form id="formSatispay" method="post" enctype="multipart/form-data">
	
	<!-- DATI -->
	<div class="box">
		<?php		
			 brick::field("text", "label:Token di autenticazione");
			echo  brick::btn("text:INVIA", "click:document.getElementById('formSatispay').submit();");
			brick::br();
			 brick::field("readonly", "label:Public key", "value:" . $publicKey);
			 brick::field("readonly", "label:Private key", "value:" . $privateKey);
			 brick::field("readonly", "label:Key Id", "value:" . $keyId);
		?>
	</div>
	
</form>

<?php 
	require 'inc/footer.php'; 
 ?>
