
<h3>Email login</h3>
<form action="index.php?action=email_login" method="post">
   
    email : <input type="text" name="email"/><br>
    password : <input type="text" name="pass"/><br>
     Longitude : <input type="text" name="long"/><br>
    Latitude : <input type="text" name="lat"/><br>
     device token : <input type="text" name="device_token"/><br>
     app version : <input type="text" name="app_version"/><br>
    <input type="submit" value="submit" />
</form>

<hr />
<h3>Fb login</h3>
<form action="index.php?action=fblogin" method="post">
    Fb Id : <input type="text" name="fb_id"/><br>
    Fb Name : <input type="text" name="fbname"/><br>
    Longitude : <input type="text" name="long"/><br>
    Latitude : <input type="text" name="lat"/><br>
    device token : <input type="text" name="device_token"/><br>
    app version : <input type="text" name="app_version"/><br>
    fb access token : <input type="text" name="fb_access_token"/><br>
    fb_email : <input type="text" name="fb_email"/><br>
    <input type="submit" value="submit" />
</form>

<hr />

<h3>login</h3>
<form action="index.php?action=login" method="post">
    
    access token : <input type="text" name="access_token"/><br>
     Longitude : <input type="text" name="long"/><br>
    Latitude : <input type="text" name="lat"/><br>
    device token : <input type="text" name="device_token"/><br>
    action: <input type="text" name="action"/><br>
    app version : <input type="text" name="app_version"/><br>
    <input type="submit" value="submit" />
</form>

<hr />

<h3>fb_friends</h3>
<form action="index.php?action=fb_friends" method="post">
    
    access token : <input type="text" name="access_token"/><br>
   
    fb_access token : <input type="text" name="fb_access_token"/><br>
    fb_id : <input type="text" name="fb_id"/><br>
    <input type="submit" value="submit" />
</form>
<hr />


<hr />

<h3>fb_friends</h3>
<form action="index.php?action=fb_friends" method="post">
    
    access token : <input type="text" name="access_token"/><br>
   
    fb_access token : <input type="text" name="fb_access_token"/><br>
    fb_id : <input type="text" name="fb_id"/><br>
    <input type="submit" value="submit" />
</form>
<hr />


<h3>near by dj</h3>
<form action="index.php?action=near_dj" method="post">
    
    access_token : <input type="text" name="access_token"/><br>
    latitude : <input type="text" name="lat"/><br>
   longitude: <input type="text" name="lon"/><br>
   
    <input type="submit" value="submit" />
</form>
<hr />

<hr />


<h3>dj playlist of user </h3>
<form action="index.php?action=get_djplaylist" method="post">
    
    access_token : <input type="text" name="access_token"/><br>
   
   
    <input type="submit" value="submit" />
</form>

<hr />


<h3>delete from playlist </h3>
<form action="index.php?action=del_fromdjplaylist" method="post">
    
    access_token : <input type="text" name="access_token"/><br>
    song id : <input type="text" name="song_id"/><br>
   
   
    <input type="submit" value="submit" />
</form>


<hr />



<hr />


<h3>Add to Playlist</h3>
<form action="index.php?action=add_to_playlist" method="post" enctype="multipart/form-data">
    
    access token : <input type="text" name="access_token"/><br>
    song_name : <input type="text" name="song_name"/><br>
    song_artist: <input type="text" name="song_artist"/><br>
    song_image : <input type="file" name="file"/><br>
    song_image_url : <input type="text" name="song_image_url"/><br>
    song_mp3 : <input type="file" name="mp3_file"/><br>
    song_link : <input type="text" name="song_link"/><br>
    song_itunes_link : <input type="text" name="song_itunes_link"/><br>
    song_status : <input type="text" name="song_status"/><br>
    
   
   
    <input type="submit" value="submit" />
</form>


<hr />


