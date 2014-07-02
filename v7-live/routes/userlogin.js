var func = require('./commonfunction');
var playlist = require('./playlist');

/*
 * --------------------------------------------------------------------------------------------------
 * Fb Login
 * INPUT : userFbId,userFbName,userFbUsername,latitude,longitude,iosDeviceToken,enabledPush(0 or 1)
 * OUTPUT : User's personal data, notifications, groups
 * --------------------------------------------------------------------------------------------------
 */
exports.getFbUserDataFromFbid = function(req, res) {

    var userFbId = req.body.fb_id;
    var userFbName = req.body.fbname;
    var latitude = req.body.lat;
    var longitude = req.body.long;
    var iosDeviceToken = req.body.device_token;
    var fbAccessToken = req.body.fb_access_token;
    var appVersion = req.body.app_version;
    var fbMail = req.body.fb_email;

    var manValues = [userFbId, userFbName, latitude, longitude, fbAccessToken];

    var checkData = func.checkBlank(manValues);
    if (checkData == 1)
    {
        var response = {"error": 'Some parameter missing'};
        res.send(JSON.stringify(response));

    }
    else
    {
        checkFbUser(userFbId, fbAccessToken, function(results)
        {
            if (results == 1)
            {
                var response = {"error": 'Not An Authenticated User!'};
                res.send(JSON.stringify(response));
            }
            else
            {
                if (results.length == 0)
                {
                    registerFbUserFromFbidFbnameFbusernameLongitudeLatitudeAndDeviceToken(userFbId, userFbName, iosDeviceToken, longitude, latitude, 1, appVersion,fbMail, function(responseUser)
                    {
                        var resultData = [];
                        resultData.push(responseUser);
                        var response = {"data": resultData}
                        res.send(JSON.stringify(response));
                    });

                }
                else
                {
                    var userId = results[0].user_id;

                    if (results[0].user_active == 0) {

                        var sql = "UPDATE tb_users SET user_active=? WHERE user_id=? limit 1";
                        connection.query(sql, [1, userId], function(err, responseUpdate) {

                        });

                    }

                    checkAppVersion(userId, appVersion, function(updateAppPopUp)
                    {
                        updateUserParams(iosDeviceToken, latitude, longitude, userId);
                        if (results[0].is_premium == 0)
                        {
                            var moment = require('moment');
                            var secsData = checkBroadcastDataForUserId(userId, results[0].broadcast_secs, results[0].broadcast_start_time);

                            var outputdate = func.getTimeDifference(secsData['broadcastTime']);

                            var timeStart = outputdate['hours'] + ":" + outputdate['minutes'] + ":" + outputdate['seconds'];
                            var timeEnd = '23:59:59';

                            //var tResult = func.timeDifference(timeEnd,timeStart);

                            var t1 = moment(timeStart, "HH:mm:ss");
                            var t2 = moment(timeEnd, "HH:mm:ss");
                            var tResult = moment(t2.diff(t1)).format("HH:mm:ss");

                            var secs = secsData['sec'];
                        }

                        else
                        {
                            var secs = 600;
                            var tResult = '23:59:59';
                        }
                        checkSessionForUserId(userId, function(sessionResult)
                        {
                            var personalData = [];
                            personalData.push({"user_id": userId, "user_name": results[0].user_name, "user_access_token": results[0].user_access_token, "user_image": results[0].user_image, "session_exist": sessionResult, "is_premium": results[0].is_premium, "broadcast_secs": secs, "expires_in": tResult, "new_reg": 0, "popup": updateAppPopUp, "popup_status": popupStatus});
                            response1 = {"personal_data": personalData};
                            var resultData = [];
                            resultData.push(response1);
                            var response = {"data": resultData}
                            res.send(JSON.stringify(response));

                        });

                    });

                }
            }

        });

    }

};

function checkSessionForUserId(userId, callback) {


    var sql = "SELECT session_id FROM tb_user_playlist WHERE user_id=? LIMIT 1";
    connection.query(sql, [userId], function(err, responseSessionIds) {
        if (responseSessionIds.length == 0) {
            return callback(0);
        } else {
            return callback(1);
        }
    });

}


function  checkBroadcastDataForUserId(userId, sec, time)
{


    var date1 = new Date();
    var broadcastTime = time;
    var dayDiff = func.getTimeDifference(time);

    if (dayDiff['days'] > 0)
    {

        var sql = "UPDATE tb_users set broadcast_secs=?,broadcast_start_time=? Where user_id=? LIMIT 1";
        connection.query(sql, [600, date1, userId], function(err, responseUpdate) {
        });
        broadcastTime = date1;
        sec = 600;
    }

    dataArray = {"sec": sec, "broadcastTime": broadcastTime};
    return dataArray;

}

function registerFbUserFromFbidFbnameFbusernameLongitudeLatitudeAndDeviceToken(userFbId, fbname, deviceToken, longitude, latitude, flag, version,useremail, callback)
{
    var accessToken = func.encrypt(userFbId);
    var userPicture = "http://graph.facebook.com/" + userFbId + "/picture?width=168&height=168";


    var regDate = new Date();
    var active = 0;
    if (flag == 1) {
        active = 1;
    }
    var isPremiumUser = 0;
    if(useremail!='')
    {
       var MailChimpAPI = require('mailchimp').MailChimpAPI;

var apiKey = '05566ba2df0857f201c10f4de9ad7bc1-us2';

try { 
    var api = new MailChimpAPI(apiKey, { version : '1.3', secure : false });
} catch (error) {
    console.log(error.message);
}
//f3cf1de3d4
var merge_vars = {
    FNAME: fbname
    
};


var params = {'id':'f3cf1de3d4','email_address':useremail,'merge_vars':merge_vars};
api.listSubscribe(params, function (error, data) {
    if (error)
        console.log(error.message);
    else
        console.log('done:'+ data); // Do something with your data!
}); 
    }    
    checkPremiumUserOrNot(useremail,function(isPremiumUser)
    {
        
    var sql = "INSERT into tb_users(user_fb_id,user_name,user_device_token,user_access_token,reg_date,user_image,longitude,latitude,user_active,broadcast_secs,broadcast_start_time,app_version,is_premium,user_fb_email) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
    connection.query(sql, [userFbId, fbname, deviceToken, accessToken, regDate, userPicture, longitude, latitude, active, 600, regDate, version,isPremiumUser,useremail], function(err, result) {

        var userId = result.insertId;
        var personalData = [];
        personalData.push({"user_id": userId, "user_name": fbname, "user_access_token": accessToken, "user_image": userPicture, "is_premium": 0, "broadcast_secs": 600, "expires_in": '23:59:59', "new_reg": 1, "popup": 0, "popup_status": popupStatus});

        response1 = {"personal_data": personalData};
        return callback(response1);




    });
    });

}

