<?php 
	require __DIR__ . '/../app/init.php';

	s::set('modulo', "articoli");
	
	if(s::get('logged') && r::ajax() && r::is('post')):

?>
<h2>Scegli l'articolo</h2>

<?php require 'inc/list-search.php'; ?>

<div class="box" >
	<?php
	
	// CARICO LA TABELLA
	$elements = module::listOf(s::get('modulo'))->order('nome ASC')->all();

	// Se ci sono elementi
	if ($elements->count() > 0):		
	
		foreach ($elements as $el):
		
			$data = module::dataOf($el);
			$element__cover = module::imagesOf("articoli", $el->GUID(),a::data('cover'));
			
		?>
			<a class="card" onclick="RELATION.set('<?= $el->GUID() ?>','<?= addslashes(a::data('nome')) ?>')">
				<card-img style="background-image:url('<?= thumb::src($element__cover, 500); ?>')"></card-img>
				
				<card-hat class="important"><?=a::data('prezzo-privato') ?> €</card-hat>
				<card-title><?=a::data('nome') ?></card-title>
			</a>  		
		<?php 
	
		endforeach;
	else:
		echo '<h3>Nessun articolo presente.</h3>';
	endif;		
	
	?>
</div>
<?php endif; ?>