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