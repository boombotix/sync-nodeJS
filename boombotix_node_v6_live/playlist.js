var func = require('./commonfunction');

/*
 * ------------------------------------------------------
 *  To add songs in the DJ playlist of the user
 * ------------------------------------------------------
 */

exports.addPlayListSongFromUserIdSessionIdChannelId = function(req, res) {

    var accessToken = req.body.access_token;
    var songName = req.body.song_name;
    var songArtist = req.body.song_artist;
    var songLink = req.body.song_link;
    var songItunesLink = req.body.song_itunes_link;
    var songImageUrl = req.body.song_image_url;
    var songStatus = req.body.song_status;

    var manValues = [accessToken, songName, songArtist, songLink, songItunesLink, songImageUrl, songStatus];
    var checkData = func.checkBlank(manValues);

    if (checkData == 1)
    {

        var response = {"error": 'Some parameter missing'};
        res.send(JSON.stringify(response));
    }
    else
    {
        func.authenticateUser(accessToken, function(result) {

            if (result == 0)
            {
                var response1 = {"error": 'Invalid access token'};
                res.send(JSON.stringify(response1));
            }
            else {
                var userId = result[0].user_id;

                var flag = 1;
                songName = songName.split('{,}');
                songArtist = songArtist.split('{,}');
                songLink = songLink.split('{,}');
                songItunesLink = songItunesLink.split('{,}');
                songImageUrl = songImageUrl.split('{,}');




                var sql = "SELECT user_id,session_id,song_name FROM tb_user_playlist WHERE user_id=?";
                connection.query(sql, [userId], function(err, responseSessionId) {


                    if (responseSessionId.length > 0) {
                        //if user exist in the table tb_user_playlist,find its session id

                        if (responseSessionId.length >= 1 && responseSessionId[0].song_name == "") {
                            flag = 0;
                        }

                        var sessionId = responseSessionId[0].session_id;
                    } else {

                        var sessionId = generateSessionId();
                    }


                    var songsCount = songName.length;
                    var correctSongs = [];
                    for (var i = 0; i < songsCount; i++)
                    {
                        (function(i) {
                            if (i == 0)
                            {
                                flag = flag;
                            }
                            else
                            {
                                flag = 1;
                            }
                            checkSongs(songLink[i], function(check)
                            {
                                if (check != 0)
                                {
                                    correctSongs.push(songLink[i]);
                                    //}
                                    savingPlaylistInDb(flag, userId, songName[i], songArtist[i], songImageUrl[i], songLink[i], songItunesLink[i], sessionId, songStatus, function(songId)
                                    {
                                        if (i == songsCount - 1)
                                        {
                                            updateUserPlaylistOrderFromUserId(userId, songId);
                                            // array difference1
                                            var errorSongs = [];
                                            errorSongs = songLink.diff(correctSongs);
                                            
                                            if (1==1)
                                            {

                                                response1 = {"log": "Song added to playlist successfully", "session_id": sessionId};
                                                res.send(JSON.stringify(response1));
???
                                            }
                                            
                                            

//                                            if ((errorSongs.length == 0) && (songLink.length == 1))
//                                            {
//
////                                                response1 = {"log": "Song added to playlist successfully", "session_id": sessionId};
////                                                res.send(JSON.stringify(response1));
//???
//                                            }
//                                            else if ((errorSongs.length == 0) && (songLink.length > 1))
//                                            {
//                                                response1 = {"log":?"Multiple?songs?added?to?playlist?successfully", "session_id": sessionId};
//                                                res.send(JSON.stringify(response1));
//                                            }
//                                            else if (errorSongs.length == 1)
//                                            {
//
//                                                var errorSongsStr = errorSongs.toString(",");
//
//                                                response1 = {"log":?"Song?not?added?to?playlist.", "session_id": sessionId, "song_id": errorSongsStr};
//                                                res.send(JSON.stringify(response1));
//                                            }
//                                            else if (errorSongs.length > 1)
//                                            {
//                                                var errorSongsStr = errorSongs.toString(",");
//                                                response1 = {"log":?"Multiple?songs?not?added?to?playlist.", "session_id": sessionId, "song_id": errorSongsStr};
//                                                res.send(JSON.stringify(response1));
//                                            }

                                        }
                                    });
                                }
                            });
                        })(i);
                    }
                });
            }
        });
    }
};

