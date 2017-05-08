(function (form, $, searchWords, undefined) {
    $(function () {
        // Private Property

        var BASEURL = "/include/getAddress.php?ajax=1&type="
        //  针对IE浏览器的模拟 placeholder效果的小插件
        $.fn.placeholderText = function (placeholderCLass) {

            if (document.createElement("input").placeholder !== undefined) {

                return;

            }

            var className = placeholderCLass || "no-placeholder",

                isClear = true,

                _proto_ = {

                    init: function () {

                        this.each(function () {

                            this.value = this.placeholder;
                            $(this).addClass(className);

                        });

                        _proto_.focusin.call(this);
                        _proto_.focusout.call(this);

                    },
                    focusin: function () {

                        this.on("focus", function () {

                            if (isClear) {

                                if (this.value === this.placeholder) {

                                    isClear = false;
                                    _proto_.clear.call(this);

                                } else if (this.value === "") {

                                    _proto_.fillText.call(this);

                                }
                            }

                        });

                    },
                    focusout: function () {

                        this.on("focusout", function () {

                            if (this.value === "") {
                                _proto_.fillText.call(this);
                            }
                            isClear = true;
                        });

                    },
                    clear: function () {//用于清空input的value的方法

                        this.value = "";
                        $(this).removeClass(className);

                    },
                    fillText: function () { //用于还原input的value为placeholder属性值的方法

                        $(this).addClass(className);
                        this.value = this.placeholder;

                    }

                }

            _proto_.init.call($(this));

        }
        $("input[placeholder]").placeholderText();

        function getData(opts) {

            var url = opts.url,
                callback = opts.callback;

            $.ajax({
                url: url,
                type: "get",
                dataType: "json",
                success: function (data) {
                    callback(data.data);
                },
                error: function (code) {
                }
            })

        }

        function putList(opts) {

            //初始化参数
            var data = opts.data,
                container = opts.container,
                dataType = opts.dataType,

                html = "";

            $.each(data, function (index, item) {
                html += "<a href='javacsript:;' data-type='" + dataType + "' data-id='" + item.ID + "'>" + item[dataType] + "</a>";
            })
            container.html(html);


        }

        function getList(opts) {

            var url = opts.url,
                $this = opts.$this,
                dataType = opts.dataType;

            getData({
                url: url,
                callback: function (data) {
                    $this.find("div[content]").toggle();
                    putList({
                        data: data,
                        container: $this.find("[content]"),
                        dataType: dataType
                    });
                    $this.find("input").eq(0).attr('value', $this.find('a').eq(0).text())
                    $this.find("input").eq(1).attr('value', $this.find('a').eq(0).data("id"));
                    console.log($this.find('a').eq(1).data("id"));
                }
            })
        }

        //   给省市县绑定一个点击事件
        $(document).on("click", "#register [content] a", function () {
            var $this = $(this),
                $parent = $this.parent(),
                $next = $parent.closest(".select").next(),

                $select = $next.find("[select]"),
                dataType = "Vc_" + $select.data("type"),
                container = $select.nextAll("div"),
                url = BASEURL + $select.data("type") + "&" + $this.data("type") + "=" + $this.data("id");

            $parent.prev().attr("value", $this.data("id"));
            $parent.prevAll("[select]").attr("value", $this.text());
            $parent.hide();

            getData({
                url: url,
                callback: function (data) {
                    putList({
                        data: data,
                        container: container,
                        dataType: dataType
                    });
                }
            })

        })

        $("#province input[select]").click(function () {
            $(this).parent().siblings().find("div[content]").hide();
            getList({
                url: BASEURL + "p",
                $this: $(this).parent(),
                dataType: "Vc_province"
            })
        })

        $(document).on("click", "#province div[content] a", function () {
            getList({
                $this: $("#city"),
                url: BASEURL + "c&pid=" + $(this).data("id"),
                dataType: "Vc_city"
            });

        })

        $("#city input[select]").click(function () {
            getList({
                url: BASEURL + "c&pid=" + $("#province input").eq(1).val(),
                $this: $(this).parent(),
                dataType: "Vc_city"
            })
        })

        $(document).on("click", "#city div[content] a", function () {

            getList({
                $this: $("#district"),
                url: BASEURL + "a&pid=" + $(this).data("id"),
                dataType: "Vc_district"
            });

        })

        $("#district input[select]").click(function () {
            getList({
                url: BASEURL + "a&pid=" + $("#city input").eq(1).val(),
                $this: $(this).parent(),
                dataType: "Vc_district"
            })
        })

        $.fn.serializeObject = function () {
            var i = {};
            var n = this.serializeArray();
            $.each(n, function () {
                if (i[this.name] !== undefined) {
                    if (!i[this.name].push) {
                        i[this.name] = [i[this.name]];
                    }
                    i[this.name].push($.trim(this.value) || "");
                } else {
                    i[this.name] = $.trim(this.value) || "";
                }
            });
            return i;
        }

        //  注册页表单插件
        $("#register").validator({
            dataFilter: function (data) {
                if (data.err == 0) {
                    return "";
                } else {
                    return data.msg;
                }
            },
            fields: {
                Vc_mobile: "手机号码:required;mobile;remote(/index.php?act=user&m=userprocess&w=checkmobile)",
                //  kcode:"验证码:required;remote(/index.php?act=user&m=userprocess&w=ckyzm)"
            },
            display: function (el) {
                return el.getAttribute('placeholder') || '';
            },
            valid: function (form) {
                $.ajax({
                    url: "/index.php?act=user&m=userprocess&w=reg",
                    data: $(form).serializeObject(),
                    type: "POST",
                    dataType: "json",
                    success: function (data) {
                        if (data.err == "0") {
                            layer.msg("注册成功,立即跳转到企业验证页面");

                            location.href = data.url;

                        }
                    },
                    error: function (data) {
                        // console.log(data);
                    }
                })
            }
        });

        $("#login .login-item").click(function () {
            $(this).find("label").hide();
            $(this).find("input").focus();
        })

        $("#login .login-item input").on("focus", function () {
            $(this).next("label").hide();
            $(this).next("input").focus();
        })

        $("#login .login-item input").blur(function () {
            var val = $(this).val();
            if (val == "") {
                $(this).next().show();
            }
        });

        //   为数组扩展一个删除方法
        var arrRegister = [];
        Array.prototype.removeByValue = function (str) {
            this.splice(this.indexOf(str), 1);
            return this.sort();
        }

        $("#login label.check").click(function (ev) {

            $(this).toggleClass("selected");
            $(this).find("input").prop("checked", function (index, val) {
                if (val) {
                    $("#remember").attr("value", 0);
                } else {
                    $("#remember").attr("value", 1);
                }
                return !val;
            })

            ev.preventDefault();

        })

        //   checkbox 选择框

        $("#register label.check").not("read").click(function (event) {

            $(this).toggleClass("selected");

            $(this).find("input").prop("checked", function (name, val) {

                var id = $(this).data("id");

                if (arrRegister.indexOf(id) == -1) {

                    arrRegister.push(id);

                } else {

                    arrRegister.removeByValue(id);

                }

                $("#compnay_hidden").attr("value", arrRegister.join(","));

                return !val;
            })

            event.preventDefault();

        });

        //   验证码刷新
        !function () {

            var $yzm = $("#reyzm"),
                src = $yzm.attr("src");

            return function () {

                $(document).on("click", "#reyzm", function () {

                    $yzm.prop("src", src + "&num=" + Math.random(2));

                });

            } ();

        } ();

        //   登录
        $("#login").submit(function () {
            var isValid = 0;
            $(this).find(".login-item input").each(function () {

                if (this.value !== "") {

                    isValid++;

                } else {
                    layer.msg($(this).attr("placeholder") + "不能为空");

                    $(this).focus();

                    return false;

                }

            });

            if (isValid == 3) {
                $.ajax({
                    url: "/index.php?act=user&m=userprocess&w=lg",
                    type: "POST",
                    dataType: "json",
                    data: $(this).serializeArray(),
                    success: function (data) {
                        if (data.err == 0) {
                            layer.msg("登录成功", {
                                time: 1000,
                                icon: 1
                            }, function () {
                                location.href = data.url;
                            });

                        } else {

                            layer.msg(data.msg);

                            $("#reyzm").trigger("click");

                            return false;
                        }

                    }
                });
            }

            return false;
        })

        //   信息认证页面表单
        $("#renzheng").validator({
            dataFilter: function (data) {
                if (data.err == 0) {
                    return "";
                } else {
                    return data.msg;
                }
            },
            rules: {
                // 身份证
                idCard: function (element) {
                    var value = element.value,
                        isValid = true;
                    var cityCode = { 11: "北京", 12: "天津", 13: "河北", 14: "山西", 15: "内蒙古", 21: "辽宁", 22: "吉林", 23: "黑龙江 ", 31: "上海", 32: "江苏", 33: "浙江", 34: "安徽", 35: "福建", 36: "江西", 37: "山东", 41: "河南", 42: "湖北 ", 43: "湖南", 44: "广东", 45: "广西", 46: "海南", 50: "重庆", 51: "四川", 52: "贵州", 53: "云南", 54: "西藏 ", 61: "陕西", 62: "甘肃", 63: "青海", 64: "宁夏", 65: "新疆", 71: "台湾", 81: "香港", 82: "澳门", 91: "国外 " };

                    /* 15位校验规则： (dddddd yymmdd xx g)    g奇数为男，偶数为女
                     * 18位校验规则： (dddddd yyyymmdd xxx p) xxx奇数为男，偶数为女，p校验位
                
                        校验位公式：C17 = C[ MOD( ∑(Ci*Wi), 11) ]
                            i----表示号码字符从由至左包括校验码在内的位置序号
                            Wi 7 9 10 5 8 4 2 1 6 3 7 9 10 5 8 4 2 1
                            Ci 1 0 X 9 8 7 6 5 4 3 2
                     */
                    var rFormat = /^\d{6}(18|19|20)\d{2}(0[1-9]|1[012])(0[1-9]|[12]\d|3[01])\d{3}(\d|X)$|^\d{6}\d{2}(0[1-9]|1[012])(0[1-9]|[12]\d|3[01])\d{3}$/;    // 格式验证

                    if (!rFormat.test(value) || !cityCode[value.substr(0, 2)]) {
                        isValid = false;
                    }
                    // 18位身份证需要验证最后一位校验位
                    else if (value.length === 18) {
                        var Wi = [7, 9, 10, 5, 8, 4, 2, 1, 6, 3, 7, 9, 10, 5, 8, 4, 2, 1];    // 加权因子
                        var Ci = "10X98765432"; // 校验字符
                        // 加权求和
                        var sum = 0;
                        for (var i = 0; i < 17; i++) {
                            sum += value.charAt(i) * Wi[i];
                        }
                        // 计算校验值
                        var C17 = Ci.charAt(sum % 11);
                        // 与校验位比对
                        if (C17 !== value.charAt(17)) {
                            isValid = false;
                        }
                    }
                    return isValid || "身份证号码格式不正确";
                }
            },
            fields: {
                Vc_cityrenID: "身份证:required;idCard;"
            },
            valid: function (form) {
                // return true;
                $(form).trigger("submit");
                // return false;
            }
        });

        //   账户信息管理
        $("#account-change a").click(function () {

            var $this = $(this),
                name = $this.data("fname"),  //要修改的对应的表单name
                $origin = $($this.data("origin")), //要修改的对应的dom
                value = $origin.text(),  // 要修改的项的初始化值
                remote = $this.data("remote"); //远端请求地址

            if (!$origin.find(".input").length) {
                var html = [
                    "<form class='input' action='#'>",
                    "<input type='text' value='" + value + "' name='" + name + "' data-remote='" + remote + "'/>",
                    // "<a href='#' class='ok' title='确认修改' data-remote='" + remote + "'></a>",
                    "</form>"
                ].join("");

                $origin.append(html);
            }

            $origin.find("input").select();

        })

        $("#js-accountInfo").on("focusout", "input", function () {
            var remote = $(this).data("remote"),
                data = $(this).parent().serializeArray();

            $.ajax({
                url: "/index.php?act=user&m=account&w=" + remote,
                data: data,
                type: "GET",
                dataType: "json",
                success: function (data) {

                    if (data.err == 0) {
                        $(this).parent().remove();
                        $(this).closest("td").html($(this).prev("input").val());
                        layer.msg("修改成功");
                        location.reload();
                    } else {
                        layer.msg("修改失败");
                        location.reload();
                    }
                }

            });
        })

        //>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>添加采集公告<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<//

        // 日期插件
        $(".modul-form-select-date input").datepicker({
            language: "zh-CN",
            format: 'yyyy-mm-dd',
            startDate: new Date(),
        })

        $("input.modul-form-select-text").click(function () {
            var $origin = $(this).parent().find(".modul-form-select-warpper");
            $origin.slideToggle(100);
        })

        if ($("#wg-select-wrapper").length) {
            getItemList(1);
        }

        function getItemList(id) {
            $.ajax({
                url: "/index.php?act=user&m=concentrated&w=itemclassinfo&I_classID=" + id,
                type: "GET",
                dataType: "json",
                success: function (data) {
                    if (data.err == 0) {
                        $(".wg-select-wrapper").empty();
                        var html = "";

                        $.each(data.itemList, function (index, item) {
                            html += "<label class='wg-checkbox'><input type='checkbox' value='" + item.id + "'/>" + item.Vc_name + "</label>";
                        })

                        $(".wg-select-wrapper").html(html);

                    } else {
                        console.log("加载失败...");
                    }

                }
            })
        }

        $("ul.modul-form-select-warpper li").click(function () {
            var $parent = $(this).parent();
            $parent.prevAll(".modul-form-select-text").attr("value", $(this).text());
            $parent.prevAll(".hidden").attr("value", $(this).data("id"));
            $parent.hide();
            getItemList($(this).data("id"));
        });

        //   调用自定义样式的checkbox组件
        !function () {

            var arrConcentratedAdd = [],
                concentratedAddInput = $("#concentratedAdd input[name='Vc_itemIds']");

            commonJs.wg.wgCheckbox({
                label: "label.wg-checkbox",
                selectedClass: "selected",
                type: "checkbox",
                onCallback: function (obj) {
                    arrConcentratedAdd.push(obj.value);
                    concentratedAddInput.val(arrConcentratedAdd.sort().join(","));
                },
                unCallback: function (obj) {
                    arrConcentratedAdd.removeByValue(obj.value);
                    concentratedAddInput.val(arrConcentratedAdd.sort().join(","));

                }
            });

        } ();

        !function () {
            //  订单评价星星 函数
            function starChange(obj, type) {

                var self = $(obj),
                    _index = self.index() - 2,
                    input = self.prevAll("input"),
                    $inner = self.prevAll(".inner");

                $inner.css({ width: _index * 20 + "%" });

                input.attr("value", _index);

            }

            //   订单评价星星
            $("#review-form .compare-my-item a").on("click", function (ev) {

                var self = $(this),
                    _index = self.index() - 2,
                    input = self.prevAll("input"),
                    $inner = self.prevAll(".inner");

                $inner.css({ width: _index * 20 + "%" });

                input.attr("value", _index);

            });

            //   订单评价提交 
            $("#review-form").on("submit", function () {
                return false;
            });
        } ();

        //   获取金融申请数据
        function getReivew(filter) {

            $.ajax({
                url: "/index.php?act=user&m=banking&w=mdy&id=" + filter.id + "&I_banking_classID=" + filter.typeid,
                type: "GET",
                dataType: "json",
                success: function (data) {
                    return filter.callback && filter.callback(data);
                },
                error: function (code) {

                }
            });

        }

        //   金融申请记录 修改和查看 html渲染函数
        function renderData(type, data, event) {

            var html = [],
                _select = "",
                disabled = "",
                submitHtml = "",
                title = "";

            if (event) {
                disabled = " disabled";
                submitHtml = '</form>';
                title = "查看";
            } else {
                submitHtml = '<button type="submit" id="submitBanking">确定</button></form>';
                title = "修改";
            }

            if (data.I_secure) {
                _select = '<option value="1" selected>是</option><option value="0">否</option>';
            } else {
                _select = '<option value="1">是</option><option value="0" selected>否</option>';
            }

            //   根据类型 渲染不同的表单数据
            switch (type) {

                //   存货质押
                case "mdyinventory":

                    html = [
                        '<p class="title">' + title + '存货质押</p>',
                        '<p class="p2p-comm">' + data.Vc_company + "</p>",
                        '<form action="##" method="POST" data-name="mdyinventory">',
                        '<input type="text" class="hidden" name="id" value="' + data.id + '"/>',
                        '<input type="text" class="hidden" name="I_bankingID" value="' + data.I_bankingID + '"/>',
                        '<input type="text" class="hidden" name="I_banking_classID" value="' + data.I_banking_classID + '">',
                        '<input type="text" class="hidden" name="Vc_company" value="' + data.Vc_company + '">',
                        '<ul class="fl-li cb">',
                        '<li><label>存活名称：</label><input type="text" name="Vc_name"' + disabled + ' value="' + data.Vc_name + '"></li>',
                        '<li><label>规格等级：</label><input type="text" name="Vc_grade"' + disabled + ' value="' + data.Vc_grade + '"></li>',
                        '<li><label>数&nbsp;&nbsp;&nbsp; &nbsp; &nbsp;量：</label><input type="text"' + disabled + ' name="I_amount" value="' + data.I_amount + '"></li>',
                        '<li><label>市场价格：</label><input type="text" name="N_price"' + disabled + ' value="' + data.N_price + '"></li>',
                        '<li><label>存货地点：</label><input type="text" name="Vc_address"' + disabled + ' value="' + data.Vc_address + '"></li>',
                        '<li><label>货权单位：</label><input type="text" name="Vc_owen_company"' + disabled + ' value="' + data.Vc_owen_company + '"></li>',
                        '<li><label>融资周期：</label><input type="text" name="Vc_bank_cycle"' + disabled + ' value="' + data.Vc_bank_cycle + '"></li>',
                        '<li><label>融资规模：</label><input type="text" name="Vc_bank_size"' + disabled + ' value="' + data.Vc_bank_size + '"></li>',
                        '<li><label>联&nbsp; 系&nbsp;&nbsp;人：</label><input type="text"' + disabled + ' name="Vc_contactor" value="' + data.Vc_contactor + '"></li>',
                        '<li><label>联系电话：</label><input type="text" name="Vc_contactor_phone"' + disabled + ' value="' + data.Vc_contactor_phone + '"></li>',
                        '<li><label>备&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; 注：</label><input type="text"' + disabled + ' name="Vc_note" value="' + data.Vc_note + '"></li>',
                        '</ul>'
                    ];
                    return html.join('').concat(submitHtml);


                //   商票质押
                case "mdyticket":

                    html = [
                        '<p class="title">' + title + '商票质押</p>',
                        '<p class="p2p-comm">' + data.Vc_company + '</p>',
                        '<form action="##" method="POST" data-name="mdyticket">',
                        '<input type="text" class="hidden" name="id" value="' + data.id + '"/>',
                        '<input type="text" class="hidden" name="I_bankingID" value="' + data.I_bankingID + '"/>',
                        '<input type="text" class="hidden" name="I_banking_classID" value="' + data.I_banking_classID + '">',
                        '<input type="text" class="hidden" name="Vc_company" value="' + data.Vc_company + '">',
                        '<ul class="fl-li cb">',
                        '<li><label>出&nbsp;票&nbsp;&nbsp;人：</label><input type="text"' + disabled + ' name="Vc_drawer" value="' + data.Vc_drawer + '"></li>',
                        '<li><label>出&nbsp;票&nbsp;&nbsp;日：</label><input type="text"' + disabled + ' name="Dt_issuedate" value="' + data.Dt_issuedate + '"></li>',
                        '<li><label>金&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;额：</label><input type="text"' + disabled + ' name="N_price" value="' + data.N_price + '"></li>',
                        '<li><label>到&nbsp;期&nbsp;&nbsp;日：</label><input type="text"' + disabled + ' name="Dt_enddate" value="' + data.Dt_enddate + '"></li>',
                        '<li><label>持&nbsp;票&nbsp;&nbsp;人：</label><input type="text"' + disabled + ' name="Vc_holder" value="' + data.Vc_holder + '"></li>',
                        '<li><label>银行是否保兑：</label><select' + disabled + ' name="I_sure">' + _select + '</select></li>',
                        '<li><label>贸易产品：</label><input type="text"' + disabled + ' name="Vc_products" value="' + data.Vc_products + '"></li>',
                        '<li><label>贸易规模：</label><input type="text"' + disabled + ' name="Vc_size" value="' + data.Vc_size + '"></li>',
                        '<li><label>融资周期：</label><input type="text"' + disabled + ' name="Vc_bank_cycle" value="' + data.Vc_bank_cycle + '"></li>',
                        '<li><label>融资规模：</label><input type="text"' + disabled + ' name="Vc_bank_size" value="' + data.Vc_bank_size + '"></li>',
                        '<li><label>联&nbsp;系&nbsp;&nbsp;人：</label><input' + disabled + ' type="text" name="Vc_contactor" value="' + data.Vc_contactor + '"></li>',
                        '<li><label>联系电话：</label><input type="text"' + disabled + ' name="Vc_contactor_phone" value="' + data.Vc_contactor_phone + '"></li>',
                        '<li><label>备&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;注：</label><input type="text"' + disabled + ' name="Vc_note" value="' + data.Vc_note + '"></li>',
                        '</ul>'

                    ];

                    return html.join('').concat(submitHtml);


                //   仓单质押
                case "mdywarehouse":

                    html = [
                        '<p class="title">' + title + '仓单质押</p>',
                        '<p class="p2p-comm">' + data.Vc_company + '</p>',
                        '<form action="##" method="POST" data-name="mdywarehouse">',
                        '<input type="text" class="hidden" name="id" value="' + data.id + '"/>',
                        '<input type="text" class="hidden" name="I_bankingID" value="' + data.I_bankingID + '"/>',
                        '<input type="text" class="hidden" name="I_banking_classID" value="' + data.I_banking_classID + '">',
                        '<input type="text" class="hidden" name="Vc_company" value="' + data.Vc_company + '">',
                        '<ul class="fl-li cb">',
                        '<li><label>仓单类型：</label><input type="text"' + disabled + ' name="Vc_type" value="' + data.Vc_type + '"></li>',
                        '<li><label>品&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;名：</label><input type="text"' + disabled + ' name="Vc_name" value="' + data.Vc_name + '"></li>',
                        '<li><label>数&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;量：</label><input type="text"' + disabled + ' name="I_amount" value="' + data.I_amount + '"></li>',
                        '<li><label>市场价格：</label><input type="text"' + disabled + ' name="N_price" value="' + data.N_price + '"></li>',
                        '<li><label>仓库名称：</label><input type="text"' + disabled + ' name="Vc_warehouse" value="' + data.Vc_warehouse + '"></li>',
                        '<li><label>存&nbsp;货&nbsp;&nbsp;人：</label><input type="text"' + disabled + ' name="Vc_bailor" value="' + data.Vc_bailor + '"></li>',
                        '<li><label>仓&nbsp;管&nbsp;&nbsp;人：</label><input type="text"' + disabled + ' name="Vc_keeper" value="' + data.Vc_keeper + '"></li>',
                        '<li><label>是否购买保险：</label><select' + disabled + ' name="I_secure">' + _select + '</select></li>',
                        '<li><label>融资周期：</label><input type="text"' + disabled + ' name="Vc_bank_cycle" value="' + data.Vc_bank_cycle + '"></li>',
                        '<li><label>融资规模：</label><input type="text"' + disabled + ' name="Vc_bank_size" value="' + data.Vc_bank_size + '"></li>',
                        '<li><label>联&nbsp;系&nbsp;&nbsp;人：</label><input type="text"' + disabled + ' name="Vc_contactor" value="' + data.Vc_contactor + '"></li>',
                        '<li><label>联系电话：</label><input type="text"' + disabled + ' name="Vc_contactor_phone" value="' + data.Vc_contactor_phone + '"></li>',
                        '<li><label>备&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;注：</label><input type="text"' + disabled + ' name="Vc_note" value="' + data.Vc_note + '"></li>',
                        '</ul>'

                    ];

                    return html.join('').concat(submitHtml);
            }

        }

        //   关闭金融申请记录弹窗
        $(".mask-bg,a.mask-btn-close", "#loadMask").click(function () {
            $("#loadMask").fadeOut(300);
        });

        //   金融申请记录 事件按钮
        $("#banking-list a").click(function () {

            var typeid = $(this).data("type-id"),
                id = $(this).data("id"),
                type = $(this).data("type"),
                event = $(this).data("event");

            $mask = $("#loadMask"),
                $content = $mask.find(".wrap");

            switch (event) {
                case "show": // 点击查看按钮
                    getReivew({
                        typeid: typeid,
                        id: id,
                        callback: function (data) {
                            if (data.err == 0) {
                                $content.empty().append(renderData(type, data.data, true));
                                $mask.fadeIn();
                            } else {
                                layer.msg("无数据");
                            }
                        }
                    });
                    break;
                case "change":// 点击修改按钮
                    getReivew({
                        typeid: typeid,
                        id: id,
                        callback: function (data) {
                            if (data.err == 0) {
                                $content.empty().append(renderData(type, data.data));
                                $mask.fadeIn();
                            } else {
                                layer.msg("无数据");
                            }
                        }
                    })
                    break;
                case "delete": //点击删除按钮
                    if (window.confirm("确定删除该记录？删除后不可恢复，可重新申请.")) {
                        $.ajax({
                            url: "/index.php?act=user&m=banking&w=delete&id=" + id + "&I_banking_classID=" + typeid,
                            type: "GET",
                            dataType: "json",
                            success: function (code) {
                                if (code.err == 0) {

                                    location.reload();
                                }
                                layer.msg(code.msg);
                            },
                            error: function (code) {
                                layer.msg(code.msg);
                            }
                        });
                    }

                    break;
                case "reapply": //点击重新申请按钮
                    if (window.confirm("确定填好了吗？填好了就确定吧!")) {
                        $.ajax({
                            url: "/index.php?act=user&m=banking&w=applyagain&id=" + id + "&I_banking_classID=" + typeid,
                            type: "GET",
                            dataType: "json",
                            success: function (code) {
                                if (code.err == 0) {
                                    layer.msg(code.msg + "请耐心等待...");
                                } else {
                                    layer.msg(code.msg + "请重新检查所填内容...");
                                }
                            },
                            error: function (code) {
                                console.log(code);
                            }
                        });
                    }

                    break;
            }
        });

        // 确定保存修改内容
        $(document).on("click", "#submitBanking", function (ev) {
            var $this = $(this);
            $this.text("请求中...");
            $.ajax({
                url: "/index.php?act=user&m=banking&submit=123&w=mdy",
                data: $("#p2p-form form").serializeObject(),
                dataType: "json",
                type: "GET",
                success: function (data) {
                    if (data.err == 0) {
                        $this.text("请求成功...");
                        setTimeout(function () {
                            $this.text("确定");
                            location.reload();
                        }, 1500);

                    } else {
                        $this.text(data.msg);
                        setTimeout(function () {
                            $this.text("确定");
                        }, 1500);
                    }

                },
                error: function (code) {
                    console.log(code);
                    $this.text("请求失败...");
                }
            });

            return false;
        });

        //   点击修改用户信息
        $("#js-accountinfo-change a.change").on("click", function () {

            var $origin = $($(this).data("origin"));

            if ($origin.length) {
                $origin.show();
                $("#mask").fadeIn(300);
            }
        });

        //   修改密码
        $("[active-close]").on("click", function () {

            var $origin = $(this).data("origin");

            $($origin).fadeOut(300, function () {
                $($origin).find("form").hide();
            });

        });

        $("#mask .changeform form").on("submit", function (ev) {

            $.ajax({
                url: $(this).attr("action"),
                type: "GET",
                data: $(this).serializeArray(),
                dataType: "json",
                success: function (code) {
                    if (code.err == 0) {
                        layer.msg("修改成功");
                        $("#mask .mask-btn-close").trigger("click");
                        location.reload();
                    } else {
                        layer.msg(code.msg);
                    }
                },
                error: function (err) {
                    layer.msg(err.msg);
                }
            })

            return false;

        });

        !function (doc) {

            var html = doc.getElementById("purchase-link"),
                btn = doc.getElementById("btn-copy");

            if (html) {
                html.onkeydown = function () {
                    return false;
                }

                return function () {

                    btn.onclick = function () {
                        $(this).closest()
                        html.focus();
                        html.select(); // 选择对象
                        document.execCommand("Copy"); // 执行浏览器复制命令
                        layer.msg("已复制到粘贴板，可按ctrl+v粘贴");
                    }

                } ();

            }

        } (document);

        // >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>产品需求功能待完善<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<//

        //   产品需求计划对象
        function Requirecommodity(id) {

            this.id = id;

            this.getId = function () {

                return this.id;

            }

            this.setId = function (id) {

                this.id = id;

            }

        };

        Requirecommodity.prototype.cancel = function (callback) {

            $.ajax({
                url: "/index.php?act=user&m=requirecommodity&w=publish&type=2&id=" + this.id,
                type: "POST",
                dataType: "json",
                success: function (code) {

                    callback(code);

                },
                error: function (code) {

                    callback(code);

                }
            });


        };

        Requirecommodity.prototype.del = function (callback) {

            $.ajax({
                url: "/index.php?act=user&m=requirecommodity&w=del&id=" + this.id,
                type: "POST",
                dataType: "json",
                success: function (code) {

                    callback(code);

                },
                error: function (code) {

                    callback(code);

                }
            });

        };

        Requirecommodity.prototype.push = function (data, callback) {

            $.ajax({
                url: "/index.php?act=user&m=requirecommodity&w=del&id=" + this.id,
                type: "POST",
                data: data,
                dataType: "json",
                success: function (code) {

                    callback(code);

                },
                error: function (code) {

                    callback(code);

                }
            });

        };

        //   我的产品需求列表
        !function (Requirecommodity) {

            var $btn = $("#requirecommodity a.btn"),
                rmd = new Requirecommodity(1);

            return function () {

                $btn.on("click", function () {

                    var event = $(this).data("event");
                    rmd.setId = $(this).data("id");

                    switch (event) {

                        case "cancel":

                            if (window.confirm("是否确认撤销发布？")) {
                                rmd.cancel(function (code) {
                                    if (code.err == 0) {

                                        layer.msg("撤销成功");

                                    } else {

                                        layer.msg("撤销失败");

                                    }
                                    location.reload();

                                });
                            }

                            break;

                        case "delete":
                            if (window.confirm("是否确认删除该信息？删除后不可恢复")) {
                                rmd.del(function (code) {

                                    if (code.err == 0) {

                                        layer.msg("删除成功");

                                    } else {

                                        layer.msg("删除失败");

                                    }
                                    location.reload();
                                });

                            }

                            break;
                    }

                });

            } ();

        } (Requirecommodity);

        //   所有地方调用 日期插件
        $("input[data-type='datepicker']").datepicker({
            language: "zh-CN",
            format: 'yyyy-mm-dd',
            startDate: new Date()
        })

        // 新增采购页面js
        !function () {

            //   新增采购
            commonJs.wg.wgCheckbox({
                label: "#type-checkbox label",
                selectedClass: "selected",
                type: "radio"
            });

            function genDataCheckbox(data) {

                var html = "";

                $.each(data, function (index, item) {

                    html += '<label for="" class="check"><input type="checkbox" name="" value="' + item.id + '">' + item.Vc_name + '</label>';

                });

                $("#type-subbox").html(html).fadeIn(300);

            }

            var subValue = [];

            $("#type-subbox").on("click", "input", function () {

                if ($(this).is(":checked")) {

                    subValue.push($(this).val());

                } else {

                    subValue.removeByValue($(this).val());

                }

                $("#classIDs").attr("value", subValue.join(","));

            })

            function getData(id, callback) {
                $.ajax({
                    url: "/index.php?act=user&m=requirecommodity&w=mallclassinfo&I_mall_classID=" + id,
                    dataType: "json",
                    type: "GET",
                    success: function (data) {

                        callback(data);

                    },
                    error: function (code) {

                        callback(code);
                    }

                });
            }

            if ($("#type-checkbox").length) {
                getData(1, function (data) {
                    if (data.err == 0) {

                        genDataCheckbox(data.itemClassList);
                    } else {
                        console.log("数据获取失败");
                    }

                });
            }

            $("#type-checkbox").on("click", "label", function (ev) {

                if (!$(this).find("input").is(":checked")) {

                    var id = $(this).data("id");

                    getData(id, function (data) {

                        genDataCheckbox(data);

                    });

                }

                ev.preventDefault();

            })

            //   上传采购单
            !(function () {

                function generator(data) {

                    var html = "";

                    $.each(data, function (index, item) {

                        html += [
                            '<tr>',
                            '<td><input type="text" name="Vc_item[]" value="' + item.Vc_item + '"></td>',
                            '<td><input type="text" name="Vc_stuff[]" value="' + item.Vc_stuff + '"></td>',
                            '<td><input type="text" name="Vc_specification[]" value="' + item.Vc_specification + '"></td>',
                            '<td><input type="text" name="N_amount[]" value="' + item.N_amount + '"></td>',
                            '<td>',
                            '<select name="I_unitType[]">' + (function (type) {
                                if (type == 1) {
                                    return '<option value="1" selected>件</option><option value="2">根</option><option value="3">吨</option>';
                                } else if (type == 2) {
                                    return '<option value="1">件</option><option value="2" selected>根</option><option value="3">吨</option>';
                                } else if (type == 3) {
                                    return '<option value="1">件</option><option value="2">根</option><option value="3" selected>吨</option>';
                                }
                            })(item.I_unitType) + '</select>',
                            '</td>',
                            '<td><input type="text" name="Vc_factorys[]" value="龙钢，鞍钢，钳钢"></td>',
                            '<td><a href="javascript:void(0);" data-event="delete">删除</a><a href="javascript:void(0);" data-event="add">新增</a></td>',
                            '</tr>'
                        ].join('');

                    })
                    return html;

                }

                $(document).on("change", "#file_exl", function () {
                    $.ajaxFileUpload({
                        fileElementId: "file_exl",
                        url: "/index.php?act=user&m=requirecommodity&w=uploadexl",
                        dataType: "json",
                        secureuri: true,
                        success: function (res) {
                            layer.msg("上传成功!");
                            $("#purchase-pushlist tbody").append(generator(res.data));
                        }
                    });
                });

            })();

            //   操作采购单
            $("#purchase-pushlist").on("click", "a", function () {

                var event = $(this).data("event");

                switch (event) {

                    case "delete":

                        $(this).closest("tr").remove();

                        break;

                    case "add":

                        var html = [
                            '<tr>',
                            '<td><input type="text" name="Vc_item[]" value=""></td>',
                            '<td><input type="text" name="Vc_stuff[]" value=""></td>',
                            '<td><input type="text" name="Vc_specification[]" value=""></td>',
                            '<td><input type="text" name="N_amount[]" value=""></td>',
                            '<td>',
                            '<select name="I_unitType[]">',
                            '<option value="1">件</option>',
                            '<option value="2">根</option>',
                            '<option value="3">吨</option>',
                            '</select>',
                            '</td>',
                            '<td><input type="text" name="Vc_factorys[]" value="龙钢,鞍钢,钳钢"></td>',
                            '<td><a href="javascript:void(0);" data-event="delete">删除</a><a href="javascript:void(0);" data-event="add">新增</a></td>',
                            '</tr>'
                        ].join('');

                        $("#purchase-pushlist").append(html);
                }

            });

            //   提交新增采购单表单
            $("#js-pubCollection button[type='submit']").click(function () {

                var event = $(this).data("submit");
                switch (event) {

                    case "pub":
                        $.ajax({
                            url: "/index.php?act=user&m=requirecommodity&w=add&submit",
                            data: $("#js-pubCollection").serializeArray(),
                            dataType: "json",
                            type: "GET",
                            success: function (data) {
                                if (data.err == 0) {
                                    layer.msg("提交成功!");
                                } else {
                                    layer.msg("提交失败,原因可能是 " + data.msg);
                                }
                                // console.log(data);

                            },
                            error: function (code) {

                            }
                        });
                        break;

                    case "pub&send":
                        break;
                }

                return false;
            })

        } ();

        //   招标需求js
        !function () {

            var uploadSwitch = true,
                $tenderFileInput = $("#tender-file"),
                $form = $("#tender-form"),
                $uploadFileInput = $form.find("input[name='Vc_excel']");

            function fileUploader(ev) {

                var value = this.value.replace(/C:\\fakepath\\/, '');

                $("#tender-form .filename").text(value);

                $tenderFileInput.off("change", fileUploader).on("change", fileUploader);

                $.ajaxFileUpload({
                    fileElementId: "tender-file",
                    url: "/index.php?act=user&m=require&w=upload",
                    dataType: "json",
                    secureuri: true,
                    success: function (data) {
                        layer.msg("上传成功", {
                            icon: 1,
                            time: 1350
                        });
                        $tenderFileInput.trigger("reset");
                        $uploadFileInput.attr("value", data);
                    },
                    error: function (data) {
                        layer.msg("上传成功", {
                            icon: 1,
                            time: 1350
                        })
                        $tenderFileInput.trigger("reset");
                        $uploadFileInput.attr("value", data);
                    }
                });

            }

            $tenderFileInput.on("change", fileUploader);

            $("#tender-list").on("click", "a", function () {
                var event = $(this).data("event"),
                    id = $(this).data("id"),
                    classId = $(this).data("class"),
                    $mask = $("#mask"),
                    $maskContent = $mask.find(".mask-content");

                switch (event) {
                    case "reUpload":
                        $("#tender-file").off("change", fileUploader).on("change", fileUploader);
                        $("#upload-tender").on("submit", function () {
                            if (uploadSwitch) {
                                $.ajaxFileUpload({
                                    fileElementId: "tender-file",
                                    url: "/index.php?act=user&m=require&w=reload&id=" + id,
                                    dataType: "json",
                                    secureuri: true,
                                    success: function (data) {
                                        uploadSwitch = false;
                                    }
                                });
                            } else {
                                layer.msg("请选择文件");
                            }
                            return false;
                        });
                        layer.open({
                            type: 1,
                            shade: [0.1, "#000"],
                            title: "重新上传标书", //不显示标题
                            content: $('div.mask-content-upload'), //捕获的元素
                            cancel: function (index) {
                                layer.close(index);
                            }
                        });
                        break;
                    case "delete":
                        layer.confirm('确定删除该标书？删除可重新发布', {
                            icon: 3,
                            title: "提示信息",
                            btn: ['确定', '取消'] //按钮
                        }, function () {
                            $.ajax({
                                url: "/index.php?act=user&m=require&w=deletetender&id=" + id + "&I_requirementID=" + classId,
                                type: "POST",
                                dataType: "json",
                                success: function (data) {
                                    console.log(data);
                                    if (data.err == 0) {
                                        layer.msg("删除成功", {
                                            time: 1500,
                                            icon: 1
                                        }, function () {
                                            location.reload();
                                        });
                                    } else {
                                        layer.msg("删除失败", {
                                            time: 1500,
                                            icon: 5
                                        });
                                    }
                                }
                            });
                        });

                        break;
                    case "cancel":
                        layer.confirm('确定撤销在前台页面展示？否则点取消', {
                            icon: 3,
                            title: "提示信息",
                            btn: ["确定", "取消"]
                        }, function () {
                            $.ajax({
                                url: "/index.php?act=user&m=require&w=cancel&id=" + id + "&I_requirementID=" + classId,
                                type: "POST",
                                dataType: "json",
                                success: function (data) {
                                    if (data.err == 0) {
                                        layer.msg("撤销成功", {
                                            time: 1500,
                                            icon: 1
                                        }, function () {
                                            location.reload();
                                        });
                                    } else {
                                        layer.closeAll();
                                        layer.msg("撤销失败", {
                                            time: 1500
                                        });
                                    }
                                }
                            });
                        });

                        break;
                    case "change":
                        $.ajax({
                            url: "/index.php?act=user&m=require&w=mdytender&id=" + id,
                            type: "POST",
                            dataType: "json",
                            success: function (data) {
                                var html = [
                                    '<ul>',
                                    '<li><label class="title">招标项目</label><input type="text" name="Vc_name" value="' + data.Vc_name + '"></li>',
                                    '<li>',
                                    '<label class="title">招标时间</label>',
                                    '<input type="text" name="D_start" class="input-date input-date-start" data-type="datepicker" data-rule="开始时间:require;date;" value="' + data.D_start.split(' ')[0] + '">&nbsp;—&nbsp;<input type="text" name="D_end" class="input-date input-date-end" data-type="datepicker" data-rule="结束时间:require;date;', 'match(gt, D_start, date);" value="' + data.D_end.split(' ')[0] + '">',
                                    '</li>',
                                    '<li><label class="title">联系人</label><input type="text" name="Vc_contact" value="' + data.Vc_contact + '"></li>',
                                    '<li><label class="title">联系方式</label><input type="text" name="Vc_contact_phone" data-rule="require;mobile;" value="' + data.Vc_contact_phone + '"></li>',
                                    '</li>',
                                    '<input type="text" class="hidden" value="xxx.xls" name="Vc_excel">',
                                    '<li><label class="title">标书</label><label class="label-file">选择文件<input type="file" name="file_exl" value="xxx.xls"></label><span class="filename">请选择文件</span></li>',
                                    '<li><label class="title"></label><button type="submit">提交</button></li>',
                                    '</ul>'
                                ].join("");
                                $("div.mask-content-change form").empty().append(html);
                                layer.open({
                                    type: 1,
                                    shade: [0.1, "#000"],
                                    title: "修改招标需求", //不显示标题
                                    area: 'auto',
                                    maxWidth: "auto",
                                    content: $("div.mask-content-change"), //捕获的元素
                                    cancel: function (index) {
                                        layer.close(index);
                                        // this.content.hide();
                                    }
                                });
                            }
                        })
                        $(document).on("click", "#tender-form-change button[type='submit']", function (ev) {

                            $.ajax({
                                url: "/index.php?act=user&m=require&w=mdytender&submit&id=" + id,
                                data: $("#tender-form-change").serializeArray(),
                                dataType: "json",
                                type: "GET",
                                success: function (data) {
                                    if (data.errr == 0) {
                                        layer.msg("修改成功", {
                                            icon: 1,
                                            time: 1500
                                        })
                                    } else {
                                        layer.msg("修改失败", {
                                            icon: 5,
                                            time: 1500
                                        })
                                    }
                                }
                            })

                            return false;
                        })
                        break;
                }
            });

            //   新增招标提交
            $form.submit(function (ev) {
                $.ajax({
                    url: "/index.php?act=user&m=require&w=addtender&submit",
                    data: $(this).serializeArray(),
                    type: "GET",
                    dataType: "json",
                    success: function (data) {
                        if (data.err == 0) {

                            layer.alert("发布成功", {
                                icon: 1
                            }, function () {

                                layer.closeAll();
                                $form.trigger("reset");

                            })

                        } else {

                            layer.alert(data.msg, {
                                icon: 2,
                                time: 1500
                            })

                        }
                    }
                });

                return false;
            })

            //   搜索招标需求列表
        } ();

        //   融资需求js
        !function () {
            var isSubmit = true;
            $("#finance-form").on("click", "button[type='submit']", function () {
                if (isSubmit) {
                    $.ajax({
                        url: "/index.php?act=user&m=require&w=addfinance&submit",
                        type: "GET",
                        data: $(this).closest("form").serializeArray(),
                        dataType: "json",
                        success: function (data) {
                            if (data.err == 0) {
                                isSubmit = false;
                                setTimeout(function () {
                                    isSubmit = true;
                                }, 15000);
                                layer.msg("提交成功");
                            }
                        }
                    });
                } else {
                    layer.msg("请稍后在提交....");
                }
                return false;
            });
            $("#logistics-form").on("click", "button[type='submit']", function () {
                if (isSubmit) {
                    $.ajax({
                        url: "/index.php?act=user&m=require&w=addlogistics&submit",
                        type: "POST",
                        data: $(this).closest("form").serializeArray(),
                        dataType: "json",
                        async: false,
                        success: function (data) {
                            if (data.err == 0) {
                                isSubmit = false;
                                setTimeout(function () {
                                    isSubmit = true;
                                }, 15000);
                                layer.msg('提交成功');
                            }
                        }
                    });
                } else {
                    layer.msg('请稍后在提交....');
                }

                return false;
            });
        } ();

        //   商铺信用首页列表
        !function () {
            var $form = $("#credit-sort-form"),
                $category = $form.find("input[name='itemClassID']"),
                $area = $form.find("input[name='cityID']"),
                $sort = $form.find("input[name='order_type']"),
                $count = $("#credit-search-count"),
                $list = $("#credit-sort-list");

            //  隐藏表单操作
            function creditSort(type) {

                var $this = $(this),
                    value = $this.data("id");

                $this.addClass("active").siblings().removeClass("active");

                if (value == undefined) {

                    value = $this.data("sort");

                }

                switch (type) {

                    case "category":

                        $category.attr("value", value == "default" ? "" : value);

                        break;

                    case "area":

                        $area.attr("value", value == "default" ? "" : value);

                        break;

                    case "sort":

                        if (value == 1) {

                            $sort.attr("value", "");

                        } else if (value == "amount") {

                            $sort.attr("value", $sort.val() == "amount_asc" ? "amount_desc" : "amount_asc");

                        } else if (value == "money") {

                            $sort.attr("value", $sort.val() == "money_asc" ? "money_desc" : "money_asc");

                        }

                        break;
                }

                $.ajax({
                    url: "/index.php?act=shop&m=shopcredit&w=searchlist",
                    data: $form.serializeArray(),
                    type: "POST",
                    dataType: "json",
                    success: function (data) {

                        if (data.code == 200) {
                            //   这里设置timeout 只是为了测试loding中效果展示
                            setTimeout(function () {
                                render(data.data);
                                layer.closeAll();
                                $count.text(data.data.count);
                            }, 350);
                        }
                    },
                    beforeSend: function () {
                        layer.load(4, {
                            shade: [0.3, '#fff'], //0.1透明度的白色背景
                        });
                    }

                });

            }

            function render(data) {

                var url = data.urlInfo,
                    data = data.data,
                    html = "";

                // $list
                // console.log(data);
                $.each(data, function (item) {
                    html += [
                        '<li>',
                        '<div class="cb credit-company-info">',
                        '<a href="/index.php?act=shop&m=shopcredit&w=index&id=' + this.id + '"  target="_blank" class="fl img"><img src="' + this.Vc_logo_pic + '" width="110" height="110"/></a>',
                        '<div class="fl credit-company-info-con">',
                        '<a href="/index.php?act=shop&m=shopcredit&w=index&id=' + this.id + '" target="_blank" class="name">' + this.Vc_name + '</a>',
                        '<p class="numinfo">成交数量： ' + this.N_amount + '吨<br />成交金额： ' + this.N_money + '元</p>',
                        '</div>',
                        '</div>',
                        '<div class="scope">',
                        '<span class="ib">经营范围：</span>',
                        '<div class="cb ib fl-a scope-wrapper">',
                        (function (data) {

                            var str = "";

                            for (var j = 0, len = data.length; j < len; j++) {

                                str += '<a href="javascript:void(0);">' + data[j].Vc_name + '</a>';
                            }

                            return str;

                        } (this.itemClassArr)),

                        '</div>',
                        '</div>',
                        '<a href="http://wpa.qq.com/msgrd?Uin=' + this.Vc_service_qq + '" class="qq" target="_blank"><img src="/tpl/image/qq.png" width="25" height="25" alt="' + this.Vc_name + '"></a>',
                        '</li>'
                    ].join("");

                });

                setTimeout(function () {
                    $list.empty().append(html);
                }, 0);

            }

            $("#credit-sort-category div.filter-condition-con a").on("click", function () {

                creditSort.call(this, "category");

            });

            $("#credit-sort-area div.filter-condition-con a").on("click", function () {

                creditSort.call(this, "area");
            });

            $("#credit-sort-desc a").on("click", function () {

                creditSort.call(this, "sort");
                return false;

            });

            $("#credit-sort-search").on("submit", function () {

                var $input = $(this).find("input"),
                    isIE = document.createElement("input").placeholder == undefined;

                if (($input.val() != "" && !isIE) || ($input.val() !== $input.attr("placeholder") && isIE)) {

                    $.ajax({
                        url: "/index.php?act=shop&m=shopcredit&w=searchlist",
                        data: $(this).serializeArray(),
                        type: "POST",
                        dataType: "json",
                        success: function (data) {

                            if (data.code == 200) {
                                //   这里设置timeout 只是为了测试loding中效果展示
                                setTimeout(function () {
                                    render(data.data);
                                    layer.closeAll();
                                    $count.text(data.data.count);
                                }, 350);
                            } else {
                                setTimeout(function () {

                                    layer.closeAll();
                                    layer.msg("加载失败...", {
                                        time: 1200
                                    })
                                }, 350);
                            }
                        },
                        beforeSend: function () {
                            layer.load(4, {
                                shade: [0.3, '#fff'], //0.3透明度的白色背景
                            });
                        },
                        error: function () {
                            layer.msg("加载失败...", {
                                time: 1200
                            })
                        }
                    })

                } else {

                    layer.msg("请输入关键字", {
                        icon: 2,
                        shade: [0.3, "#fff"],
                        time: 1300,
                        shadeClose: true
                    })

                }

                return false;

            })

        } ();

        //   商铺首页
        !function () {

            var $submitForm = $("#js-credit-submit"),
                $filterMenu = $("#js-shop-click a,#js-credit-submit div.slide-subFilter a"),
                $shopClick = $("#js-shop-click"),

                $category = $submitForm.find("input.itemClassID"),
                $productName = $submitForm.find("input.itemID"),
                $stuff = $submitForm.find("input.stuffID"),
                $spec = $submitForm.find("input.specificationID"),
                $factory = $submitForm.find("input.factoryID"),

                //   搜索结果tbody
                $result = $("#js-filter-results tbody");


            //   给筛选按钮绑定事件
            $filterMenu.on("click.fill", function () {

                var $this = $(this),

                    id = $this.data("id"),

                    text = $(this).text(),

                    type = $this.closest("div").data("type"),

                    $target;

                $this.addClass("active").siblings().removeClass("active");

                //   区分是该条件对应的是哪一个表单
                switch (type) {

                    case "itemClassID":

                        $target = $category;

                        break;

                    case "itemID":

                        $target = $productName;

                        break;

                    case "stuffID":

                        $target = $stuff;

                        break;

                    case "specificationID":

                        $target = $spec;

                        break;

                    case "factoryID":

                        $target = $factory;

                        break;
                }

                // 注: 当点击全部按钮时,data-type 就为undefined  
                $target.not("[type='text']").attr("value", id ? id : ""); //隐藏表单
                $target.not("[type='hidden']").attr("value", id ? text : ""); //用来显示文本的表单


            });

            //   禁止手动输入

            $submitForm.find("input[type='text']").on("keydown", function (ev) {

                return false;

            });

            //   展示对应条件列表
            $submitForm.find("input[type='text']").on("focus", function () {

                $(this).parent().next().slideDown(300);

            });

            $submitForm.find(".slide-subFilter a").on("click", function () {

                var $this = $(this),

                    type = $this.parent().data("type"),

                    id = $this.data("id");

                $this.parent().hide();
                $shopClick.find("div[data-type='" + type + "']").find("a[data-id='" + id + "']").addClass("active").siblings("").removeClass("active");

            });

            //   表单提交
            $submitForm.on("submit", function () {

                var layerLoad = layer.load(3, {
                    shade: [0.3, '#fff'] //0.1透明度的白色背景
                });

                //   POST ajax 获取数据
                $.post("/index.php?act=shop&m=shopcredit&w=search", $(this).serializeArray(),

                    function (data) {

                        setTimeout(function () {
                            if (data.code == 200) {

                                render(data.data);

                            } else {

                                layer.alert("获取失败...");

                            }
                            layer.closeAll();
                        }, 500);
                    },
                    "json"
                );

                return false;

            });

            //   数据渲染

            function render(data) {

                var data = data.data,
                    html = "";

                $.each(data, function (index) {

                    html += [
                        '<tr' + (index % 2 == 0 ? ' class="even"' : '') + '>',
                        '<td>' + this.itemname + '</td>',
                        '<td>' + this.stuffname + '</td>',
                        '<td>' + this.specificationname + '</td>',
                        '<td>' + this.factoryname + '</td>',
                        '<td>' + this.warehouse + '</td>',
                        '<td>' + this.N_amount + '</td>',
                        '<td>' + this.N_weight + '</td>',
                        '<td>￥' + this.N_price + '</td>',
                        '<td>' + (function (time) {
                            return time.split(" ")[0];
                        } (this.Createtime)) + '</td>',
                        '<td style="font-size: 0;"><a href="javascript:void(0);" data-id="' + this.id + '">购买</a></td>',
                        '</tr>'
                    ].join("");
                })

                $result.html(html);
            }
        } ();

        //   我的订单
        !function () {

            var url = "/index.php?act=user&m=order&w=list",

                $form = $("#js-orderForm");

            function getUrl() {

                var str = "";

                for (var attr in searchWords) {

                    str += "&" + searchWords[attr];

                }

                return str;

            }

            $("#js-orderFilter a").on("click", function () {

                var $this = $(this),

                    $filterInput = $this.prevAll("input"),

                    searchStr = "";

                if ($this.data("status") !== undefined) {

                    searchStr = "I_status=" + $this.data("status");

                } else if ($this.data("isapp") !== undefined) {

                    searchStr = "I_isapp=" + $this.data("isapp");

                } else {
                    searchStr = "";
                }

                searchWords.I_status = searchStr;

                location.href = encodeURI(url + getUrl());

            })

            $("#js-searchForm").on("click", function () {
                var data = $(this).closest("form").serializeObject();
                $.each(data, function (item, index) {

                    searchWords[item] = item + "=" + data[item];

                })

                location.href = encodeURI(url + getUrl());

            });
        } ();

        $.fn.slide = function (opts) {

            var $imgLi = $(opts.img),
                len = $imgLi.length,
                $btn = $(opts.btn),
                $this = this;

            $this.index = 0;
            $this.z = 0;
            $this.time = opts.time || 3000;
            $this.speed = opts.speed || 500;
            $this.name = opts.name || "active";

            //左切换按钮
            var _proto_ = {
                init: function () {
                    this.bind();
                    $this.timer = setInterval(this.next, $this.time);
                },
                bind: function () {
                    var self = this;
                    $btn.click(function () {
                        $this.index = $(this).index();
                        if ($this.index != $this.z) {
                            if ($this.index > $this.z) {
                                self.runLft();
                            } else {
                                self.runRht();
                            }
                        }

                    });
                    // 
                    $(opts.box).hover(function () {
                        clearInterval($this.timer);
                    }, function () {
                        $this.timer = setInterval(self.next, $this.time);
                    });
                    // 
                    $(opts.next).click(function () {
                        self.next();
                    });
                    $(opts.prev).click(function () {
                        self.prev();
                    });

                },
                prev: function () {
                    $this.index--;
                    if ($this.index < 0) {
                        $this.index = len - 1;
                    }
                    this.runRht();
                },
                next: function () {
                    $this.index++;
                    if ($this.index >= len) {
                        $this.index = 0;
                    }

                    _proto_.runLft();
                },
                runLft: function () {
                    $imgLi.eq($this.index).css("left", "100%");
                    $imgLi.eq($this.z).stop(true).animate({ left: "-100%" }, $this.speed);
                    this.run();
                },
                runRht: function () {
                    $imgLi.eq($this.z).stop(true).animate({ left: "100%" }, $this.speed);
                    $imgLi.eq($this.index).css("left", "-100%");
                    this.run();
                },
                run: function () {
                    $imgLi.eq($this.index).stop(true).animate({ left: "0px" }, $this.speed - 10, function () {
                        $this.z = $this.index;
                    });

                    $btn.eq($this.index).addClass($this.name).siblings().removeClass($this.name);

                }
            }

            _proto_.init();

        }

        //   banner
        $().slide({
            box: "#banner",
            name: "active",
            img: "#banner .list li",
            btn: "#banner .dot",
            time: 3000,
            speed: 500
        });

        //卖家中心 
        (function ($) {

            // 修改报价
            var $formEdit = $("#js-edit"),
                $formEditSubmit = $formEdit.find("a.submit-save");

            $formEditSubmit.on("click", function () {

                $.ajax({
                    url: "/index.php?act=shop&m=requirecommodity&w=edit&submit",
                    data: $formEdit.serializeArray(),
                    type: "POST",
                    dataType: "json",
                    success: function (data) {
                        if (data.err == 0) {
                            layer.alert("修改成功", {
                                icon: 1,
                                title: "提示信息"
                            }, function (index) {
                                layer.close(index);
                                history.go(-1);
                            })
                        } else {
                            layer.alert(data.msg, {
                                icon: 2,
                                title: "错误信息"
                            }, function (index) {
                                layer.close(index);
                            })
                        }


                    }
                })

            });

            $("a.confirmLetterOrder").on("click", function () {

                var $this = $(this);

                $.ajax({
                    url: "/index.php?act=shop&m=requirecommodity&w=detail&submit&orderLetterId=" + $this.data("id"),
                    type: "POST",
                    dataType: "json",
                    success: function (data) {

                        if (data.code == 200) {

                            layer.alert("确认成功", function (index) {
                                layer.close(index);
                                location.reload();

                            });

                        } else {

                            layer.alert("确认失败");
                            console.log(data);

                        }

                    }
                });

                return false;

            })

            //  修改信息

            //   checkbox 操作
            $("#wg-radio,#wg-checkbox").on("click.selected", "label", function () {

                var $this = $(this),
                    $next = $this.next();

                if ($next.attr("type") == "radio" && !$this.hasClass("selected")) { //当为单选框的时候

                    $this.addClass("selected").siblings("label").removeClass("selected");

                } else if ($next.attr("type") == "checkbox") { //当为复选框的时候

                    $this.toggleClass("selected");
                }

            });

            function renderItemClassList(data, name) {
                var html = "",
                    name = name || "itemclass";

                $.each(data, function () {
                    html += [
                        '<label for="chk' + this.id + '">' + this.Vc_name + '</label>',
                        '<input type="checkbox" name="' + name + '" value="' + this.id + '" id="chk' + this.id + '">'
                    ].join("");
                })
                return html;
            }

            $("#wg-checkbox input[type='radio']").on("change.get", function () {
                var $this = $(this);
                $.ajax({
                    url: "/index.php?act=user&m=requirecommodity&w=mallclassinfo&I_mall_classID=" + $this.val(),
                    type: "POST",
                    dataType: "json",
                    success: function (data) {
                        if (data.err == 0) {
                            $("#chk-select").html(renderItemClassList(data.itemClassList));
                        } else {
                            layer.alert("请求失败,原因可能是" + data.msg, {
                                title: "错误信息",
                                icon: 2
                            });
                        }

                    }

                })
            })

            //   checkbox 表单选择，改变字符串拼接已选择表单的值
            function changeCheckboxSelectedValue(selector, valInput) {

                var $input = $(valInput),
                    tempArray = [];

                $(selector).on("change.selected", function () {

                    tempArray.length = 0;

                    $(this).closest("div").find("input[type='checkbox']").prop("checked", function (item, val) {

                        if (val) {
                            tempArray.push(this.value);
                        }
                    });

                    $input.attr("value", tempArray.join(","));

                })
            };

            //   公司信息经营范围选择
            changeCheckboxSelectedValue("#wg-checkbox input[type='checkbox']", "#wg-checkbox input.ids");

            //   表单提交简易封装
            function formSubmit(url, callback, type) {

                var data = $(this).serializeArray(),
                    url = url,
                    type = type || "POST";

                $.ajax({
                    url: url,
                    type: type,
                    data: data,
                    dataType: "json",
                    success: function (data) {
                        callback && callback(data);
                    }
                });

            }

            //   用户信息提交
            $("#js-baseInfo").on("submit", function () {

                formSubmit.call(this, "/index.php?act=shop&m=account&w=base-save", function (data) {
                    if (data.err == 0) {
                        layer.alert("保存成功", {
                            time: 1500
                        }, function (index) {
                            layer.close(index);
                            location.reload();
                        });
                    } else {

                        layer.alert(data.msg);

                    }
                });

                return false;

            });

            //   三级联动表单插件
            $.fn.cityCode = function (opts) {

                var opts = $.extend({}, opts),

                    _proto_ = {

                        init: function () {
                            var self = this;
                            self.getList("p", 1, function (data) {
                                self.genData(0, $("select[name='" + opts.item[0].name + "']"), data);
                            });
                            self.bind();
                        },
                        getList: function (type, id, callback) {

                            var id = id ? "&pid=" + id : "";
                            $.ajax({
                                url: opts.baseUrl + type + id,
                                type: "POST",
                                dataType: "json",
                                success: function (data) {

                                    if (data.err == 0) {
                                        callback(data.data);
                                    }

                                }
                            });
                        },
                        genData: function (level, target, data) {

                            var self = opts.item[level],
                                html = "<option>请选择</option>";

                            $.each(data, function () {
                                var _this = this;
                                html += "<option value='" + this[self.value] + "' " + (function () {
                                    if (_this[self.value] == target.val()) {
                                        return " selected";
                                    } else {
                                        return "";
                                    }
                                } ()) + ">" + this[self.text] + "</option>";

                            });

                            target.length > 0 && target.empty().html(html);

                        },
                        bind: function () {

                            var self = this;

                            $.each(opts.item, function (index) {

                                $("select[name='" + this.name + "']").on("change", function (ev) {

                                    var $next = $(this).next();
                                    if ($next.length > 0) {

                                        self.getList(opts.item[index + 1].type, this.value ? this.value : "", function (data) {

                                            self.genData(index + 1, $next, data);

                                        });

                                    }
                                })

                            });

                        }
                    };

                _proto_.init();

            }
            if ($("select[name='I_provinceID']").length) {
                $().cityCode({
                    baseUrl: "/include/getAddress.php?ajax=1&type=",
                    item: [
                        {
                            name: "I_provinceID",
                            value: "ID",
                            text: "Vc_province",
                            type: "p"
                        },
                        {
                            name: "I_cityID",
                            value: "ID",
                            text: "Vc_city",
                            type: "c"
                        },
                        {
                            name: "I_districtID",
                            value: "ID",
                            text: "Vc_district",
                            type: "a"
                        }
                    ]
                })
            }

            $.fn.imageCut = function (opts) {
                if (!this) {
                    return;
                }
                var opts = $.extend({
                    maxSize: 100,
                    minSize: 96,
                    trigger: "click",
                    wrapper: ".layui-layer-content",
                }, opts),
                    _proto_ = {
                        init: function () {
                            _proto_.bind.call(this);
                            this.upJcropApi = undefined;
                        },
                        genHtml: function (type, src) {

                            var html = "",
                                src = src || "";

                            if (type == 1) {
                                html = [
                                    "<form class='up-mask up-mask-upload' id='up-js-uploadMask' style='width:500px;height:300px;'>",
                                    "<div class='up-upload-wrap'>",
                                    "<label for='" + opts.name + "'>选择图片</label>",
                                    "<input type='file' id='" + opts.name + "' style='display:none;' name='" + opts.name + "'>",
                                    "<a href='javascript:void(0);' class='up-upload-btn'>点我上传</a>",
                                    "<div class='up-filname'>请选择图片上传</div>",
                                    "</div>",
                                    "</form>"
                                ].join("");
                            } else if (type == 2) {
                                html = [
                                    "<form class='up-mask up-mask-save' id='up-js-uploadSave' style='width:500px;height:300px;'>",
                                    "<input type='hidden' name='x'>",
                                    "<input type='hidden' name='y'>",
                                    "<input type='hidden' name='w'>",
                                    "<input type='hidden' name='h'>",
                                    "<input type='hidden' name='src'>",
                                    "<input type='hidden' name='bi'>",
                                    "<img class='up-cutImg' src='" + src + "'>",
                                    "<button type='submit'>保存</button>",
                                    "</form>"
                                ].join("");
                            }

                            return html;

                        },
                        showUploadMask: function () {

                            layer.open({
                                type: 1,
                                title: "图片上传裁剪",
                                closeBtn: 1,
                                area: [500, 300],
                                shadeClose: true,
                                skin: 'yourclass',
                                content: _proto_.genHtml(1)
                            });

                        },
                        getAllEle: function (eles) {

                            this.ele = null;
                            var ele = {};

                            for (var attr in eles) {
                                ele[attr] = $(eles[attr]);
                            }

                            return ele;
                        },
                        uploadFile: function () {

                            var self = this;
                            var btn = self.upEle.file.get(0);
                            new AjaxUpload(btn, {
                                action: opts.tempUrl,
                                name: opts.name,
                                onSubmit: function (file, ext) {
                                    if (ext && /^(jpg|jpeg|png|gif)$/.test(ext)) {
                                        //ext是后缀名
                                        btn.innerHTML = "正在上传…";
                                        btn.disabled = "disabled";

                                    } else {
                                        layer.msg("不支持非图片格式！");
                                        return false;
                                    }
                                },
                                onComplete: function (file, data) {

                                    _proto_.showCutForm.call(self, JSON.parse(data));
                                    btn.innerHTML = "上传成功";
                                }
                            })

                        },
                        updateInfo: function (data) {

                            var ele = this.upEle;

                            //   实时更新裁剪的坐标和大小在隐藏表单的值
                            ele.x.attr("value", data.x);
                            ele.y.attr("value", data.y);
                            ele.w.attr("value", data.w);
                            ele.h.attr("value", data.h);
                        },
                        saveCutImg: function () {
                            var self = this;

                            // 提交表单保存已裁剪的图片
                            self.upEle.button.on("click", function () {
                                $.ajax({
                                    url: opts.saveUrl,
                                    data: $(self.upEle.form).serializeArray(),
                                    type: "POST",
                                    dataType: "json",
                                    success: function (data) {
                                        if (data.err == 0) {
                                            layer.alert("保存成功", function () {

                                                layer.closeAll();

                                                if (self.get(0).tagName == "IMG") {
                                                    self.attr("src", (opts.saveImgName && data[opts.saveImgName]) || data.photo);
                                                } else {
                                                    //   用来将服务器返回的裁剪厚度额url更新到本地图片的src。默认字段为photo
                                                    $(opts.currentImg).attr("src", (opts.saveImgName && data[opts.saveImgName]) || data.photo);
                                                }
                                            })
                                            $("input[name='" + opts.savaInput + "']" || opts.name).attr("value", (opts.saveImgName && data[opts.saveImgName]) || data.photo);
                                            console.log($("input[name='" + opts.savaInput + "']" || opts.name));
                                        }
                                    }
                                });
                                return false;

                            });

                        },
                        showCutForm: function (data) {

                            var self = this;
                            $(opts.wrapper).empty().html(_proto_.genHtml(2));

                            //   获取裁剪表单需要的元素，方便操作
                            self.upEle = _proto_.getAllEle.call(self, {
                                form: "#up-js-uploadSave",
                                x: "#up-js-uploadSave input[name='x']",
                                y: "#up-js-uploadSave input[name='y']",
                                w: "#up-js-uploadSave input[name='w']",
                                h: "#up-js-uploadSave input[name='h']",
                                src: "#up-js-uploadSave input[name='src']",
                                bi: "#up-js-uploadSave input[name='bi']",
                                img: "#up-js-uploadSave img",
                                button: "#up-js-uploadSave button[type='submit']"
                            });

                            //   开启裁剪插件
                            self.upEle.img.attr({
                                "src": data.pic,
                                width: data.width,
                                height: data.height
                            }).Jcrop({ //调用裁剪插件

                                bgColor: "#fff",
                                aspectRatio: 1,
                                minSize: [96, 96],
                                maxSize: [data.height, data.height],
                                allowSelect: false, //允许选择
                                allowResize: true, //是否允许调整大小
                                setSelect: [20, 20, 96, 96],
                                onChange: function (data) {
                                    _proto_.updateInfo.call(self, data);
                                }

                            }, function () {

                                self.upJcropApi = this;

                                //   保存隐藏表单信息
                                self.upEle.src.attr("value", data.pic);
                                self.upEle.bi.attr("value", self.upJcropApi.getScaleFactor());

                                //   保存已裁剪图片到服务器
                                _proto_.saveCutImg.call(self);

                            });
                        },
                        bind: function () {

                            var self = this;

                            self.on(opts.trigger + ".upload", function () {

                                self.upEle = null;
                                _proto_.showUploadMask.call(this);

                                self.upEle = _proto_.getAllEle.call(this, {
                                    form: "#up-js-uploadMask",
                                    file: "#up-js-uploadMask label",
                                    fileName: "#up-js-uploadMask .up-filename",
                                    uploadBtn: "#up-js-uploadMask a.up-upload-btn"
                                });
                                _proto_.uploadFile.call(self);
                            })


                        }
                    }
                //   初始化
                _proto_.init.call(this);
            }

            //   基本信息头像上传添加
            $("#js-baseInfo .companyinfo-img .upload-btn").imageCut({
                name: "mypic",
                tempUrl: "/shop/logo_action.php",
                saveUrl: "/shop/logo_action.php?&act=subface",
                savaInput: "mypic",
                currentImg: "#js-baseInfo .companyinfo-img img",
                saveImgName: "phone"
            })

            //   公司简介添加
            var uploadUrl = "/shop/certify_action.php",
                saveUrl = "/shop/certify_action.php?&act=subface";

            // 正面
            $("#file-input1").imageCut({
                name: "Vc_identity_pic1",
                tempUrl: uploadUrl,
                saveUrl: saveUrl,
                currentImg: "#file-img1",
                savaInput: "Vc_identity_pic1"
            });

            // 反面
            $("#file-input11").imageCut({
                name: "Vc_identity_pic2",
                tempUrl: uploadUrl,
                saveUrl: saveUrl,
                currentImg: "#file-img11",
                savaInput: "Vc_identity_pic2"
            });

            // 营业执照
            $("#file-input2").imageCut({
                name: "Vc_licence_pic",
                tempUrl: uploadUrl,
                saveUrl: saveUrl,
                currentImg: "#file-img2",
                savaInput: "Vc_licence_pic"
            });

            // 税务登记证
            $("#file-input3").imageCut({
                name: "Vc_tax_pic",
                tempUrl: uploadUrl,
                saveUrl: saveUrl,
                currentImg: "#file-img3",
                savaInput: "Vc_tax_pic"
            });

            // 组织机构代码证
            $("#file-input4").imageCut({
                name: "Vc_org_pic",
                tempUrl: uploadUrl,
                saveUrl: saveUrl,
                currentImg: "#file-img4",
                savaInput: "Vc_org_pic"
            });

            //   认证信息页面
            $("#js-certify").on("submit", function () {

                formSubmit.call(this, "/index.php?act=shop&m=account&w=certify-save", function (data) {
                    if (data.err == 0) {
                        layer.alert("认证信息提交成功", {
                            icon: 1
                        }, function (index) {
                            layer.close(index);
                            location.reload();
                        })
                    } else {
                        layer.alert("认证信息提交失败，请检查", {
                            icon: 5
                        }, function (index) {
                            layer.close(index);
                        })
                    }
                })

                return false;
            })

            //  报价设置
            $("#js-setGetType").on("submit", function () {

                formSubmit.call(this, "/index.php?act=shop&m=requirecommodity&w=offerset&submit", function (res) {
                    if(res.err == 0) {
                        layer.msg("设置成功");
                    } else {
                        layer.msg(res.msg);
                    }
                }, "GET");
               
                return false;
            });

            //   添加产品
            var $productTabel = $("#js-product-type");

            //  渲染
            function renderProductItem(data) {

                //  缓存选择器
                var $tabName = $productTabel.find("select[name='I_itemID']"),
                    $stuff = $productTabel.find("select[name='I_stuffID']"),
                    $spec = $productTabel.find("select[name='I_specificationID']"),
                    $factory = $productTabel.find("select[name='I_factoryID']");

                //  生成html
                function genHtml(data) {

                    var html = "";

                    $.each(data, function (index) {

                        html += "<option value='" + this.id + "'" + (function () {
                            //   默认选中第一个
                            if (index == 0) {
                                return " selected"
                            } else {
                                return "";
                            }

                        } (index)) + ">" + this.Vc_name + "</option>";

                    })

                    return html;
                }

                // 填充数据 
                return function () {

                    $tabName.html(genHtml(data.itemList));
                    $stuff.html(genHtml(data.itemStuffList));
                    $spec.html(genHtml(data.itemSpecificationList));
                    $factory.html(genHtml(data.itemFactoryList));

                } ();
            }

            //  获取
            function getProductItemById(id) {
                $.ajax({
                    url: "/index.php?act=item&m=itemclassinfo&I_classID=" + id,
                    type: "POST",
                    dataType: "json",
                    success: function (res) {
                        renderProductItem(res);
                    }
                })
            }

            if ($productTabel.length) {
                getProductItemById($productTabel.find("select[name='I_itemClassID'] option").eq(0).val());
            }

            //  绑定change事件
            $productTabel.find("select[name='I_itemClassID']").on("change", function () {

                getProductItemById($(this).val());

            });

            //  产品添加提交
            $("#js-product-form").on("submit", function () {

                var $this = $(this);

                formSubmit.call(this, "/index.php?act=shop&m=commodity&w=add", function (data) {

                    if (data.err == 0) {
                        layer.msg("添加成功", {
                            time: 1000
                        })
                        $this.trigger("reset");
                    } else {
                        layer.msg(data.msg, {
                            time: 1000
                        })
                    }

                });

                return false;

            })

            //  产品上传
            if ($("#js-product-upload").length) {

                var uploadProduct = new AjaxUpload('#js-product-upload', {

                    action: "/index.php?act=shop&m=commodity&w=importexlstep1",
                    name: "file_cp",
                    autoSubmit: false,
                    onChange: function (file, ext) {
                        if (ext && /^(xls)$/.test(ext)) {
                            $("#js-product-upload-filename").text(file);
                            return;
                        }
                        layer.msg("格式不正确");

                    },
                    onSubmit: function (file, ext) {
                        $("#js-product-upload").attr("disabled");
                        $("#js-product-upload").text("上传中.....");
                    },
                    onComplete: function (res) {
                        layer.msg("上传完成");
                        $("#js-product-upload").removeAttr("disabled");
                        $("#js-product-upload").text("上传完成");
                    }
                });

                $("#js-product-upload-btn").on("click", function () {
                    uploadProduct.submit();
                });

            }

            /**
            *
            * date 2016.06.30
            * comment 产品搜索待添加分类筛选
            * 
            */

            //   产品管理列表页面
            var $productFilter = $("#product-filter a"),
                $productForm = $("#js-search-product-form"),
                $productFilterInput = $productForm.find("input[name='ispublished']");


            //   点击发布和未发布筛选按钮
            $productFilter.on("click", function () {
                $productFilterInput.attr("value", $(this).data("id"));
                $productForm.trigger("submit");
            })

            //  全选
            function checkAll(target, status) {
                target.prop("checked", function (item, val) {
                    if (status) {
                        return true;
                    } else {
                        return false;
                    }
                })
            }

            var $productListCheck = $("#js-product-list-check input[type='checkbox']");
            $("#btn-checkAll").on("click", function () {

                if ($(this).prop("checked")) {
                    checkAll($productListCheck, true);
                } else {
                    checkAll($productListCheck, false);
                }
            });

            //   编辑操作
            function getProductInfoById(id) {
                $.get("/index.php?act=shop&m=commodity&w=edit&id=" + id, function (data) {
                    editProduct(JSON.parse(data));
                })
            }

            function editProduct(data) {

                var data = data.data;

                layer.open({
                    // type: 1,
                    title: "编辑产品",
                    closeBtn: 1,
                    area: [500, 300],
                    shadeClose: true,
                    btn: ["确定", "返回"],
                    content: [
                        "<form class='product-edit' id='product-edit'><ul>",
                        "<input type='hidden' name='id' value='" + data.id + "'>",
                        "<li><label>类别：</label><select name='I_itemClassID'><option value='" + data.I_mallClassID + "'>" + data.mallclassname + "</option></select></li>",
                        "<li><label>类型：</label><select name='I_itemClassID'><option value='" + data.I_itemClassID + "'>" + data.itemclassname + "</option></select></li>",
                        "<li><label>品名：</label><select name='I_itemID'><option value='" + data.I_itemID + "'>" + data.itemname + "</option></select></li>",
                        "<li><label>材质：</label><select name='I_stuffID'><option value='" + data.I_stuffID + "'>" + data.stuffname + "</option></select></li>",
                        "<li><label>规格(mm)：</label><select name='I_specificationID'><option value='" + data.I_specificationID + "'>" + data.specificationname + "</option></select></li>",
                        "<li><label>钢厂：</label><select name='I_factoryID'><option value='" + data.I_factoryID + "'>" + data.factoryname + "</option></select></li>",
                        "<li><label>仓库：</label><select name='I_warehouseID'><option value='" + data.I_warehouseID + "'>" + data.warehouse + "</option></select></li>",
                        "<li><label>可供件数：</label><input type='text' name='N_amount' value='" + data.N_weight + "'/></li>",
                        "<li><label>件/吨：</label><input type='text' name='N_weight' value='" + data.N_amount + "'/></li>",
                        "<li><label>销售单价：</label><input type='text' name='N_price' value='" + data.N_price + "'/></li>",
                        "</ul></form>"
                    ].join(""),
                    yes: function (index) {
                        $.ajax({
                            url: "/index.php?act=shop&m=commodity&w=edit&issubmit",
                            data: $("#product-edit").serializeArray(),
                            type: "GET",
                            dataType: "json",
                            success: function (res) {
                                if (res.err == 0) {
                                    layer.msg("产品更新成功");
                                } else {
                                    layer.msg(res.msg);
                                }
                            }
                        })
                    },
                    btn2: function (index) {
                        layer.close(index);
                    }
                });

            }

            function updateProduct(url, items, callback) {
                var ids = [],

                    $form = $("<form></form>");

                items.each(function () {
                    ids.push(this.value);
                });

                $.ajax({
                    url: url,
                    type: "GET",
                    data: $form.append($("<input name='ids'>").attr("value", ids.join(","))).serializeArray(),
                    dataType: "json",
                    success: function (res) {
                        if (res.err == 0) {
                            callback && callback();
                        }
                        layer.msg(res.msg, {
                            time: 2000
                        });
                    }
                });
            }

            //   产品管理 列表操作
            $("#js-product-list-action a").on("click", function () {

                var $product = $productListCheck.filter(":checked"),
                    $this = $(this),
                    events = $(this).data("event");

                switch (events) {

                    case "edit":

                        if ($product.length == 1) {
                            getProductInfoById($productListCheck.filter(":checked").val());
                        } else {
                            layer.alert("只能选择一个进行编辑，请检查！", {
                                title: "提示",
                                icon: 0
                            });
                        }

                        break;
                    case "delete":

                        updateProduct("/index.php?act=shop&m=commodity&w=delete", $product, function () {
                            $product.fadeOut();
                        });
                        break;

                    case "release":
                        updateProduct("/index.php?act=shop&m=commodity&w=publish&pType=1", $product, function(){
                            location.reload();
                        });
                        break;
                    case "cancel":
                        updateProduct("/index.php?act=shop&m=commodity&w=publish&pType=2", $product, function(){
                            location.reload();
                        });
                        break;

                    case "export":
                        $.ajax({
                            url: "/index.php?act=shop&m=commodity&w=exportexl",
                            data: $("#js-search-product-form").serializeArray(),
                            type: "POST",
                            dataType: "json",
                            success: function (res) {
                                console.log(res);
                            }
                        });
                        break;
                }
            })

            //  我的资源单
            $("#js-deleteResource").on("click", function () {
                var id = $(this).data("id");
                layer.confirm("确认删除该资源单吗？",
                    {
                        btn: ["确认", "取消"],
                        icon: 3
                    },
                    function () {
                        layer.msg("你点了确认");
                        $.post("/index.php?act=shop&m=resource&w=del&id=" + id, function (res) {
                            if (res.err == 0) {
                                layer.alert("删除成功");
                            } else {
                                layer.alert("删除失败");
                            }
                        })
                    }
                );

            });

            //   解析 数组资源单上传选择的经营范围和对应的品名专用函数
            var class2Ids = {};
            function resolve(class2Ids) {
                var tempType = [],
                    tempItem = [];

                for (var ids in class2Ids) {

                    if (class2Ids[ids].length) {

                        tempItem = tempItem.concat(class2Ids[ids]);

                        tempType.push(ids);
                    }
                }
                // console.log(tempItem);
                return {
                    types: tempType.join(","),
                    items: tempItem.join(",")
                }
            }
            //   上传资源单
            $("#js-uploadResource").on("click", function () {

                getClass2(function (data) {

                    layer.open({
                        title: "上传资源单",
                        closeBtn: 1,
                        area: [500, 400],
                        shadeClose: true,
                        btn: ["确定", "返回"],
                        content: [
                            '<form id="resourece-upload">',
                            '<ul>',
                            '<li>',
                            '<label class="resource-form-title">资源单名称：</label><input type="text" name="Vc_name">',
                            '</li>',
                            '<li>',
                            '<input type="hidden" name="Vc_itemClassIds">',
                            '<input type="hidden" name="Vc_itemIds">',
                            '<input type="hidden" name="Vc_res_file">',
                            '<label class="resource-form-title chk-select-title">主营品种：</label>',
                            '<div class="ib class2-wrap chk-select">',
                            (function (data) {
                                var html = "";

                                $(data).each(function (index, item) {
                                    html += '<label class="class2Id" data-id="' + this.id + '">' + this.Vc_name + '</label>';

                                    //   把对应类型的对象创建出来
                                    class2Ids[index] = [];
                                });
                                return html;

                            } (data.itemClassList)),
                            '<div class="class2-item chk-select"></div>',
                            '</div>',
                            '</li>',
                            '<li><label class="resource-form-title">已选择：</label>',
                            '<div class="class2-selected"></div>',
                            '</li>',
                            '<li><label class="resource-form-title">资源单说明：</label><input type="text" name="Vc_desc"></li>',
                            '<li><label class="resource-form-title">联系人：</label><input type="text" name="Vc_contact"></li>',
                            '<li><label class="resource-form-title">联系人电话：</label><input type="text" name="Vc_contact_phone" data-rule="联系号码：require;mobile;"></li>',
                            '<li><label class="resource-form-title">资源单：</label><a href="javascript:;" class="file-change" id="resource-upload-file">选择文件</a><span class="filename"></span></li>',
                            '</ul>',
                            '</form>'
                        ].join(""),
                        yes: function (index) {
                            var ids = resolve(class2Ids);
                            $class2Item.attr("value", ids.items);
                            $class2Type.attr("value", ids.types);
                            //   提交表单
                            $.post("/index.php?act=shop&m=resource&w=add", $form.serializeArray(), function (data) {

                                if (data.err == 0) {
                                    layer.alert("上传成功", function () {
                                        layer.closeAll();
                                        location.reload();
                                    });
                                }
                            }, "json");

                        },
                        btn2: function (index) {
                            layer.close(index);
                        }
                    });

                    //   保存一些变量
                    var $form = $("#resourece-upload"),
                        $wrap = $form.find(".class2-wrap"),
                        $item = $form.find(".class2-item"),
                        $class2Type = $form.find("input[name='Vc_itemClassIds']"),
                        $class2Item = $form.find("input[name='Vc_itemIds']"),
                        $fileInput = $form.find("input[name='Vc_res_file']"),
                        $this = undefined,
                        $selected = $form.find(".class2-selected"),
                        $before = 0,
                        $save = {}; //用来保存之前已选择的项

                    //   主营品种的选择
                    $wrap.off("click").on("click", "label", function (ev) {

                        //    根据class判断是点击的类型还是品名的label
                        if (/class2Id/.test($(this).attr("class"))) {

                            $this = $(this);

                            getClass2Item($this.data("id"), function (data) {

                                var id = $before ? $before.data("id") : 0;

                                $this.addClass("selected");

                                //   如果不存在该id对应的缓存就添加该缓存属性                                
                                !$save[$this.data("id")] && ($save[$this.data("id")] = null);

                                //   如果之前的是第一个或者跟上一个是一样的就不克隆。主要是保存之前的选中状态
                                $before && ($save[$before.data("id")] = $item.children().clone(true, true));

                                if (!$save[$this.data("id")]) {

                                    var html = "";

                                    $(data.itemList).each(function () {
                                        html += '<label data-id="' + this.id + '">' + this.Vc_name + '</label>';
                                    })

                                    $item.html(html);

                                } else {

                                    //   如果当前的节点是是之前选过的就直接使用clone的节点状态
                                    $item.empty().append($save[$this.data("id")]);
                                    $item.show();

                                }

                                // 点击切换显隐品名的box
                                if (($this.data("id") == id && $this.hasClass("selected")) || ($this.data("id") !== id && $this.hasClass("selected"))) {
                                    $item.css("top", $this.position().top + 30);
                                    $item.show();
                                    $this.addClass("selected");

                                } else if (($this.data("id") == id && !$this.hasClass("selected")) || ($this.data("id") !== id && !$this.hasClass("selected"))) {
                                    $item.hide();
                                }

                                //    如果没选择 一个品名就删除上一个分类对应的class
                                if ($before && !class2Ids[$before.data("id")].length && id !== $(this).data("id")) {
                                    $before.removeClass("selected");
                                }

                                //    保存上一个选择的类型的对象
                                setTimeout(function () {
                                    $before = $this;
                                }, 0);

                            });

                        } else {

                            $(this).toggleClass("selected");

                            //   点击的是品名项
                            if ($(this).hasClass("selected")) {

                                class2Ids[$this.data("id")].push($(this).data("id"));
                                $selected.append("<span class='class2-selected-item' id='s" + $(this).data("id") + "'>" + $(this).text() + "</span>");

                            } else {

                                class2Ids[$this.data("id")].removeByValue($(this).data("id"));
                                $("#s" + $(this).data("id")).remove();

                            }

                            //    判断 如果取消完了 对应的类名就删除
                            //  顺便清除对应的类型id值

                            if ($(this).parent().find(".selected").length == 0 && $this.hasClass("class2Id")) {

                                $this.removeClass("selected");
                                class2Ids[$this.data("id")].length = 0;

                            } else if (!$this.hasClass("selected")) {
                                $this.addClass("selected");
                            }

                            //  else if(!($this.data("id") in class2Ids) && !$this.hasClass("class2Id")) {

                            //     class2Ids[$this.data("id")].push($this.data("id"));
                            //     // $selected.append("<span class='class2-selected-item' id='s"+$(this).data("id")+"'>"+$(this).text()+"</span>");
                            // }

                        }

                    })

                    //    取消冒泡
                    $wrap.on("click", function (ev) {
                        ev.stopPropagation();
                    })

                    //  关闭主营品种选择框  
                    $form.on("click", function (ev) {
                        $item.hide();
                    })

                    //   上传资源单文件
                    new AjaxUpload("#resource-upload-file", {
                        action: "/index.php?act=shop&m=resource&w=upload",
                        name: "resfile",
                        onSubmit: function (file, ext) {
                            if (ext && /^(txt|doc|xls|xlsx)$/.test(ext)) {
                                //ext是后缀名
                                $("#resource-upload-file").text("正在上传…");
                                $("#resource-upload-file").prop("disabled", false);

                            } else {
                                layer.msg("不支持此格式文件，请检查！");
                                return false;
                            }
                        },
                        onComplete: function (file, data) {
                            $("#resource-upload-file").next().text(file);
                            $fileInput.attr("value", data);
                            $("#resource-upload-file").text("上传成功");
                        }
                    })
                });
            })

            //   获取分类id
            function getClass2(callback) {
                if (!getClass2.data) {

                    $.get("/index.php?act=shop&m=resource&w=itemclasslist", function (data) {
                        getClass2.data = data;
                        callback(getClass2.data);
                    },"json");

                } else {
                    callback(getClass2.data);
                }

            }

            function getClass2Item(id, callback) {

                if (!getClass2Item.data) {
                    getClass2Item.data = {};
                }

                if (!getClass2Item.data[id]) {
                    $.get("/index.php?act=shop&m=resource&w=itemclassinfo&I_classID=" + id, function (data) {
                        getClass2Item.data[id] = JSON.parse(data);
                        callback(getClass2Item.data[id]);
                    });
                } else {
                    callback(getClass2Item.data[id]);
                }

            }

            //   首页轮播管理 -- 上传轮播图片

            /**
             * @comment 
             * 由于时间原因，暂时处理为表单提交的方式
             * date 2016.07.06
             */
            // new AjaxUpload("#js-slide-upload",{
            //     action:"",
            //     name:"",
            //     autoSubmit:true,
            //     onSubmit: function(file, ext){
            //         console.log();
            //     },
            //     onChange: function(file, ext){
            //         console.log(file);
            //     }
            // })
            $("#js-slide-upload").on("change", function () {

                var val = this.value.replace(/C:\\fakepath\\/, "");

                if (!/.\.(png|jpg|jpeg)$/.test(val)) {

                    layer.msg("格式不正确,请重新选择");
                    this.value = "";
                    return false;
                }

            })

            //添加仓库
            $("#js-warehouse-add").on("submit", function () {
                $.post("/index.php?act=shop&m=warehouse&w=add&submit", $(this).serializeArray(), function (res) {
                    showMsg(res);
                },"json");

                return false;
            })

            //  编辑仓库
            $("#js-warehouse-edit").on("submit", function(){
                $.post("/index.php?act=shop&m=warehouse&w=edit&submit", $(this).serializeArray(), function(res){
                   showMsg(res);
                }, "json");

                return false;
            })

            //   删除仓库
            $("#js-warehouse-list a.delete").on("click", function () {
                listDelete.call(this, "/index.php?act=shop&m=warehouse&w=del&id=", $(this).data("id"));
            });

            // 集采报名
            $("#js-concentrated a").on("click", function(){
                //   如果点击的a标签没有event属性，说明不是要处理的按钮，就直接返回
                if(!$(this).data("event")) {
                    return false;
                }

                var event = $(this).data("event"),
                    id = $(this).data("id");

                switch (event) {
                    case "sign":
                        concentratedOptions(id);
                        break;
                    
                    case "change":
                        concentratedOptions(id, "change");
                        break;

                    case "delete":
                        listDelete.call(this,"/index.php?act=shop&m=concentrated&w=del&id=",id);
                        break;
                }
            });

            //轮播列表删除
            $("#js-slide-list a.event-delete").on("click", function(){
                listDelete.call(this, "/index.php?act=shop&m=slides&w=del&id=", $(this).data("id"));
            })
            
            //   修改 或者 报名操作
            function concentratedOptions(id, type) {
                // console
                if( type ) {
                    $.getJSON("/index.php?act=shop&m=concentrated&w=edit&id="+id, function(res){
                        if ( res.err == 0 ) {
                            var data = res.data;
                            //   修改信息时
                            layer.open({
                                title: "修改报名信息",
                                closeBtn: 1,
                                area: [300, 200],
                                shadeClose: true,
                                btn: ["确定", "返回"],
                                content:[
                                    '<form class="concentrated-sign" id="concentrated-sign">',
                                        '<input type="hidden" name="id" value="'+id+'">',
                                        '<div class="item"><label class="title">公司名称：</label><input type="text" name="company" value="'+data.company+'"></div>',
                                        '<div class="item"><label class="title">联系人：</label><input type="text" name="contact" value="'+data.contact+'"></div>',
                                        '<div class="item"><label class="title">电话号码：</label><input type="text" name="phone" value="'+data.phone+'"></div>',
                                    '</form>'
                                ].join(""),
                                yes: function(index){
                                    $.ajax({
                                        url:"/index.php?act=shop&m=concentrated&w=add",
                                        data:$("#concentrated-sign").serializeArray(),
                                        dataType:"json",
                                        success: function(res){
                                            if (res.err == 0) {
                                                layer.msg("报名成功");
                                                layer.close(index);
                                            } else {
                                                layer.msg(res.msg);
                                            }
                                        }
                                    })
                                },
                                btn2: function(index){
                                    // 点击取消按钮的时候关闭弹窗
                                    layer.close(index);
                                }
                            })
                        } else {
                            layer.msg("数据获取失败");
                        }
                    },"json");
                } else {
                    //   报名时的弹窗
                    layer.open({
                        title: "填写报名",
                        closeBtn: 1,
                        area: [300, 200],
                        shadeClose: true,
                        btn: ["确定", "返回"],
                        content:[
                            '<form class="concentrated-sign" id="concentrated-sign">',
                                '<input type="hidden" name="id" value="'+id+'">',
                                '<div class="item"><label class="title">公司名称：</label><input type="text" name="company"></div>',
                                '<div class="item"><label class="title">联系人：</label><input type="text" name="contact"></div>',
                                '<div class="item"><label class="title">电话号码：</label><input type="text" name="phone"></div>',
                            '</form>'
                        ].join(""),
                        yes: function(index){
                            $.ajax({
                                url:"/index.php?act=shop&m=concentrated&w=add",
                                data:$("#concentrated-sign").serializeArray(),
                                dataType:"json",
                                success: function(res){
                                    if (res.err == 0) {
                                        layer.msg("报名成功");
                                        layer.close(index);
                                    } else {
                                        layer.msg(res.msg);
                                    }
                                }
                            })
                        },
                        btn2: function(index){
                            // 点击取消按钮的时候关闭弹窗
                            layer.close(index);
                        }
                    })
                }
            }

            //  删除操作
            function listDelete(url, id) {
                var $this = $(this);
                layer.confirm("确认删除该信息？", {
                    icon: 3
                }, function (index) {
                    $.getJSON(url + id, function (res) {
                        showMsg(res);
                    });
                });
            }

            //   删除操作msg封装
            function showMsg(res){
                 if (res.err == 0) {
                    layer.alert(res.msg, {
                        icon: 1,
                        title: "提示"
                    }, function (index) {
                        layer.close(index);
                        location.reload();
                    })
                } else {
                    layer.alert(res.msg, {
                        icon: 7,
                        title: "警告消息"
                    }, function (index) {
                        layer.close(index);
                    })
                }
            }

        })(jQuery);

        //   密码强度检测
        // function passwdStrength(password) {

        //     if (/(?=.*[0-9])(?=.*[a-zA-Z])(?=.*[^a-zA-Z0-9]).{6,20}/.test(password)) {
        //         return 3;
        //     } else if (/(?=.*[0-9])(?=.*[a-zA-Z]).{6,20}/.test(password)) {
        //         return 2;
        //     } else if (/(?=.*[a-zA-Z])(?=.*[^0-9a-zA-Z]).{6,20}/.test(password)) {
        //         return 2;
        //     } else if (/(?=.*[a-zA-Z])(?=.*\d).{6,20}/.test(password)) {
        //         return 2;
        //     } else if (/(?=.*\d)(?=.*[^0-9a-zA-Z]).{6,20}/.test(password)) {
        //         return 2;
        //     } else if (/.{6,20}/.test(password)) {
        //         return 1;
        //     } else {
        //         return 0;
        //     }

        // }
        // console.log(passwdStrength('@ww@ww2w'));
    })
} (window.form = window.form || {}, jQuery, window.search));
