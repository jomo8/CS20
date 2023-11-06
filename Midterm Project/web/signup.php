<?php

require_once __DIR__ . '/src/setup.php';

$page = new EasyReader\Pages\SignUpPage();

echo $page->getOutput();