Array.prototype.diff = function(a) {
    return this.filter(function(i) {
        return !(a.indexOf(i) > -1);
    });
};
function checkSongs(songLink, callback)
{
    var sql = "SELECT status FROM tb_songs WHERE song_link=? limit 1";
    connection.query(sql, [songLink], function(err, responseSong) {
        if (responseSong.length == 1)
        {
            return callback(1);
        }
        else
        {
            var url = 'https://api.soundcloud.com/tracks/' + songLink + '/streams?client_id=b45b1aa10f1ac2941910a7f0d10f8e28';
            soundCloudUrl(url, function(link)
            {
                return callback(link)
            });
        }
    });
}
function updateUserPlaylistOrderFromUserId(userId, songId) {

    var sql = "SELECT playlist_order FROM tb_users WHERE user_id=? LIMIT 1";
    connection.query(sql, [userId], function(err, responsePlaylistOrder) {

        var playlistOrder = responsePlaylistOrder[0].playlist_order;


        if (playlistOrder != "") {

            playlistOrder = songId + "," + playlistOrder;


            var sql = "UPDATE tb_users set playlist_order=? Where user_id=? limit 1";
            connection.query(sql, [playlistOrder, userId], function(err, response) {

            });
        }
    });
}
function savingPlaylistInDb(flag, userId, songName, songArtist, songImageUrl, songLink, songItunesLink, sessionId, songStatus, callback)
{

    var playlistCreatedDatetime = new Date();
    if (flag == 1) {

        var sql = "INSERT into tb_user_playlist(user_id,song_name,song_artist,song_image,song_link,song_itunes_link,session_id,playlist_created_datetime,song_status) values(?,?,?,?,?,?,?,?,?)";
        connection.query(sql, [userId, songName, songArtist, songImageUrl, songLink, songItunesLink, sessionId, playlistCreatedDatetime, songStatus], function(err, response) {
            var songId = response.insertId;
            return callback(songId);
        });
    } else {

        var sql = "UPDATE tb_user_playlist SET song_name=?,song_artist=?,song_image=?,song_link=?,song_itunes_link=?,playlist_created_datetime=?,song_status=? where user_id=?";
        connection.query(sql, [songName, songArtist, songImageUrl, songLink, songItunesLink, playlistCreatedDatetime, songStatus, userId], function(err, response) {
        });
        var sql = "SELECT playlist_id FROM tb_user_playlist WHERE user_id=? LIMIT 1";
        connection.query(sql, [userId], function(err, responsePlaylistId) {
            var songId = responsePlaylistId[0].playlist_id;
            return callback(songId);
        });

    }
}

function generateSessionId() {

    var mathjs = require('mathjs');
    math = mathjs();
    var text = "";
    var possible = "123456789";

    for (var i = 0; i < 4; i++)
    {
        text += possible.charAt(math.floor(math.random() * possible.length));
    }

    var curDate = new Date();
    var sessionId = Date.parse(curDate) / 1000;


    sessionId = text + sessionId;
    return sessionId;
}

/*
 * ------------------------------------------------------
 *  To get songs from the DJ playlist of the user by access token
 * ------------------------------------------------------
 */

exports.getDjPlayListFromUserAccessToken = function(req, res) {
    var accessToken = req.body.access_token;

    var manValues = [accessToken];
    var checkData = func.checkBlank(manValues);

    if (checkData == 1)
    {

        var response = {"error": 'Some parameter missing'};
        res.send(JSON.stringify(response));
    }
    else
    {
        func.authenticateUser(accessToken, function(result) {

            if (result == 0)
            {
                var response1 = {"error": 'Invalid access token'};
                res.send(JSON.stringify(response1));
            }
            else {
                var userId = result[0].user_id;


                func.getDjPlayListFromUserId(userId, function(playlist)
                {
                    var resultData = [];
                    resultData.push({"user_Dj_Playlist": playlist});
                    var response = {"data": resultData};
                    res.send(JSON.stringify(response));
                });
            }


        });
    }
};


