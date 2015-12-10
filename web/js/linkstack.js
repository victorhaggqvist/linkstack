/**
 * Created by victor on 7/20/15.
 */

(function () {
    'use strict';

    var _form = document.querySelector('#pushform');
    var _url = document.querySelector('#form_url');
    var _title = document.querySelector('#form_title');
    var _tags = document.querySelector('#form_tags');
    var _save = document.querySelector('#form_save');
    var _table = document.querySelector('#recentlist tbody');
    var _status = document.querySelector('#status');
    var _statusMsg = document.querySelector('#statusMsg');

    var METASERVICE = 'https://secret-basin-9972.herokuapp.com';
    var _fetchmutex = false;

    var _loadinfo = function () {
        if (_fetchmutex) return;

        if (_url.value.length < 5) return;

        if (!(_title.value.length < 1 || _tags.value.length < 1)) return;

        _fetchmutex = true;
        var apiUrl = METASERVICE + '/info?url=' + _url.value;
        fetch(apiUrl).then(resp => resp.json(), err => console.error('meta fetch fail: ' + err)).then(ret => {
            if (ret === undefined) return;
            _fetchmutex = false;
            if (_title.value.length < 1) {
                _title.value = ret.title;
            }

            if (_tags.value.length < 1) {
                _tags.value = ret.meta;
            }

            if (_url.value !== ret.longurl) {
                _url.value = ret.longurl;
            }
        });
    };

    var _deleteItem = function () {
        console.log(this);
        var tempItem = this;
        var id = this.getAttribute('data-id');

        tempItem.className += ' spinner';
        fetch('/api/items/' + id, {
            method: 'delete',
            credentials: 'include'
        }).then(resp => {
            if (resp.status === 204) {
                tempItem.parentNode.parentNode.remove();
            }
            console.log(resp);
        }).catch(err => console.error(err));
    };

    var _putRecent = function (url, title, tags, id) {
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

        var body = {
            title: _title.value,
            url: _url.value,
            tags: _tags.value
        };
        _save.innerHTML = 'Pushing...';
        _save.disabled = 'disabled';
        fetch('/api/items', {
            method: 'post',
            body: JSON.stringify(body),
            credentials: 'include'
        }).then(resp => {
            return resp.json();
        }).then(json => {
            if ('message' in json) {
                _statusMsg.innerHTML = json.message;
                _status.style.display = 'block';
                _save.disabled = null;
                _save.innerHTML = 'Push';
            } else {
                console.log(json);
                _putRecent(body.url, body.title, body.tags, json.id);
                _url.value = '';
                _title.value = '';
                _tags.value = '';
                _save.disabled = null;
                _save.innerHTML = 'Push';
            }
        }).catch(err => console.error(err));
    };

    console.info('set hooks');
    _url.onkeyup = _loadinfo;
    _url.onmouseout = _loadinfo;
    _url.onchange = _loadinfo;
    //_push.onclick = _pushNewItem;

    var buttons = document.querySelectorAll('.delete-btn');
    Array.prototype.forEach.call(buttons, element => {
        element.onclick = _deleteItem;
    });

    console.info('linkstack boot');
})();