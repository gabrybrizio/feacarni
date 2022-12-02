<?php 
	require 'inc/menu.php';
	s::set('modulo', "puntivendita");

	if(!user::isAdmin()) util::jumpTo('/');
?>

<h1>Punti vendita</h1>

<?php require 'inc/list-search.php'; ?>

<div id="list" class="box">

	<?php
	
	// CARICO LA TABELLA
	$elements = module::listOf(s::get('modulo'))->order('denominazione ASC')->all();

	// Se ci sono elementi
	if ($elements->count() > 0):		
	
		foreach ($elements as $el):
			
			$data = module::dataOf($el);			
		?>
		
			<a class="card" href="<?= s::get('modulo') ?>-detail.php?e=<?= $el->GUID() ?>">
				
				<card-hat class="important"><?= str::upper(a::data('citta')) ?></card-hat>
				<card-title><?=a::data('denominazione') ?></card-title>
				
				<card-footer>
					<?=  brick::btn("text:ELIMINA", "class:ghost small", "click:FOLDER.deleteConfirm(event,'puntivendita','" . $el->GUID() . "','questo punto vendita')"); ?>
				</card-footer>
			</a>  		
		
		<?php 
	
		endforeach;
	else:
		echo '<h3>Nessun punto vendita presente.</h3>';
	endif;		
	
	?>

</div>

<controls>
	<?php
		echo  brick::btn("icon:plus", "text:Nuovo punto vendita", "click:location.href='" . s::get('modulo') . "-detail.php?e=" . util::GUID() . "'");
		echo  brick::btn("icon:chevron-up", "class:ghost", "click:SCROLL.toTop()"); 
	?>	
</controls>

<?php require 'inc/footer.php'; ?>
