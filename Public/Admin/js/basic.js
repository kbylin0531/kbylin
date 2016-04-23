/**
 * Created by linzh_000 on 2016/3/25.
 */

/**
 * http://www.w3school.com.cn/jquery/ajax_ajax.asp
 * @param url
 * @param data
 * @param callback
 * @param type
 * @param handler 信息处理回调函数，该回调返回true表示处理完成并直接返回
 * @returns {*}
 */
jQuery.ipost = function (url, data, callback, type , handler) {
    if ( jQuery.isFunction( data ) ) {
        type = type || callback;
        callback = data;
        data = undefined;
    }

    return jQuery.ajax({
        url: url,
        type: 'post',
        dataType: type,
        data: data,
        success: function (data,status) {
            if("object" === typeof data && "type" in data){
                if(undefined !== handler){
                    if(true === handler(data)) return ;
                }else{
                    return alert(data["message"]);
                }
            }
            return callback(data,status)
        }
    });
};


/**
 * 左侧菜单栏标题点击事件
 * @param $title
 */
var titleToggle = function ($title) {
    //如果不是jquery对象则修改成jquery对象
    !($title instanceof jQuery) && ($title = $($title));
    //修改图标
    $title.find(".icon").toggleClass("icon-fold");
    //①h3的下一个元素（ul）迅速收起     ②同辈元素的其他可见的全部收起
    $title.next().slideToggle("fast")
        //选出所有同类的显示中的side-sub-menu
        .siblings("ul.side-sub-menu:visible")
        //设置图标
        .prev("h3").find("i").addClass("icon-fold")
        //将同类的side-sub-menu隐藏
        .end().end().hide();
};
/**
 * 导航高亮
 * @param url
 */
var highlight_subnav = function(url){
    $('.side-sub-menu').find('a[href="'+url+'"]').closest('li').addClass('current');
};
//标签页切换(无下一步)
function showTab() {
    $(".tab-nav li").click(function(){
        var self = $(this), target = self.data("tab");
        self.addClass("current").siblings(".current").removeClass("current");
        window.location.hash = "#" + target.substr(3);
        $(".tab-pane.in").removeClass("in");
        $("." + target).addClass("in");
    }).filter("[data-tab=tab" + window.location.hash.substr(1) + "]").click();
}

//标签页切换(有下一步)
function nextTab() {
    $(".tab-nav li").click(function(){
        var self = $(this), target = self.data("tab");
        self.addClass("current").siblings(".current").removeClass("current");
        window.location.hash = "#" + target.substr(3);
        $(".tab-pane.in").removeClass("in");
        $("." + target).addClass("in");
        showBtn();
    }).filter("[data-tab=tab" + window.location.hash.substr(1) + "]").click();

    $("#submit-next").click(function(){
        $(".tab-nav li.current").next().click();
        showBtn();
    });
}

// 下一步按钮切换
function showBtn() {
    var lastTabItem = $(".tab-nav li:last");
    if( lastTabItem.hasClass("current") ) {
        $("#submit").removeClass("hidden");
        $("#submit-next").addClass("hidden");
    } else {
        $("#submit").addClass("hidden");
        $("#submit-next").removeClass("hidden");
    }
}


