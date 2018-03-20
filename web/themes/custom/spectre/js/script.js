/*jslint browser: true*/
/*global $, jQuery, Modernizr, enquire*/
(function (window, document, $, Drupal) {
    "use strict";
  var $html = $('html'),
    mobileOnly = "screen and (max-width:47.9375em)", // 767px.
    mobileTabletOnly = "screen and (max-width: 63.9375em)", // 1023px.
    mobileLandscape = "(min-width:30em)", // 480px.
    tablet = "(min-width:48em)", // 768px.
    desktop = "(min-width: 64em)"; // min-width: 1024px.
  // Add  functionality here.
  console.log('test');

  $(".js-carousel").slick({
    autoplay: true,
    dots: true,
    customPaging: function(slick,index) {
      var pager = $(slick.$slides[index]).find('img').data('thumb');
      return '<a><img src="'+pager+'"></a>';
    },

    responsive: [{
        breakpoint: 500,
        settings: {
            dots: false,
            arrows: false,
            infinite: false,
            slidesToShow: 2,
            slidesToScroll: 2
        }
    }]
  });


}(this, this.document, this.jQuery, this.Drupal));
