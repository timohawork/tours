$(document).ready(function() {
	$('.add').live('click', function() {
		if ($(this).hasClass('newTour')) {
			$('#albumEdit .modal-header h3').text('Добавление экскурсии');
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
		if (!$('#albumCover').val().length) {
			fileError.text('Не выбрано изоражение для обложки!');
			hasError = true;
		}
		if (!hasError) {
			$('#albumEdit form').submit();
		}
		return false;
	});
});