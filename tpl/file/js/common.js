//   公用组件，初具雏形，待后续添加

var commonJs = function ($) {

    /**
    * @descrption 自定义checkbox
    * @param {object} opts
    * @param {opts} label 自定义控件选择器
    * @param {opts} selectedClass 选中时的class
    * @param {opts} callback 选中时的回调函数
    * @returns void;
    * @date 2016.05.20
    * @author tang
    */

    function wgCheckbox(opts) {
        var context;

        if (!opts.context) {
            context = document;
        }

        $(context).on("click", opts.label, function (event) {

            var $this = $(this);

            if (opts.type && opts.type == "checkbox") {

                $this.toggleClass(opts.selectedClass);

                $this.find("input").prop("checked", function (index, val) {

                    if (opts.onCallback && !val) {

                        opts.onCallback(this);

                    } else if (opts.unCallback) {
                        opts.unCallback(this);
                    }

                    return !val;

                })

            } else if (opts.type && opts.type == "radio") {

                $this.addClass("selected").siblings("label").removeClass("selected");

                opts.onCallback && opts.onCallback($(this).find("input").get(0));

            }

            event.stopPropagation();
            event.preventDefault();
        })
    }



    //******************************************订单 Order 命名空间下的方法 ****************************************//

    var Order = {};

    /**
     * @description  订单删除方法
     * 
     * @param {string} id 订单id
     * @parm {function} callback  id 删除请求成功后的回调函数，传入0或者1 0代表失败，1代表成功
     * @auto tang
     * @date 2016.05.26
     * @return void
     */

    Order.deletes = function (id, callback) {

        $.ajax({
            url: "/index.php?act=user&m=order&w=delete&id=" + id,
            type: "GET",
            dataType: "json",
            async: true,
            success: function (code) {
                if (code.err == 0) {
                    callback(1);
                } else {
                    callback(0);
                }
            }
        });
    }

    /**
     * @description  订单取消方法
     * 
     * @param {string} id 订单id
     * @parm {function} callback  id 取消请求成功后的回调函数，传入0或者1 0代表失败，1代表成功
     * @author tang
     * @date 2016.05.26
     * @return void
     */

    Order.cancel = function (id, callback) {

        $.ajax({
            url: "/index.php?act=user&m=order&w=cancel&id=" + id,
            type: "GET",
            dataType: "json",
            async: true,
            success: function (code) {
                if (code.err == 0) {
                    callback(1);
                } else {
                    callback(0);
                }
            }
        });

    }

    return {
        tool: {
            cookie: function (name) {
                var name = escape(name),
                    allcookies = document.cookie;
                name += "=";
                var pos = allcookies.indexOf(name);
                if (pos != -1) {
                    var start = pos + name.length,
                        end = allcookies.indexOf(";", start);
                    if (end == -1) {
                        end = allcookies.length;
                        var value = allcookies.substring(start, end);
                        return unescape(value);
                    }
                }
                else {
                    return "";
                }
            },
            closeMask: function () {
            }
        },
        wg: {
            wgCheckbox: wgCheckbox
        },
        order: Order,
        Alert: function (opts) {

            var msg = "",

                $ok = $("<a href='javascript:void(0);' class='alert-btn-ok'>确定</a>"),

                $cancel = "";

            if (Object.prototype.toString.call(opts).indexOf("String") !== -1) {

                msg = opts;

            } else {

                msg = opts.msg;

                $cancel = $("<a href='javascript:void(0);' class='alert-btn-cancel'>取消</a>");

            }
            var $wrapper = $([
                '<div id="alert">',
                '<div class="alert-bg"></div>',
                '<div class="alert-content">',
                '<p class="alert-msg">' + msg + '</p>',
                '<div class="alert-btn"></btn>',
                '</div>'
            ].join(""));

            return function () {
                $wrapper.find(".alert-btn").append($ok);

                if (Object.prototype.toString.call(opts).indexOf("String") === -1) {

                    $ok.on("click", function () {
                        console.log(opts.callback);
                        if (opts.callback) opts.callback();
                    });

                    $cancel.on("click", function () {
                        $wrapper.fadeOut(300);
                    });

                    $wrapper.find(".alert-btn").append($cancel);

                } else {

                    $ok.on("click", function () {
                        $wrapper.fadeOut(300);
                    });

                }

                $wrapper.appendTo("body");

            } ();

        }
    };
    
} (jQuery);