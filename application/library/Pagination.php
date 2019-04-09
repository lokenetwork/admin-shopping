<?php

class Pagination {


  public $firstRow;
  public $listRows;
  private $tcount;
  private $pindex;
  private $psize;
  /**
   * 生成分页数据
   * @param int $currentPage 当前页码
   * @param int $totalCount 总记录数
   */
  function __construct($tcount, $pindex, $psize = 20){
    $this->tcount = $tcount;
    $this->pindex = $pindex;
    $this->psize = $psize;
    $this->firstRow = ($pindex-1)*$psize;
    $this->listRows = $psize;
  }


  /**
   * 生成分页数据
   * @param string $url 要生成的 url 格式，页码占位符请使用 *，如果未写占位符，系统将自动生成
   * @param int $pageSize 分页大小
   * @return string 分页HTML
   */
  function show( $url = '', $context = array('before' => 5, 'after' => 4, 'ajaxcallback' => '')){
    $tcount = $this->tcount;
    $pindex = $this->pindex;
    $psize = $this->psize;


    global $_W;
    $pdata = array('tcount' => 0, 'tpage' => 0, 'cindex' => 0, 'findex' => 0, 'pindex' => 0, 'nindex' => 0, 'lindex' => 0, 'options' => '');
    if($context['ajaxcallback']){
      $context['isajax'] = true;
    }

    $pdata['tcount'] = $tcount;
    $pdata['tpage'] = ceil($tcount / $psize);
    if($pdata['tpage'] <= 1){
      return '';
    }
    $cindex = $pindex;
    $cindex = min($cindex, $pdata['tpage']);
    $cindex = max($cindex, 1);
    $pdata['cindex'] = $cindex;
    $pdata['findex'] = 1;
    $pdata['pindex'] = $cindex > 1 ? $cindex - 1 : 1;
    $pdata['nindex'] = $cindex < $pdata['tpage'] ? $cindex + 1 : $pdata['tpage'];
    $pdata['lindex'] = $pdata['tpage'];

    if( isset($context['isajax']) && $context['isajax'] ){
      if(!$url){
        $url = $_W['script_name'] . '?' . http_build_query($_GET);
      }
      $pdata['faa'] = 'href="javascript:;" onclick="p(\'' . $_W['script_name'] . $url . '\', \'' . $pdata['findex'] . '\', ' . $context['ajaxcallback'] . ')"';
      $pdata['paa'] = 'href="javascript:;" onclick="p(\'' . $_W['script_name'] . $url . '\', \'' . $pdata['pindex'] . '\', ' . $context['ajaxcallback'] . ')"';
      $pdata['naa'] = 'href="javascript:;" onclick="p(\'' . $_W['script_name'] . $url . '\', \'' . $pdata['nindex'] . '\', ' . $context['ajaxcallback'] . ')"';
      $pdata['laa'] = 'href="javascript:;" onclick="p(\'' . $_W['script_name'] . $url . '\', \'' . $pdata['lindex'] . '\', ' . $context['ajaxcallback'] . ')"';
    }else{
      if($url){
        $pdata['faa'] = 'href="?' . str_replace('*', $pdata['findex'], $url) . '"';
        $pdata['paa'] = 'href="?' . str_replace('*', $pdata['pindex'], $url) . '"';
        $pdata['naa'] = 'href="?' . str_replace('*', $pdata['nindex'], $url) . '"';
        $pdata['laa'] = 'href="?' . str_replace('*', $pdata['lindex'], $url) . '"';
      }else{
        $_GET['page'] = $pdata['findex'];
        $pdata['faa'] = 'href="' . $_W['script_name'] . '?' . http_build_query($_GET) . '"';
        $_GET['page'] = $pdata['pindex'];
        $pdata['paa'] = 'href="' . $_W['script_name'] . '?' . http_build_query($_GET) . '"';
        $_GET['page'] = $pdata['nindex'];
        $pdata['naa'] = 'href="' . $_W['script_name'] . '?' . http_build_query($_GET) . '"';
        $_GET['page'] = $pdata['lindex'];
        $pdata['laa'] = 'href="' . $_W['script_name'] . '?' . http_build_query($_GET) . '"';
      }
    }

    $html = '<div class="pagination pagination-centered"><ul class="pagination pagination-centered">';
    if($pdata['cindex'] > 1){
      $html .= "<li><a {$pdata['faa']} class=\"pager-nav\">首页</a></li>";
      $html .= "<li><a {$pdata['paa']} class=\"pager-nav\">&laquo;上一页</a></li>";
    }
    //页码算法：前5后4，不足10位补齐
    if(!$context['before'] && $context['before'] != 0){
      $context['before'] = 5;
    }
    if(!$context['after'] && $context['after'] != 0){
      $context['after'] = 4;
    }

    if($context['after'] != 0 && $context['before'] != 0){
      $range = array();
      $range['start'] = max(1, $pdata['cindex'] - $context['before']);
      $range['end'] = min($pdata['tpage'], $pdata['cindex'] + $context['after']);
      if($range['end'] - $range['start'] < $context['before'] + $context['after']){
        $range['end'] = min($pdata['tpage'], $range['start'] + $context['before'] + $context['after']);
        $range['start'] = max(1, $range['end'] - $context['before'] - $context['after']);
      }
      for($i = $range['start']; $i <= $range['end']; $i++){
        if(isset($context['isajax']) && $context['isajax']){
          $aa = 'href="javascript:;" onclick="p(\'' . $_W['script_name'] . $url . '\', \'' . $i . '\', ' . $context['ajaxcallback'] . ')"';
        }else{
          if($url){
            $aa = 'href="?' . str_replace('*', $i, $url) . '"';
          }else{
            $_GET['page'] = $i;
            $aa = 'href="?' . http_build_query($_GET) . '"';
          }
        }
        $html .= ($i == $pdata['cindex'] ? '<li class="active"><a href="javascript:;">' . $i . '</a></li>' : "<li><a {$aa}>" . $i . '</a></li>');
      }
    }

    if($pdata['cindex'] < $pdata['tpage']){
      $html .= "<li><a {$pdata['naa']} class=\"pager-nav\">下一页&raquo;</a></li>";
      $html .= "<li><a {$pdata['laa']} class=\"pager-nav\">尾页</a></li>";
    }
    $html .= '</ul></div>';
    return $html;
  }

}

?>