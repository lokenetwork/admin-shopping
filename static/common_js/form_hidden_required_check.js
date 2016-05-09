(function ($) {
  $.fn.FormHiddenRequiredCheck = function () {
    return {
      element: $(this),
      //该插件的基本信息
      Info: {
        Author: "loken",
        Desc: "检测表单隐藏的required hidden信息"
      },

      Check: function () {
        var hide_required_check_result = true;
        //获取隐藏的required
        this.element.find("input[type='hidden']").each(function(i){
          //检测隐藏输入框是否必填
          if ( $(this).attr('hidden_required') == 1 && !$(this).val() ){
            var required_tips = $(this).attr('required_tips');
            alert(required_tips);
            hide_required_check_result = false;
          };
        })
        return hide_required_check_result;
      },

    }
  }



})(jQuery);