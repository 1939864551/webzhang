<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html lang="zh">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"> 
        <meta name="viewport" content="width=device-width, initial-scale=1.0,user-scalable=no">
        <script src="/mare/Public/js/jquery-1.12.3.min.js" type="text/javascript" charset="utf-8"></script>
        <title><?php echo (C("TITLE_TEXT")); ?></title>
        <link rel="stylesheet" type="text/css" href="/mare/Public/css/bootstrap.css"/>
        <link rel="stylesheet" type="text/css" href="/mare/Public/css/font-awesome.min.css"/>
        <link rel="stylesheet" type="text/css" href="/mare/Public/css/login/login.css">
        <!--[if IE]>
                <script src="http://libs.useso.com/js/html5shiv/3.7/html5shiv.min.js"></script>
        <![endif]-->

        <style type="text/css">
        body{
            overflow: hidden;
            position: relative;
            touch-action: none;
        }
            .group label{
                color: #FFFFFF;
                margin-top: 5px;
                font-size: 14px;
            }
        </style>
    </head>
    <body>
        <div class="login-container">
        </div>
            <div class="login-wrap">
                <div class="login-html">
                    <div class="login-html">
                        <?php if(C("REGISTER_MODE")== 1): ?><a href="<?php echo U('Mare/Login/register_index');?>" style="float:right;color:white;margin:8px;"><注册></a><?php endif; ?>
                        <form action="<?php echo U('Mare/Login/login_user');?>" method="post" id="login-form"  style="margin-top:30px;">
                            <div class="login-content">
                                <h2><?php echo (C("TOP_TEXT")); ?></h2>
                                <div class="form-group">
                                    <input id="username" name="username" type="text" class="input"  value="" placeholder="用户名" required="required">
                                </div>
                                <div class="form-group">
                                    <input id="userpasssword" name="password" type="password" class="input" data-type="password" placeholder="密码" required="required">
                                </div>
                                <?php if($login_captcha == 'on'): ?>
                                <div class="form-group" >
                                    <input  style="float: left; width:60%;height:45px;" id="captcha" name="captcha" type="text" class="input" data-type="text" placeholder="验证码" required="required">
                                    <image style="float: left;width:40%;height:45px;" id='captchaimg' src="/mare/index.php/Mare/Layout/create_other_captcha" onclick="javascript:this.src = this.src +'?' + Math.random();" width="150px"/>
                                </div>
                                <?php endif; ?>
                                <div class="form-group">
                                    <!-- <input id="check" type="checkbox" class="check">
                                    <label for="check"><span class="icon"></span> 记住我</label> --><label id='codetip'><font color="red"></font></label><label id='captchatid'><font color="red"></font></label>
                                </div>
                                <div class="form-group">
                                    <button class="btn btn-grey btn-large center-block submit-btn" onclick="loginsubmit()" type="button">登录</button>
                                </div>
                                <p class="text-center"><?php echo (C("TITLE_TEXT")); ?></p>
                            </div>
                        </form>
                    </div>
                </div>	
            </div>
        
    <!--<div class="mask" id="mask-overlay">&nbsp;</div>
