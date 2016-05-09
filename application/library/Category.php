<?php
/**
 * @name CategoryModel
 * @desc CategoryModel 商品分類
 * @author root
 */
class Category {
    public function __construct() {
    }   
    
    public function test() {
        echo 'Hello World!';
    }

    function get_sub_category($fields,$parent_id=0){
        if(in_array("*", $fields)){
            exit('禁止用星号查询分类表');
        }
        $condition = [
          "parent_id" => $parent_id,
          "ORDER" => ['sort_order ASC', 'cat_id ASC']
        ];
        return $GLOBALS['r_db']->select("category", $fields, $condition);
    }
}
