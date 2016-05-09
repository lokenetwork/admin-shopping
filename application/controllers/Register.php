<?php

/**
 * @name IndexController
 * @author root
 * @desc 默认控制器
 * @see http://www.php.net/manual/en/class.yaf-controller-abstract.php
 */
class RegisterController extends Yaf_Controller_Abstract {

  function init(){

  }

  /**
   * 默认动作
   * Yaf支持直接把Yaf_Request_Abstract::getParam()得到的同名参数作为Action的形参
   * 对于如下的例子, 当访问http://yourhost/Sample/index/index/index/name/root 的时候, 你就会发现不同
   */
  public function indexAction(){
    $salt = rand() % 10000;

    $Yaf_Request_Http = new Yaf_Request_Http();
    $name = $Yaf_Request_Http->getPost("name");
    $phone = $Yaf_Request_Http->getPost("phone");
    $pwd = $Yaf_Request_Http->getPost("pwd");
    $encrypt_pwd = md5($pwd . $salt);
    $third_register = $Yaf_Request_Http->getPost("third_register");
    $avatar = $Yaf_Request_Http->getPost("avatar");
    $t_uid = $Yaf_Request_Http->getPost("t_uid");

    $r_medoo = new medoo();

    $db_data = array("control_type" => 2);
    $w_medoo = new medoo($db_data);
    if($third_register == 'sina'){

      $favourites_count = $Yaf_Request_Http->getPost("favourites_count");
      $location = $Yaf_Request_Http->getPost("location");
      $description = $Yaf_Request_Http->getPost("description");
      $verified = $Yaf_Request_Http->getPost("verified");
      $friends_count = $Yaf_Request_Http->getPost("friends_count");
      $gender = $Yaf_Request_Http->getPost("gender");
      $statuses_count = $Yaf_Request_Http->getPost("statuses_count");
      $followers_count = $Yaf_Request_Http->getPost("followers_count");

      //Check is has the account or not
      $check_result = $this->check_sina_account_exist($r_medoo, $phone);

      if($check_result){
        $other_data = array('t_uid' => $t_uid, 'favourites_count' => $favourites_count, 'location' => $location, 'description' => $description, 'screen_name' => $name, 'verified' => $verified, 'friends_count' => $friends_count, 'verified' => $verified, 'gender' => $gender, 'statuses_count' => $statuses_count, 'followers_count' => $followers_count, 'profile_image_url' => $avatar, 'last_time' => time(),);
        $w_medoo->update("SinaUserInfo", $other_data, array("uid" => $check_result));
        echo 0;
        exit;
      }else{
        $data = array('sex' => 0, 'name' => $name, 'nick' => $name, 'phone' => $phone, 'password' => $encrypt_pwd, 'salt' => $salt, 'created' => time(), 'updated' => time(), 'avatar' => $avatar);
        $data['if_no_pwd'] = 0;
        $user_id = $w_medoo->insert("User", $data);

        $other_data = array('uid' => $user_id, 't_uid' => $t_uid, 'favourites_count' => $favourites_count, 'location' => $location, 'description' => $description, 'screen_name' => $name, 'verified' => $verified, 'friends_count' => $friends_count, 'verified' => $verified, 'gender' => $gender, 'statuses_count' => $statuses_count, 'followers_count' => $followers_count, 'profile_image_url' => $avatar, 'last_time' => time(),);
        $w_medoo->insert("SinaUserInfo", $other_data);
        echo $user_id;

      }

    }elseif($third_register == 'douban'){
      $check_result = 1;

      if($check_result){
        $other_data = array('t_uid' => $t_uid, 'profile_image_url' => $avatar, 'last_time' => time(),);
        $w_medoo->update("DoubanUserInfo", $other_data, array("uid" => $check_result));
        echo 0;
        exit;
      }else{
        $data = array('sex' => 0, 'name' => $name, 'nick' => $name, 'phone' => $phone, 'password' => $encrypt_pwd, 'salt' => $salt, 'created' => time(), 'updated' => time(), 'avatar' => $avatar, 'if_no_pwd' => 0);

        $user_id = $w_medoo->insert("User", $data);

        $other_data = array('uid' => $user_id, 't_uid' => $t_uid, 'profile_image_url' => $avatar, 'last_time' => time(),);
        $w_medoo->insert("DoubanUserInfo", $other_data);
        echo $user_id;
      }


    }elseif($third_register == 'renren'){

      //Check is has the account or not
      $check_result = $this->check_renren_account_exist($r_medoo, $t_uid);

      if($check_result){
        $other_data = array('t_uid' => $t_uid, 'profile_image_url' => $avatar, 'last_time' => time(),);
        $w_medoo->update("RenrenUserInfo", $other_data, array("uid" => $check_result));
        echo 0;
        exit;
      }else{
        $data = array('sex' => 0, 'name' => $name, 'nick' => $name, 'salt' => $salt, 'created' => time(), 'updated' => time(), 'avatar' => $avatar, 'if_no_pwd' => 0);

        $user_id = $w_medoo->insert("User", $data);

        $other_data = array('uid' => $user_id, 't_uid' => $t_uid, 'profile_image_url' => $avatar, 'last_time' => time(),);
        $w_medoo->insert("RenrenUserInfo", $other_data);
        echo $user_id;
      }
    }else{
      //Check is has the account or not
      $check_result = $this->check_account_exist($r_medoo, $phone);
      if($check_result){
        echo 0;
        exit;
      }else{
        $data = array('sex' => 0, 'name' => $name, 'nick' => $name, 'phone' => $phone, 'password' => $encrypt_pwd, 'salt' => $salt, 'created' => time(), 'updated' => time(),);
        $user_id = $w_medoo->insert("User", $data);
        echo $user_id;
      }
    }

    return false;
  }


