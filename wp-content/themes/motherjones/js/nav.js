var expandMenu = function() {
  $('#mj_menu_select').toggleClass('open');
  $('#navbar .menu-button').toggleClass('open');
};

var expandSearch = function(inputContainer) {
  $('#' + inputContainer).toggleClass('open');
};

var hideSearch = function(inputContainer) {
  $('#' + inputContainer).removeClass('open');
};

(function($) {
  //Make the top nav bar drop on scroll
  var begin, navbar, navCheck, beginningOffset;
  var BEGIN_SELECTOR = '#content';
  var NAVBAR_SELECTOR = '#navbar';
  var LOWER_CLASS = 'lower';

  $(document).ready(function() {
    begin = $(BEGIN_SELECTOR); //probably not here
    navbar = $(NAVBAR_SELECTOR);
    navCheck();
    $(document).scroll(navCheck);
  });

  navCheck = function() {
    if (navbar.offset().top > begin.offset().top) {
      navbar.addClass(LOWER_CLASS);
      $('.blockname-block-block-clickable-sitewrap').addClass('navdown')
    } else {
      navbar.removeClass(LOWER_CLASS);
      $('.blockname-block-block-clickable-sitewrap').removeClass('navdown')
    }
  };

})(jQuery)
