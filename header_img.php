<?php

//header("Cache-Control: must-revalidate");

header("Cache-Control: max-age=3600, private");

$img = "bente_u.jpg";

header("Status: 302");
header("Location: $img");