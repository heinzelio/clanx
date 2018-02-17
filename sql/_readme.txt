Beginning with 2018-02-17, we use Doctrine Migrations Bundle
for maintaining database version!

You need to run until update 18 on your existing database to create the
migrations table and
insert your state. After that, this scripts are not needed anymore.
However they remain here for documentation purposes.

If you start with an empty database, just call on the shell the
following commands:
$ php .\bin\console doctrine:database:create
$ php .\bin\console doctrine:migrations:migrate


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


----------------------------------
ARCHIVE
----------------------------------
Run a sql update or undo file:

[Win]+[R], cmd /K "cd C:\Users\chriglburri\Documents\dev\clanx.git\sql"

then, type "runUpdate filename.sql" (or "runUpdate.bat filename.sql")

The batch file will execute something like this:

SETLOCAL ENABLEEXTENSIONS
SET PARENTDIR=%~dp0
SET MYSQLEXE=C:\Bitnami\wamp\mysql\bin\mysql.exe
SET COMMAND=%MYSQLEXE% clanx --user=clanx -p
SET VERSIONSTATEMENT="SELECT version from info;"
%COMMAND% -e %VERSIONSTATEMENT%
SET OUTPUT=%PARENTDIR%out.tab
SET /p SQLFILE="Enter file name: "
SET INPUT=%PARENTDIR%%SQLFILE%
%COMMAND%<%INPUT%>%OUTPUT%
%COMMAND% -e %VERSIONSTATEMENT%

EDITPLUS
========
Configure UserTools:
Command: C:\Bitnami\wamp\mysql\bin\mysql.exe
Argument:  --login-path=local --table clanx < "$(FileDir)\$(FileName)"
(Take care of the leading space)
Capture Output: None
