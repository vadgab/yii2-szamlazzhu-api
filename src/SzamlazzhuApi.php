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
        // cookie file teljes el�r�si �tja a szerveren
        $cookie_file = __dir__."/temp/szamlazz_cookie.txt";
        // ebbe a f�jlba menti a pdf-et, ha az xml-ben k�rt�k

        // ezt az xml f�jlt k�ldi a sz�mla agentnek
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

        if($this->type==5){  ////////////////////////////////////EL�LEG SZ�MLA
            $adatok['fejlec']['elolegszamla'] = 'true';
            $adatok['fejlec']['vegszamla'] = 'false';
            $adatok['fejlec']['helyesbitoszamla'] = 'false';
            $adatok['fejlec']['dijbekero'] = 'false';
            $adatok['fejlec']['szallitolevel'] = 'false';
            $xmlfile = self::SzamlaGenerateXml($adatok);
            $curlName = "action-xmlagentxmlfile";
            $pdfTagName = "szamla";
        }

        if($this->type==6){  ////////////////////////////////////V�G SZ�MLA
            $adatok['fejlec']['elolegszamla'] = 'false';
            $adatok['fejlec']['vegszamla'] = 'true';
            $adatok['fejlec']['helyesbitoszamla'] = 'false';
            $adatok['fejlec']['dijbekero'] = 'false';
            $adatok['fejlec']['szallitolevel'] = 'false';
            $xmlfile = self::SzamlaGenerateXml($adatok);
            $curlName = "action-xmlagentxmlfile";
            $pdfTagName = "szamla";
        }

        if($this->type==7){   ////////////////////////////////////HELYESB�T� SZ�MLA
            $adatok['fejlec']['elolegszamla'] = 'false';
            $adatok['fejlec']['vegszamla'] = 'false';
            $adatok['fejlec']['helyesbitoszamla'] = 'true';
            $adatok['fejlec']['dijbekero'] = 'false';
            $adatok['fejlec']['szallitolevel'] = 'false';
            $xmlfile = self::SzamlaGenerateXml($adatok);
            $curlName = "action-xmlagentxmlfile";
            $pdfTagName = "szamla";
        }

        if($this->type==8){    ////////////////////////////////////D�JBEK�R� SZ�MLA
            $adatok['fejlec']['elolegszamla'] = 'false';
            $adatok['fejlec']['vegszamla'] = 'false';
            $adatok['fejlec']['helyesbitoszamla'] = 'false';
            $adatok['fejlec']['dijbekero'] = 'true';
            $adatok['fejlec']['szallitolevel'] = 'false';
            $xmlfile = self::SzamlaGenerateXml($adatok);
            $curlName = "action-xmlagentxmlfile";
            $pdfTagName = "szamla";
        }

        if($this->type==9){      ////////////////////////////////////SZ�LL�T� LEV�L
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


        // ha k�rj�k a sz�mla pdf-et, akkor legyen true
        $szamlaletoltes = true;
        // ha m�g nincs --> l�trehozzuk a cookie file-t --> l�teznie kell, hogy a CURL �rhasson bele
        if (!file_exists($cookie_file)) {
            file_put_contents($cookie_file, '');
        }
        // a CURL inicializ�l�sa
        $ch = curl_init(URL_MAIN);
        // A curl h�v�s eset�n tan�s�tv�nyhib�t kaphatunk az SSL tan�s�tv�ny val�dis�g�t�l
        // f�ggetlen�l, ez az al�bbi CURL param�ter �ll�t�ssal kik�sz�b�lhet�,
        // ilyenkor nincs k�l�n SSL ellen�rz�s:
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        // POST-ban k�ldj�k az adatokat
        curl_setopt($ch, CURLOPT_POST, true);
        // K�rj�k a HTTP headert a v�laszba, fontos inform�ci�k vannak benne
        curl_setopt($ch, CURLOPT_HEADER, true);
        // v�ltoz�ban t�roljuk a v�lasz tartalm�t, nem �rjuk a kimenetbe
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        // Kb �gy n�z ki CURLFile haszn�lat�val:
            curl_setopt($ch, CURLOPT_POSTFIELDS, array($curlName=>new CURLFile($xmlfile, 'application/xml', 'filenev')));
        // �s m�g egy opci�t sz�ks�ges ilyenkor be�ll�tani:
            curl_setopt($ch, CURLOPT_SAFE_UPLOAD, true);
//        curl_setopt($ch, CURLOPT_POSTFIELDS, array('action-xmlagentxmlfile'=>'@' . $xmlfile));
        // 30 m�sodpercig tartjuk fenn a kapcsolatot (ha valami b�kken� volna)
        curl_setopt($ch, CURLOPT_TIMEOUT, 300);
        // Itt �ll�tjuk be, hogy az �rkez� cookie a $cookie_file-ba ker�lj�n ment�sre
        curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_file);
        // Ha van m�r cookie file-unk, �s van is benne valami, elk�ldj�k a Sz�ml�zz.hu-nak
        if (file_exists($cookie_file) && filesize($cookie_file) > 0) {
            curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_file);
        }

        // elk�ldj�k a k�r�st a Sz�ml�zz.hu fel�, �s elt�roljuk a v�laszt
        $agent_response = curl_exec($ch);



        // kiolvassuk a curl-b�l volt-e hiba
        $http_error = curl_error($ch);
        // ezekben a v�ltoz�kban t�roljuk a sz�tbontott v�laszt
        $agent_header = '';
        $agent_body = '';
        $agent_http_code = '';

        // lek�rj�k a v�lasz HTTP_CODE-j�t, ami ha 200, akkor a http kommunik�ci� rendben volt
        // ett�l m�g egy�ltal�n nem biztos, hogy a sz�mla elk�sz�lt
        $agent_http_code = curl_getinfo($ch,CURLINFO_HTTP_CODE);

        // a v�lasz egy byte kupac, ebb�l az els� "header_size" darab byte lesz a header
        $header_size = curl_getinfo($ch,CURLINFO_HEADER_SIZE);

        // a header t�rol�sa, ebben lesznek majd a sz�mlasz�m, brutt� nett� �sszegek, errorcode, stb.
        $agent_header = substr($agent_response, 0, $header_size);

        // a body t�rol�sa, ez lesz a pdf, vagy sz�veges �zenet
        $agent_body = substr( $agent_response, $header_size );

        // a curl m�r nem kell, lez�rjuk
        curl_close($ch);

        // a header soronk�nt tartalmazza az inform�ci�kat, egy t�mbbe tesz�k a k�l�n sorokat
        $header_array = explode("\n", $agent_header);

        // ezt majd true-ra �ll�tjuk ha volt hiba
        $volt_hiba = false;

        // ebben lesznek a hiba inform�ci�k, plusz a bodyban
        $agent_error = '';
        $agent_error_code = '';



        // menj�nk v�gig a header sorokon, ami "szlahu"-val kezd�dik az �rdekes nek�nk �s �rjuk ki
        foreach ($header_array as $val) {
          if (substr($val, 0, strlen('szlahu')) === 'szlahu') {
//            echo urldecode($val).'<br>';

          if (strstr($val,'szlahu_szamlaszam')) {  // sz�mlasz�m kinyer�se v�lasz �zenetb�l
            $szamlaszam_ = explode(":",$val);
            $szamlaszam = substr(str_replace(" ","",$szamlaszam_['1']),0,-1);
          }

          if (strstr($val,'szlahu_nettovegosszeg')) {  // NETTO kinyer�se v�lasz �zenetb�l
            $netto_ = explode(":",$val);
            $netto = substr(str_replace(" ","",$netto_['1']),0,-1);
          }

          if (strstr($val,'szlahu_bruttovegosszeg')) {  // BRUTTO kinyer�se v�lasz �zenetb�l
            $brutto_ = explode(":",$val);
            $brutto = substr(str_replace(" ","",$brutto_['1']),0,-1);
          }

          if (strstr($val,'szlahu_vevoifiokurl')) {  // BRUTTO kinyer�se v�lasz �zenetb�l
            $vevourl_ = explode(":",$val);
            $vevourl = substr(str_replace(" ","",$vevourl_['1']),0,-1);
          }


            // megvizsg�ljuk, hogy volt-e hiba
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

        // ha volt http hiba dobunk egy kiv�telt
        if ( $http_error != "" )
        {
          echo 'Http hiba t�rt�nt:'.$http_error;
  //        throw new Exception('Hiba t�rt�nt:'.$http_error);
        }

        if ($volt_hiba) {

          // ha a sz�mla nem k�sz�lt el ki�rjuk amit lehet
          $outError = 'Agent hibak�d: '.$agent_error_code.'<br>';
          $outError = $outError.  'Agent hiba�zenet: '.urldecode($agent_error).'<br>';
//          $outError = $outError. 'Agent v�lasz: '.urldecode($agent_body).'<br>';

          // dobunk egy kiv�telt
//          throw new Exception('Sz�mlak�sz�t�s sikertelen:'.$agent_error_code);
            $out['error'] = $outError;

            return $out;


        } else {

          // ha nem volt hiba feldolgozzuk a v�laszban �rkezett pdf-et vagy sz�veges inform�ci�t

            // ha nem k�rt�k a pdf-et akkor sz�veges inform�ci� j�tt a v�laszban, ezt ki�rjuk

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
                MyFunctions::DirCheckCreateYearMonth("generateXml");  /// ellen�rzi l�tezik az adott upload k�nyvt�rban az �v �s a h�nap amibe majd �rja az xml-t
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
                MyFunctions::DirCheckCreateYearMonth("generateXml");  /// ellen�rzi l�tezik az adott upload k�nyvt�rban az �v �s a h�nap amibe majd �rja az xml-t
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
        MyFunctions::DirCheckCreateYearMonth("generateXml");  /// ellen�rzi l�tezik az adott upload k�nyvt�rban az �v �s a h�nap amibe majd �rja az xml-t
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

            $szamlaKeszites = $szamlaKeszites." ";  //hozz�adok egy spacet hogy sose legyen �res.

            if(!strstr($szamlaKeszites,"Agent hibak�d"))
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