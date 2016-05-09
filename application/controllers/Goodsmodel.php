<?php
/**
 * @name GoodsModelController
 * @author root
 * @desc 商品模型控制器
 *
 */
class GoodsModelController extends GoodsController {

  public function init(){
    parent::init();

  }

  public function indexAction(){
    global $r_db;

    $page = $this->_get('page',1);

    $model_name = $this->_get('model_name');
    $this->getView()->assign('model_name',$model_name);

    $cat_id = $this->_get('cat_id',0);
    $this->getView()->assign('cat_id',$cat_id);

    $condition = ['ORDER'=>['id DESC']];
    if( $cat_id ){
      $condition['AND']['cat_id'] = $cat_id;
    }
    if( $model_name ){
      $condition['AND']['name[~]'] = $model_name;
    }
    $spec_num =  $r_db->count('goods_model',$condition);

    $Pagination = new Pagination($spec_num, $page);
    $this->getView()->assign('pagination',$Pagination->show());

    $condition["LIMIT"] = [$Pagination->firstRow, $Pagination->listRows];
    $fields = ['name','note','spec_ids','is_filter','is_hide'];
    $model_list = $r_db->select('goods_model',$fields,$condition);


    foreach($model_list as &$item){
      $spec_ids_value = unserialize($item['spec_ids']);
      //var_dump($spec_ids_value);
      $item['spec_name_show'] = '';
      foreach( $spec_ids_value as $value ){
        $item['spec_name_show'] .= $value['admin_title'].',';
      }
      $item['spec_name_show'] = trim($item['spec_name_show'],',');
    }
    unset($item);


    $this->getView()->assign('model_list',$model_list);
    $this->getView()->assign('_title','商品模型列表');

    return true;
  }

  public function addAction(){
    $this->getView()->assign('_title', 'Add Goods Model');

  }

  function addPostAction(){
    global $w_db;

    $insert_data['name'] = $this->_get('name');
    $insert_data['cat_id'] = $this->_get('cat_id');
    $insert_data['note'] = $this->_get('note');
    $bind_category = $this->_get('bind_category',0);

    //循环序列化处理模型对应的规格
    $model_attr = $this->_get('attr');;
    $model_attr_len = count($model_attr['id']);
    $model_spec = array();
    for( $i = 0;$i< $model_attr_len;$i++)
    {
      if(!empty($model_attr['id'][$i])){
        $model_spec[$i]['id'] = $model_attr['id'][$i];
        $model_spec[$i]['name'] = $model_attr['name'][$i];
        $model_spec[$i]['name_en'] = '';
        $model_spec[$i]['admin_title'] = $model_attr['admin_title'][$i];
        $model_spec[$i]['is_hide'] = $model_attr['is_hide'][$i];
        $model_spec[$i]['is_required'] = $model_attr['is_required'][$i];
        $model_spec[$i]['is_attr'] = $model_attr['is_attr'][$i];
      }
    }
    $insert_data['spec_ids']= serialize($model_spec);

    $goods_model_id = $w_db->insert('goods_model',$insert_data);

    if( $bind_category ){
      $this->update_goods_category_model_bind_info($goods_model_id,$insert_data['cat_id']);
    }

    $this->getView()->display('common/tips.html');
    return false;
  }

  //更新商品分类的模型绑定关系函数
  private function update_goods_category_model_bind_info($goods_model_id,$category_id){
    if( $category_id > 0  ){
      //更新分类绑定的模型
      $goods_category_update_data = ['model_id'=>$goods_model_id,'model_cat_id'=>$category_id];
      $GLOBALS['w_db']->update('goods_category',$goods_category_update_data,['cat_id'=>$category_id]);
    }
  }


}