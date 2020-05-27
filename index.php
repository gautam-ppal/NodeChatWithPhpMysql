<DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js"></script>

<script src="js/socket.io.js" ></script>
<script src="js/jquery.min.js" ></script>

</head>
<body>
<div class="jumbotron text-center">
  <h1>My First Bootstrap Page</h1>
  <p>Resize this responsive page to see the effect!</p> 
</div>

    <div class="container">
        <form class="form" onsubmit="return EnterUserName()" >
            <div class="form-group text-center">
            <input type="text" name="username" id="username" class="form-control" />
            <input type="submit" name="Save" value="Save" />
            </div> 
        </form>
        <div class="user-list">
        <ul  id="users">

        </ul>
        </div>
        <div class="chat-box container">
          <div class="jumbotron" >
            <ul id="ChatList" >

            </ul>
          </div>
          <form class="" onsubmit="return SendMessageFn();" >
            <input type="text" name="MessageBox" id="MessageBox"  class="form-control" placeholder="Type Any Message..."/>
            <input type="submit" name="SendMessage" id="SendMessage" value="Send" /> 
          </form>
        </div>
    </div> 
    <script>
        var io = io("http://localhost:3000");
        var receiver = '';
        var sender = '';
      function EnterUserName()
      {
        var name = $('#username').val();
        // alert(name);
        io.emit('user_connected', name);
        sender = name;
        return false;
      }

      // listen form server
      io.on('user_connected',  function(username){
        //console.log(username)
        var html = '';
        html    += "<li><button onclick='SelectUser(this.innerHTML)'>" +username+ "</button></li>";
        
        // $('#users').append(html);
        document.getElementById('users').innerHTML += html;
      });

      function SelectUser(username)
      {
        // console.log(username)
        receiver = username;
        //call un ajax
        $.ajax({
          url: "http://localhost:3000/get_messages",
          method: "POST",
          data: {
            sender: sender,
            receiver: receiver 
          },
          success: function(response){
            //console.log(response);
            var messages = JSON.parse(response);
            var html = '';
            for(var a=0;a<messages.length;a++){
              html += "<li class='sender'>"+messages[a].sender+" Says : "+ messages[a].message + "</li>";
            }
            $('#ChatList').append(html);

          }
        });
      }

      function SendMessageFn()
      {
        var msg = $('#MessageBox').val(); 
      
        //send message on server
        io.emit("send_message",{
          sender: sender,
          receiver: receiver,
          message: msg
        });

        var html = "";
        html += "<li class='sender'>You  Said: "+ msg + "</li>";
        $('#ChatList').append(html);

        return false;

      }

      io.on("new_message",function(data){
        var html = "";
        html += "<li class='sender'>"+data.sender + " Says: "+data.message + "</li>";
        $('#ChatList').append(html);
      });
    </script>
</body>
</html>
