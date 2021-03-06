/*jslint browser: true*/
/*global $, jQuery, Modernizr, enquire*/
(function (window, document, $, Drupal) {
    "use strict";
  var $html = $('html'),
    mobileOnly = "screen and (max-width:47.9375em)", // 767px.
    mobileTabletOnly = "screen and (max-width: 63.9375em)", // 1023px.
    mobileLandscape = "(min-width: 568px)", // 480px.
    tablet = "(min-width:48em)", // 768px.
    desktop = "(min-width: 64em)"; // min-width: 1024px.

  $(".js-carousel").slick({
    autoplay: false,
    dots: true,
    speed: 500,
    customPaging: function(slick,index) {
      var thumb = $(slick.$slides[index]).find('img').data('thumb');
      var title = $(slick.$slides[index]).find('img').data('title');
      return '<div><span data-slick-index="0" class="progressBar"></span><span class="thumb-title">'+title+'</span><img src="'+thumb+'"></div>';
    },
    nextArrow: '<div class="slick--next">Next</div>',
    prevArrow: '<div class="slick--prev">Prev</div>',

    responsive: [{
        breakpoint: 568,
        settings: {
            speed: 500,
            dots: true,
            arrows: true,
            infinite: true,
            customPaging: function(slick,index) {
              return index;
          },
        }
    }]
  });
  //ticking machine
  var percentTime;
  var tick;
  var time = 1;
  var progressBarIndex = 0;

  $('.slick-dots .progressBar').each(function(index) {
      var progress = "<div class='inProgress inProgress" + index + "'></div>";
      $(this).html(progress);
  });

  function startProgressbar() {
      resetProgressbar();
      percentTime = 0;
      tick = setInterval(interval, 3);
  }

  function interval() {
      if (($('.carousel-list .slick-track div[data-slick-index="' + progressBarIndex + '"]').attr("aria-hidden")) === "true") {
          progressBarIndex = $('.carousel-list .slick-track div[aria-hidden="false"]').data("slickIndex");
          startProgressbar();
      } else {
          percentTime += 1 / (time + 5);
          $('.inProgress' + progressBarIndex).css({
              width: percentTime + "%"
          });
          if (percentTime >= 100) {
              $('.js-carousel').slick('slickNext');
              progressBarIndex++;
              if (progressBarIndex > 2) {
                  progressBarIndex = 0;
              }
              startProgressbar();
          }
      }
  }

  function resetProgressbar() {
      $('.inProgress').css({
          width: 0 + '%'
      });
      clearInterval(tick);
  }
  startProgressbar();
  // End ticking machine

  $('js-carousel .slick-dots div').click(function () {
    clearInterval(tick);
    var goToThisIndex = $(this).find("span").data("slickIndex");
    $('.js-carousel').slick('slickGoTo', goToThisIndex, false);
    startProgressbar();
  });

  $( '#dl-menu' ).dlmenu({
    animationClasses : { classin : 'dl-animate-in-2', classout : 'dl-animate-out-2' }
  });

  $(".js-filter .js-filter-click").on("click", function() {
    if ($(this).hasClass("active")) {
      console.log('1');
      $(this).removeClass("active");
      $(this)
        .siblings(".block-filter__content")
        .slideUp(500);
    } else {
      console.log('2');
      $(".js-filter .js-filter-click").removeClass("active");
      $(this).addClass("active");
      $(".block-filter__content").slideUp(500);
      $(this)
        .siblings(".block-filter__content")
        .slideDown(500);
    }
  });

  $(".js-filter-item  > .js-click").on("click", function() {
    if ($(this).hasClass("active")) {
      $(this).removeClass("active");
      $(this)
        .siblings(".block-filter__content__list")
        .slideUp(500);
    } else {
      $(".js-filter-item > .js-click").removeClass("active");
      $(this).addClass("active");
      $(".block-filter__content__list").slideUp(500);
      $(this)
        .siblings(".block-filter__content__list")
        .slideDown(500);
    }
  });

}(this, this.document, this.jQuery, this.Drupal));
