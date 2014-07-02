var check, userID, userName;
$(document).ready(function()
{
    var user_name = "clicklabs";
    if (user_name == null)
    {
        // if access token is not set in the cookies.
        $('#loader-main').show();
        $('.container').hide();
        location.href = 'index'
    }
    // if access token is set the execute this program.
    $('#loader').hide();
//    var url = window.location;
//    url = url.toString();
//
//    var arr = url.split("#");

    var page = 2;


    if (page == 2)
    {
        $('.nav.staff li.active').removeClass('active');
        $('#current').addClass('active');
        fs2();
    }

    $('.links').click(function() {
        var id = this.id;
        id = id.replace("fs", "");

        if (id == 2)
        {
            fs2();
        }
        else {

        }
    });


    // get all current products detail
    function doAjaxStuff2(callback) {

        $('#noproducts').hide();
        $('#loader-maindata').show();

        $('#maindata').empty();
        $.ajax({
            type: "POST",
            url: 'api-files/index.php?action=getusers',
            dataType: "json",
            data: {user_name: user_name},
            async: true,
            success: function(productsdata) {
                console.log(productsdata);
                callback(productsdata);
            },
            error: function(status) {
                alert("Server error! Please try again!")
            }
        });
    }

    function fs2()
    {
        // get all products detail

        doAjaxStuff2(function(productsdata) {
            $('#loader-maindata').hide();
            if (productsdata['log'] == 1)
            {
                $('#noapps').hide();
                var count = productsdata['data'].length;
                var i = 1;
                $('#maindata').html('<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"display table table-striped table-bordered\" id=\"example1\" style=\"font-size:13px\"></table>');
                /* Data set - can contain whatever information you want */
                var aDataSet1 = [];
                productsdata['data'].forEach(function(elem) {
                    //       var stat = "In Progress"

                    if (elem.is_playing == 1)
                    {
                        elem.is_playing = 'Yes';
                        var action = '<button type="button" style="margin-left:5px;margin-top:10px;" class="btn btn-small btn-inverse" id="1$$$' + elem.user_id + '" onclick="playaction(this.id)">Stop</button>'


                    }
                    else
                    {
                        elem.is_playing = 'No';
                        var action = '<button style="margin-left:5px;margin-top:10px;" type="button" class="btn btn-small btn-success" id="2$$$' + elem.user_id + '" onclick="playaction(this.id)">Play</button>'

                    }
                    action += '<button style="margin-left:5px;margin-top:10px;" type="button" class="btn btn-small btn-primary" value="' + elem.user_name + '" id="' + elem.user_id + '" onclick="useredit(this.id,this.value)">Edit</button>'


                    elem.user_image = '<img src="' + elem.user_image + '" height="50" width="50">';

                    aDataSet1.push([i, elem.user_name, elem.user_image, elem.user_email, elem.is_playing, action]);
                    ++i;

                });
                $('#example1').dataTable({
                    "aaData": aDataSet1,
                    "aoColumns": [
                        {"sTitle": "#", "sWidth": "20px"},
                        {"sTitle": "Name", "sWidth": "60px"},
                        {"sTitle": "Image", "sWidth": "40px"},
                        {"sTitle": "Email", "sWidth": "40px"},
                        {"sTitle": "Playing", "sWidth": "40px"},
                        {"sTitle": "Action", "sWidth": "40px"}

                    ]
                });
                var strVar = "";
                strVar += "<tr>";
                strVar += "<th><input type=\"text\" name=\"search_sr\" value=\"#\" class=\"search_init\" style=\"width:20px\" onBlur=\"if(this.value=='') this.value='#'\" onFocus=\"if(this.value =='#') this.value=''\"\" \/><\/th>";
                strVar += "<th><input type=\"text\" name=\"search_sr\" value=\"Name\" class=\"search_init\" style=\"width:100px\" onBlur=\"if(this.value=='') this.value='Name'\" onFocus=\"if(this.value =='Name') this.value=''\"\" \/><\/th>";
                strVar += "<th><input type=\"text\" name=\"search_sr\" value=\"Image\" class=\"search_init\" style=\"width:40px;\display:none\" onBlur=\"if(this.value=='') this.value='Image'\" onFocus=\"if(this.value =='Image') this.value=''\"\" \/><\/th>";
                strVar += "<th><input type=\"text\" name=\"search_sr\" value=\"Email\" class=\"search_init\" style=\"width:100px\" onBlur=\"if(this.value=='') this.value='Email'\" onFocus=\"if(this.value =='Email') this.value=''\"\" \/><\/th>";
                strVar += "<th><input type=\"text\" name=\"search_sr\" value=\"Playing\" class=\"search_init\" style=\"width:100px\" onBlur=\"if(this.value=='') this.value='Playing'\" onFocus=\"if(this.value =='Playing') this.value=''\"\" \/><\/th>";
                strVar += "<th></th>";
                strVar += "<\/tr>";
                $("#example1 thead").prepend(strVar);
            }
            else
            {
                $('#maindata').empty();
                $('#noapps').show();
            }
        });

        //.trigger('click');
    }







    // for submenu active status.
    $('.nav li').on('click', function() {
        $('.nav.staff li.active').removeClass('active');
        $(this).addClass('active');
    });
    //=====================================================================================
    $.fn.dataTableExt.oApi.fnFilterAll = function(oSettings, sInput, iColumn, bRegex, bSmart) {
        var settings = $.fn.dataTableSettings;

        for (var i = 0; i < settings.length; i++) {
            settings[i].oInstance.fnFilter(sInput, iColumn, bRegex, bSmart);
        }
    };

    var oTable1 = $('#example1').dataTable();
    $("body").on('keyup', '#example1 thead input', function() {
        oTable1.fnFilterAll(this.value, $(".search_init").index(this));
    });




//=====================================================================
});

function playaction(val)
{
    var test = val.split("$$$")

    check = test[0];
    userID = test[1];

    if (check == 1)
    {

        $("#user-action-data").html("Stop the user?");
        $("#user-action-data").css("width", "144px");
    }
    else if (check == 2)
    {

        $("#user-action-data").html("Play the user?");
        $("#user-action-data").css("width", "139px");
    }
    $("#useractionmodal").modal('show');

}

function useredit(id, name)
{
    userID = id;
    userName = name;
     $("#user_edit_name").val(userName);
      $("#useredit").modal('show');

}