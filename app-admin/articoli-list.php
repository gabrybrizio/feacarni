<?php 
	require 'inc/menu.php';
	s::set('modulo', "articoli");

	if(!user::isAdmin()) util::jumpTo('/');
?>

<h1>Articoli</h1>

<?php require 'inc/list-search.php'; ?>

<style>
	.visibile{background-color:#62a420}
	.non-visibile{background-color:#c92100}
</style>

<div id="list" class="box">

	<?php
	
	// CARICO LA TABELLA
	$elements = db::table('articoli')->fetch('article')->order('nome ASC')->all();

	// Se ci sono elementi
	if ($elements->count() > 0):		
	
		foreach ($elements as $el):

			//a::show($el);
			
			$data = module::dataOf($el);
			$element__cover = module::imagesOf("articoli", $el->GUID(), a::data('carne')); 			
		?>
		
			<a class="card" href="<?= s::get('modulo') ?>-detail.php?e=<?= $el->GUID() ?>">

				<card-img style="background-image:url('<?= thumb::src($element__cover, 500); ?>')">
					<?php
						if($el->isVisible()):
					?>
						<card-tag class="visibile">Visibile</card-tag>
					<?php
						else:
					?>
						<card-tag class="non-visibile">Non visibile</card-tag>
					<?php
						endif;
					?>	
					<card-tag><?= $el->price() ?> € / <?= $el->variant->weight()?></card-tag>			
				</card-img>
				
				<card-hat class="important"><?= str::upper(a::data('categoria')) ?></card-hat>
				<card-title>
					<?=a::data('nome') ?>
				</card-title>
				
				<card-footer>
					<?=  brick::btn("text:ELIMINA", "class:ghost small", "click:FOLDER.deleteConfirm(event,'articoli','" . $el->GUID() . "','questo articolo')"); ?>
				</card-footer>
			</a>  		
		
		<?php 
	
		endforeach;
	else:
		echo '<h3>Nessun articolo presente.</h3>';
	endif;		
	
	?>

</div>

<controls>
	<?php
		echo  brick::btn("icon:plus", "text:Nuovo articolo", "click:location.href='" . s::get('modulo') . "-detail.php?e=" . util::GUID() . "'");
		echo  brick::btn("icon:chevron-up", "class:ghost", "click:SCROLL.toTop()"); 
	?>	
</controls>

<?php require 'inc/footer.php'; ?>