/*
 * -----------------------------------------------------------------------------
 * Check whether update popup has to be shown or not
 * INPUT : userId, version
 * OUTPUT : popup shown for update
 * -----------------------------------------------------------------------------
 */
function checkAppVersion(userId, version, callback)
{
    var curAppVersion = 106;
    var sql = "SELECT app_version FROM tb_users Where user_id=? LIMIT 1";
    connection.query(sql, [userId], function(err, responseUser) {

        if (responseUser[0].app_version != version)
        {

            var sql = "UPDATE tb_users SET app_version=? WHERE user_id=? limit 1";
            connection.query(sql, [version, userId], function(err, responseUpdate) {

                if (version < curAppVersion)
                {
                    popup = {"title": 'Update Version', "text": 'Update app with new version!', "cur_version": curAppVersion};
                    return callback(popup);
                }
                else
                {
                    return callback(0);
                }
            });
        }
        else
        {
            if (responseUser[0].app_version < curAppVersion)
            {
                popup = {"title": 'Update Version', "text": 'Update app with new version!', "cur_version": curAppVersion};
                return callback(popup);
            }
            else
            {
                return callback(0);
            }
        }

    });

}


/*
 * -----------------------------------------------------------------------------
 * Fb user registered or not
 * INPUT : userFbId
 * OUTPUT : Fb user registered or not
 * -----------------------------------------------------------------------------
 */
function checkFbUser(userFbId, fbAccessToken, callback)
{
    var request = require('request');
    var urls = "https://graph.facebook.com/" + userFbId + "?fields=updated_time&access_token=" + fbAccessToken;

    request(urls, function(error, response, body) {
        if (!error && response.statusCode == 200) {
            
            var output = JSON.parse(body);
            if (output['error'])
            {
                return callback(1);
            }
            else
            {
                var sql = "SELECT user_id,user_name,user_access_token,user_image,user_fb_id,user_email,is_premium,broadcast_secs,broadcast_start_time,user_active FROM tb_users WHERE user_fb_id=? LIMIT 1";
                connection.query(sql, [userFbId], function(err, results) {
                    return callback(results);

                });
            }
        }

        else {
            return callback(1);
        }


    });


}




/*
 * -----------------------------------------------------------------------------
 * Update device token,latitude,longitude,login time,enable push of an user
 * INPUT : user_id
 * OUTPUT : updated user params
 * -----------------------------------------------------------------------------
 */
function updateUserParams(iosDeviceToken, latitude, longitude, userId)
{
    var loginTime = new Date();


    var sql = "UPDATE tb_users set user_device_token=?,last_login_date=?,longitude=?,latitude=? WHERE user_id=? LIMIT 1";
    connection.query(sql, [iosDeviceToken, loginTime, longitude, latitude, userId], function(err, result) {
    });

}

/*
 * -----------------------------------------------------------------------------
 * Access Token Login
 * INPUT : accessToken,latitude,longitude,iosDeviceToken,enabledPush(0 or 1)
 * OUTPUT : User's personal data, notifications, groups
 * -----------------------------------------------------------------------------
 */
exports.getuserDataFromAccessToken = function(req, res)
{
    var accessToken = req.body.access_token;
    var latitude = req.body.lat;
    var longitude = req.body.long;
    var iosDeviceToken = req.body.device_token;
    var action = req.body.action;
    var appVersion = req.body.app_version;
    var manValues = [accessToken, latitude, longitude];
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
                updateUserParams(iosDeviceToken, latitude, longitude, userId);
                func.getUserDataFromUserId(userId, function(results) {
                    var accessToken = results[0].user_access_token;
                    var userName = results[0].user_name;
                    var userPicture = results[0].user_image;


                    checkAppVersion(userId, appVersion, function(updateAppPopUp)
                    {

                        if (results[0].is_premium == 0)
                        {
                            var moment = require('moment');
                            var secsData = checkBroadcastDataForUserId(userId, results[0].broadcast_secs, results[0].broadcast_start_time);

                            var outputdate = func.getTimeDifference(secsData['broadcastTime']);

                            var timeStart = outputdate['hours'] + ":" + outputdate['minutes'] + ":" + outputdate['seconds'];
                            var timeEnd = '23:59:59';

                            //var tResult = func.timeDifference(timeEnd,timeStart);

                            var t1 = moment(timeStart, "HH:mm:ss");
                            var t2 = moment(timeEnd, "HH:mm:ss");
                            var tResult = moment(t2.diff(t1)).format("HH:mm:ss");

                            var secs = secsData['sec'];
                        }

                        else
                        {
                            var secs = 600;
                            var tResult = '23:59:59';
                        }

                        continueWithPreviousSessionOrNot(userId, action, function(sessionResult)
                        {
                            var personalData = [];
                            personalData.push({"user_id": userId, "user_name": results[0].user_name, "user_access_token": results[0].user_access_token, "user_image": results[0].user_image, "session_exist": sessionResult, "is_premium": results[0].is_premium, "broadcast_secs": secs, "expires_in": tResult, "new_reg": 0, "popup": updateAppPopUp, "popup_status": popupStatus});
                            response1 = {"personal_data": personalData};
                            var resultData = [];
                            resultData.push(response1);
                            var response = {"data": resultData}
                            res.send(JSON.stringify(response));

                        });

                    });

                });
            }
        });
    }
};

function continueWithPreviousSessionOrNot(userId, action, callback)
{

    if (action == 1)
    {

        checkSessionForUserId(userId, function(sessionResult)
        {
            return callback(sessionResult);
        });
    }
    else if (action != null && action == 0) {

        // if want to destory previous session playlist and all playlist sharing data
        logoutFromUserId(userId);
        return callback(0);
    }
}

function logoutFromUserId(userId) {

    //check if user has not shared its playlist with anyone then not store its playlist for previous session
    transferTableData('tb_playlist_share', userId, 'dj_user_id', function(res1)
    {

        //if user have no users in shared list then not archive the playlist just delete
        if (res1 == 0) {

            var sql = "DELETE from tb_user_playlist where user_id=?";
            connection.query(sql, [userId], function(err, result) {
            });
        }
        else {
            transferTableData('tb_user_playlist', userId, 'user_id', function(res2)
            {

            });
        }

        var sql = "UPDATE tb_users set playlist_order=? Where user_id=? limit 1";
        connection.query(sql, ['', userId], function(err, result) {
        });
    });

    //var response1 = {"log": 'Logged Out Successfully!'};
    //res.send(JSON.stringify(response1));


}



