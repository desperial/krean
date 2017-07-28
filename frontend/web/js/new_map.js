var overhillMap = new (function() {
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
	var googleMapOptions = {
		container: '#overhill-map', 
		latitude: 32, 
		longitude: -10, 
		zoom: 3, 
		onClickMap: null, 
		onClickMarker: null, 
		onCompleted: null,
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
	};
	
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
		
		self.construct();
		
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
		}
		return self;
	}
	this.getMap = function()
	{
		return googleMapObject;
	}
});