  /*人人注册或登录*/
  public function renrenAction(){
    $Yaf_Request_Http = new Yaf_Request_Http();
    $t_uid = $Yaf_Request_Http->getPost("t_uid");
    $access_token = $Yaf_Request_Http->getPost("access_token");

    $r_medoo = new medoo();

    $db_data = array("control_type" => 2);
    $w_medoo = new medoo($db_data);
    $resp_msg = array();

    $response = $this->get_renren_info($access_token, $t_uid);
    if(isset($response['error'])){
      $resp_msg['pass'] = 0;
      $resp_msg['info'] = "auth error";
    }else{
      $t_data =  $response['response'];
      $resp_msg['pass'] = 1;
      //Check is has the account or not
      $uid = $this->check_renren_account_exist($r_medoo, $t_uid);

      if($t_data['basicInformation']['sex'] == 'MEAL'){
        $sex = 1;
      }else{
        $sex = 2;
      }
      if($uid){
        $t_update_data = array(
          'profile_image_url' => $t_data['avatar'][3]['url'],
          'sex' => $sex, 'birthday' => $t_data['basicInformation']['birthday'],
          'homeTown' => $t_data['basicInformation']['homeTown'],
          'education_info' => serialize($t_data['education']),
          'work_info' => serialize($t_data['work']),
          'emotionalState' => $t_data['emotionalState'],
          'last_time' => time()
        );
        $w_medoo->update("RenrenUserInfo", $t_update_data, array("uid" => $uid));
        $update_data = [
          'avatar' => $t_data['avatar'][3]['url'],
        ];
        $w_medoo->update("User", $update_data, array("id" => $uid));
      }else{
        Yaf_loader::import("string.class.php");
        $String = new String();
        $domain_arr = $String->Pinyin($t_data['name']);
        $insert_data = array(
          'sex' => $sex,
          'name' => $t_data['name'],
          'nick' => $t_data['name'],
          'domain' => $domain_arr['all'],
          'avatar' => $t_data['avatar'][3]['url'],
          'created' => time(),
          'updated' => time(),);
        $uid = $w_medoo->insert("User", $insert_data);

        $t_insert_data = [
          'uid' => $uid,
          't_uid' => $t_uid,
          'sex' => $sex,
          'profile_image_url' => $t_data['avatar'][3]['url'],
          'birthday' => $t_data['basicInformation']['birthday'],
          'homeTown' => $t_data['basicInformation']['homeTown'],
          'education_info' => serialize($t_data['education']),
          'work_info' => serialize($t_data['work']),
          'emotionalState' => $t_data['emotionalState'],
          'last_time' => time()
        ];
        $w_medoo->insert("RenrenUserInfo", $t_insert_data);
      }
      $resp_msg['uid'] = $uid;
    }
    echo json_encode($resp_msg);
    return false;
  }

