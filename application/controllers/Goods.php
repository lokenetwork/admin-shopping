<?php

/**
 * @name GoodsController
 * @author root
 * @desc 商品控制器
 *
 */
class GoodsController extends UserController {

  public function init(){
    parent::init();
  }

  function indexAction(){
    global $r_db;

    $goods_name = $this->_get('goods_name');
    $page = $this->_get('page', 1);
    $goods_add_time_start = $this->_get('goods_add_time_start');
    $this->getView()->assign('goods_add_time_start', $goods_add_time_start);
    $goods_add_time_end = $this->_get('goods_add_time_end');
    $this->getView()->assign('goods_add_time_end', $goods_add_time_end);


    $condition = ['ORDER' => ['goods_id'=>'DESC']];
    $condition['AND']['is_delete'] = 0;

    $is_on_sale = $this->_get('is_on_sale', 0);
    if($is_on_sale){
      $condition['AND']['is_on_sale'] = $is_on_sale;
    }

    $is_new = $this->_get('is_new', 0);
    if($is_new){
      $condition['AND']['is_new'] = $is_new;
    }

    $is_hot = $this->_get('is_hot', 0);
    if($is_hot){
      $condition['AND']['is_hot'] = $is_hot;
    }

    $is_cheap = $this->_get('is_cheap', 0);
    if($is_cheap){
      $condition['AND']['is_cheap'] = $is_cheap;
    }

    $lock = $this->_get('lock', 0);
    if($lock){
      $condition['AND']['lock'] = $lock;
    }

    if($goods_name){
      $condition['AND']['goods_name[~]'] = $goods_name;
    }
    if($goods_add_time_start){
      $condition['AND']['add_time[>]'] = $goods_add_time_start . ' 00:00:00';
    }
    if($goods_add_time_end){
      $condition['AND']['add_time[<]'] = $goods_add_time_end . ' 00:00:00';
    }
    $spec_num = $r_db->count('goods', $condition);

    $Pagination = new Pagination($spec_num, $page, 20);
    $this->getView()->assign('pagination', $Pagination->show());

    $condition["LIMIT"] = [$Pagination->firstRow, $Pagination->listRows];
    //$fields = ['goods_id','goods_name','goods_price','goods_number','is_on_sale','page_view','add_time'];
    $fields = "*";
    $goods_list = $r_db->select('goods', $fields, $condition);

    //循环处理数据
    foreach($goods_list as $key => $item){
      $goods_list[$key]['origin_goods_name'] = $item['goods_name'];
      $goods_list[$key]['goods_name'] = mb_substr($item['goods_name'], 0, 30, "utf-8");
    }

    $this->getView()->assign('goods_name', $goods_name);

    $this->getView()->assign('goods_list', $goods_list);
    $this->getView()->assign('pagination', $Pagination->show());

    $this->getView()->assign('_title', '商品列表');

  }

  function editAction(){
    global $r_db;

    //查询出商品信息
    $goods_id = $this->_get('id');

    $goods_info = $r_db->get('goods', '*', ['goods_id' => $goods_id]);

    var_dump($goods_info);
    $goods_info['c_name'] = $this->get_shop_category($goods_info['ucat_id']);

    $this->getView()->assign('goods_info', $goods_info);

    /*

    */

  }

  function upAction(){
    global $w_db;

    //查询出商品信息
    $goods_id = $this->_get('id', 0);

    $w_db->update('goods', ['is_on_sale' => 1], ['goods_id' => $goods_id]);

    $this->getView()->assign("title", '操作提醒');
    $this->getView()->assign("desc", '商品已上架!');
    $this->getView()->assign("type", 'success');
    $this->getView()->display('common/tips.html');
    return false;
  }

  function downAction(){
    global $w_db;

    //查询出商品信息
    $goods_id = $this->_get('id', 0);

    $w_db->update('goods', ['is_on_sale' => 0], ['goods_id' => $goods_id]);

    $this->getView()->assign("title", '操作提醒');
    $this->getView()->assign("desc", '商品已下架!');
    $this->getView()->assign("type", 'success');
    $this->getView()->display('common/tips.html');
    return false;
  }


  function lockAction(){
    global $w_db;

    //查询出商品信息
    $goods_id = $this->_get('id', 0);

    $w_db->update('goods', ['lock' => 1], ['goods_id' => $goods_id]);

    $this->getView()->assign("title", '操作提醒');
    $this->getView()->assign("desc", '商品已被锁定!');
    $this->getView()->assign("type", 'success');
    $this->getView()->display('common/tips.html');
    return false;
  }


  function unlockAction(){
    global $w_db;

    //查询出商品信息
    $goods_id = $this->_get('id', 0);

    $w_db->update('goods', ['lock' => 0], ['goods_id' => $goods_id]);

    $this->getView()->assign("title", '操作提醒');
    $this->getView()->assign("desc", '商品已解锁!');
    $this->getView()->assign("type", 'success');
    $this->getView()->display('common/tips.html');
    return false;
  }

  //获取店铺商品分类
  private function get_shop_category($category_id){
    global $r_db;

    $return_str = '';
    $category_info = [];

    //获取3层的分类
    for($i = 0; $i < 3; $i++){

      $info = $r_db->get('shop_category', '*', ['category_id' => $category_id]);
      $category_info[] = $info['category_name'];
      $category_id = $info['parent_id'];

      if(0 == $info['parent_id']){
        break;
      }

    }

    krsort($category_info);

    foreach($category_info as $item){
      $return_str .= $item . ' > ';
    }

    $return_str = trim($return_str, ' > ');

    return $return_str;

  }

}