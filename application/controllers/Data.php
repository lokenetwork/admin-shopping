<?php

class DataController extends CrontabController {

	private $max_shop_num = 2500;
	private $max_goods_num = 5000;

	public function init(){
		parent::init();
	}


	//店铺首页图片


	//模拟店铺数据
	function loadShopAction(){
		global $w_db;
		$redis = new Redis();
		$redis->pconnect($this->redis_server, $this->redis_port, 1);

		//先清空数据
		$w_db->exec('TRUNCATE c_new_shop');
		$w_db->exec('TRUNCATE c_new_shop_category');
		$redis->delete('shop_location');

		$shop_pic_list = [
			'/goods/20190324/5c979418b97e0.jpg',
			'/goods/shop_index/1.jpg',
			'/goods/shop_index/2.jpg',
			'/goods/shop_index/4.jpg',
			'/goods/shop_index/5.jpg',
			'/goods/shop_index/6.jpg',
			'/goods/shop_index/7.jpg',
			'/goods/shop_index/8.jpg',
			'/goods/shop_index/9.jpg',
			'/goods/shop_index/10.jpg',
		];
		$shop_logo_list = [
			'goods/test/1_100x100.png',
			'goods/test/2_100x100.png',
			'goods/test/3_100x100.png',
			'goods/test/4_100x100.png',
			'goods/test/5_100x100.png',
			'goods/test/6_100x100.png',
			'goods/test/7_100x100.png',
			'goods/test/8_100x100.png',
			'goods/test/9_100x100.png',
			'goods/test/10_100x100.png',
		];
		$shop_name_list = [
			'狄美琳达服饰店',
			'东润服饰店',
			'优衣库',
			'荣配服饰商行',
			'绫致时装',
			'华绅服装',
			'罗依服装',
			'卡蒙特轻纺服装',
			'361专卖店',
			'安踏专卖店',
		];
		$shop_list = [];
		$shop_list[0] = [
			'shop_name' => '狄美琳达服饰店',
			'shop_profile' => '本店主营男装，独家代理品牌，价格实惠，童幼无欺，欢迎你前来。',
			'logo'=>'goods/test/1_100x100.png',
			'longitude'=> '113.953195',
			'latitude'=> '22.562101',
			'province'=>'广东省',
			'city'=>'深圳市',
			'address'=> '广东省 深圳市 宝安区 壹方城',
			'address_display'=>'广东省 深圳市 宝安区 壹方城二楼',
			'passport_user_id'=>1,
			'pic_id'=>'1',
			'pic_url'=>'/goods/20190324/5c979418b97e0.jpg',
		];
		$rand_data_index = 0;


		for($i=0; $i < $this->max_shop_num; $i++){

			$longitude = '113.953195';
			$latitude = '22.562101';

			$shop_data = [
				'shop_name' => $shop_name_list[$rand_data_index],
				'shop_profile' => '本店主营男装，独家代理品牌，价格实惠，童幼无欺，欢迎你前来。',
				'logo'=>$shop_logo_list[$rand_data_index],
				'longitude'=> $longitude,
				'latitude'=>$latitude,
				'province'=>'广东省',
				'city'=>'深圳市',
				'address'=> '广东省 深圳市 宝安区 壹方城',
				'address_display'=>'广东省 深圳市 宝安区 壹方城二楼',
				'passport_user_id'=>$i+1,
				'goods_num'=>100,
				'qq'=>'1964329685',
				'wechat'=>'13723772347',
				'mobile'=>'13723772347',
				'email'=>'13723772347@qq.com',
				'contact'=>'loken',
				'add_time'=>'2019-02-01 00:00:00',
				'last_update_time'=>'2019-02-01 00:00:00',
				'lock'=>0,
				'is_delete'=>0,
				'goods_sku'=>100,
				'is_check'=>1,
				'pic_id'=>'1',
				'pic_url'=>$shop_pic_list[$rand_data_index],
			];
			$w_db->insert('shop',$shop_data);
			$shop_id = $w_db->id();
			$redis->rawCommand('geoadd', 'shop_location', $longitude, $latitude, $shop_id);

			//载入店铺分类
			$category = [
				'category_name'=>'外套',
				'shop_id'=>$shop_id,
				'parent_id'=>0,
				'level'=>0,
			];
			$w_db->insert('shop_category',$category);

			$rand_data_index++;
			if( $rand_data_index >= 10 ){
				$rand_data_index = 0;
			}
		}

		return false;
	}

