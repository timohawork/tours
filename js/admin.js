$(document).ready(function() {
	
	var folding = new Folding();
	folding.init();
	
	$('.folding-caret').live('click', function() {
		folding.toggle($(this));
	});
	
	
	/* ------------------------------------------------------------------------------ */
	
	
	tinymce.init({
		selector: "#imageDesc",
		height: 200,
		setup : function(ed) {
			ed.on('init', function() {
				this.getDoc().body.style.fontSize = '16px';
			});
		}
	});
	
	$('.edit').live('click', function() {
		var albumTitle = $('#albumTitle'),
			tourTitle = $(this).closest('.tour-block').find('h4 .title').eq(0).text();
		$('#newTour').remove();
		$('#albumOrigTitle').remove();
		albumTitle.val('');
		$('.title-block .text-error').text('');
		$('.file-block .text-error').text('');
		
		if ($(this).hasClass('newTour')) {
			$('#albumEdit .modal-header h3').text('Добавление экскурсии');
		}
		if ($(this).hasClass('newAlbum')) {
			$('#albumEdit .modal-header h3').text('Добавление альбома');
			$('#albumEdit form').append('<input type="hidden" id="newTour" name="newTour" value="'+tourTitle+'">');
		}
		if ($(this).hasClass('editTour')) {
			$('#albumEdit .modal-header h3').text('Редактирование экскурсии');
			albumTitle.val(tourTitle);
			$('#albumEdit form').append('<input type="hidden" id="albumOrigTitle" name="albumOrigTitle" value="'+tourTitle+'">');
		}
		if ($(this).hasClass('editAlbum')) {
			var albumOrigTitle = $(this).closest('.album-block').find('.title').eq(0).text();
			$('#albumEdit .modal-header h3').text('Редактирование альбома');
			albumTitle.val(albumOrigTitle);
			$('#albumEdit form').append('<input type="hidden" id="albumOrigTitle" name="albumOrigTitle" value="'+albumOrigTitle+'"><input type="hidden" id="tourTitle" name="tourTitle" value="'+tourTitle+'">');
		}
		$('#albumEdit').modal('show');
		return false;
	});
	
	$('#albumEdit .modal-footer .btn-primary').live('click', function() {
		var titleError = $('.title-block .text-error'),
			fileError = $('.file-block .text-error'),
			hasError = false;
		
		titleError.text('');
		fileError.text('');
		
		if (6 > $('#albumTitle').val().length) {
			titleError.text('Неверно введено название!');
			hasError = true;
		}
		if (!$('#albumOrigTitle').length && !$('#albumCover').val().length) {
			fileError.text('Не выбрано изоражение для обложки!');
			hasError = true;
		}
		if (!hasError) {
			$('#albumEdit form').submit();
		}
		return false;
	});
	
	$('#tours-block .delete').live('click', function() {
		var tour = $(this).parents('.tour-block'),
			album = $(this).parents('.album-block'),
			image = $(this).parents('.image-block'),
			data = {},
			text = 'Вы уверены, что хотите удалить ';
		
		if (image.length) {
			text += 'изображение?';
			data = {deleteImage: tour.find('.title').eq(0).text()+'/'+album.find('h4 .title').eq(0).text()+'/images/'+$(this).attr('rel')};
		}
		else if (album.length) {
			text += 'альбом?';
			data = {deleteAlbum: tour.find('.title').eq(0).text()+'/'+album.find('h4 .title').eq(0).text()};
		}
		else if (tour.length) {
			text += 'экскурсию?';
			data = {deleteAlbum: tour.find('.title').eq(0).text()};
		}
		if (confirm(text)) {
			$.post('admin.php', data, function(response) {
				if (response) {
					window.location.reload();
				}
			});
		}
		return false;
	});
	
	$('.newImage').live('click', function() {
		$('#imageAdd .imageDir').val($(this).parents('.tour-block').find('.title').eq(0).text()+'/'+$(this).parents('.album-block').find('.title').eq(0).text());
		$('#imageAdd').modal('show');
		return false;
	});
	
	$('#imageAdd .modal-footer .btn-primary').live('click', function() {
		var error = $('#imageAdd .text-error');
		
		error.text('');
		
		if (!$('#image').val().length) {
			error.text('Не выбрано ни одно изоражение!');
		}
		else {
			$('#imageAdd form').submit();
		}
		return false;
	});
	
	$('.editImage').live('click', function() {
		tinymce.activeEditor.setContent($(this).parents('.image-block').find('img').next('.desc').html());
		$('#imageEdit .imageDir').val($(this).parents('.tour-block').find('.title').eq(0).text()+'/'+$(this).parents('.album-block').find('.title').eq(0).text()+'/images/'+$(this).find('img').attr('alt'));
		$('#imageEdit').modal('show');
		return false;
	});
	
	$('#imageEdit .modal-footer .btn-primary').live('click', function() {
		$('#imageEdit form').submit();
		return false;
	});
	
	
	/* ------------------------------------------------------------------------------ */
	
	
	tinymce.init({
		selector: "#edit-page textarea",
		width: 1000,
		height: 400,
		setup : function(ed) {
			ed.on('init', function() {
				this.getDoc().body.style.fontSize = '16px';
			});
		}
	});
	
	$('.page-block .delete').live('click', function() {
		if (confirm('Вы уверены, что хотите удалить страницу?')) {
			$.post('admin.php?page=static&do=delete', {name: $(this).prev('a').text()}, function(response) {
				if (response) {
					window.location.reload();
				}
			});
		}
	});
});

