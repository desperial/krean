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
}