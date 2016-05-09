<?php

/**
 * @name GoodsSpecController
 * @author root
 * @desc 商品规格控制器
 *
 */
class GoodsSpecController extends GoodsController {

  public function init(){
    parent::init();

  }

  public function indexAction(){

    global $r_db;

    $cat_id = $this->_get('cat_id', 0);
    $this->getView()->assign('cat_id', $cat_id);
    $spec_name = $this->_get('spec_name');
    $this->getView()->assign('spec_name', $spec_name);
    $spec_value = $this->_get('spec_value');
    $this->getView()->assign('spec_value', $spec_value);
    $page = $this->_get('page', 1);


    $condition = [];
    if($cat_id){
      $condition['AND']['cat_id'] = $cat_id;
    }
    if($spec_name){
      $condition['AND']['name[~]'] = $spec_name;
    }
    if($spec_value){
      $condition['AND']['value[~]'] = $spec_value;
    }
    $spec_num = $r_db->count('goods_spec', $condition);

    $Pagination = new Pagination($spec_num, $page, 5);
    $this->getView()->assign('pagination', $Pagination->show());

    $condition["LIMIT"] = [$Pagination->firstRow, $Pagination->listRows];
    $fields = ['name', 'input_type', 'show_type', 'value', 'is_filter', 'enable'];
    $spec_list = $r_db->select('goods_spec', $fields, $condition);

    foreach($spec_list as &$item){
      $item['spec_value_show'] = '';
      if($item['value']){
        $item_value = unserialize($item['value']);
        if(is_array($item_value)){
          foreach($item_value as $value){
            if($item['show_type'] == 1){
              if($value){
                $item['spec_value_show'] .= $value . ',';
              }
            }else{
              $item['spec_value_show'] .= "<img class='spec_photo' src='{$value}' />" . ',';
            }
          }
        }
      }
    }
    unset($item);

    $this->getView()->assign('spec_list', $spec_list);

    //$this->display('index');
    return true;
  }

  public function addAction(){
    $Category = new Category();
    $fields = ['cat_id', 'cat_name'];
    $first_category = $Category->get_sub_category($fields);
    $this->getView()->assign('first_category', $first_category);
    $this->getView()->assign('_title', 'Add goods attribute');

  }

  function addPostAction(){

    $data['name'] = $this->_post('name');
    $data['cat_id'] = $this->_post('cat_id');
    $data['show_type'] = $this->_post('show_type');
    $data['have_picture'] = $this->_post('have_picture');
    $data['input_type'] = $this->_post('input_type');

    $data['value'] = serialize(str_replace("\\'", "‘", str_replace("\\\\", "/", $this->_post('spec_value'))));
    $data['note'] = $this->_post('note');
    $data['unit'] = $this->_post('unit');
    $data['is_filter'] = $this->_post('is_filter');
    /* 英文版暂不开发
    $data['is_copy'] = $this->_post('is_copy');
    */
    $data['is_show'] = serialize($this->_post('is_show'));
    global $r_db;
    $r_db->insert('goods_spec', $data);
    $this->getView()->display('common/tips.html');
    return false;
  }


  function searchAction(){
    global $r_db;
    $cat_id = intval($this->_get('_cat_id', -1));
    $name = $this->_get('name');

    $condition = ['LIMIT' => [0, 50]];
    if($cat_id >= 0){
      $condition['AND']['cat_id'] = $cat_id;
    }
    if($name){
      $condition['AND']['name[~]'] = $name;
    }
    $fields = ['name', 'id'];
    $spec_list = $r_db->select('goods_spec', $fields, $condition);
    ajaxReturn($spec_list);
    return false;
  }

  /*
   * 根据录入方式输出单个规格的预览html
   * */
  function previewAction($spec_id=0){
    global $r_db;
    if($spec_id){
      $field = ['id', 'value', 'name', 'name2', 'input_type'];
      $spec_info = $r_db->get('goods_spec', ['*'], ['id' => $spec_id]);

      $specValue = $spec_info['value'] ? unserialize($spec_info['value']) : array();
      $preview_html = '<div class="col-sm-2 control-label auto_width">' . $spec_info['name'] . ":</div>";
      $preview_html .= '<div class="col-sm-8">';
      switch($spec_info['input_type']){
        case 1:
          $preview_html .= '<select class="form-control">';
          foreach($specValue as $key => $item){
            $preview_html .= '<option value="' . $item . '">' . $item . '</option>';
          }
          $preview_html .= '</select>';
          break;
        case 2:
          foreach($specValue as $key => $item){
            if($spec_info['input_type'] == 2){
              $item = '<img class="spec_pic img_border" src="../' . $item . '" width="50px" height="50px" />';
            }
            $preview_html .= '<label> <input type="radio" name="radio" class="form-control" />' . $item . '</label>';
          }
          break;
        case 3:
          foreach($specValue as $key => $item){
            if($spec_info['input_type'] == 2){
              $item = '<img class="spec_pic img_border" src="../' . $item . '" width="50px" height="50px" />';
            }
            $preview_html .= '<label><input type="checkbox" class="form-control" value="" /> ' . $item . '</label>&nbsp;&nbsp;';
          }
          break;
        case 4:
          $preview_html .= '<input class="form-control" type="text" />';
          break;
        case 5:
          $preview_html = '<form action="" method="post" name="creator"><b>' . $spec_info['name'] . '：</b> <select name="spec1" onChange = "select()"></select><br><b>' . $spec_info['name2'] . '：</b> <select name="spec2" onChange = "select()"></select>
</form><script language="javascript">
var where = new Array(' . count($specValue) . ');';
          foreach($specValue as $key => $item){
            $item = explode("|", $item);
            $preview_html .= 'where[' . $key . '] = new comefrom("' . $item[0] . '","' . $item[1] . '");';
          }
          $preview_html .= '</script><script language="javascript">init();</script>';
          break;
      }
      $preview_html .= '<input name="id" id="id" type="hidden" value="' . $spec_info['id'] . '" /><input name="name" id="name" type="hidden" value="' . $spec_info['name'] . '" /><input name="name_en" id="name_en" type="hidden" value="' . $spec_info['name_en'] . '" />';
      $preview_html .= '</div>';
      echo $preview_html;
    }
    return false;
  }

}