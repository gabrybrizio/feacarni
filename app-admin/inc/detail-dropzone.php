<div id="dropzone">
	<div id="dropzone_hover">
		<?= icon::get("upload-cloud"); ?>
		<h2 class="white">Rilascia qui i files che vuoi uploadare.</h2>
		<p>(Massimo 4 Mb)</p>
	</div>
	<div id="dropzone_uploading" class="hidden">
		<?= icon::get("clock"); ?>
		<h2 class="white">Caricamento in corso...</h2>
	</div> 	
</div>

<script src="<?= util::adminUrl() ?>assets/js/dropzone.min.js"></script>