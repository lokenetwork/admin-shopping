<?php

/**
 * @name IndexController
 * @author root
 * @desc 默认控制器
 * @see http://www.php.net/manual/en/class.yaf-controller-abstract.php
 */
class CategoryController extends UserController {

  protected $Category;
  public function init(){
    parent::init();
    $this->Category = new Category();
  }

  /*
   * 分类列表
   * */
  public function indexAction(){

    $page = $this->_get('page',1);
    $parent_id = $this->_get('parent_category_id',0);
    $this->getView()->assign('parent_category_id',$parent_id);

    $category_name = $this->_get('cat_name');
    $this->getView()->assign('category_name',$category_name);

    $t_category = $GLOBALS['r_db']->table_prefix.'category';
    $t_goods_model= $GLOBALS['r_db']->table_prefix.'goods_model';

    $condition = ' WHERE 1=1 ';

    $condition .= " AND c.parent_id = {$parent_id} ";
    if( $category_name ){
      $condition .= " AND c.cat_name LIKE '%{$category_name}%' ";
    }
    $field =  'c.cat_id,c.cat_name,gm.name';
    $tables =  "{$t_category} AS c LEFT JOIN {$t_goods_model} AS gm ON c.model_id = gm.id ";
    $sql = "SELECT ".$field." FROM ".$tables.$condition;
    $category_count = $GLOBALS['r_db']->query($sql)->rowCount();

    $Pagination = new Pagination($category_count, $page, 5);


    $limit = ' LIMIT '.$Pagination->firstRow.','.$Pagination->listRows;
    $order = ' ORDER BY c.cat_id DESC ';
    $sql = "SELECT ".$field." FROM ".$tables.$condition.$order.$limit;
    $category_list = $GLOBALS['r_db']->query($sql)->fetchAll();

    $this->getView()->assign('pagination',$Pagination->show());
    $this->getView()->assign('category_list',$category_list);
    return TRUE;
  }

  function goodsCategoryAddAction(){
    $fields = ['cat_id','cat_name'];
    $first_category = $this->Category->get_sub_category($fields);
    $this->getView()->assign('first_category',$first_category);
    $this->display('goodsCategoryAdd');
    return false;
  }

  function goodsCategoryAddPostAction(){

    $parent_id = intval($this->_post('parent_category_id'));
    $cat_name = $this->_post('cat_name');
    //别名暂时不处理
    $aliases_name = $this->_post('aliases_name');
    $sort_order = intval($this->_post('sort_order'));
    $app_index_display = intval($this->_post('app_index_display'));
    $category_level_0 = intval($this->_post('category_level_0'));
    $category_level_1 = intval($this->_post('category_level_1'));
    $category_level_2 = intval($this->_post('category_level_2'));
    $level = 0;

    if( $category_level_0 ){
      $arr_parent_id = $category_level_0;
      $level++;
      if( $category_level_1 ){
        $arr_parent_id .= "," . $category_level_1;
        $level++;
        if( $category_level_2 ){
          $arr_parent_id .= "," . $category_level_2;
          $level++;
        }
      }
    }
    if(isset($arr_parent_id)){
      $data['arr_parent_id'] = $arr_parent_id;
    }else{
      $data['arr_parent_id'] = 0;
    }
    $data['parent_id'] = $parent_id;
    $data['cat_name'] = $cat_name;
    $data['sort_order'] = $sort_order;
    $data['level'] = $level;
    $data['app_index_display'] = $app_index_display;

    $GLOBALS['w_db']->insert('category',$data);

    $this->getView()->assign("title", '操作提醒');
    $this->getView()->assign("desc", '商品分类添加成功!');
    $this->getView()->assign("url", '/Category/goodsCategoryAdd');
    $this->getView()->assign("type", 'success');
    $this->getView()->display('common/tips.html');
    return false;
  }

  function echoSubCategoryAction($fields,$parent_id){
    if( is_string($fields) ){
      $fields = explode(',',$fields);
    }
    $result = $this->Category->get_sub_category($fields,$parent_id);
    if( !$result ){
      $result = 'no_sub_category';
    }
    ajaxReturn($result);
    return false;
  }




}