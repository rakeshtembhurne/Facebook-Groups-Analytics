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
                        var wallPostForm = "<form id='frmWallPost' action='#' method='post'>"
                                           + "<textarea id='wallPostMessage' name='wallPostMessage' rows='15' class='xxlarge'>" + response.success.plainText + "</textarea>"
                                           + "<input type='submit' value='Post to Group wall' class='btn info' /><br />"
                                           + "</form>";
                        $("#result").html(wallPostForm);
                    } else {
                        var errorMessage = "<div class='alert-message error'>" + response.error + "</div>";
                        $("#result").html(wallPostForm);
                    }
                },
                'json'
            );
        } else {
            $("#result").html("<span class='error'>Please select a group and a statistic.</span>");
        }
    });
});