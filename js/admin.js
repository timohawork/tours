$(document).ready(function() {
	$('.edit').live('click', function() {
		var albumTitle = $('#albumTitle'),
			coverImg = $('#coverImg'),
			tourTitle = $(this).parents('.tour-block').find('.title').text();
		albumTitle.val('');
		coverImg.html('');
		$('.title-block .text-error').text('');
		$('.file-block .text-error').text('');
		
		if ($(this).hasClass('newTour')) {
			$('#albumEdit .modal-header h3').text('Добавление экскурсии');
			$('#albumEdit').modal('show');
		}
		if ($(this).hasClass('newAlbum')) {
			$('#albumEdit .modal-header h3').text('Добавление альбома');
			$('#albumEdit form').append('<input type="hidden" id="newTour" name="newTour" value="'+tourTitle+'">');
			$('#albumEdit').modal('show');
		}
		if ($(this).hasClass('editAlbum')) {
			$('#albumEdit .modal-header h3').text('Редактирование альбома');
			albumTitle.val(tourTitle);
			$('#albumEdit form').append('<input type="hidden" id="albumOrigTitle" name="albumOrigTitle" value="'+tourTitle+'">');
			coverImg.html('<img class="img-rounded preview" src="tours/'+tourTitle+'/cover.jpg" alt="'+tourTitle+'">');
			$('#albumEdit').modal('show');
		}
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
});