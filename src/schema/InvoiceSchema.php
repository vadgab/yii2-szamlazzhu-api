<?

namespace vadgab\Yii2SzamlazzhuApi\Schema;

use vadgab\Yii2SzamlazzhuApi\SzamlazzhuApi;
use Yii;
use CURLFile;


class InvoiceSchema extends SzamlazzhuApi{

    public $schema = "";
    private $itemsSchema = "";
    /* Main variables */
    public $items = array();
    public $settings = array();
    public $header = array();
    public $seller = array();
    public $buyer = array();
    public $payed = array();
    public $invoicePayed = "";
    public $type = 1;
    public $pdfTagName = "";
    public $curlName = "";





    /* constans variables for config params */
    CONST SZAMLAZZHU = 'szamlazzhu';
    CONST SZAMLAZZHUUSER = 'szamlazzhuuser';
    CONST SZAMLAZZHUPASS = 'szamlazzhupass';






    public function InvoiceGenerateXml(){


        $this->schema = '<?xml version="1.0" encoding="UTF-8"?>
            <xmlszamla xmlns="http://www.szamlazz.hu/xmlszamla" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.szamlazz.hu/xmlszamla https://www.szamlazz.hu/szamla/docs/xsds/agent/xmlszamla.xsd">
            <beallitasok>';
        /* username or apikey login */

        if(!Yii::$app->params['szamlazzhu']['apikey'])
            $this->schema .= '
                <felhasznalo>'.Yii::$app->params['szamlazzhu']['felhasznalonev'].'</felhasznalo>
                <jelszo>'.Yii::$app->params['szamlazzhu']['jelszo'].'</jelszo>';
        else
            $this->schema .= '
                <szamlaagentkulcs>'.Yii::$app->params['szamlazzhu']['apikey'].'</szamlaagentkulcs>';

        $this->schema .= '
                <eszamla>'.(isset($this->settings['eszamla']) ? $this->settings['eszamla'] : 'false').'</eszamla>
                <szamlaLetoltes>'.(isset($this->settings['szamlaLetoltes']) ? $this->settings['szamlaLetoltes'] : 'false').'</szamlaLetoltes>
                <szamlaLetoltesPld>'.(isset($this->settings['szamlaLetoltesPld']) ? $this->settings['szamlaLetoltesPld'] : '1').'</szamlaLetoltesPld>
                <valaszVerzio>'.(isset($this->settings['valaszVerzio']) ? $this->settings['valaszVerzio'] : '').'</valaszVerzio>
                <aggregator>'.(isset($this->settings['aggregator']) ? $this->settings['aggregator'] : '').'</aggregator>
          </beallitasok>
          <fejlec>
                <keltDatum>'.(isset($this->header['keltDatum']) ? $this->header['keltDatum'] : '').'</keltDatum>
                <teljesitesDatum>'.(isset($this->header['teljesitesDatum']) ? $this->header['teljesitesDatum'] : '').'</teljesitesDatum>
                <fizetesiHataridoDatum>'.(isset($this->header['fizetesiHataridoDatum']) ? $this->header['fizetesiHataridoDatum'] : '').'</fizetesiHataridoDatum>
                <fizmod>'.(isset($this->header['fizmod']) ? $this->header['fizmod'] : '').'</fizmod>
                <penznem>'.(isset($this->header['penznem']) ? $this->header['penznem'] : '').'</penznem>
                <szamlaNyelve>'.(isset($this->header['szamlaNyelve']) ? $this->header['szamlaNyelve'] : '').'</szamlaNyelve>
                <megjegyzes>'.(isset($this->header['megjegyzes']) ? $this->header['megjegyzes'] : '').'</megjegyzes>
                <arfolyamBank>'.(isset($this->header['arfolyamBank']) ? $this->header['arfolyamBank'] : '').'</arfolyamBank>
                <arfolyam>'.(isset($this->header['arfolyam']) ? $this->header['arfolyam'] : '').'</arfolyam>
                <rendelesSzam>'.(isset($this->header['rendelesSzam']) ? $this->header['rendelesSzam'] : '').'</rendelesSzam>
                <elolegszamla>'.(isset($this->header['elolegszamla']) ? $this->header['elolegszamla'] : 'false').'</elolegszamla>
                <vegszamla>'.(isset($this->header['vegszamla']) ? $this->header['vegszamla'] : 'false').'</vegszamla>
                <helyesbitoszamla>'.(isset($this->header['helyesbitoszamla']) ? $this->header['helyesbitoszamla'] : 'false').'</helyesbitoszamla>
                <helyesbitettSzamlaszam>'.(isset($this->header['helyesbitettSzamlaszam']) ? $this->header['helyesbitettSzamlaszam'] : '').'</helyesbitettSzamlaszam>
                <dijbekero>'.(isset($this->header['dijbekero']) ? $this->header['dijbekero'] : 'false').'</dijbekero>
                <szallitolevel>'.(isset($this->header['szallitolevel']) ? $this->header['szallitolevel'] : 'false').'</szallitolevel>';

        if(isset($this->header['szamlaszamElotag'])) //If we enter the data only then put it in
            $this->schema .='
                <szamlaszamElotag>'.$this->header['szamlaszamElotag'].'</szamlaszamElotag>';
        $this->schema .='
                <fizetve>'.(isset($this->header['fizetve']) ? $this->header['fizetve'] : 'false').'</fizetve>';

        $this->schema .='
            </fejlec>
            <elado>
                <bank>'.(isset($this->seller['bank']) ? $this->seller['bank'] : '').'</bank>
                <bankszamlaszam>'.(isset($this->seller['bankszamlaszam']) ? $this->seller['bankszamlaszam'] : '').'</bankszamlaszam>
                <emailReplyto>'.(isset($this->seller['emailReplyto']) ? $this->seller['emailReplyto'] : '').'</emailReplyto>
                <emailTargy>'.(isset($this->seller['emailTargy']) ? $this->seller['emailTargy'] : '').'</emailTargy>
                <emailSzoveg>'.(isset($this->seller['emailSzoveg']) ? $this->seller['emailSzoveg'] : '').'</emailSzoveg>
                <alairoNeve>'.(isset($this->seller['alairoNeve']) ? $this->seller['alairoNeve'] : '').'</alairoNeve>
            </elado>
            <vevo>
                <nev>'.(isset($this->buyer['nev']) ? $this->buyer['nev'] : '').'</nev>';

        if(isset($this->buyer['orszag'])) //If we enter the data only then put it in
                $this->schema .='
                <orszag>'.(isset($this->buyer['orszag']) ? $this->buyer['orszag'] : '').'</orszag>';

                $this->schema .='
                <irsz>'.(isset($this->buyer['irsz']) ? $this->buyer['irsz'] : '').'</irsz>
                <telepules>'.(isset($this->buyer['telepules']) ? $this->buyer['telepules'] : '').'</telepules>
                <cim>'.(isset($this->buyer['cim']) ? $this->buyer['cim'] : '').'</cim>
                <email>'.(isset($this->buyer['email']) ? $this->buyer['email'] : '').'</email>
                <sendEmail>'.(isset($this->buyer['sendEmail']) ? $this->buyer['sendEmail'] : 'false').'</sendEmail>
                <adoszam>'.(isset($this->buyer['adoszam']) ? $this->buyer['adoszam'] : '').'</adoszam>
                <adoszamEU>'.(isset($this->buyer['adoszamEU']) ? $this->buyer['adoszamEU'] : '').'</adoszamEU>
                <postazasiNev>'.(isset($this->buyer['postazasiNev']) ? $this->buyer['postazasiNev'] : '').'</postazasiNev>
                <postazasiIrsz>'.(isset($this->buyer['postazasiIrsz']) ? $this->buyer['postazasiIrsz'] : '').'</postazasiIrsz>
                <postazasiTelepules>'.(isset($this->buyer['postazasiTelepules']) ? $this->buyer['postazasiTelepules'] : '').'</postazasiTelepules>
                <postazasiCim>'.(isset($this->buyer['postazasiCim']) ? $this->buyer['postazasiCim'] : '').'</postazasiCim>';

        if(isset($this->header['vevoFokonyv'])){ //If we enter the data only then put it in
            $this->schema .='
                <vevoFokonyv>
                    <konyvelesDatum>'.(isset($this->header['keltDatum']) ? $this->header['keltDatum'] : '').'</konyvelesDatum>
                    <vevoAzonosito>'.(isset($this->header['vevoiAzonosito']) ? $this->header['vevoiAzonosito'] : '').'</vevoAzonosito>
                    <vevoFokonyviSzam>'.(isset($this->header['vevoFokonyv']) ? $this->header['vevoFokonyv'] : '').'</vevoFokonyviSzam>
                    <folyamatosTelj>0</folyamatosTelj>
                    <elszDatumTol>'.(isset($this->header['keltDatum']) ? $this->header['keltDatum'] : '').'</elszDatumTol>
                    <elszDatumIg>'.(isset($this->header['teljesitesDatum']) ? $this->header['teljesitesDatum'] : '').'</elszDatumIg>
                </vevoFokonyv>';
        }


            $this->schema .='
                <alairoNeve>'.(isset($this->buyer['alairoNeve']) ? $this->buyer['alairoNeve'] : '').'</alairoNeve>';


        if(isset($this->buyer['azonosito'])) //If we enter the data only then put it in
            $this->schema .='
                <azonosito>'.(isset($this->buyer['azonosito']) ? $this->buyer['azonosito'] : '').'</azonosito>';

            $this->schema .='
                <telefonszam>'.(isset($this->buyer['telefonszam']) ? $this->buyer['telefonszam'] : '').'</telefonszam>
                <megjegyzes>'.(isset($this->buyer['megjegyzes']) ? $this->buyer['megjegyzes'] : '').'</megjegyzes>';
            $this->schema .='
                </vevo>';




            $this->schema .='
                <tetelek>';

            if(isset($this->itemsSchema))$this->schema .= $this->itemsSchema;

            $this->schema .='
                </tetelek>
                </xmlszamla>';

            return $this->schema;

        }

