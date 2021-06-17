<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <!--  This file has been downloaded from bootdey.com    @bootdey on twitter -->
    <!--  All snippets are MIT license http://bootdey.com/license -->
    <title>Chat Application</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="<?php echo base_url() ?>assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="<?php echo base_url() ?>assets/css/font-awesome.min.css" rel="stylesheet">

    <style type="text/css">
    	body {
            padding-top: 0;
            font-size: 12px;
            color: #777;
            background: #f9f9f9;
            font-family: 'Open Sans',sans-serif;
            margin-top:20px;
        }

        .bg-white {
            background-color: #fff;
        }

        .friend-list {
            list-style: none;
            margin-left: -40px;
            margin-top: 20px;
            font-size: 15px;
        }

        .friend-list li {
            border-bottom: 1px solid #eee;
            cursor: pointer;
        }

        .friend-list li a img {
            float: left;
            width: 45px;
            height: 45px;
            margin-right: 10px;
        }

        .friend-list li a {
            position: relative;
            display: block;
            padding: 12px;
            transition: all .2s ease;
            -webkit-transition: all .2s ease;
            -moz-transition: all .2s ease;
            -ms-transition: all .2s ease;
            -o-transition: all .2s ease;
        }

        .friend-list li.active a {
            background-color: #f1f5fc;
        }

        .friend-list li a .friend-name, 
        .friend-list li a .friend-name:hover {
            color: #777;
        }

        .friend-list li a .last-message {
            width: 65%;
            white-space: nowrap;
            text-overflow: ellipsis;
            overflow: hidden;
        }

        .friend-list li a .time {
            position: absolute;
            top: 10px;
            right: 8px;
        }

        small, .small {
            font-size: 85%;
        }

        .friend-list li a .chat-alert {
            position: absolute;
            right: 8px;
            top: 27px;
            font-size: 10px;
            padding: 3px 5px;
        }

        .chat-message {
            padding: 0px 20px 115px;
        }

        .chat {
            list-style: none;
            margin: 0;
        }

        .chat-message {
            background: #f9f9f9;  
        }

        .chat li img {
            width: 45px;
            height: 45px;
            border-radius: 50em;
            -moz-border-radius: 50em;
            -webkit-border-radius: 50em;
        }

        img {
            max-width: 100%;
        }

        .chat-body {
            padding-bottom: 20px;
        }

        .chat li.left .chat-body {
            margin-left: 70px;
            background-color: #fff;
        }

        .chat li .chat-body {
            position: relative;
            font-size: 13px;
            padding: 10px 20px;
            border: 1px solid #f1f5fc;
            box-shadow: 0 1px 1px rgba(0,0,0,.05);
            -moz-box-shadow: 0 1px 1px rgba(0,0,0,.05);
            -webkit-box-shadow: 0 1px 1px rgba(0,0,0,.05);
        }

        .chat li .chat-body .header {
            padding-bottom: 5px;
            border-bottom: 1px solid #f1f5fc;
        }

        .chat li .chat-body p {
            margin: 0;
            color: #000 !important;
            word-break: break-all;
        }

        .chat li.left .chat-body:before {
            position: absolute;
            top: 10px;
            left: -8px;
            display: inline-block;
            background: #f9f9f9;
            width: 16px;
            height: 16px;
            border-top: 1px solid #f1f5fc;
            border-left: 1px solid #f1f5fc;
            content: '';
            transform: rotate(-45deg);
            -webkit-transform: rotate(-45deg);
            -moz-transform: rotate(-45deg);
            -ms-transform: rotate(-45deg);
            -o-transform: rotate(-45deg);
        }

        .chat li.right .chat-body:before {
            position: absolute;
            top: 10px;
            right: -8px;
            display: inline-block;
            background: #f9f9f9;
            width: 16px;
            height: 16px;
            border-top: 1px solid #f1f5fc;
            border-right: 1px solid #f1f5fc;
            content: '';
            transform: rotate(45deg);
            -webkit-transform: rotate(45deg);
            -moz-transform: rotate(45deg);
            -ms-transform: rotate(45deg);
            -o-transform: rotate(45deg);
        }

        .chat li {
            margin: 15px 0;
        }

        .chat li.right .chat-body {
            margin-right: 70px;
            background-color: #fff;
        }

        .chat-box {
            /*
            position: fixed;
            bottom: 0;
            left: 444px;
            right: 0;
            */
            padding: 15px;
            border-top: 1px solid #eee;
            transition: all .5s ease;
            -webkit-transition: all .5s ease;
            -moz-transition: all .5s ease;
            -ms-transition: all .5s ease;
            -o-transition: all .5s ease;
        }

        .primary-font {
            color: #3c8dbc;
        }

        a:hover, a:active, a:focus {
            text-decoration: none;
            outline: 0;
        }

        .padding-10 {
            padding: 10px;
            text-align: right;
        }

        .member-header {
            height: 40px;
            font-weight: bold;
            font-size: 18px;
            padding: 6px;
        }

        .mb20{
            margin-bottom: 20px;
        }

        .member-header{
            height: 50px;
            padding: 12px;
        }

        .chat-message{
            min-height: 400px;
            max-height: 400px;
            overflow-x: auto;
            padding: 0px;
        }

        .messageHeader{
            padding: 12px;
            background: #f1f5fc;
        }

        #headerName{
            font-weight: bold;
            font-size: 20px;
        }

        #headerUsers{
            font-size: 16px;
        }

        .userActive{
            background-color: #bcf5bc !important;
        }
    </style>