	function clearGoodsDataAction(){
		$redis = new Redis();
		$redis->pconnect($this->redis_server, $this->redis_port, 1);

		global $w_db;
		//先清空数据
		$w_db->exec('TRUNCATE c_new_goods');
		$w_db->exec('TRUNCATE c_new_goods_sku');
		$w_db->exec('TRUNCATE c_new_goods_intro');
		$w_db->update('pachong_data', ['value' => 1], ['name' => 'load_goods']);

		for($i = 0; $i < $this->max_shop_num; $i++){
			$redis->delete('goods_shop_' . ($i + 1));
		}
		return false;
	}

	function loadDictionaryAction(){
		global $w_db;

		$word_list  = ['婩泺儿', '连衣裙', '女装', '2019', '春夏', '新品', '女装', '性感', '短袖',
				'套装女', '韩版', '淑女', '潮流', '修身', '显瘦', '两件套', '时尚', '裙子',
				'钞库', '春夏', '新款', '女装', '时尚', '套装女', '韩版', '超显瘦', '中长款', '两件套', '俏柔', '性感', '气质',
				'套装', '连衣裙', '滋涩', '衣服', '春装', '新款女', '一字肩', '裙子', '汉服', '沙滩裙', '两件套',
				'佐莎朵', '长袖', '春夏季', '女装', '碎花', '白色', '中长款', '收腰', '修身', '雪纺', 'A字裙', '海边', '度假', '沙滩裙',
				'长袖', '大码', '芙绮姿', 'Acosh', '针织', '蕾丝', '印花', '轻奢', '品牌', '气质', '很仙的', '法国', '小众', '流行', '收腰', '裙子', '秋',
				'乔途', '风衣', '男', '中长款', '2019', '春秋季', '新款', '黑色', '免烫', '外套', '显瘦', '男士', '大衣',
				'麦图', '男神', '保暖', '防风', '夹克', '防晒', '衣服', '帅气', '情侣', '连帽', '修身',
				'高梵', '牛仔裤', '青年', '休闲', '牛仔裤', '小脚', '长裤', '男士', 'MEIRUNE', '夏季', '浅色', '破洞', '百搭', '乞丐裤', '九分', '裤子',
				'花花公子',
				'商务',
				'男士',
				'西服',
				'套装男',
				'上班',
				'职业装',
				'正装',
				'工作',
				'结婚',
				'新郎',
				'礼服',
				'外套',
			];
		$word_list = array_unique($word_list);

		foreach($word_list as $item){
			$w_db->insert('search_word',['word'=>$item]);
		}
		return false;
	}

