<?php

require_once("./vendor/autoload.php");

use vadgab\Yii2SzamlazzhuApi\SzamlazzhuApi;
use vadgab\Yii2SzamlazzhuApi\Schema\InvoiceSchema;      
 
$invoiceCreate = new SzamlazzhuApi;

$schema = new InvoiceSchema;

$xml = $schema->InvoiceGenerateXml();