    public function InvoicePayedGenerateXml(){
        $this->schema = '<?xml version="1.0" encoding="UTF-8"?>
            <xmlszamlakifiz xmlns="http://www.szamlazz.hu/xmlszamlakifiz" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.szamlazz.hu/xmlszamlakifiz xmlszamlakifiz.xsd ">
            <beallitasok>';
        /* username or apikey login */

        if(!Yii::$app->params['szamlazzhu']['apikey'])
            $this->schema .= '
                <felhasznalo>'.Yii::$app->params['szamlazzhu']['felhasznalonev'].'</felhasznalo>
                <jelszo>'.Yii::$app->params['szamlazzhu']['jelszo'].'</jelszo>';
        else
            $this->schema .= '
                <szamlaagentkulcs>'.Yii::$app->params['szamlazzhu']['apikey'].'</szamlaagentkulcs>';

        $this->schema .= '
                <szamlaszam>'.(isset($this->settings['szamlaszam']) ? $this->settings['szamlaszam'] : '').'</szamlaszam>
                <additiv>'.(isset($this->settings['additiv']) ? $this->settings['additiv'] : '').'</additiv>
                <aggregator>'.(isset($this->settings['aggregator']) ? $this->settings['aggregator'] : '').'</aggregator>
            </beallitasok>';
        $this->schema .= $this->invoicePayed;
        $this->schema .='
            </xmlszamlakifiz>';

        return $this->schema;

    }

