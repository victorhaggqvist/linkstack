
function loadtitle() {
  var url = $('#url');
  var title = $('#title');

  // [todo] - use regex to check if url is valid
  /*var regex = "(http|https)(:\/\/)[a-ö]+(.)?";
  var p = new RegExp(regex,["i"]);
  var m = p.exec();*/
  if(url.val().length>10){
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

$('.delete-btn').click(function(){
  var itemId = $(this).attr('data-id');
  var loseElement = $(this).parent().parent();
  $.ajax({
    url: './api.php',
    data: {method: "delete", token: getCookie('token'), id: itemId},
    type: 'POST',
    beforeSend: function() {
      // [todo] - placeloader on top of trashbutton
    },
    success: function(result) {
      if(result == 1){
        loseElement.remove();
      }else
        console.log('fail');
    }
  });
});

$('#push-it').click(function(event) {
  event.preventDefault();
  var button = $('#push-it');

  var itemUrl = $('#url');
  var itemTitle = $('#title');
  var itemTags = $('#tags');
  var notify = $('#notify');

  if ( itemUrl.val().length>11 ){
    $.ajax({
      url: './api.php',
      data: {method: "new", token: getCookie('token'), url: itemUrl.val(), title: itemTitle.val(), tags: itemTags.val()},
      type: 'POST',
      beforeSend: function() {
        button.html('Pushing...');
        button.prop("disabled", true );
      },
      success: function(result) {
        if(result == 1){
          $('#notify-text').html(itemUrl.val()+" stashed!");
          notify.addClass( "alert-success" );
          $('#notify').slideDown("slow");
          putRecent(itemUrl.val(), itemTitle.val(), itemTags.val());
        }else{
          $('#notify-text').html(itemUrl.val()+" stashed!");
          notify.addClass( "alert-warning" );
          notify.slideDown("slow");
        }
        hideNotificationStore = setTimeout(hideNotification, 5000);
      },
      complete: function() {
        //restartCountDown();
        button.html('Push It!');
        button.prop( "disabled", false );
        itemUrl.val("");
        itemTitle.val("");
        itemTags.val("");
      }
    });
  }
});

function getCookie(cname){
  var name = cname + "=";
  var ca = document.cookie.split(';');
  for(var i=0; i<ca.length; i++){
    var c = ca[i].trim();
    if (c.indexOf(name)==0) return c.substring(name.length,c.length);
  }
  return "";
}

var hideNotificationStore;
function hideNotification(){
  var notify = $('#notify');
  notify.slideUp( "slow" );
  notify.removeClass( "alert-success" );
  clearTimeout(hideNotificationStore);
  //clearTimeout(countDownStore);
}
/*
var countDownStore;
function countDown(){
  console.log("cc");
  var c = $('#countdown').html();
  c--;
  $('#countdown').html(c);
  console.log(c);
}

function restartCountDown(){
  $('#countdown').html("5");
  countDownStore = setTimeout(countDown, 1000);
}*/

function putRecent(url,title,tags) {
  var date = new Date();
  var y = date.getFullYear();
  var m = ((date.getMonth()+1)<10)?'0'+(date.getMonth()+1):(date.getMonth()+1);
  var day = (date.getDate()<10)?'0'+date.getDate():date.getDate();
  var hours = (date.getHours()<10)?'0'+date.getHours():date.getHours();
  var mins = (date.getMinutes()<10)?'0'+date.getMinutes():date.getMinutes();
  var timestamp = y+'-'+m+'-'+day+' '+hours+':'+mins;

  var item =  '<tr>'+
  '<td><img src="https://www.google.com/s2/favicons?domain='+url+'" alt="fav">'+
  '<a class="title" href="'+url+'">'+url+'</a></td>'+
  '<td title="'+title+'">'+title+'</td>'+
  '<td>'+tags+'</td>'+
  '<td>'+timestamp+'</td>'+
  '<td class="action"><button type="button" class="btn btn-default btn-sm delete-btn" data-id=""><span class="glyphicon glyphicon-trash"></span></button></td>'+
  '</tr>';

  $('#recentlist tbody').prepend(item);
}
