$(function() {
	jQuery.extend(jQuery.validator.messages, {
		required: "必須項目です",
		email: "メールアドレスを入力してください"
	});

	$(window).load(function() {
		$("#input_group").validate({
			errorPlacement: function(error, element) {
				element.parent().next('.error-place').append(error);
			}
		});
	});

	$('.ate1').html("* ");
	$('.ate1').css("color", "red");
	$('.error-place').css("color", "red");
	$('#mobile_img').show();
});
