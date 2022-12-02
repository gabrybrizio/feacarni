        </main>		

		<!-- MODAL -->
		<div id="modal_container">
			<div id="modal_window">
				<div id="modal_header"></div>
				<div id="modal_content"></div>
				<div id="modal_footer"></div>						
			</div>
		</div>		
	
		<script>
			// FOCUS MENU
			if(document.getElementById("m_<?= s::get('modulo') ?>")){
				document.getElementById("m_<?= s::get('modulo') ?>").classList.add("focus");		
			}
		</script>

		<script type="text/javascript" src="/js/icons.php?<?=config::version()?>"></script>
		<script src="<?= util::adminUrl() ?>assets/js/fn.min.js?<?=config::version() ?>"></script>

		<script>
			UTIL.init({
				csrf: '<?= csrf() ?>'
			});
		</script>
	</body>
</html>