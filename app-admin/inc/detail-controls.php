<?php
	brick::field("hidden", "name:anticsrf", "value:" . csrf());
	brick::field("hidden", "name:modified", "value:" . date("d/m/Y H:i:s"));
	brick::field("hidden", "name:by", "value:" . s::get('id'));
	brick::field("hidden", "name:scroll_position", "value:0");
?>

<controls>
	<?php
		echo brick::btn("icon:save", "text:Salva", "type:submit", "class:important", "id:btn_save", "name:btn_save", "value:save", "formnovalidate");
		if(get('s') != "1"){
			echo brick::btn("icon:grid", "text:Elenco", "click:location.href='" . s::get('modulo') . "-list.php'");
		}
		echo brick::btn("icon:chevron-up", "class:ghost", "click:SCROLL.toTop()");
	?>
</controls>