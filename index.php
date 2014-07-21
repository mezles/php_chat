<?php
	session_start();
?>
<!DOCTYPE html>
<html>
    <head>
        <title>Chat v.1</title>
        <meta name="author" content="John Jason Q. Taladro">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <!-- Bootstrap -->
        <link href="//netdna.bootstrapcdn.com/twitter-bootstrap/2.3.2/css/bootstrap-combined.min.css" rel="stylesheet">
        <link href="assets/css/style.css" rel="stylesheet">
    </head>
    
    <body>		
        <div class="well span12">
		<form id="sendmsg" method="POST">
            <div class="row">
                <div class="span4">
					<div class="user-panel">
						<ul class="nav nav-pills first-nav">
							<li class="dropdown">
								<a class="dropdown-toggle" data-toggle="dropdown" href="#"><span id="status"><?php 
								echo ( isset($_SESSION['status']) ) ? $_SESSION['status'] : "Status"; ?></span><b class="caret"></b></a>
								<ul class="dropdown-menu">
									<li><a href="#" class="select-stats" data-status="online" data-text="Online">
										<span class="badge badge-success">&nbsp;</span>&nbsp;Online</a></li>
									<li><a href="#" class="select-stats" data-status="away" data-text="Away">
										<span class="badge badge-warning">&nbsp;</span>&nbsp;Away</a></li>
									<li><a href="#" class="select-stats" data-status="busy" data-text="Do not disturb">
										<span class="badge badge-important">&nbsp;</span>&nbsp;Do not disturb</a></li>
									<li><a href="#" class="select-stats" data-status="invisible" data-text="Invisible">
										<span class="badge">&nbsp;</span>&nbsp;Invisible</a></li>
									<li><a href="#" class="select-stats" data-status="offline" data-text="Offline">
										<span class="badge">&nbsp;</span>&nbsp;Offline</a></li>
								</ul>
							</li>
							<li class="dropdown pull-right">
								<a class="dropdown-toggle" data-toggle="dropdown" href="#">Action<b class="caret"></b></a>
								<ul class="dropdown-menu" role="menu" aria-labelledby="dLabel">
									<li><a id="logout" href="#">Logout</a></li>
								</ul>
							</li>
							<?php if( isset( $_SESSION['user'] ) ): ?>
							<li class="active span2"><a id="current_user" href="#"></a></li>
							<?php endif; ?>
						</ul>							
						<input type="text" class="span4" id="name" placeholder="Your Nick Name" required />
					</div>
                    <label>Message</label>
                    <textarea class="span4" id="field" placeholder="Your Message" required></textarea>
                    <button type="submit" class="btn btn-primary btn-block">Send</button>
                </div>
                <div class="span5">
                    <label>Message Dashboard</label>
                    <div name="message" id="message" class="hero-unit">
						<div id="announcement"></div>
						<div id="msgs"></div>
					</div>
                </div>
				<div class="span3">
					<label>Who is online?</label>
					<div class="online-user"></div>
				</div>
            </div>		
		</form>
		
		<footer>
          <p class="pull-right">&copy; John Jason Q. Taladro <?php echo date("Y"); ?></p>
        </footer>
    
        </div>
		
		<div class="modal fade hide" id="myModal" data-keyboard="false" data-backdrop="static">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header">
						<h4 class="modal-title">Hi, who are you?</h4>
					</div>
					<div class="modal-body">
						<form class="form-horizontal" id="form-login">
							<div class="form-group">
								<label for="nickname" class="col-lg-2 control-label">Nickname</label>
								<div class="col-lg-10">
									<input type="text" class="form-control" id="nickname" placeholder="Nickname">
								</div>
							</div>
						</form>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-primary" id="save_nickname" data-loading-text="Saving..." autocomplete="off">Save changes</button>
					</div>
				</div><!-- /.modal-content -->
			</div><!-- /.modal-dialog -->
		</div><!-- /.modal -->

        <script src="assets/js/LAB.min.js" type="text/javascript"></script>
		<script type="text/javascript">
			$LAB
				.script("//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js")				
				.script("http://netdna.bootstrapcdn.com/twitter-bootstrap/2.3.2/js/bootstrap.min.js")
				.script("assets/js/app.js")
				.wait(function(){ // wait for all scripts to execute first					
					
					$(document).ready(function() {
						var sess_user	= "";
						
						<?php if( !isset( $_SESSION['user'] ) ): ?>
						$('#myModal').modal('show');
						<?php else: ?>
						$("#nickname").val("<?php echo $_SESSION['user']; ?>");
						$(".dropdown").removeClass("hide");
						sess_user = $("#nickname").val();
						reset( sess_user );
						get_online();
						<?php endif; ?>
						
						$("form#sendmsg").submit(function(e) {
							e.preventDefault();
							
							var _name = $("#name").val()
							,	_msg = $("#field").val();
							
							$("#field").val("");
							$("#message").attr("id", "content");
							waitMsg( false, _name, _msg );
							
						});
						
						$("form#form-login").submit(function(e) {
							e.preventDefault();
							$("#save_nickname").click();
						});
						
						$("#save_nickname").click(function() {
							login();
						});
						
						$("#logout").click(function() {
							logout();
						});
						
						$(".select-stats").click(function() {
							var _status = $(this).data("status")
							,	_text 	= $(this).data("text");
							change_status( _status, _text );
						});
						
						touchScroll("content");
					});
				});
		</script>
    </body>
    
</html>