<?php

$total = query('SELECT COUNT(*) FROM `phones`')->fetchColumn();

header('Content-Type: application/json');
echo json_encode(['total' => number_format($total)]);