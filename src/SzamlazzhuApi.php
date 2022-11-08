<?php

namespace vadgab\Yii2SzamlazzhuApi;






class SzamlazzhuApi
{

    const URL_MAIN = "https://www.szamlazz.hu/szamla/";
    const TYPE = "1";
    public $type = 1;
    public $pdfTagNameValue = null;






    public static function createSzamla(){


        $session = new Session;
        // cookie file teljes elérési útja a szerveren
        $cookie_file = __dir__."/temp/szamlazz_cookie.txt";
        // ebbe a fájlba menti a pdf-et, ha az xml-ben kértük

        // ezt az xml fájlt küldi a számla agentnek
        if($this->type==1){
            $adatok['fejlec']['elolegszamla'] = 'false';
            $adatok['fejlec']['vegszamla'] = 'false';
            $adatok['fejlec']['helyesbitoszamla'] = 'false';
            $adatok['fejlec']['dijbekero'] = 'false';
            $adatok['fejlec']['szallitolevel'] = 'false';
            $xmlfile = self::SzamlaGenerateXml($adatok);
            $curlName = "action-xmlagentxmlfile";
            $pdfTagName = "szamla";

        }
        if($this->type==2){
            $xmlfile = self::SzamlaKifGenerateXml($adatok);
            $curlName = "action-szamla_agent_kifiz";
            $pdfTagName = "szamlakifiz";
        }
        if($this->type==3){
            $xmlfile = self::SzamlaSztornoGenerateXml($adatok);
            $curlName = "action-szamla_agent_st";
            $pdfTagName = "szamlast";
        }
        if($this->type==4){
//            $xmlfile = self::SzamlaSztornoGenerateXml($adatok);
            $curlName = "action-szamla_agent_pdf";
            $pdfTagName = "szamlapdf";
        }

        if($this->type==5){  ////////////////////////////////////ELÕLEG SZÁMLA
            $adatok['fejlec']['elolegszamla'] = 'true';
            $adatok['fejlec']['vegszamla'] = 'false';
            $adatok['fejlec']['helyesbitoszamla'] = 'false';
            $adatok['fejlec']['dijbekero'] = 'false';
            $adatok['fejlec']['szallitolevel'] = 'false';
            $xmlfile = self::SzamlaGenerateXml($adatok);
            $curlName = "action-xmlagentxmlfile";
            $pdfTagName = "szamla";
        }

        if($this->type==6){  ////////////////////////////////////VÉG SZÁMLA
            $adatok['fejlec']['elolegszamla'] = 'false';
            $adatok['fejlec']['vegszamla'] = 'true';
            $adatok['fejlec']['helyesbitoszamla'] = 'false';
            $adatok['fejlec']['dijbekero'] = 'false';
            $adatok['fejlec']['szallitolevel'] = 'false';
            $xmlfile = self::SzamlaGenerateXml($adatok);
            $curlName = "action-xmlagentxmlfile";
            $pdfTagName = "szamla";
        }

        if($this->type==7){   ////////////////////////////////////HELYESBÍTÕ SZÁMLA
            $adatok['fejlec']['elolegszamla'] = 'false';
            $adatok['fejlec']['vegszamla'] = 'false';
            $adatok['fejlec']['helyesbitoszamla'] = 'true';
            $adatok['fejlec']['dijbekero'] = 'false';
            $adatok['fejlec']['szallitolevel'] = 'false';
            $xmlfile = self::SzamlaGenerateXml($adatok);
            $curlName = "action-xmlagentxmlfile";
            $pdfTagName = "szamla";
        }

        if($this->type==8){    ////////////////////////////////////DÍJBEKÉRÕ SZÁMLA
            $adatok['fejlec']['elolegszamla'] = 'false';
            $adatok['fejlec']['vegszamla'] = 'false';
            $adatok['fejlec']['helyesbitoszamla'] = 'false';
            $adatok['fejlec']['dijbekero'] = 'true';
            $adatok['fejlec']['szallitolevel'] = 'false';
            $xmlfile = self::SzamlaGenerateXml($adatok);
            $curlName = "action-xmlagentxmlfile";
            $pdfTagName = "szamla";
        }

        if($this->type==9){      ////////////////////////////////////SZÁLLÍTÓ LEVÉL
            $adatok['fejlec']['elolegszamla'] = 'false';
            $adatok['fejlec']['vegszamla'] = 'false';
            $adatok['fejlec']['helyesbitoszamla'] = 'false';
            $adatok['fejlec']['dijbekero'] = 'false';
            $adatok['fejlec']['szallitolevel'] = 'true';
            $xmlfile = self::SzamlaGenerateXml($adatok);
            $curlName = "action-xmlagentxmlfile";
            $pdfTagName = "szallito";
        }

        if($this->type==10){
            $xmlfile = self::SzamlaDijDelGenerateXml($adatok);
            $curlName = "action-szamla_agent_dijbekero_torlese";
            $pdfTagName = "szamlaDijDel";
        }




        if($this->pdfTagNameValue)$pdfTagName = $this->pdfTagNameValue;


        // ha kérjük a számla pdf-et, akkor legyen true
        $szamlaletoltes = true;
        // ha még nincs --> létrehozzuk a cookie file-t --> léteznie kell, hogy a CURL írhasson bele
        if (!file_exists($cookie_file)) {
            file_put_contents($cookie_file, '');
        }
        // a CURL inicializálása
        $ch = curl_init(URL_MAIN);
        // A curl hívás esetén tanúsítványhibát kaphatunk az SSL tanúsítvány valódiságától
        // függetlenül, ez az alábbi CURL paraméter állítással kiküszöbölhetõ,
        // ilyenkor nincs külön SSL ellenõrzés:
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        // POST-ban küldjük az adatokat
        curl_setopt($ch, CURLOPT_POST, true);
        // Kérjük a HTTP headert a válaszba, fontos információk vannak benne
        curl_setopt($ch, CURLOPT_HEADER, true);
        // változóban tároljuk a válasz tartalmát, nem írjuk a kimenetbe
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        // Kb így néz ki CURLFile használatával:
            curl_setopt($ch, CURLOPT_POSTFIELDS, array($curlName=>new CURLFile($xmlfile, 'application/xml', 'filenev')));
        // És még egy opciót szükséges ilyenkor beállítani:
            curl_setopt($ch, CURLOPT_SAFE_UPLOAD, true);
//        curl_setopt($ch, CURLOPT_POSTFIELDS, array('action-xmlagentxmlfile'=>'@' . $xmlfile));
        // 30 másodpercig tartjuk fenn a kapcsolatot (ha valami bökkenõ volna)
        curl_setopt($ch, CURLOPT_TIMEOUT, 300);
        // Itt állítjuk be, hogy az érkezõ cookie a $cookie_file-ba kerüljön mentésre
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
        // ettõl még egyáltalán nem biztos, hogy a számla elkészült
        $agent_http_code = curl_getinfo($ch,CURLINFO_HTTP_CODE);

        // a válasz egy byte kupac, ebbõl az elsõ "header_size" darab byte lesz a header
        $header_size = curl_getinfo($ch,CURLINFO_HEADER_SIZE);

        // a header tárolása, ebben lesznek majd a számlaszám, bruttó nettó összegek, errorcode, stb.
        $agent_header = substr($agent_response, 0, $header_size);

        // a body tárolása, ez lesz a pdf, vagy szöveges üzenet
        $agent_body = substr( $agent_response, $header_size );

        // a curl már nem kell, lezárjuk
        curl_close($ch);

        // a header soronként tartalmazza az információkat, egy tömbbe teszük a külön sorokat
        $header_array = explode("\n", $agent_header);

        // ezt majd true-ra állítjuk ha volt hiba
        $volt_hiba = false;

        // ebben lesznek a hiba információk, plusz a bodyban
        $agent_error = '';
        $agent_error_code = '';



        // menjünk végig a header sorokon, ami "szlahu"-val kezdõdik az érdekes nekünk és írjuk ki
        foreach ($header_array as $val) {
          if (substr($val, 0, strlen('szlahu')) === 'szlahu') {
//            echo urldecode($val).'<br>';

          if (strstr($val,'szlahu_szamlaszam')) {  // számlaszám kinyerése válasz üzenetbõl
            $szamlaszam_ = explode(":",$val);
            $szamlaszam = substr(str_replace(" ","",$szamlaszam_['1']),0,-1);
          }

          if (strstr($val,'szlahu_nettovegosszeg')) {  // NETTO kinyerése válasz üzenetbõl
            $netto_ = explode(":",$val);
            $netto = substr(str_replace(" ","",$netto_['1']),0,-1);
          }

          if (strstr($val,'szlahu_bruttovegosszeg')) {  // BRUTTO kinyerése válasz üzenetbõl
            $brutto_ = explode(":",$val);
            $brutto = substr(str_replace(" ","",$brutto_['1']),0,-1);
          }

          if (strstr($val,'szlahu_vevoifiokurl')) {  // BRUTTO kinyerése válasz üzenetbõl
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

          // ha nem volt hiba feldolgozzuk a válaszban érkezett pdf-et vagy szöveges információt

            // ha nem kértük a pdf-et akkor szöveges információ jött a válaszban, ezt kiírjuk

            if($adatok['fejlec']['fizetve']=='true')$adatok['fejlec']['fizetve'] = 1;
            if($adatok['fejlec']['fizetve']=='false')$adatok['fejlec']['fizetve'] = 0;


            $out['error'] = $outError;
            $out['szamlaszam'] = $szamlaszam;
            $out['agent_body'] = $agent_body;

          return $out;
        }
    }

public static function SzamlaKifGenerateXml($adatok){
        $genXmlOut = '<?xml version="1.0" encoding="UTF-8"?>
        <xmlszamlakifiz xmlns="http://www.szamlazz.hu/xmlszamlakifiz" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.szamlazz.hu/xmlszamlakifiz xmlszamlakifiz.xsd ">
          <beallitasok>
            <felhasznalo>'.Yii::$app->params['szamlazzhu']['felhasznalonev'].'</felhasznalo>
            <jelszo>'.Yii::$app->params['szamlazzhu']['jelszo'].'</jelszo>
            <szamlaszam>'.$adatok['beallitasok']['szamlaszam'].'</szamlaszam>
            <additiv>'.$adatok['beallitasok']['additiv'].'</additiv>
            <aggregator>'.$adatok['beallitasok']['aggregator'].'</aggregator>
          </beallitasok>
          <kifizetes>
            <datum>'.$adatok['kifizetes']['datum'].'</datum>
            <jogcim>'.$adatok['kifizetes']['jogcim'].'</jogcim>
            <osszeg>'.$adatok['kifizetes']['osszeg'].'</osszeg>
          </kifizetes>';
          $genXmlOut = $genXmlOut.'
        </xmlszamlakifiz>';

                $dir = __dir__."/../uploads/generateXml/".date(Y)."/".date(m)."/";
                $filename = date(YmdHis)."_szamlakif_".rand(1000,9999).".xml";
                MyFunctions::DirCheckCreateYearMonth("generateXml");  /// ellenõrzi létezik az adott upload könyvtárban az év és a hónap amibe majd írja az xml-t
                $handle = fopen($dir.$filename,"a");
                $xmlWrite = fwrite($handle,$genXmlOut);
                fclose($handle);
                if($xmlWrite)return $dir.$filename;

            }

           public static function SzamlaDijDelGenerateXml($adatok){
        $genXmlOut = '<?xml version="1.0" encoding="UTF-8"?>
        <xmlszamladbkdel xmlns="http://www.szamlazz.hu/xmlszamladbkdel" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.szamlazz.hu/xmlszamladbkdel xmlszamladbkdel.xsd ">
            <beallitasok>
                <felhasznalo>'.Yii::$app->params['szamlazzhu']['felhasznalonev'].'</felhasznalo>
                <jelszo>'.Yii::$app->params['szamlazzhu']['jelszo'].'</jelszo>
            </beallitasok>
            <fejlec>
                <szamlaszam>'.$adatok['fejlec']['szamlaszam'].'</szamlaszam>
                <rendelesszam>'.$adatok['fejlec']['rendelesszam'].'</rendelesszam>
            </fejlec>
        </xmlszamladbkdel>';

                $dir = __dir__."/../uploads/generateXml/".date(Y)."/".date(m)."/";
                $filename = date(YmdHis)."_szamlakif_".rand(1000,9999).".xml";
                MyFunctions::DirCheckCreateYearMonth("generateXml");  /// ellenõrzi létezik az adott upload könyvtárban az év és a hónap amibe majd írja az xml-t
                $handle = fopen($dir.$filename,"a");
                $xmlWrite = fwrite($handle,$genXmlOut);
                fclose($handle);
                if($xmlWrite)return $dir.$filename;

            }


            public static function SzamlaSztornoGenerateXml($adatok){
        $genXmlOut = '<?xml version="1.0" encoding="UTF-8"?>
        <xmlszamlast xmlns="http://www.szamlazz.hu/xmlszamlast" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.szamlazz.hu/xmlszamlast xmlszamlast.xsd ">
          <beallitasok>
            <felhasznalo>'.Yii::$app->params['szamlazzhu']['felhasznalonev'].'</felhasznalo>
            <jelszo>'.Yii::$app->params['szamlazzhu']['jelszo'].'</jelszo>
            <eszamla>'.$adatok['beallitasok']['eszamla'].'</eszamla>
            <kulcstartojelszo>'.$adatok['beallitasok']['kulcstartojelszo'].'</kulcstartojelszo>
            <szamlaLetoltes>'.$adatok['beallitasok']['szamlaLetoltes'].'</szamlaLetoltes>
            <szamlaLetoltesPld>'.$adatok['beallitasok']['szamlaLetoltesPld'].'</szamlaLetoltesPld>
            <aggregator>'.$adatok['beallitasok']['aggregator'].'</aggregator>
          </beallitasok>
          <fejlec>
            <szamlaszam>'.$adatok['fejlec']['szamlaszam'].'</szamlaszam>
            <keltDatum>'.$adatok['fejlec']['keltDatum'].'</keltDatum>
            <teljesitesDatum>'.$adatok['fejlec']['teljesitesDatum'].'</teljesitesDatum>
            <tipus>SS</tipus>
          </fejlec>
          <elado>
            <emailReplyto>'.$adatok['elado']['emailReplyto'].'</emailReplyto>
            <emailTargy>'.$adatok['elado']['emailTargy'].'</emailTargy>
            <emailSzoveg>
        '.$adatok['elado']['emailSzoveg'].'    </emailSzoveg>
          </elado>
          <vevo>
            <email>'.$adatok['vevo']['email'].'</email>
          </vevo>
        </xmlszamlast>';

        $dir = __dir__."/../uploads/generateXml/".date(Y)."/".date(m)."/";
        $filename = date(YmdHis)."_szamlast_".rand(1000,9999).".xml";
        MyFunctions::DirCheckCreateYearMonth("generateXml");  /// ellenõrzi létezik az adott upload könyvtárban az év és a hónap amibe majd írja az xml-t
        $handle = fopen($dir.$filename,"a");
        $xmlWrite = fwrite($handle,$genXmlOut);
        fclose($handle);
        if($xmlWrite)return $dir.$filename;

    }

    public static function stornoSzamlaSzamlaszam($szamlaszam){

                $Adatoksztorno =
                ['beallitasok'=>
                        ['felhasznalo'=>Yii::$app->params["szamlazzhu"]["felhasznalonev"],
                        'jelszo'=>Yii::$app->params["szamlazzhu"]["jelszo"],
                        'eszamla'=>0,
                        'kulcstartojelszo'=>'',
                        'szamlaLetoltes'=>'true',
                        'szamlaLetoltesPld'=>'1',
                        'aggregator'=>''],
                    'fejlec'=>
                        ['keltDatum'=>date('Y-m-d'),
                        'teljesitesDatum'=>date('Y-m-d'),
                        'szamlaszam'=>$szamlaszam,
                        'tipus'=>'SS'
                        ],
                    'elado'=>
                        ['emailReplyto'=>'',
                        'emailTargy'=>'',
                        'emailSzoveg'=>''],
                    'vevo'=>
                        ['email'=>'',
                        ],
                        ];
                        $szamlaKeszites = Szamlazzhu::createSzamla(3,$Adatoksztorno);
                        return $szamlaKeszites;
    }




    public static function stornoSzamla($id){

        $szamla = SzlKimeno::findOne($id);

        if($szamla->elektronikus == 1)$eszamla = 1;
        else $eszamla = 0;

        $Adatoksztorno =
        ['beallitasok'=>
            ['felhasznalo'=>Yii::$app->params["szamlazzhu"]["felhasznalonev"],
            'jelszo'=>Yii::$app->params["szamlazzhu"]["jelszo"],
            'eszamla'=>$eszamla,
            'kulcstartojelszo'=>'',
            'szamlaLetoltes'=>'true',
            'szamlaLetoltesPld'=>'1',
            'aggregator'=>''],
          'fejlec'=>
            ['keltDatum'=>date('Y-m-d'),
            'teljesitesDatum'=>substr($szamla->teljesites,0,10),
            'szamlaszam'=>$szamla->szamlaszam,
            'tipus'=>'SS'
            ],
          'elado'=>
            ['emailReplyto'=>'',
            'emailTargy'=>'',
            'emailSzoveg'=>''],
          'vevo'=>
            ['email'=>'',
            ],
            ];
            $szamlaKeszites = Szamlazzhu::createSzamla(3,$Adatoksztorno);

        return $szamlaKeszites['szamlaszam'];

    }

    public static function stornoSzallito($id){

            $szamla = SzlKimenoszallito::findOne($id);

            $Adatoksztorno =
            ['beallitasok'=>
                ['felhasznalo'=>Yii::$app->params["szamlazzhu"]["felhasznalonev"],
                'jelszo'=>Yii::$app->params["szamlazzhu"]["jelszo"],
                'eszamla'=>'false',
                'kulcstartojelszo'=>'',
                'szamlaLetoltes'=>'true',
                'szamlaLetoltesPld'=>'1',
                'aggregator'=>''],
              'fejlec'=>
                ['keltDatum'=>date('Y-m-d'),
                'teljesitesDatum'=>substr($szamla->teljesites,0,10),
                'szamlaszam'=>$szamla->szamlaszam,
                'tipus'=>'SS'
                ],
              'elado'=>
                ['emailReplyto'=>'',
                'emailTargy'=>'',
                'emailSzoveg'=>''],
              'vevo'=>
                ['email'=>'',
                ],
                ];
                $szamlaKeszites = Szamlazzhu::createSzamla(3,$Adatoksztorno);
                return $szamlaKeszites['szamlaszam'];
    }



    public static function kiegySzamla($id){

                $Adatokkiegy =
                [
                'beallitasok'=>
                    ['szamlaszam'=>$szamla->szamlaszam,
                    'additiv'=>'false',
                    'aggregator'=>''],
                'kifizetes'=>
                    ['datum'=>date('Y-m-d'),
                    'jogcim'=>$fizetesimod,
                    'osszeg'=>$szamla->kiegyenlitve,]
                ];
                $szamlaKeszites = Szamlazzhu::createSzamla(2,$Adatokkiegy);



            return $szamlaKeszites;

    }


    public static function DijDelSzamla($id){


            $szamla = SzlKimeno::findOne($id);
            $szamla->kifizetes = $_POST['kiegyDate'];


                $Adatokkiegy =
                [
                'fejlec'=>
                    [
                    'szamlaszam'=>$szamla->szamlaszam,
                    'rendelesszam'=>''
                     ],
                ];
            $szamlaKeszites = Szamlazzhu::createSzamla(10,$Adatokkiegy);

            $szamlaKeszites = $szamlaKeszites." ";  //hozzáadok egy spacet hogy sose legyen üres.

            if(!strstr($szamlaKeszites,"Agent hibakód"))
            $szamla->rontott = 1;

            $ok = $szamla->update(false);
            return $ok;

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
            $current = ord($value{$i});
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