<?php

class Cstring {
  function Pinyin($_String, $_Code = 'UTF8'){ //GBK页面可改为gb2312，其他随意填写为UTF8
    $_DataKey = "a|ai|an|ang|ao|ba|bai|ban|bang|bao|bei|ben|beng|bi|bian|biao|bie|bin|bing|bo|bu|ca|cai|can|cang|cao|ce|ceng|cha" . "|chai|chan|chang|chao|che|chen|cheng|chi|chong|chou|chu|chuai|chuan|chuang|chui|chun|chuo|ci|cong|cou|cu|" . "cuan|cui|cun|cuo|da|dai|dan|dang|dao|de|deng|di|dian|diao|die|ding|diu|dong|dou|du|duan|dui|dun|duo|e|en|er" . "|fa|fan|fang|fei|fen|feng|fo|fou|fu|ga|gai|gan|gang|gao|ge|gei|gen|geng|gong|gou|gu|gua|guai|guan|guang|gui" . "|gun|guo|ha|hai|han|hang|hao|he|hei|hen|heng|hong|hou|hu|hua|huai|huan|huang|hui|hun|huo|ji|jia|jian|jiang" . "|jiao|jie|jin|jing|jiong|jiu|ju|juan|jue|jun|ka|kai|kan|kang|kao|ke|ken|keng|kong|kou|ku|kua|kuai|kuan|kuang" . "|kui|kun|kuo|la|lai|lan|lang|lao|le|lei|leng|li|lia|lian|liang|liao|lie|lin|ling|liu|long|lou|lu|lv|luan|lue" . "|lun|luo|ma|mai|man|mang|mao|me|mei|men|meng|mi|mian|miao|mie|min|ming|miu|mo|mou|mu|na|nai|nan|nang|nao|ne" . "|nei|nen|neng|ni|nian|niang|niao|nie|nin|ning|niu|nong|nu|nv|nuan|nue|nuo|o|ou|pa|pai|pan|pang|pao|pei|pen" . "|peng|pi|pian|piao|pie|pin|ping|po|pu|qi|qia|qian|qiang|qiao|qie|qin|qing|qiong|qiu|qu|quan|que|qun|ran|rang" . "|rao|re|ren|reng|ri|rong|rou|ru|ruan|rui|run|ruo|sa|sai|san|sang|sao|se|sen|seng|sha|shai|shan|shang|shao|" . "she|shen|sheng|shi|shou|shu|shua|shuai|shuan|shuang|shui|shun|shuo|si|song|sou|su|suan|sui|sun|suo|ta|tai|" . "tan|tang|tao|te|teng|ti|tian|tiao|tie|ting|tong|tou|tu|tuan|tui|tun|tuo|wa|wai|wan|wang|wei|wen|weng|wo|wu" . "|xi|xia|xian|xiang|xiao|xie|xin|xing|xiong|xiu|xu|xuan|xue|xun|ya|yan|yang|yao|ye|yi|yin|ying|yo|yong|you" . "|yu|yuan|yue|yun|za|zai|zan|zang|zao|ze|zei|zen|zeng|zha|zhai|zhan|zhang|zhao|zhe|zhen|zheng|zhi|zhong|" . "zhou|zhu|zhua|zhuai|zhuan|zhuang|zhui|zhun|zhuo|zi|zong|zou|zu|zuan|zui|zun|zuo";
    $_DataValue = "-20319|-20317|-20304|-20295|-20292|-20283|-20265|-20257|-20242|-20230|-20051|-20036|-20032|-20026|-20002|-19990" . "|-19986|-19982|-19976|-19805|-19784|-19775|-19774|-19763|-19756|-19751|-19746|-19741|-19739|-19728|-19725" . "|-19715|-19540|-19531|-19525|-19515|-19500|-19484|-19479|-19467|-19289|-19288|-19281|-19275|-19270|-19263" . "|-19261|-19249|-19243|-19242|-19238|-19235|-19227|-19224|-19218|-19212|-19038|-19023|-19018|-19006|-19003" . "|-18996|-18977|-18961|-18952|-18783|-18774|-18773|-18763|-18756|-18741|-18735|-18731|-18722|-18710|-18697" . "|-18696|-18526|-18518|-18501|-18490|-18478|-18463|-18448|-18447|-18446|-18239|-18237|-18231|-18220|-18211" . "|-18201|-18184|-18183|-18181|-18012|-17997|-17988|-17970|-17964|-17961|-17950|-17947|-17931|-17928|-17922" . "|-17759|-17752|-17733|-17730|-17721|-17703|-17701|-17697|-17692|-17683|-17676|-17496|-17487|-17482|-17468" . "|-17454|-17433|-17427|-17417|-17202|-17185|-16983|-16970|-16942|-16915|-16733|-16708|-16706|-16689|-16664" . "|-16657|-16647|-16474|-16470|-16465|-16459|-16452|-16448|-16433|-16429|-16427|-16423|-16419|-16412|-16407" . "|-16403|-16401|-16393|-16220|-16216|-16212|-16205|-16202|-16187|-16180|-16171|-16169|-16158|-16155|-15959" . "|-15958|-15944|-15933|-15920|-15915|-15903|-15889|-15878|-15707|-15701|-15681|-15667|-15661|-15659|-15652" . "|-15640|-15631|-15625|-15454|-15448|-15436|-15435|-15419|-15416|-15408|-15394|-15385|-15377|-15375|-15369" . "|-15363|-15362|-15183|-15180|-15165|-15158|-15153|-15150|-15149|-15144|-15143|-15141|-15140|-15139|-15128" . "|-15121|-15119|-15117|-15110|-15109|-14941|-14937|-14933|-14930|-14929|-14928|-14926|-14922|-14921|-14914" . "|-14908|-14902|-14894|-14889|-14882|-14873|-14871|-14857|-14678|-14674|-14670|-14668|-14663|-14654|-14645" . "|-14630|-14594|-14429|-14407|-14399|-14384|-14379|-14368|-14355|-14353|-14345|-14170|-14159|-14151|-14149" . "|-14145|-14140|-14137|-14135|-14125|-14123|-14122|-14112|-14109|-14099|-14097|-14094|-14092|-14090|-14087" . "|-14083|-13917|-13914|-13910|-13907|-13906|-13905|-13896|-13894|-13878|-13870|-13859|-13847|-13831|-13658" . "|-13611|-13601|-13406|-13404|-13400|-13398|-13395|-13391|-13387|-13383|-13367|-13359|-13356|-13343|-13340" . "|-13329|-13326|-13318|-13147|-13138|-13120|-13107|-13096|-13095|-13091|-13076|-13068|-13063|-13060|-12888" . "|-12875|-12871|-12860|-12858|-12852|-12849|-12838|-12831|-12829|-12812|-12802|-12607|-12597|-12594|-12585" . "|-12556|-12359|-12346|-12320|-12300|-12120|-12099|-12089|-12074|-12067|-12058|-12039|-11867|-11861|-11847" . "|-11831|-11798|-11781|-11604|-11589|-11536|-11358|-11340|-11339|-11324|-11303|-11097|-11077|-11067|-11055" . "|-11052|-11045|-11041|-11038|-11024|-11020|-11019|-11018|-11014|-10838|-10832|-10815|-10800|-10790|-10780" . "|-10764|-10587|-10544|-10533|-10519|-10331|-10329|-10328|-10322|-10315|-10309|-10307|-10296|-10281|-10274" . "|-10270|-10262|-10260|-10256|-10254";
    $_TDataKey = explode('|', $_DataKey);
    $_TDataValue = explode('|', $_DataValue);
    $_Data = array_combine($_TDataKey, $_TDataValue);
    arsort($_Data);
    reset($_Data);
    $pinyin['first'] = '';
    $pinyin['all'] = '';
    if($_Code != 'gb2312')
      $_String = $this->_U2_Utf8_Gb($_String);
    for($i = 0; $i < strlen($_String); $i++){
      $_P = ord(substr($_String, $i, 1));
      if($_P > 160){
        $_Q = ord(substr($_String, ++$i, 1));
        $_P = $_P * 256 + $_Q - 65536;
      }
      $pinyin['first'] .= $this->sub_str($this->_Pinyin($_P, $_Data), 1, false);
      $pinyin['all'] .= $this->_Pinyin($_P, $_Data);
    }

    $pinyin['all'] = preg_replace("/[^a-z0-9]*/", '', $pinyin['all']);
    return $pinyin;
  }

