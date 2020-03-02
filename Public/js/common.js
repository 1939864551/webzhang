/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


var urlbefore = "/mare/index.php/Home/";

// var passwordreg = new RegExp("^(?=.{6,})(((?=.*[A-Z])(?=.*[a-z]))|((?=.*[A-Z])(?=.*[0-9]))|((?=.*[a-z])(?=.*[0-9]))).*$");
// var passwordreg = /^(?=.{6,})(((?=.*[A-Z])(?=.*[a-z]))|((?=.*[A-Z])(?=.*[0-9]))|((?=.*[a-z])(?=.*[0-9]))).*$/g;
//密码大小写和数字混合表示至少6个字符，必须同时含有大写字母和小写字母；或者同时有大写字母和小写字母;或者同时有小写字母和数字
var passwordreg = /^(?=.{6,}$)((?!.*\s)(?=.*[A-Z])(?=.*[a-z]))(?=(1)(?=.*\d)|.*[A-Za-z0-9]).*$/;

var userReg = /[`~!@#$%^&*_+<>{}\/'[\]]/im;//检查是否有特殊字符

var phone = /^1\d{10}$/gi;//检测手机号码是否符合要求

/**
 * 提示信息
 * @param type alert-danger(1警告)， alert-success（2成功）
 *@author qxn
 * */
function alert_msg(msg, obj, type) {
    // alert(msg); return;
    // alert(arguments[1]);return;
    if (type == 1) {
        var type_show = 'alert-danger';
    } else {
        var type_show = 'alert-success';
    }
    var info = '<div id="action-tips" class="alert fade in ' + type_show + ' global-tips" role="alert">' + msg + '</div>';
    $(".block-wrapper").append(info);
    setTimeout(function () {
        $("#action-tips").alert("close");
    }, 3000)
    if (obj != '') {
        obj.focus();
    }
}