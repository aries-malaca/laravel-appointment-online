


	$(document).ready(function(){
		// alert("AHAHA");
		$('#btnPush').on('click',function(e){
			e.preventDefault();
			sendPushNotification();
		})
	})

	

	function sendPushNotification(){
		// alert("Sending...");
		$.ajax({
			method:"POST",
			url:"../../api/mobile/sendPushNotification",
			beforeSend:function(){
				
			},
			success:function(data){
				// alert(data);
			}


		})
	}


