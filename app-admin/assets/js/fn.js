axios.defaults.headers.post['X-Requested-With'] = 'XMLHttpRequest';

var UTIL = (function () {

	var publicAPIs = {};
	var settings = {};

	publicAPIs.init = function(options) {
		let defaults = {
			csrf: ''
		};
		settings = Object.assign({}, defaults, options);
	}

	publicAPIs.rnd = function(min, max){
		min = Math.ceil(min);
		max = Math.floor(max);
		return Math.floor(Math.random() * (max - min + 1)) + min;
	}

	publicAPIs.csrf = function() {
		return settings.csrf
	}

	return publicAPIs;

})();


var STRING = (function () {

	var publicAPIs = {};

	// Trimma la stringa e elimina spazi multipli, a capo, tab,,,
	publicAPIs.clean = function(str){
		str = str.trim();
		str = str.replace(/\s\s+/g, ' ');
		str = str.replace(/\r?\n|\r/g, ' ');

		return str;
	}

	// Aggiunge char davanti a una string fino a raggiungere la length
	publicAPIs.pad = function(str, length, char){
		var pad_char = typeof char !== 'undefined' ? char : '0';
		var pad = new Array(1 + length).join(pad_char);
		return (pad + str).slice(-pad.length);
	}

	// Sluggare una strigna
    publicAPIs.slug = function(str){
        str = str.replace(/^\s+|\s+$/g, ''); // trim
        str = str.toLowerCase();

        // remove accents, swap ñ for n, etc
        var from = "àáãäâèéëêìíïîòóöôùúüûñç·/_,:;’'";
        var to   = "aaaaaeeeeiiiioooouuuunc--------";

        for (var i=0, l=from.length ; i<l ; i++) {
            str = str.replace(new RegExp(from.charAt(i), 'g'), to.charAt(i));
        }

        str = str.replace(/[^a-z0-9 -]/g, '') // remove invalid chars
            .replace(/\s+/g, '-') // collapse whitespace and replace by -
            .replace(/-+/g, '-'); // collapse dashes

        return str;
    }

	return publicAPIs;

})();


var ARRAY = (function () {

	var publicAPIs = {};

	// Elimina elementi doppi da un'array
	publicAPIs.unique = function(Arr){
		Arr = Arr.sort();
		i = 0;
		while (i < Arr.length) {
			if (Arr[i] == Arr[i + 1]) {
				Arr.splice(i, 1);
				i--
			}
			i++
		}
		return Arr;
	}

	// Shuffle degli elementi di un'array
	publicAPIs.shuffle = function(Arr){
		for (var j, x, i = Arr.length; i; j = parseInt(Math.random() * i), x = Arr[--i], Arr[i] = Arr[j], Arr[j] = x);
		return Arr;
	}

	// Elimina elementi di un'array tramite valore
	publicAPIs.remove = function(Arr){
		var what, a = arguments,
			L = a.length,
			ax;
		while (L > 1 && Arr.length) {
			what = a[--L];
			while ((ax = Arr.indexOf(what)) !== -1) {
				Arr.splice(ax, 1);
			}
		}
		return Arr;
	}

	return publicAPIs;

})();


var MENU = (function () {

	var publicAPIs = {};

	publicAPIs.toggle = function(){
		document.querySelector("nav").classList.toggle("is-visible");
	}

	return publicAPIs;

})();


var SCROLL = (function () {

	var publicAPIs = {};

	publicAPIs.save = function(){
		var doc = document.documentElement;
		var scroll_position = (window.pageYOffset || doc.scrollTop)  - (doc.clientTop || 0);

		if(document.querySelector('input[name=scroll_position]') !== null){
			document.querySelector("input[name=scroll_position]").value = scroll_position;
		}
	}

	publicAPIs.toTop = function(){
		window.scrollTo({top: 0, behavior: 'smooth'});
	}

	return publicAPIs;

})();


var BUTTON = (function () {

	var publicAPIs = {};

	publicAPIs.add = function(btn){
		document.querySelector('controls button[type=submit]').insertAdjacentHTML('afterend', btn);
	}

	publicAPIs.remove = function(){

	}

	return publicAPIs;

})();


