/**
 * Created by loken_mac on 1/23/16.
 */
function r_l_btn_click(){
  if ( $("#r_l_btn").hasClass('r_l_selected') ) {
    $("#r_l_btn").removeClass('r_l_selected');
  }else{
    $("#r_l_btn").addClass('r_l_selected');
  }
}
function login_post(){
  if( $("#submit_blk").hasClass("submit_click") ){
    return false;
  }
  var account = $.trim($('#account').val());
  var pwd = $.trim($('#pwd').val());

  if(!account){
    $("#account_tips_w").html('请输入您的账号');
    $("#account_tips_icon").removeClass('icon-remove').addClass('icon-exclamation-sign');
    $("#account_tips").addClass('text-warning').show();
    return false;
  }else{
    $("#account_tips").hide();
  }

  if(!pwd){
    $("#pwd_tips_w").html('请输入您的密码');
    $("#pwd_tips").addClass('text-warning').show();
    return false;
  }else{
    $("#pwd_tips").hide();
  }


  $("#submit_blk").removeClass("submit_no_click").addClass("submit_click").html('登陆中...');

  var keep_login;
  if ( $("#r_l_btn").hasClass('r_l_selected') ) {
    keep_login = 1;
  }else{
    keep_login = 0;
  }
  $.ajax({
    type: "POST",
    url: "/Login/post",
    data: "account=" + account + "&pwd=" + pwd + '&keep_login='+keep_login,
    success: function (msg) {
      $("#submit_blk").removeClass("submit_click").addClass("submit_no_click").html('登陆');
      if (msg.status == 1) {
        $.jump_tips('success', '/', '登陆提示', '登陆成功', 1);
      } else {
        $("#account_tips_w").html(msg.msg);
        $("#account_tips_icon").removeClass('icon-exclamation-sign').addClass('icon-remove');
        $("#account_tips").addClass('text-danger').show();
      }
    }
  });



}
