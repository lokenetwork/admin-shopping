/**
 * Created by loken_mac on 1/24/16.
 */
$.extend({
  /*
   * js 弹窗跳转函数
   *  type:类型,有warning,error,success
   *  url:跳转的url
   *  title:头部文字
   *  desc,描述
   *  show_out_side 是否显示外部黑块
   * */

  jump_tips:function(type,url,title,desc,show_out_side){
    var icon_css;
    var font_css;
    var icon_class;
    var out_side_rgba;
    if( type == 'warning' ){
      font_css = 'color:#FF9933';
      icon_css = 'color:#FF9933';
      icon_class = 'icon-exclamation-sign';
    }else if( type == 'error' ) {
      font_css = 'color:#D65C5C';
      icon_css = 'color:#D65C5C';
      icon_class = ' icon-remove';
    }else if( type == 'success' ){
      font_css = 'color:#333';
      icon_css = 'color:#0A8';
      icon_class = 'icon-ok';
    }
    if( show_out_side ){
      out_side_rgba = '0,0,0,0.5';
    }else{
      out_side_rgba = '0,0,0,0';
    }

    var css_style = '<style>.alert_icon_ok{  }</style>';

    var html = '' +
      '<div id="alert_outside" style="position:fixed; top:0px; left:0px; width:100%; height:100%; opacity:0; background-color:rgba('+out_side_rgba+')"  >'+
          '<div style="position:relative; width:450px; top: 0%; margin:155px auto 0px auto; border: 1px solid #D0D0D0; border-radius:5px; " >'+
              '<div style=" font-size:20px; text-align:center; background:#ccc; border-bottom:1px solid #BBBBBB; border-radius:5px 5px 0px 0px; background:-webkit-gradient(linear, 0% 0%, 0% 100%,from(#fff), to(#DEDEDE));">'+
                '<span style="line-height:50px; '+font_css+'">'+title+'</span>'+
              '</div>'+
              '<div style="height:180px; background:#fff; border-radius:0px 0px 5px 5px;">'+
                '<div style="width:120px; height:100%; margin:0px 3px 0px 31px; float:left;">'+
                  '<i id="alert_tips_icon" style=" margin:44px 0px 0px 20px; font-size:90px; display: inline-block;'+icon_css+'" class="'+icon_class+' alert_icon_ok"></i>'+
                '</div>'+
                '<div style=" height:100%; float:left; ">'+
                  '<div style=" margin:60px 0px 0px 0px; font-size:30px; '+font_css+'">'+
                    '<span id="alert_tips_desc">'+desc+'</span>'+
                  '</div>'+
                  '<div style=" margin: 0px 0px 0px 0px; font-size:12px; color:#807C7C;">'+
                      '2秒后自动<a href="'+url+'" id="alert_jump_url">跳转</a>,请稍后...'+
                  '</div>'+
                '</div>'+
              '</div>'+
          '</div>'+
      '</div>';
    $("body").append(html);

    $("#alert_outside").animate({
      opacity: "1",
    }, 200,function(){
      setTimeout(function(){
        window.location = url;
      }, 2000);
    });

  }
})