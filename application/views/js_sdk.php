<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width,initial-scale=1,user-scalable=no">
	<title>js_sdk</title>
	<script src="http://res.wx.qq.com/open/js/jweixin-1.0.0.js"></script>
	<script src="http://cdn.bootcss.com/zepto/1.0rc1/zepto.min.js"></script>
</head>
<body>
	<button id="onMenuShareAppMessage" ">分享</button>
	<button id="scanQRCode" ">扫码</button>
	<button id="onMenuShareTimeline" ">分享朋友圈</button>
	 <?php echo $qrcode;?>
	<script>
		wx.config({
		    debug: true, // 开启调试模式,调用的所有api的返回值会在客户端alert出来，若要查看传入的参数，可以在pc端打开，参数信息会通过log打出，仅在pc端时才会打印。
		    appId: 'xxxxxxxxxxxxxxxxx', // 必填，公众号的唯一标识
		    timestamp: '<?php echo $timestamp;?>', // 必填，生成签名的时间戳
		    nonceStr: '<?php echo $nonceStr;?>', // 必填，生成签名的随机串
		    signature: '<?php echo $signature;?>',// 必填，签名，见附录1
		    jsApiList: ["onMenuShareAppMessage","chooseImage","scanQRCode","onMenuShareTimeline"] // 必填，需要使用的JS接口列表，所有JS接口列表见附录2
		});
		wx.ready(function(){
		    // config信息验证后会执行ready方法，所有接口调用都必须在config接口获得结果之后，config是一个客户端的异步操作，所以如果需要在页面加载时就调用相关接口，则须把相关接口放在ready函数中调用来确保正确执行。对于用户触发时才调用的接口，则可以直接调用，不需要放在ready函数中。
		    wx.checkJsApi({  
              jsApiList: [  
                'getLocation',  
                'onMenuShareTimeline',  
                'onMenuShareAppMessage'  
              ],  
              success: function (res) {  
                alert(res.errMsg);  
              }  
            });  
		    $('#onMenuShareAppMessage').on('click',function () {
		        wx.onMenuShareAppMessage({
		          title: '互联网之子 方倍工作室',
		          desc: '在长大的过程中，我才慢慢发现，我身边的所有事，别人跟我说的所有事，那些所谓本来如此，注定如此的事，它们其实没有非得如此，事情是可以改变的。更重要的是，有些事既然错了，那就该做出改变。',
		          link: 'http://movie.douban.com/subject/25785114/',
		          imgUrl: 'http://img3.douban.com/view/movie_poster_cover/spst/public/p2166127561.jpg',
		          trigger: function (res) {
		            alert('用户点击发送给朋友');
		          },
		          success: function (res) {
		            alert('已分享');
		          },
		          cancel: function (res) {
		            alert('已取消');
		          },
		          fail: function (res) {
		            alert(JSON.stringify(res));
		          }
		        });
		        alert('已注册获取“发送给朋友”状态事件');
		      });
		     
		       document.querySelector('#scanQRCode').onclick = function () {
		        wx.scanQRCode({
		          needResult: 0, // 默认为0，扫描结果由微信处理，1则直接返回扫描结果，
		          scanType: ["qrCode","barCode"], // 可以指定扫二维码还是一维码，默认二者都有
		          success: function (res) {
		          var result = res.resultStr; // 当needResult 为 1 时，扫码返回的结果
		      }
		      });
		        // alert('已注册获取“发送给朋友”状态事件');
		      };
		      document.querySelector('#onMenuShareTimeline').onclick = function () {
		      	wx.onMenuShareTimeline({
		        title: '互联网之子 方倍工作室',
		        desc: '在长大的过程中，我才慢慢发现，我身边的所有事，别人跟我说的所有事，那些所谓本来如此，注定如此的事，它们其实没有非得如此，事情是可以改变的。更重要的是，有些事既然错了，那就该做出改变。',
		        link: 'http://movie.douban.com/subject/25785114/',
		        imgUrl: 'http://img3.douban.com/view/movie_poster_cover/spst/public/p2166127561.jpg',
		        trigger: function (res) {
		          alert('用户点击发送给朋友');
		        },
		        success: function (res) {
		          alert('已分享11');
		        },
		        cancel: function (res) {
		          alert('已取消');
		        },
		        fail: function (res) {
		          alert(JSON.stringify(res));
		        }
		      });
		      };
		      wx.onMenuShareAppMessage({
		        title: '互联网之子 方倍工作室',
		        desc: '在长大的过程中，我才慢慢发现，我身边的所有事，别人跟我说的所有事，那些所谓本来如此，注定如此的事，它们其实没有非得如此，事情是可以改变的。更重要的是，有些事既然错了，那就该做出改变。',
		        link: 'http://movie.douban.com/subject/25785114/',
		        imgUrl: 'http://img3.douban.com/view/movie_poster_cover/spst/public/p2166127561.jpg',
		        trigger: function (res) {
		          alert('用户点击发送给朋友');
		        },
		        success: function (res) {
		          alert('已分享11');
		        },
		        cancel: function (res) {
		          alert('已取消');
		        },
		        fail: function (res) {
		          alert(JSON.stringify(res));
		        }
		      });
		      
		    //   wx.chooseImage({
		    //       count: 1, // 默认9
		    //       sizeType: ['original', 'compressed'], // 可以指定是原图还是压缩图，默认二者都有
		    //       sourceType: ['album', 'camera'], // 可以指定来源是相册还是相机，默认二者都有
		    //       success: function (res) {
		    //           var localIds = res.localIds; // 返回选定照片的本地ID列表，localId可以作为img标签的src属性显示图片
		    //       }
		    //   });
		    // }
		});
		wx.error(function(res){
			alert(0);

		    // config信息验证失败会执行error函数，如签名过期导致验证失败，具体错误信息可以打开config的debug模式查看，也可以在返回的res参数中查看，对于SPA可以在这里更新签名。

		});
		
	</script>
</body>
</html>