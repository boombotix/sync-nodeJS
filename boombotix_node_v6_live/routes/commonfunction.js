
/*
 * ------------------------------------------------------
 * Check if manadatory fields are not filled
 * INPUT : array of field names which need to be mandatory
 * OUTPUT : Error if mandatory fields not filled
 * ------------------------------------------------------
 */

exports.checkBlank = function(arr)
{

    var arrlength = arr.length;
    for (var i = 0; i < arrlength; i++)
    {
        if (arr[i] == '')
        {
            return 1;
            break;
        }

    }

    return 0;
};

/*
 * ------------------------------------------------------
 *  Authenticate a user
 *  INPUT : user_access_token
 *  OUTPUT : user_id
 * ------------------------------------------------------
 */

exports.authenticateUser = function(userAccessToken, callback)
{
    var sql = "SELECT `user_id`,`user_name` FROM `tb_users` WHERE `user_access_token`=? LIMIT 1";
    connection.query(sql, [userAccessToken], function(err, result) {

        if (result.length > 0) {
            return callback(result);
        } else {
            return callback(0);
        }
    });
};
/*
 * -----------------------------------------------------------------------------
 * Getting user data from user_id
 * INPUT : user_id
 * OUTPUT : user's data
 * -----------------------------------------------------------------------------
 */
exports.getUserDataFromUserId = function(userId, callback)
{
    var sql = "SELECT user_id,user_name,user_access_token,user_image,user_fb_id,user_email,is_premium,broadcast_secs,broadcast_start_time FROM tb_users WHERE user_id=? LIMIT 1";
    connection.query(sql, [userId], function(err, results) {

        if (results[0].user_fb_id == 0)
        {
            results[0].user_image = userImageBaseUrl + results[0].user_image;
        }


        return callback(results);

    });
}
/*
 * -----------------------------------------------------------------------------
 * return time difference of cur date and another time
 * INPUT : time
 * OUTPUT : diff of 2 times
 * -----------------------------------------------------------------------------
 */
exports.getTimeDifference = function(time)
{

    var mathjs = require('mathjs');
    var Math = mathjs();
    var today = new Date();
    var diffMs = (today - time); // milliseconds between now & post date

    var diffDays = Math.floor(diffMs / 86400000); // days
    var diffHrs = Math.floor((diffMs % 86400000) / 3600000); // hours
    var diffMins = Math.floor(((diffMs % 86400000) % 3600000) / 60000); // minutes
    var diffSecs = Math.round((((diffMs % 86400000) % 3600000) % 60000) / 1000); // seconds
    var postTime = {"days": diffDays, "hours": diffHrs, "minutes": diffMins, "seconds": diffSecs};

    return postTime;

};
/*
 * -----------------------------------------------------------------------------
 * Encryption code
 * INPUT : string
 * OUTPUT : crypted string
 * -----------------------------------------------------------------------------
 */
exports.encrypt = function(text) {
    var crypto = require('crypto');
    var cipher = crypto.createCipher('aes-256-cbc', 'd6F3Efeq');
    var crypted = cipher.update(text, 'utf8', 'hex');
    crypted += cipher.final('hex');
    return crypted;
}

exports.sendEmail = function(receiverMailId,message,subject,callback) {
    var nodemailer = require("nodemailer");
var smtpTransport = nodemailer.createTransport("SMTP",{
    service: "Gmail",
    auth: {
        user: "boombotixserver@gmail.com",
        pass: "clicklabs"
    }
});

// setup e-mail data with unicode symbols
var mailOptions = {
    from: "Boombotix Admin", // sender address
    to: receiverMailId, // list of receivers
    subject: subject, // Subject line
    text: message, // plaintext body
    //html: "<b>Hello world ?</b>" // html body
}

// send mail with defined transport object
smtpTransport.sendMail(mailOptions, function(error, response){
    if(error){ 
        
       return callback(0);
    }else{
        
        return callback(1);
    }

    // if you don't want to use this transport object anymore, uncomment following line
    //smtpTransport.close(); // shut down the connection pool, no more messages
});
};

