<p align="center">
    <a href="https://github.com/yiisoft" target="_blank">
        <img src="https://avatars0.githubusercontent.com/u/993323" height="100px">
    </a>
    <h1 align="center">Yii2 Szamlazz.hu Api Extension</h1>
    <br>
</p>




Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/):

```
composer require --prefer-dist vadgab/yii2-szamlazzhu-api
```

Basic Usage
-----------

General use can be tried through the following examples:

- Create Invoice

	```php
        $invoiceCreate = new SzamlazzhuApi;
        $schema = new InvoiceSchema;
	
        $schema->type = 1;  // invoice set payed
		/****** Invoice types this method 
		* 5 - Pre Invoice
		* 6 - Final Invoice
		* 7 - Corrective Invoice 
		* 8 - Pro forma (díjbekérő)
		* 9 - Delivery Invoice
		*******/
	
        $schema->defineInvoiceType();
	
        /* adding settings value */
	
        $schema->settings['eszamla'] = "false";
        $schema->settings['szamlaLetoltes'] = "true";
        $schema->settings['szamlaLetoltesPld'] = "1";
        $schema->settings['valaszVerzio'] = "1";
        $schema->settings['aggregator'] = "";
	
        /* adding header value */
	
        $schema->header['keltDatum'] = date('Y-m-d');
        $schema->header['teljesitesDatum'] = date('Y-m-d');
        $schema->header['fizetesiHataridoDatum'] = date('Y-m-d',mktime(0,0,0,date('m'),date('d')+8,date('Y')));
        $schema->header['fizmod'] = "Átutalás";
        $schema->header['penznem'] = "Ft";
        $schema->header['szamlaNyelve'] = "hu";
        $schema->header['megjegyzes'] = "Számla teszt megjegyzés ";
        $schema->header['arfolyamBank'] = "MNB";
        $schema->header['arfolyam'] = "1";
        $schema->header['fizetve'] = "false";
	
        /* adding seller value */
	
        $schema->seller['bank'] = "";
        $schema->seller['bankszamlaszam'] = "11111111-22222222-33333333";
        $schema->seller['emailReplyto'] = "info@test.com";
        $schema->seller['emailTargy'] = "Teszt tárgy";
        $schema->seller['emailSzoveg'] = "Teszt szöveg";
        $schema->seller['alairoNeve'] = "Eladó aláírója";
	
        /* adding buyer value */
	
        $schema->buyer['nev'] = "Teszt cég minta";
        $schema->buyer['orszag'] = "Magyarország";
        $schema->buyer['irsz'] = "1156";
        $schema->buyer['telepules'] = "Budapest";
        $schema->buyer['cim'] = "Drégelyvár utca 6";
        $schema->buyer['email'] = "";
        $schema->buyer['sendEmail'] = "false";
        $schema->buyer['adoszam'] = "11111111-42-1";
        $schema->buyer['adoszamEU'] = "HU11111111";
        $schema->buyer['postazasiNev'] = "";
        $schema->buyer['postazasiIrsz'] = "";
        $schema->buyer['postazasiTelepules'] = "";
        $schema->buyer['postazasiCim'] = "";
        $schema->buyer['alairoNeve'] = "Vevő aláírója";
        $schema->buyer['megjegyzes'] = "Teszt megjegyzés 2";
	
        /* adding items 1 value */
	
        $schema->items['megnevezes'] = "Teszt termék 1";
        $schema->items['mennyiseg'] = "1";
        $schema->items['mennyisegiEgyseg'] = "2000";
        $schema->items['afakulcs'] = "AAM";
        $schema->items['nettoEgysegar'] = "2000";
        $schema->items['nettoErtek'] = "2000";
        $schema->items['bruttoErtek'] = "2000";
        $schema->items['megjegyzes'] = "";
        $schema->InvoiceAddItems();
	
        /* adding items 2 value */
	
        $schema->items['megnevezes'] = "Teszt termék 2";
        $schema->items['mennyiseg'] = "1";
        $schema->items['mennyisegiEgyseg'] = "1000";
        $schema->items['afakulcs'] = "AAM";
        $schema->items['nettoEgysegar'] = "1000";
        $schema->items['nettoErtek'] = "1000";
        $schema->items['bruttoErtek'] = "1000";
        $schema->items['megjegyzes'] = "";
        $schema->InvoiceAddItems();
	
        /* Generate XML */
	
        $xml = $schema->InvoiceGenerateXml();
	
        /* Invoice payed send and process */
	
        $out = $invoiceCreate->createSzamla($schema);
	
        var_dump($out);
		/***** output 
		* $out['error']
		* $out['szamlaszam']
		* $out['agent_body'] //PDF
		*****/


- Add Invoice payed

  ```php
          $invoiceCreate = new SzamlazzhuApi;
          $schema = new InvoiceSchema;
  
          $schema->type = 2;  // invoice set payed
  
          $schema->defineInvoiceType();
  
          $schema->settings['szamlaszam'] = '78987-2022-326';
          $schema->settings['additiv'] = 'false';
  
          $schema->payed['datum'] = '2022-11-16';
          $schema->payed['jogcim'] = 'This invoice is payed ';
          $schema->payed['osszeg'] = '2000';
          $schema->InvoiceAddPayed();
          $schema->payed['datum'] = '2022-11-16';
          $schema->payed['jogcim'] = 'This invoice is payed ';
          $schema->payed['osszeg'] = '2000';
          $schema->InvoiceAddPayed();
          /* Generate XML */
  
          $xml = $schema->InvoicePayedGenerateXml();
  
          /* Invoice payed send and process */
  
          $out = $invoiceCreate->createSzamla($schema);
  
          var_dump($out);
  ```


- Create Storno invoice

  ```php
          $invoiceCreate = new SzamlazzhuApi;
          $schema = new InvoiceSchema;
  
          $schema->type = 3;  // Create strono invoice 
  
          $schema->defineInvoiceType();
  
          /* adding settings value */
  
          $schema->settings['eszamla'] = "false";
          $schema->settings['szamlaLetoltes'] = "true";
          $schema->settings['szamlaLetoltesPld'] = "1";
          $schema->settings['aggregator'] = "";
  
          /* adding header value */
  
          $schema->header['keltDatum'] = date('Y-m-d');
          $schema->header['teljesitesDatum'] = date('Y-m-d');
          $schema->header['szamlaszam'] = "78987-2022-328";
  
          /* adding seller value */
  
          $schema->seller['emailReplyto'] = "info@test.com";
          $schema->seller['emailTargy'] = "Teszt tárgy";
          $schema->seller['emailSzoveg'] = "Teszt szöveg";
  
          /* adding buyer value */
  
          $schema->buyer['email'] = "teszt@teszt.com";
  
          /* Generate XML */
  
          $xml = $schema->InvoiceStornoGenerateXml();
  
          /* Invoice payed send and process */
  
          $out = $invoiceCreate->createSzamla($schema);
  
          var_dump($out);
          /***** output 
  		* $out['error']
  		* $out['szamlaszam']
  		* $out['agent_body'] //PDF
  		*****/
  ```

- Create delete Pro forma 

  ```php
          $invoiceCreate = new SzamlazzhuApi;
          $schema = new InvoiceSchema;
  
          $schema->type = 10;  // invoice set delete Pro Forma
  
          $schema->defineInvoiceType();
  
          /* adding header value */
  
          $schema->header['szamlaszam'] = "D-78987-277";
  //        $schema->header['rendelesszam'] = "";
  
          /* Generate XML */
  
          $xml = $schema->InvoiceProFormaDeleteGenerateXml();
  
          /* Invoice payed send and process */
  
          $out = $invoiceCreate->createSzamla($schema);
  
  ```

- Get invoice data

  ```php
  $invoiceCreate = new SzamlazzhuApi;
  $schema = new InvoiceSchema;
  
  $schema->type = 11;  // invoice get invoice data
  
  $schema->defineInvoiceType();
  
  /* adding header value */
  
  $schema->header['szamlaszam'] = "E-78987-2017-536";
  //        $schema->header['rendelesszam'] = "";
  $schema->header['pdf'] = "true";  // output base64 encoded
  
  /* Generate XML */
  
  $xml = $schema->InvoiceGetDataGenerateXml();
  
  /* Invoice payed send and process */
  
  $out = $invoiceCreate->createSzamla($schema);
  
  var_dump($out);
  ```

