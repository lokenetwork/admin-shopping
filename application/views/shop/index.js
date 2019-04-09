/**
 * Created by loken_mac on 1/27/16.
 */

$(function($) {


});

function confirmDelete(shop_id){
  if(confirm("是否确认删除店铺?")){
    window.location="/Shop/del?id="+shop_id;
  }
}

function confirmLock(shop_id){
  if(confirm("是否确认锁定店铺?")){
    window.location="/Shop/lock?id="+shop_id;
  }
}
function confirmUnlock(shop_id){
  if(confirm("是否确认解锁店铺?")){
    window.location="/Shop/unlock?id="+shop_id;
  }
}