	//模拟商品数据
	function loadGoodsAction(){
		

		global $w_db;
		$redis = new Redis();
		$redis->pconnect($this->redis_server, $this->redis_port, 1);

		$w_db->begin_transaction();
		//多线程分页处理
		$current_page = $w_db->query('SELECT "value" FROM "c_new_pachong_data" WHERE "name" = \'load_goods\' FOR UPDATE ')->fetch()['value'];
		$page_num = 10;
		$start = ($current_page-1)*$page_num;

		if( $start == 0 ){
			$time = explode(' ', microtime());
			echo "<h1>开始时间: " . date("Y-m-d H:i:s", $time[1]) . ":" . $time[0] . "</h1>\r\n";

		}

		if( $start >= $this->max_shop_num ){

			$time = explode(' ', microtime());
			echo "<h1>结束时间: " . date("Y-m-d H:i:s", $time[1]) . ":" . $time[0] . "</h1>\r\n";

			$w_db->rollback();
			sleep(200);

			exit;
		}
		$end = ($current_page)*$page_num;
		$w_db->update('pachong_data', ['value' => $current_page + 1], ['name' => 'load_goods']);
		$w_db->commit();


		$info_list = [
			[
				'name'=>'婩泺儿连衣裙女装2019春夏新品女装性感连衣裙短袖套装女韩版淑女潮流女装修身显瘦两件套时尚套装裙子',
				'pic'=>['goods/goods_test/10-1.jpg', 'goods/goods_test/10-2.jpg',],
			],
			[
				'name'=>'钞库连衣裙2019春夏新款女装时尚套装女韩版超显瘦中长款两件套俏柔性感气质套装连衣裙',
				'pic'=>['goods/goods_test/9-1.jpg', 'goods/goods_test/9-2.jpg',],
			],
			[
				'name'=>'滋涩连衣裙2019春夏新品女装韩版时尚套装女衣服春装新款女一字肩裙子汉服沙滩裙两件套',
				'pic'=>['goods/goods_test/8-1.jpg', 'goods/goods_test/8-2.jpg',],
			],
			[
				'name'=>'佐莎朵长袖连衣裙2019春夏季女装碎花白色收腰中长款修身雪纺A字裙海边度假沙滩裙',
				'pic'=>['goods/goods_test/8-1.jpg', 'goods/goods_test/8-2.jpg',],
			],
			[
				'name'=>'芙绮姿针织连衣裙2019春季新品新款韩版修身长袖大码女装春装蕾丝雪纺印花性感两件套时尚套装女裙子',
				'pic'=>['goods/goods_test/7-1.jpg', 'goods/goods_test/7-2.jpg',],
			],
			[
				'name'=>'Acosh轻奢品牌女装雪纺连衣裙中长款长袖修身气质2019春装新款女装很仙的法国小众流行收腰裙子秋',
				'pic'=>['goods/goods_test/6-1.jpg', 'goods/goods_test/6-2.jpg',],
			],
			[
				'name'=>'乔途 风衣男中长款2019春秋季新款黑色修身免烫外套显瘦流行男士大衣 ',
				'pic'=>['goods/goods_test/5-1.jpg', 'goods/goods_test/5-2.jpg',],
			],
			[
				'name'=>'麦图 风衣男 中长款2019春季新品男神风衣保暖防风夹克防晒衣服韩版帅气情侣连帽修身',
				'pic'=>['goods/goods_test/4-1.jpg', 'goods/goods_test/4-2.jpg',],
			],
			[
				'name'=>'高梵 牛仔裤男2019春季新款青年休闲修身小脚韩版潮流休闲长裤男士牛仔裤',
				'pic'=>['goods/goods_test/3-1.jpg', 'goods/goods_test/3-2.jpg',],
			],
			[
				'name'=>'MEIRUNE 牛仔裤男2019夏季浅色破洞牛仔裤男韩版修身百搭乞丐裤小脚九分裤子男',
				'pic'=>['goods/goods_test/2-1.jpg', 'goods/goods_test/2-2.jpg',],
			],
			[
				'name'=>'花花公子 商务男士西服套装男修身职业装正装上班工作西装结婚新郎礼服外套',
				'pic'=>['goods/goods_test/1-1.jpg', 'goods/goods_test/1-2.jpg',],
			],
		];


		for($i = $start; $i < $end; $i++){
			$redis->delete('goods_shop_'.($i+1));

			$shop_id = $i+1;
			$rand_data_index = 1;

			//批量插入goods表数据
			$goods_data = [];
			$goods_sku_data = [];
			$intro_data = [];
			$goods_shop_set_data = [];
			for($j = 0; $j < $this->max_goods_num; $j++){
				$goods_data[] = [
					'goods_name'=>$info_list[$rand_data_index]['name'],
					'shop_id'=>$shop_id,
					'ucat_id'=>$shop_id,
					'goods_number'=>1,
					'goods_price'=>100,
					'is_new'=>1,
					'is_hot'=>1,
					'is_cheap'=>1,
					'is_on_sale'=>1,
					'first_picture'=>$info_list[$rand_data_index]['pic'][0],
					'first_picture_id'=>1,
					'view'=>10,
					'collect'=>10,
					'add_time'=>'2019-02-02 00:00:00',
					'last_update_time'=>'2019-02-02 00:00:00',
					'lock'=>0,
					'is_delete'=>0,
				];

				$goods_id = ($shop_id-1)*$this->max_goods_num+$j+1;
				$goods_sku_data[] = [
					'goods_id'=> $goods_id,
					'color'=>'原色',
					'size'=>'小码',
					'sku_price'=>'100',
					'sku_num'=>'20',
					'sku_code'=>'00000000',
					'pic_url'=>$info_list[$rand_data_index]['pic'][1],
					'pic_id'=> 1,
				];
				$goods_sku_data[] = [
					'goods_id'=> $goods_id,
					'color'=>'原色',
					'size'=>'大码',
					'sku_price'=>'110',
					'sku_num'=>'20',
					'sku_code'=>'00000000',
					'pic_url'=>$info_list[$rand_data_index]['pic'][1],
					'pic_id'=> 1,
				];

				//todo,后面再一次性更新goods_intro表。
				$intr = '<p><img src="http://192.168.0.120/goods/20190407/5caab81225034.jpg" alt="794c02bc7dd3825b.jpg" /><img src="http://192.168.0.120/goods/20190407/5caab812b0e8c.jpg" alt="81bb7018324e204c.jpg" /><img src="http://192.168.0.120/goods/20190407/5caab81318208.jpg" alt="5ae96a435430a682.jpg" /><img src="http://192.168.0.120/goods/20190407/5caab813610b7.jpg" alt="0627dddee7e88da3.jpg" /><img src="http://192.168.0.120/goods/20190407/5caab813c6900.jpg" alt="2e14f04f0c659dfe.jpg" /><img src="http://192.168.0.120/goods/20190407/5caab8142882b.jpg" alt="df5be247822ce922.jpg" /><img src="http://192.168.0.120/goods/20190407/5caab81484ee1.jpg" alt="662087d29b15ff25.jpg" /><img src="http://192.168.0.120/goods/20190407/5caab814e01b4.jpg" alt="ac7b38624dff43b4.jpg" /><img src="http://192.168.0.120/goods/20190407/5caab815581d3.jpg" alt="f2a0652886e87499.jpg" /><img src="http://192.168.0.120/goods/20190407/5caab8158b6f8.jpg" alt="3455aa1463d8898e.jpg" /><img src="http://192.168.0.120/goods/20190407/5caab815deddf.jpg" alt="7739642d9f8c5381.jpg" /><img src="http://192.168.0.120/goods/20190407/5caab8164d15b.jpg" alt="05cdcf8e44783c4c.jpg" /><img src="http://192.168.0.120/goods/20190407/5caab8169c49a.jpg" alt="ae5c5dadea4d7ba3.jpg" /><img src="http://192.168.0.120/goods/20190407/5caab817112fd.jpg" alt="23f2ffbbe67f0579.jpg" /><img src="http://192.168.0.120/goods/20190407/5caab817694a7.jpg" alt="20414c78335bb004.jpg" /><img src="http://192.168.0.120/goods/20190407/5caab817b7632.jpg" alt="89cf8ac9adff5959.jpg" /><img src="http://192.168.0.120/goods/20190407/5caab81829be0.jpg" alt="e28a46c0bbcd4e42.jpg" /><img src="http://192.168.0.120/goods/20190407/5caab818920f8.jpg" alt="ae53948f770ba451.jpg" /><img src="http://192.168.0.120/goods/20190407/5caab819111c0.jpg" alt="a3cf16ac9f4bbf4b.jpg" /><img src="http://192.168.0.120/goods/20190407/5caab8195ce91.jpg" alt="363b3fb7b101f8b2.jpg" /><img src="http://192.168.0.120/goods/20190407/5caab819bad1d.jpg" alt="b6ff900661274433.jpg" /><br /></p><p><br /></p>';
				$intr = 'a';
				$intro_data[] = ['goods_id'=>$goods_id,'intro'=>$intr];

				//把商品ID加入 redis
				$goods_shop_set_data[] = $goods_id;

				$rand_data_index++;
				if( $rand_data_index >= 10 ){
					$rand_data_index = 0;
				}
			}
			$goods_shop_set = "goods_shop_".$shop_id;
			$redis->sAddArray($goods_shop_set,$goods_shop_set_data);
			$w_db->insert('goods',$goods_data);
			$w_db->insert('goods_sku', $goods_sku_data);
			$w_db->insert('goods_intro',$intro_data);
		}

		return false;
	}