function transferTableData(table, userId, field, callback) {

    var sql = "SELECT * from " + table + " where " + field + "=?";
    connection.query(sql, [userId], function(err, row) {


        if (row.length == 0) {
            return callback(0);
        }

        //Call the function to archive the table
        //Function definition is given below
        else if (row.length != 0) {

            archiveRecord(table + "_archive", row);
        }

        //Once you archive, delete the record from original table


        if (row.length != 0 || table == 'tb_user_playlist') {

            var sql = "DELETE from " + table + " where " + field + "=?";
            connection.query(sql, [userId], function(err, row) {
            });
        }
        return callback(1);
    });

}

/*
 * ------------------------------------------------------
 *  Archiving the user current session list and shared friends ids
 * ------------------------------------------------------
 */

function archiveRecord(archived_tablename, row) {


    var curDate = new Date();
    var rowLength = row.length;
    if (archived_tablename == 'tb_user_playlist_archive') {


        for (var i = 0; i < rowLength; i++)
        {
            if (row[i].song_name == "") {
                continue;
            }
            var sql = "INSERT into " + archived_tablename + " values(?,?,?,?,?,?,?,?,?,?)";
            connection.query(sql, [row[i].playlist_id, row[i].user_id, row[i].song_name, row[i].song_artist, row[i].song_image, row[i].song_link, row[i].song_itunes_link, row[i].session_id, curDate, row[i].song_status], function(err, result) {

            });

        }
    }
    else if (archived_tablename == 'tb_playlist_share_archive') {

        for (var i = 0; i < rowLength; i++) {
            var sql = "INSERT into " + archived_tablename + " values(?,?,?,?,?)";
            connection.query(sql, [row[i].session_id, row[i].dj_user_id, row[i].listner_user_id, row[i].status, , curDate], function(err, result) {

            });

        }
    }
}

exports.getEmailUserDataFromEmailAndPassword = function(req, res) {

    var email = req.body.email;
    var pass = req.body.pass;
    var latitude = req.body.lat;
    var longitude = req.body.long;
    var iosDeviceToken = req.body.device_token;
    var appVersion = req.body.app_version;

    var manValues = [email, latitude, longitude];

    var checkData = func.checkBlank(manValues);
    if (checkData == 1)
    {
        var response = {"error": 'Some parameter missing'};
        res.send(JSON.stringify(response));

    }
    else
    {
        checkEmailUser(email, function(results)
        {

            if (results.length == 0)
            {
                registerUserFromEmailPasswordUsernameLongitudeLatitudeAndDeviceToken(email, iosDeviceToken, longitude, latitude, 1, appVersion, function(responseUser)
                {
                    var resultData = [];
                    resultData.push(responseUser);
                    var response = {"data": resultData}
                    res.send(JSON.stringify(response));
                });

            }
            else
            {
                var userId = results[0].user_id;

                if (results[0].user_active == 0) {

                    var password = passwordGenerator(email);

                    var sql = "UPDATE tb_users set user_active=?,password=? Where user_id=? LIMIT 1";
                    connection.query(sql, [1, password, userId], function(err, row) {

                    });


                    checkAppVersion(userId, appVersion, function(updateAppPopUp)
                    {

                        updateUserParams(iosDeviceToken, latitude, longitude, userId);

                        var personalData = [];
                        personalData.push({"user_id": userId, "user_name": results[0].user_name, "user_access_token": results[0].user_access_token, "user_image": userImageBaseUrl + results[0].user_image, "session_exist": sessionResult, "is_premium": results[0].is_premium, "broadcast_secs": secs, "expires_in": tResult, "new_reg": 0, "popup": 0, "popup_status": popupStatus});
                        response1 = {"personal_data": personalData};
                        var resultData = [];
                        resultData.push(response1);
                        var response = {"data": resultData}
                        res.send(JSON.stringify(response));

                    });
                }

                checkAppVersion(userId, appVersion, function(updateAppPopUp)
                {
                    updateUserParams(iosDeviceToken, latitude, longitude, userId);
                    if (results[0].is_premium == 0)
                    {
                        var moment = require('moment');
                        var secsData = checkBroadcastDataForUserId(userId, results[0].broadcast_secs, results[0].broadcast_start_time);
                        console.log(secsData);
                        var outputdate = func.getTimeDifference(secsData['broadcastTime']);
                        console.log(outputdate);
                        var timeStart = outputdate['hours'] + ":" + outputdate['minutes'] + ":" + outputdate['seconds'];
                        var timeEnd = '23:59:59';

                        //var tResult = func.timeDifference(timeEnd,timeStart);

                        var t1 = moment(timeStart, "HH:mm:ss");
                        var t2 = moment(timeEnd, "HH:mm:ss");
                        var tResult = moment(t2.diff(t1)).format("HH:mm:ss");
                        console.log(tResult);
                        var secs = secsData['sec'];
                    }

                    else
                    {
                        var secs = 600;
                        var tResult = '23:59:59';
                    }
                    checkSessionForUserId(userId, function(sessionResult)
                    {
                        var personalData = [];
                        personalData.push({"user_id": userId, "user_name": results[0].user_name, "user_access_token": results[0].user_access_token, "user_image": userImageBaseUrl + results[0].user_image, "session_exist": sessionResult, "is_premium": results[0].is_premium, "broadcast_secs": secs, "expires_in": tResult, "new_reg": 0, "popup": updateAppPopUp, "popup_status": popupStatus});
                        response1 = {"personal_data": personalData};
                        var resultData = [];
                        resultData.push(response1);
                        var response = {"data": resultData};
                        res.send(JSON.stringify(response));

                    });

                });

            }


        });

    }

};

