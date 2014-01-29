<?php
$url = $_GET['url'];

$ch = curl_init('http://json-pagetitle.appspot.com/?url='.$url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER , true);

$result = curl_exec($ch);

echo json_decode($result,true)['title'];
?>