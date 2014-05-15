$(document).ready(function() {
	
	tinymce.init({
		selector: "#imageDesc",
		height: 200
	});
	
	$('.edit').live('click', function() {
		var albumTitle = $('#albumTitle'),
			coverImg = $('#coverImg'),
			tourTitle = $(this).parents('.tour-block').find('.title').eq(0).text();
		$('#newTour').remove();
		$('#albumOrigTitle').remove();
		albumTitle.val('');
		coverImg.html('');
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
			coverImg.html('<img class="img-rounded preview" src="tours/'+tourTitle+'/cover.jpg" alt="'+tourTitle+'">');
		}
		if ($(this).hasClass('editAlbum')) {
			var albumOrigTitle = $(this).parents('.album').find('.title').eq(0).text();
			$('#albumEdit .modal-header h3').text('Редактирование альбома');
			albumTitle.val(albumOrigTitle);
			$('#albumEdit form').append('<input type="hidden" id="albumOrigTitle" name="albumOrigTitle" value="'+albumOrigTitle+'"><input type="hidden" id="tourTitle" name="tourTitle" value="'+tourTitle+'">');
			coverImg.html('<img class="img-rounded preview" src="tours/'+tourTitle+'/'+albumOrigTitle+'/cover.jpg" alt="'+albumOrigTitle+'">');
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
	
	$('.delete').live('click', function() {
		var tour = $(this).parents('.tour-block'),
			album = $(this).parents('.album'),
			image = $(this).parents('.image-block'),
			data = {},
			text = 'Вы уверены, что хотите удалить ';
		
		if (image.length) {
			text += 'изображение?';
			data = {deleteImage: tour.find('.title').eq(0).text()+'/'+album.find('.title').eq(0).text()+'/images/'+$(this).attr('rel')};
		}
		else if (album.length) {
			text += 'альбом?';
			data = {deleteAlbum: tour.find('.title').eq(0).text()+'/'+album.find('.title').eq(0).text()};
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
		$('#imageDir').val($(this).parents('.tour-block').find('.title').eq(0).text()+'/'+$(this).parents('.album').find('.title').eq(0).text());
		$('#isEditImage').remove();
		$('#imageEdit').modal('show');
		return false;
	});
	
	$('.editImage').live('click', function() {
		$('#imageEdit .modal-header h3').text('Редактирование изображения');
		$('#imageEdit .file-block').addClass('hide');
		tinymce.activeEditor.setContent($(this).parents('.image-block').find('img').attr('data-desc'));
		$('#imageDir').val($(this).parents('.tour-block').find('.title').eq(0).text()+'/'+$(this).parents('.album').find('.title').eq(0).text()+'/images/'+$(this).parents('.image-block').find('.title').text());
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
});