function registerUserFromEmailPasswordUsernameLongitudeLatitudeAndDeviceToken(useremail, device_token, longitude, latitude, flag, version, callback) {


    var access_token = func.encrypt(useremail);
    var username = useremail.split("@");
    username = username[0];
    var imgname = 'user.png';


    if (flag == 1) {
        var password_db = passwordGenerator(useremail);
        var active = 1;
    } else {
        var password_db = "";
        var active = 0;
    }
var MailChimpAPI = require('mailchimp').MailChimpAPI;

var apiKey = '05566ba2df0857f201c10f4de9ad7bc1-us2';

try { 
    var api = new MailChimpAPI(apiKey, { version : '1.3', secure : false });
} catch (error) {
    console.log(error.message);
}
//f3cf1de3d4
var merge_vars = {
    FNAME: username
    
};


var params = {'id':'f3cf1de3d4','email_address':useremail,'merge_vars':merge_vars};
api.listSubscribe(params, function (error, data) {
    if (error)
        console.log(error.message);
    else
        console.log('done:'+ data); // Do something with your data!
});
    checkPremiumUserOrNot(useremail, function(isPremiumUser)

    {

        var reg_date = new Date();
        var sql = "INSERT into tb_users(user_email,user_name,password,user_device_token,user_access_token,reg_date,user_image,longitude,latitude,user_active,broadcast_secs,broadcast_start_time,app_version,is_premium) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
        connection.query(sql, [useremail, username, password_db, device_token, access_token, reg_date, imgname, longitude, latitude, active, 600, reg_date, version, isPremiumUser], function(err, result) {


            var userPicture = userImageBaseUrl + imgname;
            var userId = result.insertId;
            var personalData = [];
            personalData.push({"user_id": userId, "user_name": username, "user_access_token": access_token, "user_image": userPicture, "is_premium": isPremiumUser, "broadcast_secs": 600, "expires_in": '23:59:59', "new_reg": 1, "popup": 0, "popup_status": popupStatus});

            response1 = {"personal_data": personalData};
            return callback(response1);

        });
    });
}

function checkPremiumUserOrNot(useremail, callback) {


    var sql = "SELECT id FROM tb_premium_users Where email=? LIMIT 1";
    connection.query(sql, [useremail], function(err, results) {

        if (results.length > 0)
        {
            return callback(1);
        }
        else
        {
            return callback(0);
        }
    });

}
function passwordGenerator(useremail) {
    var mathjs = require('mathjs');
    math = mathjs();
    var text = "";
    var possible = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";

    for (var i = 0; i < 6; i++)
        text += possible.charAt(math.floor(math.random() * possible.length));
    //password generation

    var md5 = require('MD5');
    var password_db = md5(text);

    return password_db;
}

function checkEmailUser(email, callback)
{
    var sql = "SELECT user_id,user_name,user_access_token,user_image,user_fb_id,user_email,is_premium,broadcast_secs,broadcast_start_time,user_active FROM tb_users WHERE user_email=? LIMIT 1";
    connection.query(sql, [email], function(err, results) {
        return callback(results);
    });
}



exports.getFbFriendsFromFbIdAndFbAccessToken = function(req, res)
{
    var accessToken = req.body.access_token;
    var fbAccessToken = req.body.fb_access_token;
    var fbId = req.body.fb_id;

    var manValues = [accessToken, fbAccessToken, fbId];
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
                var sql = "SELECT `user_fb_id` from `tb_users` where `user_fb_id`!=0";
                connection.query(sql, function(err, result_fb_id) {

                    var request = require('request');
                    var urls = "https://graph.facebook.com/" + fbId + "/friends?access_token=" + fbAccessToken;
                    
                    console.log(urls);

                    request(urls, function(error, response, body) {
                        if (!error && response.statusCode == 200) {
                            
                            var output = JSON.parse(body);

                            var dbFbIds = [];
                            var fbFriends = [];

                            var fbFriendsName = [];

                            var dbFbLength = result_fb_id.length;
                            var fbfriendLength = (output['data']).length;
                            for (var i = 0; i < dbFbLength; i++)
                            {
                                dbFbIds.push(result_fb_id[i].user_fb_id);
                            }



                            for (var j = 0; j < fbfriendLength; j++)
                            {
                                fbFriends.push(parseInt(output['data'][j].id));

                                fbFriendsName.push((output['data'][j].name));

                            }

                            var reg = [];
                            var regNames = [];
                            var k = 0;

                            for (var i = 0; i < fbfriendLength; i++)
                            {

                                if (dbFbIds.indexOf(fbFriends[i]) != -1)
                                {

                                    reg[k] = fbFriends[i];
                                    regNames[k] = fbFriendsName[i];
                                    fbFriends.splice(i, 1);
                                    fbFriendsName.splice(i, 1);
                                    k++;
                                    i--;
                                }

                            }


                            var regLength = reg.length;


                            if (regLength > 0)
                            {


                                var resultReg = [];
                                for (var m = 0; m < regLength; m++)
                                {
                                    resultReg.push({"id": reg[m], "name": regNames[m]});
                                }

                                var unregCount = fbFriends.length;
                                var unregData = [];
                                for (var l = 0; l < unregCount; l++)
                                {
                                    unregData.push({"id": fbFriends[l], "name": fbFriendsName[l]});
                                }
                                var output = {"registered": resultReg, "not registered": unregData};

                                res.send(JSON.stringify(output));

                            }

                            else
                            {
                                var resultReg = [];
                                var output = {"registered": resultReg, "not registered": output['data']};
                                res.send(JSON.stringify(output));
                            }




                        }
                        else {
                            var response1 = {"error": 'Error Fetching Fb Friends'};
                            res.send(JSON.stringify(response1));
                        }
                    });
                });
            }
        });
    }
};

/*
 * ------------------------------------------------------
 *  find near by user based on distance calculated
 * ------------------------------------------------------
 */

      exports.getNearByDjFromUserId = function(req,res) {

        var accessToken = req.body.access_token;
        var latitude = req.body.lat;
        var longitude = req.body.lon;


         var manValues = [accessToken,latitude, longitude];
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



        var sql = "UPDATE tb_users set longitude=?,latitude=? Where user_id=? LIMIT 1";
        connection.query(sql, [longitude, latitude, userId], function(err, responseDataTotalSong) {

         nearByDjFromUserId(userId, latitude, longitude,function(resultDjs)
    {
       
        var responseUser = {"near_dj": resultDjs};
        var resultData = [];
        resultData.push(responseUser);
        var response1 = {"data": resultData}
        res.send(JSON.stringify(response1));
    });

        
    });
            }
        });
    }
      };

     function nearByDjFromUserId(userid, lat1, lon1,callback) {


        playlist.getUniqueDjIdsFromUserId(userid,function(uniqueDjIds)
        {

	   if(uniqueDjIds==0)
	   {
	    return callback([]);
	   }
           else
               {
           var userIds =[];
           var uniqueDjIdsCount= uniqueDjIds.length;
        for(var i=0; i<uniqueDjIdsCount; i++)
        {
          userIds[i]=uniqueDjIds[i].dj_user_id;
        }

        var str = userIds.toString(",");
       // print_r($uniqueDjIds);

      
        var sql = "SELECT user_id,user_name,user_image,user_fb_id,longitude,latitude FROM tb_users WHERE user_id in ("+str+") "; //and datediff(CURDATE(), DATE(last_login_date)) <= 30";
        connection.query(sql, function(err, response) {
           

        if (response.length == 0) {
            return callback([]);
        }
else
    {
      var responseCount =response.length;
        for(var j=0; j<responseCount; j++) {

            var image = '';
            if (response[j].user_fb_id != 0) {
                image = response[j].user_image;
            } else {
                image = userImageBaseUrl+ response[j].user_image;
            }

            response[j].user_image = image;


            response[j].share_status=uniqueDjIds[j].share_status;


            response[j].distance = func.toMiles(lat1, lon1, response[j].latitude, response[j].longitude);
            
            
        }

        response = func.sortByKeyDesc(response, 'share_status');
        response = func.sortByKeyAsc(response, 'distance');
        

        return callback(response);
    }
    });
               }
        });
     }


