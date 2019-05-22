
		
	function contact()
	{
		
		var name = $("#name").val();
		var age = $("#age").val();
		var submit = $("#submit").val();
		
		$.ajax({
			url:"http://localhost/cigenerator/contact",
			data:{
				name:name,
				age:age,
				submit:submit
				},
			type:"post",
			beforeSend:function() {
				
			},
			success:function(result) {
				var arr = JSON.parse(result);
				if(arr['status']==1)
				{
					$("#result_msg").html("<font style='color:green;'>"+arr['msg']+"</font>");
				}
				else
				{
					$("#result_msg").html("<font style='color:red;'>"+arr['msg']+"</font>");
				}
			},
			error:function() {
				
			}

		});
	}
