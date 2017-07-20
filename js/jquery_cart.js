$(document).ready(function(){
	$(".addToCart").click(function(e){
		e.preventDefault();
		window.location = $(this).attr("href") + "&quantity="+$("#pieces option:selected").val();
	});
});	

