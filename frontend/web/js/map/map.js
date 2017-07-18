var overhillMap = new (function()
{
	/**
	 * Экземпляр текущего объекта
	 *
	 * @var     object
	 * @access  private
	 */
	var self = this;
	
	/**
	 * Объект Google карты
	 *
	 * @var     object
	 * @access  private
	 */
	var googleMapObject = null;
	
	/**
	 * Параметры Google карты по умолчанию
	 *
	 * @var     object
	 * @access  private
	 */
	var googleMapOptions = {container: '#overhill-map', latitude: 32, longitude: -10, zoom: 3, onClickMap: null, onClickMarker: null, onCompleted: null};
	
	/**
	 * Контейнер для Google карты
	 *
	 * @var     object
	 * @access  private
	 */
	var googleMapContainer = null;
	
	/**
	 * Контейнер для геообъектов
	 *
	 * @var     object
	 * @access  private
	 */
	var googleMapMarkers = null;
	
	/**
	 * Объект кластеризатора геообъектов
	 *
	 * @var     object
	 * @access  private
	 */
	var googleMapMarkersClusterer = null;
	
	/**
	 * Экземпляр Google Geocoder объекта
	 *
	 * @var     object
	 * @access  private
	 */
	var googleGeocoderInstance = null;
	
	/**
	 * Инициализация Google карты
	 *
	 * @param   object   options
	 *
	 * @access  public
	 * @return  object
	 */
	this.init = function(options)
	{
		if (typeof options === 'object')
		{
			googleMapOptions = jQuery.extend(googleMapOptions, options);
		}
		
		jQuery(window).load(function()
		{
			// Google карты не имеют технологии кластеризации меток, как бы это не казалось парадоксально, но это так.
			// По этой причине, прибегаем к стороннему классу, который закрывает столь существенный пробел Google карт...
			jQuery.getScript('/assets/map/markerclusterer_compiled.js', function()
			{
				self.construct();
			});
		});
		
		return self;
	};
	
	/**
	 * Конструктор Google карты
	 *
	 * @access  public
	 * @return  object
	 */
	this.construct = function()
	{
		if (googleMapObject === null)
		{
			googleMapContainer = jQuery(googleMapOptions.container).get(0);
			
			googleMapObject = new google.maps.Map(googleMapContainer,
			{
				// Центр карты по умолчанию
				center: new google.maps.LatLng(googleMapOptions.latitude, googleMapOptions.longitude),
				
				// Масштаб карты по умолчанию
				zoom: googleMapOptions.zoom,
				
				// Тип карты по умолчанию
				mapTypeId: google.maps.MapTypeId.ROADMAP,
				
				// Цвет фона карты во время её загрузки по умолчанию
				backgroundColor: '#e6e6e6',
				
				// Элементы управления карты (масштабирование)
				zoomControl: true,
				zoomControlOptions:
				{
					position: google.maps.ControlPosition.RIGHT_CENTER
				},
				
				// Элементы управления карты (Google street viewer)
				streetViewControl: true,
				streetViewControlOptions:
				{
					position: google.maps.ControlPosition.RIGHT_CENTER
				},
				
				// Элементы управления карты (остальные)
				panControl: false,
				mapTypeControl: false,
				scaleControl: false,
				overviewMapControl: false,
				
				// Стилизация карты
				styles:
				[
					// Цвет воды
					// {featureType: 'water', stylers: [{color: '#d3f0ff'}, {saturation: 0}]}
				]
			});
			
			// Костыль необходимый для заглушения клика по карте в случаи,
			// если клик происходит по кластеру, так как библиотека
			// MarkerClusterer такой возможности не предоставляет.
			googleMapObject.__clickableCluster = false;
			
			google.maps.event.addListener(googleMapObject, 'click', function(event)
			{
				if (googleMapObject.__clickableCluster === true) {
					googleMapObject.__clickableCluster = false;
					return;
				}
				
				var currentCursorPosition = {};
					currentCursorPosition.x = overhill.mouseX;
					currentCursorPosition.y = overhill.mouseY;
				
				$.waiting.init();
				
				self.getGeocoderInstance().geocode({latLng: event.latLng}, function(response, status)
				{
					$.waiting.deinit();
					
					if (status === google.maps.GeocoderStatus.OK)
					{
						for (var i = 0; i < response[0].address_components.length; i++)
						{
							if (response[0].address_components[i].types[0] === 'country')
							{
								var sn = response[0].address_components[i].short_name,
									ln = response[0].address_components[i].long_name,
									iu = fenric.url('web/upload/overhill/icons/countries/'+sn.toLowerCase()+'.png');
								
								jQuery('.special-country-menu').remove();
								
								jQuery('body').append
								(
									'<div id="special-country-menu-'+sn+'" class="special-country-menu" style="z-index:999;position:absolute;top:'+currentCursorPosition.y+'px;left:'+currentCursorPosition.x+'px;">'+
										'<div class="special-country-menu-content">'+
											'<div class="special-country-menu-header"><img src="'+iu+'" align="middle"/>&nbsp;'+ln+'</div>'+
											'<div class="special-country-menu-items">Загрузка данных...</div>'+
										'</div>'+
									'</div>'
								);
								
								jQuery.post('ajax/overhill-country_menu', {code:sn}, function(response)
								{
									jQuery('#special-country-menu-'+sn).each(function()
									{
										var mc = jQuery(this).find('.special-country-menu-items');
											mc.empty();
										
										if (response === null) {
											mc.text('Информация о стране отстутсвует.');
											return;
										}
										
										if (response.currency !== null) {
											mc.append('<p>Валюта в стране: <b>'+response.currency+'</b></p>');
										}
										
										if (response.forum !== null) {
											mc.append('<p><a target="_blank" href="http://forum.krean.ru/viewforum.php?f='+response.forum_id+'">Вся информация о стране на нашем форуме</a></p>');
											
											for (var i=0;i<response.forum.length;i++) {
												mc.append('<p><a target="_blank" href="http://forum.krean.ru/viewtopic.php?f='+response.forum[i].forum_id+'&t='+response.forum[i].topic_id+'">'+response.forum[i].topic_title+'</a></p>');
											}
										}
									});
								});
								
								// Перестраховка от лишних итераций...
								break;
							}
						}
					}
				});
			});
			
			// Удаление "меню страны" в случаи клика не по нему...
			jQuery(document).on('click', function(event)
			{
				jQuery(event.target).each(function()
				{
					if (! jQuery(this).closest('.special-country-menu').length)
					{
						jQuery('.special-country-menu').remove();
						
						event.stopPropagation();
					}
				});
			});
			
			jQuery.get('/ajax/overhill-unload_coordinates', function(response)
			{
				var markerIcon = new google.maps.MarkerImage(fenric.url('assets/map/marker.png?v3'), new google.maps.Size(32,50));
				
				googleMapMarkers = {};
				
				for (var i = 0; i < response.length; i++)
				{
					googleMapMarkers[response[i].id] = new google.maps.Marker({position: new google.maps.LatLng(response[i].latitude, response[i].longitude), icon: markerIcon});
					
					googleMapMarkers[response[i].id].marker_id = response[i].id;
					
					if (typeof googleMapOptions.onClickMarker === 'function')
					{
						google.maps.event.addListener(googleMapMarkers[response[i].id], 'click', function()
						{
							googleMapOptions.onClickMarker(this.marker_id);
						});
					}
				}
				
				// Сохранение экземпляра кластера
				googleMapMarkersClusterer = new MarkerClusterer(googleMapObject, googleMapMarkers,
				{
					styles: [{url: fenric.url('assets/map/cluster.png?v4'), width: 48, height: 48, textColor: 'white'}]
				});
				
				// Оповещение экземпляра карты о клике по кластеру
				google.maps.event.addListener(googleMapMarkersClusterer, 'clusterclick', function(event)
				{
					googleMapObject.__clickableCluster = true;
				});
				
				// Вызов события по окончанию всех операций с картой
				if (typeof googleMapOptions.onCompleted === 'function')
				{
					googleMapOptions.onCompleted(googleMapObject, self);
				}
				
				// Освобождение памяти
				delete markerIcon;
			});
		}
		
		return self;
	};
	
	/**
	 * Запуск анимации маркера
	 *
	 * @param   int      realty
	 * @param   int      animation
	 *
	 * @access  public
	 * @return  void
	 */
	this.startMarkerAnimation = function(realty, animation)
	{
		if (/^[0-9]{1,11}$/.test(realty))
		{
			if (googleMapMarkers[realty] !== undefined)
			{
				googleMapMarkers[realty].setAnimation(animation || google.maps.Animation.BOUNCE);
			}
		}
	};
	
	/**
	 * Остановка анимации маркера
	 *
	 * @param   int      realty
	 *
	 * @access  public
	 * @return  void
	 */
	this.stopMarkerAnimation = function(realty)
	{
		if (/^[0-9]{1,11}$/.test(realty))
		{
			if (googleMapMarkers[realty] !== undefined)
			{
				googleMapMarkers[realty].setAnimation(null);
			}
		}
	};
	
	/**
	 * Деструктор Google карты
	 *
	 * @access  public
	 * @return  object
	 */
	this.destruct = function()
	{
		if (googleMapObject !== null)
		{
			// Удаление всех событий
			google.maps.event.clearInstanceListeners(window);
			google.maps.event.clearInstanceListeners(document);
			google.maps.event.clearInstanceListeners(googleMapContainer);
			
			// Удаление всех меток в кластере
			googleMapMarkersClusterer.clearMarkers();
			
			// Освобождение памяти
			googleMapObject = googleMapContainer = googleMapMarkersClusterer = null;
		}
		
		return self;
	};
	
	/**
	 * Перезагрузка Google карты
	 *
	 * @access  public
	 * @return  object
	 */
	this.restart = function()
	{
		return self.destruct().construct();
	};
	
	/**
	 * Получение объекта Google карты
	 *
	 * @access  public
	 * @return  object
	 */
	this.getMap = function()
	{
		return googleMapObject;
	};
	
	/**
	 * Получение объекта кластеризатора геообъектов
	 *
	 * @access  public
	 * @return  object
	 */
	this.getClusterer = function()
	{
		return googleMapMarkersClusterer;
	};
	
	/**
	 * Получение экземпляра Google Geocoder объекта
	 *
	 * @access  public
	 * @return  object
	 */
	this.getGeocoderInstance = function()
	{
		if (googleGeocoderInstance === null)
		{
			googleGeocoderInstance = new google.maps.Geocoder();
		}
		
		return googleGeocoderInstance;
	};
	
	/**
	 * Позиционирование Google карты
	 *
	 * @param   float    latitude
	 * @param   float    longitude
	 * @param   int      zoom
	 *
	 * @access  public
	 * @return  object
	 */
	this.to = function(latitude, longitude, zoom)
	{
		googleMapObject.setCenter(new google.maps.LatLng(latitude, longitude));
		
		googleMapObject.setZoom(zoom || 18);
		
		return self;
	};
	
	/**
	 * Вызывается при изменении размера контейнера карты для того, чтобы карта применила новый размер
	 *
	 * @access  public
	 * @return  object
	 */
	this.triggerResize = function()
	{
		google.maps.event.trigger(googleMapObject, 'resize');
		
		return self;
	};
})();