/*
 * ------------------------------------------------------
 *  To delete song from user playlist current session
 * ------------------------------------------------------
 */

exports.deleteUserPlaylistSongFromUserAccessTokenAndSongId = function(req, res) {
    var accessToken = req.body.access_token;
    var songId = req.body.song_id;


    var manValues = [accessToken, songId];
    var checkData = func.checkBlank(manValues);

    if (checkData == 1)
    {

        var response = {"error": 'Some parameter missing'};
        res.send(JSON.stringify(response));
    }
    else
    {
        func.authenticateUser(accessToken, function(result) {

            if (result == 0)
            {
                var response1 = {"error": 'Invalid access token'};
                res.send(JSON.stringify(response1));
            }
            else {
                var userId = result[0].user_id;

                var sql = "SELECT session_id from tb_user_playlist where user_id=? ";
                connection.query(sql, [userId], function(err, responseDataTotalSong) {


                    var userSessionId = 0;
                    if (responseDataTotalSong.length == 1) {
                        userSessionId = responseDataTotalSong[0].session_id;
                    }

                    var sql = "DELETE FROM tb_user_playlist WHERE user_id=? && playlist_id=? limit 1";
                    connection.query(sql, [userId, songId], function(err, responseDel) {

                        if (userSessionId != 0) {
                            var playlistCreatedDatetime = new Date();

                            var sql = "INSERT into tb_user_playlist(user_id,session_id,playlist_created_datetime) values(?,?,?)";
                            connection.query(sql, [userId, userSessionId, playlistCreatedDatetime], function(err, response) {
                                var response = {"log": 'Song deleted successfully!'};
                                res.send(JSON.stringify(response));
                            });



                        }
                        else
                        {
                            var response = {"log": 'Song deleted successfully!'};
                            res.send(JSON.stringify(response));
                        }
                    });
                });
            }
        });
    }
};

/*
 * ----------------------------------------------------------------------------------------------
 *  Getting the list unique djs(who have shared their playlist and lat-long are not 0.00000)
 * ----------------------------------------------------------------------------------------------
 */
exports.getUniqueDjIdsFromUserId = function(userId, callback)
{

//        $sql = "SELECT distinct(dj_user_id) FROM tb_playlist_share";
//        $bindParams = array();
//
//        $response = $this->_DAL->sqlQuery($sql, $bindParams);
//
//        //print_r($responseResult);
//		foreach($response['data'] as $val)
//        {
//          $userIds[]=$val['dj_user_id'];
//        }
//
//
//
//        $placeholders = rtrim(str_repeat('?, ', count($userIds)), ', ');


    var sql = "SELECT user_id FROM tb_users WHERE longitude!='0.000000' AND latitude!='0.000000'";
    connection.query(sql, function(err, responseResult) {

        if (responseResult.length == 0)
        {
            return callback(0);
        }
        else
        {

            getOnlineDjs(responseResult, userId, function(onlineDj)
            {

                var onlineDjCount = onlineDj.length;
                if (onlineDjCount == 0)
                {
                    return callback(0);
                }
                var responseNearDjIdsWithStatus = [];
                for (var j = 0; j < onlineDjCount; j++)
                {
                    (function(j) {

                        var sql = "SELECT status FROM tb_playlist_share where dj_user_id=? and listner_user_id=? limit 1";
                        connection.query(sql, [onlineDj[j], userId], function(err, responseResultStatus) {

                            if (responseResultStatus.length == 0)
                            {
                                var status = 0;
                            }
                            else
                            {
                                if (responseResultStatus[0].status == 0)
                                {
                                    var status = 1;
                                }
                                else {
                                    var status = responseResultStatus[0].status;
                                }


                            }
                            responseNearDjIdsWithStatus.push({"dj_user_id": onlineDj[j], "share_status": status});
                            if (j == onlineDjCount - 1)
                            {

                                return callback(responseNearDjIdsWithStatus);
                            }
                        });

                    })(j);
                }
            });

        }

    });
};


