/**
 * Resize function without multiple trigger
 * 
 * Usage:
 * $(window).smartresize(function(){  
 *     // code here
 * });
 */
(function($,sr){
    // debouncing function from John Hann
    // http://unscriptable.com/index.php/2009/03/20/debouncing-javascript-methods/
    var debounce = function (func, threshold, execAsap) {
      var timeout;

        return function debounced () {
            var obj = this, args = arguments;
            function delayed () {
                if (!execAsap)
                    func.apply(obj, args); 
                timeout = null; 
            }

            if (timeout)
                clearTimeout(timeout);
            else if (execAsap)
                func.apply(obj, args);

            timeout = setTimeout(delayed, threshold || 100); 
        };
    };

    // smartresize 
    jQuery.fn[sr] = function(fn){  return fn ? this.bind('resize', debounce(fn)) : this.trigger(sr); };

})(jQuery,'smartresize');
/**
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

var CURRENT_URL = window.location.href.split('?')[0],
    $BODY = $('body'),
    $MENU_TOGGLE = $('#menu_toggle'),
    $SIDEBAR_MENU = $('#sidebar-menu'),
    $SIDEBAR_FOOTER = $('.sidebar-footer'),
    $LEFT_COL = $('.left_col'),
    $RIGHT_COL = $('.right_col'),
    $NAV_MENU = $('.nav_menu'),
    $FOOTER = $('footer'),
    $SEARCH = $('#show-search'),
    $SEARCH_BOX = $('.search_box'),
    $DATETIMEPICKER = $('.datetimepicker');
    $TIMEPICKER = $('.timepicker');

// Sidebar
$(document).ready(function() {
    // TODO: This is some kind of easy fix, maybe we can improve this
    var setContentHeight = function () {
        // reset height
        // $RIGHT_COL.css('min-height', $(window).height());

        var bodyHeight = $BODY.outerHeight(),
            footerHeight = $BODY.hasClass('footer_fixed') ? -10 : $FOOTER.height(),
            leftColHeight = $LEFT_COL.eq(1).height() + $SIDEBAR_FOOTER.height(),
            contentHeight = bodyHeight < leftColHeight ? leftColHeight : bodyHeight;

        // normalize content
        contentHeight -= $NAV_MENU.height() + footerHeight;

        $RIGHT_COL.css('min-height', contentHeight);
    };

    $SIDEBAR_MENU.find('a').on('click', function(ev) {
        var $li = $(this).parent();

        if ($li.is('.active')) {
            $li.removeClass('active active-sm');
            $('ul:first', $li).slideUp(function() {
                setContentHeight();
            });
        } else {
            // prevent closing menu if we are on child menu
            if (!$li.parent().is('.child_menu')) {
                $SIDEBAR_MENU.find('li').removeClass('active active-sm');
                $SIDEBAR_MENU.find('li ul').slideUp();
            }
            
            $li.addClass('active');

            $('ul:first', $li).slideDown(function() {
                setContentHeight();
            });
        }
    });

    // toggle small or large menu
    $MENU_TOGGLE.on('click', function() {
        if ($BODY.hasClass('nav-md')) {
            $SIDEBAR_MENU.find('li.active ul').hide();
            $SIDEBAR_MENU.find('li.active').addClass('active-sm').removeClass('active');
        } else {
            $SIDEBAR_MENU.find('li.active-sm ul').show();
            $SIDEBAR_MENU.find('li.active-sm').addClass('active').removeClass('active-sm');
        }

        $BODY.toggleClass('nav-md nav-sm');

        setContentHeight();
    });

    // check active menu
    $SIDEBAR_MENU.find('a[href="' + CURRENT_URL + '"]').parent('li').addClass('current-page');

    $SIDEBAR_MENU.find('a').filter(function () {
        return this.href == CURRENT_URL;
    }).parent('li').addClass('current-page').parents('ul').slideDown(function() {
        setContentHeight();
    }).parent().addClass('active');

    //toggle search box on xs screen
    $SEARCH.on('click', function(){
        if($SEARCH_BOX.is(':hidden')){
            $SEARCH_BOX.slideDown();
        }else{
            $SEARCH_BOX.slideUp();
        }
    });

    $DATETIMEPICKER.daterangepicker({
        "calender_style": "picker_3",
        "singleDatePicker": true,
        todayHighlight: 1,
        "format" : "YYYY-MM-DD",
      }, function(start, end, label) {
    });

     $TIMEPICKER.daterangepicker({
        "calender_style": "picker_3",
        "timePicker": true,
        "timePicker24Hour": false,
        "singleDatePicker": true,
        "todayHighlight": 1,
        "format" : "YYYY-MM-DD HH:mm:ss",
      }, function(start, end, label) {
    });



    // recompute content when resizing
    $(window).smartresize(function(){  
        setContentHeight();
    });

    setContentHeight();

    // fixed sidebar
    if ($.fn.mCustomScrollbar) {
        $('.menu_fixed').mCustomScrollbar({
            autoHideScrollbar: true,
            theme: 'minimal',
            mouseWheel:{ preventDefault: true }
        });
    }
    $('.ui.checkbox').checkbox();
    $('.ui.radio.checkbox').checkbox();
    $('.message .close')
      .on('click', function() {
        $(this)
          .closest('.message')
          .transition('fade');
      });

    $('.todo_done').on('click',function(){
      $todo_li = $(this).parent();
      $.ajax({
        url:'/business-statuses/done',
        type:'post',
        data:{
          id:$todo_li.data('id')
        },
        success:function (data) {
          window.location.href = '/customers/view/'+$todo_li.data('c-id')
        },
        error:function (err) {
          alert('系统错误');
        }
      })

    });

    $('#bulk').find('[name=submit]').on('click',function(){
      op = $(this).text().trim();
      if (confirm('批量操作不可逆，是否确认进行\r \r '+op+'\r \r 操作？')) {
        return true;
      }
      return false;
    });
    
});
// /Sidebar

// Panel toolbox
$(document).ready(function() {
    $('.collapse-link').on('click', function() {
        var $BOX_PANEL = $(this).closest('.x_panel'),
            $ICON = $(this).find('i'),
            $BOX_CONTENT = $BOX_PANEL.find('.x_content');
        
        // fix for some div with hardcoded fix class
        if ($BOX_PANEL.attr('style')) {
            $BOX_CONTENT.slideToggle(200, function(){
                $BOX_PANEL.removeAttr('style');
            });
        } else {
            $BOX_CONTENT.slideToggle(200); 
            $BOX_PANEL.css('height', 'auto');  
        }

        $ICON.toggleClass('fa-chevron-up fa-chevron-down');
    });

    $('.close-link').click(function () {
        var $BOX_PANEL = $(this).closest('.x_panel');

        $BOX_PANEL.remove();
    });
});
// /Panel toolbox

// Tooltip
$(document).ready(function() {
    $('[data-toggle="tooltip"]').tooltip({
        container: 'body'
    });
});
// /Tooltip

// Progressbar
if ($(".progress .progress-bar")[0]) {
    $('.progress .progress-bar').progressbar();
}
// /Progressbar

// Switchery
$(document).ready(function() {
    if ($(".js-switch")[0]) {
        var elems = Array.prototype.slice.call(document.querySelectorAll('.js-switch'));
        elems.forEach(function (html) {
            var switchery = new Switchery(html, {
                color: '#26B99A'
            });
        });
    }
});
// /Switchery

// iCheck
$(document).ready(function() {
        $(document).ready(function () {
            $('input.flat').iCheck({
                checkboxClass: 'icheckbox_minimal-green',
                radioClass: 'iradio_minimal-green',
            });
        });
    }
);
// /iCheck

// Accordion
$(document).ready(function() {
    $(".expand").on("click", function () {
        $(this).next().slideToggle(200);
        $expand = $(this).find(">:first-child");

        if ($expand.text() == "+") {
            $expand.text("-");
        } else {
            $expand.text("+");
        }
    });
});

//模块首页ajax加载
$(function(){
   let page=1,
       params = window.location.search.slice(1),
       main=$('#main'),
       message = $('#message'),
       loading = false;
    if (main.length) {
        let controller= main.data('controller');
        LoadingDataFn(controller);
        $(window).on('scroll',function() {
            //当时滚动条离底部60px时开始加载下一页的内容
            if (($(window).height() + $(window).scrollTop() + 60) >= $(document).height() && !loading) {
                
                LoadingDataFn(controller);
                
            }
        });
    }


   function LoadingDataFn(controller){
       message.show();
       loading = true;
       $.ajax({
           url:'/'+controller+'/ajaxList/?'+params,
           type:'get',
           data:{
               page:page
           },
           success:function(data){
               if (data) {
                   if (data == 'authorized_wrong') {
                        alert('无权访问该页面.');
                        window.location.href='/';
                   }if (data == 'parameter_error') {
                        alert('Parameter error.Please,try again');
                        window.location.href='/';
                   } else {
                        main.append(data);
                        $('input.flat').iCheck({
                            checkboxClass: 'icheckbox_minimal-green',
                            radioClass: 'iradio_minimal-green',
                        });                        

                        $('input#check-all').on('ifChecked', function () {
                            $("input[name='ids[]']").iCheck('check');
                        });

                        $('input#check-all').on('ifUnchecked', function () {
                            $("input[name='ids[]']").iCheck('uncheck');
                        });
                        page++;
                        message.hide();
                   }

               } else {
                   message.text('没有更多，已经到底了~');
                   $(window).off('scroll');
               }
               loading = false;
           },error:function (error) {

               loading = false;
               alert('系统错误');
               $(window).off('scroll');
               message.hide();                    
           }
       });
   }
});

$(function () {
    $(window).scroll(function () {
        if ($(window).scrollTop() >= 100) {
            $('#btn_top').fadeIn();
        }
        else {
            $('#btn_top').fadeOut();
        }
    });
    $('#btn_top').on('click',function(){
        $('html,body').animate({ scrollTop: 0 }, 500);
    })
});


$('#del').on('submit',function () {
    if(!$('.check:checked').length){
        alert('未选中图片');
        return false;
    }
})



