@echo off
setlocal

rem 获取当前日期和时间
set datetime=%date% %time%
set datetime=%datetime: =_%

rem 定义输出文件
set outputFile=sorted_files.txt

rem 写入日期和时间到输出文件
echo Date and Time: %datetime% > %outputFile%

rem 获取当前目录下的所有文件并按名称排序，记录到输出文件
dir /b /a-d | sort >> %outputFile%

echo Sorted file list has been written to %outputFile%
endlocal