var FOLDER = (function () {

	var publicAPIs = {};

	publicAPIs.deleteConfirm = function(e, modulo, el, label){
		e.preventDefault();

		$content = 'Sicuro di voler eliminare ' + label + '?'
		$action = '<button class="btn red" onclick="FOLDER.delete(\'' + modulo + '\',\'' + el + '\')">' + ICON('trash') + 'Elimina</button>';

		MODAL.open($content, $action);
	}

	publicAPIs.delete = function(modulo, folder){

		var formdata = new FormData();
		formdata.append('_MODULO', modulo);
		formdata.append('_FOLDER', folder);

		axios({
			method: 'POST',
			url: 'ajax/folder-delete.php',
			data: formdata
		})
		.then(function (response) {
			if (response.data.trim() == 'OK') {
				window.location = window.location.href; // Evita il postback
			} else {
				alert(response.data);
			}
		})
		.catch(function (response) {
			alert("Folder delete: " + response.data);
		});

	}

	publicAPIs.clone = function(modulo, folder){

		var formdata = new FormData();
		formdata.append('_MODULO', modulo);
		formdata.append('_FOLDER', folder);

		axios({
			method: 'POST',
			url: 'ajax/folder-clone.php',
			data: formdata
		})
		.then(function (response) {
			if (response.data.trim() == 'OK') {
				window.location = window.location.href; // Evita il postback
			} else {
				alert(response.data);
			}
		})
		.catch(function (response) {
			alert("Folder clone: " + response.data);
		});

	}

	return publicAPIs;

})();