   public function InvoiceProFormaDeleteGenerateXml(){
        $this->schema = '<?xml version="1.0" encoding="UTF-8"?>
            <xmlszamladbkdel xmlns="http://www.szamlazz.hu/xmlszamladbkdel" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.szamlazz.hu/xmlszamladbkdel xmlszamladbkdel.xsd ">
                <beallitasok>';
        /* username or apikey login */

        if(!Yii::$app->params['szamlazzhu']['apikey'])
            $this->schema .= '
                <felhasznalo>'.Yii::$app->params['szamlazzhu']['felhasznalonev'].'</felhasznalo>
                <jelszo>'.Yii::$app->params['szamlazzhu']['jelszo'].'</jelszo>';
        else
            $this->schema .= '
                <szamlaagentkulcs>'.Yii::$app->params['szamlazzhu']['apikey'].'</szamlaagentkulcs>';

        $this->schema .= '
                </beallitasok>
                <fejlec>
                    <szamlaszam>'.(isset($this->header['szamlaszam']) ? $this->header['szamlaszam'] : '').'</szamlaszam>
                    <rendelesszam>'.(isset($this->header['rendelesszam']) ? $this->header['rendelesszam'] : '').'</rendelesszam>
                </fejlec>
            </xmlszamladbkdel>';

        return $this->schema;

    }


