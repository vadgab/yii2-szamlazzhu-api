<?

namespace vadgab\Yii2SzamlazzhuApi\Schema;

use vadgab\Yii2SzamlazzhuApi\SzamlazzhuApi;


class InvoiceSchema extends SzamlazzhuApi{

    private $schema = "";
    public $settings = array();
    public $header = array();
    public $seller = array();
    public $buyer = array();
    public $items = array();


    public static function InvoiceGenerateXml($adatok){


        $this->schema = '<?xml version="1.0" encoding="UTF-8"?>
        <xmlszamla xmlns="http://www.szamlazz.hu/xmlszamla" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.szamlazz.hu/xmlszamla xmlszamla.xsd">
            <beallitasok>
                <felhasznalo>'.Yii::$app->params['szamlazzhu']['felhasznalonev'].'</felhasznalo>
                <jelszo>'.Yii::$app->params['szamlazzhu']['jelszo'].'</jelszo>
                <eszamla>'.$this->settings['eszamla'].'</eszamla>

                <szamlaLetoltes>'.$this->settings['szamlaLetoltes'].'</szamlaLetoltes>
                <szamlaLetoltesPld>1</szamlaLetoltesPld>
                <valaszVerzio>'.$this->settings['valaszVerzio'].'</valaszVerzio>
                <aggregator>'.$this->settings['aggregator'].'</aggregator>
          </beallitasok>
          <fejlec>
                <keltDatum>'.$this->header['keltDatum'].'</keltDatum>
                <teljesitesDatum>'.$this->header['teljesitesDatum'].'</teljesitesDatum>
                <fizetesiHataridoDatum>'.$this->header['fizetesiHataridoDatum'].'</fizetesiHataridoDatum>
                <fizmod>'.$this->header['fizmod'].'</fizmod>
                <penznem>'.$this->header['penznem'].'</penznem>
                <szamlaNyelve>'.$this->header['szamlaNyelve'].'</szamlaNyelve>
                <megjegyzes>'.$this->header['megjegyzes'].'</megjegyzes>
                <arfolyamBank>'.$this->header['arfolyamBank'].'</arfolyamBank>
                <arfolyam>'.$this->header['arfolyam'].'</arfolyam>
                <rendelesSzam>'.$this->header['rendelesSzam'].'</rendelesSzam>
                <elolegszamla>'.$this->header['elolegszamla'].'</elolegszamla>
                <vegszamla>'.$this->header['vegszamla'].'</vegszamla>
                <helyesbitoszamla>'.$this->header['helyesbitoszamla'].'</helyesbitoszamla>
                <helyesbitettSzamlaszam>'.$this->header['helyesbitettSzamlaszam'].'</helyesbitettSzamlaszam>
                <dijbekero>'.$this->header['dijbekero'].'</dijbekero>
                <szallitolevel>'.$this->header['szallitolevel'].'</szallitolevel>';

        if($this->header['szamlaszamElotag']!="") //ha megadjuk az adatot csak akkor teszi bele
            $this->schema .='
                <szamlaszamElotag>'.$this->header['szamlaszamElotag'].'</szamlaszamElotag>';
        $this->schema .='
            <fizetve>'.$this->header['fizetve'].'</fizetve>';

        $this->schema .='
            </fejlec>
            <elado>
                <bank>'.$this->buyer['bank'].'</bank>
                <bankszamlaszam>'.$this->buyer['bankszamlaszam'].'</bankszamlaszam>
                <emailReplyto>'.$this->buyer['emailReplyto'].'</emailReplyto>
                <emailTargy>'.$this->buyer['emailTargy'].'</emailTargy>
                <emailSzoveg>'.$this->buyer['emailSzoveg'].'</emailSzoveg>
                <alairoNeve>'.$this->buyer['alairoNeve'].'</alairoNeve>
            </elado>
            <vevo>
                <nev>'.$this->seller['nev'].'</nev>';

        if($this->seller['orszag']!="") //ha megadjuk az azonosítót csak akkor teszi bele
                $this->schema .='
                <orszag>'.$this->seller['orszag'].'</orszag>';

                $this->schema .='
                <irsz>'.$this->seller['irsz'].'</irsz>
                <telepules>'.$this->seller['telepules'].'</telepules>
                <cim>'.$this->seller['cim'].'</cim>
                <email>'.$this->seller['email'].'</email>
                <sendEmail>'.$this->seller['sendEmail'].'</sendEmail>
                <adoszam>'.$this->seller['adoszam'].'</adoszam>
                <adoszamEU>'.$this->seller['adoszamEU'].'</adoszamEU>
                <postazasiNev>'.$this->seller['postazasiNev'].'</postazasiNev>
                <postazasiIrsz>'.$this->seller['postazasiIrsz'].'</postazasiIrsz>
                <postazasiTelepules>'.$this->seller['postazasiTelepules'].'</postazasiTelepules>
                <postazasiCim>'.$this->seller['postazasiCim'].'</postazasiCim>';

        if($this->header['vevoFokonyv']!=""){ //ha megadjuk az adatot csak akkor teszi bele
            $this->schema .='
                <vevoFokonyv>
                    <konyvelesDatum>'.$this->header['keltDatum'].'</konyvelesDatum>
                    <vevoAzonosito>43242</vevoAzonosito>
                    <vevoFokonyviSzam>'.$this->header['vevoFokonyv'].'</vevoFokonyviSzam>
                    <folyamatosTelj>0</folyamatosTelj>
                    <elszDatumTol>'.$this->header['keltDatum'].'</elszDatumTol>
                    <elszDatumIg>'.$this->header['teljesitesDatum'].'</elszDatumIg>
                </vevoFokonyv>';
        }


            $this->schema .='
                <alairoNeve>'.$this->seller['alairoNeve'].'</alairoNeve>';


        if($this->seller['azonosito']!="") //ha megadjuk az azonosítót csak akkor teszi bele
            $this->schema .='
                <azonosito>'.$this->seller['azonosito'].'</azonosito>';

            $this->schema .='
                <telefonszam>'.$this->seller['telefonszam'].'</telefonszam>
                <megjegyzes>'.$this->seller['megjegyzes'].'</megjegyzes>';
            $this->schema .='
                </vevo>';




            $this->schema .='
                <tetelek>';

            if(count($this->items)>0)foreach($this->items as $item){
                $vtsz = "";
                $tkod = "";

                $tipusok = ['ÁKK', 'K.AFA', 'F.AFA', 'MAA','EUK','EUT','EUKT','EU','AAM','TAM','THK','TAHK','TEHK'];
                if($item['afabetukod']!=""){
                    if(!array_search($item['afabetukod'],$tipusok)){
                        $afakulcs = $item['afakulcs'];
                    }else{
                        $afakulcs = str_replace("%","",$item['afabetukod']);
                    }
                }else{
                    $afakulcs = $item['afakulcs'];
                }

                $this->schema .='
                    <tetel>
                        <megnevezes>'.$item['megnevezes'].'</megnevezes>
                        <mennyiseg>'.$item['mennyiseg'].'</mennyiseg>';
                $this->schema .='
                        <mennyisegiEgyseg>'.$item['mennyisegiEgyseg'].'</mennyisegiEgyseg>';


                    /////csak egész szémokat kapjon.
                        if(Yii::$app->params['payed']['cellarius']['szamlazzhuHUFOnlyRound'] && $this->header['penznem']=="Ft"){
                            $item['bruttoErtek'] = round($item['bruttoErtek']);
                            $item['nettoErtek'] = round($item['nettoErtek']);
                            $item['afaErtek'] = round($item['bruttoErtek'] - $item['nettoErtek']);
                        }

                        $this->schema .='
                        <nettoEgysegar>'.$item['nettoEgysegar'].'</nettoEgysegar>
                        <afakulcs>'.$afakulcs.'</afakulcs>
                        <nettoErtek>'.$item['nettoErtek'].'</nettoErtek>
                        <afaErtek>'.$item['afaErtek'].'</afaErtek>
                        <bruttoErtek>'.$item['bruttoErtek'].'</bruttoErtek>
                        <megjegyzes>'.$item['megjegyzes'].'</megjegyzes>';

                $this->schema .='
                    </tetel>';
            }
            $this->schema .='
            </tetelek>
            </xmlszamla>';

            return $this->schema;

        }

