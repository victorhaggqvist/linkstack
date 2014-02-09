API DOC
=======

#Parameters
| Paramater | Req. | Description |
|:----------|:-----|:------------|
| token | No | user token, obtained by login |
| method | Yes | method to be used |
| id | Yes | Id of stack item |

#Methods
##login
| Paramater | Req. | Description |
|:----------|:-----|:------------|
| provider | No | OpenID provider to login with, fallback to Google |


###Result
User token
```
77d08c22ed3ce5a8101c6cd176010184266b5dd074cc200786dabbfa2c5175fd
```

##ping
No parameters

###Result
```json
{
  "status": "ok",
  "ping": 1
}
```

##new
| Paramater | Req. | Description |
|:----------|:-----|:------------|
| url | Yes | Url to be stacked |
| title | No | Title |
| tags | No | Tags |

###Result

##list
| Paramater | Req. | Description |
|:----------|:-----|:------------|
| page | No | Page to get, default 1 aka latest items |
| query | No | Query string |
| tags | No | Wether query should be run on tags only |

###Result
```json
{
  "success": "1",
  "list": [{
    "id": "55",
    "url": "https:\/\/github.com\/SublimeLinter\/SublimeLinter-php",
    "title": "SublimeLinter\/SublimeLinter-php \u00b7 GitHub",
    "timestamp": "2014-01-26 16:42:15",
    "tags": "awesomeness",
    "user_id": "2"
  },
  {
  ..
  }]
}
```
