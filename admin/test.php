<h1>Login</h1>
<form action="index.php?action=login" method="POST">
    Email: <input type="text" name="email" />
    Password : <input type="text" name="password" />
    <input type="submit" value="Login" />
</form>

===================================================

<h1>Get Apps</h1>
<form action="index.php?action=getusers" method="POST">
    Username: <input type="text" name="user_name" />
    <input type="submit" value="Login" />
</form>

<h1>Bots Status</h1>
<form action="index.php?action=bot_status" method="POST">
    Username: <input type="text" name="user_name" />
    <input type="submit" value="Login" />
</form>
<h1>Play bot</h1>
<form action="index.php?action=play_bot_playlist" method="POST">
    bot_id: <input type="text" name="bot_id" />
    <input type="submit" value="Login" />
</form>
<h1>edit username</h1>
<form action="index.php?action=edit_user" method="POST">
    Username: <input type="text" name="user_name" />
    user_id: <input type="text" name="user_id" />
    <input type="submit" value="Login" />
</form>

<h1>stop bot</h1>
<form action="index.php?action=stop_bot" method="POST">
    
    dj_id: <input type="text" name="dj_id" />
    <input type="submit" value="Login" />
</form>