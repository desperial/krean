var overhill =
{
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
	}

	show:
		{
			open: function(callback)
			{
				var container = jQuery('.overhill-ad-container');

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

			template: '\
			<div class="item">\
				<div class="item-left">\
					<div class="item-photos">\
						%photos%\
					</div>\
					<div class="seller-ads">\
						<div class="seller-ads-header">Продавец</div>\
						<div class="seller-ads-contact-name"><i class="fa fa-user"></i>&nbsp;&nbsp;%user_name%</div>\
						<div class="seller-ads-contact-feedback"><i class="fa fa-bullhorn"></i>&nbsp;&nbsp;<a href="javascript:void(0)" onclick="overhillRealty.service.feedback(this, %id%)">Связаться с продавцом</a></div>\
						<div class="seller-ads-contact-phone">\
							<a href="javascript:void(0)" onclick="overhillRealty.service.requestPhone(%id%, this)" title="Показать номер телефона продавца">%user_contact_phone%</a>\
						</div>\
						<div class="seller-ads-note">Пожалуйста, сообщите продавцу,<br/>что нашли это объявление на Krean.ru</div>\
					</div>\
					<div class="google-streetview"></div>\
					<div class="similar-ads">\
						<div class="similar-ads-header">Похожие объявления</div>\
						<div class="similar-ads-list"></div>\
					</div>\
					<div class="banner" style="display:none">\
						<img src="' + fenric.url('web/upload/test-001.png?v2') + '" width="350" height="254" />\
					</div>\
				</div>\
				<div class="item-right">\
					<div class="item-header">\
						<div class="item-header-title">%view_str%, %country_name%</div>\
						<div class="item-header-address">\
							<a href="javascript:void(0)" onclick="overhillMap.to(%latitude%, %longitude%)" title="Показать на карте">%address%</a>\
						</div>\
					</div>\
					<div class="item-data">\
						<div class="item-data-list-left">\
							<div class="item-data-list-label">Цена</div>\
						</div>\
						<div class="item-data-list-right">\
							<div class="item-data-list-label">%price% %formated_currency% %deal_suffix%</div>\
						</div>\
						<div class="clear"></div>\
						<div class="item-data-list-left">\
							<div class="item-data-list-label">Общая площадь</div>\
						</div>\
						<div class="item-data-list-right">\
							<div class="item-data-list-label">%area% м²</div>\
						</div>\
						<div class="clear"></div>\
						<div class="item-data-list-left">\
							<div class="item-data-list-label">Цена за квадратный метр</div>\
						</div>\
						<div class="item-data-list-right">\
							<div class="item-data-list-label">%price_square_meter% %formated_currency%</div>\
						</div>\
						<div class="clear"></div>\
						<div class="item-data-list-left">\
							<div class="item-data-list-label">Вид сделки</div>\
						</div>\
						<div class="item-data-list-right">\
							<div class="item-data-list-label">%deal_str%</div>\
						</div>\
						<div class="clear"></div>\
						<div class="item-data-list-left">\
							<div class="item-data-list-label">Тип объекта</div>\
						</div>\
						<div class="item-data-list-right">\
							<div class="item-data-list-label">%type_str%</div>\
						</div>\
						<div class="clear"></div>\
						<div class="item-data-list-left">\
							<div class="item-data-list-label">Социальная группа объекта</div>\
						</div>\
						<div class="item-data-list-right">\
							<div class="item-data-list-label">%group_str%</div>\
						</div>\
						<div class="clear"></div>\
						<div class="item-data-list-left">\
							<div class="item-data-list-label">Номер объявления</div>\
						</div>\
						<div class="item-data-list-right">\
							<div class="item-data-list-label">%id%</div>\
						</div>\
						<div class="clear"></div>\
						<div class="item-data-list-left">\
							<div class="item-data-list-label">Дата создания</div>\
						</div>\
						<div class="item-data-list-right">\
							<div class="item-data-list-label">%date_create%</div>\
						</div>\
						<div class="clear"></div>\
						<div class="item-data-list-left">\
							<div class="item-data-list-label">Дата обновления</div>\
						</div>\
						<div class="item-data-list-right">\
							<div class="item-data-list-label">%date_update%</div>\
						</div>\
						<div class="clear"></div>\
						<div class="item-data-list-left">\
							<div class="item-data-list-label">Количество просмотров</div>\
						</div>\
						<div class="item-data-list-right">\
							<div class="item-data-list-label">%shows%</div>\
						</div>\
						<div class="clear"></div>\
					</div>\
					<div class="item-description">%description%</div>\
				</div>\
				<div class="clear"></div>\
			</div>\
			',

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
			',
		},
	},
}