	function change_to_memory_engineAction(){
		global $w_db;
		$table_list = [
			'c_new_goods',
			'c_new_goods_sku',
			'c_new_goods_intro',
			'c_new_search_word',
		];
		foreach($table_list as $table_name){
			$w_db->exec("alter table $table_name engine=memory;");
		}
		return false;
	}

	function change_to_innodb_engine(){
		global $w_db;
		$table_list = [
			'c_new_goods',
			'c_new_goods_sku',
			'c_new_goods_intro',
			'c_new_search_word',
		];
		foreach($table_list as $table_name){
			$w_db->exec("alter table $table_name engine=innodb;");
		}
		return false;
	}

	public function reindexPrepareAction(){
		global  $r_db,$w_db;
		$word_list = $r_db->select('search_word',"*");
		$redis = new Redis();
		$redis->pconnect($this->redis_server, $this->redis_port, 1);
		$redis->delete('word_set');
		foreach($word_list as $item){
			$redis->sAdd('word_set',$item['word']);
			//把词条关联的商品信息删除掉。
			$redis->delete("word_".$item['word']."_goods");
		}
		$w_db->exec("TRUNCATE c_new_search_goods_word");
		$w_db->update('pachong_data', ['value' => 1], ['name' => 'search_reindex']);
		return false;
	}

