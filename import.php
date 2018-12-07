<?php
error_reporting(0);
ini_set('display_errors', 0);
header('Content-Type: text/html; charset=utf-8');
setlocale(LC_CTYPE, 'en_AU.utf8');

$first_row = array();       // columns name from csv file to use als key in values list
$values_list = array();     // list for all values ( from csv file )

/**
 * Database information
 */
$servername = "localhost";
$username = "admin";
$password = "1";
$dbname = "csvimport";

// to test a connection
try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    // set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // create new database
    $sql = file_get_contents('sqlquery.sql');
    $qr = $conn->exec($sql);

    echo "connection is ok".PHP_EOL;
}
catch(PDOException $e)
{
    echo $sql . "<br>" . $e->getMessage();
    return;
}

/**
 * to quote and return the integer values , ( convert monthes to integer )
 * @param $cn
 *
 * @return int
 */
function result_integer($cn, $fild_name = null){
    if($cn == "" || $cn == " ")
    {
        return 0;
    }
    elseif($cn == null)
    {
        return 0;
    }
    elseif(is_numeric($cn))
    {
        return $cn;
    }
    else{
        try{
            if(strpos($cn, " cm") !== false )
            {
                $int = str_replace(" cm", "", $cn);
                //echo str_replace(" cm", "", $cn);
                return (int)$int;
            }
            // if it is monthe
            $month_number = date('m', strtotime($cn));
            $int = (int)$month_number;
            return $int;
        }catch (Exception $e)
        {
            // if not number or white space or month
            return 0;
        }
    }
}

/**
 * if boolean
 * @param $cn
 *
 * @return int
 */
function result_bool($cn){
    if($cn == "Falsch" || $cn == "false" || $cn == "0" || $cn == "")
    {
        return 0;
    }
    elseif($cn == "Wahr" || $cn == "true" || $cn == "1")
    {
        return 1;
    }
    return 0;
}

/**
 * to quote and return the date values
 * @param $cn
 *
 * @return false|null|string
 */
function result_date($cn){
    if($cn == "" || $cn == "0" || $cn == " ")
    {
        return NULL;
    }


    return date("Y.m.d", strtotime($cn)) ;
}

/**
 *  take and return string ( quote and clear )
 *
 * @param $cn
 *
 * @return string
 */
function result_varchar($cn)
{
    if (strpos($cn, " cm") !== false) {
        $cn = str_replace(" cm", "", $cn);
    }
    if($cn == "'" || $cn == " " || $cn == "")
    {
        return NULL;
    }

//    $cn = addslashes($cn);
    return $cn;
}

/**
 * get and return string ( tax )
 * @param $cn
 *
 * @return string
 */
function get_steuer($cn){
    if($cn == "1B"){ $cn = "7%"; }
    elseif ($cn == "1A"){ $cn = "19%"; }
    else{ $cn = "0%"; }
    return $cn ;
}

/**
 * get the prices plan
 * @param $row
 *
 * @return float
 */
function get_last_prise($row)
{
    // VKVALIDD1	VKPREIS1	VKVALIDD2	VKPREIS2
    //Wenn VKVALIDD2 > VKVALIDD1 muss VKPREIS2 => price; ansonsten VKPREIS1 => price (float)
    if(strtotime($row["VKVALIDD2"]) > strtotime($row["VKVALIDD1"]))
    {
        return floatval(str_replace(",", ".", $row["VKPREIS2"]));
    }
    else{
        return floatval(str_replace(",", ".", $row["VKPREIS1"]));
    }
}

/**
 * returns an array like
 * :mwst => $row["WM"] . $row["NUMMER"]
 *
 * @param $row
 *
 * @return array
 */