</head>
<body>

<?php
if (!$this->session->userdata('is_logged')) {
    redirect('home');
}
?>

<div class="container bootstrap snippets">
    <div class="col-md-12 padding-10 mb20">
      <span style="float: left; font-size: 32px;">Welcome <font color="#007bff"><?php echo $this->session->userdata('name'); ?></font></a></span>
      <a class="btn btn-info" href="<?php echo site_url('chat/rooms'); ?>"><i class="fas fa-users-class mr-2"></i> Room Management</a>
      <a class="btn btn-info" href="<?php echo site_url('home/profile'); ?>"><i class="fa fa-user mr-2"></i> Profile</a>
      <a class="btn btn-primary" href="<?php echo site_url('home/logout'); ?>"><i class="fa fa-power-off mr-2"></i> Logout</a>
    </div>

    <div class="row">
        <div class="col-md-4 bg-white ">
            <div class=" row border-bottom padding-sm member-header">
             <i class="fa fa-users mt-1 mr-2"></i> Members
            </div>
            
            <!-- =============================================================== -->
            <!-- member list -->
            <ul class="friend-list">           
            </ul>
    </div>
        
        <!--=========================================================-->
        <!-- selected chat -->
        <div class="col-md-8 bg-white ">
            <input type="hidden" id="connectedUser" value="">
            <input type="hidden" id="connectedRoom" value="">
            <div class="col-md-12 messageHeader">
                <div class="col-md-12" id="headerName"></div>
                <div class="col-md-12" id="headerUsers"></div>
            </div>
            <div class="col-md-12 chat-message">
                <ul class="chat">                 
                </ul>
            </div>
            <div class="chat-box bg-white">
                <div class="input-group">
                    <input class="form-control border no-shadow no-rounded" placeholder="Type your message here" id="message">
                    <span class="input-group-btn">
                        <button class="btn btn-success no-rounded" type="button" id="send">Send  <i class="fa fa-send mt-1 mr-2"></i></button>
                    </span>
                </div><!-- /input-group -->
            </div>            
        </div>        
    </div>
</div>

<script src="<?php echo base_url() ?>assets/js/jquery-3.3.1.slim.min.js"></script>
<script src="<?php echo base_url() ?>assets/js/bootstrap.bundle.min.js"></script>
<script src="<?php echo base_url() ?>assets/js/jquery.timeago.min.js"></script>

<script type="text/javascript">
scrollMsgBottom();

var conn = new WebSocket("ws://localhost:8080?userId=<?php echo $this->session->userdata('email'); ?>");
    
conn.onopen = function(e){
    var defaultUser = "<?php echo isset($activeSesssion['as_current_user'])?$activeSesssion['as_current_user']:''; ?>";
    var defaultGroup = "<?php echo isset($activeSesssion['as_current_room'])?$activeSesssion['as_current_room']:''; ?>";
    initializeChat(defaultUser,defaultGroup);
    console.log('Connection Established.');
}

conn.onmessage = function(e) {
    var data = JSON.parse(e.data);
    
    if ('users' in data){
        updateUsers(data.users);
    }

    if('messages' in data){
        newMessage(data.messages);
    }

    if('chatUsers' in data){
        updateChatUsers(data);
    }

    scrollMsgBottom();
};

$('#send').on('click', function () {
    var connectedUser = $('#connectedUser').val();
    var connectedRoom = $('#connectedRoom').val();
    var msg = $('#message').val();

    if(msg.trim() == '')
        return false;
    myMessage(msg);
    //conn.send(msg);
    if(connectedRoom){
        conn.send(JSON.stringify({command: "groupchat", from:"<?php echo $this->session->userdata('email'); ?>", message: msg, channel: connectedRoom}));
    } else {
        conn.send(JSON.stringify({command: "message", from:"<?php echo $this->session->userdata('email'); ?>", to: connectedUser, message: msg}));
    }
    $('#message').val('');
});

conn.onclose = function (e){
    console.log('Connection Closed');
};

window.addEventListener("unload", function () {
    if(conn.readyState == WebSocket.OPEN)
    conn.close();
});

