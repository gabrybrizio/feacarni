<?php 

	require 'inc/menu.php';
	s::set('modulo', "operatori");

	if(!user::isAdmin()){
		util::jumpTo('/');	
		die();
	}

?>

<h1>Operatori</h1>

<?php require 'inc/list-search.php'; ?>

<div id="list" class="box">

	<?php
	
	// CARICO LA TABELLA
	$elements = module::listOf(s::get('modulo'))->order('cognome ASC, nome ASC')->all();

	// Se ci sono elementi
	if ($elements->count() > 0):		
	
		foreach ($elements as $el):
			
			$data = module::dataOf($el);
		?>
		
			<a class="card" href="<?= s::get('modulo') ?>-detail.php?e=<?= $el->GUID() ?>">
				<card-tag><?=a::data('ruolo') ?></card-tag>
				<card-hat class="important"><?=a::data('username') ?></card-hat>
				<card-title><?=a::data('cognome') . " " .a::data('nome') ?></card-title>
				<card-footer>
					<?=  brick::btn("text:ELIMINA", "class:ghost small", "click:FOLDER.deleteConfirm(event,'operatori','" . $el->GUID() . "','questo operatore')"); ?>
				</card-footer>
			</a>  		
		
		<?php 
	
		endforeach;
	else:
		echo '<h3>Nessun operatore presente.</h3>';
	endif;		
	
	?>

</div>

<controls>
	<?php
		echo  brick::btn("icon:plus", "text:Nuovo operatore", "click:location.href='" . s::get('modulo') . "-detail.php?e=" . util::GUID() . "'");
		echo  brick::btn("icon:chevron-up", "class:ghost", "click:SCROLL.toTop()"); 
	?>	
</controls>

<?php require 'inc/footer.php'; ?>