exports.generateRandomString=function()
{
    var mathjs = require('mathjs');
    math = mathjs();
    
    var text = "";
    var possible = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";

    for( var i=0; i < 6; i++ )
        text += possible.charAt(math.floor(math.random() * possible.length));

    return text;
};
/*
     * ------------------------------------------------------
     *  To get songs from the DJ playlist of the user
     * ------------------------------------------------------
     */

      exports.getDjPlayListFromUserId = function(userId,callback) {

        var sql = "SELECT playlist_order FROM tb_users WHERE user_id=? LIMIT 1";
        connection.query(sql, [userId], function(err, responsePlaylistOrder) {

        var playlistOrder = responsePlaylistOrder[0].playlist_order;
        var lastWord = playlistOrder.slice(-1);
        
        if(lastWord ==',')
            {
                playlistOrder = playlistOrder.substring(0, playlistOrder.length - 1);
            }
            
        
        getDjPlaylist(playlistOrder,userId,function(responseDj)
    {

        if (responseDj == 0) {
            var response = [];
            return callback(response);
        } else if (responseDj == 1) {
            var response = [];
            return callback(response);
        }
        else
            {
           var responseDjLength = responseDj.length;
          for(var i=0; i<responseDjLength; i++)
          {
            responseDj[i].song_id = responseDj[i].playlist_id;
            delete(responseDj[i].playlist_id);

            if (responseDj[i].song_name == '') {
                delete(responseDj[i]);
            }
        }
        return callback(responseDj);
    }
    });
        });
     }


    function getDjPlaylist(playlistOrder,userId,callback)
    {

        if (playlistOrder == "") {

            var sql = "SELECT playlist_id,song_name,song_artist,song_image,song_link,song_itunes_link,session_id,song_status FROM tb_user_playlist WHERE user_id=? and song_name!=? ORDER BY playlist_created_datetime desc";
            connection.query(sql, [userId, ''], function(err, responseDj) {

                var responseDjLength = responseDj.length;
                if (responseDjLength == 0) {
                    return callback(0);
                }
                else if(responseDjLength == 1 && responseDj[0].song_name == "")
                    {
                        return callback(1);
                    }
                else{
                return callback(responseDj);
                }
            });

        } else {

            var sql = "SELECT playlist_id,song_name,song_artist,song_image,song_link,song_itunes_link,session_id,song_status FROM tb_user_playlist WHERE user_id=? and song_name!=? ORDER BY FIELD(playlist_id,"+playlistOrder+")";
            connection.query(sql, [userId, ''], function(err, responseDj) {

                var responseDjLength = responseDj.length;
               if (responseDjLength == 0) {
                    return callback(0);
                }
                else if(responseDjLength == 1 && responseDj[0].song_name == "")
                    {
                        return callback(1);
                    }
                else
                {
                return callback(responseDj);
                }
            });

        }
    }
    /*
 * -----------------------------------------------------------------------------
 * sorting an array in ascending order
 * INPUT : array and key according to which sorting is to be done
 * OUTPUT : sorted array
 * -----------------------------------------------------------------------------
 */
exports.sortByKeyAsc = function(array, key) {
    return array.sort(function(a, b) {
        var x = a[key];
        var y = b[key];
        return ((x < y) ? -1 : ((x > y) ? 1 : 0));
    });
};
/*
 * -----------------------------------------------------------------------------
 * sorting an array in descending order
 * INPUT : array and key according to which sorting is to be done
 * OUTPUT : sorted array
 * -----------------------------------------------------------------------------
 */
exports.sortByKeyDesc = function(array, key) {
    return array.sort(function(a, b) {
        var x = a[key];
        var y = b[key];
        return ((x > y) ? -1 : ((x < y) ? 1 : 0));
    });
};
/*
 * -----------------------------------------------------------------------------
 * Uploading image to s3 bucket
 * INPUT : file parameter
 * OUTPUT : image path
 * -----------------------------------------------------------------------------
 */
exports.uploadImageToS3Bucket = function(file, folder, callback)
{
    var fs = require('fs');
     var mathjs = require('mathjs');
    math = mathjs();
    
    var AWS = require('aws-sdk');

   
        var filename = file.name; // actual filename of file
        var path = file.path; //will be put into a temp directory
        var mimeType = file.type;

        fs.readFile(path, function(error, file_buffer) {
            if (error)
            {
                return callback(0);
            }
            
            filename = file.name;

            AWS.config.update({accessKeyId: 'AKIAIQWM7VJLD5LBFISQ', secretAccessKey: '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'});
            var s3bucket = new AWS.S3();
            var params = {Bucket: 'boom-botix', Key: folder + '/' + filename, Body: file_buffer, ACL: 'public-read', ContentType: mimeType};

            s3bucket.putObject(params, function(err, data) {
                if (err)
                {
                    return callback(0);
                }
                return callback(filename);
            });
        });
    
};

 /*
     * ------------------------------------------------------
     *  to find distance between logitude and latitude
     * ------------------------------------------------------
     */

      exports.toMiles=function(lat1, lon1, lat2, lon2) {
        // Formula for calculating distances
        // from latitude and longitude.
        var dist = require('geo-distance-js');

        var distance = dist.getDistance(lat1, lon1, lat2, lon2);

        var distanceInMiles = (0.000621371 * distance);

        return distanceInMiles;
    };
    
    /*
 * -----------------------------------------------------------------------------
 * Sending push notification to devices
 * INPUT : iosDeviceToken,message
 * OUTPUT : Notification send
 * -----------------------------------------------------------------------------
 */
exports.sendPushNotification = function(iosDeviceToken, message)
{

    var apns = require('apn');

    var options = {
        cert: 'boombotix.pem',
        certData: null,
        key: 'boombotix.pem',
        keyData: null,
        passphrase: 'click',
        ca: null,
        pfx: null,
        pfxData: null,
        gateway: 'gateway.push.apple.com',
        port: 2195,
        rejectUnauthorized: true,
        enhanced: true,
        cacheLength: 100,
        autoAdjustCache: true,
        connectionTimeout: 0,
        ssl: true
    }

    
        var deviceToken = new apns.Device(iosDeviceToken);
        var apnsConnection = new apns.Connection(options);
        var note = new apns.Notification();

       
        note.sound = 'ping.aiff';
        note.alert = message;
       
        apnsConnection.pushNotification(note, deviceToken);

// i handle these events to confirm the notification gets
// transmitted to the APN server or find error if any

        function log(type) {
            return function() {
                console.log(type, arguments);
            }
        }

        apnsConnection.on('transmitted', function() {

        });

        apnsConnection.on('error', log('error'));
        apnsConnection.on('transmitted', log('transmitted'));
        apnsConnection.on('timeout', log('timeout'));
        apnsConnection.on('connected', log('connected'));
        apnsConnection.on('disconnected', log('disconnected'));
        apnsConnection.on('socketError', log('socketError'));
        apnsConnection.on('transmissionError', log('transmissionError'));
        apnsConnection.on('cacheTooSmall', log('cacheTooSmall'));
    
};