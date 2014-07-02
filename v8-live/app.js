
/**
 * Module dependencies.
 */
 
var express = require('express'),
  request = require('request'),
  jsdom = require('jsdom'),
  http = require('http'),
  path = require('path');


  

var userlogin =require('./routes/userlogin');
var playlist =require('./routes/playlist');

mysqlLib = require('./routes/conn');

var app = express();



userImageBaseUrl='http://boom-botix.s3.amazonaws.com/user_profile/';
userChatBaseUrl='http://boom-botix.s3.amazonaws.com/user_chat/';
popupStatus = 0;

app.configure(function(){
  app.set('port', process.env.PORT || 1336);
  app.set('views', __dirname + '/views');
  app.set('view engine', 'jade');
  app.use(express.favicon());
  app.use(express.logger('dev'));
  app.use(express.bodyParser());
  app.use(express.methodOverride());
  app.use(app.router);
  app.use(express.static(path.join(__dirname, 'public')));
});



app.configure('development', function(){
  app.use(express.errorHandler());
});

app.get('/test', function(req, res){
  res.render('test');
});

app.post('/fblogin', userlogin.getFbUserDataFromFbid);
app.post('/login', userlogin.getuserDataFromAccessToken);
app.post('/email_login', userlogin.getEmailUserDataFromEmailAndPassword);
app.post('/fb_friends', userlogin.getFbFriendsFromFbIdAndFbAccessToken);
app.post('/forgot_password', userlogin.forgotPasswordFromEmail);
app.post('/near_dj', userlogin.getNearByDjFromUserId);
app.post('/share_djplaylist', userlogin.shareDjPlayListFromUserIdToFriendId);
app.post('/friends_sharedwith', userlogin.getFriendsSharedWithFromUserId);
app.post('/accept_request', userlogin.acceptRequestFromUserId);
app.post('/logout', userlogin.logoutFromUserAccessToken);
app.post('/clear_playlist', userlogin.clearPlaylistFromUserAccessToken);
app.post('/previous_session', userlogin.getPreviousSessionFromUserId);
app.post('/connection', userlogin.getconnectionFromUserId);
app.post('/request_dj', userlogin.requestDjFromUserIdAndDjId);
app.post('/accept_listener_request', userlogin.acceptRequestFromDjId);
app.post('/edit_profile', userlogin.editProfileFromAccessToken);
app.post('/update_to_premium', userlogin.updateUserToPremiumFromAccessToken);

app.post('/add_to_playlist', playlist.addPlayListSongFromUserIdSessionIdChannelId);
app.post('/get_djplaylist', playlist.getDjPlayListFromUserAccessToken);
app.post('/del_fromdjplaylist', playlist.deleteUserPlaylistSongFromUserAccessTokenAndSongId);
app.post('/get_previous_session_djplaylist', playlist.getPreviousSessionDjPlayListFromUserIdAndSessionId);
app.post('/shuffle_playlist', playlist.setPlaylistOrderForUser);
app.post('/soundcloud', playlist.getLocation);
app.post('/delete_listener', playlist.deleteListenerFromAccessTokenAndListenerId);
app.post('/disconnect_from_dj', playlist.disconnectDjFromAccessTokenAndDjId);
app.post('/pubnub_data', playlist.getPubnubDataFromDjId);
app.post('/insert_chat', playlist.getChatDataFromDjId);
app.post('/playlist_data', playlist.getPlaylistDataFromDjId);
app.get('/delete_bad_songs', playlist.deleteBadSongs);


http.createServer(app).listen(app.get('port'), function(){

  console.log("Express server listening on port " + app.get('port'));
});

