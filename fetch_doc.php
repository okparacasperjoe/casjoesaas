<?php
$url = "https://teamapt.atlassian.net/wiki/api/v2/pages/1039826999?body-format=storage";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
$response = curl_exec($ch);
curl_close($ch);
$json = json_decode($response, true);
if(isset($json['body']['storage']['value'])) {
    $html = $json['body']['storage']['value'];
    // convert some common tags to text
    $html = str_replace(['<p>', '</p>'], ["", "\n\n"], $html);
    $html = str_replace(['<tr>', '</tr>', '<td>', '</td>'], ["", "\n", "", " | "], $html);
    $html = strip_tags($html);
    file_put_contents('moniepoint_api.txt', $html);
    echo "Saved to moniepoint_api.txt";
} else {
    echo "Error parsing JSON";
}
