/**
 * Created by Administrator on 2015/6/7.
 */
var family_block_height;
$(document).ready(function(){
  init_tips();


});

function _view_detail(family_id,family_name){

}

//是否正在请求
var load = 0;

window.onscroll = function(){
  var w_h = parseFloat($(window).height());
  var p = parseInt($("#page").val());
  var document_height = parseFloat($(document).height());
  //获取可以滚动的高度
  var max_scroll_h = document_height-w_h-10;
  var t = $(document).scrollTop();
  if( parseFloat(max_scroll_h) < parseFloat(t) && load == 0 ){
    load = 1;
    var family_ids = $("#family_ids").val();
    var next_p = p+1;
    var url = '/Search/family/family_name/'+f_name+"/p/"+next_p+"/family_ids/"+family_ids;
    $.get(url, function(data){
      //request finish
      load = 0;
      if( data != 0 ){
        var family_list = eval('(' + data + ')');
        var html = '';
        for(var k in family_list){
          html += '<div class="family_block" onclick="view_detail(\''+family_list[k]['family_id']+'\')">\
                   <div class="fl head_portrait">\
                      <img class="head_portrait_img" src="'+family_list[k]['head_portrait']+'">\
                   </div>\
                   <div class="info_blk">\
                      <div class="family_name">'+
          family_list[k]['family_name']+'\
                      </div>\
                      <div class="fl family_icon_info_blk">\
                        <div class="family_num fl">\
                          <img class="fl people_img" src="/application/views/search/img/people.png">\
                          <div class="fl p_num">'+
          family_list[k]['people_num']+'\
                          </div>\
                        </div>\
                      </div>\
                      <div class="fl family_introduce">'+
          family_list[k]['introduce']+'\
                      </div>\
                   </div>\
                 </div>';
        }
        $("#loading_blk").before(html);
        $("#page").val(next_p);
      }else{
        $("#loading_blk").html("没有更多了");
      }
    });

  }
  //$("#tets_blk").html(max_scroll_h+"-"+t);

};

function init_tips(){
  var family_block_num = 0;
  $(".family_block").each(function(i){
    family_block_num++;
  });
  if( family_block_num < 30 ){
    $("#loading_blk").html("没有更多了");
  }
}