  function _Pinyin($_Num, $_Data){
    if($_Num > 0 && $_Num < 160){
      return chr($_Num);
    }elseif($_Num < -20319 || $_Num > -10247){
      return '';
    }else{
      foreach($_Data as $k => $v){
        if($v <= $_Num)
          break;
      }
      return $k;
    }
  }

  function _U2_Utf8_Gb($_C){
    $_String = '';
    if($_C < 0x80){
      $_String .= $_C;
    }elseif($_C < 0x800){
      $_String .= chr(0xC0 | $_C >> 6);
      $_String .= chr(0x80 | $_C & 0x3F);
    }elseif($_C < 0x10000){
      $_String .= chr(0xE0 | $_C >> 12);
      $_String .= chr(0x80 | $_C >> 6 & 0x3F);
      $_String .= chr(0x80 | $_C & 0x3F);
    }elseif($_C < 0x200000){
      $_String .= chr(0xF0 | $_C >> 18);
      $_String .= chr(0x80 | $_C >> 12 & 0x3F);
      $_String .= chr(0x80 | $_C >> 6 & 0x3F);
      $_String .= chr(0x80 | $_C & 0x3F);
    }
    return mb_convert_encoding($_String, "UTF-8", "GB2312");
    //return iconv('UTF-8', 'GB2312', $_String);
  }


