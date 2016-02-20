/**
 * Created by victor on 7/20/15.
 */

var log = require('loglevel');

if (BUILD_DEV) log.setDefaultLevel(log.levels.TRACE);
else  log.setDefaultLevel(log.levels.SILENT);

var url = document.querySelector('#form_url');
var title = document.querySelector('#form_title');
var tags = document.querySelector('#form_tags');
var save = document.querySelector('#form_save');
var table = document.querySelector('#recentlist tbody');
var status = document.querySelector('#status');
var statusMsg = document.querySelector('#statusMsg');


const METASERVICE = 'https://tool.stack.snilius.com';
var fetchmutex = false;

var loadinfo = () => {
    if (fetchmutex) return;

    if (url.value.length < 5) return;

    if (!(title.value.length < 1 || tags.value.length < 1)) return;

    fetchmutex = true;
    var apiUrl = METASERVICE + '/info?url=' + url.value;
    fetch(apiUrl)
        .then(resp => resp.json(),
            err => {
                window.err = err;
                fetchmutex = false;
                log.debug('meta fetch fail');
                log.debug(err);
            })
        .then(ret => {
            if (ret === undefined) return;
            fetchmutex = false;

            if (title.value.length < 1) title.value = ret.title;

            if (tags.value.length < 1) tags.value = ret.meta;

            if (url.value !== ret.longurl) url.value = ret.longurl;
        });
};

var putRecent = function(url, title, tags, id) {
    const date = new Date();
    var y = date.getFullYear();
    var m = ((date.getMonth() + 1) < 10) ? '0' + (date.getMonth() + 1) : (date.getMonth() + 1);
    var day = (date.getDate() < 10) ? '0' + date.getDate() : date.getDate();
    var hours = (date.getHours() < 10) ? '0' + date.getHours() : date.getHours();
    var mins = (date.getMinutes() < 10) ? '0' + date.getMinutes() : date.getMinutes();
    var timestamp = y + '-' + m + '-' + day + ' ' + hours + ':' + mins;

    var titleTags = tags.length > 25 ? 'title="' + tags.substr(0, 25) + '"' : '';
    var displayTags = tags.length > 25 ? tags.substr(0, 25) + '...' : tags;
    var titleTitle = title.length > 50 ? 'title="' + title.substr(0, 50) + '"' : '';
    var displayTitle = title.length > 50 ? title.substr(0, 50) + '...' : title;

    const item = document.createElement('tr');
    item.innerHTML = '<td><a href="/stack/'+id+'">'+id+'</a></td>'+
        '<td><img src="https://www.google.com/s2/favicons?domain=' + url + '" alt="fav">' +
        '<a class="title" href="' + url + '">' + url + '</a></td>' +
        '<td' + titleTitle + '>' + displayTitle + '</td>' +
        '<td' + titleTags + '>' + displayTags + '</td>' +
        '<td>' + timestamp + '</td>' +
        '<td class="action"><button type="button" class="btn btn-default btn-sm delete-btn icon-button" data-id="' + id + '"><span class="glyphicon glyphicon-trash" aria-hidden="true"></span></button></td>';

    table.insertBefore(item, table.firstChild);

    bindDeletes();
};

save.onclick = event => {
    event.preventDefault();

    const body = {
        title: title.value,
        url: url.value,
        tags: tags.value
    };
    save.innerHTML = 'Pushing...';
    save.disabled = 'disabled';
    fetch('/api/items', {
        method: 'post',
        body: JSON.stringify(body),
        credentials: 'include'
    })
        .then(resp => resp.json())
        .then(json => {
            if ('message' in json) {
                statusMsg.innerHTML = json.message;
                status.style.display = 'block';
                save.disabled = null;
                save.innerHTML = 'Push';
            } else {
                log.info(json);
                putRecent(body.url, body.title, body.tags, json.id);
                url.value = '';
                title.value = '';
                tags.value = '';
                save.disabled = null;
                save.innerHTML = 'Push';
            }
        },err => log.error(err));
};

log.info('set hooks');
url.onkeyup = loadinfo;
url.onmouseout = loadinfo;
url.onchange = loadinfo;

const bindDeletes = () => {
    var buttons = document.querySelectorAll('.delete-btn');
    log.info('rebind deletes');
    Array.prototype.forEach.call(buttons, element => {
        element.onclick = () => {
            log.debug(element);
            var tempItem = element;
            var id = element.getAttribute('data-id');

            tempItem.classList.add('spinner');
            fetch('/api/items/' + id, {
                method: 'delete',
                credentials: 'include'
            }).then(resp => {
                if (resp.status === 204) tempItem.parentNode.parentNode.remove();

                log.info(resp);
            }, err => log.error(err));
        };
    });
};

bindDeletes();