  public function testAction(){
    header("Content-type: text/html; charset=utf-8");
    $w = '{"response":{"name":"罗上文","id":880293366,"avatar":[{"size":"TINY","url":"http://head.xiaonei.com/photos/0/0/men_tiny.gif"},{"size":"HEAD","url":"http://head.xiaonei.com/photos/0/0/men_head.gif"},{"size":"MAIN","url":"http://head.xiaonei.com/photos/0/0/men_main.gif"},{"size":"LARGE","url":"http://head.xiaonei.com/photos/0/0/men_main.gif"}],"star":0,"basicInformation":{"sex":"FEMALE","birthday":"1995-1-29","homeTown":null},"education":[{"name":"University of Arkansas Little Rock","year":"2003","educationBackground":"MASTER","department":"其它院系"}],"work":[{"name":"深圳深深蓝电子","time":"2014","industry":null,"job":null}],"like":null,"emotionalState":null}}';
    dump($w);
    foreach(json_decode($w, true) as $k => $v){
      dump($k);
      dump($v);
    };
    $this->getView()->assign("number", rand());
    return true;
  }
  
  public function responseAction(){
    $w = '{"response":{"name":"罗上文","id":880293366,"avatar":[{"size":"TINY","url":"http://head.xiaonei.com/photos/0/0/men_tiny.gif"},{"size":"HEAD","url":"http://head.xiaonei.com/photos/0/0/men_head.gif"},{"size":"MAIN","url":"http://head.xiaonei.com/photos/0/0/men_main.gif"},{"size":"LARGE","url":"http://head.xiaonei.com/photos/0/0/men_main.gif"}],"star":0,"basicInformation":{"sex":"FEMALE","birthday":"1995-1-29","homeTown":null},"education":[{"name":"University of Arkansas Little Rock","year":"2003","educationBackground":"MASTER","department":"其它院系"}],"work":[{"name":"深圳深深蓝电子","time":"2014","industry":null,"job":null}],"like":null,"emotionalState":null}}';
	echo $w;
    return false;
  }

  /**
   * @param $r_medoo
   * @param $account
   * @return int
   * 1 represent exsit
   * 0 represent not exsit
   */
  function check_account_exist($r_medoo, $phone){
    $condition = array("phone" => $phone);
    $check_result = $r_medoo->get("User", 'id', $condition);
    if($check_result){
      return $check_result;
    }else{
      return 0;
    }
  }

  /**
   * @param $r_medoo
   * @param $t_uid
   * @return int
   * 1 represent exsit
   * 0 represent not exsit
   */
  function check_sina_account_exist($r_medoo){
    global $t_uid;
    $condition = array("sina_encrypt_uid" => $t_uid);
    $check_result = $r_medoo->get("User", 'id', $condition);
    if($check_result){
      return $check_result;
    }else{
      return 0;
    }
  }

  function check_renren_account_exist($r_medoo, $t_uid){
    $condition = array("t_uid" => $t_uid);
    $uid = $r_medoo->get("RenrenUserInfo", 'uid', $condition);
    if($uid){
      return $uid;
    }else{
      return 0;
    }
  }

  /*检测人人授权是否是正确的*/
  function get_renren_info($access_token, $uid){
    $url = "https://api.renren.com/v2/user/get?access_token={$access_token}&userId={$uid}";
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $data = curl_exec($ch);
    curl_close($ch);
    return json_decode($data, true);
  }

  function check_douban_account_exist($r_medoo){
    global $t_uid;
    $condition = array("douban_encrypt_uid" => $t_uid);
    $check_result = $r_medoo->get("User", 'id', $condition);
    if($check_result){
      return $check_result;
    }else{
      return 0;
    }
  }
}