    public function InvoiceStornoGenerateXml(){
        $this->schema = '<?xml version="1.0" encoding="UTF-8"?>
            <xmlszamlast xmlns="http://www.szamlazz.hu/xmlszamlast" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.szamlazz.hu/xmlszamlast xmlszamlast.xsd ">
                <beallitasok>';
        /* username or apikey login */

        if(!Yii::$app->params['szamlazzhu']['apikey'])
            $this->schema .= '
                <felhasznalo>'.Yii::$app->params['szamlazzhu']['felhasznalonev'].'</felhasznalo>
                <jelszo>'.Yii::$app->params['szamlazzhu']['jelszo'].'</jelszo>';
        else
            $this->schema .= '
                <szamlaagentkulcs>'.Yii::$app->params['szamlazzhu']['apikey'].'</szamlaagentkulcs>';

        $this->schema .= '
                    <eszamla>'.$this->settings['eszamla'].'</eszamla>
                    <szamlaLetoltes>'.(isset($this->settings['szamlaLetoltes']) ? $this->settings['szamlaLetoltes'] : '').'</szamlaLetoltes>
                    <szamlaLetoltesPld>'.(isset($this->settings['szamlaLetoltesPld']) ? $this->settings['szamlaLetoltesPld'] : '').'</szamlaLetoltesPld>
                    <aggregator>'.(isset($this->settings['aggregator']) ? $this->settings['aggregator'] : '').'</aggregator>
                </beallitasok>
                <fejlec>
                    <szamlaszam>'.(isset($this->header['szamlaszam']) ? $this->header['szamlaszam'] : '').'</szamlaszam>
                    <keltDatum>'.(isset($this->header['keltDatum']) ? $this->header['keltDatum'] : '').'</keltDatum>
                    <teljesitesDatum>'.(isset($this->header['teljesitesDatum']) ? $this->header['teljesitesDatum'] : '').'</teljesitesDatum>
                    <tipus>SS</tipus>
                </fejlec>
                <elado>
                    <emailReplyto>'.(isset($this->seller['emailReplyto']) ? $this->seller['emailReplyto'] : '').'</emailReplyto>
                    <emailTargy>'.(isset($this->seller['emailTargy']) ? $this->seller['emailTargy'] : '').'</emailTargy>
                    <emailSzoveg>
                    '.(isset($this->seller['emailSzoveg']) ? $this->seller['emailSzoveg'] : '').'
                    </emailSzoveg>
                </elado>
                <vevo>
                    <email>'.(isset($this->buyer['email']) ? $this->buyer['email'] : '').'</email>
                </vevo>
            </xmlszamlast>';

        return $this->schema;

    }

