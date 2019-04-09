<?php

/**
 * @name NewWordController
 * @author root
 * @desc 新词发现
 *
 */
class NewWordController extends BaseController {

	public function init(){
		parent::init();
	}

	//抓取第三方商品数据,存储进我们的数据库
	public function pachongAction(){

		$start_time = explode(' ', microtime());
		//echo "<h1>开始时间: " . date("Y-m-d H:i:s", $start_time[1]) . ":" . $start_time[0] . "</h1>";

		global $w_db;

		$max_page_num = 100;
		//一次性获取多少页
		$page_step = 13;

		$w_db->begin_transaction();
		//锁一下
		$w_db->query('SELECT "value" FROM "c_new_pachong_data" WHERE "name" = \'current_page\' FOR UPDATE ');
		//查询出正在爬的分类名称
		$search = $w_db->get('cloth_category', 'cat_name', ['searching' => 1, 'finish' => 0]);

		//找不到已经完成的,就把一个为开始的设置成开始
		if(!$search){
			$new_search_data = $w_db->get('cloth_category', '*', ['searching' => 0, 'finish' => 0]);
			if(!$new_search_data){
                $w_db->rollback();
                echo "爬虫完成\r\n";
                sleep(50);
				return false;
			}else{
				//把分页数量重置为1;
				$w_db->update('pachong_data', ['value' => 1], ['name' => 'current_page']);
				$w_db->update('cloth_category', ['searching' => 1], ['cat_id' => $new_search_data['cat_id']]);
				$search = $new_search_data['cat_name'];
			}
		}


		$current_page = $w_db->query('SELECT "value" FROM "c_new_pachong_data" WHERE "name" = \'current_page\' FOR UPDATE ')->fetch()['value'];
		if($current_page > $max_page_num){
			//设置成已完成
			$w_db->update('cloth_category', ['finish' => 1], ['cat_name' => $search]);
			$w_db->commit();
			//重新加载页面
			//echo '<script>location.reload()</script>';
			return false;
		}

		//一次性处理10页的数据
		$end_page = $current_page + $page_step;
		if($end_page >= $max_page_num + 1){
			$end_page = $max_page_num + 1;
		}
		//更新页数
		$w_db->update('pachong_data', ['value' => $end_page], ['name' => 'current_page']);
		$w_db->commit();

		$insert_sql = "INSERT INTO c_new_pachong_goods_name (goods_name) VALUES ";
		//是否搜索到商品
		$has_search_goods = false;
		for($current_page = $current_page; $current_page < $end_page; $current_page++){
			$url_start = 1 + ($current_page - 1) * 26;
			$url = 'https://search.jd.com/Search?keyword=%s&enc=utf-8&qrst=1&rt=1&stop=1&vt=2&suggest=4.def.0.V19&wq=fuz&page=%s&s=%s&click=0';
			$url = sprintf($url, $search, $current_page, $url_start);
			$user_agent = "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_14_0) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/72.0.3626.109 Safari/537.36";
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE); // https请求 不验证证书和hosts
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_HEADER, 0);
			curl_setopt($ch, CURLOPT_USERAGENT, $user_agent); // 模拟用户使用的浏览器
			$html = curl_exec($ch);
			curl_close($ch);
			if($html === FALSE){
				file_put_contents('.//log/pachong.txt', "CURL Error:" . curl_error($ch) . $search . "\r\n", FILE_APPEND);
			}else{
				$reg = "/<div class=\"p-name p-name-type-2\">\s+<a target=\"_blank\" title=\".+\" href=\"\/\/item.jd.com\/.+\">\s+<em>(.+)<\/em>/";
				preg_match_all($reg, $html, $res);
				$goods_list = $res[1];
				if(!empty($goods_list)){
					//处理过滤html数据
					foreach($goods_list as $key => $item){
						$goods_list[$key] = strip_tags($item);
					}
					//拼接sql
					foreach($goods_list as $key => $item){
                        $has_search_goods = true;
                        $item = str_replace("'", "\\'", $item);
                        //删除不可打印字符
                        $item = preg_replace("/[^\PC\s]/u","",$item);
						$insert_sql .= "('{$item}'),";
					}
				}
			}
		}
		$insert_sql = trim($insert_sql, ',');
		//echo $insert_sql;
        if( $has_search_goods ){
            $w_db->exec($insert_sql);
        }

		$end_time = explode(' ', microtime());
		//echo "<h1>结束时间: " . date("Y-m-d H:i:s", $end_time[1]) . ":" . $end_time[0] . "</h1>";

		//js 刷新页面
		//echo '<script>location.reload()</script>';

		return false;

	}

	//生成分词文档
	public function testAction(){
		global $w_db;
		$current_page = $this->_get('p', 1);
		$page_num = 1000;
		$start = ($current_page - 1) * $page_num;
		$list = $w_db->select('pachong_goods_name', '*', ['LIMIT' => [$start, $page_num]]);
		if(!$list){
			echo "无数据!\r\n";sleep(10);
			return false;
		}
		foreach($list as $item){
			file_put_contents('./train_for_ws.txt', $item['goods_name'] . "\r\n", FILE_APPEND);
		}

		$script = '<script type="text/javascript">window.location="http://127.0.0.1:8081/Goods/test?p=' . ($current_page + 1) . '"</script>';
		echo($script);
		return false;
	}

	//生成候选词
	public function generateCandidateWordAction(){
		global $w_db;

		$start_time = explode(' ', microtime());
		//echo "<h1>开始时间: " . date("Y-m-d H:i:s", $start_time[1]) . ":" . $start_time[0] . "</h1>\r\n";

		$w_db->begin_transaction();
		//多线程分页处理
		$current_page = $w_db->query('SELECT "value" FROM "c_new_pachong_data" WHERE "name" = \'generate_candidate_word_current_page\' FOR UPDATE ')->fetch()['value'];
		$w_db->update('pachong_data', ['value' => $current_page + 1], ['name' => 'generate_candidate_word_current_page']);
		$page_num = 1000;
		$start = ($current_page - 1) * $page_num;
		$goods_list = $w_db->select('pachong_goods_name', '*', ['LIMIT' => [$start, $page_num]/*,'goods_name_id'=>3*/]);
		$_max_word_len = 3;
		if(!$goods_list){
            $w_db->rollback();
            echo "无数据!\r\n";
			sleep(10);
			return false;
		}
        $w_db->commit();
		//设置 mb 库的默认字符集,后面就不用写了
		mb_strlen("预设", 'utf8');
		//过滤好的文档数据
		$doc_arr = [];
		foreach($goods_list as $item){
			//$item["goods_name"] = '001(黑色) 185/100A/XXXL';

			//根据空格切分候选词
			$small_doc_arr = explode(" ", $item["goods_name"]);
			foreach($small_doc_arr as $l1_item){

				//英文本身应该是一个词,咱们那个分词算法应该要这么干,英文不需要存储在数据库,因为很容易识别

				//不是中文的字符,应该就是词的分词线,再次切成数组.
				//上一次分出数组的位置
				$last_cut_position = 0;
				//判断是否已经遇到非中文的字符
				$meet_not_chinese = false;
				$l1_item_strlen = mb_strlen($l1_item, 'utf8');
				for($i = 0; $i < $l1_item_strlen; $i++){
					//是否遍历到最后了
					if($i + 1 == $l1_item_strlen){
						$word = mb_substr($l1_item, $i, 1, 'utf8');
						//如果最后一个字符是非中文,直接删掉.
						if(preg_match('/[\x{4e00}-\x{9fa5}]/u', $word) && !preg_match("/[，。《》、？：；“”‘’｛｝【】【】（）()丨]+/u", $word)){
							$my_candidate_word = mb_substr($l1_item, $last_cut_position, ($l1_item_strlen - $last_cut_position), "utf8");
							if(mb_strlen($my_candidate_word, 'utf8') >= 1){
								array_push($doc_arr, $my_candidate_word);
							}
						}else{
							$my_candidate_word = mb_substr($l1_item, $last_cut_position, ($i - $last_cut_position), "utf8");
							if(mb_strlen($my_candidate_word, 'utf8') >= 1){
								array_push($doc_arr, $my_candidate_word);
							}
						}
					}else{
						$word = mb_substr($l1_item, $i, 1, 'utf8');
						//判断字符是不是中文
						if(preg_match('/[\x{4e00}-\x{9fa5}]/u', $word) && !preg_match("/[，。《》、？：；“”‘’｛｝【】【】（）()丨]+/u", $word)){
							$meet_not_chinese = false;
							continue;
						}else{
							//如果 $i 大于 0 而且 之前都没遇到英文,就需要把字分到数组里
							if($i > 0 && false == $meet_not_chinese){
								$meet_not_chinese = true;
								//把 $l1_item $i 前面的一部分都作为一个候选词数据
								$my_candidate_word = mb_substr($l1_item, $last_cut_position, ($i - $last_cut_position), "utf8");
								if(mb_strlen($my_candidate_word, 'utf8') >= 1){
									array_push($doc_arr, $my_candidate_word);
								}
								$last_cut_position = $i + 1;
							}else{
								$meet_not_chinese = true;
								//如果 $i 等于 0 ,直接把这个字符串消除掉
								$last_cut_position = $i + 1;
							}
						}
					}

				}
			}
		}

		//批量插入文档数据
		$insert_sql = "INSERT INTO c_new_pachong_candidate_doc (doc_word,doc_len,right_end_two,right_end_three) VALUE ";
		foreach($doc_arr as $doc){
			$doc_length = mb_strlen($doc, 'utf8');
            //如果文档只有一个长度
            if( $doc_length  <= 2 ){
                $right_end_two= $doc;
                $right_end_three =$doc;
            }else if( $doc_length > 2  ){
                //获取文档后面两个字符
                $right_end_two = mb_substr($doc,$doc_length-2,2,"utf8");
                $right_end_three= mb_substr($doc,$doc_length-3,3,"utf8");
            }
			$insert_sql .= "('{$doc}','{$doc_length}','{$right_end_two}','{$right_end_three}'),";
		}
		$insert_sql = rtrim($insert_sql, ',');
		$w_db->exec($insert_sql);

		//批量插入候选词
		$insert_sql = "INSERT INTO c_new_pachong_candidate_word (candidate_word) VALUE ";
		//拿出候选词
		foreach($doc_arr as $doc){
			$doc_length = mb_strlen($doc, 'utf8');

			for($i = 0; $i < $doc_length; $i++){
				if($i + $_max_word_len > $doc_length){
					$position_end = $doc_length - $i;
				}else{
					$position_end = $_max_word_len;
				}
				for($j = 0; $j < $position_end; $j++){
					$__candidate_word = mb_substr($doc, $i, $j + 1, 'utf8');
					if(mb_strlen($__candidate_word, 'utf8') >= 1){
						$insert_sql .= "('{$__candidate_word}'),";
					}
				}
			}
		}
		$insert_sql = rtrim($insert_sql, ',');
		$insert_sql .= " ON DUPLICATE KEY UPDATE candidate_frequent=candidate_frequent+1 ";
		//ON DUPLICATE KEY UPDATE  可能会导致死锁。
        $end_time = explode(' ', microtime());
        //echo "<h1>开始时间: " . date("Y-m-d H:i:s", $end_time[1]) . ":" . $end_time[0] . "</h1>\r\n";
        $w_db->begin_transaction();
        $w_db->exec('LOCK TABLES c_new_pachong_candidate_word WRITE ');
        $w_db->exec($insert_sql);
        $w_db->exec('UNLOCK TABLES ');
        $end_time = explode(' ', microtime());
        //echo "<h1>结束时间: " . date("Y-m-d H:i:s", $end_time[1]) . ":" . $end_time[0] . "</h1>";
        $w_db->commit();

		$end_time = explode(' ', microtime());
		//echo "<h1>结束时间: " . date("Y-m-d H:i:s", $end_time[1]) . ":" . $end_time[0] . "</h1>";

		//echo '<script>location.reload()</script>';
		return false;

	}

	//生成全部文档
	public function generateAllDocAction(){
		global $w_db;

		$current_page = $this->_get('p', 1);
		$page_num = 30000;
		$start = ($current_page - 1) * $page_num;

		//查询出所有文档,用空白字符隔开,组成一个大的文档.
		$doc_list = $w_db->select('pachong_candidate_doc', 'doc_word', ['LIMIT' => [$start, $page_num]]);
		if(!$doc_list){
			echo "无数据!\r\n";
			return false;
		}
		//头尾加上空格
		$big_doc = ' ';
		foreach($doc_list as $item){
			$big_doc .= $item . ' ';
		}
        $big_doc .=  ' ';
        file_put_contents('./data/all_doc.txt', $big_doc, FILE_APPEND);

		$script = '<script type="text/javascript">window.location="/NewWord/generateAllDoc?p=' . ($current_page + 1) . '"</script>';
		echo $script;
		return false;
	}

	//计算左边信息熵的集合
	public function calculationLeftEntropyAction(){
		global $w_db;

		//$start_time = explode(' ', microtime());
		//echo "<h1>开始时间: " . date("Y-m-d H:i:s", $start_time[1]) . ":" . $start_time[0] . "</h1>\r\n";

		$w_db->begin_transaction();
		//多线程分页处理
		$current_page = $w_db->query('SELECT "value" FROM "c_new_pachong_data" WHERE "name" = \'calculation_left_entropy_current_page\' FOR UPDATE ')->fetch()[0];

		$page_num = 2;
		$start = ($current_page - 1) * $page_num;
		$candidate_word_list = $w_db->select('pachong_candidate_word', '*', ['LIMIT' => [$start, $page_num]]);
		if(!$candidate_word_list){
            $w_db->rollback();
			echo '无数据!'."\r\n";
			sleep(10);
			return false;
		}
		//$w_db->update('pachong_data', ['value' => $current_page + 1], ['name' => 'calculation_left_entropy_current_page']);
		$w_db->commit();

		//查询出所有文档,用空白字符隔开,组成一个大的文档.
        $all_doc_path = APPLICATION_PATH.'/data/all_doc.txt';
		$doc_file = fopen($all_doc_path, 'r');
		$big_doc = fread($doc_file, filesize($all_doc_path));
        $big_doc_len = mb_strlen($big_doc,"utf8");
		fclose($doc_file);

		$insert_list = [];
        $start_time = explode(' ', microtime());
        echo "<h1>开始时间: " . date("Y-m-d H:i:s", $start_time[1]) . ":" . $start_time[0] . "</h1>\r\n";

        foreach($candidate_word_list as $item){
            /*  my 实现 start */
            $candidate_word = $item['candidate_word'];
            $candidate_word_len = mb_strlen($candidate_word,"utf8");
            //todo，后面这个删除，在sql里加条件
            if( 1 == $candidate_word_len ){
                continue;
            }

            if( 2== $candidate_word_len ){
                //把候选词拆成单个字的数组。
                $first_word = mb_substr($candidate_word,0,1,"utf8");
                $second_word = mb_substr($candidate_word,1,1,"utf8");
                //循环读取遍历文档内容，自己实现匹配算法。
                $first_word_match = false;
                $second_word_match = false;
                //匹配到第一个字的位置
                $first_word_match_position = 0;
                for($i = 0; $i < $big_doc_len; $i++){
                    if( $i > 617260 ){
                        break;
                    }
                    //获取文档的一个字符。如果是空格符合就跳过
                    $a_word = mb_substr($big_doc,$i,1);
                    //todo，如果等于换行符，跳过，充值所有状态
                    if( "\r\n" == $a_word ){
                        $first_word_match = false;
                        $second_word_match = false;
                        continue;
                    }

                    //这里有非常多的情况，
                    //第一种，第二，第三还没匹配中的状态下，判断第一个字符是否匹配
                    if( false == $first_word_match ){
                        if( $first_word == $a_word ){
                            $first_word_match_position = $i;
                            $first_word_match = true;
                        }else{
                            $first_word_match = false;
                        }
                    }else if( true == $first_word_match && false == $second_word_match ){
                        if( $second_word == $a_word ){
                            $second_word_match = true;
                            //获取前一个字符
                            $left_word =  mb_substr($big_doc,$first_word_match_position-1,1);
                            $insert_list[$item['candidate_word_id']][$left_word] += 1;
                            //恢复状态
                            $first_word_match = false;
                            $second_word_match = false;
                        }else{
                            $first_word_match = false;
                            $second_word_match = false;
                        }
                    }
                }

            }
            else if( 3== $candidate_word_len ){
                //把候选词拆成单个字的数组。
                $first_word = mb_substr($candidate_word,0,1,"utf8");
                $second_word = mb_substr($candidate_word,1,1,"utf8");
                $third_word = mb_substr($candidate_word,2,1,"utf8");
                //循环读取遍历文档内容，自己实现匹配算法。
                $first_word_match = false;
                $second_word_match = false;
                $third_word_match = false;
                //匹配到第一个字的位置
                $first_word_match_position = 0;
                for($i = 0; $i < $big_doc_len; $i++){
                    //获取文档的一个字符。如果是空格符合就跳过
                    $a_word = mb_substr($big_doc,$i,1);
                    //如果等于换行符，跳过，充值所有状态
                    if( "\r\n" == $a_word ){
                        $first_word_match = false;
                        $second_word_match = false;
                        continue;
                    }

                    //这里有非常多的情况，
                    //第一种，第二，第三还没匹配中的状态下，判断第一个字符是否匹配
                    if( false == $first_word_match ){
                        if( $first_word == $a_word ){
                            $first_word_match_position = $i;
                            $first_word_match = true;
                        }else{
                            $first_word_match = false;
                        }
                    }else if( true == $first_word_match && false == $second_word_match ){
                        if( $second_word == $a_word ){
                            $second_word_match = true;
                        }else{
                            $first_word_match = false;
                            $second_word_match = false;
                        }
                    }else if( true == $first_word_match && true == $second_word_match && false == $third_word_match ){
                        if( $third_word== $a_word ){
                            $third_word_match = true;
                            //获取前一个字符
                            $left_word =  mb_substr($big_doc,$first_word_match_position-1,1);
                            $insert_list[$item['candidate_word_id']][$left_word] += 1;
                            //恢复状态
                            $first_word_match = false;
                            $second_word_match = false;
                            $third_word_match = false;
                        }else{
                            $first_word_match = false;
                            $second_word_match = false;
                            $third_word_match = false;
                        }
                    }

                }

            }
            /*  my 实现 end */


            /*  preg 实现 start
			$left_entropy_preg = "/(\S)" . $item['candidate_word'] . "/u";
			preg_match_all($left_entropy_preg, $big_doc, $res);
			//$left_list = $res[1];
			//如果没有左集合,算熵值比较的时候,如果为0,则选右集合.
			if(!empty($res[1])){
				foreach($res[1] as $i_item){
					if(isset($insert_list[$item['candidate_word_id']])){
						$insert_list[$item['candidate_word_id']][$i_item] += 1;
					}else{
						$insert_list[$item['candidate_word_id']][$i_item] = 1;
					}
				}
			}
            preg 实现 end */


		}

        var_dump($insert_list);

        $end_time = explode(' ', microtime());
        echo "<h1>结束时间: " . date("Y-m-d H:i:s", $end_time[1]) . ":" . $end_time[0] . "<h1>\r\n";
		//把商品名称插入数据库
		$insert_sql = 'INSERT INTO c_new_pachong_candidate_word_left (candidate_word_id,left_word,frequent) VALUES ';
		foreach($insert_list as $i_key => $i_item){
			foreach($i_item as $j_key => $j_item){
				$insert_sql .= "('{$i_key}','{$j_key}','{$j_item}'),";
			}
		}
		$insert_sql = trim($insert_sql, ',');
        $end_time = explode(' ', microtime());
        echo "<h1>结束时间: " . date("Y-m-d H:i:s", $end_time[1]) . ":" . $end_time[0] . "<h1>\r\n";
		//echo $insert_sql;
		$w_db->exec($insert_sql);
        $end_time = explode(' ', microtime());
        echo "<h1>结束时间: " . date("Y-m-d H:i:s", $end_time[1]) . ":" . $end_time[0] . "<h1>\r\n";

		//$end_time = explode(' ', microtime());
		///echo "<h1>结束时间: " . date("Y-m-d H:i:s", $end_time[1]) . ":" . $end_time[0] . "<h1>\r\n";
		//echo '<script>location.reload()</script>';
		return false;

	}

	//计算右边信息熵的集合
	public function calculationRightEntropyAction(){
		global $w_db;

		$start_time = explode(' ', microtime());
        //echo "<h1>开始时间: " . date("Y-m-d H:i:s", $start_time[1]) . ":" . $start_time[0] . "</h1>\r\n";


		$w_db->begin_transaction();
		//多线程分页处理
		$current_page = $w_db->query('SELECT "value" FROM "c_new_pachong_data" WHERE "name" = \'calculation_right_entropy_current_page\' FOR UPDATE ')->fetch()[0];
		$page_num = 200;
		$start = ($current_page - 1) * $page_num;
		$candidate_word_list = $w_db->select('pachong_candidate_word', '*', ['LIMIT' => [$start, $page_num]]);
		if(!$candidate_word_list){
            $w_db->rollback();
            echo "无数据!\r\n";
            sleep(10);
			return false;
		}
		$w_db->update('pachong_data', ['value' => $current_page + 1], ['name' => 'calculation_right_entropy_current_page']);
		$w_db->commit();

		//查询出所有文档,用空白字符隔开,组成一个大的文档.
        $all_doc_path = APPLICATION_PATH."/data/all_doc.txt";
        $doc_file = fopen($all_doc_path, "r");
        $big_doc = fread($doc_file, filesize($all_doc_path));
        fclose($doc_file);

		$insert_list = [];
		foreach($candidate_word_list as $item){
			$left_entropy_preg = "/" . $item['candidate_word'] . "(\S)/u";
			preg_match_all($left_entropy_preg, $big_doc, $res);
			$left_list = $res[1];
			//如果没有左集合,算熵值比较的时候,如果为0,则选右集合.
			if(empty($left_list)){

			}else{

				foreach($left_list as $i_item){
					if(isset($insert_list[$item['candidate_word_id']])){
						$insert_list[$item['candidate_word_id']][$i_item] += 1;
					}else{
						$insert_list[$item['candidate_word_id']][$i_item] = 1;
					}

				}

			}
		}
		//把商品名称插入数据库
		$insert_sql = "INSERT INTO c_new_pachong_candidate_word_right (candidate_word_id,right_word,frequent) VALUES ";
		foreach($insert_list as $i_key => $i_item){
			foreach($i_item as $j_key => $j_item){
				$insert_sql .= "('{$i_key}','{$j_key}','{$j_item}'),";
			}
		}
		$insert_sql = trim($insert_sql, ',');
		//echo $insert_sql;
		$w_db->exec($insert_sql);

		$end_time = explode(' ', microtime());
        //echo "<h1>结束时间: " . date("Y-m-d H:i:s", $end_time[1]) . ":" . $end_time[0] . "</h1>\r\n";
        //echo '<script>location.reload()</script>';
		return false;


	}

	//计算候选词最小信息熵
	public function caculateMinEntropyAction(){
		$start_time = explode(' ', microtime());
        //echo "<h1>开始时间: " . date("Y-m-d H:i:s", $start_time[1]) . ":" . $start_time[0] . "</h1>\r\n";


		global $w_db;

		$w_db->begin_transaction();
		//多线程分页处理
		$current_page = $w_db->query('SELECT "value" FROM "c_new_pachong_data" WHERE "name" = \'caculate_min_entropy_current_page\' FOR UPDATE ')->fetch()['value'];
		$page_num = 1000;
		$start = ($current_page - 1) * $page_num;
		$candidate_word_list = $w_db->select('pachong_candidate_word', '*', ['LIMIT' => [$start, $page_num]]);
		if(!$candidate_word_list){
		    $w_db->rollback();
			echo "无数据!\r\n";
			sleep(10);
			return false;
		}
		$w_db->update('pachong_data', ['value' => $current_page + 1], ['name' => 'caculate_min_entropy_current_page']);
		$w_db->commit();

		/*
        //查询出所有文档,用空白字符隔开,组成一个大的文档.
        $all_doc_path = APPLICATION_PATH."/data/all_doc.txt";
        $doc_file = fopen($all_doc_path, "r");
        $big_doc = fread($doc_file, filesize($all_doc_path));
        fclose($doc_file);
		*/

        //最大信息熵
		$max_entropy  = 88888888;

		foreach($candidate_word_list as $item){
            $candidate_word_len = mb_strlen($item['candidate_word'],"utf8");
            if( 1 == $candidate_word_len ){
                continue;
            }
		    //t查询出候选词是不是在文档的开头，如果是，左边信息熵无限大。
            /*
            $left_entropy_preg = "/(\s)" . $item['candidate_word'] . "/u";
            preg_match_all($left_entropy_preg, $big_doc, $res);
            $left_start_num = count($res[1]);
            */
            $left_start_num = $w_db->count("pachong_candidate_doc",["doc_word[~]"=> $item['candidate_word']."%"]);
            if( $left_start_num > 0 ){
                $left_entropy = $max_entropy;
            }else{
                //查询出左边字集合
                $left_list = $w_db->select('pachong_candidate_word_left', '*', ['candidate_word_id' => $item['candidate_word_id']]);
                //计算长度
                $left_lenght = $w_db->sum('pachong_candidate_word_left', 'frequent', ['candidate_word_id' => $item['candidate_word_id']]);
                $left_entropy = 0;
                foreach($left_list as $n_item){
                    $tem_res = $n_item['frequent'] / $left_lenght;
                    $left_entropy += -$tem_res * log($tem_res);
                }
            }

            //查询出候选词是不是在文档的结尾，如果是，右边信息熵无限大。
            /*
            $right_entropy_preg = "/" . $item['candidate_word'] . "(\s)/u";
            preg_match_all($right_entropy_preg, $big_doc, $res);
            $right_start_num = count($res[1]);
             */
            //获取候选词的长度，然后才知道查询哪个字段
            $candidate_word_len = mb_strlen($item['candidate_word'],"utf8");
            if( 2 == $candidate_word_len ){
                $right_start_num = $w_db->count("pachong_candidate_doc",["right_end_two"=> $item['candidate_word']]);
            }else if( 3 == $candidate_word_len ) {
                $right_start_num = $w_db->count("pachong_candidate_doc",["right_end_three"=> $item['candidate_word']]);
            }
            if( $right_start_num > 0 ){
                $right_entropy = $max_entropy;
            }else{
                $right_list = $w_db->select('pachong_candidate_word_right', '*', ['candidate_word_id' => $item['candidate_word_id']]);
                //计算长度
                $right_lenght = $w_db->sum('pachong_candidate_word_right', 'frequent', ['candidate_word_id' => $item['candidate_word_id']]);
                $right_entropy = 0;
                foreach($right_list as $n_item){
                    $tem_res = $n_item['frequent'] / $right_lenght;
                    $right_entropy += -$tem_res * log($tem_res);
                }
            }

            $candidate_word_entropy = min($right_entropy, $left_entropy);

            $update = [];
            $update['left_entropy'] = $left_entropy;
            $update['left_start_num'] = $left_start_num;
            $update['right_entropy'] = $right_entropy;
            $update['right_start_num'] = $right_start_num;
            $update['min_entropy'] = $candidate_word_entropy;
			$w_db->update('pachong_candidate_word', $update, ['candidate_word_id' => $item['candidate_word_id']]);

		}

		$end_time = explode(' ', microtime());
		//echo "<h1>结束时间: " . date("Y-m-d H:i:s", $end_time[1]) . ":" . $end_time[0] . "</h1>";
		//echo '<script>location.reload()</script>';
		return false;
	}

	//计算候选词的凝固程度
	public function caculatePmiAction(){
		$start_time = explode(' ', microtime());
		//echo "<h1>开始时间: " . date("Y-m-d H:i:s", $start_time[1]) . ":" . $start_time[0] . "</h1>";

		global $w_db;

		$w_db->begin_transaction();
		//多线程分页处理
		$current_page = $w_db->query('SELECT "value" FROM "c_new_pachong_data" WHERE "name" = \'caculate_pmi_current_page\' FOR UPDATE ')->fetch()['value'];
		$page_num = 500;
		$start = ($current_page - 1) * $page_num;
		$candidate_word_list = $w_db->select('pachong_candidate_word', '*', ['LIMIT' => [$start, $page_num]]);
		if(!$candidate_word_list){
			$w_db->rollback();
		    echo "无数据!\r\n";
			sleep(10);
			return false;
		}
		$w_db->update('pachong_data', ['value' => $current_page + 1], ['name' => 'caculate_pmi_current_page']);
		$w_db->commit();


		//计算出文档的总长度.
		$doc_len = $w_db->sum('pachong_candidate_doc', 'doc_len');

		foreach($candidate_word_list as $item){

			//候选词出现得概率
			$candidate_word_probability = $item['candidate_frequent'] / $doc_len;
			$candidate_word_pmi = 0;

			if(mb_strlen($item['candidate_word'], 'utf8') == 1){
				//长度为1的,凝固程度就是1
				$candidate_word_pmi = 1;
			}else if(mb_strlen($item['candidate_word'], 'utf8') == 2){
				$sub_part = [];
				$sub_part[0] = mb_substr($item['candidate_word'], 0, 1, 'utf8');
				$sub_part[1] = mb_substr($item['candidate_word'], 1, 1, 'utf8');
				$sub_word_info_1 = $w_db->get('pachong_candidate_word', '*', ['candidate_word' => $sub_part[0]]);
				$sub_word_info_2 = $w_db->get('pachong_candidate_word', '*', ['candidate_word' => $sub_part[1]]);
				$candidate_word_pmi = $candidate_word_probability / ($sub_word_info_1['candidate_frequent'] / $doc_len) / ($sub_word_info_2['candidate_frequent'] / $doc_len);
			}else if(mb_strlen($item['candidate_word'], 'utf8') == 3){

				$sub_part = [];
				$sub_part[0][0] = mb_substr($item['candidate_word'], 0, 1, 'utf8');
				$sub_part[0][1] = mb_substr($item['candidate_word'], 1, 2, 'utf8');
				$sub_word_info_1 = $w_db->get('pachong_candidate_word', '*', ['candidate_word' => $sub_part[0][0]]);
				$sub_word_info_2 = $w_db->get('pachong_candidate_word', '*', ['candidate_word' => $sub_part[0][1]]);
				$pmi_1 = $candidate_word_probability / ($sub_word_info_1['candidate_frequent'] / $doc_len) / ($sub_word_info_2['candidate_frequent'] / $doc_len);

				$sub_part[1][0] = mb_substr($item['candidate_word'], 0, 2, 'utf8');
				$sub_part[1][1] = mb_substr($item['candidate_word'], 2, 1, 'utf8');
				$sub_word_info_1 = $w_db->get('pachong_candidate_word', '*', ['candidate_word' => $sub_part[1][0]]);
				$sub_word_info_2 = $w_db->get('pachong_candidate_word', '*', ['candidate_word' => $sub_part[1][1]]);
				$pmi_2 = $candidate_word_probability / ($sub_word_info_1['candidate_frequent'] / $doc_len) / ($sub_word_info_2['candidate_frequent'] / $doc_len);
				$candidate_word_pmi = min($pmi_1, $pmi_2);

			}

			$update = [];
			$update['pmi'] = $candidate_word_pmi;
			$w_db->update('pachong_candidate_word', $update, ['candidate_word_id' => $item['candidate_word_id']]);
		}

		$end_time = explode(' ', microtime());
		//echo "<h1>结束时间: " . date("Y-m-d H:i:s", $end_time[1]) . ":" . $end_time[0] . "</h1>";

		$script = '<script type="text/javascript">window.location="/NewWord/caculatePmi?p=' . ($current_page + 1) . '"</script>';
		//echo $script;
		return false;
	}

    //计算文档后缀
    public function caculateDocSuffixAction(){
        $start_time = explode(' ', microtime());
        //echo "<h1>开始时间: " . date("Y-m-d H:i:s", $start_time[1]) . ":" . $start_time[0] . "</h1>\r\n";

        global $w_db;

        $w_db->begin_transaction();
        //多线程分页处理
        $current_page = $w_db->query('SELECT "value" FROM "c_new_pachong_data" WHERE "name" = \'caculate_doc_suffix_current_page\' FOR UPDATE ')->fetch()['value'];
        $page_num = 1000;
        $start = ($current_page - 1) * $page_num;
        $doc_list = $w_db->select('pachong_candidate_doc', '*', ['LIMIT' => [$start, $page_num]]);
        if(!$doc_list){
            $w_db->rollback();
            echo "无数据!\r\n";
            sleep(10);
            return false;
        }
        $w_db->update('pachong_data', ['value' => $current_page + 1], ['name' => 'caculate_doc_suffix_current_page']);
        $w_db->commit();

        foreach($doc_list as $doc){
                //如果文档只有一个长度
                if( $doc['doc_len']  <= 2 ){
                    $update_data = [];
                    $update_data['right_end_two'] = $doc['doc_word'];
                    $update_data['right_end_three'] = $doc['doc_word'];
                    $w_db->update('pachong_candidate_doc', $update_data, ['doc_id' => $doc['doc_id']]);
                }else if( $doc['doc_len'] > 2  ){
                    //获取文档后面两个字符
                    $right_end_two = mb_substr($doc['doc_word'],$doc['doc_len']-2,2,"utf8");
                    $right_end_three= mb_substr($doc['doc_word'],$doc['doc_len']-3,3,"utf8");
                    $update_data = [];
                    $update_data['right_end_two'] = $right_end_two;
                    $update_data['right_end_three'] = $right_end_three;
                    $w_db->update('pachong_candidate_doc', $update_data, ['doc_id' => $doc['doc_id']]);
                }
        }


        $end_time = explode(' ', microtime());
        //echo "<h1>结束时间: " . date("Y-m-d H:i:s", $end_time[1]) . ":" . $end_time[0] . "</h1>";
        //echo '<script>location.reload()</script>';
        return false;
    }

	//此函数用来提取出新词
	public function detectAction(){
		$this->calculationLeftEntropyAction();
		$this->calculationRightEntropyAction();
		$this->caculateMinEntropyAction();
		$this->caculatePmiAction();
	}

}