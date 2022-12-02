<div id="search" class="box">
	<field class="full">
		<label>Cerca:</label>
		<?php
		  if (isset($searchSubmit)):
		?>
			<form method="post" enctype="multipart/form-data">
				<input id="searchField" type="text" name="search" value="<?= get("search") ?>"/>
				<?= brick::btn("icon:x", "click:location.href='" . s::get('modulo') . "-list.php'", "class:reset"); ?>
				<?= brick::btn("icon:search", "type:submit", "class:side"); ?>
			</form>
		<?php
		  else:
		?>
			<input id="searchField" type="text" value="" onKeyDown="if(event.keyCode==13) SEARCH.filter()"/>
			<?= brick::btn("icon:x", "click:SEARCH.reset()", "class:reset"); ?>
			<?= brick::btn("icon:search", "click:SEARCH.filter()", "class:side"); ?>
		<?php
		  endif;
		?>
	</field>
</div>