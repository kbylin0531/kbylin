/**
 * Created by linzh_000 on 2016/3/25.
 */


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
    url = url.replace(/(\/(p)\/\d+)|(&p=\d+)|(\/(id)\/\d+)|(&id=\d+)|(\/(group)\/\d+)|(&group=\d+)/, "");
    subnav.find("a[href='" + url + "']").parent('li').addClass("current");

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

});
