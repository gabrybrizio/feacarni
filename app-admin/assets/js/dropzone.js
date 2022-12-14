document.body.ondrag = function(){win_drag(event)};
document.body.ondragstart = function(){win_drag(event)};
var win_drag = function(e){
	e.preventDefault();
	e.stopPropagation();
};


document.body.ondragover = function(){win_dragover(event)};
document.body.ondragenter = function(){win_dragover(event)};
var win_dragover = function(e){
	e.preventDefault();
	e.stopPropagation();

	dropzone.classList.add('is-visible');
	dropzone_hover.classList.remove('hidden');
	dropzone_uploading.classList.add('hidden');
};

document.body.ondragleave= function(){win_dragleave(event)};
document.body.ondragend = function(){win_dragleave(event)};
var win_dragleave = function(e){
	e.preventDefault();
	e.stopPropagation();

	dropzone.classList.remove('is-visible');
};

document.body.ondrop = function(){win_drop(event)};
var win_drop = function(e){
	e.preventDefault();
	e.stopPropagation();

	dropzone.classList.remove('is-visible');
	files_dropped = e.dataTransfer.files;

	// prendo il primo input[type=file] della pagina
	let el = document.querySelector('input[type=file]');
	FIELD.focus(el);
	FILE.upload(el);
};