    public function InvoiceGetDataGenerateXml(){
        $this->schema = '<?xml version="1.0" encoding="UTF-8"?>
                            <xmlszamlaxml xmlns="http://www.szamlazz.hu/xmlszamlaxml" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.szamlazz.hu/xmlszamlaxml https://www.szamlazz.hu/szamla/docs/xsds/agentxml/xmlszamlaxml.xsd">';
        if(!Yii::$app->params['szamlazzhu']['apikey'])
            $this->schema .= '
                <felhasznalo>'.Yii::$app->params['szamlazzhu']['felhasznalonev'].'</felhasznalo>
                <jelszo>'.Yii::$app->params['szamlazzhu']['jelszo'].'</jelszo>';
        else
            $this->schema .= '
                <szamlaagentkulcs>'.Yii::$app->params['szamlazzhu']['apikey'].'</szamlaagentkulcs>';

        $this->schema .= '
                <szamlaszam>'.(isset($this->header['szamlaszam']) ? $this->header['szamlaszam'] : '').'</szamlaszam>
                <rendelesSzam>'.(isset($this->header['rendelesSzam']) ? $this->header['rendelesSzam'] : '').'</rendelesSzam>
                <pdf>'.(isset($this->header['pdf']) ? $this->header['pdf'] : 'false').'</pdf>
                </xmlszamlaxml>';
    }

    public function InvoiceGetPDFGenerateXml(){
        $this->schema = '<?xml version="1.0" encoding="UTF-8"?>
            <xmlszamlapdf xmlns="http://www.szamlazz.hu/xmlszamlapdf" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.szamlazz.hu/xmlszamlapdf https://www.szamlazz.hu/szamla/docs/xsds/agentpdf/xmlszamlapdf.xsd">';
        if(!Yii::$app->params['szamlazzhu']['apikey'])
            $this->schema .= '
                <felhasznalo>'.Yii::$app->params['szamlazzhu']['felhasznalonev'].'</felhasznalo>
                <jelszo>'.Yii::$app->params['szamlazzhu']['jelszo'].'</jelszo>';
        else
            $this->schema .= '
                <szamlaagentkulcs>'.Yii::$app->params['szamlazzhu']['apikey'].'</szamlaagentkulcs>';

        $this->schema .= '
                <szamlaszam>'.(isset($this->header['szamlaszam']) ? $this->header['szamlaszam'] : '').'</szamlaszam>
                <valaszVerzio>1</valaszVerzio>
                <szamlaKulsoAzon></szamlaKulsoAzon>
            </xmlszamlapdf>';
    }

    public function InvoiceAddItems(){


        $vtsz = "";
        $tkod = "";

        $tipusok = ['ÁKK', 'K.AFA', 'F.AFA', 'MAA','EUK','EUT','EUKT','EU','AAM','TAM','THK','TAHK','TEHK'];
        if( $this->items['afakulcs']!=""){
            if(!array_search( $this->items['afakulcs'],$tipusok)){
                $afakulcs = $this->items['afakulcs'];
            }else{
                $afakulcs = str_replace("%","",$this->items['afakulcs']);
            }
        }else{
            $afakulcs = $this->afakulcs;
        }

        $this->itemsSchema .='
            <tetel>
                <megnevezes>'.$this->items['megnevezes'].'</megnevezes>
                <mennyiseg>'.$this->items['mennyiseg'].'</mennyiseg>';
        $this->itemsSchema .='
                <mennyisegiEgyseg>'.$this->items['mennyisegiEgyseg'].'</mennyisegiEgyseg>';


                $this->itemsSchema .='
                <nettoEgysegar>'.$this->items['nettoEgysegar'].'</nettoEgysegar>
                <afakulcs>'.$afakulcs.'</afakulcs>
                <nettoErtek>'.$this->items['nettoErtek'].'</nettoErtek>
                <afaErtek>'.round($this->items['bruttoErtek'] - $this->items['nettoErtek'],2).'</afaErtek>
                <bruttoErtek>'.$this->items['bruttoErtek'].'</bruttoErtek>
                <megjegyzes>'.$this->items['megjegyzes'].'</megjegyzes>';

        $this->itemsSchema .='
            </tetel>';

    }