$(function () {

    /******** 头部管理员菜单 **************/
    var userbar = $("div.user-bar");
    userbar.mouseenter(function () {
        var userMenu = $(this).children(".user-menu");
        userMenu.removeClass("hidden");
        clearTimeout(userMenu.data("timeout"));//无返回值
    }).mouseleave(function () {
        var userMenu = $(this).children(".user-menu");
        userMenu.data("timeout") && clearTimeout(userMenu.data("timeout"));//如果之前设置了定时器，则清除定时
        userMenu.data("timeout", setTimeout(function(){ userMenu.addClass("hidden");}, 100));//setTimeout返回延时时间
    });

    /******** 内容区高度自动调整 **************/
    var $window = $(window);
    $window.resize(function () {
        //min-height为元素的最小高度，元素一定大于等于这个高度，当内容大于这个高度的时候会调整
        $("#main").css("min-height", $window.height() - 130);//130 = 50(顶部) + 20*2(body_padding) + 40(copyright)
        console.log($window.height());
    }).resize();

    /******** 左侧子菜单菜单 **************/
    var subnav = $("#subnav");
    var sidebar = $("#sidebar");


    /******** 左边菜单高亮 **************/
    var url = window.location.pathname;//参阅window.location对象
    //url = url.replace(/(\/(p)\/\d+)|(&p=\d+)|(\/(id)\/\d+)|(&id=\d+)|(\/(group)\/\d+)|(&group=\d+)/, "");
    //subnav.find("a[href='" + url + "']").parent('li').addClass("current");
    highlight_subnav(url);

    /******** 左边菜单标题栏点击收放 **************/
    subnav.on("click", "h3", function () {  titleToggle(this);});
    subnav.find("h3 a").click(function (e) {  e.stopPropagation();/* 终止时间的传递，暂时未用上 */ });


    /******** 表单获取焦点变色 **************/
    var form = $("form");
    form.on("focus", "input", function () {
        $(this).addClass('focus');
    }).on("blur", "input", function () {
        $(this).removeClass('focus');
    });
    form.on("focus", "textarea", function () {
        $(this).closest('label').addClass('focus');
    }).on("blur", "textarea", function () {
        $(this).closest('label').removeClass('focus');
    });

    /******** 导航栏超出窗口高度后的模拟滚动条 **************/
    var sidebarHeight = sidebar.height();
    var subnavHeight = subnav.height();
    var diff = subnavHeight - sidebarHeight;
    if (diff > 0) {
        $(window).mousewheel(function (event, delta) {
            if (delta > 0) {
                if (parseInt(subnav.css('marginTop')) > -10) {
                    subnav.css('marginTop', '0px');
                } else {
                    subnav.css('marginTop', '+=' + 10);
                }
            } else {
                if (parseInt(subnav.css('marginTop')) < '-' + (diff - 10)) {
                    subnav.css('marginTop', '-' + (diff - 10));
                } else {
                    subnav.css('marginTop', '-=' + 10);
                }
            }
        });
    }


    /******** 提示栏 **************/
    var top_alert = $('#top-alert');
    top_alert.find('.close').on('click', function () {
        /* 点击关闭按钮隐藏 */
        top_alert.removeClass('block').slideUp(200);
        //content.animate({paddingTop:'-=55'},200);//下往上移动
    });
    /**
     * 添加到window属性，可以直接调用
     * @param text
     * @param c
     */
    window.updateAlert = function (text,c) {
        //默认参数设置
        text = text||'default';
        switch (c){
            /* 可以直接书用数字代替字符串类型，避免出错  */
            case 0:
                c = 'alert-success';
                break;
            case 1:
                c = 'alert-success';
                break;
            case 2:
                c = 'alert-success';
                break;
            case 3:
                c = 'alert-success';
                break;
            case 4:
                c = 'alert-success';
                break;
            default:
                //否则直接使用
                c = c||false;
        }

        if ( text == 'default' ) {
            if (top_alert.hasClass('block')) {
                top_alert.removeClass('block').slideUp(200);
                // content.animate({paddingTop:'-=55'},200);
            }
        } else {
            top_alert.find('.alert-content').text(text);
            if (top_alert.hasClass('block')) {
            } else {
                top_alert.addClass('block').slideDown(200);
                // content.animate({paddingTop:'+=55'},200);
            }
        }

        if ( c != false ) {
            top_alert.removeClass('alert-error alert-warn alert-info alert-success').addClass(c);
        }
    };


    /******** 顶部导航栏高亮(只要导航栏的链接存在于子导航栏中就算为current) **************/
    var mainnav = $("#main-nav");
    var mainlias = mainnav.find('li a');
    var sublias = subnav.find('li a');
    for(var i=0;i<mainlias.length;++i){
        var href = mainlias[i].pathname;
        //console.log('NAV',href);
        for(var j=0;j<sublias.length;++j){
            //console.log('SUB',sublias[j].pathname);
            if(href === sublias[j].pathname){
                $(mainlias[i]).closest('li').addClass('current');
            }
        }
    }









    //全选的实现
    $(".check-all").click(function(){
        $(".ids").prop("checked", this.checked);
    });
    $(".ids").click(function(){
        var option = $(".ids");
        option.each(function(i){
            if(!this.checked){
                $(".check-all").prop("checked", false);
                return false;
            }else{
                $(".check-all").prop("checked", true);
            }
        });
    });

    //ajax get请求
    $('.ajax-get').click(function(){
        var target;
        var that = this;
        if ( $(this).hasClass('confirm') ) {
            if(!confirm('确认要执行该操作吗?')){
                return false;
            }
        }
        if ( (target = $(this).attr('href')) || (target = $(this).attr('url')) ) {
            $.get(target).success(function(data){
                if (data.status==1) {
                    if (data.url) {
                        updateAlert(data.info + ' 页面即将自动跳转~','alert-success');
                    }else{
                        updateAlert(data.info,'alert-success');
                    }
                    setTimeout(function(){
                        if (data.url) {
                            location.href=data.url;
                        }else if( $(that).hasClass('no-refresh')){
                            $('#top-alert').find('button').click();
                        }else{
                            location.reload();
                        }
                    },1500);
                }else{
                    updateAlert(data.info);
                    setTimeout(function(){
                        if (data.url) {
                            location.href=data.url;
                        }else{
                            $('#top-alert').find('button').click();
                        }
                    },1500);
                }
            });

        }
        return false;
    });

    //ajax post submit请求
    $('.ajax-post').click(function(){
        var target,query,form;
        var target_form = $(this).attr('target-form');
        var that = this;
        var nead_confirm=false;
        if( ($(this).attr('type')=='submit') || (target = $(this).attr('href')) || (target = $(this).attr('url')) ){
            form = $('.'+target_form);

            if ($(this).attr('hide-data') === 'true'){//无数据时也可以使用的功能
                form = $('.hide-data');
                query = form.serialize();
            }else if (form.get(0)==undefined){
                return false;
            }else if ( form.get(0).nodeName=='FORM' ){
                if ( $(this).hasClass('confirm') ) {
                    if(!confirm('确认要执行该操作吗?')){
                        return false;
                    }
                }
                if($(this).attr('url') !== undefined){
                    target = $(this).attr('url');
                }else{
                    target = form.get(0).action;
                }
                query = form.serialize();
            }else if( form.get(0).nodeName=='INPUT' || form.get(0).nodeName=='SELECT' || form.get(0).nodeName=='TEXTAREA') {
                form.each(function(k,v){
                    if(v.type=='checkbox' && v.checked==true){
                        nead_confirm = true;
                    }
                });
                if ( nead_confirm && $(this).hasClass('confirm') ) {
                    if(!confirm('确认要执行该操作吗?')){
                        return false;
                    }
                }
                query = form.serialize();
            }else{
                if ( $(this).hasClass('confirm') ) {
                    if(!confirm('确认要执行该操作吗?')){
                        return false;
                    }
                }
                query = form.find('input,select,textarea').serialize();
            }
            $(that).addClass('disabled').attr('autocomplete','off').prop('disabled',true);
            $.post(target,query).success(function(data){
                if (data.status==1) {
                    if (data.url) {
                        updateAlert(data.info + ' 页面即将自动跳转~','alert-success');
                    }else{
                        updateAlert(data.info ,'alert-success');
                    }
                    setTimeout(function(){
                        $(that).removeClass('disabled').prop('disabled',false);
                        if (data.url) {
                            location.href=data.url;
                        }else if( $(that).hasClass('no-refresh')){
                            $('#top-alert').find('button').click();
                        }else{
                            location.reload();
                        }
                    },1500);
                }else{
                    updateAlert(data.info);
                    setTimeout(function(){
                        $(that).removeClass('disabled').prop('disabled',false);
                        if (data.url) {
                            location.href=data.url;
                        }else{
                            $('#top-alert').find('button').click();
                        }
                    },1500);
                }
            });
        }
        return false;
    });

    /**顶部警告栏*/
        //按钮组
    (function(){
        //按钮组(鼠标悬浮显示)
        $(".btn-group").mouseenter(function(){
            var userMenu = $(this).children(".dropdown ");
            var icon = $(this).find(".btn i");
            icon.addClass("btn-arrowup").removeClass("btn-arrowdown");
            userMenu.show();
            clearTimeout(userMenu.data("timeout"));
        }).mouseleave(function(){
            var userMenu = $(this).children(".dropdown");
            var icon = $(this).find(".btn i");
            icon.removeClass("btn-arrowup").addClass("btn-arrowdown");
            userMenu.data("timeout") && clearTimeout(userMenu.data("timeout"));
            userMenu.data("timeout", setTimeout(function(){userMenu.hide()}, 100));
        });

        //按钮组(鼠标点击显示)
        // $(".btn-group-click .btn").click(function(){
        //     var userMenu = $(this).next(".dropdown ");
        //     var icon = $(this).find("i");
        //     icon.toggleClass("btn-arrowup");
        //     userMenu.toggleClass("block");
        // });
        $(".btn-group-click .btn").click(function(e){
            if ($(this).next(".dropdown").is(":hidden")) {
                $(this).next(".dropdown").show();
                $(this).find("i").addClass("btn-arrowup");
                e.stopPropagation();
            }else{
                $(this).find("i").removeClass("btn-arrowup");
            }
        });
        $(".dropdown").click(function(e) {
            e.stopPropagation();
        });
        $(document).click(function() {
            $(".dropdown").hide();
            $(".btn-group-click .btn").find("i").removeClass("btn-arrowup");
        });
    })();

    // 独立域表单获取焦点样式
    $(".text").focus(function(){
        $(this).addClass("focus");
    }).blur(function(){
        $(this).removeClass('focus');
    });
    $("textarea").focus(function(){
        $(this).closest(".textarea").addClass("focus");
    }).blur(function(){
        $(this).closest(".textarea").removeClass("focus");
    });

});
