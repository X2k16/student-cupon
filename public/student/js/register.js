$(function() {

	$('#input_group').submit(function(e) {
		var _UA = navigator.userAgent;
		$("#mail_error").html("");

		if (_UA.indexOf('iPhone') > -1 || _UA.indexOf('iPod') > -1 || _UA.indexOf('Android') > -1) {
			//if invalid do nothing
			if(!$("#input_group").valid() && !$("input[name='email']").val()){
				alert("未入力項目があります");
				return false;

			} else if(!$("input[name='email']").val().match(/.+ac\.jp$/)){
				alert("大学のメールアドレスを入力してください");
				$("#mail_error").html("大学のメールアドレスを入力してください");
				return false;
			}

		}else{
		//if invalid do nothing
			if(!$("#input_group").validationEngine('validate')){
				alert("未入力項目があります");
				return false;
			} else if(!$("input[name='email']").val().match(/.+ac\.jp$/)){
			 	alert("大学のメールアドレスを入力してください");
			 	return false;
			}
		}

		// Ajax通信を開始する
		$.ajax({
				url: 'api.php',
				type: 'post',
				dataType: 'json',
				async: false,
				contentType: "application/json",
				data: JSON.stringify({
					name: $("input[name='name']").val(),
					mail: $("input[name='email']").val(),
					sex: $("input[name='sex']").val(),
					university: $("input[name='university']").val(),
					department: $("input[name='department']").val(),
					career: $("input[name='career']").val()
				})
			})
			// ・ステータスコードは正常で、dataTypeで定義したようにパース出来たとき
			.done(function(response) {
				alert("入力してただいたアドレスにメールを送信しました。\nクーポンコードを記載しておりますので、ご確認ください。");
			})
			// ・サーバからステータスコード400などが返ってきたとき
			// ・ステータスコードは正常だが、dataTypeで定義したようにパース出来なかったとき
			// ・通信に失敗したとき
			.fail(function(err) {
				var response = err.responseJSON;
				if(response.message == "duplicated") {
					alert("メールアドレスが既に登録されています");
				} else {
					alert("登録に失敗しました");
				}
			});

		return e.preventDefault();
	});

});
