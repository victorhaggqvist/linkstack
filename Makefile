all: deps, docs, grunt-build

deps:
    npm install
    bower install
    composer update

docs:
	raml2html linkstack-api.raml > app/templates/linkstack-api.html

db-create:
	vagrant ssh -c "cd /vagrant && php vendor/bin/doctrine orm:schema-tool:create"

db-update:
	vagrant ssh -c "cd /vagrant && php vendor/bin/doctrine orm:schema-tool:update --force"

grunt-build:
    grunt build
