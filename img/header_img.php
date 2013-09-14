<?php

header("Cache-Control: max-age=3600, private");

$img = "bente.jpg";

header("Status: 302");
header("Location: $img");