	public function reindexAction(){
		global  $w_db;

		//truncate 掉 goods_word 表
		$w_db->begin_transaction();
		//多线程分页处理
		$current_page = $w_db->query('SELECT "value" FROM "c_new_pachong_data" WHERE "name" = \'search_reindex\' FOR UPDATE ')->fetch()['value'];
		$page_num = 1000;
		$start = ($current_page - 1) * $page_num;
		$goods_list = $w_db->select('goods', ['goods_id','goods_name','shop_id'], ['LIMIT' => [$start, $page_num]]);
		if( 1 == $current_page ){
			$time = explode(' ', microtime());
			echo "<h1>开始时间: " . date("Y-m-d H:i:s", $time[1]) . ":" . $time[0] . "</h1>\r\n";
		}
		if(!$goods_list){
			$w_db->rollback();
			$time = explode(' ', microtime());
			echo "<h1>结束时间: " . date("Y-m-d H:i:s", $time[1]) . ":" . $time[0] . "</h1>\r\n";
			sleep(3600);
			return false;
		}
		$w_db->update('pachong_data', ['value' => $current_page + 1], ['name' => 'search_reindex']);
		$w_db->commit();

		$redis = new Redis();
		$redis->pconnect($this->redis_server, $this->redis_port, 1);//长链接，本地host，端口为6379，超过1秒放弃链接
		$insert_data = [];
		$redis_insert_data = [];

		foreach($goods_list as $goods_data){
			//根据空格切分 输入
			$arr = explode(" ", $goods_data['goods_name']);
			$all_words = [];
			foreach($arr as $item){
				$all_words = array_merge($this->pullword($item, $redis), $all_words);
			}
			foreach($all_words as $word){
				//查询出词的id
				$word_id =  $w_db->get('search_word','word_id',['word'=>$word]);
				$insert_data[] = [
					'goods_id'=>$goods_data['goods_id'],
					'shop_id'=>$goods_data['shop_id'],
					'shop_id'=>$goods_data['shop_id'],
					'word_id'=>$word_id,
				];

				//存进redis
				$word_godds_set = "word_".$word."_goods";
				$redis_insert_data[$word_godds_set]['all_goods_id'][] = $goods_data['goods_id'];

			}
		}
		//$w_db->insert('search_goods_word',$insert_data);
		foreach($redis_insert_data as $key=>$item){
			$redis->sAddArray($key,$item['all_goods_id']);
		}
		//$time = explode(' ', microtime());
		//echo "<h1>开始时间: " . date("Y-m-d H:i:s", $time[1]) . ":" . $time[0] . "</h1>\r\n";
		return false;
	}