    public static function InvoicePayedGenerateXml($adatok){
        $this->schema = '<?xml version="1.0" encoding="UTF-8"?>
            <xmlszamlakifiz xmlns="http://www.szamlazz.hu/xmlszamlakifiz" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.szamlazz.hu/xmlszamlakifiz xmlszamlakifiz.xsd ">
            <beallitasok>
                <felhasznalo>'.Yii::$app->params['szamlazzhu']['felhasznalonev'].'</felhasznalo>
                <jelszo>'.Yii::$app->params['szamlazzhu']['jelszo'].'</jelszo>
                <szamlaszam>'.$this->settings['szamlaszam'].'</szamlaszam>
                <additiv>'.$this->settings['additiv'].'</additiv>
                <aggregator>'.$this->settings['aggregator'].'</aggregator>
            </beallitasok>
            <kifizetes>
                <datum>'.$adatok['kifizetes']['datum'].'</datum>
                <jogcim>'.$adatok['kifizetes']['jogcim'].'</jogcim>
                <osszeg>'.$adatok['kifizetes']['osszeg'].'</osszeg>
            </kifizetes>';
        $this->schema .='
            </xmlszamlakifiz>';

        return $this->schema;

    }

   public static function InvoicePayedDeleteGenerateXml($adatok){
        $this->schema = '<?xml version="1.0" encoding="UTF-8"?>
            <xmlszamladbkdel xmlns="http://www.szamlazz.hu/xmlszamladbkdel" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.szamlazz.hu/xmlszamladbkdel xmlszamladbkdel.xsd ">
                <beallitasok>
                    <felhasznalo>'.Yii::$app->params['szamlazzhu']['felhasznalonev'].'</felhasznalo>
                    <jelszo>'.Yii::$app->params['szamlazzhu']['jelszo'].'</jelszo>
                </beallitasok>
                <fejlec>
                    <szamlaszam>'.$this->header['szamlaszam'].'</szamlaszam>
                    <rendelesszam>'.$this->header['rendelesszam'].'</rendelesszam>
                </fejlec>
            </xmlszamladbkdel>';

        return $this->schema;

    }


