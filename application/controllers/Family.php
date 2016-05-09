<?php

/**
 * @name IndexController
 * @author root
 * @desc 默认控制器
 * @see http://www.php.net/manual/en/class.yaf-controller-abstract.php
 */
class FamilyController extends Yaf_Controller_Abstract {

  public function detailAction($family_id=''){
    $r_medoo = new medoo(['charset'=>'utf8mb4']);
    $condition = [
      "family_id" => $family_id,
    ];
    $field = ['head_portrait','introduce','province_id','city_id'];
    $family_info = $r_medoo->get("Family", $field, $condition);
    if( !$family_info['head_portrait'] ){
      $family_info['head_portrait'] = "/static/common_img/header_transparency.png";
    }

    if( !$family_info['introduce'] ){
      $default_introduce_list = [
        '只有家里人才对你好啊。',
        '青春尽头，家人永远陪在你身边。',
        '能用美的电饭煲给你煮樱桃小丸子，只有家里人。',
        '有家才有国。',
        '有空就要聚。',
        '你妈跟女友掉落水，你先救谁？',
        '单着的，明年必须带另一半回家过年。',
        '还有单着的吗？还有没结婚的吗？',
        '青春易逝，恋爱趁早。',
        '爷爷奶奶要抱孙子啦，先生娃，后结婚！'
      ];
      $introduce_key = array_rand($default_introduce_list,1);
      $family_info['introduce'] = $default_introduce_list[$introduce_key];
    }
    $a_condition = [
      'area_id'=>[
        $family_info['province_id'],
        $family_info['city_id']
      ]
    ];
    $a_field = ['area_id','area_name'];
    $area_info = $r_medoo->select("Areas", $a_field, $a_condition);
    foreach( $area_info as $v ){
      if( $v['area_id'] ==  $family_info['province_id'] ){
        $family_info['province_name'] = $v['area_name'];
      }
      if( $v['area_id'] ==  $family_info['city_id'] ){
        $family_info['city_name'] = $v['area_name'];
      }
    }
    if( !$family_info['province_name'] ){
      $family_info['province_name'] = "中国";
    }
    if( !$family_info['city_name'] ){
      $family_info['city_name'] = "";
    }

    $this->getView()->assign("family_info", $family_info);
    //暂时查询出30个家族成员，以后再弄像QQ那样的，再参考QQ吧
    $p_condition = [
      'family_id'=>$family_id,
      "LIMIT" => [0,10]
    ];
    $p_field = ['avatar'];
    $people_list = $r_medoo->select("User", $p_field, $p_condition);
    foreach($people_list as $k=>$v){
      if( !$v['avatar'] ){
        $people_list[$k]['avatar'] =  "/static/common_img/tt_default_user_portrait_corner.png";
      }
    }
    $this->getView()->assign("people_list", $people_list);
    return true;
  }

  public function joinAction(){
    $Yaf_Request_Http = new Yaf_Request_Http();
    $user_id = $Yaf_Request_Http->getPost("user_id");
    $family_id = $Yaf_Request_Http->getPost("family_id");

    $w_medoo = new medoo(['charset'=>'utf8mb4','control_type'=>2]);
    $data = ['family_id'=>$family_id];
    $where = ['id'=>$user_id];
    if($w_medoo->update("User",$data,$where)){
      $w_medoo->update("Family",['people_num[+]'=>1],['family_id'=>$family_id]);
      echo 1;
    }else{
      echo 0;
    };
    return false;
  }

  public function createAction(){
    $Yaf_Request_Http = new Yaf_Request_Http();
    $user_id = $Yaf_Request_Http->getPost("user_id");
    $family_name = $Yaf_Request_Http->getPost("family_name");

    $r_medoo = new medoo(['charset'=>'utf8mb4','control_type'=>1]);
    $check_where = [
      'family_name'=>$family_name
    ];
    $field = "family_id";
    //check the family name exsit or not
    $check_resutl = $r_medoo->get("Family", $field, $check_where);

    //check the user is had join the family or not
    $_family_id = $r_medoo->get("User", 'family_id', ['id'=>$user_id]);

    if( $_family_id == 0 ){
      if($check_resutl){
        echo -1;
      }else{
        $w_medoo = new medoo(['charset'=>'utf8mb4','control_type'=>2]);
        $data = [
          'create_user_id'=>$user_id,
          'people_num'=>1,
          'family_name' => $family_name,
          'create_time' => time(),
          'update_time' => time()
        ];
        $family_id = $w_medoo->insert("Family",$data);
        if( $family_id ){
          $update_data['family_id'] = $family_id;
          $where = ['id'=>$user_id];
          $w_medoo->update("User",$update_data,$where);
          echo $family_id;
        }else{
          echo 0;
        }
      };

    }else{
      echo 0;
    }
    return false;
  }

  public function testAction(){
    $arr_1 = [
      'appKey'=>'123456',
      'req_id'=>'1',
      'to_id'=>'4',
    ];
    $arr_2 = [
      'cid'=>'new_user',
      'name'=>"loken"
    ];
    $arr_1['msg_content'] = json_encode($arr_2);
    dump($arr_1);
    $strJson = json_encode($arr_1);
    dump($strJson);

    dump("-------------------------------------------------------------");
    $top_array = json_decode($strJson,true);
    dump($top_array);
    $bottom_array = json_decode($top_array['msg_content']);
    dump($bottom_array);
    return false;
  }


}