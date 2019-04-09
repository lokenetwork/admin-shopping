<?php
/**
 * @name UserController
 * @author root
 * @desc use this control must be login
 * @see
 */
class UserController extends BaseController {

  public function init(){
    parent::init();
    $this->checkLogin();
  }
  /**
   * get the admin info from database
   */
  public function useInfoAction($field){
    if( $field == '*' ){
      //limit select with *
      return false;
    }
    $condition = [
      "admin_id" => $_SESSION['admin_id'],
    ];
    $c_info = $GLOBALS['r_db']->get("admin_user", $field, $condition);
    return $c_info;
  }

  function logoutAction(){
    unset($_SESSION['admin_id']);
    $this->getView()->assign("title", '温馨提示');
    $this->getView()->assign("desc", '您已成功退出!');
    $this->getView()->assign("url", '/Login/index');
    $this->getView()->assign("type", 'success');
    $this->getView()->display('common/tips.html');
    return false;
  }


  //Check the user is login or not
  function checkLogin(){
    $this->setViewPath(VIEW_PATH);
    if( !isset($_SESSION['admin_id']) || !$_SESSION['admin_id'] ){
      $this->getView()->assign("title", '登陆提示');
      $this->getView()->assign("desc", '请先登陆!');
      $this->getView()->assign("url", '/Login/index');
      $this->getView()->assign("type", 'warning');
      $this->getView()->display('common/tips.html');
      exit;
    }
  }


}