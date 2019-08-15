<?php

if(empty($_POST)) {
  return;
}

$numbersList = trim($_POST['numbers_list']);
$numbersList = str_replace("\r\n", ',', $numbersList);
$numbersList = str_replace("\r", ',', $numbersList);
$numbersList = str_replace("\n", ',', $numbersList);
$numbersList = str_replace('(', '', $numbersList);
$numbersList = str_replace(')', '', $numbersList);

$numbersList = explode(',', $numbersList);
$numbersList = array_unique($numbersList);

$cleanNumbersList = array();
foreach ($numbersList as $number) {
  $cleanNumber = trim($number);
  $cleanNumber = substr($cleanNumber, 0, 3);
  $cleanNumber = preg_replace('/[^0-9]/', '', $cleanNumber);
  if (empty($cleanNumber)) {
    continue;
  }
  $cleanNumbersList[] = $cleanNumber;
}

if (empty($cleanNumbersList)) {
  return;
}

$sql = 'SELECT COUNT(`number`)
      FROM `phones`
      WHERE `areacode` IN ('.implode(',', $cleanNumbersList).')';

$foundTotal = (int)query($sql)->fetchColumn();
