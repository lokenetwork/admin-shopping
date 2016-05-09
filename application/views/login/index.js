/**
 * Created by loken_mac on 1/23/16.
 */


(function($)
{

  $.extend({
    Login:
    {

      Info: {
        Author: "loken",
      },



      CheckData: function(){
        var account = $.trim($('#account').val());
        var password = $.trim($('#password').val());
        if( account ){
          $("#account_help").html('');
        }else{
          $("#account_help").html('请输入账号').addClass('text-warning');
          return false;
        }

        if( password ){
          $("#password_help").html('');
        }else{
          $("#password_help").html('请输入密码').addClass('text-warning');
          return false;
        }
        return true;
      },

      Post: function () {
        if(this.CheckData()){
          $("#loginSubmitButton").button('loading');
          var post_data = $("#loginForm").serialize()
          $.ajax({
            type: "POST",
            url: "/Login/post",
            data: post_data,
            success: function (msg) {
              if (msg.status == 1) {
                $("#account_help").html("");
                $.jump_tips('success', '/', '登陆提示', '登陆成功', 1);
              } else {
                $("#account_help").html(msg.msg).addClass('text-danger');
                $("#loginSubmitButton").button('reset');
              }
            }
          });
        }
      },
    }
  });



})(jQuery)




