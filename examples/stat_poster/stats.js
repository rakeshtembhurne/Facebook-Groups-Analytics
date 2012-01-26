$(function(){

	$('#show_result_publish').click(function() {
		var data = "publish=1&group="+$("#group").val()+"&from="+$("#from").val()+"&to="+$("#to").val();
		processRequest(data);
	});

    $("#frmStat").submit(function(event){
        event.preventDefault();
        if ($("#group").val() && $("#stat").val()) {
            $("#result").html("<strong>Loading.......</strong>");
            $.post(
                "ajax.php",
                $("#frmStat").serializeArray(),
                function(response){
                    console.log(response);
                    if (!response.error) {
                        $("#sourceId").val(response.formData.group);
                        $("#wallPostMessage").html(response.success.plainText);
                        $("#data-sender").css("display", "block");
                        $("#result").html("");
                    } else {
                        var errorMessage = "<div class='alert-message error'>" + response.error + "</div>";
                        $("#result").html(errorMessage);
                    }
                },
                'json'
            );
        } else {
            $("#result").html("<span class='error'>Please select a group and a statistic.</span>");
        }
    });

    $("#frmWallPost").submit(function(event){
        event.preventDefault();
        //if ($("#sourceId").val() && $("#wallPostMessage").val()) {
            $("#result").prepend("<div class='alert-message info'>Posting to group's wall...</div>");
            $.post(
                "send_to_wall.php",
                $("#frmWallPost").serializeArray(),
                function(response){
                    console.log(response);
                    if (!response.error) {
                        var message = "<div class='alert-message success'>" + response.success + "</div>";
                    } else {
                        var message = "<div class='alert-message error'>" + response.error + "</div>";
                    }
                    $("#data-sender").hide();
                    $("#result").html(message);
                },
                "json"
            );
            return false;
        /*} else {
            $("#result").prepend("<div class='alert-message error'>Form does not have necessary fields.</span>");
        }*/

    });
});