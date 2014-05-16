loadWait = 30000;
loadCheck = 300;
preloadObjects = "img";
notImagesLoaded = [];
excludeImages = false;
 
function getScreenHeight() {
	var myHeight = 0;
	if ("number" === typeof(window.innerHeight)) {
		myHeight = window.innerHeight;
	}
	else if (document.documentElement && (document.documentElement.clientHeight || document.documentElement.clientHeight)) {
		myHeight = document.documentElement.clientHeight;
	}
	else if (document.body && (document.body.clientHeight || document.body.clientHeight)) {
		myHeight = document.body.clientHeight;
	}
	return myHeight;
}
 
function preloadOther() {
	var l = notImagesLoaded.length;
	var currentExists = false;

	for (var i = 0;i < l;i++) {
		var item = notImagesLoaded[i];
		if (item) {
			loadImage(item);
			currentExists = true;
		};
	};
	if (!currentExists) {
		notImagesLoaded = [];
		$(window).unbind("scroll", preloadOther);
	};
};
 
function imagesPreloader() {
	$(preloadObjects).each(function() {
		var item = this;
		if ("img" === item.nodeName.toLowerCase() && ("undefined" === typeof(excludeImages) || false === excludeImages || (-1 == item.className.indexOf(excludeImages)))) {
			item.longDesc = item.src;
			item.src = "#";
			item.alt = "";

			var preloaderElt = $("<span></span>");
			$(preloaderElt).css({display: "block"});
			preloaderElt.className = "preloader "+item.className;
			$(item).before(preloaderElt);
			loadImage(item);
		};
	});
	$(window).bind("scroll", preloadOther);
};
 
function loadImage(item) {
	var pos = jQuery(item).position(),
		ItemOffsetTop = "object" === typeof(pos) && "undefined" !== typeof(pos.top) ? pos.top : 0,
		documentScrollTop = jQuery(window).scrollTop(),
		scrHeight= getScreenHeight();
 
	if (ItemOffsetTop <= (documentScrollTop + scrHeight) && "undefined" === typeof(item.storePeriod)) {
 
		item.src = item.longDesc;
		item.onerror = function() {
			this.width = 0;
			this.height = 0;
		}
		item.onabort = function(){
			this.width = 0;
			this.height = 0;
		}
		item.wait = 0;
		item.storePeriod = setInterval(function() {
			item.wait += loadCheck;
			if (item.width && item.height && item.complete) {
				clearInterval(item.storePeriod);
				item.storePeriod = false;
				jQuery(item.previousSibling).remove();
				jQuery(item).css("visibility", "visible");
				if ("undefined" !== typeof(item.loadedCount) && notImagesLoaded[item.loadedCount]) {
					notImagesLoaded[item.loadedCount] = false;
				};
			}
			else if (item.wait > loadWait) {
				clearInterval(item.storePeriod);
				item.storePeriod = false;
				if ("undefined" !== typeof(item.loadedCount) && notImagesLoaded[item.loadedCount]) {
					notImagesLoaded[item.loadedCount] = false;
				};
				jQuery(item).css({
					display: "none",
					visibility: "hidden"
				});
				jQuery(item.previousSibling).remove();
			};
		}, loadCheck);
	}
	else {
		if("undefined" === typeof item.loadedCount) {
			item.loadedCount = notImagesLoaded.length;
			notImagesLoaded[item.loadedCount] = item;
		};
	};
};
 
jQuery(document).ready(imagesPreloader);