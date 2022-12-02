<?php

	require __DIR__ . '/../../app/init.php';

	if(!s::get('logged')) go('/');
	if(config::maintenance()) go("/maintenance/");
?>
<!DOCTYPE HTML>
<html lang="it">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <title><?= site::get('ragione-sociale');?></title>

	<?php include 'inc/assets.php' ?>

</head>
<body onclick="CHOICE.hide()">

	<sidebar>
		<header>
			<div id="logo">
				<a onclick="MENU.toggle()"><?= icon::get("menu")?></a>
				<div id="brand"><?= site::get('ragione-sociale'); ?></div>
			</div>
			<div id="mobile-actions">
				<?php if(get("e") == ""): ?>
					<a onclick="SEARCH.display()"><?= icon::get("search")?></a>
				<?php endif; ?>
				<?php if(!user::isAdmin()): ?>
					<a href="operatori-detail.php?s=1&e=<?= s::get("id") ?>" style="margin-left:7px"><?= icon::get("user")?></a>
				<?php endif; ?>
			</div>
		</header>

			<?php
				if(user::isAdmin()):
			?>
				<profile>
			<?php
				else:
			?>
				<profile style="cursor:pointer" onclick="location.href='operatori-detail.php?e=<?= s::get('id')?>'">
			<?php
				endif;
			?>
			<profile-icon>
				<?= icon::get('user'); ?>
			</profile-icon>
			<profile-text>
				<?= s::get('name') ?><br/>
				<span class="important"><?= str::upper(s::get('role')) ?></span>
			</profile-text>
		</profile>
		<nav>
			<?php if(user::isAdmin()): ?>
				<a id="m_azienda" href="azienda-detail.php?s=1&e=f9f02f03-1e5a-43d6-bd78-dda63b7ba691">
					<?= icon::get("home")?>Azienda
				</a>

				<a id="m_operatori" href="operatori-list.php"><?= icon::get('users'); ?>
					<span>Operatori</span>
					<menu-count><?= db::table('operatori')->count(); ?></menu-count>
				</a>

				<a id="m_articoli" href="articoli-list.php"><?= icon::get('package'); ?>
					<span>Articoli</span>
					<menu-count><?= db::table('articoli')->count(); ?></menu-count>
				</a>

				<a id="m_ordini" href="ordini-list.php"><?= icon::get('thumbs-up'); ?>
					<span>Ordini</span>
					<menu-count><?= db::table('ordini')->where('stato', '<>', OrderStatus::Carrello)->count(); ?></menu-count>
				</a>				

				<a id="m_puntivendita" href="puntivendita-list.php"><?= icon::get('compass'); ?>
					<span>Punti vendita</span>
					<menu-count><?= db::table('puntivendita')->count(); ?></menu-count>
				</a>				

			<?php endif; ?>

			<a href="logout.php"><?= icon::get("log-out")?>Esci</a>
			</nav>
	</sidebar>

	<main>