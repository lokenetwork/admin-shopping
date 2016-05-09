/*
 *  选择分类触发事件,适用于4级分类
 * */
$(function($) {
  //ajax 读取后端顶级分类信息
  $.get("/Category/echoSubCategory/fields/cat_name,cat_id/parent_id/0",
    function (sub_category) {
      if (sub_category != 'no_sub_category') {
        var html = "<option value='0'>Top category</option>";
        for (var o in sub_category) {
          var selected;
          if( _category_level_0 == sub_category[o].cat_id ){
            selected = 'selected';
          }else{
            selected = '';
          }
          html += ' <option '+selected+' value="' + sub_category[o].cat_id + '">' + sub_category[o].cat_name + '</option>'
        }
        $("#_category_level_0").html(html);
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

