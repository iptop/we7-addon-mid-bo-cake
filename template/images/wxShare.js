
    

    function _ShareFriend() {
        WeixinJSBridge.invoke('sendAppMessage',{
              'appid': appid,
              'img_url': img111,
              'img_width': width,
              'img_height': height,
              'link': url,
              'desc': desc,
              'title': title
              }, function(res){
                _report('send_msg', res.err_msg);
          })
    }
    function _ShareTL() {	
        WeixinJSBridge.invoke('shareTimeline',{
              'img_url': img111,
              'img_width': width,
              'img_height': height,
              'link': url,
              'desc': desc,
              'title': title
              }, function(res) {
              _report('timeline', res.err_msg);
              });
    }
    function _ShareWB() {
        WeixinJSBridge.invoke('shareWeibo',{
              'content': desc,
              'url': url,
              }, function(res) {
              _report('weibo', res.err_msg);
              });
    }

    document.addEventListener('WeixinJSBridgeReady', function onBridgeReady() {
        
            WeixinJSBridge.on('menu:share:appmessage', function(argv){
                _ShareFriend();
          });

            WeixinJSBridge.on('menu:share:timeline', function(argv){
                _ShareTL();
                });

            WeixinJSBridge.on('menu:share:weibo', function(argv){
                _ShareWB();
           });
    }, false);
