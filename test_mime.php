<?php
require 'vendor/autoload.php';
$spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
$spreadsheet->getActiveSheet()->setCellValue('A1', 'aluno csv');
$writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
$writer->save('test_fake.csv');
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
$spreadsheet->getActiveSheet()->setCellValue('A1', 'aluno csv');
$writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
$writer->save('test_fake.csv'); // Real XLSX file, but named .csv

echo "Identified: " . \PhpOffice\PhpSpreadsheet\IOFactory::identify('test_fake.csv') . "\n";