function change_value($row)
{
    //WM,NUMMER,MWST,RES_ZEIT,WARENGR,INTERNET,SAISON_KZ,BANAME1,BANAME2,BANAME3,DKZ1,DKZ2,DKZ3,SYS_ANLAGE,DKZ4,PREIS_GRP,BIS_MENGE,VKVALIDD1,VKPREIS1,VKVALIDD2,VKPREIS2,STKPREIS,GP_MENGE,GP_EINHEIT,PACK_MENGE,RABATT,VKPREIS3,VKVALIDD3,VKBISDT1,VKBISDT2,VKBISDT3,STAFRABATT,FAKTOR,ISZUSATZ00,ISZUSATZ01,ISZUSATZ02,ISZUSATZ03,ISZUSATZ04,ISZUSATZ05,ISZUSATZ06,ISZUSATZ07,ISZUSATZ08,ISZUSATZ09,ISZUSATZ10,ISZUSATZ11,ISZUSATZ12,ISZUSATZ13,ISZUSATZ14,ISZUSATZ15,ISZUSATZ16,ISZUSATZ17,ISZUSATZ18,ISZUSATZ19,ISZUSATZ20,ISZUSATZ21,ISZUSATZ22,ISZUSATZ23,ISZUSATZ24,ISZUSATZ25,ISZUSATZ26,ISZUSATZ27,ISZUSATZ28,ISZUSATZ29,ISZUSATZ30,ISZUSATZ31,ISZUSATZ32,ISZUSATZ33,ISZUSATZ34,ISZUSATZ35,ISZUSATZ36,ISZUSATZ37,ISZUSATZ38,ISZUSATZ39,ISZUSATZ40,ISZUSATZ41,ISZUSATZ42,ISZUSATZ43,ISZUSATZ44,ISZUSATZ45,ISZUSATZ46,ISZUSATZ47,ISZUSATZ48,ISZUSATZ49,ISZUSATZ50,KAT_1,KAT_2,KAT_3,KAT_4,KAT_5,,BF_LILA,BF_ROSA,BF_WEISS,BF_GELB,BF_ORANGE,BF_ROT,BF_MEHRF,BF_GRUEN,BF_PINK,BF_SCHWARZ,FF_BLAU,FF_ORANGE,FF_WEISS,FF_GELB,FF_ROT,FF_GRUEN,FF_SCHWARZ,ISZUSATZ51,ISZUSATZ52,ISZUSATZ53,ISZUSATZ54,ISZUSATZ55,ISZUSATZ56,LAUB_IG,LAUB_LA,LAUB_WG,BESTELLT,GELIEFERT,OFFEN,LIETERMIN,GEWICHT,VERF_BEST,MARKE
    return [
        ":id" => $row["WM"] . $row["NUMMER"],  //id varchar(255),
        ":mwst" =>  get_steuer($row["MWST"]) ,   //mwst varchar(255),
        ":RES_ZEIT" => result_integer($row["RES_ZEIT"]) ,    // RES_ZEIT integer,
        ":WARENGR" => result_integer($row["WARENGR"]) ,    //WARENGR integer,
        ":INTERNET" => result_bool($row["INTERNET"]) ,    //INTERNET bool,
        ":SAISON_KZ" => result_varchar($row["SAISON_KZ"]) ,     //SAISON_KZ varchar(255),
        ":title" => result_varchar($row["BANAME1"])  ,     //title varchar(255), #BANAME1
        ":description" => result_varchar($row["BANAME2"] . $row["BANAME3"])  ,     //description varchar(255) , #BANAME2 und BANAME3
        ":DKZ1" =>  result_integer($row["DKZ1"]) ,    //DKZ1 integer,
        ":DKZ2" =>  result_integer($row["DKZ2"]) ,    //DKZ2 integer,
        ":DKZ3" =>  result_integer($row["DKZ3"]) ,    //DKZ3 integer,
        ":SYS_ANLAGE" => result_date($row["SYS_ANLAGE"])  ,     //SYS_ANLAGE datetime, # muss syntax wechseln
        ":DKZ4" =>  result_integer($row["DKZ4"]) ,    //DKZ4 integer,
        ":PREIS_GRP" => result_varchar($row["PREIS_GRP"]) ,     //PREIS_GRP varchar(255),
        ":BIS_MENGE" => result_integer($row["BIS_MENGE"]) ,    //BIS_MENGE integer,
        ":price" => get_last_prise($row) ,                   //price decimal,  #VKVALIDD1	VKPREIS1	VKVALIDD2	VKPREIS2
        ":STKPREIS" => result_bool($row["STKPREIS"]) ,    //STKPREIS bool,# falsch = 0 , wahr ? true
        ":GP_MENGE" => result_integer($row["GP_MENGE"]) ,    //GP_MENGE integer,
        ":GP_EINHEIT" => result_integer($row["GP_EINHEIT"]) ,    //GP_EINHEIT integer,
        ":PACK_MENGE" => result_integer($row["PACK_MENGE"]) ,    //PACK_MENGE integer,
        ":RABATT" => result_bool($row["RABATT"]) ,    //RABATT bool, # falsch = 0 , wahr ? true
        ":VKPREIS3" => result_bool($row["VKPREIS3"]) ,    //VKPREIS3 bool,
        ":VKVALIDD3" => result_bool($row["VKVALIDD3"]) ,    //VKVALIDD3 bool,
        ":VKBISDT1" => result_integer($row["VKBISDT1"]) ,    //VKBISDT1 integer,
        ":VKBISDT2" => result_date($row["VKBISDT2"]) ,    //VKBISDT2 date,
        ":VKBISDT3" => result_date($row["VKBISDT3"]) ,    //VKBISDT3 date,
        ":STAFRABATT" => result_date($row["STAFRABATT"]) ,    //STAFRABATT date, #isnull
        ":FAKTOR" => result_date($row["FAKTOR"]) ,    //FAKTOR date,
        ":ISZUSATZ00" => result_varchar($row["ISZUSATZ00"]) ,     //ISZUSATZ00 varchar(255),
        ":ISZUSATZ01" => result_varchar($row["ISZUSATZ01"]) ,     //ISZUSATZ01 varchar(255),
        ":ISZUSATZ02" => result_varchar($row["ISZUSATZ02"]) ,     //ISZUSATZ02 varchar(255),
        ":ISZUSATZ03" => result_varchar($row["ISZUSATZ03"]) ,     //ISZUSATZ03 varchar(255),
        ":ISZUSATZ04" => result_varchar($row["ISZUSATZ04"]) ,     //ISZUSATZ04 varchar(255),
        ":ISZUSATZ05" => result_varchar($row["ISZUSATZ05"]) ,     //ISZUSATZ05 varchar(255),
        ":ISZUSATZ06" => result_varchar($row["ISZUSATZ06"]) ,     //ISZUSATZ06 varchar(255),
        ":ISZUSATZ07" => result_varchar($row["ISZUSATZ07"]) ,     //ISZUSATZ07 varchar(255),
        ":ISZUSATZ08" => result_varchar($row["ISZUSATZ08"]) ,     //ISZUSATZ08 varchar(255),
        ":ISZUSATZ09" => result_varchar($row["ISZUSATZ09"]) ,     //ISZUSATZ09 varchar(255),
        ":ISZUSATZ10" => result_varchar($row["ISZUSATZ10"]) ,     //ISZUSATZ10	Varchar(255),
        ":ISZUSATZ11" => result_varchar($row["ISZUSATZ11"]) ,     //ISZUSATZ11	Varchar(255),
        ":ISZUSATZ12" => result_varchar($row["ISZUSATZ12"]) ,     //ISZUSATZ12	Varchar(255),
        ":ISZUSATZ13" => result_varchar($row["ISZUSATZ13"]) ,     //ISZUSATZ13	Varchar(255),
        ":ISZUSATZ14" => result_varchar($row["ISZUSATZ14"]) ,     //ISZUSATZ14	Varchar(255),
        ":ISZUSATZ15" => result_varchar($row["ISZUSATZ15"]) ,     //ISZUSATZ15	Varchar(255),
        ":ISZUSATZ16" => result_varchar($row["ISZUSATZ16"]) ,     //ISZUSATZ16	Varchar(255),
        ":ISZUSATZ17" => result_varchar($row["ISZUSATZ17"]) ,     //ISZUSATZ17	Varchar(255),
        ":ISZUSATZ18" => result_varchar($row["ISZUSATZ18"]) ,     //ISZUSATZ18	Varchar(255),
        ":ISZUSATZ19" => result_varchar($row["ISZUSATZ19"]) ,     //ISZUSATZ19	Varchar(255),
        ":ISZUSATZ20" => result_varchar($row["ISZUSATZ20"]) ,     //ISZUSATZ20	Varchar(255),
        ":ISZUSATZ21" => result_varchar($row["ISZUSATZ21"]) ,     //ISZUSATZ21	Varchar(255),
        ":ISZUSATZ22" => result_varchar($row["ISZUSATZ22"]) ,     //ISZUSATZ22	Varchar(255),
        ":ISZUSATZ23" => result_varchar($row["ISZUSATZ23"]) ,     //ISZUSATZ23	Varchar(255),
        ":ISZUSATZ24" => result_varchar($row["ISZUSATZ24"]) ,     //ISZUSATZ24	Varchar(255),
        ":ISZUSATZ25" => result_varchar($row["ISZUSATZ25"]) ,     //ISZUSATZ25	Varchar(255),
        ":ISZUSATZ26" => result_varchar($row["ISZUSATZ26"]) ,     //ISZUSATZ26	Varchar(255),
        ":ISZUSATZ27" => result_varchar($row["ISZUSATZ27"]) ,     //ISZUSATZ27	Varchar(255),
        ":ISZUSATZ28" => result_varchar($row["ISZUSATZ28"]) ,     //ISZUSATZ28	Varchar(255),
        ":ISZUSATZ29" => result_varchar($row["ISZUSATZ29"]) ,     //ISZUSATZ29	Varchar(255),
        ":ISZUSATZ30" => result_varchar($row["ISZUSATZ30"]) ,     //ISZUSATZ30	Varchar(255),
        ":ISZUSATZ31" => result_varchar($row["ISZUSATZ31"]) ,     //ISZUSATZ31	Varchar(255),
        ":ISZUSATZ32" => result_varchar($row["ISZUSATZ32"]) ,     //ISZUSATZ32	Varchar(255),
        ":ISZUSATZ33" => result_varchar($row["ISZUSATZ33"]) ,     //ISZUSATZ33	Varchar(255),
        ":ISZUSATZ34" => result_varchar($row["ISZUSATZ34"]) ,     //ISZUSATZ34	Varchar(255),
        ":ISZUSATZ35" => result_varchar($row["ISZUSATZ35"]) ,     //ISZUSATZ35	Varchar(255),
        ":ISZUSATZ36" => result_varchar($row["ISZUSATZ36"])  ,     //ISZUSATZ36	Varchar(255),  # löchen "cm"
        ":ISZUSATZ37" => result_varchar($row["ISZUSATZ37"]) ,     //ISZUSATZ37	Varchar(255),
        ":ISZUSATZ38" => result_varchar($row["ISZUSATZ38"]) ,     //ISZUSATZ38	Varchar(255),
        ":ISZUSATZ39" => result_varchar($row["ISZUSATZ39"]) ,     //ISZUSATZ39	Varchar(255),
        ":ISZUSATZ40" => result_varchar($row["ISZUSATZ40"]) ,     //ISZUSATZ40	Varchar(255),
        ":ISZUSATZ41" => result_varchar($row["ISZUSATZ41"]) ,     //ISZUSATZ41	Varchar(255),
        ":ISZUSATZ42" => result_varchar($row["ISZUSATZ42"]) ,     //ISZUSATZ42	Varchar(255),
        ":ISZUSATZ43" => result_varchar($row["ISZUSATZ43"]) ,     //ISZUSATZ43	Varchar(255),
        ":ISZUSATZ44" => result_varchar($row["ISZUSATZ44"]) ,     //ISZUSATZ44	Varchar(255),
        ":ISZUSATZ45" => result_varchar($row["ISZUSATZ45"]) ,     //ISZUSATZ45	Varchar(255),
        ":ISZUSATZ46" => result_varchar($row["ISZUSATZ46"]) ,     //ISZUSATZ46	Varchar(255),
        ":ISZUSATZ47" => result_varchar($row["ISZUSATZ47"]) ,     //ISZUSATZ47	Varchar(255),
        ":ISZUSATZ48" => result_varchar($row["ISZUSATZ48"]) ,     //ISZUSATZ48	Varchar(255),
        ":ISZUSATZ49" => result_varchar($row["ISZUSATZ49"]) ,     //ISZUSATZ49	Varchar(255),
        ":ISZUSATZ50" => result_varchar($row["ISZUSATZ50"]) ,     //ISZUSATZ50	Varchar(255),
        ":ISZUSATZ51" => result_integer($row["ISZUSATZ51"]) ,    //ISZUSATZ51 integer , # monthes id
        ":ISZUSATZ52" => result_integer($row["ISZUSATZ52"]) ,    //ISZUSATZ52 integer, # monthes id
        ":ISZUSATZ53" => result_integer($row["ISZUSATZ53"]) ,    //ISZUSATZ53 integer, # monthes id
        ":ISZUSATZ54" => result_varchar($row["ISZUSATZ54"]) ,    //ISZUSATZ54 VARCHAR(255),
        ":ISZUSATZ55" => result_integer($row["ISZUSATZ55"]) ,    //ISZUSATZ55 integer, # löchen "cm"
        ":ISZUSATZ56" => result_integer($row["ISZUSATZ56"]) ,    //ISZUSATZ56 integer, # löchen "cm"
        ":KAT_1" => result_varchar($row["KAT_1"]) ,     //KAT_1	Varchar(255),
        ":KAT_2" => result_varchar($row["KAT_2"]) ,     //KAT_2	Varchar(255),
        ":KAT_3" => result_varchar($row["KAT_3"]) ,     //KAT_3	Varchar(255),
        ":KAT_4" => result_varchar($row["KAT_4"]) ,     //KAT_4	Varchar(255),
        ":KAT_5" => result_varchar($row["KAT_5"]) ,     //KAT_5	Varchar(255),
        ":pflanzen_type" => result_integer($row["pflanzen_type"]) ,    //pflanzen_type integer, # id von : halbschattig, sonnig
        ":LAUB_IG" => result_bool($row["LAUB_IG"]) ,    //LAUB_IG bool,
        ":LAUB_LA" => result_bool($row["LAUB_LA"]) ,    //LAUB_LA bool,
        ":LAUB_WG" => result_bool($row["LAUB_WG"]) ,    //LAUB_WG bool,
        ":BESTELLT" => result_integer($row["BESTELLT"]) ,    //BESTELLT integer,
        ":GELIEFERT" => result_bool($row["GELIEFERT"]) ,    //GELIEFERT bool,
        ":OFFEN" => result_bool($row["OFFEN"]) ,    //OFFEN bool,
        ":LIETERMIN" => result_date($row["LIETERMIN"]) ,    //   IN date,
        ":GEWICHT" => result_integer($row["GEWICHT"]) ,    //GEWICHT float,
        ":VERF_BEST" => (float)$row["VERF_BEST"] ,    //VERF_BEST decimal,
        ":MARKE" => result_varchar($row["MARKE"]) ,     //MARKE VARCHAR(255)
        ":BF_id" => get_BF_value($row) ,    //BF_id integer, # BF_BLAU	BF_LILA	BF_ROSA	BF_WEISS	BF_GELB	BF_ORANGE	BF_ROT	BF_MEHRF	BF_GRUEN	BF_PINK	BF_SCHWARZ
        ":FF_id" => get_FF_value($row)
    ];    //FF_id integer, # 	FF_BLAU	FF_ORANGE	FF_WEISS	FF_GELB	FF_ROT	FF_GRUEN	FF_SCHWARZ

}

