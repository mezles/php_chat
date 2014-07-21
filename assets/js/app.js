var timestamp = null
, xhr_msg = null
, xhr_ol = null;
					
function waitMsg( reload, name, msg ) {
	reload = reload || false;
	name = name || null;
	msg = msg || null;
	
	if ( xhr_msg !== null ) xhr_msg.abort();
	xhr_msg = $.ajax({
		url: "getData.php",
		type: "GET",
		dataType: "json",
		async: true,
		cache: false,
		data: {
			"timestamp"	: timestamp,
			"name"	: name,
			"msg"	: msg,
			'action'	: 'chat_content'
		},
		success: function( response ) {
			if ( reload ) {
				$("#announcement").html('<p><strong>Hi ' + $('#name').val() + ', welcome to chat</strong></p>');
			}
			
			if ( response.msg !== "" ) {							
				
				$("#msgs").html( unescape(response.msg) );
				
				if ( reload ) {
					setTimeout(function() {
						$("#content").animate({ scrollTop: $("#content").prop("scrollHeight") - $("#content").height() }, 100);
					}, 2000);
					
				} else {
					$("#content").animate({ scrollTop: $("#content").prop("scrollHeight") - $("#content").height() }, 100);
				}
					
				fix_href();
				fix_msg();
				/* get_online(); */
			}
			timestamp = response.timestamp;
			setTimeout(waitMsg(), 1000);
		},
		error: function( XMLHttpRequest, textStatus, errorThrown ) {
			/* console.log( "error: " + textStatus + " (" + errorThrown + ")" ); */
			if ( textStatus !== 'abort') setTimeout(waitMsg(), 10000);
		}
	});
}

function fix_href() {
	$("#msgs").find("a:not([href^='http://'], [href^='https://'])").each(function() {
		$(this).attr("href", "http://" + $(this).attr("href") );
	});
}

function fix_msg() {
	$("#msgs").find("span[data-user='" + $("#name").val() + "']").each(function() {
		$(this).attr("class", "text-muted");
	});
}

function login() {
	var _this = $(this)
	,	_nickname = $("#nickname").val();
	
	if ( $(".alert-error").length > 0 ) {
		$(".alert-error").remove();
	}
	
	if ( $.trim( _nickname ) === "" ) {
		$(".modal-body").prepend(
			"<div class=\"alert alert-error\">" + 
			"<strong>Oh crap!</strong>  You should have a name." +
			"</div>"
		);
	} else {
		$.ajax({
			url: "getData.php",
			type: "POST",
			dataType: "json",
			async: true,
			cache: false,
			data: {
				"nickname"	: _nickname,
				'action'	: 'login'
			},
			beforeSend: function() {
				$(".alert-error").remove();
				_this.button("loading");
			},
			success: function( response ) {
				if ( !response.error ) {
					_this.button("reset");
					$('#myModal').modal('hide');
					$("#nickname").val("");
					$("#name").addClass("hide").val( _nickname );
					$("#current_user").text( _nickname );
					$("#message").attr("id", "content");
					$(".dropdown").removeClass("hide");
					$("ul.first-nav").append(
						"<li class=\"active span2\"><a id=\"current_user\" href=\"#\">" + _nickname + "</a></li>"
					);
					waitMsg( true, _nickname );
					get_online();
				} else {
					_this.button("reset");
					$(".modal-body").prepend(
						"<div class=\"alert alert-error\">" + 
						"<strong>Oh crap!</strong> " + response.msg +
						"</div>"
					);
				}
			}
		});
		
	}
}

function get_online() {
	if ( xhr_ol !== null ) xhr_ol.abort();
	
	xhr_ol = $.ajax({
		url: "getData.php",
		type: "GET",
		dataType: "json",
		async: true,
		cache: false,
		data: {
			"timestamp" : timestamp,
			"action"	: "whosonline"
		},
		success: function( response ) {
			var _online = [];
			var _users = [];
			if ( $.isArray( response.online ) ) {
				$.each( response.online, function( i, val ) {
					var online_class = "";
					switch( val.status ) {
						case "online" :
							online_class = " label-success";
							break;
						case "away" :
							online_class = " label-warning";
							break;
						case "busy" :
							online_class = " label-important";
							break;
						case "invisible" :
						case "offline" :
							online_class = " ";
							break;
						default:
							online_class = " label-success";
							break;
					}
					_online[i] = "<p class='label" + online_class + " btn-block'>" + val.name + "</p>";
					_users[i] = val.name;
				});
			}
			
			$(".online-user").html( _online.join("") );
			
			if ( $("#name").val() !== "" ) {
				if ( $.inArray( $("#name").val(), _users ) === -1 ) {
					logout();
					return;
				}
			}
			timestamp = response.timestamp;
			setTimeout(get_online(), 1000);
		},
		error: function( XMLHttpRequest, textStatus, errorThrown ) {
			console.log( "error: " + textStatus + " (" + errorThrown + ")" );
			setTimeout(get_online(), 15000);
		}
	});
}

function logout() {
	$.ajax({
		url: "getData.php",
		type: "GET",
		dataType: "json",
		async: true,
		cache: false,
		data: {
			'action'	: 'logout'
		},
		success: function( response ) {
			window.location.reload();
		}
	});
}

function reset( sess_user ) {
	$('#myModal').modal('hide');
	$("#nickname").val("");
	$("#name").addClass("hide").val( sess_user );
	$("#current_user").text( sess_user );
	$("#message").attr("id", "content");
	waitMsg( true, sess_user );
}


function change_status( status, text ) {
	$.ajax({
		url: "getData.php",
		type: "POST",
		dataType: "json",
		data: {
			'status' : status,
			'action' : 'change_status'
		},
		success: function( response ) {
			if ( !response.error ) {
				var online_class = "";
				switch( status ) {
					case "online" :
						online_class = " label-success";
						break;
					case "away" :
						online_class = " label-warning";
						break;
					case "busy" :
						online_class = " label-important";
						break;
					case "invisible" :
					case "offline" :
						online_class = " ";
						break;
					default:
						online_class = " label-success";
						break;
				}
				$(".online-user p").each(function() {
					if ( $(this).text() === $("#name").val() ) {
						$(this).removeAttr("class").addClass("label" + online_class + " btn-block");
					}
				});
				$("#status").text( text );
			}
		}
	});
}

function isTouchDevice(){
    try {
        document.createEvent("TouchEvent");
        return true;
    } catch(e) {
        return false;
    }
}

function touchScroll(id){
    if ( isTouchDevice() ) { //if touch events exist...
        var el=document.getElementById(id);
        var scrollStartPos=0;
         
        document.getElementById(id).addEventListener("touchstart", function(event) {
            scrollStartPos = this.scrollTop + event.touches[0].pageY;
            event.preventDefault();
        },false);
         
        document.getElementById(id).addEventListener("touchmove", function(event) {
            this.scrollTop=scrollStartPos-event.touches[0].pageY;
            event.preventDefault();
        },false);
    }
}