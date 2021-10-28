$(function() {

	$('a.gallery').featherlightGallery({});

	$('#uploadAuctionPhotos').MultiFile({
		max: 10,
		accept: 'gif|jpg|png'
	  });

	$(".logo").lettering('words');

	$(function () {
	  $('[data-toggle="tooltip"]').tooltip()
	})

	$("#searchq").keyup(function(){
		var q = $(this).val();
		if(!q==""){
			$.ajax({
		      type : 'get',
		      dataType: 'html',
		      url : '/home/searchautocomplete/' + q , 
		      success : function(response) {
		      		$("#searchr").show();
		           $("#searchr").html(response);
		      }
		    });
		}
		else{
			$("#searchr").hide();
		}
	});

	$('#sold-popover').popover();

	$("#sbJoin").click(function(e) {
		e.preventDefault();
		
		document.location.href= '/users/join';
		
		return false;
	}); 

	$("#contact-form").validate({
		 errorClass: "alert alert-error",
		 submitHandler: function(form) {
		   $("#contact-form").ajaxSubmit({target: '#contact_output_div'});
		 },
		 highlight: function(element, errorClass) {
		    $(element).fadeOut(function() {
		      $(element).css("border", "1px solid red");
		      $(element).fadeIn();
		    });
		},
		rules: {
			    // simple rule, converted to {required:true}
			    yname: "required",
			    yemail: {
			      required: true,
			      email: true
			    },
			    ysubject: "required",
			    ymessage: "required",
			  },
		errorPlacement: function(error, element) {}
	});


	$("#login-form").validate({
		 errorClass: "alert alert-error",
		 submitHandler: function(form) {
		   $("#login-form").ajaxSubmit({target: '#login_output_div'});
		 },
		 highlight: function(element, errorClass) {
		    $(element).fadeOut(function() {
		      $(element).css("border", "1px solid red");
		      $(element).fadeIn();
		    });
		},
		rules: {
			    // simple rule, converted to {required:true}
			    uname: "required",
			    upwd: "required"
			  },
		errorPlacement: function(error, element) {}
	});
	
	$("#signup-form").validate({
		 errorClass: "alert alert-error",
		 submitHandler: function(form) {
		   $("#signup-form").ajaxSubmit({target: '#signup_output_div'});
		 },
		 highlight: function(element, errorClass) {
		    $(element).fadeOut(function() {
		      $(element).css("border", "1px solid red");
		      $(element).fadeIn();
		    });
		},
		rules: {
			    // simple rule, converted to {required:true}
			    username: "required",
			    email: {
			      required: true,
			      email: true
			    },
			    password: "required"
			  },
		errorPlacement: function(error, element) {}
	});
	
	$("#att").validate({
	 errorClass: "alert alert-error",
	 submitHandler: function(form) {
	   $("#att").ajaxSubmit({target: '.att_rs'});
	 }
	});
	
	function showRequest(formData, jqForm, options) {
		$('.ajax-modal-result').html('<img src="/img/ajax-loader.gif"/> Please wait.'); 
	} 


	var options = {target: '#comment_output'}; 
    
	$("#comment-form").validate({
	 errorClass: "alert alert-error",
	 submitHandler: function(form) {
	   $("#comment-form").ajaxSubmit(options);
	   var lastID = $(".user_comments > li:last").attr('data-lastID');
	   var movID = $("#movID").html();
	   $.post('/listings/ajax_last_comment', {last : lastID, movie: movID}, function(data) {
	   		$('.user_comments').append(data);
	   		$("html,body").animate({scrollTop: $('.user_comments li:last').offset().top - 30});
	   });
	 }
	});
	
	
	
	
	$('.remove_c').click(function() {
		var remID = $(this).attr("id");
		var dosplit = remID.split("_");
		var theID = dosplit[1];
		var link = $(this);
		var listID = $("#movID").html(); 
		
		$.get('/listings/remove_c/' + theID + '/' + listID, function(data) {
			if(data != 'ok') {
				$(link).html(data);
			}else{
				$(link).parent().hide('slow');
			}
		});
		
	});
	
	$("a[data-target=#myModal]").click(function(ev) {
	    ev.preventDefault();
	    var target = $(this).attr("href");
	
	    // load the url and show modal on success
	    $("#myModal .modal-body").load(target, function() { 
	         $("#myModal").modal("show"); 
	    });
	    
	    return false;
	});
	
});
