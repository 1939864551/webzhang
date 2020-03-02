var urlbefore = "http://127.0.0.1/mare/index.php/Home/";
var getUrl = window.location.href;
var appid = getUrl.slice(getUrl.indexOf("appid/") + 6);

var initTable = function (ele, id) {

    var getProject = $(".project");
    var ttt;
    var datahead;
    if(id == 'communicationrequest'){
        datahead = [
            {data: "tid"},
            {data: "host"},
            {data: "time"},
            {data: "operation"}
        ];
    }else{
        datahead = [
            {data: "id"},
            // { data: "appname"},
            // { data: "zhtestname" },
            {data: "zhtestname"},
            {data: "resultname"},
            // { data: "uploaddate" },
            {data: "lastupgraddate"},
            // { data: "completionstatus"},
            {data: "operation"}
        ];
    }

    $(ele).DataTable({
        "processing": true,
        "ajax": {
            "type": "post",
            "url": urlbefore + "Info/" + id + "/appid/" + appid + ".html"
        },
        "columns": datahead,
        "language": {
            // url:"datajson/table-language.json"
            url: urlbefore + "Layout/tablelanguage"
        },
        "autoWidth": false,
        "pageLength": 20,
        "destroy": true
    });
}

//第一次浏览页面时读取
var getAjaxHtml = function (page) {
    $.ajax({
        type: "get",
        url: urlbefore + "Layout/" + page + "/appid/" + appid + ".html",
        async: true,
        data: page,
        dataType: "json",
        success: function (data1) {
            // data1.replace('\\','');
            // console.log(data1);
            $("#ajax-content").html(data1.content);
            $(".project-title").html(data1.title);
            $(".breadcrumb").html(data1.breadcrumb);
            var PaneId = $(".tab-content .active").attr("id");

            $("#ajax-content .nav-tabs li a").click(function (e) {
                e.preventDefault();
                var getUrl = $(this).attr("href");
                var getKey = getUrl.slice(getUrl.indexOf("#") + 1);

                clickAjaxHtml(getKey);
            });
            //获取需要激活的table
            var getTable = "#" + PaneId + " table";
            //激活dataTable
            initTable(getTable, PaneId);
        },
        error: function () {
            $("#ajax-content").html("暂无数据内容");
        }
    });
}

//点击tab请求ajax数据
var clickAjaxHtml = function (page) {
    //获取需要激活的table
    var getTabTable = "#" + page + " table";
    //激活dataTable
    initTable(getTabTable, page);   
}

var menuFn = function () {//点击左侧menu进行ajax内容切换
    var oldUrl = getUrl.slice(getUrl.indexOf("#") + 1);
    oldUrl = oldUrl.slice(oldUrl.indexOf("/") + 1);
    var getProject = $(".project");
    var getTargetId;
    getProject.click(function (e) {
        e.preventDefault();
        if ($(this).hasClass('active')) {
            return false;
        }
        else {
            $(this).parent("li").siblings().removeClass("active");
            $(this).parent("li").addClass("active");
            var getActiveTabText = $(".center-menu .active").text();//修改导航条的文字
            $(".breadcrumb .active").text(getActiveTabText);
            getTargetId = $(this).attr("id");
            // getTargetId = getTargetId + getUrl.slice(getUrl.indexOf("#")+1)

            // if(getUrl.slice(getUrl.indexOf("#")+1,getUrl.indexOf("appid/")-1) != 'server'){
            getAjaxHtml(getTargetId + '/' + oldUrl);//读取页面内容:环境安全、应用安全监等
            // }else{
            // 	location.href = getUrl.slice(0,getUrl.indexOf("#")+1)+getTargetId+'/'+oldUrl;
            // }			
            window.location.hash = "#" + getTargetId + '/' + oldUrl;//点击菜单修改url

            //当小屏幕并侧栏是开启状态，点击项目恢复侧栏隐藏状态，隐藏遮罩层，恢复菜单按钮样式
            if (sidebarContainer.hasClass("active")) {
                menuBtn.removeClass("active");
                sidebarContainer.removeClass("active");
                mask.fadeOut();
            }
        }
    });
}

var menuAction = function (KeyWord) {
    var newKeyWord = KeyWord.slice(0, KeyWord.indexOf('/'));
    var getName = ".center-menu #" + newKeyWord;//获取当前tab a的id
    var getCurrentTab = $(getName).parent("li");
    getCurrentTab.addClass("active");//第一次读取页面时根据当前显示内容，左侧menu跟随变动
    var getCurrentTabText = getCurrentTab.text();//获取当前Tab的文本内容：环境安全、应用安全监测等。
    // alert(getCurrentTabText);
    $(".breadcrumb .active").text(getCurrentTabText);//修改导航条文字
    getAjaxHtml(KeyWord);
}