	function pullword($str, $redis){

		$charset = "UTF-8";
		//词条 redis集合
		$set_name = "word_set";

		//最大的词有10个字符,这里考虑了英文单词.
		$max_word_len = 10;

		$finish_word = [];

		$search_str = $str;

		$remain_str = $search_str;

		/* 正向最大词匹配 */
		//无限循环,符合特定条件才退出
		for(; ;){

			//如果待切分的短语 少于最大词长度
			if(mb_strlen($remain_str, $charset) < $max_word_len){
				$word_len = mb_strlen($remain_str, $charset);
			}else{
				$word_len = $max_word_len;
			}

			$maybe_word = mb_substr($remain_str, 0, $word_len, $charset);

			//判断分词是否完成
			$pullword_finish = false;

			//这个标示如果是true,则 maybe_word 里肯定有一个词.否者没有词则退出分词,分词结束
			$is_mark = false;

			for($i = 0; $i < $word_len; $i++){
				$tmp_word = mb_substr($maybe_word, 0, $word_len - $i, $charset);
				$word_exist = $redis->sIsMember($set_name, $tmp_word);

				//找到词,退出循环
				if($word_exist){
					$is_mark = true;
					array_push($finish_word, $tmp_word);

					//去除已经匹配到的短语
					$remain_str = substr($remain_str, strlen($tmp_word));
					if(mb_strlen($remain_str, $charset) <= 0){
						//分词已经完成
						$pullword_finish = true;
					}

					break;
				}else{

				}

			}

			//$maybe_word 里没有一个词,分词完成
			if(false == $is_mark){
				break;
			}

			if($pullword_finish){
				break;
			}

		}

		/* 逆向最大词匹配 */
		for(; ;){

			//如果待切分的短语 少于最大词长度,那就从待切分短语的开头读取字符串

			//如果待切分的短语 大于最大词长度,那就从 (待切分短语长度-最大词长度[10]) 位置读取 最大词长度[10] 个字符出来匹配

			//如果待切分的短语 少于最大词长度
			if(mb_strlen($remain_str, $charset) <= $max_word_len){
				$maybe_word = mb_substr($remain_str, 0, $max_word_len, $charset);
			}else{
				$maybe_word = mb_substr($remain_str, mb_strlen($remain_str, $charset) - $max_word_len, $max_word_len, $charset);
			}

			//判断分词是否完成
			$pullword_finish = false;

			//这个标示如果是true,则 maybe_word 里肯定有一个词.否者没有词则退出分词,分词结束
			$is_mark = false;

			$word_len = mb_strlen($maybe_word, $charset);

			for($i = 0; $i < $word_len; $i++){
				$tmp_word = mb_substr($maybe_word, $i, $word_len - $i, $charset);
				$word_exist = $redis->sIsMember($set_name, $tmp_word);

				//找到词,退出循环
				if($word_exist){
					$is_mark = true;
					array_push($finish_word, $tmp_word);

					//词在待切分短语的偏移位置
					$tmp_word_position = mb_strlen($remain_str, $charset) - mb_strlen($tmp_word, $charset);

					//去除已经匹配到的短语
					$remain_str = mb_substr($remain_str, 0, $tmp_word_position, $charset);
					if(mb_strlen($remain_str, $charset) <= 0){
						//分词已经完成
						$pullword_finish = true;
					}

					break;
				}else{

				}

			}

			//$maybe_word 里没有一个词,分词完成
			if(false == $is_mark){
				break;
			}

			if($pullword_finish){
				break;
			}
		}
		return $finish_word;

	}


}
