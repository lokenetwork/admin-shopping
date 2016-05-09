<?php

/**
 * @name IndexController
 * @author root
 * @desc 默认控制器
 * @see http://www.php.net/manual/en/class.yaf-controller-abstract.php
 */
class UploadController extends Yaf_Controller_Abstract {

  /**
   * 默认动作
   * Yaf支持直接把Yaf_Request_Abstract::getParam()得到的同名参数作为Action的形参
   * 对于如下的例子, 当访问http://yourhost/Sample/index/index/index/name/root 的时候, 你就会发现不同
   */
  public function userAvaterAction(){
    $avater_path = "/uploadFiles/userAvater/";
    $date = date("YmdHis");
    $filename = APPLICATION_PATH.$avater_path.$date.".png";
    $file = fopen($filename, "w");
    $data = base64_decode($_POST['img']);
    fwrite($file, $data);
    fclose($file);
    echo $avater_path.$date.".png";
    return false;
  }

  public function familyAvaterAction(){
    $avater_path = "/uploadFiles/familyAvater/";
    $date = date("YmdHis");
    $filename = APPLICATION_PATH.$avater_path.$date.".png";
    $file = fopen($filename, "w");
    $data = base64_decode($_POST['img']);
    fwrite($file, $data);
    fclose($file);
    echo $avater_path.$date.".png";
    return false;
  }


}