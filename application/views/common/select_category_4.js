/*
 *  选择分类触发事件,适用于4级分类
 * */
$(function($) {
  //ajax 读取后端顶级分类信息
  $.get("/Category/echoSubCategory/fields/cat_name,cat_id/parent_id/0",
    function (sub_category) {
      if (sub_category != 'no_sub_category') {
        var html = "<option value='0'>顶级分类</option>";
        for (var o in sub_category) {
          var selected;
          if( category_level_0 == sub_category[o].cat_id ){
            selected = 'selected';
          }else{
            selected = '';
          }
          html += ' <option '+selected+' value="' + sub_category[o].cat_id + '">' + sub_category[o].cat_name + '</option>'
        }
        $("#category_level_0").html(html);
      }
    });
  for (var _i=1;_i<4;_i++)
  {
    var parent_category_level = eval('category_level_'+(_i-1));
    var category_level = eval('category_level_'+_i);
    if( parent_category_level > 0 ){
      //ajax 读取后端顶级分类信息
      $.ajax({
        type: "GET",
        url: "/Category/echoSubCategory/fields/cat_name,cat_id/parent_id/"+parent_category_level,
        data: "",
        async: false,
        success: function(sub_category){
          if (sub_category != 'no_sub_category') {
            $("#category_level_"+_i).addClass('in');
            var html = "<option value='-1'>--请选择--</option>";
            for (var o in sub_category) {
              var selected;
              if (category_level == sub_category[o].cat_id) {
                selected = 'selected';
              } else {
                selected = '';
              }
              html += ' <option ' + selected + ' value="' + sub_category[o].cat_id + '">' + sub_category[o].cat_name + '</option>'
            }
            $("#category_level_"+_i).html(html);
          }
        }
      });
    }
  }

});

function selectCate(obj,level) {
  function hide_other(){
    //沒有子分类,其他全部隐藏
    if(level == 0){
      $("#category_level_1").hide();
      $("#category_level_2").hide();
      $("#category_level_3").hide();
    }else if (level == 1){
      $("#category_level_2").hide();
      $("#category_level_3").hide();
    } else if (level == 2){
      $("#category_level_3").hide();
    }
  };
  var category_id = $(obj).val();
  $("#cat_id").val(category_id);
  if( category_id != 0 ){

    var next_level = level+1;
    if(level == 0){
      $("#category_level_2").hide();
      $("#category_level_3").hide();
    }
    if(level == 1){
      $("#category_level_3").hide();
    }
    //这句貌似没有意义
    //$("#category_level").val(next_level);
    if( level != 3 ){
      $.get("/Category/echoSubCategory/fields/cat_name,cat_id/parent_id/"+category_id, { },
        function(sub_category){
          if( sub_category != 'no_sub_category' ){
            var html = "<option value='0'>---请选择---</option>";
            for(var o in sub_category){
              html += ' <option value="'+sub_category[o].cat_id+'">'+sub_category[o].cat_name+'</option>'
            }
            $("#category_level_"+next_level).html(html).show();
          }else{
            hide_other();
          }
        });
    }
  }else{
    hide_other();
  }
  return;
}