var files_dropped = false;
var FILE = (function () {

	var publicAPIs = {};

	publicAPIs.deleteConfirm = function(e, file){
		e.stopPropagation();

		$content = 'Sicuro di voler eliminare questo file?';
		$action = '<button class="btn red" onclick="FILE.delete(\'' + file + '\')">' + ICON('trash') + 'Elimina</button>';

		MODAL.open($content, $action);
	}

	publicAPIs.delete = function(file){

		var formdata = new FormData();
		formdata.append('_FILE', file);

		axios({
			url: 'ajax/file-delete.php',
			method: 'POST',
			data: formdata
		}).then(function (response) {
			if (response.data.trim() == 'OK') {

				// Elimino il nome del file eliminato da ogni blocco attachment
				document.querySelectorAll("[type=attachments]").forEach(function(attachment_block){

					$file_list = attachment_block.querySelector("input").value;
					$file_list = $file_list.split('|');
					ARRAY.remove($file_list, file)

					attachment_block.querySelector("input").value = $file_list.join("|");
				});

				// Salvo la pagina in modo che tutto sia ricaricato
				// getElementById IE11
				document.getElementById('btn_save').click();

			} else {
				alert(response.data);
			}
		}).catch(function (response) {
			alert("File delete: " + response.data);
		});
	}

	publicAPIs.get = function(_type){

		var type = _type;
		var getFiles = true;

		// Tutti i FILE.get() sono lanciati nel footer

		// Genero le immagini generali solamente se nel dettaglio sono presenti
		if(type == 'images' && !document.getElementById("detail-images")){getFiles = false}

		// Genero i documenti generali solamente se nel dettaglio sono presenti
		if(type == 'documents' && !document.getElementById("detail-documents")){getFiles = false}

		// Genero le immagini per le choices images solamente se nel dettaglio sono presenti
		if(type == 'choice_images' && document.querySelectorAll("field[type=image]").length == 0){getFiles = false}

		// Genero i documenti per le choices documents solamente se nel dettaglio sono presenti
		if(type == 'choice_documents' && document.querySelectorAll("field[type=document]").length == 0){getFiles = false}

		// Genero immagini e documenti per attachments solamente se nel dettaglio sono presenti
		if(type == 'attachments' && document.querySelectorAll("[type$='attachments']").length == 0){getFiles = false}

		if (getFiles) {
			if (type != "attachments") {

				var formdata = new FormData();
				formdata.append('_TYPE', type);

				axios({
					url: 'ajax/file-get.php',
					method: 'POST',
					data: formdata
				}).then(function (response){

					switch(type){
						case "images":
						case "documents":
							// Elenco immagini o documenti generici nel dettaglio
							document.getElementById("detail-" + type).innerHTML = response.data;
							break;
						case "choice_images":
							// Elenco delle choices di tipo immagine
							document.querySelectorAll("field[type=image] choices").forEach(function(choice){
								// Vengono di base caricate tutte le immagini legate a quell'elemento
								choice.innerHTML = response.data;

								// Il file-source serve per legare un field image solamente ai file di un attachment
								// Se c'è un files-source (normalmente legato a un attachments) tolgo quelle che non ne fanno parte
								$file_source = choice.closest("field[type=image]").getAttribute("data-source");

								if ($file_source) {
									file_source_str = document.querySelector('input[name="' + $file_source + '"]').value;
									file_source_array = file_source_str.split("|");

									choice.querySelectorAll("choice").forEach(function (choice_option) {
										$file_name = choice_option.querySelector("choice-value").innerText;
										if (file_source_array.indexOf($file_name) == -1) {
											choice_option.parentNode.removeChild(choice_option);
										}
									})

								}
							});
							break;
						case "choice_documents":
							// Elenco documenti nelle choices di tipo documento
							document.querySelectorAll("field[type=document] choices").forEach(function(choice){
								choice.innerHTML = response.data;
							});
							break;
					}

				}).catch(function (response){
					alert("Files " + type + " get:" + response.data);
				});


			} else if (type == "attachments"){

				// Ciclo tutti i blocchi attachments (e readonly-attachment)
				document.querySelectorAll("[type$='attachments']").forEach(function(attachment_block) {

					// Svuoto il blocco
					attachment_block.querySelector('.attachments-files').innerHTML = "";

					// Recupero il valore del campo files
					var file_list = attachment_block.querySelector("input").value;

					// Recupero la tipologia di attachment (normale o read-only)
					var attachment_type = attachment_block.getAttribute("type");

					if (typeof file_list != 'undefined' && file_list != "") {

						var formdata = new FormData();
						formdata.append('_ATTACHMENTTYPE', attachment_type);
						formdata.append('_FILELIST', file_list);

						axios({
								url:'ajax/file-get-attachments.php',
								method: 'POST',
								data: formdata
							}).then(function (response) {

								// Da valutare nel caso uscissero fuori problemi di //async: false
								// Nella response è presente sia il valore dei file di partenza che l'html da inserire
								//$response = response.split("###");

								// Inserisco immagini o documenti nel attachment_block
								attachment_block.querySelector('.attachments-files').innerHTML = response.data;

							}).catch(function (response) {
								alert("File get attachments: " + response.data);
							});

					} else {
						// Se è un attachments readonly e non ci sono file
						if (attachment_type == 'readonly-attachments') {
							attachment_block.querySelector('.attachments-files').innerHTML = "<i>Nessun file presente</i>";
						}
					}

				});
			}
		}
	}

	publicAPIs.upload = function(el){
		let vaildationFileType = el.getAttribute('data-validation-file-type');

		if (document.querySelector('form').classList.contains('is-uploading')) return false;

		PROGRESS.start();

		document.querySelector('form').classList.add('is-uploading');
		if(document.getElementById("dropzone")){
			dropzone.classList.add('is-visible');
			dropzone_hover.classList.add('hidden');
			dropzone_uploading.classList.remove('hidden');
		}

		var formdata = new FormData();
		formdata.append('anticsrf', UTIL.csrf());

		if(files_dropped) {
			for (var f = 0; f < files_dropped.length; f++) {
				formdata.append('file[]', files_dropped[f]);
			}
		}else{
			// Upload traditional
			var ins = el.files.length;
			for (var i = 0; i < ins; i++) {
				formdata.append("file[]", el.files[i]);
			}
		}

		// Recupero i file già legati eventualmente al field focus
		let $fieldContainer = el.closest('[type=attachments]');
		if ($fieldContainer) {
			let $linkedFiles = $fieldContainer.querySelector('input[type=hidden]');

			if ($linkedFiles) {
				formdata.append("files__linked", $linkedFiles.value);
			}
		}

		axios({
			method: 'POST',
			url: 'ajax/file-upload.php?type=' + vaildationFileType,
			headers: {
				'X-Requested-With': 'XMLHttpRequest',
				'Content-Type': 'multipart/form-data'
			},
			data: formdata
			})
			.then(function (response) {
				el.value = '';
				document.querySelector('form').classList.remove('is-uploading');
				PROGRESS.done();

				if(document.getElementById("dropzone")){
					dropzone.classList.remove('is-visible');
				}

				$r = response.data.split("#");
				$esito = $r[0];
				$content = $r[1];

				if($esito == "OK"){
					// Valorizzo il campo con il nome dei file uploadati
					if(field_focus && field_focus.querySelector('input') !== null){
						field_focus.querySelector('input').value = $content;
					}

					// Salvo la pagina in modo che tutto sia ricaricato
					// getElementById IE11
					document.getElementById('btn_save').click();
				}else{
					dropzone.classList.remove('is-visible');
					MODAL.open($content);
				}
			})
			.catch(function (response) {
				el.value = '';
				PROGRESS.done();

				if(document.getElementById('dropzone')){
					dropzone.classList.remove('is-visible');
				}

				response = "Si è verificato un errore. E' possibile che alcuni file pesino più di 4 Mb oppure riprovare più tardi.";
				MODAL.open(response);
			});
	}

	return publicAPIs;

})();