/**
 * /** return the colors value
 * @param $row
 *
 * @return int
 */
function get_BF_value($row)
{
    if($row["BF_BLAU"]      == "Wahr")  { return 1  ;}
    if($row["BF_LILA"]      == "Wahr")  { return 2  ;}
    if($row["BF_ROSA"]      == "Wahr")  { return 3  ;}
    if($row["BF_WEISS"]     == "Wahr")  { return 4  ;}
    if($row["BF_GELB"]      == "Wahr")  { return 5  ;}
    if($row["BF_ORANGE"]    == "Wahr")  { return 6  ;}
    if($row["BF_ROT"]       == "Wahr")  { return 7  ;}
    if($row["BF_MEHRF"]     == "Wahr")  { return 8  ;}
    if($row["BF_GRUEN"]     == "Wahr")  { return 9  ;}
    if($row["BF_PINK"]      == "Wahr")  { return 10 ;}
    if($row["BF_SCHWARZ"]   == "Wahr")  { return 11 ;}
    if($row["BF_BLAU"]      == "Wahr")  { return 12 ;}
    if($row["BF_ORANGE"]    == "Wahr")  { return 13 ;}
    if($row["BF_WEISS"]     == "Wahr")  { return 14 ;}
    if($row["BF_GELB"]      == "Wahr")  { return 15 ;}
    if($row["BF_ROT"]       == "Wahr")  { return 16 ;}
    if($row["BF_GRUEN"]     == "Wahr")  { return 17 ;}
    if($row["BF_SCHWARZ"]   == "Wahr")  { return 18 ;}
    return 0;
}

