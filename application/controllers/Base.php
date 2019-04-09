<?php

/**
 * Created by PhpStorm.
 * User: loken_mac
 * Date: 1/22/16
 * Time: 9:52 PM
 */
class BaseController extends Yaf_Controller_Abstract {


	public function init(){

		$this->redis_port = Yaf_Application::app()->getConfig()->redis->port;
		$this->redis_server = Yaf_Application::app()->getConfig()->redis->server;

		$this->getView()->assign("css_rel", CSS_REL);
		$this->getView()->assign("css_type", CSS_TYPE);

		if(ini_get("yaf.environ") == 'dev'){
			$this->getView()->assign("client_less", '<script src="/static/common_js/less.js"></script>');
		}else{
			$this->getView()->assign("client_less", '');
		}

		$this->getView()->company_name = $this->get_company_name();


	}

	/**
	 * get the group name quickly
	 */
	public function get_company_name(){
		$condition = ["name" => 'company_name',];
		$field = ['value'];
		$c_info = $GLOBALS['r_db']->get("setting", $field, $condition);
		return $c_info['value'];
	}

	/*
	 * 再封装下get,post,为以后过滤做准备,直接改yaf我们不熟
	 * */
	protected function _get($name, $default_value = ''){
		$Yaf_Request_Http = new Yaf_Request_Http();
		$value = $Yaf_Request_Http->get($name);
		if($value === null){
			$responed = $default_value;
		}else{
			$responed = $value;
		}
		if(is_string($responed)){
			$responed = trim($responed);
		}
		return $responed;
	}

	protected function _post($name, $default_value = ''){
		$Yaf_Request_Http = new Yaf_Request_Http();
		$value = $Yaf_Request_Http->getPost($name);
		if($value === null){
			$responed = $default_value;
		}else{
			$responed = $value;
		}
		if(is_string($responed)){
			$responed = trim($responed);
		}
		return $responed;
	}


	/*
	 * pdo 查看错误信息demo
	 * */
	function pdo_error_demo(){
		global $r_db;
		var_dump($r_db->pdo->errorInfo());
	}


}