var Folding = function() {
	var self = this;
	
	self.toursType = 'tours';
	self.albumsType = 'albums';
	self.foldingValue = window.location.hash.replace('#', '');
	self.obj = {
		tours: [],
		albums: []
	};
	
	self.init = function() {
		var hash = self.foldingValue.split('&');
		$.each(hash, function(key, value) {
			var valueArray = value.split('=');
			if (self.toursType === valueArray[0]) {
				self.obj.tours.push(valueArray[1]);
				self.toggle(self.getCaret(self.toursType, valueArray[1]), false);
			}
			else if (self.albumsType === valueArray[0]) {
				self.obj.albums.push(valueArray[1]);
				self.toggle(self.getCaret(self.albumsType, valueArray[1]), false);
			}
		});
	}
	
	self.getCaret = function(type, name) {
		var blockClass;
		if (type === self.toursType) {
			blockClass = '.tour-block';
		}
		else if (type === self.albumsType) {
			blockClass = '.album-block';
		}
		return $(blockClass+' .title[rel="'+name+'"]').closest('.folding-block').find('.folding-caret').eq(0);
	}
	
	self.set = function(type, name, closing) {
		var result = '';
		
		if (!closing && type === self.toursType) {
			self.obj.tours.push(name);
		}
		else if (!closing && type === self.albumsType) {
			self.obj.albums.push(name);
		}
		else if (closing && type === self.toursType) {
			self.obj.tours.splice(self.obj.tours.indexOf(name), 1);
		}
		else if (closing && type === self.albumsType) {
			self.obj.albums.splice(self.obj.albums.indexOf(name), 1);
		}
		
		$.each(self.obj.tours, function(key, value) {
			result += '&'+self.toursType+'='+value;
		});
		$.each(self.obj.albums, function(key, value) {
			result += '&'+self.albumsType+'='+value;
		});
		
		result = result.substring(1);
		window.location.hash = self.foldingValue = result;
	}
	
	self.toggle = function(selector, set) {
		var block = selector.closest('.folding-block'),
			closing = selector.hasClass('fa-caret-down'),
			name = block.find('.title').eq(0).text(),
			type;

		if (block.hasClass('tour-block')) {
			type = self.toursType;
		}
		else if (block.hasClass('album-block')) {
			type = self.albumsType;
		}

		if (!closing) {
			selector.removeClass('fa-caret-right');
			selector.addClass('fa-caret-down');
		}
		else {
			selector.addClass('fa-caret-right');
			selector.removeClass('fa-caret-down');
		}
		block.find('.folding-toggle').eq(0).toggleClass('hide');
		
		if (undefined === set || set) {
			self.set(type, name, closing);
		}
	}
}