/*
 * ------------------------------------------------------
 *  Forgot Password (Password send to mail)
 * ------------------------------------------------------
 */


exports.forgotPasswordFromEmail = function(req, res) {
    var email = req.body.email;
    var manValues = [email];
    var checkData = func.checkBlank(manValues);
    if (checkData == 1)
    {

        var response = {"error": 'Some parameter missing'};
        res.send(JSON.stringify(response));
    }
    else
    {
        var sql = "SELECT user_id FROM tb_users WHERE user_email=? LIMIT 1";
        connection.query(sql, [email], function(err, response) {

            if (response.length == 1)
            {
                var password = func.generateRandomString();

                var md5 = require('MD5');
                var encrypted_pass = md5(password);


                var game_name = "Boombotix";
                var to = email;
                var sub = "Password Mail";
                var msg = "Your password for " + game_name + " profile.\nEmail: " + email + "\nPassword: " + password + "\n\nThanks \n BOOMBOTIX Team";

                func.sendEmail(to, msg, sub, function(result)
                {
                    if (result == 1)
                    {
                        var sql = "UPDATE tb_users set password=? WHERE user_email=? LIMIT 1";
                        connection.query(sql, [encrypted_pass, email], function(err, response) {


                            var response = {"log": 'Password sent to mail'};
                            res.send(JSON.stringify(response));
                        });
                    }
                    else
                    {
                        var response = {"error": 'Password not sent to mail'};
                        res.send(JSON.stringify(response));
                    }
                });
            }
            else
            {
                var response = {"error": 'Email address not found'};
                res.send(JSON.stringify(response));
            }

        });
    }
};

/*
 * ------------------------------------------------------
 *  share dj playlist with friends
 * ------------------------------------------------------
 */

exports.shareDjPlayListFromUserIdToFriendId = function(req, res) {

    var accessToken = req.body.access_token;
    var fbId = req.body.fb_id;
    var fbname = req.body.fbname;
    var userEmail = req.body.user_email;
    var inviteBy = req.body.invite_by;

    if (fbId != '') {
        var inviteId = fbId;
        var inviteName = fbname;
    }

    if (userEmail) {
        var inviteId = userEmail;
    }

    //$sessionId = $data['session_id'];

    if (inviteBy == 'fb') {

        var manValues = [accessToken, inviteId, inviteBy, inviteName];

    } else if (inviteBy == 'email') {

        var manValues = [accessToken, inviteId, inviteBy];
    }

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
                var userName = result[0].user_name;
                
                if (inviteBy == 'fb') {
                    checkUserFromFbIdOrUserEmail(inviteId, inviteBy, inviteName, function(userIdFriend)
                    {
                        playlist.setSessionIdFromUserId(userId, function(sessionId)
                        {

                            playlist.saveUserPlayList(sessionId, userId, userIdFriend, 0, function(response)
                            {
                              var sql = "SELECT user_device_token FROM tb_users WHERE user_id=? LIMIT 1";
                                        connection.query(sql, [userIdFriend], function(err, userDevice) {
                                            var message =  "What's UP!! "+userName+" has just invited you to join his/her DJ session on Boombotix";
                                            func.sendPushNotification(userDevice[0].user_device_token, message);   
                                res.send(JSON.stringify(response));
                                        });
                            });
                        });

                    });
                } else if (inviteBy == 'email') {
                    checkUserFromFbIdOrUserEmail(inviteId, inviteBy, '', function(userIdFriend)
                    {
                        playlist.setSessionIdFromUserId(userId, function(sessionId)
                        {

                            playlist.saveUserPlayList(sessionId, userId, userIdFriend, 0, function(response)
                            {
                                var sql = "SELECT user_device_token FROM tb_users WHERE user_id=? LIMIT 1";
                                        connection.query(sql, [userIdFriend], function(err, userDevice) {
                                            var message =  "What's UP!! "+userName+" has just invited you to join his/her DJ session on Boombotix";
                                            func.sendPushNotification(userDevice[0].user_device_token, message);
//                                var game_name = "Boombotix";
//                                var to = inviteId;
//                                var sub = "Invitation Mail";
//                                var msg = "Some one shared playlist with you in " + game_name + "\n\nThanks \n BOOMBOTIX Team";
//
//                                func.sendEmail(to, msg, sub, function(result)
//                                {
//                                });
                                res.send(JSON.stringify(response));
                                        });
                            });
                        });
                    });
                }

            }
        });
    }
};


/*
 * ------------------------------------------------------
 *  check weather a user is registered or not
 * ------------------------------------------------------
 */

function checkUserFromFbIdOrUserEmail(inviteId, inviteBy, inviteName, callback) {
    if (inviteBy == 'fb') {

        var sql = "SELECT user_id FROM tb_users WHERE user_fb_id=? LIMIT 1";
        connection.query(sql, [inviteId], function(err, responseCheckId) {
            if (responseCheckId.length == 0)
            {
                registerFbUserFromFbidFbnameFbusernameLongitudeLatitudeAndDeviceToken(inviteId, inviteName, "", "", "", 0, 0,0, function(data)
                {
                    return callback(data['personal_data'][0].user_id);
                });

            }
            else
            {
                return callback(responseCheckId[0].user_id);
            }
        });

    }
    else if (inviteBy == 'email') {
        var sql = "SELECT user_id FROM tb_users WHERE user_email=? LIMIT 1";
        connection.query(sql, [inviteId], function(err, responseCheckId) {

            if (responseCheckId.length == 0)
            {
                registerUserFromEmailPasswordUsernameLongitudeLatitudeAndDeviceToken(inviteId, "", "", "", 0, 0, function(data)
                {
                    return callback(data['personal_data'][0].user_id);
                });

            }
            else
            {
                var userId = responseCheckId[0].user_id;


                return callback(userId);
            }
        });

    }
}

