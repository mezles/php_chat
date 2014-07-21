<!DOCTYPE html>
<html>
    <head>
        <title>Login :: PHP Long Pull</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <!-- Bootstrap -->
        <link href="http://netdna.bootstrapcdn.com/twitter-bootstrap/2.3.2/css/bootstrap-combined.min.css" rel="stylesheet">
        <link href="style.css" rel="stylesheet">
    </head>
    
    <body>
		<form id="sendmsg" method="POST">
        <div class="well span8">
            <div class="row">
                <div class="span3">
                    <label>Nick Name</label>
                    <input type="text" class="span3" id="name" placeholder="Your Nick Name" required />
                    <label>Message</label>
                    <textarea class="span3" id="field" placeholder="Your Message" required></textarea>
                    <button type="submit" class="btn btn-primary btn-block">Send</button>
                </div>
                <div class="span5">
                    <label>Message Dashboard</label>
                    <div name="message" id="message" class="hero-unit">
						<div id="announcement"></div>
						<div id="msgs"></div>
					</div>
                </div>
            </div>
        </div>
		</form>
        <script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
        <script src="http://netdna.bootstrapcdn.com/twitter-bootstrap/2.3.2/js/bootstrap.min.js"></script>
		<script type="text/javascript">
			var timestamp = null;
			function waitMsg( reload, name, msg ) {
				reload = reload || false;
				name = name || null;
				msg = msg || null;
				
				$.ajax({
					url: "getData.php",
					type: "GET",
					dataType: "json",
					async: true,
					cache: false,
					data: {
						"timestamp"	: timestamp,
						"name"	: name,
						"msg"	: msg
					},
					success: function( response ) {
						if ( response.msg != "" ) {
							if ( reload ) {
								$("#announcement").html('<p>Welcome to chat...</p>');
							}
							$("#msgs").html( unescape(response.msg) );
							fix_href();
						}
						timestamp = response.timestamp;
						setTimeout('waitMsg()', 1000);
					},
					error: function( XMLHttpRequest, textStatus, errorThrown ) {
						console.log( "error: " + textStatus + " (" + errorThrown + ")" );
						setTimeout('waitMsg()', 1000);
					}
				});
			}
			
			function fix_href() {
				$("#msgs").find("a:not([href^='http://'], [href^='https://'])").each(function() {
					$(this).attr("href", "http://" + $(this).attr("href") );
				});
			}
			
			
			
			$(document).ready(function() {
			
				$("form#sendmsg").submit(function(e) {
					e.preventDefault();
					
					var _name = $("#name").val()
					,	_msg = $("#field").val();
					
					$("#message").attr("id", "content");
					waitMsg( true, _name, _msg );
					
				});
			});
		</script>
    </body>
    
</html>