function getOnlineDjs(responseResult, userId, callback)
{

    var usersCount = responseResult.length;
    var onlineDj = [];
    var users = [];
    for (var j = 0; j < usersCount; j++)
    {
        users[j] = responseResult[j].user_id;
    }
    var userStr = users.toString(",");

    var sql = "SELECT id,dj_id,ntp_date,bit_rate,song_file_length FROM tb_pubnub_data WHERE dj_id IN(" + userStr + ")";
    connection.query(sql, function(err, responseOnlineDj) {

        var responseOnlineDjCount = responseOnlineDj.length;
        for (var i = 0; i < responseOnlineDjCount; i++)
        {
            (function(i) {

                if (responseOnlineDj.length != 0)
                {

                    var moment = require('moment');
                    var curDate = new moment();
                    var songLength = ((responseOnlineDj[i].song_file_length * 8) / (responseOnlineDj[i].bit_rate)); //in seconds
                    var curSongTime = (responseOnlineDj[i].ntp_date).split("  ");


                    var moment = require('moment');
                    var dateFormat = 'YYYY-MM-DD HH:mm:ss';
                    var seconds = songLength;
                    var futureDate = moment(curSongTime[0], "YYYY-MM-DD HH:mm:ss");
                    futureDate = moment(futureDate).add('seconds', seconds).format(dateFormat);

                    var timeFirst = moment(curDate, "YYYY-MM-DD HH:mm:ss");

                    var differenceInSeconds = timeFirst.diff(futureDate, 'seconds');


                    if (differenceInSeconds < 0)
                    {
                        if (responseOnlineDj[i].dj_id != userId)
                        {
                            onlineDj.push(responseOnlineDj[i].dj_id);
                        }
                    }

                }
                if (i == responseOnlineDjCount - 1)
                {
                    return callback(onlineDj);
                }

            })(i);
        }
    });
}

/*
 * ------------------------------------------------------
 *  To set session for the user
 * ------------------------------------------------------
 */

exports.setSessionIdFromUserId = function(userId, callback) {

    var sql = "SELECT session_id FROM tb_user_playlist where user_id=? limit 1";
    connection.query(sql, [userId], function(err, responseSessionId) {

        if (responseSessionId.length == 0) {

            var sessionId = generateSessionId();

            var playlistCreatedDatetime = new Date();

            var sql = "INSERT into tb_user_playlist(user_id,session_id,playlist_created_datetime) values(?,?,?)";
            connection.query(sql, [userId, sessionId, playlistCreatedDatetime], function(err, response) {

                return callback(sessionId);
            });
        }
        else {
            return callback(responseSessionId[0].session_id);
        }
    });
};

/*
 * ----------------------------------------------------------------------
 *  Saving user playlist
 * ----------------------------------------------------------------------
 */
exports.saveUserPlayList = function(session_id, userId, userIdFriend, status, callback) {

    var sql = "SELECT dj_user_id,listner_user_id FROM  tb_playlist_share WHERE dj_user_id=? && listner_user_id=? LIMIT 1";
    connection.query(sql, [userId, userIdFriend], function(err, responseSharedIds) {


        if (responseSharedIds.length != 0) {
            var response = {"log": "Playlist already shared with this user"};
            return callback(response);
        }

        var playlistShareDatetime = new Date();

        var sql = "INSERT into tb_playlist_share(session_id,dj_user_id,listner_user_id,status,share_datetime) values(?,?,?,?,?)";
        connection.query(sql, [session_id, userId, userIdFriend, status, playlistShareDatetime], function(err, response) {

            var response = {"log": "Song playlist shared successfully", "session_id": session_id};

            return callback(response);
        });
    });
};

/*
 * ----------------------------------------------------------------------
 *  Getting the list of user whom with the dj has shared his playlist
 * ----------------------------------------------------------------------
 */
exports.getSharedFriendsIds = function(userId, mode, callback) {
    if (mode == 'dj') {
        var sql = "SELECT listner_user_id,status FROM  tb_playlist_share WHERE dj_user_id=?";
    } else {
        var sql = "SELECT dj_user_id,status FROM  tb_playlist_share WHERE listner_user_id=?";
    }
    connection.query(sql, [userId], function(err, responseSharedIds) {

        if (responseSharedIds.length == 0) {
            return callback(1)
        }
        return callback(responseSharedIds);
    });
};