/*
 * ------------------------------------------------------
 *  Getting shared friends list
 * ------------------------------------------------------
 */

exports.getFriendsSharedWithFromUserId = function(req, res) {
    var access_token = req.body.access_token;
    var mode = req.body.mode;

    var manValues = [access_token, mode];

    var checkData = func.checkBlank(manValues);

    if (checkData == 1)
    {
        var response = {"error": 'Some parameter missing'};
        res.send(JSON.stringify(response));
    }
    else
    {

        func.authenticateUser(access_token, function(result) {

            if (result == 0)
            {
                var response1 = {"error": 'Invalid access token'};
                res.send(JSON.stringify(response1));
            }
            else {

                var userId = result[0].user_id;

                playlist.getSharedFriendsIds(userId, mode, function(userIdFriends)
                {
                    if(userIdFriends == 1)
                        {
                         
                          var response1 = {"log": "No user found"};
                          res.send(JSON.stringify(response1));
                        }
                        else
                            {

                    var sharedFriendResponse = [];
                    var friendsCount = userIdFriends.length;
                    if (mode == 'dj') {

                        for (var i = 0; i < friendsCount; i++) {
                            (function(i) {
                                func.getUserDataFromUserId(userIdFriends[i]['listner_user_id'], function(userData)

                                {
                                    sharedFriendResponse[i] = userData[0];
                                    if (userIdFriends[i].status == 3) {
                                        sharedFriendResponse[i].share_status = 1;
                                    } else {
                                        sharedFriendResponse[i].share_status = userIdFriends[i].status;
                                    }



                                    if (i == friendsCount - 1)
                                    {
                                        var response1 = {"data": sharedFriendResponse};
                                        res.send(JSON.stringify(response1));
                                    }
                                });

                            })(i);
                        }
                    }
                    
                    else {

                        for (var i = 0; i < friendsCount; i++) {
                            (function(i) {

                                //$online=$this->getOnlineDjFromDjId($userIdFriends['data'][$i]['dj_user_id']);

                                func.getUserDataFromUserId(userIdFriends[i]['dj_user_id'], function(userData)
                                {
                                    getOnlineDjFromDjId(userIdFriends[i]['dj_user_id'], function(online)
                                    {
                                        sharedFriendResponse[i] = userData[0];

                                        if (userIdFriends[i].status == 0) {
                                            sharedFriendResponse[i].share_status = 1;
                                        } else {
                                            sharedFriendResponse[i].share_status = userIdFriends[i]['status'];
                                        }
                                        sharedFriendResponse[i].online = online;
                                        if (i == friendsCount - 1)
                                        {
                                            var response1 = {"data": sharedFriendResponse};
                                            res.send(JSON.stringify(response1));
                                        }

                                        //unset($sharedFriendResponse['data'][$i]['new_reg']);
                                    });
                                });
                            })(i);
                        }

                    }
                            }
                });
            }
        });
    }
};

function getOnlineDjFromDjId(djId, callback)
{
    var sql = "SELECT id,ntp_date,bit_rate,song_file_length FROM tb_pubnub_data WHERE dj_id =? LIMIT 1";
    connection.query(sql, [djId], function(err, responseOnlineDj) {


        if (responseOnlineDj.length == 0)
        {

            return callback(0);
        }
        else
        {
            var moment = require('moment');
            var curDate = new moment();
            var songLength = ((responseOnlineDj[0].song_file_length * 8) / (responseOnlineDj[0].bit_rate)); //in seconds
            var curSongTime = (responseOnlineDj[0].ntp_date).split("  ");


            var moment = require('moment');
            var dateFormat = 'YYYY-MM-DD HH:mm:ss';
            var seconds = songLength;
            var futureDate = moment(curSongTime[0], "YYYY-MM-DD HH:mm:ss");
            futureDate = moment(futureDate).add('seconds', seconds).format(dateFormat);

            var timeFirst = moment(curDate, "YYYY-MM-DD HH:mm:ss");

            var differenceInSeconds = timeFirst.diff(futureDate, 'seconds');


            if (differenceInSeconds > 0)
            {

                return callback(0);
            }
            else
            {

                return callback(1);
            }

        }
    });

}

/*
 * ------------------------------------------------------
 *  Accepting friend request and getting playlist
 * ------------------------------------------------------
 */

exports.acceptRequestFromUserId = function(req, res) {
    var access_token = req.body.access_token;
    var dj_id = req.body.dj_id;


    var manValues = [access_token, dj_id];

    var checkData = func.checkBlank(manValues);

    if (checkData == 1)
    {
        var response = {"error": 'Some parameter missing'};
        res.send(JSON.stringify(response));
    }
    else
    {

        func.authenticateUser(access_token, function(result) {

            if (result == 0)
            {
                var response1 = {"error": 'Invalid access token'};
                res.send(JSON.stringify(response1));
            }
            else {

                var userId = result[0].user_id;

                var sql = "SELECT dj_user_id FROM tb_playlist_share WHERE listner_user_id=? && status=? LIMIT 1";
                connection.query(sql, [userId, 2], function(err, responsePreviousDj) {

                    if (responsePreviousDj.length > 0) {
                        var sql = "UPDATE tb_playlist_share set status=? Where dj_user_id=? && listner_user_id=? LIMIT 1";
                        connection.query(sql, [0, responsePreviousDj[0].dj_user_id, userId], function(err, responseUserUpdate) {

                        });
                    }

                    var sql = "UPDATE tb_playlist_share set status=? Where dj_user_id=? && listner_user_id=? LIMIT 1";
                    connection.query(sql, [2, dj_id, userId], function(err, responseUser) {


                        func.getDjPlayListFromUserId(dj_id, function(djPlayList)
                        {

                            var response = {"data": djPlayList};
                            res.send(JSON.stringify(response));
                        });
                    });
                });
            }
        });
    }
};


exports.logoutFromUserAccessToken = function(req, res) {
    var access_token = req.body.access_token;

    var manValues = [access_token];

    var checkData = func.checkBlank(manValues);

    if (checkData == 1)
    {
        var response = {"error": 'Some parameter missing'};
        res.send(JSON.stringify(response));
    }
    else
    {

        func.authenticateUser(access_token, function(result) {

            if (result == 0)
            {
                var response1 = {"error": 'Invalid access token'};
                res.send(JSON.stringify(response1));
            }
            else {

                var userId = result[0].user_id;
                logoutFromUserId(userId);
                var response1 = {"log": 'Logged Out Successfully!'};
                res.send(JSON.stringify(response1));
            }
        });
    }
};

