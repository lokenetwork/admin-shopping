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
    $is_on_sale = $this->_get('is_on_sale',0);
    $goods_add_time_start = $this->_get('goods_add_time_start');
    $this->getView()->assign('goods_add_time_start',$goods_add_time_start);
    $goods_add_time_end = $this->_get('goods_add_time_end');
    $this->getView()->assign('goods_add_time_end',$goods_add_time_end);


    $condition = ['ORDER'=>['goods_id DESC']];
    if($is_on_sale){
      $condition['AND']['is_on_sale'] = $is_on_sale;
    }
    if($goods_name){
      $condition['AND']['goods_name[~]'] = $goods_name;
    }
    if($goods_add_time_start){
      $condition['AND']['add_time[>]'] = strtotime($goods_add_time_start);
    }
    if($goods_add_time_end){
      $condition['AND']['add_time[<]'] = strtotime($goods_add_time_end);
    }
    $spec_num = $r_db->count('goods', $condition);

    $Pagination = new Pagination($spec_num, $page, 20);
    $this->getView()->assign('pagination', $Pagination->show());

    $condition["LIMIT"] = [$Pagination->firstRow, $Pagination->listRows];
    $fields = ['goods_id','goods_name','goods_price','goods_number','is_on_sale','page_view','add_time'];
    $goods_list = $r_db->select('goods', $fields, $condition);


    $this->getView()->assign('goods_name',$goods_name);

    $this->getView()->assign('goods_list',$goods_list);
    $this->getView()->assign('pagination',$Pagination->show());

    $this->getView()->assign('_title','Goods list');

  }

}