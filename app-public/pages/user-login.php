<?php
    require __DIR__ . '/../../app/init.php';

	$login_fedback = "";
	$username = escape::html(get("username"));
	$password = get("password");
	$anticsrf = get("anticsrf");

	if(r::is('post') && get('login') && csrf($anticsrf)) {

		// I campi devono essere valorizzati
		if($username != "" && $password != ""){

			// Ricerco l'utente registrato
			$user = db::table('utenti')->where("username", "=", $username)->andWhere("abilitato", "=", "1")->first();

			if($user){

				// Recupero i data
				$data = module::dataOf($user);
				$failure = (int)$user->login_attempt();

				if($failure > config::login_failure_limit()){
					// Utente bannato
					$login_fedback = "banned";
				}else{
					// Utente non bannato, controllo la password
					if(password::match($password,a::data('password'))){

						// Login OK

						// Controllo se esiste un ordine anonimo fatto dall'utente prima di loggarsi, quindi ancora anonimo
						$ordine_anonimo = module::listOf('ordini')->where('utente', '=', s::get('public_id'))->andWhere("stato", "=", OrderStatus::Carrello)->first();

						if($ordine_anonimo){

							// Se esistono ordini a carrello dell'utente loggato fatti in precedenza li cancello
							db::table('ordini')->where('utente', '=', $user->GUID())->andWhere("stato", "=", OrderStatus::Carrello)->delete();

							// Associo l'ordine anonimo all'utente loggato
							$ordine_data = module::dataOf($ordine_anonimo);
							$ordine_data['utente'] = $user->GUID();

							//a::show($ordine_data);

							$ordine_update = db::table('ordini')->where('GUID', '=', $ordine_anonimo->GUID())->update(array(
								'utente' => $user->GUID(),
								'data' => a::json($ordine_data),
							));
						}

						s::set('public_logged', true);
						s::set('public_id', $user->GUID());
						s::set('public_username',a::data('username'));
						s::set('public_name',a::data('cognome') . ' ' .a::data('nome'));
						s::set('public_tipologia',a::data('tipologia'));

						// Azzero login_attempt
						$update_login_attempt = db::table('utenti')->where('GUID', '=', $user->GUID())->update(array('login_attempt' => 0));

						if($update_login_attempt){

							// Se c'è un ordine a carrello per quest'utente lo mando alla pagina di pagamento
							$ordine_utente = db::table('ordini')->where('utente', '=', s::get('public_id'))->andWhere("stato", "=", OrderStatus::Carrello)->first();

							if($ordine_utente){
								util::jumpTo("/carrello/");
							}else{
								util::jumpTo('/');
							}

							die();
						}

					}else{
						// Login KO
						$login_fedback = "error";
						$failure ++;
						$update_login_attempt = db::table('utenti')->where('GUID', '=', $user->GUID())->update(array('login_attempt' => $failure));
					}
				}

			}else{
				$login_fedback = "error";
			}

		}else{
			$login_fedback = "incomplete";
		}

	}

	page::title(lang::get('account-login'));

	require __DIR__ . '/../inc/header.php';

?>

<div class="container">

	<h1><?=lang::get('account-login')?></h1>

	<div class="form-login">
		<form method="post">

			<input id="anticsrf" name="anticsrf" value="<?= csrf() ?>" type="hidden">

			<div class="form-field is-full">
				<label><?=lang::get('account-email') ?>:</label>
				<input name="username" type="text" autofocus="autofocus" value="<?= $username ?>">
			</div>

			<div class="form-field is-full">
				<label><?=lang::get('account-password') ?>:</label>
				<input name="password" type="password" autocomplete="off">
			</div>

			<?php if($login_fedback != ""): ?>
				<div id="form_feedback" class="is-visible red">
					<?php
						switch ($login_fedback) {
							case "incomplete":
								echo lang::get('validation-all-fields-required');
							break;
							case "error":
								echo lang::get('account-login-ko');
							break;
							case "banned":
								echo lang::get('account-disabled');
							break;
						}
					?>
				</div>
			<?php endif ?>

			<div class="form-field">
				<a href="/password-reset/"><small><?=lang::get('account-forgot-password') ?></small></a>
			</div>

			<div class="t-center">
				<?=brick::btn("type:submit", "name:login", "value:login", "text:" . lang::get('account-login'), "formnovalidate") ?>
			</div>

		</form>
	</div>

	<div class="t-center">
		<?=lang::get('account-yet') ?> <a class="" href="<?=url::to('registrazione/') ?>"><?=lang::get('sign-up') ?></a>
	</div>

</div>

<?php
require __DIR__ . '/../inc/footer.php';
?>