/**
 * return the colors value
 * @param $row
 *
 * @return int
 */
function get_FF_value($row)
{
    if($row["FF_BLAU"]      == "Wahr")  { return 1  ;}
    if($row["FF_LILA"]      == "Wahr")  { return 2  ;}
    if($row["FF_ROSA"]      == "Wahr")  { return 3  ;}
    if($row["FF_WEISS"]     == "Wahr")  { return 4  ;}
    if($row["FF_GELB"]      == "Wahr")  { return 5  ;}
    if($row["FF_ORANGE"]    == "Wahr")  { return 6  ;}
    if($row["FF_ROT"]       == "Wahr")  { return 7  ;}
    if($row["FF_MEHRF"]     == "Wahr")  { return 8  ;}
    if($row["FF_GRUEN"]     == "Wahr")  { return 9  ;}
    if($row["FF_PINK"]      == "Wahr")  { return 10 ;}
    if($row["FF_SCHWARZ"]   == "Wahr")  { return 11 ;}
    if($row["FF_BLAU"]      == "Wahr")  { return 12 ;}
    if($row["FF_ORANGE"]    == "Wahr")  { return 13 ;}
    if($row["FF_WEISS"]     == "Wahr")  { return 14 ;}
    if($row["FF_GELB"]      == "Wahr")  { return 15 ;}
    if($row["FF_ROT"]       == "Wahr")  { return 16 ;}
    if($row["FF_GRUEN"]     == "Wahr")  { return 17 ;}
    if($row["FF_SCHWARZ"]   == "Wahr")  { return 18 ;}
    return 0;
}

