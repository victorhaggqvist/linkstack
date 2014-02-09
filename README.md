Link Stack
==========
A place to stack your links, instead of having them flood your bookmarks. View live and try it out at [http://stack.snilius.com](http://stack.snilius.com). Until further notice you can even continue to use my hosted version if you like. Just log in with Google, do notice that I will collect your e-mail address if you login. This is just so that I can send you a e-mail if things changes.

- [Changelog](./CHANGELOG.md)
- [Todo](./TODO.md)

#Install
To install Link Stack your self either clone or [download](https://github.com/victorhaggqvist/linkstack/archive/master.zip) the repo.

Then do as following:

- Import the sql dump `stack.sql` to your MySQL database.
- Edit the config file `core/config.inc` with appropriate database credentials
- Edit the `OPENID_CALLBACK` constant to repsesent the path where your install is located.
