<?php
/**
 * @name GoodsController
 * @author root
 * @desc 商品控制器
 *
 */
class ShopController extends UserController {

  public function init(){
    parent::init();
  }

  function indexAction(){
    global $r_db;

    $shop_name = $this->_get('shop_name');
    $page = $this->_get('page', 1);
    $add_time_start = $this->_get('add_time_start');
    $this->getView()->assign('add_time_start',$add_time_start);
    $add_time_end = $this->_get('add_time_end');
    $this->getView()->assign('add_time_end',$add_time_end);


    $condition = ['ORDER'=>['shop_id'=>'DESC']];
    $condition['AND']['is_delete'] = 0;


    $lock = $this->_get('lock',0);
    if($lock){
      $condition['AND']['lock'] = $lock;
    }

    if($shop_name){
      $condition['AND']['shop_name[~]'] = $shop_name;
    }
    if($add_time_start){
      $condition['AND']['add_time[>]'] = $add_time_start.' 00:00:00';
    }
    if($add_time_end){
      $condition['AND']['add_time[<]'] = $add_time_end.' 00:00:00';
    }
    $spec_num = $r_db->count('shop', $condition);

    $Pagination = new Pagination($spec_num, $page, 20);
    $this->getView()->assign('pagination', $Pagination->show());

    $condition["LIMIT"] = [$Pagination->firstRow, $Pagination->listRows];
    //$fields = ['goods_id','goods_name','goods_price','goods_number','is_on_sale','page_view','add_time'];
    $fields = "*";
    $list = $r_db->select('shop', $fields, $condition);

    //循环处理数据
    foreach($list as $key=>$item){
      $l_info = $this->get_shop_location($item);
      $list[$key]['location_url'] =  $l_info['location_url'];
    }

    $this->getView()->assign('shop_name',$shop_name);

    $this->getView()->assign('list',$list);
    $this->getView()->assign('pagination',$Pagination->show());

    $this->getView()->assign('_title','店铺列表');

  }

  function detailAction(){
    global $r_db;

    $shop_id = $this->_get('id');

    $fields = "*";
    $condition = ['shop_id'=>$shop_id];
    $shop_info = $r_db->get('shop', $fields, $condition);


    $l_info = $this->get_shop_location($shop_info);
    $shop_info['location_url'] =  $l_info['location_url'];

    $this->getView()->assign('shop_info',$shop_info);

    $this->getView()->assign('_title','店铺详情');

  }

  //获取店铺位置函数
  private function get_shop_location($shop_info){
    $url= "https://map.baidu.com/?latlng=%s,%s&title=%s&content=%s&autoOpen=true&l";
    $arr['location_url'] = sprintf($url,$shop_info['latitude'],$shop_info['longitude'],$this->get_company_name(),$shop_info['shop_name']);
    return($arr);
  }


  function lockAction(){
    global $w_db;

    //查询出商品信息
    $shop_id = $this->_get('id',0);

    $w_db->update('shop',['lock'=>1],['shop_id'=>$shop_id]);

    $this->getView()->assign("title", '操作提醒');
    $this->getView()->assign("desc", '店铺已被锁定!');
    $this->getView()->assign("type", 'success');
    $this->getView()->display('common/tips.html');
    return false;
  }


  function unlockAction(){
    global $w_db;

    //查询出商品信息
    $shop_id = $this->_get('id',0);

    $w_db->update('shop',['lock'=>0],['shop_id'=>$shop_id]);

    $this->getView()->assign("title", '操作提醒');
    $this->getView()->assign("desc", '店铺已解锁!');
    $this->getView()->assign("type", 'success');
    $this->getView()->display('common/tips.html');
    return false;
  }

  function delAction(){
    global $w_db;

    //查询出商品信息
    $shop_id = $this->_get('id',0);

    $w_db->update('shop',['is_delete'=>1],['shop_id'=>$shop_id]);

    $this->getView()->assign("title", '操作提醒');
    $this->getView()->assign("desc", '店铺已删除!');
    $this->getView()->assign("type", 'success');
    $this->getView()->display('common/tips.html');
    return false;
  }


}