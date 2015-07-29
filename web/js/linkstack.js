/**
 * Created by victor on 7/20/15.
 */

'use strict';

(function () {
    'use strict';

    var _form = document.querySelector('#pushform');
    var _url = document.querySelector('#form_url');
    var _title = document.querySelector('#form_title');
    var _tags = document.querySelector('#form_tags');
    var _save = document.querySelector('#form_save');
    var _table = document.querySelector('#recentlist tbody');

    var METASERVICE = 'https://eternal-dynamo-704.appspot.com';
    var _fetchmutex = false;

    var _serializeForm = function _serializeForm(formEle) {
        console.log(formEle.elements);
        return {
            title: formEle.elements[0].value,
            url: formEle.elements[1].value,
            tags: formEle.elements[2].value
        };
    };

    var _loadinfo = function _loadinfo() {
        if (_fetchmutex) return;

        if (_url.value.length < 5) return;

        if (!(_title.value.length < 1 || _tags.value.length < 1)) return;

        _fetchmutex = true;
        var apiUrl = METASERVICE + '/info?url=' + _url.value;
        fetch(apiUrl).then(function (resp) {
            return resp.json();
        }, function (err) {
            return console.error('meta fetch fail: ' + err);
        }).then(function (ret) {
            if (ret === undefined) return;
            _fetchmutex = false;
            if (ret.title !== 'None' && _title.value.length < 1) {
                _title.value = ret.title;
            }

            if (ret.meta !== 'None' && _tags.value.length < 1) {
                _tags.value = ret.meta;
            }

            if (_url.value !== ret.longurl) {
                _url.value = ret.longurl;
            }
        });
    };

    var _deleteItem = function _deleteItem() {
        console.log(this);
        var tempItem = this;
        var id = this.getAttribute('data-id');

        tempItem.className += ' spinner';
        fetch('/api/items/' + id, {
            method: 'delete',
            credentials: 'include'
        }).then(function (resp) {
            if (resp.status === 204) {
                tempItem.parentNode.parentNode.remove();
            }
            console.log(resp);
        })['catch'](function (err) {
            return console.error(err);
        });
    };

    var _putRecent = function _putRecent(url, title, tags, id) {
        var date = new Date();
        var y = date.getFullYear();
        var m = date.getMonth() + 1 < 10 ? '0' + (date.getMonth() + 1) : date.getMonth() + 1;
        var day = date.getDate() < 10 ? '0' + date.getDate() : date.getDate();
        var hours = date.getHours() < 10 ? '0' + date.getHours() : date.getHours();
        var mins = date.getMinutes() < 10 ? '0' + date.getMinutes() : date.getMinutes();
        var timestamp = y + '-' + m + '-' + day + ' ' + hours + ':' + mins;

        var titleTags = tags.length > 25 ? 'title="' + tags.substr(0, 25) + '"' : '';
        var displayTags = tags.length > 25 ? tags.substr(0, 25) + '...' : tags;
        var titleTitle = title.length > 50 ? 'title="' + title.substr(0, 50) + '"' : '';
        var displayTitle = title.length > 50 ? title.substr(0, 50) + '...' : title;

        var item = document.createElement('tr');
        item.innerHTML = '<td><img src="https://www.google.com/s2/favicons?domain=' + url + '" alt="fav">' + '<a class="title" href="' + url + '">' + url + '</a></td>' + '<td' + titleTitle + '>' + displayTitle + '</td>' + '<td' + titleTags + '>' + displayTags + '</td>' + '<td>' + timestamp + '</td>' + '<td class="action"><button type="button" class="btn btn-default btn-sm delete-btn icon-button" data-id="' + id + '"><span class="glyphicon glyphicon-trash" aria-hidden="true"></span></button></td>';

        _table.insertBefore(item, _table.firstChild);

        var binder = document.querySelector('button[data-id="' + id + '"]');
        binder.onclick = _deleteItem;
    };

    _save.onclick = function (event) {
        event.preventDefault();

        var body = _serializeForm(_form);
        _save.innerHTML = 'Pushing...';
        _save.disabled = 'disabled';
        fetch('/api/items', {
            method: 'post',
            body: JSON.stringify(body),
            credentials: 'include'
        }).then(function (resp) {
            return resp.json();
        }).then(function (json) {
            console.log(json);
            _putRecent(body.url, body.title, body.tags, json.id);
            _url.value = '';
            _title.value = '';
            _tags.value = '';
            _save.disabled = null;
            _save.innerHTML = 'Push';
        })['catch'](function (err) {
            return console.error(err);
        });
    };

    console.info('set hooks');
    _url.onkeyup = _loadinfo;
    _url.onmouseout = _loadinfo;
    _url.onchange = _loadinfo;
    //_push.onclick = _pushNewItem;

    var buttons = document.querySelectorAll('.delete-btn');
    Array.prototype.forEach.call(buttons, function (element) {
        element.onclick = _deleteItem;
    });

    console.info('linkstack boot');
})();