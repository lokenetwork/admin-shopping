<?php
/**
 * @name IndexController
 * @author root
 * @desc 默认控制器
 * @see http://www.php.net/manual/en/class.yaf-controller-abstract.php
 */
class IndexController extends UserController {



  /*
   * 操作盘
   * */
  public function indexAction($name = "Stranger"){

    return TRUE;
  }


  public function indexBackAction($name = "Stranger"){
    //1. fetch query
    $get = $this->getRequest()->getQuery("get", "default value");
    var_dump(111);
    //2. fetch model
    $model = new SampleModel();

    //3. assign
    $this->getView()->assign("content", $model->selectSample());
    $this->getView()->assign("name", $name);
    //phpinfo();
    //4. render by Yaf, 如果这里返回FALSE, Yaf将不会调用自动视图引擎Render模板
    return TRUE;
  }

  public function testAction(){
    var_dump(111);
    exit;
    $database = new medoo();

    dump($database);
    $database->insert("test", [
      "name" => "foo",
      "age" => "1"
    ]);
    dump($database->last_query());
    return false;
  }
}