var progressing = null;
var PROGRESS = (function () {

	var publicAPIs = {};

	publicAPIs.start = function(){

		document.body.insertAdjacentHTML('beforeend', '<div id="progress"></div>');

		progress_width = 1;
		progressing = setInterval(function(){
			progress_width += UTIL.rnd(1,10);
			if(progress_width > 99){
				clearInterval(progressing);
				progress.style.width = '99%';
			}else{
				progress.style.width = progress_width + '%';
			}
		}, 500);

	}

	publicAPIs.done = function(){
		clearInterval(progressing);
		if(document.getElementById("progress")){
			document.body.removeChild(progress);
		}
	}

	return publicAPIs;

})();


var MODAL = (function () {

	var publicAPIs = {};

	publicAPIs.open = function(content,actions,type){
		actions = actions || "";
		type = type || "";

		switch (type) {
			case "success":
					modal_header.classList.add("success");
					modal_header.innerHTML = ICON('check');
				break;
			case "error":
					modal_header.classList.add("error");
					modal_header.innerHTML = ICON('x');
				break;
			case "relation":
					modal_window.classList.add("relation");
				break;
			case "calendar":
					modal_window.classList.add("calendar");
				break;
		}

		modal_content.innerHTML = content;

		var $close__class = actions == "" ? "" : "ghost";

		var $close = '<button onclick="MODAL.close()" class="btn ' + $close__class + '">' + ICON('x') + 'Chiudi</button>'
		modal_footer.innerHTML = actions + $close;

		modal_container.classList.add("is-visible");
		document.body.style.overFlow = "hidden";
	}

	publicAPIs.close = function(){
        modal_container.className = '';
        modal_window.className = '';
		modal_header.className = '';
		modal_header.innerHTML = '';
        modal_content.innerHTML = '';
		modal_footer.innerHTML = '';
		document.body.style.overFlow = "auto";
	}

	return publicAPIs;

})();


