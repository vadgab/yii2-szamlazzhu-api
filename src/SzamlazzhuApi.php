<?php

namespace vadgab\Yii2SzamlazzhuApi;


use vadgab\Yii2SzamlazzhuApi\Schema\InvoiceSchema;



class SzamlazzhuApi
{


    const URL_MAIN = "https://www.szamlazz.hu/szamla/";
    const TYPE = "1";





    public function createSzamla($schema){



        // Cookie File Full Acquisition on the server

        $cookie_file = __dir__."/../temp/szamlazz_cookie.txt";

        $dir = __dir__."/../temp/generateXml/".date('Y')."/".date('m')."/";
        if(!is_dir(__dir__."/../temp/generateXml/".date('Y')))mkdir(__dir__."/../temp/generateXml/".date('Y'));
        if(!is_dir(__dir__."/../temp/generateXml/".date('Y')."/".date('m')))mkdir(__dir__."/../temp/generateXml/".date('Y')."/".date('m'));
        $filename = date('YmdHis')."_".$schema->curlName."_".rand(1000,9999).".xml";
        $fullPathFilename = $dir.$filename;
        $fileSave = file_put_contents($fullPathFilename,$schema->schema);


        $xmlfile =  $schema->schema;



        // Saves pdf to this file if we requested in XML
        if($schema->pdfTagName)$pdfTagName = $schema->pdfTagName;
        $curlName = $schema->curlName;
        $outError = "";
        $szamlaszam = "";
        $agent_body = "";

        // If you ask the invoice pdf then be true
        $szamlaletoltes = true;
        // If you don't have-> Create your cookie file-> you have to exist for Curl to write in
        if (!file_exists($cookie_file)) {
            file_put_contents($cookie_file, '');
        }
        // Initialization of Curl
        $ch = curl_init(self::URL_MAIN);
        // You can get a certificate error from the Curl call from the SSL Certificate's Reality
        // regardless of this, this can be eliminated with the Curl parameter statement below,
        // There is no separate SSL check:
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        // We will send the data in post
        curl_setopt($ch, CURLOPT_POST, true);
        // Please http header in response, there are important information in it
        curl_setopt($ch, CURLOPT_HEADER, true);
        // Store the contents of the answer in a variable, do not write to the output
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        



        curl_setopt($ch, CURLOPT_POSTFIELDS, array($curlName=>new \CURLFile($fullPathFilename, 'application/xml', 'filenev')));


        // És még egy opciót szükséges ilyenkor beállítani:
        curl_setopt($ch, CURLOPT_SAFE_UPLOAD, true);
//        curl_setopt($ch, CURLOPT_POSTFIELDS, array('action-xmlagentxmlfile'=>'@' . $fullPathFilename));
        // 30 másodpercig tartjuk fenn a kapcsolatot (ha valami bökkenő volna)
        curl_setopt($ch, CURLOPT_TIMEOUT, 300);
        // Itt állítjuk be, hogy az érkező cookie a $cookie_file-ba kerüljön mentésre
        curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_file);
        // Ha van már cookie file-unk, és van is benne valami, elküldjük a Számlázz.hu-nak
        if (file_exists($cookie_file) && filesize($cookie_file) > 0) {
            curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_file);
        }

        // elküldjük a kérést a Számlázz.hu felé, és eltároljuk a választ
        $agent_response = curl_exec($ch);



        // kiolvassuk a curl-ból volt-e hiba
        $http_error = curl_error($ch);
        // ezekben a változókban tároljuk a szétbontott választ
        $agent_header = '';
        $agent_body = '';
        $agent_http_code = '';


        // lekérjük a válasz HTTP_CODE-ját, ami ha 200, akkor a http kommunikáció rendben volt
        // ettől még egyáltalán nem biztos, hogy a számla elkészült
        $agent_http_code = curl_getinfo($ch,CURLINFO_HTTP_CODE);

        // a válasz egy byte kupac, ebből az első "header_size" darab byte lesz a header
        $header_size = curl_getinfo($ch,CURLINFO_HEADER_SIZE);

        // a header tárolása, ebben lesznek majd a számlaszám, bruttó nettó összegek, errorcode, stb.
        $agent_header = substr($agent_response, 0, $header_size);

        // a body tárolása, ez lesz a pdf, vagy szöveges üzenet
        $agent_body = substr( $agent_response, $header_size );

        var_dump($agent_body);



        // Curl no longer needs to be closed
        curl_close($ch);

        // Header contains information per row, we put the separate lines in a block
        $header_array = explode("\n", $agent_header);

        // This will be set to TRUE if there was a mistake
        $volt_hiba = false;

        // There will be the error information plus in the Body
        $agent_error = '';
        $agent_error_code = '';

        if(!$volt_hiba && ($schema->curlName == "action-szamla_agent_xml" || $schema->curlName == "action-szamla_agent_taxpayer"))
        return simplexml_load_string(str_replace(["ns2:","ns3:","ns4:"],["","",""],$agent_body),'SimpleXMLElement', LIBXML_NOCDATA);





        // menjünk végig a header sorokon, ami "szlahu"-val kezdődik az érdekes nekünk és írjuk ki
        foreach ($header_array as $val) {
          if (substr($val, 0, strlen('szlahu')) === 'szlahu') {
//            echo urldecode($val).'<br>';

          if (strstr($val,'szlahu_szamlaszam')) {  // számlaszám kinyerése válasz üzenetből
            $szamlaszam_ = explode(":",$val);
            $szamlaszam = substr(str_replace(" ","",$szamlaszam_['1']),0,-1);
          }

          if (strstr($val,'szlahu_nettovegosszeg')) {  // NETTO kinyerése válasz üzenetből
            $netto_ = explode(":",$val);
            $netto = substr(str_replace(" ","",$netto_['1']),0,-1);
          }

          if (strstr($val,'szlahu_bruttovegosszeg')) {  // BRUTTO kinyerése válasz üzenetből
            $brutto_ = explode(":",$val);
            $brutto = substr(str_replace(" ","",$brutto_['1']),0,-1);
          }

          if (strstr($val,'szlahu_vevoifiokurl')) {  // BRUTTO kinyerése válasz üzenetből
            $vevourl_ = explode(":",$val);
            $vevourl = substr(str_replace(" ","",$vevourl_['1']),0,-1);
          }


            // megvizsgáljuk, hogy volt-e hiba
            if (substr($val, 0, strlen('szlahu_error:')) === 'szlahu_error:') {
              // sajnos volt
              $volt_hiba = true;
              $agent_error = substr($val, strlen('szlahu_error:'));
            }
            if (substr($val, 0, strlen('szlahu_error_code:')) === 'szlahu_error_code:') {
              // sajnos volt
              $volt_hiba = true;
              $agent_error_code = substr($val, strlen('szlahu_error_code:'));
            }
          }
        }


        // ha volt http hiba dobunk egy kivételt
        if ( $http_error != "" )
        {
          echo 'Http hiba történt:'.$http_error;
  //        throw new Exception('Hiba történt:'.$http_error);
        }



        if ($volt_hiba) {

          // ha a számla nem készült el kiírjuk amit lehet
          $outError = 'Agent hibakód: '.$agent_error_code.'<br>';
          $outError = $outError.  'Agent hibaüzenet: '.urldecode($agent_error).'<br>';
//          $outError = $outError. 'Agent válasz: '.urldecode($agent_body).'<br>';

          // dobunk egy kivételt
//          throw new Exception('Számlakészítés sikertelen:'.$agent_error_code);
            $out['error'] = $outError;

            return $out;


        } else {

          // if there was no error processing the PDF or text information received in the reply

            // if we didn't ask for pdf then text information came in the answer, we'll write this

            if($schema->type == 13){

              $out['agent_body'] = $agent_body;

            }else{
              $out['error'] = $outError;
              $out['szamlaszam'] = $szamlaszam;
              if(!empty($vevourl))$out['url'] = urldecode($vevourl);
              $out['agent_body'] = $agent_body;
            }
            

          return $out;
        }
    }

    public static function stripInvalidXml($value)
    {
        $ret = "";
        $current;
        if (empty($value))
        {
            return $ret;
        }

        $length = strlen($value);
        for ($i=0; $i < $length; $i++)
        {
            $current = ord($value[$i]);
            if (($current == 0x9) ||
                ($current == 0xA) ||
                ($current == 0xD) ||
                (($current >= 0x20) && ($current <= 0xD7FF)) ||
                (($current >= 0xE000) && ($current <= 0xFFFD)) ||
                (($current >= 0x10000) && ($current <= 0x10FFFF)))
            {
                $ret .= chr($current);
            }
            else
            {
                $ret .= " ";
            }
        }
        return $ret;
    }
 







}
?>