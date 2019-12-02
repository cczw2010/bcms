/**
 * Created by wangzhiwei on 2017/8/25.
 */
function toId(id) {
    var idT = $("#" + id).offset().top,
        headerH = $(".header").outerHeight(),
        subnavH = $(".subnav").outerHeight() || 0;
    $("html,body").animate({
        scrollTop: idT - headerH - subnavH
    }, 500);
}

function toClass(_class) {
    var classT = $("." + _class).offset().top,
        headerH = $(".header").outerHeight();
    $("html,body").animate({
        scrollTop: classT - headerH
    }, 500);
}

function initTimeLine() {
    var l = $(".timeline-wrapper .swiper-slide").length,
        w = $(".timeline-wrapper .swiper-slide").outerWidth();
    // if (l % 2 == 0) {
    //     var timelineWidth = l / 2 * (w - 10) + 95;
    // } else {
    //     var timelineWidth = (l - 1) / 2 * (w - 10) + w;
    // }
    // awen 改一点
    var timelineWidth = (l / 2) * w + 100;

    $(".timeline-wrapper").width(timelineWidth);

    var firstSlideLeft = $(".timeline-wrapper .swiper-slide").eq(0).offset().left,
        firstSlideTop = $(".timeline-wrapper .swiper-slide").eq(0).offset().top;
    var timelineLeft = $(".timeline").offset().left,
        timelineTop = $(".timeline").offset().top;
    $(".swiper-slide-bg").css({
        "top": (firstSlideTop - timelineTop - 21) + "px",
        "left": (firstSlideLeft - timelineLeft - 19) + "px"
    })
    var prevYear = '';
    $(".timeline-wrapper .swiper-slide").each(function(i) {
        var year = $(this).find("h3").text();
        // awen 如果与之前的年份相同则隐藏
        if (year == prevYear) {
            $(this).find("h3").hide();
        } else {
            prevYear = year;
        }
    })

}

function scrollTimeLine(i) {
    var twrapperLeft = $(".timeline-wrapper").offset().left; //æ—¶é—´è½´çš„ä½ç½®
    var twrapperWidth = $(".timeline-wrapper").outerWidth(); //æ—¶é—´è½´çš„å®½åº¦
    var timelineLeft = $(".timeline").offset().left,
        timelineTop = $(".timeline").offset().top;
    var indexSlideLeft = $(".timeline-wrapper .swiper-slide").eq(i).offset().left,
        indexSlideTop = $(".timeline-wrapper .swiper-slide").eq(i).offset().top;
    var transitionLeft = timelineLeft - twrapperLeft;

    var w = $(".timeline-wrapper .swiper-slide").outerWidth(),
        timelineW = $(".timeline").outerWidth();
    var twidth = 0;
    if (w + indexSlideLeft > timelineW + timelineLeft) {
        twidth = indexSlideLeft - timelineLeft - timelineW / 2;
        if (indexSlideLeft + w + timelineW / 2 > twrapperWidth + twrapperLeft) {
            twidth = twrapperWidth - transitionLeft - timelineW
        }
        $(".timeline-wrapper").css({
            "transform": "translate3d(" + (-transitionLeft - twidth) + "px,0,0)"
        })
    } else if (indexSlideLeft < timelineLeft) {
        twidth = -timelineLeft + indexSlideLeft - timelineW / 2;
        if (indexSlideLeft - twrapperLeft < timelineW / 2) {
            twidth = -timelineLeft + twrapperLeft;
        }
        $(".timeline-wrapper").css({
            "transform": "translate3d(" + (-transitionLeft - twidth) + "px,0,0)"
        })
    }
    $(".swiper-slide-bg").css({
        "top": (indexSlideTop - timelineTop - 21) + "px",
        "left": (indexSlideLeft - timelineLeft - 19 - twidth) + "px"
    })
}