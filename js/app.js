/*jshint browser: true, devel:true*/

var LinkStack = (function() {
  "use strict";
  var _url = document.querySelector('#url');
  var _title = document.querySelector('#title');
  var _tags = document.querySelector('#tags');
  var _push = document.querySelector('#push-it');
  var _pushForm = document.querySelector('#push-form');
  var _key = null;
  var _timestamp = null;
  var _table = document.querySelector("#recentlist tbody");

  var METASERVICE = "https://eternal-dynamo-704.appspot.com";
  var APIENDPOINT = "./api";
  // var METASERVICE = "http://localhost:4242";
  var xhrmutex = false;

  var _loadinfo = function () {
    if (_url.value.length < 5 || xhrmutex)
      return;

    if (!(_title.value.length < 1 || _tags.value.length < 1))
      return;

    xhrmutex = true;
    var apiUrl = METASERVICE + '/info?url=' + _url.value;
    var xhr = new XMLHttpRequest();
    xhr.open("GET", apiUrl, true);
    xhr.onreadystatechange = function () {
      if (xhr.readyState !== 4 || xhr.status !== 200) {
        xhrmutex = false;
        return;
      }

      // spinner.style.display = 'block';
      handleInfoResponse(xhr.responseText);
    };
    xhr.send();
  };

  var handleInfoResponse = function (resp) {
    xhrmutex = false;
    //console.log(resp);
    var ret = JSON.parse(resp);
    if (ret.title != "None" && _title.value.length < 1)
      _title.value = ret.title;

    if (ret.meta != "None" && _tags.value.length < 1)
      _tags.value = ret.meta;

    if (_url.value != ret.longurl)
      _url.value = ret.longurl;
  };

  var _pushNewItem = function (e) {
    e.preventDefault();
    if (_url.value.length < 5)
      return;
    var newItem = new Item(_url.value, _title.value, _tags.value);

    var xhr = new XMLHttpRequest();
    xhr.open("POST", APIENDPOINT + '/items?key='+_key+'&timestamp='+_timestamp, true);
    _push.innerHTML = "Pushing...";
    _push.disabled = true;
    xhr.onreadystatechange = function () {
      if (xhr.readyState !== 4 || xhr.status !== 201) {
        return;
      }

      // spinner.style.display = 'block';
      //console.log("done"+xhr.status);
      _pushNewItemResponse(xhr);
    };
    var data = JSON.stringify(newItem);
    xhr.send(data);
  };

  var _pushNewItemResponse = function (xhr) {
    var resp = JSON.parse(xhr.responseText);
    _putRecent(_url.value, _title.value, _tags.value, resp.itemId);
    _push.innerHTML = "Push It!";
    _push.disabled = false;
    _clearForm();
  };

  var _clearForm = function() {
    _url.value = '';
    _title.value = '';
    _tags.value = '';
  };

  var _putRecent = function(url, title, tags, id) {
    var date = new Date();
    var y = date.getFullYear();
    var m = ((date.getMonth()+1)<10) ? '0'+(date.getMonth()+1) : (date.getMonth()+1);
    var day = (date.getDate()<10) ? '0'+date.getDate() : date.getDate();
    var hours = (date.getHours()<10) ? '0'+date.getHours() : date.getHours();
    var mins = (date.getMinutes()<10) ? '0'+date.getMinutes() : date.getMinutes();
    var timestamp = y+'-'+m+'-'+day+' '+hours+':'+mins;

    var titleTags = tags.length > 25 ? 'title="'+tags.substr(0,25)+'"':'';
    var displayTags = tags.length > 25 ? tags.substr(0,25)+'...':tags;
    var titleTitle = title.length > 50 ? 'title="'+title.substr(0,50)+'"':'';
    var displayTitle = title.length > 50 ? title.substr(0,50)+'...':title;

    var item = document.createElement('tr');
    item.innerHTML = '<td><img src="https://www.google.com/s2/favicons?domain='+url+'" alt="fav"> '+
      '<a class="title" href="'+url+'">'+url+'</a></td>'+
      '<td '+titleTitle+'>'+displayTitle+'</td>'+
      '<td '+titleTags+'>'+displayTags+'</td>'+
      '<td>'+timestamp+'</td>'+
      '<td class="action"><button type="button" class="btn btn-default btn-sm delete-btn icon-button" data-id="'+id+'"><svg><use xlink:href="#icon-trash-o" /></svg></button></td>';

    _table.insertBefore(item, _table.firstChild);

    var binder = document.querySelector('button[data-id="'+id+'"]');
    binder.onclick = _deleteItem;
  };

  var _deleteItem = function(){
    console.log(this);
    var tempItem = this;
    var id = this.getAttribute('data-id');
    var xhr = new XMLHttpRequest();
    xhr.open("DELETE", APIENDPOINT + '/items/'+id+'?key='+_key+'&timestamp='+_timestamp, true);
    xhr.onreadystatechange = function () {
      if (xhr.readyState !== 4 || xhr.status !== 200) {
        return;
      }

      // spinner.style.display = 'block';
      console.log("done"+xhr.status);
      _deleteItemResponse(tempItem, xhr);
    };

    tempItem.className += ' spinner';
    xhr.send();
  };

  var _deleteItemResponse = function(element, xhr) {
    console.log('muu'+xhr.status);
    var row = element.parentNode.parentNode;
    console.log(row);
    //console.log(table.removeChild((Node)))

    if (xhr.status == 200)
      row.remove();
    //  teble.removeChild((Node) element);
  };

  var setUp = function(key, timestamp) {
    _key = key;
    _timestamp = timestamp;

    console.log("set hooks");
    if (_url !== null){
      _url.onkeyup = _loadinfo;
      _url.onmouseout = _loadinfo;
      _url.onchange = _loadinfo;
      _push.onclick = _pushNewItem;
    }

    var buttons = document.querySelectorAll('.delete-btn');
    Array.prototype.forEach.call(buttons, function(element, index, array) {
      element.onclick = _deleteItem;
      //console.log('a[' + index + '] = ' + element);
    });

    console.log("loaded");
  };

  var Item = function(url, title, tags){
    var item = {};
    item.url = url;
    item.title = title;
    item.tags = tags;
    return item;
  };

  return {
    setUp: setUp
  };
})();