  /**
   * 生成UUID 单机使用
   * @access public
   * @return string
   */
  static public function uuid(){
    $charid = md5(uniqid(mt_rand(), true));
    $hyphen = chr(45);// "-"
    $uuid = chr(123)// "{"
      . substr($charid, 0, 8) . $hyphen . substr($charid, 8, 4) . $hyphen . substr($charid, 12, 4) . $hyphen . substr($charid, 16, 4) . $hyphen . substr($charid, 20, 12) . chr(125);// "}"
    return $uuid;
  }

  /**
   * 生成Guid主键
   * @return Boolean
   */
  static public function keyGen(){
    return str_replace('-', '', substr(String::uuid(), 1, -1));
  }

  /**
   * 检查字符串是否是UTF8编码
   * @param string $string 字符串
   * @return Boolean
   */
  static public function isUtf8($str){
    $c = 0;
    $b = 0;
    $bits = 0;
    $len = strlen($str);
    for($i = 0; $i < $len; $i++){
      $c = ord($str[$i]);
      if($c > 128){
        if(($c >= 254))
          return false;elseif($c >= 252)
          $bits = 6;
        elseif($c >= 248)
          $bits = 5;
        elseif($c >= 240)
          $bits = 4;
        elseif($c >= 224)
          $bits = 3;
        elseif($c >= 192)
          $bits = 2;
        else return false;
        if(($i + $bits) > $len)
          return false;
        while($bits > 1){
          $i++;
          $b = ord($str[$i]);
          if($b < 128 || $b > 191)
            return false;
          $bits--;
        }
      }
    }
    return true;
  }

