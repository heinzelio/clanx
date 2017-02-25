@ECHO OFF
SETLOCAL ENABLEEXTENSIONS
SET ME=%~n0
SET PARENTDIR=%~dp0
SET DB=clanx
SET USR=clanx
SET PWD=clanx
SET MYSQLEXE=C:\Bitnami\wamp\mysql\bin\mysql.exe
SET COMMAND=%MYSQLEXE% %DB% --user=%USR% --password=%PWD%

SET VERSIONSTATEMENT="SELECT version from info;"

%COMMAND% -e %VERSIONSTATEMENT%

SET OUTPUT=%PARENTDIR%out.tab

REM use arguments instead of prompts.
REM call it like 'runUpdate.bat 012upgrade.sql'
REM SET /p SQLFILE="Enter file name: "
SET INPUT=%PARENTDIR%%1

%COMMAND%<%INPUT%
%COMMAND% -e %VERSIONSTATEMENT%
