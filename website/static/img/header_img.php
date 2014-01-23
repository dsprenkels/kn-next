<?php

header('Cache-Control: max-age=3600, public');
header('Content-Type: image/jpeg');

$imgs = glob('headers/*.jpg');
$rnd = mt_rand(0, count($imgs) - 1);

readfile($imgs[$rnd]);

?>
