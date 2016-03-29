
<!DOCTYPE html>
<html>
<head>
<script src="http://apps.bdimg.com/libs/jquery/2.1.4/jquery.min.js"></script>
<script src="http://apps.bdimg.com/libs/jquery.cookie/1.4.1/jquery.cookie.js"></script>

<meta charset=utf-8 />
<style type="text/css">
</style>
<link rel="stylesheet" href="style.css">
<title>WebSockets Client</title>

</head>
<body>
  <div id="wrapper">

  	<div id="container">
		<div class="main">
    	<h1>WebSockets Client</h1>

        <div id="chatLog" class="chatPane">
			<a href="javascript:;" class="clear_scr">清屏</a>

        </div>
        <p id="examples">e.g. try 'hi', 'name', 'age', 'today'</p>

		<form id="chat_form" >
			<input id="chat_box" type="text" />
			<button id="sd_chat" type="submit">发送</button><br/>
		</form>
		<br/><br/>
		<form id="rename_form" >
			<input id="text" type="text" />
			<button id="sd" type="submit">改名</button>
		</form>
        <button id="disconnect">断开</button>
		<button id="reconnect" disabled>重新连接</button>
			<div id="console_area" class="pane">
				<a href="javascript:;" class="clear_scr">清除</a>
			</div>
		</div>
		<div id="userList" class="roll_right list">
			<ul>用户列表
			</ul>
		</div>
	</div>
  </div>
<script type="text/javascript">
    $(function() {
        $(".clear_scr").on("click",function(){
            $(this).find("~").remove();
        });

        $("#text").focus();
    });
</script>
<script type="text/javascript">
var socket;
$(document).ready(function() {
    webSocketConnect();
    $("#rename_form").submit(function(e){
        e.preventDefault();
        send( $('#text').val() );
        var date = new Date();
        date.setTime(date.getTime() + ( 30 * 60 * 1000));
        $.cookie("name",$("#text").val(),{ expires: date });
		return false;
	});

    $("#chat_form").submit(function(e){
        e.preventDefault();
        sendMessage( $('#chat_box').val() );
        $('#chat_box').val("");
    });

    function webSocketConnect(){
        if(!("WebSocket" in window)){
            $('#chatLog, input, button, #examples').fadeOut("fast");
            $('<p>Oh no, you need a browser that supports WebSockets. How about <a href="http://www.google.com/chrome">Google Chrome</a>?</p>').appendTo('#container');
        }else{
            //The user has WebSockets
            connect();
        }//End connect()
	}
    function initUser(socket){
        var old_name = $.cookie('name') ;
        if(old_name){
            $("#text").val( $.cookie('name') );
            socket.send('type=add&ming='+$.cookie('name'));
            console.log(20 );
        }else{
            var name ;
            if( $("#text").val().length >0 ){
                name = $("#text").val();
            }else{
                name = defaultName() ;
            }
            socket.send('type=add&ming='+ name );
            $("#text").val( name );
        }
    }
    function connect(){
        var host = "ws://120.25.195.76:8000";
        try{
            socket = new WebSocket(host);
            operateLog('Socket Status: '+socket.readyState,'event');
            socket.onopen = function(){
                operateLog('Socket Status: '+socket.readyState+' (open)' ,'event');
                $("#disconnect").attr("disabled",false);
                $("#reconnect").attr("disabled",true);
                initUser(socket);
            }

			socket.onmessage = function(msg){
                console.log(msg.data);
                parseMessage(msg);
            }

			socket.onclose = function(){
                console.log("关闭ing");
                operateLog('Socket Status: '+socket.readyState+' (Closed)' ,'event');
                $("#reconnect").attr("disabled",false);
                $("#disconnect").attr("disabled",true);
                $("#userList li").addClass("disconnected").removeClass("connected");
            }
		} catch(exception){
            message(exception,"error");
        }

        $('#disconnect').on("click",function(){
            socket.close();
        });

        $('#reconnect').on("click",function(){
            webSocketConnect();
        });
    }
});
function send(text){
    if(text==""){
        //message('Please enter a message','warning');
        return ;
    }
    try{
        socket.send('type=add&ming='+text);
        operateLog('Sent: '+text,'event')
	} catch(exception){
        operateLog(' 消息未发送成功 ','warning!');
    }
}
function sendMessage(text){
    if(text==""){
        return ;
    }
    try{
        socket.send('type=chat&content='+text );
        operateLog('Sent: '+text ,"normal");
    } catch(exception){
        operateLog(' 消息未发送成功 ','warning');
    }
}
function operateLog(text,type){
    var $aa = $("<p></p>").addClass(type).text(text);
    $('#console_area').append( $aa );
}
function defaultName(){
    var str = new Date().getTime() + "";
    str = "游客" + str.slice(-5);
    return str;
}
function parseMessage(msg){
    var jdata = jQuery.parseJSON(msg.data);
    if(jdata.add){
        addToUserList(jdata.users);
        message(jdata.nrong,"event");
        return ;
    }
    if(jdata.remove){
        var rid=jdata.removekey ;
        $("#userList li#" + rid).css({"color":"grey"});
		message(jdata.nrong,'warning');
		return ;
	}
    if(jdata.chat){
        message(jdata.username + ":",'title');
        message(jdata.nrong,'chat');
        return ;
    }
}
	function message(msg , style_class){
        var $aa = $("<p></p>").addClass(style_class).text(msg);
        $('#chatLog').append( $aa );
        var div = $('#chatLog')[0];
        div.scrollTop = div.scrollHeight;
    }//End message()
//包括从其他会话中传递进来的消息
function addToUserList( arr ){
    if(!arr) return ;
    $.each(arr,function(k,v){
        var $oldLi = $("#userList ul li#"+k);
        if($oldLi.length == 0){
            var $li = $("<li>"+v + "</li>");
            $li.attr("id",k).addClass("connected");
            $("#userList ul").append($li);
        }else{
            $oldLi.text(v);
            $oldLi.addClass("connected").removeClass("disconnected");
        }
    });
}
</script>
</body>
</html>​