function initializeChat(user, room){
    $('#connectedUser').val(user);
    $('#connectedRoom').val(room);

    $('.chat').empty();
    conn.send(JSON.stringify({command: "chathistory", from:"<?php echo $this->session->userdata('email'); ?>", to: user, channel: room}));
}

function newMessage(messages){
    var html = '';
    $.each(messages, function(key, msg){
        if(msg.email != '<?php echo $this->session->userdata('email'); ?>'){
            html += '<li class="left clearfix"> <span class="chat-img pull-left"> <img src="'+msg.image+'" alt="User Avatar"> </span><div class="chat-body clearfix" style="background: #b7dcfe;"><div class="header"> <strong class="primary-font">' + msg.author + '</strong> <small class="pull-right text-muted"><i class="fa fa-clock-o"></i> <time class="timeago" datetime="'+msg.time+'" title=""></time></small></div><p>' + msg.message + '</p></div></li>';
        } else {
            html += '<li class="right clearfix"> <span class="chat-img pull-right"> <img src="'+msg.image+'" alt="User Avatar"> </span><div class="chat-body clearfix" style="background: #d2d6de;"><div class="header"> <strong class="primary-font">Me</strong> <small class="pull-right text-muted"><i class="fa fa-clock-o"></i> <time class="timeago" datetime="'+msg.time+'" title=""></time></small></div><p>' + msg.message + '</p></div></li>';
        }
    });
    
    $('.chat').html(html);
    $("time.timeago").timeago();
    scrollMsgBottom();

}

function myMessage(msg){
    var name = "<?php echo $this->session->userdata('name'); ?>";
    var today = new Date();
    var date = today.getFullYear() + '-' + (today.getMonth()+1) + '-' + today.getDate();
    var time = today.getHours() + ":" + today.getMinutes() + ":" + today.getSeconds();
    var date_time = date + ' ' + time;

    var image = "<?php echo base_url()."assets/images/"; ?>user.png";
    <?php if ($this->session->userdata('user_image')) { ?>
        var image = "<?php echo base_url()."uploads/".$this->session->userdata('user_image'); ?>";
    <?php } ?>

    html = '<li class="right clearfix"><span class="chat-img pull-right"><img src="'+image+'" alt="User Avatar"></span><div class="chat-body clearfix" style="background: #d2d6de;"><div class="header"><strong class="primary-font">Me</strong><small class="pull-right text-muted"><i class="fa fa-clock-o"></i> <time class="timeago" datetime="'+date_time+'" title=""></time></small></div><p>'+ msg +'</p></div></li>';
    $('.chat').append(html);
    $("time.timeago").timeago();
    scrollMsgBottom();
}

function updateUsers(users){
    var html = '';
    var myId = "<?php echo $this->session->userdata('email'); ?>";
    
    for (let index = 0; index < users.length; index++) {
        var image = "<?php echo base_url().'assets/images/'; ?>user.png";
        if (users[index].us_image){ 
            var image = "<?php echo base_url().'uploads/'; ?>"+users[index].us_image;
        }

        if (users[index].cn_user) {
            html += '<li class="active bounceInDown"><a id="'+users[index].cn_user_name.replace(" ","_")+'" class="clearfix" onclick="initializeChat(\''+users[index].cn_user+'\',\'\');"><img src="'+image+'" alt="" class="img-circle"><div class="friend-name">	<strong>'+ users[index].cn_user_name +'</strong></div><div class="last-message text-muted">'+ users[index].cn_user +'</div></a></li>';
        } else {
            html += '<li class="active bounceInDown"><a id="'+users[index].cn_user_name.replace(" ","_")+'" class="clearfix" onclick="initializeChat(\''+users[index].cn_user+'\','+users[index].rm_id+');"><img src="'+image+'" alt="" class="img-circle"><div class="friend-name">	<strong>'+ users[index].cn_user_name +'</strong></div><div class="last-message text-muted">'+ users[index].cn_user +'</div></a></li>';
        }
    }

    if(html == ''){
        html = '<p>The Chat Room is Empty</p>';
    }
    
    $('.friend-list').html(html);

}

function updateChatUsers(data){
    var users = "";
    if(data.group){
        $('#connectionType').val(data.type);
        $('#connectedUser').val('');
        $('.friend-list a').removeClass('userActive');
        
        if(data.type == 0){
            $('#connectedRoom').val(data.group_id);
        }

        var userId = data.group.replace(" ","_");
        $('#'+userId).addClass('userActive');
        $('#headerName').text(data.group);

        var users = data.chatUsers;
        $('#headerUsers').text(users.join(", "));

        if(users.length == 1 && data.type == 1){
            $('#connectedUser').val(users[0]);
        }
    }
} 

function scrollMsgBottom(){
    var d = $('.chat-message');
    d.scrollTop(d.prop("scrollHeight"));
}
</script>
</body>
</html>