  /**
   * 字符串截取，支持中文和其他编码
   * @static
   * @access public
   * @param string $str 需要转换的字符串
   * @param string $start 开始位置
   * @param string $length 截取长度
   * @param string $charset 编码格式
   * @param string $suffix 截断显示字符
   * @return string
   */
  static public function msubstr($str, $start = 0, $length, $charset = "utf-8", $suffix = true){
    if(function_exists("mb_substr"))
      $slice = mb_substr($str, $start, $length, $charset);elseif(function_exists('iconv_substr')){
      $slice = iconv_substr($str, $start, $length, $charset);
    }else{
      $re['utf-8'] = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}/";
      $re['gb2312'] = "/[\x01-\x7f]|[\xb0-\xf7][\xa0-\xfe]/";
      $re['gbk'] = "/[\x01-\x7f]|[\x81-\xfe][\x40-\xfe]/";
      $re['big5'] = "/[\x01-\x7f]|[\x81-\xfe]([\x40-\x7e]|\xa1-\xfe])/";
      preg_match_all($re[$charset], $str, $match);
      $slice = join("", array_slice($match[0], $start, $length));
    }
    return $suffix ? $slice . '...' : $slice;
  }

  /**
   * 产生随机字串，可用来自动生成密码
   * 默认长度6位 字母和数字混合 支持中文
   * @param string $len 长度
   * @param string $type 字串类型
   * 0 字母 1 数字 其它 混合
   * @param string $addChars 额外字符
   * @return string
   */
  static public function randString($len = 6, $type = '', $addChars = ''){
    $str = '';
    switch($type){
      case 0:
        $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz' . $addChars;
        break;
      case 1:
        $chars = str_repeat('0123456789', 3);
        break;
      case 2:
        $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ' . $addChars;
        break;
      case 3:
        $chars = 'abcdefghijklmnopqrstuvwxyz' . $addChars;
        break;
      case 4:
        $chars = "们以我到他会作时要动国产的一是工就年阶义发成部民可出能方进在了不和有大这主中人上为来分生对于学下级地个用同行面说种过命度革而多子后自社加小机也经力线本电高量长党得实家定深法表着水理化争现所二起政三好十战无农使性前等反体合斗路图把结第里正新开论之物从当两些还天资事队批点育重其思与间内去因件日利相由压员气业代全组数果期导平各基或月毛然如应形想制心样干都向变关问比展那它最及外没看治提五解系林者米群头意只明四道马认次文通但条较克又公孔领军流入接席位情运器并飞原油放立题质指建区验活众很教决特此常石强极土少已根共直团统式转别造切九你取西持总料连任志观调七么山程百报更见必真保热委手改管处己将修支识病象几先老光专什六型具示复安带每东增则完风回南广劳轮科北打积车计给节做务被整联步类集号列温装即毫知轴研单色坚据速防史拉世设达尔场织历花受求传口断况采精金界品判参层止边清至万确究书术状厂须离再目海交权且儿青才证低越际八试规斯近注办布门铁需走议县兵固除般引齿千胜细影济白格效置推空配刀叶率述今选养德话查差半敌始片施响收华觉备名红续均药标记难存测士身紧液派准斤角降维板许破述技消底床田势端感往神便贺村构照容非搞亚磨族火段算适讲按值美态黄易彪服早班麦削信排台声该击素张密害侯草何树肥继右属市严径螺检左页抗苏显苦英快称坏移约巴材省黑武培著河帝仅针怎植京助升王眼她抓含苗副杂普谈围食射源例致酸旧却充足短划剂宣环落首尺波承粉践府鱼随考刻靠够满夫失包住促枝局菌杆周护岩师举曲春元超负砂封换太模贫减阳扬江析亩木言球朝医校古呢稻宋听唯输滑站另卫字鼓刚写刘微略范供阿块某功套友限项余倒卷创律雨让骨远帮初皮播优占死毒圈伟季训控激找叫云互跟裂粮粒母练塞钢顶策双留误础吸阻故寸盾晚丝女散焊功株亲院冷彻弹错散商视艺灭版烈零室轻血倍缺厘泵察绝富城冲喷壤简否柱李望盘磁雄似困巩益洲脱投送奴侧润盖挥距触星松送获兴独官混纪依未突架宽冬章湿偏纹吃执阀矿寨责熟稳夺硬价努翻奇甲预职评读背协损棉侵灰虽矛厚罗泥辟告卵箱掌氧恩爱停曾溶营终纲孟钱待尽俄缩沙退陈讨奋械载胞幼哪剥迫旋征槽倒握担仍呀鲜吧卡粗介钻逐弱脚怕盐末阴丰雾冠丙街莱贝辐肠付吉渗瑞惊顿挤秒悬姆烂森糖圣凹陶词迟蚕亿矩康遵牧遭幅园腔订香肉弟屋敏恢忘编印蜂急拿扩伤飞露核缘游振操央伍域甚迅辉异序免纸夜乡久隶缸夹念兰映沟乙吗儒杀汽磷艰晶插埃燃欢铁补咱芽永瓦倾阵碳演威附牙芽永瓦斜灌欧献顺猪洋腐请透司危括脉宜笑若尾束壮暴企菜穗楚汉愈绿拖牛份染既秋遍锻玉夏疗尖殖井费州访吹荣铜沿替滚客召旱悟刺脑措贯藏敢令隙炉壳硫煤迎铸粘探临薄旬善福纵择礼愿伏残雷延烟句纯渐耕跑泽慢栽鲁赤繁境潮横掉锥希池败船假亮谓托伙哲怀割摆贡呈劲财仪沉炼麻罪祖息车穿货销齐鼠抽画饲龙库守筑房歌寒喜哥洗蚀废纳腹乎录镜妇恶脂庄擦险赞钟摇典柄辩竹谷卖乱虚桥奥伯赶垂途额壁网截野遗静谋弄挂课镇妄盛耐援扎虑键归符庆聚绕摩忙舞遇索顾胶羊湖钉仁音迹碎伸灯避泛亡答勇频皇柳哈揭甘诺概宪浓岛袭谁洪谢炮浇斑讯懂灵蛋闭孩释乳巨徒私银伊景坦累匀霉杜乐勒隔弯绩招绍胡呼痛峰零柴簧午跳居尚丁秦稍追梁折耗碱殊岗挖氏刃剧堆赫荷胸衡勤膜篇登驻案刊秧缓凸役剪川雪链渔啦脸户洛孢勃盟买杨宗焦赛旗滤硅炭股坐蒸凝竟陷枪黎救冒暗洞犯筒您宋弧爆谬涂味津臂障褐陆啊健尊豆拔莫抵桑坡缝警挑污冰柬嘴啥饭塑寄赵喊垫丹渡耳刨虎笔稀昆浪萨茶滴浅拥穴覆伦娘吨浸袖珠雌妈紫戏塔锤震岁貌洁剖牢锋疑霸闪埔猛诉刷狠忽灾闹乔唐漏闻沈熔氯荒茎男凡抢像浆旁玻亦忠唱蒙予纷捕锁尤乘乌智淡允叛畜俘摸锈扫毕璃宝芯爷鉴秘净蒋钙肩腾枯抛轨堂拌爸循诱祝励肯酒绳穷塘燥泡袋朗喂铝软渠颗惯贸粪综墙趋彼届墨碍启逆卸航衣孙龄岭骗休借" . $addChars;
        break;
      default :
        // 默认去掉了容易混淆的字符oOLl和数字01，要添加请使用addChars参数
        $chars = 'ABCDEFGHIJKMNPQRSTUVWXYZabcdefghijkmnpqrstuvwxyz23456789' . $addChars;
        break;
    }
    if($len > 10){//位数过长重复字符串一定次数
      $chars = $type == 1 ? str_repeat($chars, $len) : str_repeat($chars, 5);
    }
    if($type != 4){
      $chars = str_shuffle($chars);
      $str = substr($chars, 0, $len);
    }else{
      // 中文随机字
      for($i = 0; $i < $len; $i++){
        $str .= self::msubstr($chars, floor(mt_rand(0, mb_strlen($chars, 'utf-8') - 1)), 1, 'utf-8', false);
      }
    }
    return $str;
  }

  /**
   * 生成一定数量的随机数，并且不重复
   * @param integer $number 数量
   * @param string $len 长度
   * @param string $type 字串类型
   * 0 字母 1 数字 其它 混合
   * @return string
   */
  static public function buildCountRand($number, $length = 4, $mode = 1){
    if($mode == 1 && $length < strlen($number)){
      //不足以生成一定数量的不重复数字
      return false;
    }
    $rand = array();
    for($i = 0; $i < $number; $i++){
      $rand[] = self::randString($length, $mode);
    }
    $unqiue = array_unique($rand);
    if(count($unqiue) == count($rand)){
      return $rand;
    }
    $count = count($rand) - count($unqiue);
    for($i = 0; $i < $count * 3; $i++){
      $rand[] = self::randString($length, $mode);
    }
    $rand = array_slice(array_unique($rand), 0, $number);
    return $rand;
  }

  /**
   *  带格式生成随机字符 支持批量生成
   *  但可能存在重复
   * @param string $format 字符格式
   *     # 表示数字 * 表示字母和数字 $ 表示字母
   * @param integer $number 生成数量
   * @return string | array
   */
  static public function buildFormatRand($format, $number = 1){
    $str = array();
    $length = strlen($format);
    for($j = 0; $j < $number; $j++){
      $strtemp = '';
      for($i = 0; $i < $length; $i++){
        $char = substr($format, $i, 1);
        switch($char){
          case "*"://字母和数字混合
            $strtemp .= String::randString(1);
            break;
          case "#"://数字
            $strtemp .= String::randString(1, 1);
            break;
          case "$"://大写字母
            $strtemp .= String::randString(1, 2);
            break;
          default://其他格式均不转换
            $strtemp .= $char;
            break;
        }
      }
      $str[] = $strtemp;
    }
    return $number == 1 ? $strtemp : $str;
  }

  /**
   * 获取一定范围内的随机数字 位数不足补零
   * @param integer $min 最小值
   * @param integer $max 最大值
   * @return string
   */
  static public function randNumber($min, $max){
    return sprintf("%0" . strlen($max) . "d", mt_rand($min, $max));
  }

  // 自动转换字符集 支持数组转换
  static public function autoCharset($string, $from = 'gbk', $to = 'utf-8'){
    $from = strtoupper($from) == 'UTF8' ? 'utf-8' : $from;
    $to = strtoupper($to) == 'UTF8' ? 'utf-8' : $to;
    if(strtoupper($from) === strtoupper($to) || empty($string) || (is_scalar($string) && !is_string($string))){
      //如果编码相同或者非字符串标量则不转换
      return $string;
    }
    if(is_string($string)){
      if(function_exists('mb_convert_encoding')){
        return mb_convert_encoding($string, $to, $from);
      }elseif(function_exists('iconv')){
        return iconv($from, $to, $string);
      }else{
        return $string;
      }
    }elseif(is_array($string)){
      foreach($string as $key => $val){
        $_key = self::autoCharset($key, $from, $to);
        $string[$_key] = self::autoCharset($val, $from, $to);
        if($key != $_key)
          unset($string[$key]);
      }
      return $string;
    }else{
      return $string;
    }
  }


}


?>