/**
 * convert the encoding to UTF-8
 * @param $str
 *
 * @return false|string
 */
function convert( $str ) {
    return iconv( "Windows-1252", "UTF-8", $str );
}


/**
 * read the csv file and and save the values in $values_list
 */
function read_file()
{
    // scv datei complet path
    $file="/var/www/html/importcsvfile/Artikeldaten-Shop.csv";
    global $first_row;
    global $values_list;
    $row = 1;

    if (($handle = fopen($file, "r")) !== FALSE) {
        while (($data = fgetcsv($handle, 10000000, ";")) !== FALSE) {

            $data = array_map( "convert", $data );

            $num = count($data);

//            if(strpos($data[1], "TEST") !== false)
//            {
//                echo "stop";
//            }

            if($first_row == null)
            {
                $first_row = $data;
                continue;
            }

            $row++;
            $array = array();
            for ($c = 0; $c < $num; $c++) {
                $array[$first_row[$c]] = $data[$c];
            }

            $the_fist_time = true;
            //$row["WM"] . $row["NUMMER"]
            foreach ($values_list as $key => $item){
                if($item["NUMMER"] == $array["NUMMER"]){

                    // ich bin hier und ich muss prise table machen
                    $the_fist_time = false;
                    $values_list[$key] = $array;
                    break;
                }
            }

            if($the_fist_time){
                array_push($values_list, $array);
            }

            if(count($values_list) > 530){
                break;
            }
            echo ".";
        }

        fclose($handle);
        echo PHP_EOL . 'Read FILE complete' . PHP_EOL;
    }
}


