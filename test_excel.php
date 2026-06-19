<?php
require 'c:/laragon/www/sigae/vendor/autoload.php';

$app = require_once 'c:/laragon/www/sigae/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$csvData = "RA;Nome;Nascimento;Sexo;Telefone\n12345;Joao;15/05/2000;M;99999-9999";
file_put_contents('test.csv', $csvData);

use Illuminate\Http\UploadedFile;

$csvData = "RA;Nome;Nascimento;Sexo;Telefone\n12345;Joao;15/05/2000;M;99999-9999";
file_put_contents('test.csv', $csvData);

$file = new UploadedFile('test.csv', 'test.csv', 'text/csv', null, true);
$path = $file->store('temp');
echo "Stored path: " . $path . "\n";

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Create an empty xlsx file
$spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
$writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
$writer->save('test_fake.csv'); // saved as xlsx binary but named csv

try {
    $res = \Maatwebsite\Excel\Facades\Excel::toArray(new \stdClass, 'test_fake.csv', null, \Maatwebsite\Excel\Excel::CSV);
    var_dump($res[0][0][0]);
} catch (\Exception $e) {
    echo "Exception: " . $e->getMessage() . "\n";
}

