<?php
    require __DIR__ . '/../../app/init.php';

	if(!user::isLogged()){
		util::jumpTo('/');
		die();
	}

	page::title(lang::get('shop-orders'));

	require __DIR__ . '/../inc/header.php';

	$ordini = module::listOf('ordini')->where("utente", "=", s::get('public_id'))->andWhere("stato", "<>", OrderStatus::Carrello)->order('data_ordine DESC')->all();

?>

<div class="container">

	<h1>
		<?=lang::get('shop-orders') ?>
	</h1>

	<?php if($ordini->count() == 0): ?>
        <h1><?=lang::get('no-result') ?></h1>
	<?php else: ?>
		<table>
			<thead>
				<tr>
					<th class="t-left"><?=lang::get('shop-order') ?></th>
					<th class="t-left"><?=lang::get('date') ?></th>
					<th class="t-right"><?=lang::get('shop-total-price') ?></th>
					<th class="t-center"><?=lang::get('status') ?></th>
				</tr>
			</thead>
			<tbody>

			<?php
				foreach($ordini as $ordine):

				$ordine_data = module::dataOf($ordine);
			?>
				<tr>
					<td>
						<?= str::upper(str::short($ordine->GUID(), 8, "")); ?>
					</td>
					<td>
						<?= a::get($ordine_data, "dataOrdine"); ?>
					</td>
					<td class="t-right">
						<?= util::euro(a::get($ordine_data, "total")); ?>
					</td>
					<td class="t-center">
						<?= lang::get('order-status-' . str::slug(a::get($ordine_data, "stato"))) ?>
					</td>
				</tr>
			<?php
				endforeach;
			?>

			</tbody>
		</table>

	<?php
		endif;
	?>

</div>

<?php
require __DIR__ . '/../inc/footer.php';
?>