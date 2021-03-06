    $(function(){

      //忽略提示				
      $(".del_prompt_alerts").click(function(){
      	var str_url = 'del_prompt_alerts';
      	$.ajax({
      		url: str_url,
      		type: 'GET',
      		dataType: 'json',
      	})
      	.done(function(data) {
      		console.log(data);
      		if(data.status == 200){      				
      			$(".prompts_container").remove();
      		}else{
      			alert('未知错误！');
      		}

      	})
      	.fail(function() {
      		console.log("error");
      	})
      	.always(function() {
      		console.log("complete");
      	});      	       
      });

      //忽略点赞
      $(".all_praise").click(function() {      	

      	var Ajaxurl = 'del_all_praise_alert';		
      	$.ajax({
      		url: Ajaxurl,
      		type: 'GET',
      		dataType: 'json',      		
      	})
      	.done(function(data) {
      		if(data.status == 200){
				$(".praise_container").remove();				
      		}else {
      			alert('未知错误，请重试！');
      		}
      	console.log("success!");

      	})
      	.fail(function() {
      		console.log("error");
      	})
      	.always(function() {
      		console.log("complete");
      	});      	      	      	
      });


      //删除提示by_alert_id
      $(".del_alerts").click(function() {
         var alertId = $(this).attr('data-alert-id');
         var url = 'alert_messages/remove_alert_by_id'; 
         $.ajax({
           url: url,
           type: 'POST',
           dataType: 'json',
           data: {alert_id: alertId},
         })
         .done(function(data) {
          console.log("success");
         })
         .fail(function() {
           console.log("error");
         })
         .always(function() {
           console.log("complete");
         });
         
      });
      
      //是否打开照片墙
      $(".open_wall_photos").click(function() {
        var r = confirm("将加载大量图片，建议WiFi下查看，如果继续查看，请点击确定按钮!");
        if(!r){
          return false;
        }

      });
      //end
    });
