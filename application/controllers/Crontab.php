<?php

class CrontabController extends BaseController {

	public function init(){

		//判断是否是cli模式，如果不是立即退出
		if(!preg_match("/cli/i", php_sapi_name())){
			//exit("not cli");
		}
		parent::init();
	}

	//todo,处理坏数据，例如cli计划任务没跑。
	public function dealWrongData(){
	}
	//将商品统计 归档
	public function goodsViewHourAction(){
		global $w_db;

		//将当期时间跟上一个小时的数据归档，防止时间临界点
		$Ctime = new Ctime();
		$now_timestamp = time();
		$time_format = 'Y-m-d H:00:00';
		$now_time = $Ctime->custom($time_format,$now_timestamp);
		$before_time = $Ctime->custom($time_format,$now_timestamp-3600);
		$after_time = $Ctime->custom($time_format,$now_timestamp+3600);

		//开启事务
		$w_db->begin_transaction();
		$w_db->exec('LOCK TABLES c_new_goods_view WRITE ');

		//todo,这里分组可以优化。
		$where = [];
		$where['AND']['view_time[>]'] = $before_time;
		$where['AND']['view_time[<]'] = $now_time;
		$where['GROUP'] = ['goods_id'];
		$before_list = $w_db->select('goods_view',['goods_id','shop_id','view_num' => Medoo::raw('count(*)')],$where);
		$where = [];
		$where['AND']['view_time[>]'] = $before_time;
		$where['AND']['view_time[<]'] = $now_time;
		$w_db->delete('goods_view',$where);
		$w_db->exec('UNLOCK TABLES ');
		$w_db->commit();

		foreach($before_list as $item){
			$w_db->update('goods',['view[+]'=>$item['view_num']],['goods_id'=>$item['goods_id']]);

			$view_hour_info = $w_db->get("goods_view_hour",'*',['goods_id'=>$item['goods_id'],'shop_id'=>$item['shop_id'],'view_time'=>$before_time]);
			if( $view_hour_info ){
				$now_view_num = $view_hour_info['view_num']+$item['view_num'];
				$w_db->update('goods_view_hour',['view_num'=>$now_view_num],['goods_id'=>$item['goods_id'],'shop_id'=>$item['shop_id'],'view_time'=>$before_time]);
			}else{
				$insert_data = [];
				$insert_data['goods_id'] = $item['goods_id'];
				$insert_data['shop_id'] = $item['shop_id'];
				$insert_data['view_num'] = $item['view_num'];
				$insert_data['view_time'] = $before_time;
				$w_db->insert('goods_view_hour',$insert_data);
			}

		}


		//查询后一个小时的数据。
		$w_db->begin_transaction();
		$w_db->exec('LOCK TABLES c_new_goods_view WRITE ');
		$where = [];
		$where['AND']['view_time[>]'] = $now_time;
		$where['AND']['view_time[<]'] = $after_time;
		$where['GROUP'] = ['goods_id'];
		$after_list = $w_db->select('goods_view',['goods_id','shop_id','view_num' => Medoo::raw('count(*)')],$where);
		$where = [];
		$where['AND']['view_time[>]'] = $now_time;
		$where['AND']['view_time[<]'] = $after_time;
		$w_db->delete('goods_view',$where);
		$w_db->exec('UNLOCK TABLES ');
		$w_db->commit();
		foreach($after_list as $item){
			$w_db->update('goods',['view[+]'=>$item['view_num']],['goods_id'=>$item['goods_id']]);
			$view_hour_info = $w_db->get("goods_view_hour",'*',['goods_id'=>$item['goods_id'],'shop_id'=>$item['shop_id'],'view_time'=>$now_time]);
			if( $view_hour_info ){
				$now_view_num = $view_hour_info['view_num']+$item['view_num'];
				$w_db->update('goods_view_hour',['view_num'=>$now_view_num],['goods_id'=>$item['goods_id'],'shop_id'=>$item['shop_id'],'view_time'=>$now_time]);
			}else{
				$insert_data = [];
				$insert_data['goods_id'] = $item['goods_id'];
				$insert_data['shop_id'] = $item['shop_id'];
				$insert_data['view_num'] = $item['view_num'];
				$insert_data['view_time'] = $now_time;
				$w_db->insert('goods_view_hour',$insert_data);
			}
		}
		return false;

	}
	public function goodsViewDayAction(){
		global $w_db;

		//将前一天的数据归档
		$Ctime = new Ctime();
		$now_timestamp = time();
		$time_format = 'Y-m-d 00:00:00';
		$now_time = $Ctime->custom($time_format,$now_timestamp);
		$before_time = $Ctime->custom($time_format,$now_timestamp-(3600*24));

		//开启事务
		$w_db->begin_transaction();
		$w_db->exec('LOCK TABLES c_new_goods_view_day WRITE,c_new_goods_view_hour WRITE ');

		$where = [];
		$where['AND']['view_time[>]'] = $before_time;
		$where['AND']['view_time[<]'] = $now_time;
		$where['GROUP'] = ['goods_id','shop_id'];
		$before_list = $w_db->select('goods_view_hour',['goods_id','shop_id','view_num' => Medoo::raw('sum(view_num)')],$where);


		foreach($before_list as $item){

			$view_hour_info = $w_db->get("goods_view_day",'*',['goods_id'=>$item['goods_id'],'shop_id'=>$item['shop_id'],'view_time'=>$before_time]);
			if( $view_hour_info ){
				$now_view_num = $item['view_num'];
				$w_db->update('goods_view_day',['view_num'=>$now_view_num],['goods_id'=>$item['goods_id'],'shop_id'=>$item['shop_id'],'view_time'=>$before_time]);
			}else{
				$insert_data = [];
				$insert_data['goods_id'] = $item['goods_id'];
				$insert_data['shop_id'] = $item['shop_id'];
				$insert_data['view_num'] = $item['view_num'];
				$insert_data['view_time'] = $before_time;
				$w_db->insert('goods_view_day',$insert_data);
			}

		}
		$w_db->exec('UNLOCK TABLES ');
		$w_db->commit();
		return false;
	}
	public function goodsViewMonthAction(){
		global $w_db;

		//将前一个月跟这个月的数据归档
		$Ctime = new Ctime();
		$now_timestamp = time();
		$time_format = 'Y-m-01 00:00:00';
		$now_time = $Ctime->custom($time_format,$now_timestamp);
		$before_time = $Ctime->custom($time_format,strtotime("-1 month"));
		$after_time = $Ctime->custom($time_format,strtotime("+1 month"));

		//开启事务
		$w_db->begin_transaction();
		$w_db->exec('LOCK TABLES c_new_goods_view_day WRITE,c_new_goods_view_month WRITE ');

		$where = [];
		$where['AND']['view_time[>]'] = $before_time;
		$where['AND']['view_time[<]'] = $now_time;
		$where['GROUP'] = ['goods_id','shop_id'];
		$before_list = $w_db->select('goods_view_day',['goods_id','shop_id','view_num' => Medoo::raw('sum(view_num)')],$where);


		foreach($before_list as $item){

			$view_hour_info = $w_db->get("goods_view_month",'*',['goods_id'=>$item['goods_id'],'shop_id'=>$item['shop_id'],'view_time'=>$before_time]);
			if( $view_hour_info ){
				$now_view_num = $item['view_num'];
				$w_db->update('goods_view_month',['view_num'=>$now_view_num],['goods_id'=>$item['goods_id'],'shop_id'=>$item['shop_id'],'view_time'=>$before_time]);
			}else{
				$insert_data = [];
				$insert_data['goods_id'] = $item['goods_id'];
				$insert_data['shop_id'] = $item['shop_id'];
				$insert_data['view_num'] = $item['view_num'];
				$insert_data['view_time'] = $before_time;
				$w_db->insert('goods_view_month',$insert_data);
			}

		}
		$w_db->exec('UNLOCK TABLES ');
		$w_db->commit();


		//开启事务
		$w_db->begin_transaction();
		$w_db->exec('LOCK TABLES c_new_goods_view_day WRITE,c_new_goods_view_month WRITE ');

		$where = [];
		$where['AND']['view_time[>]'] = $now_time;
		$where['AND']['view_time[<]'] = $after_time;
		$where['GROUP'] = ['goods_id','shop_id'];
		$after_list = $w_db->select('goods_view_day',['goods_id','shop_id','view_num' => Medoo::raw('sum(view_num)')],$where);

		foreach($after_list as $item){

			$view_hour_info = $w_db->get("goods_view_month",'*',['goods_id'=>$item['goods_id'],'shop_id'=>$item['shop_id'],'view_time'=>$now_time]);
			if( $view_hour_info ){
				$now_view_num = $item['view_num'];
				$w_db->update('goods_view_month',['view_num'=>$now_view_num],['goods_id'=>$item['goods_id'],'shop_id'=>$item['shop_id'],'view_time'=>$now_time]);
			}else{
				$insert_data = [];
				$insert_data['goods_id'] = $item['goods_id'];
				$insert_data['shop_id'] = $item['shop_id'];
				$insert_data['view_num'] = $item['view_num'];
				$insert_data['view_time'] = $now_time;
				$w_db->insert('goods_view_month',$insert_data);
			}

		}
		$w_db->exec('UNLOCK TABLES ');
		$w_db->commit();
		return false;
	}


