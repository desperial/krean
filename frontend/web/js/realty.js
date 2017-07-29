var markerIcon = new google.maps.MarkerImage('js/map/marker.png?v3', new google.maps.Size(32,50));
/*var overhillMap = new google.maps.Map(document.getElementById('overhill-map'), {
          	center: {lat: 50.0456224, lng: 2.6595129},
          	scrollwheel: true,
         	zoom: 4,
			mapTypeControl: true,
          	mapTypeControlOptions: {
              style: google.maps.MapTypeControlStyle.HORIZONTAL_BAR,
              position: google.maps.ControlPosition.TOP_CENTER
          },
          zoomControl: true,
          zoomControlOptions: {
              position: google.maps.ControlPosition.LEFT_CENTER
          },
          scaleControl: true,
          streetViewControl: true,
          streetViewControlOptions: {
              position: google.maps.ControlPosition.LEFT_TOP
          },
          fullscreenControl: true
        });
*/

var realty = new (function()
{
	var self = this;
	
	this.getById = function(id)
	{
		$('.overhill-ad-container').addClass('on-background-process');

		$.get('/realty/object', {id:id}, function(response)
		{
			jQuery('.overhill-ad-content').html(response);
			overhill.simpleSlider(jQuery('div.simple-slider'));
			$('.overhill-ad-content').mCustomScrollbar({theme: 'rounded-dark', scrollButtons: {enable: true, scrollAmount: 50}, mouseWheel: {scrollAmount: 300}, advanced: {updateOnImageLoad: false}, keyboard: {enable: false}});
			var container = jQuery('.overhill-ad-content');
			overhill.show.open(function(container, parentContainer)
			{
				$('.overhill-ad-container').removeClass('on-background-process');
			});
		});
	};
});
$(document).ready(function(){
	$.ajax({
	    url:"/realty/index"
	}).done(function(data){
	    $(".overhill-list-ads-content").html(data);
		$('.overhill-list-ads-container').mCustomScrollbar({theme: 'rounded-dark', scrollButtons: {enable: true, scrollAmount: 50}, mouseWheel: {scrollAmount: 300}, advanced: {updateOnImageLoad: false}, keyboard: {enable: false}});
		var position = $('.overhill-search-ads-container').height();
		$('.overhill-list-ads-container').css({top: position}).mCustomScrollbar('update');
	});

	$.ajax({
	    url:"/realty/coords"
	}).done(function(data){
		var location = $.parseJSON(data); 
		var locations = location.obj;
	   	overhillMap.init();

	   	var markers = locations.map(function(location, i) {
          return new google.maps.Marker({
            position: location,
			icon: markerIcon
          });
        });
	   	var markerCluster = new MarkerClusterer(overhillMap.getMap(), markers,
            {imagePath: 'https://developers.google.com/maps/documentation/javascript/examples/markerclusterer/m'});
	});
});