var SEARCH = (function () {

	var publicAPIs = {};

	publicAPIs.display = function(){
		document.querySelector("nav").classList.remove("is-visible");
		search.classList.add("is-visible");
	}

	publicAPIs.reset = function(){
		searchField.value = "";
		SEARCH.filter();
	}

	publicAPIs.filter = function(){

		if(document.querySelector('#list h3') !== null){
			$noresults = document.querySelector('#list h3');
			$noresults.parentNode.removeChild($noresults);
		}

		document.querySelectorAll(".card").forEach(function (card){
			card.classList.remove("hidden");
		});

		// Ricavo e pulisco la string da ricerca
		// Elimino spazi multipli, a capo, tab,...
		var needle = searchField.value.toLowerCase();
		needle = STRING.clean(needle);

		if (!needle == ''){

			// Ricavo ogni singola parola
			var needles = needle.split(" ");

			document.querySelectorAll(".card").forEach(function(card){

				var founded = [];

				if(card.querySelector("card-tag") !== null){
					// Possono esserci più card-tag
					card_tag_array = [];
					card.querySelectorAll("card-tag").forEach(function(tag){
						card_tag_array.push(STRING.clean(tag.innerText.toLowerCase()));
					});
					card_tag = card_tag_array.join();
				}else{
					card_tag = "";
				}

				if(card.querySelector("card-hat") !== null){
					// Possono esserci più card-hat
					let card_hat_array = [];
					card.querySelectorAll("card-hat").forEach(function(hat){
						card_hat_array.push(STRING.clean(hat.innerText.toLowerCase()));
					});
					card_hat = card_hat_array.join();
				}else{
					card_hat = "";
				}

				if(card.querySelector("card-title") !== null){
					card_title = STRING.clean(card.querySelector("card-title").innerText.toLowerCase());
				}else{
					card_title = "";
				}

				// Ricerco ogni singola parola nei tag della card
				needles.forEach(function(n,i){
					if(card_tag.indexOf(n) > -1) founded.push(n);
					if(card_hat.indexOf(n) > -1) founded.push(n);
					if(card_title.indexOf(n) > -1) founded.push(n);
				});

				founded = ARRAY.unique(founded);
				if(founded.length < needles.length) card.classList.add("hidden")

			});
		}

		if(document.querySelectorAll(".card").length == document.querySelectorAll("a.card.hidden").length){
			list.insertAdjacentHTML('beforeend', '<h3>Nessun risultato.</h3>');
		}
	}

return publicAPIs;

})();


var field_focus = null;
var FIELD = (function () {

	var field = {};

	field.init = function() {
		// Sostituisce nei campi euro il punto con la virgola.
		let $euroFields = document.querySelectorAll('field[type=euro] input');

		$euroFields.forEach(el => {
			el.addEventListener('change', function(e) {
				el.value = el.value.replace(/\./g, ',');
			})
		});
	}

	field.focus = function(el){
		field_focus = el.closest("[type=attachments]");
	}

	return field;

})();


var INPUT = (function () {

	var publicAPIs = {};

	publicAPIs.reset = function(el){
		el.closest("field").querySelectorAll("input").forEach(function (input) {
			input.value = "";
		});
	}

	return publicAPIs;

})();


var PASSWORD = (function () {

	var publicAPIs = {};

	publicAPIs.switch = function(el){
		var rel_input = el.closest("field[type=password]").querySelector("input");

		if(rel_input.getAttribute("type") === "password"){
			rel_input.setAttribute("type","text");
			el.querySelector("svg").innerHTML = ICON('eye-off');
		}else{
			rel_input.setAttribute("type","password");
			el.querySelector("svg").innerHTML = ICON('eye');
		}
	}

	return publicAPIs;

})();


var CHECK = (function () {

	var publicAPIs = {};

	publicAPIs.toggle = function(el){
		let currentValue = el.querySelector("input").value;
		let newValue = (currentValue == '0' ? '1' : '0');
        let svg = el.querySelector('svg');

		let template = document.createElement('template');
		template.innerHTML = ICON((newValue === '1' ? 'check-square' : 'square'));
		let newSvg = template.content.childNodes[0];

		el.replaceChild(newSvg, svg);
		el.querySelector('input').value = newValue;
	}

	return publicAPIs;

})();


