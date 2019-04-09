<?php

/**
 * @name IndexController
 * @author root
 * @desc 默认控制器
 * @see http://www.php.net/manual/en/class.yaf-controller-abstract.php
 */
class AreaController extends Yaf_Controller_Abstract {

  /**
   * 默认动作
   * Yaf支持直接把Yaf_Request_Abstract::getParam()得到的同名参数作为Action的形参
   * 对于如下的例子, 当访问http://yourhost/Sample/index/index/index/name/root 的时候, 你就会发现不同
   */
  public function getChinaProvinceAction(){
    $r_medoo = new medoo();
    $pronvince = $r_medoo->select("Areas", [
      "area_id",
      "area_name"
    ], [
      'AND'=>[
        "parent_id" => 1,
        'area_type'=>2
      ],
      "ORDER" => "area_name_en ASC",
    ]);
    $this->getView()->assign("pronvince", $pronvince);
    return true;
  }



}