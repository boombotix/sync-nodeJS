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
                   // var action = '<button type="button" class="btn-small btn-success" id="' + elem.app_id + '" onclick="productedit(this.id)">View</button>'

//                    if (elem.active_status == 1)
//                    {
//                        elem.active_status = '<input type="checkbox" checked="checked" value="' + elem.active_status + '">';
//                    }
//                    else
//                    {
//                        elem.active_status = '<input type="checkbox" value="' + elem.active_status + '">';
//                    }
//                    
//                    if (elem.admin_recommended == 1)
//                    {
//                        elem.admin_recommended = '<input type="checkbox" checked="checked" value="' + elem.admin_recommended + '">';
//                    }
//                    else
//                    {
//                        elem.admin_recommended = '<input type="checkbox" value="' + elem.admin_recommended + '">';
//                    }

                    elem.user_image = '<img src="http://boom-botix.s3.amazonaws.com/user_profile/' + elem.user_image + '" height="50" width="50">';

                    aDataSet1.push([i, elem.user_name,elem.user_image,elem.user_email]);
                    ++i;
                    
                });
                $('#example1').dataTable({
                    "aaData": aDataSet1,
                    "aoColumns": [
                        {"sTitle": "#", "sWidth": "20px"},                       
                        {"sTitle": "Name", "sWidth": "60px"},
                        {"sTitle": "Image", "sWidth": "40px"},
                        {"sTitle": "Email", "sWidth": "40px"},
//                        {"sTitle": "Description", "sWidth": "40px"},
//                        {"sTitle": "Package", "sWidth": "40px"},
//                        {"sTitle": "Updates", "sWidth": "40px"},
//                        {"sTitle": "Size", "sWidth": "60px"},
//                        {"sTitle": "Developed by", "sWidth": "53px"},
//                        {"sTitle": "What's new", "sWidth": "150px"},
//                        {"sTitle": "App link", "sWidth": "40px"},
//                        {"sTitle": "Upload date", "sWidth": "60px"},
//                        {"sTitle": "Downloads", "sWidth": "40px"},
//                        {"sTitle": "Action", "sWidth": "40px"}
                    ]
                });
                var strVar = "";
                strVar += "<tr>";
                strVar += "<th><input type=\"text\" name=\"search_sr\" value=\"#\" class=\"search_init\" style=\"width:20px\" onBlur=\"if(this.value=='') this.value='#'\" onFocus=\"if(this.value =='#') this.value=''\"\" \/><\/th>";
                strVar += "<th><input type=\"text\" name=\"search_sr\" value=\"Name\" class=\"search_init\" style=\"width:100px\" onBlur=\"if(this.value=='') this.value='Name'\" onFocus=\"if(this.value =='Name') this.value=''\"\" \/><\/th>";
                strVar += "<th><input type=\"text\" name=\"search_sr\" value=\"Image\" class=\"search_init\" style=\"width:40px;\display:none\" onBlur=\"if(this.value=='') this.value='Image'\" onFocus=\"if(this.value =='Image') this.value=''\"\" \/><\/th>";
                strVar += "<th><input type=\"text\" name=\"search_sr\" value=\"Email\" class=\"search_init\" style=\"width:100px\" onBlur=\"if(this.value=='') this.value='Email'\" onFocus=\"if(this.value =='Email') this.value=''\"\" \/><\/th>";
//                strVar += "<th><input type=\"text\" name=\"search_sr\" value=\"Description\" class=\"search_init\" style=\"width:34px\" onBlur=\"if(this.value=='') this.value='Description'\" onFocus=\"if(this.value =='Description') this.value=''\"\" \/><\/th>";
//                strVar += "<th><input type=\"text\" name=\"search_sr\" value=\"Package\" class=\"search_init\" style=\"width:30px\" onBlur=\"if(this.value=='') this.value='Package'\" onFocus=\"if(this.value =='Package') this.value=''\"\" \/><\/th>";
//                strVar += "<th><input type=\"text\" name=\"search_sr\" value=\"Updates\" class=\"search_init\" style=\"width:33px\" onBlur=\"if(this.value=='') this.value='Updates'\" onFocus=\"if(this.value =='Updates') this.value=''\"\" \/><\/th>";
//                strVar += "<th><input type=\"text\" name=\"search_sr\" value=\"Size\" class=\"search_init\" style=\"width:57px\" onBlur=\"if(this.value=='') this.value='Size'\" onFocus=\"if(this.value =='Size') this.value=''\"\" \/><\/th>";
//                strVar += "<th><input type=\"text\" name=\"search_sr\" value=\"Devloped by\" class=\"search_init\" style=\"width:53px\" onBlur=\"if(this.value=='') this.value='Devloped by'\" onFocus=\"if(this.value =='Devloped by') this.value=''\"\" \/><\/th>";
//                strVar += "<th><input type=\"text\" name=\"search_sr\" value=\"New\" class=\"search_init\" style=\"width:100px\" onBlur=\"if(this.value=='') this.value='New'\" onFocus=\"if(this.value =='New') this.value=''\"\" \/><\/th>";
//                strVar += "<th><input type=\"text\" name=\"search_sr\" value=\"App link\" class=\"search_init\" style=\"width:60px\" onBlur=\"if(this.value=='') this.value='App link'\" onFocus=\"if(this.value =='App link') this.value=''\"\" \/><\/th>";
//                strVar += "<th><input type=\"text\" name=\"search_sr\" value=\"date\" class=\"search_init\" style=\"width:40px\" onBlur=\"if(this.value=='') this.value='date'\" onFocus=\"if(this.value =='date') this.value=''\"\" \/><\/th>";
//                strVar += "<th><input type=\"text\" name=\"search_sr\" value=\"value\" class=\"search_init\" style=\"width:40px\" onBlur=\"if(this.value=='') this.value='value'\" onFocus=\"if(this.value =='value') this.value=''\"\" \/><\/th>";
//                strVar += "<th><\/th>";
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

function productedit(val)
{
    window.location.href = "products_upload.php?detailID=" + val;

}
