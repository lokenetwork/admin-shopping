/**
 * Created by loken_mac on 1/29/16.
 */
function search_spec(){

  var filter_data = $("#search_sepc_form").serialize();

  $.ajax({
    type: "GET",
    url: "/GoodsSpec/search",
    data: filter_data,
    success: function(spec_list){
      var html = "<option value='-1'>--请选择--</option>";
      for (var o in spec_list) {
        html += ' <option  value="' + spec_list[o].id + '">' + spec_list[o].name + '</option>'
      }
      $("#spec_preview_select").html(html).focus();
    }
  });
}

function selectPreviewSpec(spec_id){
  $.get("/GoodsSpec/preview/spec_id/"+spec_id, function(data){
    $("#preview").html(data);
  });
}


