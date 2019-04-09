<?php

class IndexController extends UserController {



	public function indexAction(){
		$Ctime = new Ctime();
		global $r_db;

		$start_time = $this->_get('start_time', $Ctime->short_time());
		$end_time = $this->_get('end_time', $Ctime->addDate($Ctime->short_time(), 1, 'd'));
		$start_time = $this->_get('start_time', '2019-04-08');
		$end_time = $this->_get('end_time', '2019-04-09');
		$type = $this->_get('type', 'day');

		$condition = [];
		$condition['AND']['create_time[>]'] = $start_time . ' 00:00:00';
		$condition['AND']['create_time[<]'] = $end_time . ' 00:00:00';
		//限制查询1500条数据
		$condition["LIMIT"] = 1500;
		if('day' == $type){
			$create_data = $r_db->select('shop_create_day', '*', $condition);
		}else if('month' == $type){
			$create_data = $r_db->select('shop_create_month', '*', $condition);
		}
		$chat_data = [];
		//处理数据
		foreach($create_data as $key => $item){
			$view_time = strtotime($item['create_time']) * 1000;
			$chat_data[$key][0] = $view_time;
			$chat_data[$key][1] = $item['shop_num'];
		}

		$this->getView()->assign("type", $type);
		$this->getView()->assign("start_timestamp", strtotime($start_time) * 1000);
		$this->getView()->assign("end_timestamp", strtotime($end_time) * 1000);
		$this->getView()->assign("start_time", $start_time);
		$this->getView()->assign("end_time", $end_time);
		$this->getView()->assign("chat_data", json_encode($chat_data));
		$this->getView()->assign('_title', '新增店铺统计');
		return TRUE;
	}


	public function goodsAction(){
		$Ctime = new Ctime();
		global $r_db;

		$start_time = $this->_get('start_time', $Ctime->short_time());
		$end_time = $this->_get('end_time', $Ctime->addDate($Ctime->short_time(), 1, 'd'));
		$start_time = $this->_get('start_time', '2019-04-07');
		$end_time = $this->_get('end_time', '2019-04-09');
		$type = $this->_get('type', 'day');

		$condition = [];
		$condition['AND']['create_time[>=]'] = $start_time . ' 00:00:00';
		$condition['AND']['create_time[<=]'] = $end_time . ' 00:00:00';
		//限制查询1500条数据
		$condition["LIMIT"] = 1500;
		if('day' == $type){
			$create_data = $r_db->select('goods_create_day', '*', $condition);
		}else if('month' == $type){
			$create_data = $r_db->select('goods_create_month', '*', $condition);
		}
		$chat_data = [];
		//处理数据
		foreach($create_data as $key => $item){
			$view_time = strtotime($item['create_time']) * 1000;
			$chat_data[$key][0] = $view_time;
			$chat_data[$key][1] = $item['goods_num'];
		}

		$this->getView()->assign("type", $type);
		$this->getView()->assign("start_timestamp", strtotime($start_time) * 1000);
		$this->getView()->assign("end_timestamp", strtotime($end_time) * 1000);
		$this->getView()->assign("start_time", $start_time);
		$this->getView()->assign("end_time", $end_time);
		$this->getView()->assign("chat_data", json_encode($chat_data));
		$this->getView()->assign('_title', '新增商品统计');
		return TRUE;
	}



}
