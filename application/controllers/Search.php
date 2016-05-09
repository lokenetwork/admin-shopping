<?php

/**
 * @name IndexController
 * @author root
 * @desc 默认控制器
 * @see http://www.php.net/manual/en/class.yaf-controller-abstract.php
 */
class SearchController extends Yaf_Controller_Abstract {

  /**
   * 默认动作
   * Yaf支持直接把Yaf_Request_Abstract::getParam()得到的同名参数作为Action的形参
   * 对于如下的例子, 当访问http://yourhost/Sample/index/index/index/name/root 的时候, 你就会发现不同
   */
  public function familyAction($family_name,$p=1,$family_ids=''){
    $r_medoo = new medoo();
    $family_name = urldecode($family_name);
    //第一次关键词的搜索结果的sphinx_id缓存在html里，ajax提交会把id，post过来，不用再查sphinx
    if( $p <= 1 ){
      $family_ids = $this->get_the_sphinx_family_id($family_name);
    }else{
      $family_ids = unserialize(urldecode($family_ids));
    }
    $field = ['family_id','family_name','head_portrait','introduce','people_num'];
    if(!empty($family_ids)){
      $condition = [
        "OR" => [
          "family_id" => $family_ids,
          "family_name[~]" => $family_name
        ],
      ];
    }else{
      $condition = [
        "family_name[~]" => $family_name,
      ];
    }
    $singl_page_num = 30;
    $data_start = ($p-1)*$singl_page_num;
    $condition['LIMIT'] = [$data_start,$singl_page_num];
    $family_list = $r_medoo->select("Family", $field, $condition);
    //循环处理家庭图片
    foreach($family_list as $key=>$v){
      if( !$v['head_portrait'] ){
        $family_list[$key]['head_portrait'] = "/static/common_img/logo_grey.jpg";
      }
    }

    if (!$this->getRequest()->isXmlHttpRequest()) {
      $family_ids_serialize = serialize($family_ids);
      $this->getView()->assign("family_list", $family_list);
      $this->getView()->assign("family_ids_serialize", $family_ids_serialize);
      $this->getView()->assign("family_name", $family_name);

      return true;
    }else{
      if( !empty($family_list) ){
        echo(json_encode($family_list));
      }else{
        echo 0;
      }
      return false;
    }
  }

  protected function get_the_sphinx_family_id($family_name){
    return array();
    //define the index of sphinx for search
    $sphinx_index = "family_main_index";
    //highlight index
    $highlight_main_index = "family_main_index";
    $highlight_delta_index = "family_delta_index";

    $sphinx_server = Yaf_Application::app()->getConfig()->sphinx->server;
    $sphinx_port = Yaf_Application::app()->getConfig()->sphinx->port;
    //Create sphinx object
    $sphinx = new SphinxClient();
    //connect sphinx server
    $sphinx->SetServer($sphinx_server, $sphinx_port);


    //设置匹配模式
    $sphinx->SetMatchMode(SPH_MATCH_ALL);
    //Limit 1000 family result
    $sphinx->setLimits(0, 1000);
    $family_result = $sphinx->query($family_name, $sphinx_index);
    if(isset($family_result['matches'])){
      $family_ids = (array_keys($family_result['matches']));
    }else{
      $family_ids = array();
    }
    return $family_ids;
  }


}