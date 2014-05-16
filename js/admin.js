$(document).ready(function() {
	
	tinymce.init({
		selector: "#imageDesc",
		height: 200,
		setup : function(ed) {
			ed.on('init', function() {
				this.getDoc().body.style.fontSize = '16px';
			});
		}
	});
	
	$('.folding-caret').live('click', function() {
		if ($(this).hasClass('fa-caret-right')) {
			$(this).removeClass('fa-caret-right');
			$(this).addClass('fa-caret-down');
		}
		else {
			$(this).addClass('fa-caret-right');
			$(this).removeClass('fa-caret-down');
		}
		$(this).closest('.folding-block').find('.folding-toggle').eq(0).toggleClass('hide');
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
		$('#imageEdit .modal-header h3').text('Добавить изображение');
		$('#imageEdit .file-block').removeClass('hide');
		tinymce.activeEditor.setContent('');
		$('#imageDir').val($(this).parents('.tour-block').find('.title').eq(0).text()+'/'+$(this).parents('.album-block').find('.title').eq(0).text());
		$('#isEditImage').remove();
		$('#imageEdit').modal('show');
		return false;
	});
	
	$('.editImage').live('click', function() {
		$('#imageEdit .modal-header h3').text('Редактирование изображения');
		$('#imageEdit .file-block').addClass('hide');
		tinymce.activeEditor.setContent($(this).parents('.image-block').find('img').next('.desc').html());
		$('#imageDir').val($(this).parents('.tour-block').find('.title').eq(0).text()+'/'+$(this).parents('.album-block').find('.title').eq(0).text()+'/images/'+$(this).find('img').attr('alt'));
		$('#imageEdit form').append('<input type="hidden" id="isEditImage" name="isEditImage" value="1">');
		$('#imageEdit').modal('show');
		return false;
	});
	
	$('#imageEdit .modal-footer .btn-primary').live('click', function() {
		var error = $('#imageEdit .text-error');
		
		error.text('');
		
		if (!$('#isEditImage').length && !$('#image').val().length) {
			error.text('Не выбрано изоражение!');
		}
		else {
			$('#imageEdit form').submit();
		}
		return false;
	});
	
	
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