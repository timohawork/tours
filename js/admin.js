$(document).ready(function() {
	
	var folding = new Folding();
	folding.init();
	
	$('.folding-caret').live('click', function() {
		folding.toggle($(this));
	});
	
	$('.folding-block h4').live('click', function() {
		$(this).parent().find('.folding-caret').eq(0).trigger('click');
	});
	
	
	/* ------------------------------------------------------------------------------ */
	
	
	$('.edit').live('click', function() {
		var title = $(this).closest('.album-block').find('h4 .title').eq(0).text(),
			path = $(this).closest('.album-block').attr('rel');
		$('.title-block .text-error').text('');
		$('.file-block .text-error').text('');
		$('#albumTitle').val(!$(this).hasClass('album-edit') ? '' : title);
		$('#albumPath').val(path);
		$('#albumEdit .modal-header h3').text((!$(this).hasClass('album-edit') ? 'Добавление' : 'Редактирование')+' альбома');
		$('#isEdit').val($(this).hasClass('album-edit') ? 1 : 0);
		$('#albumEdit .album-desc').remove();
		path = undefined === path ? [] : path.split('/');
		$('#albumEdit').removeClass('wide');
		if (2 == path.length) {
			$('#albumEdit form').append('<div class="control-group album-desc">'+
				'<label class="control-label" for="albumDesc">Описание</label>'+
				'<div class="controls">'+
					'<textarea class="span3" name="albumDesc" id="albumDesc">'+($(this).closest('.album-block').find('.description').eq(0).text())+'</textarea>'+
				'</div>'+
			'</div>');
			tinymce.init({
				selector: "#albumDesc",
				height: 200,
				setup : function(ed) {
					ed.on('init', function() {
						this.getDoc().body.style.fontSize = '16px';
					});
				}
			});
			$('#albumEdit').addClass('wide');
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
		
		if (1 > $('#albumTitle').val().length) {
			titleError.text('Неверно введено название!');
			hasError = true;
		}
		if (0 == $('#isEdit').val() && !$('#albumCover').val().length) {
			fileError.text('Не выбрано изоражение для обложки!');
			hasError = true;
		}
		if (!hasError) {
			Progress.show('#albumEdit form');
		}
		return false;
	});
	
	$('.delete').live('click', function() {
		var image = $(this).parents('.image-block'),
			data = {},
			text = 'Вы уверены, что хотите удалить '+(image.length ? 'изображение' : 'альбом')+'?';
		
		if (image.length) {
			data = {deleteImage: $(this).closest('.image-block').find('img').attr('src')};
		}
		else {
			data = {deleteAlbum: $(this).closest('.album-block').attr('rel')};
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
		$('#imageAdd .imageDir').val($(this).attr('rel'));
		$('#imageAdd').modal('show');
		return false;
	});
	
	$('#imageAdd .modal-footer .btn-primary').live('click', function() {
		var error = $('#imageAdd .text-error');
		error.text('');
		!$('#image').val().length ? error.text('Не выбрано ни одно изоражение!') : Progress.show('#imageAdd form');
		return false;
	});
	
	$('.editImage').live('click', function() {
		$('#imageDesc').val($(this).parents('.image-block').find('img').next('.desc').html());
		$('#imageEdit .imageDir').val($(this).find('img').attr('src'));
		$('#imageEdit').modal('show');
		return false;
	});
	
	$('#imageEdit .modal-footer .btn-primary').live('click', function() {
		Progress.show('#imageEdit form');
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
	
	$('.deletePage').live('click', function() {
		if (confirm('Вы уверены, что хотите удалить страницу?')) {
			$.post('admin.php?page=static&do=delete', {name: $(this).parent().find('.title').text()}, function(response) {
				if (response) {
					window.location.reload();
				}
			});
		}
	});
});

var Folding = function()
{
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
			buttons = block.find('.album-buttons').eq(0),
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
			buttons.removeClass('hide');
		}
		else {
			selector.addClass('fa-caret-right');
			selector.removeClass('fa-caret-down');
			buttons.addClass('hide');
		}
		block.find('.folding-toggle').eq(0).toggleClass('hide');
		
		if (undefined === set || set) {
			self.set(type, name, closing);
		}
	}
}

var Progress = new function()
{
	this.show = function(form) {
		$(form).ajaxForm({
			beforeSend: function() {
			$(form).append('<div class="progress progress-striped active">'+
				'<div class="bar"></div>'+
			'</div>');
				$(form+' .bar').width('0%');
			},
			uploadProgress: function(event, position, total, percentComplete) {
				$(form+' .bar').width(percentComplete+'%');
			},
			success: function() {
				$(form+' .bar').width('100%');
			},
			complete: function(xhr) {
				window.location.reload();
			}
		}).submit();
	}
	
	this.hide = function() {
		$('.progress').remove();
	}
}