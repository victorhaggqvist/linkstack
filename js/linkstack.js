/**
 * Created by victor on 7/20/15.
 */

!(function () {
    var _form = document.querySelector('#pushform');
    var _url = document.querySelector('#form_url');
    var _title = document.querySelector('#form_title');
    var _tags = document.querySelector('#form_tags');
    var _save = document.querySelector('#form_save');
    var _table = document.querySelector('#recentlist tbody');

    var APIENDPOINT = "/api";

    var _serializeForm = function(formEle) {
        console.log(formEle.elements);
        return {
            title: formEle.elements[0].value,
            url: formEle.elements[1].value,
            tags: formEle.elements[2].value
        };
    };

    _save.onclick = function (event) {
        event.preventDefault();

        var body = _serializeForm(_form);
        fetch('/api/items', {
            method: 'post',
            body: JSON.stringify(body),
            credentials: 'include'
        }).then(function(resp) {
            return resp.json();
            //_putRecent(body.url, body.title, body.tags)
        }).then(function (json) {
            console.log(json);
            _putRecent(body.url, body.title, body.tags, json.id);
        }).catch(function(err) {
            console.log(err);
        });
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
            '<td class="action"><button type="button" class="btn btn-default btn-sm delete-btn icon-button" data-id="'+id+'"><span class="glyphicon glyphicon-trash" aria-hidden="true"></span></button></td>';

        _table.insertBefore(item, _table.firstChild);

        var binder = document.querySelector('button[data-id="'+id+'"]');
        binder.onclick = _deleteItem;
    };

    var _deleteItem = function(){
        console.log(this);
        var tempItem = this;
        var id = this.getAttribute('data-id');

        tempItem.className += ' spinner';
        fetch(APIENDPOINT + '/items/' + id, {
            method: 'delete',
            credentials: 'include'
        }).then(function(resp) {
            if (resp.status === 204) {
                //tempItem.parentNode.parentNode.remove();
                //_deleteItemResponse(tempItem, xhr);
            }
            console.log(resp);
        }).catch(function (err) {
            console.error(err);
        });

        //var xhr = new XMLHttpRequest();
        //xhr.open("DELETE", APIENDPOINT + '/items/'+id, true);
        //xhr.onreadystatechange = function () {
        //    if (xhr.readyState !== 4 || xhr.status !== 200) {
        //        return;
        //    }
        //
        //    // spinner.style.display = 'block';
        //    console.log("done"+xhr.status);
        //    _deleteItemResponse(tempItem, xhr);
        //};
        //

        //xhr.send();
    };

    var setUp = (function() {
        console.info("set hooks");
        //if ('undefined' !== _url){
        //    _url.onkeyup = _loadinfo;
        //    _url.onmouseout = _loadinfo;
        //    _url.onchange = _loadinfo;
        //    _push.onclick = _pushNewItem;
        //}

        var buttons = document.querySelectorAll('.delete-btn');
        Array.prototype.forEach.call(buttons, function(element, index, array) {
            element.onclick = _deleteItem;
            //console.log('a[' + index + '] = ' + element);
        });

        console.info('linkstack boot');
    })();

})();