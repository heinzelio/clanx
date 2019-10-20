
CREATE OR UPDATE DATABASE
=====
If you start with an empty database, just call on the shell the
following commands:
$ php .\bin\console doctrine:database:create
$ php .\bin\console doctrine:migrations:migrate

Create a new admin user by calling:
$ php .\bin\console fos:user:create

then give it the role "ROLE_SUPER_ADMIN":
php .\bin\console fos:user:promote


The process then goes like this:
- create a new field on your entity
- or create a new entity
- on shell, run
$ php ./bin/console doctrine:migrations:diff
- you get a new migrations file.
- run
$ php ./bin/console doctrine:migrations:migrate

If you checkout changes from git, first run on your shell
$ php ./bin/console doctrine:migrations:migrate


Migration scripts are stored in ./src/migrations

----------------------------------

LEGACY:
=====
The legacy scripts have been removed by version > 1.4.1
