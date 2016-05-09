/**
 * Created by loken_mac on 1/27/16.
 */

//选中哪个index
  var select_spec_index;

  function search_spec_modal(em) {
    select_spec_index = $(em).attr('spec_index');
    $('#spec_select_modal').modal('show');
  }


  function self_up(obj) {
    var tr_dom = $(obj).parents('.spec_tr');
    //判断是不是到顶了
    if (tr_dom.prev().hasClass('spec_tr')) {
      tr_dom.prev().before(tr_dom);
    }
  }

  function self_down(obj) {
    var tr_dom = $(obj).parents('.spec_tr');
    //判断是不是到顶了
    if (tr_dom.next().hasClass('spec_tr')) {
      tr_dom.next().after(tr_dom);
    }
  }

  function self_remove(obj) {
    var tr_dom = $(obj).parents('.spec_tr');
    //判断是不是到顶了
    if (tr_dom.next().hasClass('spec_tr') || tr_dom.prev().hasClass('spec_tr')) {
      if (confirm("确定要删除吗?")) {
        tr_dom.remove();
      }
    }
  }

  function add_spec() {
    var blank_html = $('#spec_tr_blank').html();
    var blank_html = '<tr class="spec_tr">' + blank_html + '</tr>';
    $('#spec_tbody').prepend(blank_html);

    //没新增一个栏,重新排序spec_index
    $("#spec_tbody .spec_index_div").each(function (i) {
      $(this).attr('spec_index', i);
    });
  }


  function select_spec() {
    var select_spec_id = $("#spec_preview_select").val();
    if (select_spec_id > 0) {
      var select_spec_name = $("#spec_preview_select option:selected").html();
      var select_spec_admin_title = '';
      for (var i = 0; i < 4; i++) {
        var category_name = $("#_category_level_" + i + " option:selected").html();
        var category_id = $("#_category_level_" + i).val();
        if (category_name != undefined && category_id >= 0) {
          select_spec_admin_title += category_name + '--->';
        }
      }
      select_spec_admin_title += select_spec_name;

      $(".spec_index_div[spec_index='" + select_spec_index + "'] .spec_index_attr_id").val(select_spec_id);
      $(".spec_index_div[spec_index='" + select_spec_index + "'] .spec_index_attr_name").val(select_spec_name);
      $(".spec_index_div[spec_index='" + select_spec_index + "'] .spec_index_attr_admin_title").val(select_spec_admin_title);

      $(".spec_index_div[spec_index='" + select_spec_index + "']").parent().next('.display_spec_name').html(select_spec_name);

      //$(".spec_index_div[spec_index='"+select_spec_index+"']").parents('td').next().html(select_spec_name);;
      $('#spec_select_modal').modal('hide');
    } else {
      alert('请先选择商品规格!');
    }
  }

(function ($) {
  add_spec();
  $.extend({
    //检测商品模型表单
    CheckGoodsModalFrom: {
      //该拓展函数的基本信息
      Info: {
        Author: "loken",
        CheckResult:false
      },

      CheckHiddenRequired: function(){
        this.Info.CheckResult = $("#goodsModalForm").FormHiddenRequiredCheck().Check();
      },

      CheckAll: function () {
        this.CheckHiddenRequired();
        return this.Info.CheckResult;
      },

      //具有参数的函数对象
      FunctionWithParams: function (paramObj) {
        //使用参数，是否使用默认值
        var params = paramObj ? paramObj : {
          param1: "1",
          param2: "2"
        };
        return this.Info.Name + ".FunctionWithParamObect";
      },

      //具有参数的函数对象，这里参数是一个变量
      FunctionWithParam: function (varparam) {
        //使用参数，是否使用默认值
        var param = varparam ? varparam : null;
        return this.Info.Name + ".FunctionWithParam";
      },
      //不具有参数的函数对象
      FunctionWithOutParam: function () {
        return this.Info.Name + ".FunctionWithOutParam";
      }
    }
  });
  //$.CheckGoodsModalFrom.CheckGoodsSpec();
})(jQuery);


