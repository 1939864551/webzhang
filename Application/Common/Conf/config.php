<?php
return  array(
    'LOAD_EXT_CONFIG'                   => 'db', 
    'REGISTER_MODE'                     => 0, //注册模块开关
    'ADUSER_MODE'                       => 0, //域用户模块开关
    'USB_MODE'                          => 0, //跑USB脚本开关
    'ISDYNAMIC_MODE'                    => 1, //是否动态检测开关
    'IS_PROPOSAL'                       => 0, //是否开启导出通报报告
    'HTTP_HEAD'                         => "http://",
    'HTTPS_HEAD'                        => "https://",
    'MODULE_ALLOW_LIST'                 => array('Android','Mare','sapi','UCenter','Mare1'),
    'URL_MODULE_MAP'                    => array('sapi'=>'Api'),//模块映射
    // 'DEFAULT_MODULE'        =>  'Android',
    'DEFAULT_MODULE'                    =>  'Android',
    'URL_CASE_INSENSITIVE'              =>  true,
    // 'MODULE_DENY_LIST'      => array('Web'),
    //模板缓存
    'TMPL_CACHE_ON'                     =>  false,

    'USERNAME_PASS_HAS_NULL'            =>  '用户名或密码为空',
    'LOGIN_ERROR'                       =>  '用户名或密码错误',
    'METHOD_TIP'                        =>  '请使用POST方式传输数据JSON数据',
    'POST_METHOD'                       =>  '请使用POST方式传递数据',
    'NO_DATA'                           =>  '暂无数据',
    'USERID_NEED'                       =>  '没有获取到userid的值',
    'USER_NOT_EXIST'                    =>  '不存在该用户',
    'USER_NOT_USEFUL'                   =>  '该账户不能使用！',
    'USER_ALERDY_EXIST'                 =>  '已经存在用户,请重新添加',
    'UPLOAD_FALSE'                      =>  '上传失败',
    'APPID_USERID_NULL'                 =>  '应用ID或用户ID不能为空',
    'USER_OLD_NEW_PWD'                  =>  '用户ID或旧密码或新密码不能为空',
    'UPDATE_PWD_ERROR'                  =>  '用户密码错误',
    'NEED_PARAMETER'                    =>  '传递的参数不够',
    'UPLOAD_IMG_FALSE'                  =>  '上传图片识别',
    'RECORD_ADD_SUCCESS'                =>  '上传记录成功',
    'RECORD_ADD_FALSE'                  =>  '上传记录操作失败',
    'IOS_APP_EXIST'                     =>  '该应用已经上传了',
    'DATA_VALIDATION_ERROR'             =>  '数据验证错误',
    'LOGIN_SUCCESS'                     =>  '登录成功',
    'DATE_TIME_REPEAT'                  =>  '时间和随机数重复',
    'DATA_UPDATE_SUCCESS'               =>  '数据更新成功',
    'DATA_UPDATE_FALSE'                 =>  '数据更新失败',
    'DATA_INSERT_SUCCESS'               =>  '数据添加成功',
    'DATA_INSERT_FALSE'                 =>  '数据添加失败',
    'REQUEST_DATA_SUCCESS'              =>  '获取数据成功',
    'REQUEST_DATA_FALSE'                =>  '获取数据失败',
    'DELETE_DATA_SUCCESS'               =>  '数据删除成功',
    'DELETE_DATA_FALSE'                 =>  '数据删除失败',
    'DETEC_SUCCESS'                     =>  '完成',
    'DETEC_FALSE'                       =>  '未完成',
    'PWD_HAVE_ZH'                       =>  '密码不能为中文',
    'REQUESTREPORT_ERROR'               =>  '提交的检测数据为空',
    'NOT_ALLOCATING_TASK'               =>  '未分配任务给测试人员',

    'OVERTIME'                          =>  '1800',
    'USERNAME_LENGTH_LESSORMORE'        =>  '用户的密码长度大于6位,且为字母和数字的组合！纯字母或纯数字均不满足!',
    'PWD_RULE'                          =>  '密码需要8-20位数字或字母',
    'LOGIN_OVERTIME'                    =>  '登录超时,无法操作请重新登录',
    'HASH_REPERT'                       =>  'HASH值重复',
    'DATA_REPERT'                       =>  '插入数据库的数据,有重复',
    'STATUS_ISNOT_MODIFY'               =>  '检测状态不能修改,已经确认',
    'USER_NOT_ENOUGH_OTHER'             =>  '不能操作其他用户的数据',
    'PARMAE_NOT_NULL'                   =>  '上述参数均不能为空',
    'NOT_APPID_TESTNAME'                =>  '不存在该应用下的对应检测结果项',
    'NOT_HAVE_WIFIFILE'                 =>  '不存在该WIFI配置文件',
    'LOGIN_PLATFROM_ERROR'              =>  '登录的平台错误',


    'ANALYST'                           =>  '分析人员',
    'MANAGERS'                          =>  '管理人员',
    'SYSTEMER'                          =>  '系统管理员',
    'APPNAME'                           =>  '应用名',
    'PACKAGENAME'                       =>  '包名',
    'DETECNAME'                         =>  '检测项名',
    'APPVERSION'                        =>  '应用版本',
    'APPVV'                             =>  '版本号',
    'RESULTNAME'                        =>  '危险程度',
    'UPLOADDATE'                        =>  '提交日期',
    'LASTUPGRADDATE'                    =>  '最后修改日期',
    'REQUEST_TIME'                      =>  '请求时间',
    'UPGRADDATE'                        =>  '修改日期',
    'APP_NOT_EXISTS'                    =>  '应用不存在',
    'DOMAIN'                            =>  '请求主机',
    'REQUEST_METHOD'                    =>  '请求方法',
    'DOMAIN_ADDRESS'                    =>  '请求地址',
    'USER_NAME'							=>  '用户名',
    'USER_NICKNAME'						=>	'用户昵称',
   	'OPERATION_TIME'					=>  '操作时间',
   	'OPERATION_IP'						=>	'操作IP',
   	'OPERATION_INFO'					=>	'操作信息',		
    
    'OPERATION'                         =>  '操作',
    'SUCCESS'                           =>  '成功',
    'FALSE'                             =>  '失败',
    'IMPLEMENT'                         =>  '执行',
    'MISINFORMATION'                    =>  '误报',
    'CONFIRM'                           =>  '确认',
    'NOT_EXIST_DETECITEM'               =>  '不存在该检测项',
    'RISKLEVEL'                         =>  array(0=>'<p class="unattuned"><i class="fa fa-question-circle-o"></i><span>暂未评级</span></p>',1=>'<p class="pass"><i class="fa fa-check-circle"></i><span style="color:#008200;">通过</span></p>',2=>'<p class="normal"><i class="fa fa-check-circle"></i><span style="color:#00BCD4;">低危</span></p>',3=>'<p class="warning"><i class="fa fa-close"></i><span style="color:#FF9800;">中危</span></p>',4=>'<p class="danger"><i class="fa fa-close"><span>高危</span></p>'),
    'RISKLEVEL_TRANS'                   =>  array(0=>'<span style="color:#99999;">暂未评级</span>',1=>'<span class="pass">通过</span>',2=>'<span class="normal">低危</span>',3=>'<span class="warning">中危</span>',4=>'<span class="danger">高危</span>'),
    'NOT_EXIST_CONTROLLER'              =>  '不存在该控制器',
    'RISK_ZH_TREANS'                    =>  array(0=>'暂未评级',1=>'通过',2=>'低危',3=>'中危',4=>'高危'),

    'STEP'                              =>  '步骤',
    'MESSAGE_DATA'                      =>  '报文数据',
    'SELECTZH'                          =>  '选项',
    'RESULTZH'                          =>  '结果',
    'EVALUATE_FALSE'                    =>  '评定结果失败',
    'PERMISSION_NOT_ENOUGH'             =>  '权限不够',
    'CCC'                               => '192.168.199.131:8080',
    'NOT_EXIST_LOGINUSERTYP'            =>  '不存在的登录类型',
    'RESULT_NOT_EVALUATE'               =>  '请先评定危险等级',
    'EDIT'                              =>  '编辑',
    'SEE'                               =>  '查看',
    'SERVER_TRANS_ZH'                   =>  array('issues_name'=>'漏洞类型','issues_severity'=>'风险等级','issues_vector_url'=>'URL','issues_vector_method'=>'请求方法','issues_vector_affected_input_name'=>'问题参数','issues_vector_affected_input_value'=>'漏洞POC','issues_proof'=>'漏洞证明','issues_request'=>'漏洞请求报文','issues_img_url'=>'截图信息'),
    'PORT_TRANS_ZH'                     => array('product'=>'软件名','service'=>'开发服务','product_version'=>'软件版本','port'=>'端口'),
    'NEED_SHOW_PORT'                    => array('product','service','product_version','port'),

    //模块缓存
    'TMPL_CACHE_ON'                     =>  false,

    //request表中的issue_name 为pass时，给他设置的提示
    'testresult'                        =>  '本次检测未发现相关安全问题',

    //检测项id
    'DETECTIONID_IS_NULL'               =>  '检测项ID不能为空',
    'DATA_STATUS_IS_JIA'                => '该状态不存在,有人篡改数据库',

    'SLEEP_TIP_NOT_DATA'                => '脚本休息去了,你也去休息一下吧！',
    'NEED_TABLENAME'                    => '需要表名信息,您的表名为空',
    'DETEC_NO_FINISH'                   => '检测尚未完成,请等待完成之后再下载报告',
    'PLEASE_CONFIRM_RECORD_STATUS'      => '请先确认记录的状态,是否误报等',
    'DETEC_COMPLATE_NO'                 =>  '还未检测完成',
    'USER_NOT_ALLOW_DEL_SELF'           => '用户不允许删除自己',
    '2PASS_DIFFERENT'                   =>'两次密码不相同',
    'IMAGE_IS_NULL'                     => '图片不能为空',
    'WORD_IS_NULL'                      => '文字描述不能为空',
    'ADD_USER_FALSE'                    => '添加用户失败',
    'WX_TASK_NOT_EXIST'                 => '不存在微信测试任务',

    //日志
    'LOGIN_USER_NAME'                   =>  "登录名为",
    'APP_DATA_NOT_EXIST'                =>  '的应用数据为空',
    'LOGIN_USER_PASS'                   =>  '登录密码',
    'LONIN_USER_PASS_ERROR'             =>  '用户登录错误!',
    'WIFI_CONNECT_SUCCESS'              =>  'WIFI链接成功!',
    'WIFI_CONNECT_FALSE'                =>  'WIFI链接失败!',
    'WIFI_WRITE_SUCCESS'                =>  'WIFI写入配置成功!',
    'WIFI_WRITE_FALSE'                  =>  'WIFI写入配置失败!',
    'TOP_TEXT'                          =>  'MARS',
    'TITLE_TEXT'                        =>  '海云安移动应用安全风险评估系统',
	
    //AD域验证配置
    'auth'=>'81F5E21E35407D884A6CD4A731AEBFB6AF209E1B',
    'domain'=>'sf.com',
    'basedn'=>'dc=sf,dc=com',
	
    //数据库备份路径
    'DATA_BACKUP_PATH'                  => './Data/',
    'DATA_BACKUP_PART_SIZE'             => 20,//数据库备份卷大小
    'DATA_BACKUP_COMPRESS'              => 1, //启用压缩
    'DATA_BACKUP_COMPRESS_LEVEL'        =>4, //压缩级别	
    
    'SECURITY_LEVEL_LIKE' => 'GA/T 1390.3 —2017 网络安全等级保护基本要求 ',
    
//     'DETECTION_ARR' => [1001,1002,1003],
    'DETECTION_ARR' => [],
    
    
);