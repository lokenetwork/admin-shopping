/**
 * Created by loken_mac on 1/27/16.
 */

/*
 *  选择分类触发事件
 * */


function change_input_type(input_type) {
  if (input_type == 4) {
    $("#spec_value_form_group").slideUp('fast');
  } else {
    $("#spec_value_form_group").slideDown('fast');
  }

  if (input_type == 5) {
    $("#spec_name_2").slideDown('fast');
  } else {
    $("#spec_name_2").slideUp('fast');
  }

}

function self_up(obj) {
  var tr_dom = $(obj).parents('.spec_value_tr');
  //判断是不是到顶了
  if (tr_dom.prev().hasClass('spec_value_tr')) {
    tr_dom.prev().before(tr_dom);
  }
}

function self_down(obj) {
  var tr_dom = $(obj).parents('.spec_value_tr');
  //判断是不是到顶了
  if (tr_dom.next().hasClass('spec_value_tr')) {
    tr_dom.next().after(tr_dom);
  }
}

function self_remove(obj) {
    var tr_dom = $(obj).parents('.spec_value_tr');
    //判断是不是到顶了
    if (tr_dom.next().hasClass('spec_value_tr') || tr_dom.prev().hasClass('spec_value_tr')) {
      if (confirm("确定要删除吗?")) {
        tr_dom.remove();
      }
    }
}

function add_spec_value() {
  var first_dom = $('.spec_value_tr:first');
  first_dom.before(first_dom.prop('outerHTML'));
}