var lastLeft = new Date();
var lastRight = new Date();
var shaking = false;
var stopNum = 0;
var ds, a1, a2, a3, a4, a5, a6;

window.sounds = new Object();
var sound = new Audio('bo.mp3');
var gamecoin = 0;
sound.load();
//window.sounds['music'].play();
window.onload = function() {
	window.scroll(0, 0);
	/*var sound = new Audio('/zy.mp3');
	 sound.load();
	 window.sounds['music'] = sound;*/
	if ( typeof window.DeviceMotionEvent != 'undefined') {
		var sensitivity = 10;

		var mydiv = document.getElementById("mydiv");

		window.addEventListener('devicemotion', function(e) {
			var x1 = e.accelerationIncludingGravity.x;

			if (x1 > sensitivity)
				lastLeft = new Date();

			if (x1 < -sensitivity)
				lastRight = new Date();

			if (x1 > sensitivity / 2 && !shaking && new Date().getTime() - lastRight.getTime() < 100) {

				shaking = true;

				begin_func();
			} else if (x1 < -sensitivity / 2 && !shaking && new Date().getTime() - lastLeft.getTime() < 100) {
				shaking = true;

				begin_func();
			} else if (x1 >= -sensitivity / 2 && x1 <= sensitivity / 2) {
				shaking = false;
			}

		}, false);

	}

	var str = aaajax('ajax.php?t=' + Math.random());
	switch (str.status) {

		case '200':
			$("#id_leftcount").html(str.times);
			gamecoin = parseInt(str.score);
			addinfo();
			break;
		case '204':
			$("#txt").html("网络出错啦,请稍候再来！");
			break;
		case '300':
			$("#txt").html("您还没登录哟！");
			alert("您还没登录哟！");
			window.location.href = "sign.html";
			break;
		default:
			$("#txt").html("出错啦,请稍候再来！");
			break;
	}
}

function imghide() {

	var str = bbajax('ajax.php?t=' + Math.random());
	$("#txt").show();
	switch (str.status) {
		case '200':
			ds = str.roll;
			a1 = ds.substr(0, 1);
			a2 = ds.substr(1, 1);
			a3 = ds.substr(2, 1);
			a4 = ds.substr(3, 1);
			a5 = ds.substr(4, 1);
			a6 = ds.substr(5, 1);
			document.getElementById("rezult").innerHTML = "<img src='img/dian" + a1 + ".png' id='p1' class='p_1' /> <img src='img/dian" + a2 + ".png' id='p2' class='p_2' /> <img src='img/dian" + a3 + ".png' id='p3' class='p_3' /> <img src='img/dian" + a4 + ".png' id='p4' class='p_4' /> <img src='img/dian" + a5 + ".png' id='p5' class='p_5' /> <img src='img/dian" + a6 + ".png' id='p6' class='p_6' />";
			$("#txt").html(str.score_each);
			$("#id_leftcount").html(str.times);
			gamecoin += parseInt(str.score_each);
			addinfo(str);
			break;
		case '201':
			$("#txt").html("您的博饼次数已经用完！");
			break;
		case '300':
			$("#txt").html("您还没登录哟！");
			alert("您还没登录哟！");
			window.location.href = "sign.html";
			break;
		case '203':
			$("#txt").html("全部包厢博完了！");
			break;
		case '204':
			$("#txt").html("本次博饼活动已经结束！");
			break;
		case '205':
			$("#txt").html("您点击太快了休息几秒吧！");
			break;
		default:
			$("#txt").html("出错啦,请稍候再来！");
			break;
	};
	$("#loading").hide();
	$("#rezult").show();
	$("#boyiba").attr("disabled", false);
	//$("#i_bg").attr("src","images/l"+arr[5]+".wav")
}

function addinfo(str) {
	var gchtml = document.getElementById("id_gamecoin");
	gchtml.innerHTML = gamecoin;

	switch(str.score_each) {

		case '0':
			$("#txt").html("分享好友，再来一次（0分）");
			break;
		case '10':
			$("#txt").html("小小秀才，下次再来（一秀10分）");
			break;
		case '20':
			$("#txt").html("一举成名，二举成双（二举20分）");
			break;
		case '50':
			$("#txt").html("四海升平，四世同堂（四进50分）");
			break;
		case '100':
			$("#txt").html("三朋四友，必要分享（三红100分）");
			break;
			
		case '150':
			$("#txt").html("一帆风顺，心想事成（对堂150分）");
			break;
		case '200':
			$("#txt").html("状元在手，天下我有（状元200分）");
			break;
		default:
			$("#txt").html("出错啦,请稍候再来！");
			break;
	}
}

var nowDate = 0, lastDate = 0;

function begin_func() {

	nowDate = new Date().getTime();
	//	alert(nowDate-lastDate);
	if (nowDate - lastDate < 1500) {
		lastDate = nowDate;
		return false;
	}
	lastDate = nowDate;
	var bbCount = parseInt($("#id_leftcount").html());
	if (bbCount > 0) {
		//		OnClick();
		sound.play();
		$("#txt").hide();
		$("#rezult").hide();
		$("#yaobin").hide();
		$("#loading").show();
		$("#boyiba").attr("disabled", true);
		setTimeout(imghide, 3000);

	} else {
		$("#txt").html("您今日的次数已用尽,请明天继续！");
	}

}

function addOne(){
	$("#shareImg").show();
	var str = ccajax('ajax.php?t=' + Math.random());
}

var jqresult;
function bbajax(url) {

	$.ajax({
		url : url,
		type : 'post',
		dataType : "json",
		data : {
			type : "gameTimes"
		},
		async : false,
		error : function(ret, error) {
			alert(ret.responseText);
		},
		success : function(ret) {

			if (!ret) {
				return;
			}
			jqresult = ret;
		}
	});

	return jqresult;
}

function aaajax(url) {

	$.ajax({
		url : url,
		type : 'post',
		dataType : "json",
		data : {
			type : "initGame"
		},
		async : false,
		error : function(ret, error) {
			alert(ret.responseText);
		},
		success : function(ret) {

			if (!ret) {
				return;
			}
			jqresult = ret;
		}
	});

	return jqresult;
}

function ccajax(url) {

	$.ajax({
		url : url,
		type : 'post',
		dataType : "json",
		data : {
			type : "addOne"
		},
		async : false,
		error : function(ret, error) {
			alert(ret.responseText);
		},
		success : function(ret) {

			if (!ret) {
				return;
			}
			jqresult = ret;
		}
	});

	return jqresult;
}

function shareHide(){
	
	$("#shareImg").hide();
	
}

