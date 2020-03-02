$(window).ready(function(){
	if ($(window).width()<=1024) {
		$(".menu-btn").on("click",function(){
			$("#mask-overlay").fadeIn();
			$(".sidebar-container").addClass("active");
		});
		
		$("#mask-overlay").on("click",function(){
			$("#mask-overlay").fadeOut();
			$(".sidebar-container").removeClass("active");
		});
	}
	else{
		$(".menu-btn").off("click",function(){
			$("#mask-overlay").fadeIn();
			$(".sidebar-container").addClass("active");
		});
		
		$("#mask-overlay").off("click",function(){
			$("#mask-overlay").fadeOut();
			$(".sidebar-container").removeClass("active");
		});
	}
});

$(window).resize(function(){
	if ($(window).width()<=1024) {
		$(".menu-btn").on("click",function(){
			$("#mask-overlay").fadeIn();
			$(".sidebar-container").addClass("active");
		});
		
		$("#mask-overlay").on("click",function(){
			$("#mask-overlay").fadeOut();
			$(".sidebar-container").removeClass("active");
		});
	}
	else{
		$(".menu-btn").off("click",function(){
			$("#mask-overlay").fadeIn();
			$(".sidebar-container").addClass("active");
		});
		
		$("#mask-overlay").off("click",function(){
			$("#mask-overlay").fadeOut();
			$(".sidebar-container").removeClass("active");
		});
	}
});