/*
 * ------------------------------------------------------
 *  Clear the user current playlist without destroy session id and sharing list
 * ------------------------------------------------------
 */

exports.clearPlaylistFromUserAccessToken = function(req, res) {
    var access_token = req.body.access_token;

    var manValues = [access_token];

    var checkData = func.checkBlank(manValues);

    if (checkData == 1)
    {
        var response = {"error": 'Some parameter missing'};
        res.send(JSON.stringify(response));
    }
    else
    {

        func.authenticateUser(access_token, function(result) {

            if (result == 0)
            {
                var response1 = {"error": 'Invalid access token'};
                res.send(JSON.stringify(response1));
            }
            else {

                var userId = result[0].user_id;

                var sql = "SELECT session_id from tb_user_playlist where user_id=? LIMIT 1";
                connection.query(sql, [userId], function(err, responseSessionId) {


                    if (responseSessionId.length != 0) {
                        var userSessionId = responseSessionId[0].session_id;

                        var sql = "DELETE from tb_user_playlist where user_id=?";
                        connection.query(sql, [userId], function(err, responseDel) {



                            var playlistCreatedDatetime = new Date();

                            var sql = "INSERT into tb_user_playlist(user_id,session_id,playlist_created_datetime) values(?,?,?)";
                            connection.query(sql, [userId, userSessionId, playlistCreatedDatetime], function(err, response) {

                            });
                        });

                    }

                    var sql1 = "UPDATE tb_users set playlist_order=? Where user_id=? limit 1";
                    connection.query(sql1, ['', userId], function(err, response1) {

                    });

                    var response1 = {"log": 'Playlist cleared!'};
                    res.send(JSON.stringify(response1));
                });
            }
        });
    }
};

/*
 * ------------------------------------------------------
 *  Getting previous playlist shared and friends ids
 * ------------------------------------------------------
 */

exports.getPreviousSessionFromUserId = function(req, res) {

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

                var sql = "SELECT dj_user_id,session_id,share_datetime FROM tb_playlist_share_archive where listner_user_id=? ";
                connection.query(sql, [userId], function(err, responseDjIds) {
                    var djCount = responseDjIds.length;
                    if (djCount == 0) {

                        var response1 = {"log": 'No previous playlist'};
                        res.send(JSON.stringify(response1));
                    }
                    else
                    {
                        responseDjIds = func.sortByKeyDesc(responseDjIds, 'share_datetime');
                        responseDjIds = responseDjIds.slice(0, 5);
                        var response = [];
                        var userData = [];

                        for (var i = 0; i < djCount; i++) {
                            (function(i) {
                                func.getUserDataFromUserId(responseDjIds[i].dj_user_id, function(userData)
                                {

                                    userData[0].session_id = responseDjIds[i].session_id;

                                    response[i] = userData[0];

                                    if (i == djCount - 1)
                                    {

                                        var responseUser = {"dj_data": response};
                                        var resultData = [];
                                        resultData.push(responseUser);
                                        var response1 = {"data": resultData}
                                        res.send(JSON.stringify(response1));
                                    }
                                });
                            })(i);
                        }

                    }
                });
            }
        });
    }
};

/*
 * ------------------------------------------------------
 *  Getting connecting friends of current user
 * ------------------------------------------------------
 */

exports.getconnectionFromUserId = function(req, res) {

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
            else {

                var userId = result[0].user_id;

                var sql = "SELECT dj_user_id FROM tb_playlist_share where listner_user_id=? and status=?  LIMIT 1";
                connection.query(sql, [userId, 2], function(err, responseDjId)  {

                    if (responseDjId.length == 0) {
                        var response1 = {"error": 'Not Listening to any Dj'};
                        res.send(JSON.stringify(response1));
                    } else {
                        var sql = "SELECT listner_user_id FROM tb_playlist_share where dj_user_id=?";
                        connection.query(sql, [responseDjId[0].dj_user_id], function(err, responseUserIds) {

                            // print_r($responseUserIds);
                            var listenerCount = responseUserIds.length;
                            if (listenerCount == 0) {
                                var response1 = {"error": 'No Connections'};
                                res.send(JSON.stringify(response1));

                            } else {
                                var response = [];
                                for (var i = 0; i < listenerCount; i++) {
                                    (function(i) {
                                        func.getUserDataFromUserId(responseUserIds[i].listner_user_id, function(userData)
                                        {
                                            
                                            response[i] = userData[0];

                                            if (i == listenerCount - 1)
                                            {

                                                var responseUser = {"friends": response};
                                                var resultData = [];
                                                resultData.push(responseUser);
                                                var response1 = {"data": resultData}
                                                res.send(JSON.stringify(response1));
                                            }
                                        });
                                    })(i);
                                }

                            }

                        });
                    }
                });
            }
        });
    }
};
/*
 * ------------------------------------------------------
 *  Requesting dj to allow the user to listen his music
 * ------------------------------------------------------
 */