var choice_focus = null;
var CHOICE = (function () {

	var publicAPIs = {};

	publicAPIs.toggle = function(ev){

		// Passo l'evento e non l'elemento per non var propagare il click <body>
		ev = ev || window.event;
		ev.preventDefault();
		ev.stopPropagation();

		// Mi ricavo l'elemnto del click
		var _this = ev.target || ev.srcElement;

		choice_focus = _this.closest("field").querySelector("choices");

		if (choice_focus.classList.contains("is-visible")) {
			CHOICE.hide();
		}else{
			CHOICE.show();
		}
	}

	publicAPIs.show = function(){
		CHOICE.hide();
		choice_focus.classList.add("is-visible");
	}

	publicAPIs.hide = function(){
		document.querySelectorAll("choices").forEach(function (choice) {
			choice.classList.remove("is-visible");
		});
	}

	publicAPIs.set = function(el){
		var choice = el.querySelector("choice-value");
		let key, val;
		if (choice.hasAttribute('val')) {
			key = choice.getAttribute('val');
			val = choice.innerText.trim();
		} else {
			key = choice.innerText.trim();
			val = key;
		}

		let $field = el.closest("field");
		let $elHidden = $field.querySelector("input[type=hidden]");
		let $elText = $field.querySelector("input[type=text]");
		if ($elHidden) {
			$elHidden.value = key;
		}
		if ($elText) {
			$elText.value = val;
		}
		CHOICE.hide();
	}

	return publicAPIs;

})();


var REPEATER = (function () {

	var publicAPIs = {};

	publicAPIs.clone = function(el){

		// Creo un template duplicando il primo repeater
		var template = el.closest(".box").querySelector("repeater").innerHTML;
		var repeater = document.createElement("repeater");
		repeater.innerHTML = template;

		// Recupero eventuali valori di default
		// Altrimenti svuoto i valori
		repeater.querySelectorAll(".richtext-area").forEach(function(richtext){
			// il nextElementSibling è l'input relativo al richtext
			if(richtext.nextElementSibling.hasAttribute("data-default")){
				richtext_default = richtext.nextElementSibling.getAttribute("data-default")
				richtext.innerHTML = richtext_default;
			}else{
				richtext.innerHTML = "";
			}
		});

		repeater.querySelectorAll("input").forEach(function(input){
			if(input.hasAttribute("data-default")){
				input_default = input.getAttribute("data-default");
				input.value = input_default;
			}else{
				input.value = "";
			}
		});

		repeater.querySelectorAll("check").forEach(function(check){
			if(check.querySelector("input").hasAttribute("data-default") && check.querySelector("input").getAttribute("data-default") == "1"){
				check.querySelector("svg").innerHTML = '<rect x="1" y="1" width="16" height="16" rx="2" ry="2"/><polyline points="5 9 8 12 14 5"/>';
				check.querySelector("input").value = '1';
			}else{
				check.querySelector("svg").innerHTML = '<rect x="1" y="1" width="16" height="16" rx="2" ry="2"/><polyline style="display:none" points="5 9 8 12 14 5"/>';
				check.querySelector("input").value = '0';
			}
		});

		repeater.querySelectorAll(".attachments-files").forEach(function(attachments){
			attachments.innerHTML = "";
		});

		el.closest(".box").querySelector("repeaters").appendChild(repeater);

		REPEATER.index();
	}

	publicAPIs.delete = function(el){
		repeater_parent = el.closest("repeater");
		repeater_parent.parentNode.removeChild(repeater_parent);

		REPEATER.index();
	}

	publicAPIs.index = function(){
		document.querySelectorAll("repeaters").forEach(function(repeaters){
			repeaters.querySelectorAll("repeater").forEach(function(repeater, index){
				repeater.querySelectorAll("field").forEach(function(field){
					var input = field.querySelector("[name]");
					var name = input.getAttribute("name");
					if (typeof name != 'undefined'){
						name = name.replace(/\[[0-9]*\]/g, "[" + index + "]");
						input.setAttribute("name", name);
					}
				});
			});
		});
	}

	return publicAPIs;

})();