var submitForm = function () {
    // var gettextInput 		= UE.getEditor('editor').getContent();
    var gettextInput = $('#editor').val();
    var detectionid = $('input[name=detectionid]').val();
    var selectval = $('select[name=result]').val();

    if (gettextInput == "") {
        var showMessage = "请输入数据";
        $(".modal-body p").html(showMessage);
        $("#alert-message").modal('show');
        return false;
    }
    if (selectval == '0') {
        $(".modal-body p").html("<p style='text-align:center;'>请先评定等级,不能为未评定</p>");
        $("#alert-message").modal('show');
    } else {
        $.ajax({
            type: "post",
            data: {
                'content': gettextInput,
                'detectionid': detectionid,
                'appid': appid,
                'result': selectval,
            },
            url: urlbefore + "Index/analysisDesc.html",
            async: true,
            dataType: "json",
            success: function (data2) {
                if (data2.code == 'success') {
                    // $('.submitform').hide();
                    $(".modal-body p").html("<p style='text-align:center;'>" + data2.info + "</p>");
                    
                } else if (data2.code == 'falseNoConfirm') {
                    $(".modal-body p").html("<p style='text-align:center;'>" + data2.info + "</p>");
                    
                } else {
                    $(".modal-body p").html("<p style='text-align:center;'>提交表单错误</p>");
                    
                }
                $("#alert-message").modal('show');
                $('#alert-message .modal-footer .btn-default').click(function(){
                    location.reload();
                });
            }
        });
    }

}

//点击查看,获取检测项的检测信息
var confirmAction = function (detectionid, recordid) {
    var selectname = $('select[name=operation_' + detectionid + ']').val();
    $.ajax({
        type: "post",
        url: urlbefore + "Index/testdetial/recordid/" + recordid + "/appid/" + appid + ".html",
        async: true,
        success: function (datacontent) {
            $("#ajax-content").html(datacontent);
            $.ajax({
                type: "post",
                dataType: "json",
                url: urlbefore + "Info/detecdeteailinfo/recordid/" + recordid + "/appid/" + appid + "/detectionid/" + detectionid + ".html",
                async: true,
                success: function (datacontent1) {
                    //将检测的数据和需要确认及误报的内容放在 detail-content 中
                    $('.detail-content').html(datacontent1);
                    // console.log(datacontent1);
                    //将评论的数据放在评论框里
                    $.ajax({
                        type: "post",
                        data: {
                            'detectionid': detectionid,
                            'appid': appid,
                        },
                        url: urlbefore + "Index/retestexplain.html",
                        async: true,
                        dataType: "json",
                        success: function (data4) {
                            console.log(data4);
                            if (data4.code == 'success') {
                                $('#oldansysicform').show();
                                $('#oldansysic').html(data4.info);
                            }else{
                                $('#oldansysicform').hide();
                            }
                        }
                    });

                    //检测的评级
                    $.ajax({
                        type: "post",
                        data: {
                            'detectionid': $('input[name=detectionid]').val(),
                            'appid': appid,
                        },
                        url: urlbefore + "Index/detecResultShow.html",
                        async: true,
                        dataType: "json",
                        success: function (data6) {
                            if (data6.code == 'success') {
                                $("select[name=result]").find("option[value='" + data6.info + "']").attr("selected", true);
                                //评级后是否显示提交表单按钮，即评级的功能是否显示
                                ColXs3();
                            }
                        }
                    });
                    //是否显示文本提交框,即评级功能是否开放

                },
                error: function () {

                }
            });
            $(".breadcrumb li a").click(function (e) {
                e.preventDefault();
                var getTargetId = $(this).attr("id");
                getAjaxHtml(getTargetId);//读取页面内容:环境安全、应用安全监等
            });
        },
        error: function () {

        }
    });
}
//点击查看,获取检测项的检测信息
var confirmActionHost = function (recordid) {
    $.ajax({
        type: "post",
        url: urlbefore + "Index/testdetial/recordid/" + recordid + "/appid/" + appid + ".html",
        async: true,
        success: function (datacontent) {
            $("#ajax-content").html(datacontent);
            $.ajax({
                type: "post",
                dataType: "json",
                url: urlbefore + "Info/detecdeteailinfohost/recordid/" + recordid + "/appid/" + appid + ".html",
                async: true,
                success: function (datacontent1) {
                    //将检测的数据和需要确认及误报的内容放在 detail-content 中
                    $('.detail-content').html(datacontent1);
                    $('#public-text').remove();
                    $('#detail-list').removeClass('col-md-8').addClass('col-md-12');
                    
                    //是否显示文本提交框,即评级功能是否开放

                },
                error: function () {

                }
            });
            $(".breadcrumb li a").click(function (e) {
                e.preventDefault();
                var getTargetId = $(this).attr("id");
                getAjaxHtml(getTargetId);//读取页面内容:环境安全、应用安全监等
            });
        },
        error: function () {

        }
    });

}


