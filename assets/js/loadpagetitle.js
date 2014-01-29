function loadtitle() {
  var url = $('#url');
  var title = $('#title');
  if($('#title').val()=="" && url.val().length>0){
    
    $.ajax({
      url: 'core/getpagetitle.php',
      data: {"url": url.val()},
      beforeSend: function() {
        title.addClass('loading');
      },
      complete: function() {
        title.removeClass('loading');
      },
      success: function(data) {
        title.val(data);
      }
    });
  }
};
// [review] - Trigger for a right-click -> paste also, using mouseout as hack meanwhile
$('#url').keyup(loadtitle).change(loadtitle).mouseout(loadtitle);