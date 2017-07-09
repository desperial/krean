$(document).ready(function(){
	$.ajax({
	    url:"/realty/index"
	}).done(function(data){
	    $(".overhill-list-ads-content").html(data);
	});

});