<script src="/mare/Public/mars/js/lib/jquery/jquery-1.11.0.js" type="text/javascript" charset="utf-8"></script>
<script src="/mare/Public/mars/js/lib/bootstrap/bootstrap.min.js" type="text/javascript" charset="utf-8"></script>
<script src="/mare/Public/mars/js/lib/scrollreveal/scrollreveal.min.js" type="text/javascript" charset="utf-8"></script>
<script src="/mare/Public/mars/js/lib/smoothsroll/SmoothScroll.js" type="text/javascript" charset="utf-8"></script>
<script src="/mare/Public/mars/js/script/globalscript.js" type="text/javascript" charset="utf-8"></script>
<script src="/mare/Public/js/common.js" type="text/javascript" charset="utf-8"></script>-->
    <script src="/mare/Public/mars/js/lib/three/three.min.js"></script>
    <script src="/mare/Public/mars/js/lib/three/OrbitControls.js"></script>
    <script src="/mare/Public/mars/js/lib/three/Mirror.js"></script>
    <script src="/mare/Public/mars/js/lib/three/WaterShader.js"></script>
    <script src="/mare/Public/mars/js/lib/three/SkyShader.js"></script>

        <!-- <script>
            var container, stats;
            var camera, scene, renderer;

            var waterNormals;
            var envTexture;
            var azimuth = .45843;
            var inclination = .3011;

            var loader = new THREE.TextureLoader();
            var clock = new THREE.Clock();

            var time = 0;
            var uniforms;
            var v;
            var light;
            var rusty;

            loader.load('/mare/Public/mars/img/waternormals.jpg', function(t) {
                t.mapping = THREE.UVMapping;
                waterNormals = t;
                waterNormals.wrapS = waterNormals.wrapT = THREE.RepeatWrapping;
                rusty = loader.load('/mare/Public/mars/img/tex02.jpg')
                rusty.wrapS = rusty.wrapT = THREE.RepeatWrapping;

                init();
                animate();
            })

            function initSky() {

                // Add Sky Mesh
                sky = new THREE.Sky();
                scene.add(sky.mesh);

                // Add Sun Helper
                sunSphere = new THREE.Mesh(
                    new THREE.SphereBufferGeometry(20, 16, 8),
                    new THREE.MeshBasicMaterial({
                        color: 0xffffff
                    })
                );
                sunSphere.visible = false;
                scene.add(sunSphere);

                uniforms = sky.uniforms;
                uniforms.turbidity.value = 0.01;//混浊度，数值越高，越混浊
                uniforms.rayleigh.value = 0.5066;//天空的颜色深度,数值越高颜色越亮
                uniforms.luminance.value = 0.8;//亮度，0为最亮，1为最暗
                uniforms.mieCoefficient.value = 10.1;//光源散射系数
                uniforms.mieDirectionalG.value = 0.224;//散射方向

                moveSun();
            }

            function moveSun() {
                var distance = 4500;

                var theta = Math.PI * (inclination - 0.5);
                var phi = 2 * Math.PI * (azimuth - 0.5);

                sunSphere.position.x = distance * Math.cos(phi);
                sunSphere.position.y = distance * Math.sin(phi) * Math.sin(theta);
                sunSphere.position.z = distance * Math.sin(phi) * Math.cos(theta);

                sky.uniforms.sunPosition.value.copy(sunSphere.position);
            }

            function init() {
                container = document.createElement('div');
                container.style.position = "fixed";
                container.style.zIndex = 1;
                container.style.top = 0;
                container.style.left = 0;
                document.body.appendChild(container);
                renderer = new THREE.WebGLRenderer({
                    antialias: true
                });
                renderer.setPixelRatio(window.devicePixelRatio);
                renderer.setSize(window.innerWidth, window.innerHeight);
                renderer.shadowMap.enabled = true;
                renderer.shadowMap.type = THREE.PCFSoftShadowMap;

                container.appendChild(renderer.domElement);
                scene = new THREE.Scene();

                camera = new THREE.PerspectiveCamera(55, window.innerWidth / window.innerHeight, 0.5, 3000000);
                camera.position.set(0, 15, 0);
                camera.rotation.set(-0, 0, 0);

                initSky();

                water = new THREE.Water(renderer, camera, scene, {
                    textureWidth: 512,
                    textureHeight: 512,
                    waterNormals: waterNormals,
                    alpha: 1.0,
                    sunDirection: sky.uniforms.sunPosition.value.normalize(),
                    sunColor: 0xf5ebce,
                    waterColor: 0x5b899b,
                    distortionScale: 15.0,
                });
                mirrorMesh = new THREE.Mesh(
                    new THREE.PlaneGeometry(4400, 4400, 120, 120),
                    water.material
                );
                mirrorMesh.add(water);
                mirrorMesh.rotation.x = -Math.PI * 0.5;
                scene.add(mirrorMesh);

                v = mirrorMesh.geometry.vertices;

                //LIGHT
                var ambient = new THREE.AmbientLight(0xf5ebce, 0.25);
                scene.add(ambient);

                light = new THREE.DirectionalLight(0xf5ebce, 0.8);
                light.position.set(0, 0, 0);

                light.castShadow = true;
                light.shadow = new THREE.LightShadow(new THREE.PerspectiveCamera(40, .7, 4000, 4800));
                light.shadow.bias = 0.0000001;
                light.shadow.mapSize.width = 2048;
                light.shadow.mapSize.height = 2048;
                scene.add(light);

                n = 110;
                var elementMaterial = new THREE.MeshPhongMaterial({
                    specular: 0xf5ebce,
                    shininess: 23,
                    specularMap: rusty,
                    shading: THREE.FlatShading,
                    map: rusty
                });

                for(var i = n - 1; i >= 0; i--) {
                    var elementGeometry = new THREE.CylinderGeometry(5, 5, (random(i) + 1) * 50, 4);
                    var addU = Math.random();
                    var addV = Math.random();
                    for(var z = 0; z < elementGeometry.faces.length; z++) {
                        elementGeometry.faceVertexUvs[0][z][0].x += addU;
                        elementGeometry.faceVertexUvs[0][z][0].y += addV;
                        elementGeometry.faceVertexUvs[0][z][1].x += addU;
                        elementGeometry.faceVertexUvs[0][z][1].y += addV;
                        elementGeometry.faceVertexUvs[0][z][2].x += addU;
                        elementGeometry.faceVertexUvs[0][z][2].y += addV;
                    }
                    elementGeometry.uvsNeedUpdate = true;

//                  var element = new THREE.Mesh(elementGeometry, elementMaterial);
//                  scene.add(element);
//
//                  element.position.y = elementGeometry.parameters.height / 2 - 5;
//                  element.position.x = (random(i + 214) - .5) * 1500;
//                  element.position.z = -random(i * 35) * 1500 - 100;
//                  element.rotation.set(random(i) * Math.PI / 20, 0, 0);
//
//                  element.receiveShadow = true;
//                  element.castShadow = true;.
                }
            }
            //
            function animate() {

                var delta = clock.getDelta();
                time += delta * 0.5;

                for(var i = v.length - 1; i >= 0; i--) {
                    v[i].z = Math.sin(i * 1 + time * -1) * 3;
                }
                camera.position.y = v[7320].z * 1.5 + 14;

                mirrorMesh.geometry.verticesNeedUpdate = true;

                moveSun();
                water.material.uniforms.time.value -= 1.0 / 60.0;
                water.sunDirection = sky.uniforms.sunPosition.value.normalize() //sunSphere.position.normalize()
                light.position.copy(sunSphere.position);

                water.render();

                requestAnimationFrame(animate);
                renderer.render(scene, camera);
                window.addEventListener('resize', onWindowResize, false);

            }

            function random(seed) {
                var x = Math.sin(seed) * 10000;
                return x - Math.floor(x);
            }

            function onWindowResize() {
                camera.aspect = window.innerWidth / window.innerHeight;
                camera.updateProjectionMatrix();

                renderer.setSize(window.innerWidth, window.innerHeight);
            }

            //# sourceURL=pen.js
        </script> -->


    <!-- 	<script src="js/jquery-1.12.3.min.js" type="text/javascript" charset="utf-8"></script>
            <script src="js/jquery.validate.js" type="text/javascript" charset="utf-8"></script> -->
            <input type="hidden" id="tip" value="<?php echo ($_GET['tip']); ?>" />
    <script type="text/javascript">
        function loginsubmit(){
            var captcha =  $('input[name=captcha]').val();
            var captchaurl = "<?php echo U('Mare/Layout/verify_captcha');?>";
            
//          $.getJSON(captchaurl,{captcha:captcha},function(data,status){
//              if(data.info == "success"){
//                  $('#captchatid').children('font').html('验证码正确').css('visibility','hidden');
//              }else{
//                  $('#captchatid').children('font').html('验证码错误').show();
//                  
//                  $('#captchaimg').attr('src',$('#captchaimg').attr('src') +'?'+ Math.random());
//                  return false;
//              }
//          },'text');
            $.ajax({
            	type: "post",
            	url: captchaurl,
            	async:false,
            	data: {captcha:captcha},
            	dataType: "json",
            	success:function(data){
            		if(data.info == "success"){
	                    $('#captchatid').children('font').html('验证码正确').css('visibility','hidden');
	                    if(($('#captchatid').children('font').html() != '验证码正确' && $('#captchaimg').length >0) || $('#username').val().length  < 1 || $('#userpasssword').val().length < 1 ){
			                if($('#username').val().length < 1){
			                    $('#codetip').children('font').html('请填写用户名');
                                 return false;
			                }else{
			                    if($('#userpasssword').val().length <1 ){
			                        $('#codetip').children('font').html('请填写密码');
			                        return false;
			                    }
		                        // console.log($('#captchatid').children('font').html());
		                        if($('#captchatid').children('font').html() != '验证码正确' && $('#captchaimg').length >0){
		                            return false;
		                        }
			                }
			            }
	                    //$('#login-form').submit();
	                    var username = $.trim($('input[name=username]').val());
	                    var password = $.trim($('input[name=password]').val());
	                    $.ajax({
	                        type: "post",
	                        url: "<?php echo U('Login/login_user');?>",
	                        async: true,
	                        data: {'username':username, 'password':password},
	                        success: function (data) {
	                            if (data.status == 'success') {
	                                //$('#codetip').children('font').html(data.msg.info).show();
	                                window.location = "<?php echo U('Mare/Index/index');?>";
	                                location.reload();
	                            } else if(data.status == 'error') {
	                                $('#codetip').children('font').html(data.msg.info).show();
	                            }
	                        }
	                    });
	                }else{
                        if($('#captchaimg').length >0){
                            console.log(1);
                            $('#codetip').children('font').html('验证码错误').show();
                            $('#captchaimg').attr('src',$('#captchaimg').attr('src') +'?'+ Math.random()); 
                        }
	                    return false;
	                }
            	}
            });
            
        }
     
        
        var tip = $('#tip').val();
        if(tip != ''){
            // var successTips = '<div id="action-tips" class="alert fade in alert-success global-tips" role="alert">下载成功，3秒后自动跳转，若没跳转，点击：<a href="'+_url +'<?php echo U('Mare/Report/rep_index');?>">这里</a></div>';
            var str = '';
            var errorTips = '<div id="action-tips" class="alert fade in alert-danger global-tips" role="alert">'+str+'</div>';
            // var uploadErrorTips = '<div id="action-tips" class="alert fade in alert-danger global-tips" role="alert">上传失败，请重试</div>';
            // var uploadSuccessTips = '<div id="action-tips" class="alert fade in alert-success global-tips" role="alert">上传成功</div>';
//            if(tip == 'LOGIN_ERROR'){
//                str = "登录的用户名或密码错误";
//                $('#codetip').children('font').html(str).show();
//            }
//            if(tip == 'LOGIN_STATUS_ERROR'){
//                str = '登录的用户名或密码错误';
//                $('#codetip').children('font').html(str).show();
//            }
//            if(tip == 'LOGIN_TIME_LIMIT'){
//                str = '您登录的密码错误过多，账号暂时被多冻结';
//                $('#codetip').children('font').html(str).show();
//            }
//            if(tip == 'LOGIN_CHECK_STATUS'){
//                str = '该用户未通过审核，请联系管理员';
//                $('#codetip').children('font').html(str).show();
//            }
//            if(tip == 'LOGIN_STATUS'){
//                str = '该用户已经被禁用，请联系管理员';
//                $('#codetip').children('font').html(str).show();
//            }
        }
    </script>
    </body>
</html>