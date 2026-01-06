off
setlocal
enabledelayedexpansion
set
search=<div class=\
sidebar-logo-icon\>CM</div>
set
replace=<img src=\
{ asset('images/epss_logo.png') }
\ alt=\EPSS
Logo\ onerror=\this.src={{ asset(images/logo.png) }}\>
set
file=resources\views\dashboard\index.blade.php
for
/f
delims=
%%i
in
type "%file%"
do
move
/y
temp_file.txt
%file%