<h3>Share DJ Playlist</h3>
<form action="index.php?action=share_djplaylist" method="post">
    access token : <input type="text" name="access_token"/><br>
    fb_id: <input type="text" name="fb_id"/><br>
    Fb Name : <input type="text" name="fbname"/><br>
    user_email: <input type="text" name="user_email"/><br>
    
       invite_by: <input type="text" name="invite_by"/><br>
   
    
    <input type="submit" value="submit" />
</form>
<hr />


<h3>forgot password</h3>
<form action="index.php?action=forgot_password" method="post">
    
    email: <input type="text" name="email"/><br>
   
    
   
   
    <input type="submit" value="submit" />
</form>

<hr />

<h3>shared friends list</h3>
<form action="index.php?action=friends_sharedwith" method="post">
    
    access token : <input type="text" name="access_token"/><br>
    mode : <input type="text" name="mode"/><br>
   
    
   
   
    <input type="submit" value="submit" />
</form>

<hr />
<h3>Accept request</h3>
<form action="index.php?action=accept_request" method="post">
    
    access token : <input type="text" name="access_token"/><br>
    dj_id : <input type="text" name="dj_id"/><br>
   
    <input type="submit" value="submit" />
</form>

<hr />
<h3>Logout</h3>
<form action="index.php?action=logout" method="post">
    
    access token : <input type="text" name="access_token"/><br>
    
    <input type="submit" value="submit" />
</form>
<hr />
<h3>Clear List</h3>
<form action="index.php?action=clear_playlist" method="post">
    
    access token : <input type="text" name="access_token"/><br>
    
    <input type="submit" value="submit" />
</form>
<hr />
<h3>Previous Session dj ids and other info</h3>
<form action="index.php?action=previous_session" method="post">
    
    access token : <input type="text" name="access_token"/><br>
    
    <input type="submit" value="submit" />
</form>


<hr />
<h3>Previous Session dj playlist of a dj</h3>
<form action="index.php?action=get_previous_session_djplaylist" method="post">
    
    access token : <input type="text" name="access_token"/><br>
    dj id : <input type="text" name="user_id"/><br>
    session id  : <input type="text" name="session_id"/><br>
    
    <input type="submit" value="submit" />
</form>




<hr />

<h3>Connections</h3>
<form action="index.php?action=connection" method="post">
    
    access token : <input type="text" name="access_token"/><br>
    dj_id : <input type="text" name="dj_id"/><br>
    <input type="submit" value="submit" />
</form>




<hr />

<h3>Shuffle playlist</h3>
<form action="index.php?action=shuffle_playlist" method="post">
    
    access token : <input type="text" name="access_token"/><br>
    order : <input type="text" name="order"/><br>
    <input type="submit" value="submit" />
</form>  




<hr />

<h3>sound cloud</h3>
<form action="index.php?action=soundcloud" method="post">
    
    URL: <input type="text" name="url"/><br>
   
    
   
   
    <input type="submit" value="submit" />
</form>
<hr />



<h3>pub nub data</h3>
<form action="index.php?action=pubnub_data" method="post">
    
    NTP Date: <input type="text" name="ntp_date"/><br>
   access token : <input type="text" name="access_token"/><br>
    song_status : <input type="text" name="song_status"/><br>
    selected index: <input type="text" name="selected_index"/><br>
    message: <input type="text" name="message"/><br>
   selected song : <input type="text" name="selected_song"/><br>
   bit rate: <input type="text" name="bit_rate"/><br>
   npackets : <input type="text" name="npackets"/><br>
    numBytes : <input type="text" name="num_bytes"/><br>
   song_url : <input type="text" name="song_url"/><br>
   dj_id : <input type="text" name="dj_id"/><br>
   audio_byte : <input type="text" name="audio_bytes"/><br>
   data offset : <input type="text" name="data_offset"/><br>
   seek time : <input type="text" name="seek_time"/><br>
   song_file_length : <input type="text" name="song_file_length"/><br>
    flag : <input type="text" name="flag"/><br>
    status : <input type="text" name="status"/><br>
    remain_time : <input type="text" name="remain_time"/><br>
   
   
    <input type="submit" value="submit" />
