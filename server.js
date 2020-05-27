//create express
var express = require('express');
var app = express();

//creating http instance
var http = require('http').createServer(app);

// create socket io instance
var io   = require('socket.io')(http);

// create  body parser instance (For Accept Post Requests)
var bodyParser = require("body-parser");

//enable URL encoded for POST request

app.use(bodyParser.urlencoded());

//create instance of mysql
var mysql = require('mysql');


//make mysql connection

var connection = mysql.createConnection({
    "host": "localhost",
    "user": "root",
    "password": "",
    "database": "chatbox"
});

// connection
connection.connect(function (error){
    //show any error

});
//enable headers required for post request 
app.use(function (request, result, next){
    result.setHeader("Access-Control-Allow-Origin","*");
    next();
});


// create api to return all message
app.post('/get_messages',function (request, result){
    //get all message from database
    connection.query("SELECT * FROM chat_messages WHERE (sender = '"+request.body.sender+"' AND receiver = '"+request.body.receiver+"') OR (sender = '"+request.body.receiver+"' AND receiver = '"+request.body.sender+"')", function (error,message){
        // response will be in json
        result.end(JSON.stringify(message))
    });
});

var user = [];

io.on('connection',function(socket){
    console.log('User Connected', socket.id);

    // attach incomming listing for user
    socket.on('user_connected', function(username){
        //save in array
        user[username] = socket.id;

        //socket id use to send message to the indivisual person

        // notify all connection clients
        io.emit('user_connected', username);

    });

    //listen from client side
    socket.on('send_message',function(data){
      //console.log(data);
      var socketId = user[data.receiver];
      io.to(socketId).emit("new_message", data);

      //save message into database
      connection.query("INSERT INTO chat_messages (sender, receiver, message) VALUES ('"+data.sender+"','"+data.receiver+"','"+data.message+"')", function(error,result){

      });
    });
});

http.listen(3000,function(){
    console.log('Server is Running On 3000');
});
