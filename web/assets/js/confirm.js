/*
* confirmDelete by Maxime AILLOUD
* Copyright or Licence ? No thanks I don't give a shit what you gonna do with it
*/
(function($) {
  $.fn.confirmDelete = function(message)
  {
    $(this).on('click', function(event)
    {
      var answer = confirm(message);

      if(!answer)
      {
        event.preventDefault();
      }
    });
    
    return this;
  };
})(jQuery);