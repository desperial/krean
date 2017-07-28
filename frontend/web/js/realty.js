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
			overhill.container.show.open(function(container, parentContainer)
			{
				$('.overhill-ad-container').removeClass('on-background-process');

				if (response == false)
				{
					container.html
					(
						'<div class="page-not-found-title">404 — Page Not Found</div>' +
						'<div class="page-not-found-description">Запрошенной вами страницы не существует :(</div>'
					);

					return false;
				}

				overhillRouter.disableHashChange().setParam({advert: id});

				var _photos = '<img src="' + fenric.url('/web/upload/overhill/realty/0.350x197.png') + '" />';

				if (response.photos !== null)
				{
					if (response.photos.length > 0)
					{
						_photos = '<div class="simple-slider">';
							_photos += '<a href="javascript:void(0)" class="prev">';
								_photos += '<i class="fa fa-angle-left"></i>';
							_photos += '</a>';
							_photos += '<a href="javascript:void(0)" class="next">';
								_photos += '<i class="fa fa-angle-right"></i>';
							_photos += '</a>';
							_photos += '<ul>';

							for (var i = 0; i < response.photos.length; i++)
							{
								_photos += '<li>';
									_photos += '<a class="colorbox" rel="realty-photographies" href="' + fenric.url('/web/upload/overhill/realty/' + response.photos[i].filename) + '" target="_blank">';
										_photos += '<img src="' + fenric.url('/web/upload/overhill/realty/' + overhill.toolbox.resize(response.photos[i].filename, 350, 263)) + '" width="350" height="263" />';
									_photos += '</a>';
								_photos += '</li>';
							}

							_photos += '</ul>';
						_photos += '</div>';
					}
				}

				if (response.description === null)
				{
					response.description = fenric.i18n('Описание объекта отсутсвует...');
				}

				container.html
				(
					overhill.container.show.template

					.replace(/%photos%/g, _photos)
					.replace(/%description%/g, response.description)

					.replace(/%address%/g, response.address)

					.replace(/%country_name%/g, response.country_name)

					.replace(/%area%/g, overhill.toolbox.numberFormat(response.area, 2, '.', ' '))
					.replace(/%price%/g, overhill.toolbox.numberFormat(response.price, 0, '.', ' '))
					.replace(/%price_square_meter%/g, overhill.toolbox.numberFormat(response.price_square_meter, 2, '.', ' '))
					.replace(/%currency%/g, response.currency)
					.replace(/%formated_currency%/g, overhill.toolbox.currencyFormat(response.currency))

					.replace(/%deal%/g, response.deal)
					.replace(/%deal_suffix%/g, (response.deal=='rent') ? '(в месяц)' : '')
					.replace(/%type%/g, response.type)
					.replace(/%view%/g, response.view)
					.replace(/%group%/g, response.group)

					.replace(/%deal_str%/g, overhill.realty.i18n.deal[response.deal])
					.replace(/%type_str%/g, overhill.realty.i18n.type[response.type])
					.replace(/%view_str%/g, overhill.realty.i18n.view[response.view])
					.replace(/%group_str%/g, overhill.realty.i18n.group[response.group])

					.replace(/%latitude%/g, response.latitude)
					.replace(/%longitude%/g, response.longitude)

					.replace(/%id%/g, response.id)
					.replace(/%user%/g, response.user)
					.replace(/%shows%/g, response.shows)

					.replace(/%user_id%/g, response.user_id)
					.replace(/%user_name%/g, response.user_name)
					.replace(/%user_login_email%/g, response.user_login_email)
					.replace(/%user_contact_email%/g, response.user_contact_email || 'неизвестно')
					.replace(/%user_contact_phone%/g, response.user_contact_phone ? overhill.toolbox.phoneFormat(response.user_contact_phone) : 'неизвестно')

					.replace(/%date_create%/g, response.date_create)
					.replace(/%date_update%/g, response.date_update)

					.replace(/%is_autor_start%(.+)%is_autor_end%/g, response.is_autor ? '$1' : '')
				);

				// Галерея фотографий
				overhill.simpleSlider(container.find('div.simple-slider')).find('.colorbox').colorbox({maxWidth: '100%', maxHeight: '100%'});

				// Формирование похожих объявлений
				overhill.realty.getSimilarRealty(response.id, function(response)
				{
					if (response.sql !== null)
					{
						console.log(response.sql);
					}

					if (response.rows.length > 0)
					{
						var containerSimilar = container.find('div.similar-ads').show(0).find('div.similar-ads-list');

						for (var i = 0; i < response.rows.length; i++)
						{
							containerSimilar.append
							(
								overhill.container.show.templateSimilar

								.replace(/%id%/g, response.rows[i].id)
								.replace(/%photo%/, '<img src="' + fenric.url('/web/upload/overhill/realty/' + overhill.toolbox.resize(response.rows[i].photo, 100, 75)) + '" width="100" height="75" />')
								.replace(/%address%/, response.rows[i].address)
								.replace(/%price%/, overhill.toolbox.numberFormat(response.rows[i].price, 0, '.', ' '))
								.replace(/%currency%/, overhill.toolbox.currencyFormat(response.rows[i].currency))
								.replace(/%area%/, response.rows[i].area)
							);
						}
					}
				});

				(function(){return new google.maps.StreetViewService()})().getPanoramaByLocation(new google.maps.LatLng(response.latitude, response.longitude), 50, function(data, status)
				{
					if (status === google.maps.StreetViewStatus.OK)
					{
						new google.maps.StreetViewPanorama(container.find('.google-streetview').show(0).get(0),
						{
							position: new google.maps.LatLng(response.latitude, response.longitude), pov: {heading: 34, pitch: 10}
						});
					}
				});
			});
		});
	}
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