</form>
<hr />

<h3>Delete Listener</h3>
<form action="index.php?action=delete_listener" method="post">
    
    access token : <input type="text" name="access_token"/><br>
    listener id : <input type="text" name="listener_id"/><br>
    <input type="submit" value="submit" />
</form> 
<hr />
<h3>Sent request to dj</h3>
<form action="index.php?action=request_dj" method="post">
    
    access token : <input type="text" name="access_token"/><br>
    dj id : <input type="text" name="dj_id"/><br>
    <input type="submit" value="submit" />
</form> 
<hr />
<h3>Accept request of listener</h3>
<form action="index.php?action=accept_listener_request" method="post">
    
    access token : <input type="text" name="access_token"/><br>
    listener id : <input type="text" name="listener_id"/><br>
    <input type="submit" value="submit" />
</form> 
<hr />
<h3>Edit user profile</h3>
<form action="index.php?action=edit_profile" method="post" enctype="multipart/form-data">
    
    access token : <input type="text" name="access_token"/><br>
    image <input type="file" name="pic"/><br>
    <input type="submit" value="submit" />
</form> 

<hr />
<h3>update to premium</h3>
<form action="index.php?action=update_to_premium" method="post" >
    
    access token : <input type="text" name="access_token"/><br>
    
    <input type="submit" value="submit" />
</form>

<hr />
<h3>Disconnect from dj</h3>
<form action="index.php?action=disconnect_from_dj" method="post" >
    
    access token : <input type="text" name="access_token"/><br>
    Dj id : <input type="text" name="dj_id"/><br>
    
    <input type="submit" value="submit" />
</form>

<hr />
<h3>Sound Cloud add playlist</h3>
<form action="index.php?action=playlist_add" method="post" >
    
    access token : <input type="text" name="access_token"/><br>
    name:<input type="text" name="name"/><br>
   
    
    <input type="submit" value="submit" />
</form>

<hr />
<h3>Search sound cloud for tracks</h3>
<form action="index.php?action=track_search" method="post" >
    
   Search : <input type="text" name="search_term"/><br>
   
    
    <input type="submit" value="submit" />
</form>

<hr />


<h3>ADD track in SoundCloud PlayList</h3>
<form action="index.php?action=add_track" method="post" >
    
   access token : <input type="text" name="access_token"/><br> 
   Playlist : <input type="text" name="playlist_id"/><br>
   track id : <input type="text" name="track_id"/><br>
    
    <input type="submit" value="submit" />
</form>
<hr />

<h3>Sound Cloud delete playlist</h3>
<form action="index.php?action=delete_playlist" method="post" >
    
    access token : <input type="text" name="access_token"/><br>
    ID:<input type="text" name="playlist_id"/><br>
   
    
    <input type="submit" value="submit" />
</form>
<hr />

<h3>Delete track in SoundCloud PlayList</h3>
<form action="index.php?action=delete_track" method="post" >
    
   access token : <input type="text" name="access_token"/><br> 
   Playlist : <input type="text" name="playlist_id"/><br>
   track id : <input type="text" name="track_id"/><br>
    
    <input type="submit" value="submit" />
</form>
<hr />


<h3>change premium</h3>
<form action="index.php?action=change_premium" method="post" >
    
   email : <input type="text" name="email"/><br> 
   flag : <input type="text" name="flag"/><br>
   
    <input type="submit" value="submit" />
</form>

<hr />
<h3>show premium users</h3>
<form action="index.php?action=premium_users" method="post" >
   
    <input type="submit" value="submit" />
</form>

<hr />
<h3>play bot playlist</h3>
<form action="index.php?action=play_bot_playlist" method="post" >
   Bot_id : <input type="text" name="bot_id"/><br> 
    <input type="submit" value="submit" />
</form>