/*
 * ------------------------------------------------------
 *  To get previous session shared with login user from perticular dj
 * ------------------------------------------------------
 */

exports.getPreviousSessionDjPlayListFromUserIdAndSessionId = function(req, res) {


    var accessToken = req.body.access_token;
    var userId = req.body.user_id;
    var sessionId = req.body.session_id;

    var manValues = [accessToken, userId, sessionId];

    var checkData = func.checkBlank(manValues);

    if (checkData == 1)
    {
        var response = {"error": 'Some parameter missing'};
        res.send(JSON.stringify(response));
    }
    else
    {

        func.authenticateUser(accessToken, function(result) {

            if (result == 0)
            {
                var response1 = {"error": 'Invalid access token'};
                res.send(JSON.stringify(response1));
            }
            else {


                var sql = "SELECT song_name,song_artist,song_image,song_link,song_itunes_link,session_id,song_status FROM tb_user_playlist_archive WHERE user_id=? && session_id=?";
                connection.query(sql, [userId, sessionId], function(err, responseDj) {

                    if (responseDj.length == 0) {
                        var response = [];
                        res.send(JSON.stringify(response));
                    }
                    else
                    {
                        var response1 = {"data": responseDj};
                        res.send(JSON.stringify(response1));
                    }
                });

            }
        });
    }
};

/*
 * ------------------------------------------------------
 *  Userd for shuffling the playlist of user
 * ------------------------------------------------------
 */
exports.setPlaylistOrderForUser = function(req, res) {
    var accessToken = req.body.access_token;
    var shuffle_order = req.body.order;



    var manValues = [accessToken, shuffle_order];

    var checkData = func.checkBlank(manValues);

    if (checkData == 1)
    {
        var response = {"error": 'Some parameter missing'};
        res.send(JSON.stringify(response));
    }
    else
    {

        func.authenticateUser(accessToken, function(result) {

            if (result == 0)
            {
                var response1 = {"error": 'Invalid access token'};
                res.send(JSON.stringify(response1));
            }
            else {

                var userId = result[0].user_id;

                var sql = "UPDATE tb_users set playlist_order=? Where user_id=? limit 1";
                connection.query(sql, [shuffle_order, userId], function(err, response) {

                    var response1 = {"log": 'Shuffle Order Updated Successfully!'};
                    res.send(JSON.stringify(response1));

                });

            }
        });
    }
};
exports.getLocation = function(req, res) {

    var url = req.body.url;

    //soundCloudUrl(url,function(link)
    // {

    var response1 = {"url": url};
    res.send(JSON.stringify(response1));

    //  });


};

function soundCloudUrl(url, callback)
{
    var util = require('util');
    var exec = require('child_process').exec;

//var command = 'curl -sL -w "%{http_code} %{time_total}\\n" "http://query7.com" -o /dev/null'

    var command = 'curl -i "%{http_code} %{time_total}\\n" "' + url + '" -o /dev/null';

    child = exec(command, function(error, stdout, stderr) {


        if (error !== null)
        {

            return callback(0);
        }
        else
        {

            var output = stdout.match("HTTP/1.1 200 OK");

            if (output == null)
            {
                return callback(1);
            }
            else
            {

                return callback(0);

            }
        }

    });
}


/*
 * ------------------------------------------------------
 *  Deleting listener from dj's listening list
 * ------------------------------------------------------
 */
exports.deleteListenerFromAccessTokenAndListenerId = function(req, res) {


    var accessToken = req.body.access_token;
    var listenerId = req.body.listener_id;


    var manValues = [accessToken, listenerId];
    var checkData = func.checkBlank(manValues);

    if (checkData == 1)
    {

        var response = {"error": 'Some parameter missing'};
        res.send(JSON.stringify(response));
    }
    else
    {
        func.authenticateUser(accessToken, function(result) {

            if (result == 0)
            {
                var response1 = {"error": 'Invalid access token'};
                res.send(JSON.stringify(response1));
            }
            else
            {

                var userId = result[0].user_id;

                var sql = "SELECT * from tb_playlist_share where dj_user_id=? AND listner_user_id=? LIMIT 1";
                connection.query(sql, [userId, listenerId], function(err, row) {

                    var curDate = new Date();
                    var sql = "INSERT into tb_playlist_share_archive values(?,?,?,?,?)";
                    connection.query(sql, [row[0].session_id, row[0].dj_user_id, row[0].listner_user_id, row[0].status, curDate], function(err, response) {


                        var sql = "DELETE from tb_playlist_share where dj_user_id=? AND listner_user_id=? LIMIT 1";
                        connection.query(sql, [userId, listenerId], function(err, responseDelete) {

                            var response1 = {"log": 'Listener Deleted Successfully!'};
                            res.send(JSON.stringify(response1));
                        });
                    });
                });


            }
        });
    }
};
/*
 * ------------------------------------------------------
 *  Deleting listener from dj's listening list
 * ------------------------------------------------------
 */