var relation_focus = null;
var RELATION = (function () {

	var publicAPIs = {};

	publicAPIs.display = function(el, modulo){

		relation_focus = el;

		axios({
			method: 'POST',
			url: modulo + '-relation.php',
		})
		.then(function (response) {
			var $h = '<relation>' + response.data + '</relation>';
			MODAL.open($h,'','relation');
		})
		.catch(function (response) {
			alert("Relation:" + response.data);
		});
	}

	publicAPIs.set = function(uid,text){
		relation_focus.closest("field").querySelector("input[readonly]").value = text;
		relation_focus.closest("field").querySelector("input[type=hidden]").value = uid;
		MODAL.close();
	}

	return publicAPIs;

})();


var richtext_selection;
var RICHTEXT = (function () {

	var publicAPIs = {};

	publicAPIs.init = function(){

		document.querySelectorAll('field[type=richtext]').forEach(function(richtext){
			richtext_input = richtext.querySelector('.richtext-input').value;
			richtext.querySelector('.richtext-area').innerHTML = richtext_input;
		});

		// Sync richtext-area con richtext-input
		setInterval(function(){
			document.querySelectorAll('field[type=richtext]').forEach(function(richtext){
				var $h = richtext.querySelector('.richtext-area').innerHTML;
				$hCleaned = RICHTEXT.clean($h)
				richtext.querySelector('.richtext-input').value = $hCleaned;
			});
		}, 1000);
	}

	publicAPIs.format = function(cmd, value){
		document.execCommand(cmd, false, value);
	}

	publicAPIs.deformatting = function(){
		document.execCommand('removeformat');
	}

	publicAPIs.saveSelection = function(){
		if(window.getSelection){
			var sel = window.getSelection();
			if (sel.getRangeAt && sel.rangeCount) {
				return sel.getRangeAt(0);
			}
		}else if(document.selection && document.selection.createRange) {
			return document.selection.createRange();
		}

		return null;
	}

	publicAPIs.restoreSelection = function(range){
		if(range){
			if(window.getSelection){
				var sel = window.getSelection();
				sel.removeAllRanges();
				sel.addRange(range);
			}else if(document.selection && range.select) {
				range.select();
			}
		}
	}

	publicAPIs.writelink = function(){
		richtext_selection = RICHTEXT.saveSelection();

		$content = '<field class="full"><label>Inserisci il link:</label><input id="richtext_link" value="https://" type="text"></field>';
		$action = '<button class="btn" onclick="RICHTEXT.insertLink()">' + ICON('link') + 'Inserisci</button>';

		MODAL.open($content, $action);
	}

	publicAPIs.insertLink = function(){
		RICHTEXT.restoreSelection(richtext_selection);
		RICHTEXT.format('createlink',richtext_link.value);
		MODAL.close();
	}

	publicAPIs.empty = function(richtext){
		richtext.closest("field[type=richtext]").querySelector("div.richtext-area").innerHTML = "";
	}

	publicAPIs.clean = function(input){

		// 1. remove line breaks / Mso classes
		var stringStripper = /(\n|\r| class=(")?Mso[a-zA-Z]+(")?)/g;
		var output = input.replace(stringStripper, '');

		// 2. strip Word generated HTML comments
		var commentSripper = new RegExp('<!--(.*?)-->','g');
		output = output.replace(commentSripper, '');

		// 3. remove tags leave content if any
		var tagStripper = new RegExp('<(/)*(meta|link|span|\\?xml:|st1:|o:|font)(.*?)>','gi');
		output = output.replace(tagStripper, '');

		// 4. Remove everything in between and including tags '<style(.)style(.)>'
		var badTags = ['style', 'script','applet','embed','noframes','noscript'];
		for (var i=0; i< badTags.length; i++) {
			tagStripper = new RegExp('<'+badTags[i]+'.*?'+badTags[i]+'(.*?)>', 'gi');
			output = output.replace(tagStripper, '');
		}

		// 5. remove attributes ' style="..."'
		var attributeStripper = / style="[^"]*"/gi;
		output = output.replace(attributeStripper, '');

		// 6. remove event attribute 'onerror=... onload=...' avoiding xss attack
		var eventStripper = / on\w+="[^"]*"/gi;
		output = output.replace(eventStripper, '');

		return output;
	}

	return publicAPIs;

})();

