<?php

/**
 * @name IndexController
 * @author root
 * @desc 默认控制器
 * @see http://www.php.net/manual/en/class.yaf-controller-abstract.php
 */
class SearchController extends UserController {

	/**
	 * 默认动作
	 * Yaf支持直接把Yaf_Request_Abstract::getParam()得到的同名参数作为Action的形参
	 * 对于如下的例子, 当访问http://yourhost/Sample/index/index/index/name/root 的时候, 你就会发现不同
	 */
	public function reportAction(){
		global $r_db;
		$page = $this->_get('page',1);
		$join = [
			"[>]shop" => ["shop_id" => "shop_id"],
		];
		$filed = [
			"search_pullword_report.report_id",
			"search_pullword_report.goods_name",
			"search_pullword_report.pullword",
			"search_pullword_report.create_time",
			"shop.shop_name",
			"shop.shop_id",
		];
		$where = [
		];

		$spec_num = $r_db->count('search_pullword_report');
		$Pagination = new Pagination($spec_num, $page, 20);
		$where["LIMIT"] = [$Pagination->firstRow, $Pagination->listRows];
		$this->getView()->assign('pagination', $Pagination->show());

		$report_list = $r_db->select('search_pullword_report',$join,$filed,$where);
		$this->getView()->assign('list',$report_list);
		$this->getView()->assign('pagination',$Pagination->show());
		$this->getView()->assign('_title','分词反馈');
	}

	public function delReportAction(){
		global  $w_db;
		$id = $this->_get('id');
		$w_db->delete("search_pullword_report",['report_id'=>$id]);
		$this->getView()->assign("title", '操作提醒');
		$this->getView()->assign("desc", '删除成功!');
		$this->getView()->assign("type", 'success');
		$this->getView()->display('common/tips.html');
		return false;
	}

	public function delWordAction(){
		global  $w_db;
		$id = $this->_get('id');
		$w_db->delete("search_word",['word_id'=>$id]);
		$this->getView()->assign("title", '操作提醒');
		$this->getView()->assign("desc", '删除成功!');
		$this->getView()->assign("type", 'success');
		$this->getView()->display('common/tips.html');
		return false;
	}

	public function dictionaryAction(){
		global $r_db;
		$page = $this->_get('page',1);
		$key_name = $this->_get('key_name');

		$spec_num = $r_db->count('search_word');
		$Pagination = new Pagination($spec_num, $page, 20);
		$where = [];
		if( $key_name ){
			$where['word[~]'] = $key_name;
		}
		$where["LIMIT"] = [$Pagination->firstRow, $Pagination->listRows];
		$where["ORDER"] = ["word_id" => "DESC"];
		$this->getView()->assign('pagination', $Pagination->show());
		$report_list = $r_db->select('search_word',"*",$where);
		$this->getView()->assign('list',$report_list);
		$this->getView()->assign('key_name',$key_name);
		$this->getView()->assign('pagination',$Pagination->show());
		$this->getView()->assign('_title','字典管理');
	}

	public function addwordAction(){
		global $r_db;
		$page = $this->_get('page',1);
		$key_name = $this->_get('key_name');

		$spec_num = $r_db->count('search_word');
		$Pagination = new Pagination($spec_num, $page, 20);
		$where = [];
		if( $key_name ){
			$where['word[~]'] = $key_name;
		}
		$where["LIMIT"] = [$Pagination->firstRow, $Pagination->listRows];
		$this->getView()->assign('pagination', $Pagination->show());
		$report_list = $r_db->select('search_word',"*",$where);
		$this->getView()->assign('list',$report_list);
		$this->getView()->assign('key_name',$key_name);
		$this->getView()->assign('pagination',$Pagination->show());
		$this->getView()->assign('_title','添加字典');
	}

	public function addwordPostAction(){
		global  $w_db;
		$name = $this->_get('name');
		$w_db->insert("search_word",['word'=>$name]);
		$this->getView()->assign("title", '操作提醒');
		$this->getView()->assign("desc", '添加成功!');
		$this->getView()->assign("type", 'success');
		$this->getView()->assign("url", '/Search/dictionary');
		$this->getView()->display('common/tips.html');
		return false;
	}


}
