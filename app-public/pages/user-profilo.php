<?php
    require __DIR__ . '/../../app/init.php';

	if(!user::isLogged()){
		util::jumpTo('/');
		die();
	}

	$utente_data = module::dataOf("utenti", s::get('public_id'));

	page::title(lang::get('account-profile'));

	require __DIR__ . '/../inc/header.php';	
?>

<h1>
	<?=lang::get('account-profile') ?>
</h1>

<div class="container">		

	<p>
		<?=lang::get('account-data-owner') . " " . site::get('ragione-sociale'); ?>
	</p>

	<div class="dati-utente">
		<div class="form-field">
			<b><?=lang::get('account-name') ?>:</b>
			<?= a::v($utente_data, 'cognome') ?> <?= a::v($utente_data, 'nome') ?>
		</div>

		<div class="form-field">
			<b><?=lang::get('account-email') ?>:</b>
			<?= a::v($utente_data, 'username') ?>
		</div>

		<?php
		  if(a::v($utente_data, 'tipologia') != 'Privato'):
		?>
			<div class="form-field">
				<b><?=lang::get('account-company') ?>:</b>
				<?= a::v($utente_data, 'azienda') ?>
			</div>

			<div class="form-field">
				<b><?=lang::get('account-vat-number') ?>*:</b>
				<?= a::v($utente_data, 'partita-iva') ?>
			</div>
			
			<div class="form-field">
				<b>PEC:</b>
				<?= a::v($utente_data, 'pec') ?>
			</div>
			
			<div class="form-field">
				<b>Codice SDI:</b>
				<?= a::v($utente_data, 'codice-sdi') ?>
			</div>
		<?php
		  endif;
		?>
	</div>	

	<p>
		<?=lang::get('account-edit-data') . " " .site::get('ragione-sociale'); ?>.<br>
		<b><?=lang::get('validation-fields-required') ?></b>
	</p>

	<div class="form-wrapper">

		<form id="profile_form" action="javascript:void(0);" method="post">

			<h2><?=lang::get('account-credentials') ?></h2>

			<div class="form-field">
				<label><?=lang::get('account-choose-password') ?>:</label>
				<input name="password" type="password" autocomplete="off" placeholder="Minimo 6 caratteri">
			</div>

			<div class="form-field">
				<label><?=lang::get('account-repeat-password') ?>:</label>
				<input name="password_copy" type="password" autocomplete="off">
			</div>			
			
			<h2><?=lang::get('account-billing') ?></h2>			

			<div class="form-field">
				<label><?=lang::get('account-state') ?>*:</label>
				<?=brick::select('fatturazione_paese', 'fatturazione-paese', util::countries(), a::v($utente_data, 'fatturazione-paese')) ?>
			</div>
			
			<div class="form-field">
				<label><?=lang::get('account-address') ?>*:</label>
				<input type="text" id="fatturazione_indirizzo" name="fatturazione-indirizzo" placeholder="Es. Via Roma, 100" value="<?= a::v($utente_data, 'fatturazione-indirizzo')?>"/>
			</div>

			<div class="form-field">
				<label><?=lang::get('account-city') ?>*:</label>
				<input type="text" id="fatturazione_citta" name="fatturazione-citta" value="<?= a::v($utente_data, 'fatturazione-citta')?>"/>
			</div>
			
			<div class="form-field is-half">
				<label><?=lang::get('account-province') ?>:</label>
				<input type="text" id="fatturazione_provincia" name="fatturazione-provincia" value="<?= a::v($utente_data, 'fatturazione-provincia')?>" placeholder="Es. RM"/>
			</div>
			
			<div class="form-field is-half">
				<label><?=lang::get('account-postal-code') ?>*:</label>
				<input type="text" id="fatturazione_cap" name="fatturazione-cap"  value="<?= a::v($utente_data, 'fatturazione-cap')?>"/>
			</div>
			
			<div class="form-field">
				<label><?=lang::get('account-tel') ?>*:</label>
				<input type="text" name="telefono"  value="<?= a::v($utente_data, 'telefono')?>"/>
			</div>			
			
			<h2><?=lang::get('account-shipping') ?></h2>

			<div class="form-field is-full">
				<a onclick="REGISTRATION.copy()" class="btn"><?=lang::get('account-address-same-as-billing') ?></a>
			</div>

			<div class="form-field">
				<label><?=lang::get('account-state') ?>*:</label>
				<?=brick::select('spedizione_paese', 'spedizione-paese', util::countries(), a::v($utente_data, 'spedizione-paese')) ?>
			</div>
			
			<div class="form-field">
				<label><?=lang::get('account-address') ?>*:</label>
				<input type="text" id="spedizione_indirizzo" name="spedizione-indirizzo" placeholder="Es. Via Roma, 100" value="<?= a::v($utente_data, 'spedizione-indirizzo')?>"/>
			</div>

			<div class="form-field">
				<label><?=lang::get('account-city') ?>*:</label>
				<input type="text" id="spedizione_citta" name="spedizione-citta" value="<?= a::v($utente_data, 'spedizione-citta')?>"/>
			</div>
			
			<div class="form-field is-half">
				<label><?=lang::get('account-province') ?>:</label>
				<input type="text" id="spedizione_provincia" name="spedizione-provincia" value="<?= a::v($utente_data, 'spedizione-provincia')?>" placeholder="Es. RM"/>
			</div>
			
			<div class="form-field is-half">
				<label><?=lang::get('account-postal-code') ?>*:</label>
				<input type="text" id="spedizione_cap" name="spedizione-cap" value="<?= a::v($utente_data, 'spedizione-cap')?>"/>
			</div>
			
			<h2><?=lang::get('privacy') ?></h2>

			<input type="hidden" name="anticsrf" value="<?= csrf() ?>"> 
			<input type="text" name="contact_me" style="display:none !important" tabindex="-1" autocomplete="off"> 

			<div class="form-field is-full is-checkbox">
				<?php
					$checked = (a::v($utente_data, 'marketing') == "1" ? " checked" : "");
				?>
				<input type="checkbox" id="marketing" name="marketing" value="1" <?= $checked ?>>
				<label for="marketing"><?=lang::get('privacy-advertising-agree') ?> <?=site::get('ragione-sociale') ?></label>
			</div>					

			<div id="form_feedback"></div>
			
			<div class="form-actions">
				<a id="btn_send" class="btn" onclick="PROFILE.update()"><?=lang::get('update') ?></a>
			</div>
		</form>				

	</div>
</div>

<?php 
require __DIR__ . '/../inc/footer.php';
?>