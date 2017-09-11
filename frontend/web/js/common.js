var overhill =
{
	// Время последней активности клиента
	lastClientActivity: 0,

	// Позиция курсора
	mouseX: 0, mouseY: 0,

	// Выгруженные страны
	usedCountries: {},

	container:
	{
		list:
		{
			adjustPositionOfList: function()
			{
				var position = jQuery('.overhill-search-ads-container').height();

				jQuery('.overhill-list-ads-container').css({top: position}).mCustomScrollbar('update');
			},

			toggleAdvanced: function(o)
			{
				o = jQuery(o);

				var c = jQuery('.overhill-search-ads-advanced-container');

				switch (c.is(':visible'))
				{
					case true
					:
						c.hide(0, function()
						{
							o.text('Расширенный поиск');

							overhill.container.list.adjustPositionOfList();
						});
						break;

					case false
					:
						c.show(0, function()
						{
							o.text('Обычный поиск');

							overhill.container.list.adjustPositionOfList();
						});
						break;
				}
			},

			toggle: function()
			{
				var left = $('.overhill-app-left'),
					right = $('.overhill-app-right'),
					visual = $('.overhill-app-right-toggle .fa');

				switch (right.attr('condition'))
				{
					case null :
					case 'hide' :
						left.removeClass('without-right');
						right.attr('condition', 'show').css({right: 0});
						visual.removeClass('fa-angle-left').addClass('fa-angle-right');
						break;

					default :
						left.addClass('without-right');
						right.attr('condition', 'hide').css({right: -600});
						visual.removeClass('fa-angle-right').addClass('fa-angle-left');
						break;
				}

				google.maps.event.trigger(overhillMap, 'resize');
			}
		}
	},

	show:
	{
		open: function(callback)
		{
			var container = jQuery('.overhill-ad-container');
			container.attr('condition', 'show');
			container.find('.overhill-ad-toggle .fa').removeClass('fa-angle-left').addClass('fa-angle-right');
			container.show(0).animate({right: 0}, function()
			{
				container.find('.overhill-ad-wrapper').mCustomScrollbar({theme: 'rounded-dark', scrollButtons: {enable: true, scrollAmount: 50}, mouseWheel: {scrollAmount: 300}});

				callback && callback(container.find('.overhill-ad-content'), container);
			});

			return overhill.container;
		},

		close: function(callback)
		{
			var container = jQuery('.overhill-ad-container');

			container.removeAttr('condition').animate({right: -900}, function()
			{
				overhillRouter.disableHashChange().delParam('advert');

				container.hide(0).find('.overhill-ad-content').empty();

				container.find('.overhill-ad-wrapper').mCustomScrollbar('destroy');

				callback && callback();
			});

			return overhill.container;
		},

		toggle: function()
		{
			var container = jQuery('.overhill-ad-container');

			if (container.attr('condition') !== 'hide')
			{
				container.attr('condition', 'hide').stop().animate({right: -900}, function()
				{
					container.find('.overhill-ad-toggle .fa').removeClass('fa-angle-right').addClass('fa-angle-left');
				});
			}
			else
			{
				container.attr('condition', 'show').stop().animate({right: 0}, function()
				{
					container.find('.overhill-ad-toggle .fa').removeClass('fa-angle-left').addClass('fa-angle-right');
				});
			}
		},

		templateSimilar: '\
		<div class="similar-ad">\
			<div class="similar-ad-photo">\
				<a href="javascript:void(0)" onclick="overhill.realty.getById(%id%)" title="Открыть объявление">%photo%</a>\
			</div>\
			<div class="similar-ad-info">\
				<div class="similar-ad-address">\
					<a href="javascript:void(0)" onclick="overhill.realty.getById(%id%)" title="Открыть объявление">%address%</a>\
				</div>\
				<div class="similar-ad-price">%price% %currency%</div>\
				<div class="similar-ad-area">%area% м²</div>\
			</div>\
			<div class="clear"></div>\
		</div>\
		'
	},

	simpleSlider: function(container)
	{
		var ul = container.find('ul');

		container.find('.prev').click(function()
		{
			ul.find('li:last-child').prependTo(ul);
		});

		container.find('.next').click(function()
		{
			ul.find('li:first-child').appendTo(ul);
		});

		return container;
	},

	bottomTabsControl: function(o)
	{
		o = jQuery(o);
		o.parent().find('a.toggle').removeClass('active');
		o.addClass('active');

		var a = jQuery('.overhill-app-tabs .overhill-app-tabs-box'),
			b = jQuery('.overhill-app-tabs .overhill-app-tabs-box .tab'),
			c = jQuery('.overhill-app-tabs .overhill-app-tabs-box .tab-' + o.attr('rel'));

		if (c.hasClass('active')) {
			a.slideToggle();
			return;
		}
		else if(! a.is(':visible')) {
			a.slideDown();
		}

		b.removeClass('active');
		c.addClass('active');

		b.hide(0);
		c.show(0);
	},

	toolbox:
	{
		// Обрезка изображения на лету используя алгоритм компонента "Resizer"
		resize: function(img, width, height)
		{
			img = img.split('.'); ext = img.pop(); return [img.join('.'), [width, height].join('x'), ext].join('.');
		},

		// Получение символа валюты по идентификатору
		currencyFormat: function(id)
		{
			return {RUR: '<span class="ruble">P</span>', EUR: '€', USD: '$'}[id] || null;
		},

		// Разделение числа по разрядам, форматирование
		numberFormat: function(v, n, c, s)
		{
			var x;
			var re = '\\d(?=(\\d{' + (x || 3) + '})+' + (n > 0 ? '\\D' : '$') + ')';
			var num = ($.type(v) === "string") ? v.replace(/\s+/g, '') : v;
			num = parseFloat(num).toFixed(Math.max(0, ~~n));

			return (c ? num.replace('.', c) : num).replace(new RegExp(re, 'g'), '$&' + (s || ','));
		},

		// Форматирование номера телефона
		phoneFormat: function(p)
		{
			if (p !== null)
			{
				var pre = (p.indexOf('+')===0) ? p.substr(0, 2) : p.substr(0, 1);
				var phone = p.substr(p.length-7, 3)+'-'+p.substr(p.length-4, 2)+'-'+p.substr(p.length-2)
				var code = p.substr(pre.length, p.length-phone.length);
				return pre+' '+code+' '+phone;
			}
		},

		time: function()
		{
			return Math.floor(new Date().getTime() / 1000);
		}
	},

	loadMenuCountries: function()
	{
		$.ajax({
		    url:"/realty/menucountries"
		}).done(function(data){
		    $("#overhill-countries").html(data);
		});
	},
	modal: 
	{
		login:
		{
			show: function()
			{
				$('#loginModal').modal('show');
			}
		},
		addRealty:
		{
			show: function()
			{
				$('#addRealtyModal').modal('show');
			}
		}
	}
};

overhill.lastClientActivity = overhill.toolbox.time();

jQuery(document).on('mousemove', function(event)
{
	overhill.lastClientActivity = overhill.toolbox.time();

	overhill.mouseX = event.pageX;
	overhill.mouseY = event.pageY;
});

// Альтернатива jQuery метода .on(),
// но использует установленную задержку в микросекундах
// @see http://api.jquery.com/on/
jQuery.fn.onDelay = function(name, time, callback)
{
	var timer = null;

	return this.on(name, function(element)
	{
		clearTimeout(timer);

		timer = setTimeout(function()
		{
			callback(jQuery(element.target));
		},

		time);
	});
};
overhill.loadMenuCountries();
realty.vip.unload('#overhill-vip-ads');