exports.requestDjFromUserIdAndDjId = function(req, res) {
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
                var userName = result[0].user_name;

//                checkingDjCanSharePlaylistOrNot(djId, userId, function(canShare)
//                {
//
//                
//                    if (canShare != 0)
//                    {

                        var sql = "SELECT dj_user_id,status FROM tb_playlist_share WHERE listner_user_id=?";
                        connection.query(sql, [userId], function(err, responsePreviousDj) {

                            var curDj = 0;
                            var flag = 0;
                            var responsePreviousDjCount = responsePreviousDj.length;
                            for (var i = 0; i < responsePreviousDjCount; i++)
                            {

                                if (responsePreviousDj[i].dj_user_id == djId && responsePreviousDj[i].status == 2)
                                {

                                    flag=3;
                                    
                                }
                                if (responsePreviousDj[i].dj_user_id != djId && responsePreviousDj[i].status == 2)
                                {

                                    curDj = responsePreviousDj[i].dj_user_id;
                                }

                                if (responsePreviousDj[i].dj_user_id == djId && responsePreviousDj[i].status != 2)
                                {

                                    flag = 1;
                                }

                            }
                            if(flag == 3)
                                {
                                    var response1 = {"error": 'Already joined!'};
                                    res.send(JSON.stringify(response1));
                                }
                                else
                                    {
                                    if (curDj != 0) {


                                var sql = "UPDATE tb_playlist_share set status=? Where dj_user_id=? && listner_user_id=? LIMIT 1";
                                connection.query(sql, [0, curDj, userId], function(err, responseUserUpdate) {


                                });
                            }

                                //$response = $this->_playlist->saveUserPlayList($sessionId, $djId, $userId,2);
//            $sql = "UPDATE tb_playlist_share set status=? Where dj_user_id=? && listner_user_id=? LIMIT 1";
//            $bindParams = array(2, $userId, $listenerId);
//            $responseUser = $this->_DAL->sqlQuery($sql, $bindParams);





                                if (flag == 0)
                                {
                                    playlist.setSessionIdFromUserId(djId, function(sessionId)
                                    {
                                   
                                        var playlistShareDatetime = new Date();
                                        var sql = "INSERT into tb_playlist_share(session_id,dj_user_id,listner_user_id,status,share_datetime) values(?,?,?,?,?)";
                                        connection.query(sql, [sessionId, djId, userId, 2, playlistShareDatetime], function(err, response) {
                                         var sql = "SELECT user_device_token FROM tb_users WHERE user_id=? LIMIT 1";
                                        connection.query(sql, [djId], function(err, userDevice) {
                                            var message =  userName+' has tuned into your DJ channel';
                                            func.sendPushNotification(userDevice[0].user_device_token, message);
                                         var response1 = {"log": 'Dj joined successfully!'};
                                        res.send(JSON.stringify(response1));
                                        });
                                        });
                                   
                                    });
                                }
                                else if (flag == 1)
                                {
                                    var sql = "UPDATE tb_playlist_share set status=? Where dj_user_id=? && listner_user_id=? LIMIT 1";
                                    connection.query(sql, [2, djId, userId], function(err, responseUserUpdate) {
                                        var sql = "SELECT user_device_token FROM tb_users WHERE user_id=? LIMIT 1";
                                        connection.query(sql, [djId], function(err, userDevice) {
                                            var message =  userName+' has tuned into your DJ channel';
                                            func.sendPushNotification(userDevice[0].user_device_token, message);
                                        var response1 = {"log": 'Dj joined successfully!'};
                                        res.send(JSON.stringify(response1));
                                        });
                                    });
                                }
                                    }
                        });
//                            }
//                            else
//                            {
//                                var response1 = {"error": "Can't join!"};
//                                res.send(JSON.stringify(response1));
//
//                            }
//
//                        
//                });
            }
        });
    }
};



function checkingDjCanSharePlaylistOrNot(djId, listenerId, callback)
{
    var canshare = 0;
    var sql = "SELECT id FROM tb_bot_counter WHERE dj_id=? LIMIT 1";
    connection.query(sql, [djId], function(err, responseBotDj) {
      if(responseBotDj.length ==1)  
          {
             canshare = 1;
             return callback(canshare); 
          }
          else
              {
            
    var sql = "SELECT is_premium FROM tb_users WHERE user_id=? LIMIT 1";
    connection.query(sql, [djId], function(err, isPremium) {


        var sql = "SELECT listner_user_id FROM tb_playlist_share WHERE dj_user_id=? ";
        connection.query(sql, [djId], function(err, responseDjListeners) {
            var listenersCount = responseDjListeners.length;
            for (var i = 0; i < listenersCount; i++)
            {

                if (responseDjListeners[i].listner_user_id == listenerId)
                {

                    canshare = 1;
                    return callback(canshare);
                }
            }


            if (isPremium[0]['is_premium'] == 0)
            {
                if (listenersCount < 1)
                    canshare = 1;
                return callback(canshare);
            }
            else
            {
                if (listenersCount < 10)
                    canshare = 1;
                return callback(canshare);
            }
            
        });

    });
              }
              });
}
/*
 * ------------------------------------------------------
 *  Dj accepting request of an user
 * ------------------------------------------------------
 */
exports.acceptRequestFromDjId = function(req, res) {

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

                var sql = "SELECT dj_user_id FROM tb_playlist_share WHERE listner_user_id=? && status=? LIMIT 1";
                connection.query(sql, [listenerId, 2], function(err, responsePreviousDj) {

                    if (responsePreviousDj.length > 0) {
                        var sql = "UPDATE tb_playlist_share set status=? Where dj_user_id=? && listner_user_id=? LIMIT 1";
                        connection.query(sql, [0, responsePreviousDj[0].dj_user_id, listenerId], function(err, responseUserUpdate) {
                        });
                    }

                    var sql = "UPDATE tb_playlist_share set status=? Where dj_user_id=? && listner_user_id=? LIMIT 1";
                    connection.query(sql, [2, userId, listenerId], function(err, responseUser) {

                        var response1 = {"log": 'Request Accepted Successfully!'};
                        res.send(JSON.stringify(response1));
                    });
                });
            }
        });
    }
};

/*
 * ------------------------------------------------------
 *  Dj accepting request of an user
 * ------------------------------------------------------
 */
exports.editProfileFromAccessToken = function(req, res) {
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
            else
            {

                var userId = result[0].user_id;

                var mathjs = require('mathjs');
                math = mathjs();
                var timestamp = new Date().getTime().toString();
                var length = 4;
                var str = '';
                var chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
                var size = chars.length;
                for (var i = 0; i < length; i++) {

                    var randomnumber = math.floor(math.random() * size);
                    str = chars[randomnumber] + str;
                }

                var timestamp = new Date().getTime().toString();

                req.files.pic.name = str + timestamp + "-" + req.files.pic.name;
                func.uploadImageToS3Bucket(req.files.pic, 'user_profile', function(userImageLink)
                {

                    var sql = "UPDATE tb_users set user_image=? Where user_id=? LIMIT 1";
                    connection.query(sql, [userImageLink, userId], function(err, responseUser) {

                        var response1 = {"log": 'User Profile Updated Successfully!'};
                        res.send(JSON.stringify(response1));

                    });
                });
            }
        });
    }
};

/*
 * ------------------------------------------------------
 *  updating user account to premium
 * ------------------------------------------------------
 */
exports.updateUserToPremiumFromAccessToken = function(req, res) {
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
            else
            {

                var userId = result[0].user_id;

                var sql = "UPDATE tb_users set is_premium=? Where user_id=? LIMIT 1";
                connection.query(sql, [1, userId], function(err, responseUser) {

                    var response1 = {"log": 'User Profile Updated Successfully!'};
                    res.send(JSON.stringify(response1));
                });
            }
        });
    }
};