	public function shopViewHourAction(){
		global $w_db;

		//将当期时间跟上一个小时的数据归档，防止时间临界点
		$Ctime = new Ctime();
		$now_timestamp = time();
		$time_format = 'Y-m-d H:00:00';
		$now_time = $Ctime->custom($time_format,$now_timestamp);
		$before_time = $Ctime->custom($time_format,$now_timestamp-3600);
		$after_time = $Ctime->custom($time_format,$now_timestamp+3600);

		//开启事务
		$w_db->begin_transaction();
		$w_db->exec('LOCK TABLES c_new_shop_view WRITE,c_new_shop_view_hour WRITE ');

		$where = [];
		$where['AND']['view_time[>]'] = $before_time;
		$where['AND']['view_time[<]'] = $now_time;
		$where['GROUP'] = ['shop_id'];
		$before_list = $w_db->select('shop_view',['shop_id','view_num' => Medoo::raw('count(*)')],$where);

		$sql = 'SELECT shop_id, count(*) AS view_user_num
						FROM (SELECT shop_id, user_id, count(*) AS user_view_num
						      FROM c_new_shop_view
						      WHERE (view_time > \'%s\' AND view_time < \'%s\')
						      GROUP BY shop_id, user_id) AS tem_table
						GROUP BY shop_id
						';
		$sql = sprintf($sql,$before_time,$now_time);
		$before_list2 = $w_db->query($sql)->fetchAll();
		foreach($before_list as $key=>$item){
			foreach($before_list2 as $n_item){
				if($n_item['shop_id'] == $item['shop_id']){
					$before_list[$key]['view_user_num'] = $n_item['view_user_num'];
				}
			}
		}

		foreach($before_list as $key=>$item){


			$view_hour_info = $w_db->get("shop_view_hour",'*',['shop_id'=>$item['shop_id'],'view_time'=>$before_time]);
			if( $view_hour_info ){
				$now_view_num = $view_hour_info['view_num']+$item['view_num'];
				$w_db->update('shop_view_hour',['view_num'=>$now_view_num,'view_user_num'=>$item['view_user_num']],['shop_id'=>$item['shop_id'],'view_time'=>$before_time]);
			}else{
				$insert_data = [];
				$insert_data['shop_id'] = $item['shop_id'];
				$insert_data['view_num'] = $item['view_num'];
				$insert_data['view_user_num'] = $item['view_user_num'];
				$insert_data['view_time'] = $before_time;
				$w_db->insert('shop_view_hour',$insert_data);
			}

		}
		$where = [];
		$where['AND']['view_time[>]'] = $before_time;
		$where['AND']['view_time[<]'] = $now_time;
		$w_db->delete('shop_view',$where);
		$w_db->exec('UNLOCK TABLES ');
		$w_db->commit();

		//查询后一个小时的数据。
		$w_db->begin_transaction();
		$w_db->exec('LOCK TABLES c_new_shop_view WRITE,c_new_shop_view_hour WRITE ');
		$where = [];
		$where['AND']['view_time[>]'] = $now_time;
		$where['AND']['view_time[<]'] = $after_time;
		$where['GROUP'] = ['shop_id'];
		$after_list = $w_db->select('shop_view',['shop_id','view_num' => Medoo::raw('count(*)')],$where);
		$sql = 'SELECT shop_id, count(*) AS view_user_num
						FROM (SELECT shop_id, user_id, count(*) AS user_view_num
						      FROM c_new_shop_view
						      WHERE (view_time > \'%s\' AND view_time < \'%s\')
						      GROUP BY shop_id, user_id) AS tem_table
						GROUP BY shop_id
						';
		$sql = sprintf($sql,$now_time,$after_time);
		$after_list2 = $w_db->query($sql)->fetchAll();
		foreach($after_list as $key=>$item){
			foreach($after_list2 as $n_item){
				if($n_item['shop_id'] == $item['shop_id']){
					$after_list[$key]['view_user_num'] = $n_item['view_user_num'];
				}
			}
		}
		foreach($after_list as $item){
			$view_hour_info = $w_db->get("shop_view_hour",'*',['shop_id'=>$item['shop_id'],'view_time'=>$now_time]);
			if( $view_hour_info ){
				$now_view_num = $view_hour_info['view_num']+$item['view_num'];
				$w_db->update('shop_view_hour',['view_num'=>$now_view_num,'view_user_num'=>$item['view_user_num']],['shop_id'=>$item['shop_id'],'view_time'=>$now_time]);
			}else{
				$insert_data = [];
				$insert_data['shop_id'] = $item['shop_id'];
				$insert_data['view_num'] = $item['view_num'];
				$insert_data['view_time'] = $now_time;
				$insert_data['view_user_num'] = $item['view_user_num'];
				$w_db->insert('shop_view_hour',$insert_data);
			}
		}
		$where = [];
		$where['AND']['view_time[>]'] = $now_time;
		$where['AND']['view_time[<]'] = $after_time;
		$w_db->delete('shop_view',$where);
		$w_db->exec('UNLOCK TABLES ');
		$w_db->commit();



		return false;

	}



}
