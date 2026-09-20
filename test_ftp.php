<?php
$conn = ftp_connect('ftp.casjoe.com');
ftp_login($conn, 'app@casjoe.com', 'app@casjoe.com');
ftp_pasv($conn, true);
$pub = '55acb03a4ff77aa26c05a1ab0ab5c7e661871743';

// Casjoe FTP utility















