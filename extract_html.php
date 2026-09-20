<?php
$data = json_decode(file_get_contents('moniepoint_api.json'), true);
file_put_contents('moniepoint_raw.html', $data['body']['storage']['value']);