/**
 * Home function , start the code from her
 */
function home()
{
    // read the file and save the values in $value_list
    read_file();

    $servername = "localhost";
    $username = "admin";
    $password = "1";
    $dbname = "csvimport";

    try{
        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password,
            array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));

    }catch (Exception $e){ echo "error database connection"; return; }

    global $values_list;
    $sqlCommand = $conn->prepare('INSERT INTO article(id, mwst, RES_ZEIT, WARENGR, INTERNET, SAISON_KZ, title, description, DKZ1, DKZ2, DKZ3, SYS_ANLAGE, DKZ4, PREIS_GRP, BIS_MENGE, price, STKPREIS, GP_MENGE, GP_EINHEIT, PACK_MENGE, RABATT, VKPREIS3, VKVALIDD3, VKBISDT1, VKBISDT2, VKBISDT3, STAFRABATT, FAKTOR, ISZUSATZ00, ISZUSATZ01, ISZUSATZ02, ISZUSATZ03, ISZUSATZ04, ISZUSATZ05, ISZUSATZ06, ISZUSATZ07, ISZUSATZ08, ISZUSATZ09, ISZUSATZ10, ISZUSATZ11, ISZUSATZ12, ISZUSATZ13, ISZUSATZ14, ISZUSATZ15, ISZUSATZ16, ISZUSATZ17, ISZUSATZ18, ISZUSATZ19, ISZUSATZ20, ISZUSATZ21, ISZUSATZ22, ISZUSATZ23, ISZUSATZ24, ISZUSATZ25, ISZUSATZ26, ISZUSATZ27, ISZUSATZ28, ISZUSATZ29, ISZUSATZ30, ISZUSATZ31, ISZUSATZ32, ISZUSATZ33, ISZUSATZ34, ISZUSATZ35, ISZUSATZ36, ISZUSATZ37, ISZUSATZ38, ISZUSATZ39, ISZUSATZ40, ISZUSATZ41, ISZUSATZ42, ISZUSATZ43, ISZUSATZ44, ISZUSATZ45, ISZUSATZ46, ISZUSATZ47, ISZUSATZ48, ISZUSATZ49, ISZUSATZ50, ISZUSATZ51, ISZUSATZ52, ISZUSATZ53, ISZUSATZ54, ISZUSATZ55, ISZUSATZ56, KAT_1, KAT_2, KAT_3, KAT_4, KAT_5, pflanzen_type, LAUB_IG, LAUB_LA, LAUB_WG, BESTELLT, GELIEFERT, OFFEN, LIETERMIN, GEWICHT, VERF_BEST, MARKE, BF_id, FF_id) 
                      VALUES (:id, :mwst, :RES_ZEIT, :WARENGR, :INTERNET, :SAISON_KZ, :title, :description, :DKZ1, :DKZ2, :DKZ3, :SYS_ANLAGE, :DKZ4, :PREIS_GRP, :BIS_MENGE, :price, :STKPREIS, :GP_MENGE, :GP_EINHEIT, :PACK_MENGE, :RABATT, :VKPREIS3, :VKVALIDD3, :VKBISDT1, :VKBISDT2, :VKBISDT3, :STAFRABATT, :FAKTOR, :ISZUSATZ00, :ISZUSATZ01, :ISZUSATZ02, :ISZUSATZ03, :ISZUSATZ04, :ISZUSATZ05, :ISZUSATZ06, :ISZUSATZ07, :ISZUSATZ08, :ISZUSATZ09, :ISZUSATZ10, :ISZUSATZ11, :ISZUSATZ12, :ISZUSATZ13, :ISZUSATZ14, :ISZUSATZ15, :ISZUSATZ16, :ISZUSATZ17, :ISZUSATZ18, :ISZUSATZ19, :ISZUSATZ20, :ISZUSATZ21, :ISZUSATZ22, :ISZUSATZ23, :ISZUSATZ24, :ISZUSATZ25, :ISZUSATZ26, :ISZUSATZ27, :ISZUSATZ28, :ISZUSATZ29, :ISZUSATZ30, :ISZUSATZ31, :ISZUSATZ32, :ISZUSATZ33, :ISZUSATZ34, :ISZUSATZ35, :ISZUSATZ36, :ISZUSATZ37, :ISZUSATZ38, :ISZUSATZ39, :ISZUSATZ40, :ISZUSATZ41, :ISZUSATZ42, :ISZUSATZ43, :ISZUSATZ44, :ISZUSATZ45, :ISZUSATZ46, :ISZUSATZ47, :ISZUSATZ48, :ISZUSATZ49, :ISZUSATZ50, :ISZUSATZ51, :ISZUSATZ52, :ISZUSATZ53, :ISZUSATZ54, :ISZUSATZ55, :ISZUSATZ56, :KAT_1, :KAT_2, :KAT_3, :KAT_4, :KAT_5, :pflanzen_type, :LAUB_IG, :LAUB_LA, :LAUB_WG, :BESTELLT, :GELIEFERT, :OFFEN, :LIETERMIN, :GEWICHT, :VERF_BEST, :MARKE, :BF_id, :FF_id)');

    $success = 0;
    $errors = 0;

    foreach ($values_list as $row)
    {
        $values = change_value($row);

        try{

            if($values[":id"] != "")
            {
                if(strpos($values[":id"], "TEST") !== false)
                {
                    $xxxxxxxxxxxxxxxx =  "stop";
                }

                if(!$sqlCommand->execute($values)){
                    echo PHP_EOL . "error in id : " . $row["WM"] . $row["NUMMER"] . PHP_EOL;
                    echo implode(';', $values) . PHP_EOL;
                    ++$errors;
                } else {
                    ++$success;
                }
            }
        }catch(PDOException $e){
            echo PHP_EOL . $e->getMessage() . PHP_EOL. "error in row : " .$row["id"] .PHP_EOL;
        }
    }

    echo PHP_EOL . sprintf('Wrote succesfully %s lines from %s rows (%s errors)', $success, count($values_list), $errors) . PHP_EOL;
}

/**
 * call the home function and start the script from her
 */
home();

// clear the connection
$conn = null;
