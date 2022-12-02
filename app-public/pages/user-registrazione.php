<?php
    require __DIR__ . '/../../app/init.php';

	if(user::isLogged()){
		util::jumpTo('/');
		die();
	}

	page::title(lang::get('sign-up'));

	require __DIR__ . '/../inc/header.php';
?>

<h1>
	<?=lang::get('sign-up') ?>
</h1>

<div class="container">

	<p>
		<b><?=lang::get('validation-fields-required') ?></b>
	</p>

	<div class="form-registrazione-tipo">
		<h2><?=lang::get('account-type') ?></h2>
		<a onclick="REGISTRATION.choice(this,'Privato')" class="is-focus"><?=lang::get('account-type-personal') ?></a>
		<a onclick="REGISTRATION.choice(this,'Azienda')"><?=lang::get('account-type-business') ?></a>
	</div>

	<div class="form-wrapper">

		<form id="registration_form" action="javascript:void(0);" method="post">

			<input type="hidden" id="tipologia" name="tipologia" value="Privato"/>

			<h2><?=lang::get('account-credentials') ?></h2>

			<div class="form-field">
				<label><?=lang::get('account-email') ?>*:</label>
				<input type="text" name="username"/>
			</div>
			
			<div class="form-break"></div>

			<div class="form-field">
				<label><?=lang::get('account-choose-password') ?>*:</label>
				<input name="password" type="password" autocomplete="off" placeholder="<?=lang::get('account-password-hint') ?>">
			</div>

			<div class="form-field">
				<label><?=lang::get('account-repeat-password') ?>*:</label>
				<input name="password_copy" type="password" autocomplete="off">
			</div>			
			
			<h2><?=lang::get('account-billing') ?></h2>

			<div class="form-field">
				<label><?=lang::get('account-name') ?>*:</label>
				<input type="text" name="nome"/>
			</div>	

			<div class="form-field">
				<label><?=lang::get('account-surname') ?>*:</label>
				<input type="text" name="cognome"/>
			</div>
			
			<div class="form-field field-azienda is-hidden">
				<label><?=lang::get('account-company') ?>*:</label>
				<input type="text" name="azienda"/>
			</div>
			
			<div class="form-field field-azienda is-hidden">
				<label><?=lang::get('account-vat-number') ?>*:</label>
				<input type="text" name="partita-iva"/>
			</div>
			
			<div class="form-field field-azienda is-hidden">
				<label>PEC*:</label>
				<input type="text" name="pec"/>
			</div>	
			
			<div class="form-field field-azienda is-hidden">
				<label>Codice SDI*:</label>
				<input type="text" name="codice-sdi"/>
			</div>			

			<div class="form-field">
				<label><?=lang::get('account-state') ?>*:</label>
				<?=brick::select('fatturazione_paese', 'fatturazione-paese', util::countries(), '', true) ?>
			</div>
			
			<div class="form-field">
				<label><?=lang::get('account-address') ?>*:</label>
				<input type="text" id="fatturazione_indirizzo" name="fatturazione-indirizzo" placeholder="<?=lang::get('account-address-example') ?>"/>
			</div>

			<div class="form-field">
				<label><?=lang::get('account-city') ?>*:</label>
				<input type="text" id="fatturazione_citta" name="fatturazione-citta"/>
			</div>
			
			<div class="form-field is-half">
				<label><?=lang::get('account-province') ?>*:</label>
				<input id="fatturazione_provincia" name="fatturazione-provincia" placeholder="Es. RM"/>
			</div>
			
			<div class="form-field is-half">
				<label><?=lang::get('account-postal-code') ?>*:</label>
				<input type="text" id="fatturazione_cap" name="fatturazione-cap"/>
			</div>
			
			<div class="form-field">
				<label><?=lang::get('account-tel') ?>*:</label>
				<input type="text" name="telefono"/>
			</div>			
			
			<h2><?=lang::get('account-shipping') ?></h2>

			<div class="form-field is-full">
				<a onclick="REGISTRATION.copy()" class="btn"><?=lang::get('account-address-same-as-billing') ?></a>
			</div>

			<div class="form-field">
				<label><?=lang::get('account-state') ?>*:</label>
				<?=brick::select('spedizione_paese', 'spedizione-paese', util::countries(), '', true) ?>
			</div>
			
			<div class="form-field">
				<label><?=lang::get('account-address') ?>*:</label>
				<input type="text" id="spedizione_indirizzo" name="spedizione-indirizzo" placeholder="Es. Via Roma, 100"/>
			</div>

			<div class="form-field">
				<label><?=lang::get('account-city') ?>*:</label>
				<input type="text" id="spedizione_citta" name="spedizione-citta"/>
			</div>
			
			<div class="form-field is-half">
				<label><?=lang::get('account-province') ?>*:</label>
				<input id="spedizione_provincia" name="spedizione-provincia" placeholder="Es. RM"/>
			</div>
			
			<div class="form-field is-half">
				<label><?=lang::get('account-postal-code') ?>*:</label>
				<input type="text" id="spedizione_cap" name="spedizione-cap"/>
			</div>
			
			<h2><?=lang::get('privacy') ?></h2>

			<input type="hidden" name="anticsrf" value="<?= csrf() ?>"> 
			<input type="text" name="contact_me" style="display:none !important" tabindex="-1" autocomplete="off"> 

			<div class="form-field is-full is-checkbox">
				<input type="checkbox" id="privacy" name="privacy" value="accetto">
				<label for="privacy"><?=lang::get('privacy-policy-agree') ?> <a href="" class=""><?=lang::get('privacy-policy') ?></a></label>
			</div>

			<div class="form-field is-full is-checkbox">
				<input type="checkbox" id="marketing" name="marketing" value="1">
				<label for="marketing"><?=lang::get('privacy-advertising-agree') ?> <?=site::get('ragione-sociale') ?></label>
			</div>

			<div id="form_feedback"></div>
			
			<div class="form-actions">
				<a id="btn_send" class="btn" onclick="REGISTRATION.send()"><?=lang::get('sign-up') ?></a>
			</div>
		</form>				

	</div>
</div>

<?php 
require __DIR__ . '/../inc/footer.php';
?>