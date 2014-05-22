$(document).ready(function() {
	
	var flagsTop =  $('#info-flags').offset().top;
	$(window).scroll(function() {
		$('#info-flags').animate({top: flagsTop+$(this).scrollTop()+'px'}, 100);
	});
	
	$('.jcarousel').jcarousel();

	$('.jcarousel-control-prev')
		.on('jcarouselcontrol:active', function() {
			$(this).removeClass('inactive');
		})
		.on('jcarouselcontrol:inactive', function() {
			$(this).addClass('inactive');
		})
		.jcarouselControl({
			target: '-=1'
		});

	$('.jcarousel-control-next')
		.on('jcarouselcontrol:active', function() {
			$(this).removeClass('inactive');
		})
		.on('jcarouselcontrol:inactive', function() {
			$(this).addClass('inactive');
		})
		.jcarouselControl({
			target: '+=1'
		});

	$('.jcarousel-pagination')
		.on('jcarouselpagination:active', 'a', function() {
			$(this).addClass('active');
		})
		.on('jcarouselpagination:inactive', 'a', function() {
			$(this).removeClass('active');
		})
		.jcarouselPagination();

	$("a[rel^='prettyPhoto']").prettyPhoto({
		animation_speed: 'normal',
		show_title: true,
		allow_resize: false,
		default_width: 800,
		default_height: 600,
		social_tools: false,
		slideshow: 5000
	});	
})