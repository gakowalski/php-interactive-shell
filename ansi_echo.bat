@echo off
setlocal
set message=%2
set message=%message:$!= %
echo [%1%message%[0m