    public static function InvoiceStornoGenerateXml($adatok){
        $this->schema = '<?xml version="1.0" encoding="UTF-8"?>
            <xmlszamlast xmlns="http://www.szamlazz.hu/xmlszamlast" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.szamlazz.hu/xmlszamlast xmlszamlast.xsd ">
                <beallitasok>
                    <felhasznalo>'.Yii::$app->params['szamlazzhu']['felhasznalonev'].'</felhasznalo>
                    <jelszo>'.Yii::$app->params['szamlazzhu']['jelszo'].'</jelszo>
                    <eszamla>'.$this->settings['eszamla'].'</eszamla>
                    <kulcstartojelszo>'.$this->settings['kulcstartojelszo'].'</kulcstartojelszo>
                    <szamlaLetoltes>'.$this->settings['szamlaLetoltes'].'</szamlaLetoltes>
                    <szamlaLetoltesPld>'.$this->settings['szamlaLetoltesPld'].'</szamlaLetoltesPld>
                    <aggregator>'.$this->settings['aggregator'].'</aggregator>
                </beallitasok>
                <fejlec>
                    <szamlaszam>'.$this->header['szamlaszam'].'</szamlaszam>
                    <keltDatum>'.$this->header['keltDatum'].'</keltDatum>
                    <teljesitesDatum>'.$this->header['teljesitesDatum'].'</teljesitesDatum>
                    <tipus>SS</tipus>
                </fejlec>
                <elado>
                    <emailReplyto>'.$this->buyer['emailReplyto'].'</emailReplyto>
                    <emailTargy>'.$this->buyer['emailTargy'].'</emailTargy>
                    <emailSzoveg>
                    '.$this->buyer['emailSzoveg'].'
                    </emailSzoveg>
                </elado>
                <vevo>
                    <email>'.$this->seller['email'].'</email>
                </vevo>
            </xmlszamlast>';

        return $this->schema;

    }


}