//判断是否存在 step-des 下的 col-xs-3 class类,用于是否显示提交说明按钮的方式
function ColXs3() {

    //如果存在col-xs-3类 则显示 submitform类
    if ($('.step-des').hasClass('col-xs-3') == true) {
        $('.submitform').show();
    }

}


var menuBtn = $(".menu-btn"),
        mask = $(".mask"),
        sidebarContainer = $(".sidebar-container");
//启动menu
var toggleMenu = function () {
    menuBtn.toggleClass("active");
    sidebarContainer.toggleClass("active");

    if (sidebarContainer.hasClass("active")) {
        mask.fadeIn();
    }
    else {
        mask.fadeOut();
    }
}

//缩略图点击显示大图
function fancyImgBox(e) {
    var getImgUrl = $(this).find("img").attr("src");
    $(".fancy-img-wrapper").find(".fancy-img-box img").attr("src", getImgUrl);
    $(".fancy-img-wrapper").fadeIn();
    closeFancyBox();
}
//关闭大图
function closeFancyBox() {
    $(".close-box").on("click", function () {
        $(".fancy-img-wrapper").fadeOut();
    });
    $(".fancy-img-wrapper").on("click", function () {
        $(".fancy-img-wrapper").fadeOut();
    });
}

//testdetial 点击确认是否误报
//status 状态值
//tablename 表名
//recordid 记录id
function clickConfirmStatus(status, tablename, recordid) {
    var detectionid = $('input[name=detectionid]').val();
    var statusvalue;
    if (status == 'confirm') {
        statusvalue = 2;
    }
    if (status == 'error') {
        statusvalue = 3;
    }
    var divid = 'section-' + tablename + '-' + recordid;
    // console.log($('.detail-content').html());;
    if (statusvalue != 'undefined') {
        $.ajax({
            type: "post",
            url: urlbefore + "Index/appconfirmstatus/detectionid/" + detectionid + "/confirmstatus/" + statusvalue + "/appid/" + appid + "/tablename/" + tablename + "/recordid/" + recordid + ".html",
            async: true,
            success: function (datacontent1) {
                if (datacontent1.code == 'success') {
                    if (statusvalue == 2) {
                        $(".modal-body p").html("<p style='text-align:center;'>执行确认操作成功</p>");
                        $("#alert-message").modal('show');
                        $('#' + divid).children('.step-des').children('.col-xs-12').remove();
                    }
                    if (statusvalue == 3) {
                        $(".modal-body p").html("<p style='text-align:center;'>执行误报操作成功</p>");
                        $("#alert-message").modal('show');
                        // $('#'+divid).children('.step-des').children('.col-xs-12').remove();
                        $('#' + divid).children('.step-des').children('.col-xs-12').remove();
                    }
                } else {
                    if (statusvalue == 2) {
                        $(".modal-body p").html("<p style='text-align:center;'>执行确认操作失败</p>");
                        $("#alert-message").modal('show');
                    }
                    if (statusvalue == 3) {
                        $(".modal-body p").html("<p style='text-align:center;'>执行误报操作失败</p>");
                        $("#alert-message").modal('show');
                    }
                }
            },
            error: function () {
                alert('false');
            }
        });
    }
}

//不能下载报告的提示
function notallowdownreport() {
    $(".modal-body p").html("<p style='text-align:center;'>检测尚未完成,请等待检测完成</p>");
    $("#alert-message").modal('show');
}

$(document).ready(function () {

    $(".menu-btn").on("click", toggleMenu);//点击menu按钮打开菜单

    $("#ajax-content").on("click", ".thumbnail-img-wrapper", fancyImgBox);//点击显示大图



    var KeyWord = getUrl.slice(getUrl.indexOf("#") + 1);//获取url中#后方的key

    if (KeyWord != "") {
        menuAction(KeyWord);
    } else if (KeyWord == "") {
        KeyWord = "environment";
        menuAction(KeyWord);
    }
    menuFn();

    $(".menu-btn").on("click", toggleMenu);//点击menu按钮打开菜单
});