    public function InvoiceAddPayed(){
        $this->invoicePayed .='
            <kifizetes>
                <datum>'.(isset($this->payed['datum']) ? $this->payed['datum'] : '').'</datum>
                <jogcim>'.(isset($this->payed['jogcim']) ? $this->payed['jogcim'] : '').'</jogcim>
                <osszeg>'.(isset($this->payed['osszeg']) ? $this->payed['osszeg'] : '').'</osszeg>
            </kifizetes>';
    }

    public function defineInvoiceType(){

        /* create invoice */
        if($this->type==1){
            $this->header['elolegszamla'] = 'false';
            $this->header['vegszamla'] = 'false';
            $this->header['helyesbitoszamla'] = 'false';
            $this->header['dijbekero'] = 'false';
            $this->header['szallitolevel'] = 'false';
            $this->curlName = "action-xmlagentxmlfile";
            $this->pdfTagName = "szamla";

        }

        /* invoice mark payed */
        if($this->type==2){
            $this->curlName = "action-szamla_agent_kifiz";
            $this->pdfTagName = "szamlakifiz";
        }

        /* create storno invoice */
        if($this->type==3){
            $this->curlName = "action-szamla_agent_st";
            $this->pdfTagName = "szamlast";
        }

        /* create pdf download invoice */
        if($this->type==4){
            $this->curlName = "action-szamla_agent_pdf";
            $this->pdfTagName = "szamlapdf";
        }

        /* create preinvoice invoice */
        if($this->type==5){
            $this->header['elolegszamla'] = 'true';
            $this->header['vegszamla'] = 'false';
            $this->header['helyesbitoszamla'] = 'false';
            $this->header['dijbekero'] = 'false';
            $this->header['szallitolevel'] = 'false';
            $this->curlName = "action-xmlagentxmlfile";
            $this->pdfTagName = "szamla";
        }

        /* create final invoice */
        if($this->type==6){
            $this->header['elolegszamla'] = 'false';
            $this->header['vegszamla'] = 'true';
            $this->header['helyesbitoszamla'] = 'false';
            $this->header['dijbekero'] = 'false';
            $this->header['szallitolevel'] = 'false';
            $this->curlName = "action-xmlagentxmlfile";
            $this->pdfTagName = "szamla";
        }

        /* create corrective invoice */
        if($this->type==7){
            $this->header['elolegszamla'] = 'false';
            $this->header['vegszamla'] = 'false';
            $this->header['helyesbitoszamla'] = 'true';
            $this->header['dijbekero'] = 'false';
            $this->header['szallitolevel'] = 'false';
            $this->curlName = "action-xmlagentxmlfile";
            $this->pdfTagName = "szamla";
        }

        /* create pro forma invoice */
        if($this->type==8){
            $this->header['elolegszamla'] = 'false';
            $this->header['vegszamla'] = 'false';
            $this->header['helyesbitoszamla'] = 'false';
            $this->header['dijbekero'] = 'true';
            $this->header['szallitolevel'] = 'false';
            $this->curlName = "action-xmlagentxmlfile";
            $this->pdfTagName = "szamla";
        }

        /* create Delivery invoice */
        if($this->type==9){
            $this->header['elolegszamla'] = 'false';
            $this->header['vegszamla'] = 'false';
            $this->header['helyesbitoszamla'] = 'false';
            $this->header['dijbekero'] = 'false';
            $this->header['szallitolevel'] = 'true';
            $this->curlName = "action-xmlagentxmlfile";
            $this->pdfTagName = "szallito";
        }

        /* create Delete Pro forma */
        if($this->type==10){
            $this->curlName = "action-szamla_agent_dijbekero_torlese";
            $this->pdfTagName = "szamlaDijDel";
        }

        if($this->type==11){
            $this->curlName = "action-szamla_agent_xml";
            $this->pdfTagName = "szamlaXmlszamlaxml";
        }

        if($this->type==12){
            $this->curlName = "action-szamla_agent_pdf";
            $this->pdfTagName = "szamlaXmlszamlaPDF";
        }

    }



}
