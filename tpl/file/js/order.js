var Order = (function ($, Order, BASEURL) {
    
    if(Order.url == null){
        Order.url = "http://www.bigsm.com/";
    }
    
    if (BASEURL && Order.url !== BASEURL) {
        Order.url = BASEURL;
    }

    Order.deletes = function (id,callback) {

        $.ajax({
            url: Order.url+"index.php?act=user&m=order&w=delete&id=" + id,
            type: "GET",
            dataType: "json",
            async:true,
            success: function (code) {
                if (code.err == 0) {
                    callback(1);
                } else {
                    callback(1);
                }
            }
        });
    }

    Order.cancel = function (id,callback) {
        
        $.ajax({
            url: Order.url+"index.php?act=user&m=order&w=cancel&id=" + id,
            type: "GET",
            dataType: "json",
            async:true,
            success: function (code) {
                if (code.err == 0) {
                    callback(1);
                } else {
                    callback(1);
                }
            }
        });

    }
    
    return Order;
    

})(jQuery, Order || {});

//   删除订单
$("#order-list").on("click",".delete a",function(){
    if(window.confirm("确认删除该订单？删除后将不可恢复！")){
        Order.deletes($(this).data("id"),function(code){
            if(code){
                layer.msg("删除成功");
                location.reload();
            }else{
                layer.msg("删除失败");
            }
        });
    }
});

$("#order-list").on("click",".cancel",function(){
    if(window.confirm("确认取消该订单？取消后可重新下单")){
        Order.cancel($(this).data("id"),function(code){
            if(code){
                layer.msg("取消成功");
                location.reload();
            }else{
                layer.msg("取消失败");
            }
        });
    }
});