var ICON = (function() {
	let jsonIcons = {};

	let icon = function(iconName) {
		let template = ICONS_CONFIG['_template'];
		let icon = template.replace('{{icon}}', ICONS_CONFIG[iconName]);
		return icon;
	}

	icon.init = function(json) {
		jsonIcons = json;
	};

	return icon;
})();

/**
  * CALENDAR
*/
var Weekdays = ['Lun','Mar','Mer','Gio','Ven','Sab','Dom'];
var Months = ['Gennaio', 'Febbraio', 'Marzo', 'Aprile', 'Maggio', 'Giugno', 'Luglio', 'Agosto', 'Settembre', 'Ottobre', 'Novembre', 'Dicembre'];

var Today = new Date();
var D = Today.getDate();
var M = Today.getMonth();
var Y = Today.getFullYear();

var date_focus;
var Cal = new calendarBase.Calendar({siblingMonths: true, weekStart: 1})

var CALENDAR = (function () {

	var publicAPIs = {};

	publicAPIs.display = function(t){

		date_focus = t;

		var $h = '<calendar-header>';
		$h += '<button onclick="CALENDAR.prevYear()" class="btn only--icon small">' + ICON('chevrons-left') + '</button>';
		$h += '<button onclick="CALENDAR.prevMonth()" class="btn only--icon small">' + ICON('chevron-left') + '</button>';
		$h += '<h5>' + Months[M] + '&#160;' + Y + '</h5>';
		$h += '<button onclick="CALENDAR.nextMonth()" class="btn only--icon small">' + ICON('chevron-right') + '</button>';
		$h += '<button onclick="CALENDAR.nextYear()" class="btn only--icon small">' + ICON('chevrons-right') + '</button>';
		$h += '</calendar-header>';
		$h += '<ul class="calendar">';

		Weekdays.forEach(function(day){
			$h += '<li class="day weekday">' + day + '</li>';
		});

		Cal.getCalendar(Y,M).forEach(function(date){

			var classes = ["day"];

			// Giorni di mesi vicini
			if (date.siblingMonth){classes.push("is-sibling")}

			// Oggi
			if (date.day == Today.getDate() && date.month == Today.getMonth() && date.year == Today.getFullYear()){classes.push("is-today")}

			d = STRING.pad(date.day,2);
			m = STRING.pad((date.month + 1),2);
			y = date.year;

			var ddmmyyyy = d + '/' + m + '/' + y;

			$h += '<li class="' + classes.join(" ") + '" onclick="CALENDAR.setDate(\'' + ddmmyyyy + '\')">';
			$h += '<img src="assets/img/day.gif" />';
			$h += '<p>' + date.day + '</p>';
			$h += '</li>';
		});

		$h += '</ul>';

		MODAL.open($h,"","calendar");
	}

	publicAPIs.prevYear = function(){
		Y -= 1;
		CALENDAR.display(date_focus);
	}

	publicAPIs.prevMonth = function(){
		if (M == 0){
			M = 11;
			Y -= 1;
		}else{
			M -= 1;
		}
		CALENDAR.display(date_focus);
	}

	publicAPIs.nextMonth = function(){
		if (M == 11){
			M = 0;
			Y += 1;
		}else{
			M += 1;
		}
		CALENDAR.display(date_focus);
	}

	publicAPIs.nextYear = function(){
		Y += 1;
		CALENDAR.display(date_focus);
	}

	publicAPIs.setDate = function(_date){
		date_focus.closest("field").querySelector("input").value = _date;
		MODAL.close();
	}

	return publicAPIs;

})();

// Inizializzo field (es. replace . in , nei field[type=euro])
FIELD.init();

// INIZIALIZZO RICHTTEXT
RICHTEXT.init();

// SPAZIO AL FONDO (Essendo altezza del controls variabile)
if (document.querySelector('main')) {
	document.querySelector('main').style.paddingBottom = document.querySelector('controls').offsetHeight + 16 + "px";
}

FILE.get("images");
FILE.get("choice_images");
FILE.get("documents");
FILE.get("choice_documents");
FILE.get("attachments");

// INIT SCROLL
document.body.onscroll = SCROLL.save;