exports.disconnectDjFromAccessTokenAndDjId = function(req, res) {


    var accessToken = req.body.access_token;
    var djId = req.body.dj_id;


    var manValues = [accessToken, djId];
    var checkData = func.checkBlank(manValues);

    if (checkData == 1)
    {

        var response = {"error": 'Some parameter missing'};
        res.send(JSON.stringify(response));
    }
    else
    {
        func.authenticateUser(accessToken, function(result) {

            if (result == 0)
            {
                var response1 = {"error": 'Invalid access token'};
                res.send(JSON.stringify(response1));
            }
            else
            {

                var userId = result[0].user_id;

                var sql = "UPDATE tb_playlist_share set status=? where dj_user_id=? AND listner_user_id=? LIMIT 1";
                connection.query(sql, [0, djId, userId], function(err, responseUser) {

                    var response1 = {"log": 'Dj Disconnected Successfully!'};
                    res.send(JSON.stringify(response1));
                });

            }
        });
    }
};

/*
 * ---------------------------------------------
 *  Storing pubnub data into db and fetching it
 * ---------------------------------------------
 */
exports.getPubnubDataFromDjId = function(req, res) {


    var accessToken = req.body.access_token;
    var djId = req.body.dj_id;
    var flag = req.body.flag; //0 means inserting, 1 means updating, 2 means fetching, 3 means delete
    var songStatus = req.body.song_status;
    var selectedIndex = req.body.selected_index;
    var selectedSong = req.body.selected_song;
    var ntpDate = req.body.ntp_date;
    var message = req.body.message;
    var bitRate = req.body.bit_rate;
    var npackets = req.body.npackets;
    var numBytes = req.body.num_bytes;
    var songUrl = req.body.song_url;
    var audioBytes = req.body.audio_bytes;
    var dataOffset = req.body.data_offset;
    var songFilelength = req.body.song_file_length;
    var status = req.body.status;
    var remainTime = req.body.remain_time;


    var manValues = [accessToken, flag];
    var checkData = func.checkBlank(manValues);

    if (checkData == 1)
    {

        var response = {"error": 'Some parameter missing'};
        res.send(JSON.stringify(response));
    }
    else
    {
        func.authenticateUser(accessToken, function(result) {

            if (result == 0)
            {
                var response1 = {"error": 'Invalid access token'};
                res.send(JSON.stringify(response1));
            }
            else
            {


                if (flag == 0)
                {

                    var sql2 = "SELECT id from tb_pubnub_data  Where dj_id=? limit 1";
                    connection.query(sql2, [djId], function(err, response2) {


                        if (response2.length == 1)
                        {
                            var sql = "UPDATE tb_pubnub_data set ntp_date=?,song_status=?,selected_index=?,selected_song=?,song_url=?,message=? Where id=? limit 1";
                            connection.query(sql, [ntpDate, songStatus, selectedIndex, selectedSong, songUrl, message, response2[0].id], function(err, response) {
                            });
                        }
                        else
                        {
                            var sql = "INSERT INTO `tb_pubnub_data`( `ntp_date`, `song_status`, `selected_index`, `selected_song`, `song_url`,`message`, `dj_id`) VALUES(?,?,?,?,?,?,?)";
                            connection.query(sql, [ntpDate, songStatus, selectedIndex, selectedSong, songUrl, message, djId], function(err, response) {
                            });
                        }
                        //$songUrl= 'https://api.soundcloud.com/tracks/93321366/stream?client_id=b45b1aa10f1ac2941910a7f0d10f8e28';
                        var songLink = songUrl.split("/");
                        songLink = songLink[4];
                        //echo $songLink;
                        var sql2 = "SELECT id from tb_bot_pubnub  Where song_link=? limit 1";
                        connection.query(sql2, [songLink], function(err, responseSong) {

                            if (responseSong.length == 0)
                            {
                                var sql = "INSERT INTO `tb_bot_pubnub`(`selected_song`, `song_link`) VALUES(?,?)";
                                connection.query(sql, [selectedSong, songLink], function(err, response) {

                                    if (status == 1)
                                    {
                                        var reg_date = new Date();
                                        var sql = "UPDATE tb_users set broadcast_start_time=? Where user_id=? limit 1";
                                        connection.query(sql, [reg_date, djId], function(err, response) {
                                            var response1 = {"log": 'Pubnub Data Updated!'};
                                            res.send(JSON.stringify(response1));

                                        });
                                    }
                                    else
                                    {
                                        var response1 = {"log": 'Pubnub Data Updated!'};
                                        res.send(JSON.stringify(response1));
                                    }
                                });
                            }
                            else
                            {
                                if (status == 1)
                                {
                                    var reg_date = new Date();
                                    var sql = "UPDATE tb_users set broadcast_start_time=? Where user_id=? limit 1";
                                    connection.query(sql, [reg_date, djId], function(err, response) {
                                        var response1 = {"log": 'Pubnub Data Updated!'};
                                        res.send(JSON.stringify(response1));

                                    });
                                }
                                else
                                {
                                    var response1 = {"log": 'Pubnub Data Updated!'};
                                    res.send(JSON.stringify(response1));
                                }
                            }

                        });
                    });
                }
                else if (flag == 1)
                {

                    var sql2 = "SELECT song_url from tb_pubnub_data  Where dj_id=? limit 1";
                    connection.query(sql2, [djId], function(err, response2) {


                        var songUrl = response2[0].song_url;
                        var songLink = songUrl.split("/");
                        songLink = songLink[4];

                        var sql = "UPDATE tb_bot_pubnub set bit_rate=?,npackets=?,num_bytes=?,audio_bytes=?,data_offset=?,song_file_length=? Where song_link=? limit 1";
                        connection.query(sql, [bitRate, npackets, numBytes, audioBytes, dataOffset, songFilelength, songLink], function(err, response) {

                            var sql1 = "UPDATE tb_pubnub_data set bit_rate=?,npackets=?,num_bytes=?,audio_bytes=?,data_offset=?,song_file_length=? Where dj_id=? limit 1";
                            connection.query(sql1, [bitRate, npackets, numBytes, audioBytes, dataOffset, songFilelength, djId], function(err, response) {

                                var response1 = {"log": 'Pubnub Data Updated!'};
                                res.send(JSON.stringify(response1));
                            });
                        });
                    });

                }
                else if (flag == 2)
                {
                    var sql2 = "SELECT ntp_date, song_status, selected_index, selected_song,message,bit_rate,npackets,num_bytes,song_url,audio_bytes,data_offset,song_file_length from tb_pubnub_data  Where dj_id=? limit 1";
                    connection.query(sql2, [djId], function(err, response2) {

                        if (response2.length == 0)
                        {
                            var response1 = {"error": "Dj Don't Exists!"};
                            res.send(JSON.stringify(response1));
                        }
                        else
                        {

                            var response1 = {"data": response2};
                            res.send(JSON.stringify(response1));

                        }
                    });
                }

                else if (flag == 3)
                {
                    if (status == 1)
                    {

                        var sql = "UPDATE tb_users set broadcast_secs=? Where user_id=? limit 1";
                        connection.query(sql, [remainTime, djId], function(err, response) {
                        });
                    }
                    var sql2 = "DELETE FROM tb_pubnub_data WHERE dj_id=? limit 1";
                    connection.query(sql2, [djId], function(err, response2) {
                        var response1 = {"log": 'Pubnub Data Deleted!'};
                        res.send(JSON.stringify(response1));

                    });
                }
            }
        });
    }

};