<?php
/**
 * @name IndexController
 * @author root
 * @desc 默认控制器
 * @see http://www.php.net/manual/en/class.yaf-controller-abstract.php
 */
class LoginController extends BaseController {

  public function init(){
    parent::init();
    $this->checkHasLogin();
  }

  //Check the user is login or not
  function checkHasLogin(){
    if( isset($_SESSION['admin_id']) && $_SESSION['admin_id'] ){
      $this->getView()->assign("title", '登陆提示');
      $this->getView()->assign("desc", '您已经登陆了!');
      $this->getView()->assign("url", '/');
      $this->getView()->assign("type", 'warning');
      $this->display(VIEW_PATH.'/common/tips');
      exit;
    }
  }

  /**
   * just display the login page
   */
  public function indexAction($name = "Stranger"){


    return TRUE;
  }

  public function index2Action($name = "Stranger"){


    return TRUE;
  }

  /*
   * submit action deal
   * */
  public function postAction(){
    $account = trim($this->_post('account'));
    $password = trim($this->_post('password'));
    $keep_login = intval($this->_post('keep_login'));

    //get the possible admin list
    $condition = [
      'OR'=>[
        "admin_name" => $account,
        "admin_email" => $account,
        "mobile" => $account,
      ]
    ];
    $field = ['group_id','admin_password','admin_name','admin_id'];
    $admin_list = $GLOBALS['r_db']->select("admin_user", $field, $condition);



    foreach($admin_list as $item){
      if( password_verify($password,$item['admin_password']) ){
        $_SESSION['admin_name'] = $item['admin_name'];
        $_SESSION['admin_id'] = $item['admin_id'];
        $group_id = $item['group_id'];
        break;
      }
    }
    if( isset( $_SESSION['admin_id'] ) && $_SESSION['admin_id'] ){
      $Ctime = new Ctime();
      $time = $Ctime->long_time();
      $last_ip = $_SERVER['REMOTE_ADDR'];
      //Update login info
      $GLOBALS['w_db']->update('admin_user',['last_login'=>$time,'last_ip'=>$last_ip],['admin_id'=>$_SESSION['admin_id']]);

      //管理组权限
      if($_SESSION['admin_id'] == 1){
        $_SESSION['rights'] = "all_privilege";
      } else {
        if($group_id){
          //Get the people right
          $condition = [
            "id" => $group_id,
          ];
          $field = 'rights';
          $_SESSION['rights'] = $GLOBALS['r_db']->get("admin_group", $field, $condition);
        }else{
          $_SESSION['rights']= "";
        }
      }
      $respond_data['status'] = 1;
      $respond_data['msg'] = '登陆成功!';
      $session_name = ini_get('session.name');
      if( $keep_login && isset($_COOKIE[$session_name]) ){
        setcookie($session_name, $_COOKIE[$session_name], time()+3600*24*7,'/');
      }

    }else{
      if( $admin_list ){
        $respond_data['status'] = 0;
        $respond_data['msg'] = '账号或密码错误!';
      }else{
        $respond_data['status'] = -1;
        $respond_data['msg'] = '账号不存在!';
      }
    }
    ajaxReturn($respond_data);
    return false;
  }


}