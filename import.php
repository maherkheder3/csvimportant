<?php
//header('Content-Encoding: UTF-8');
$first_row = array();       // colmun name
$values_list = array();     // list for all values

$servername = "localhost";
$username = "admin";
$password = "1";
$dbname = "csvimport";


try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    // set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "connicatien is ok".PHP_EOL;
}
catch(PDOException $e)
{
    echo $sql . "<br>" . $e->getMessage();
}




function home()
{
    read_file();

    $servername = "localhost";
    $username = "admin";
    $password = "1";
    $dbname = "csvimport";

    try{
        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    }catch (Exception $e){ echo "error database connection"; return; }

    global $values_list;
    foreach ($values_list as $row)
    {
        $values = change_value($row);

        $sql = "INSERT INTO article (id, mwst, RES_ZEIT, WARENGR, INTERNET, SAISON_KZ, title, description, DKZ1, DKZ2, DKZ3, SYS_ANLAGE, DKZ4, PREIS_GRP, BIS_MENGE, price, STKPREIS, GP_MENGE, GP_EINHEIT, PACK_MENGE, RABATT, VKPREIS3, VKVALIDD3, VKBISDT1, VKBISDT2, VKBISDT3, STAFRABATT, FAKTOR, ISZUSATZ00, ISZUSATZ01, ISZUSATZ02, ISZUSATZ03, ISZUSATZ04, ISZUSATZ05, ISZUSATZ06, ISZUSATZ07, ISZUSATZ08, ISZUSATZ09, ISZUSATZ10, ISZUSATZ11, ISZUSATZ12, ISZUSATZ13, ISZUSATZ14, ISZUSATZ15, ISZUSATZ16, ISZUSATZ17, ISZUSATZ18, ISZUSATZ19, ISZUSATZ20, ISZUSATZ21, ISZUSATZ22, ISZUSATZ23, ISZUSATZ24, ISZUSATZ25, ISZUSATZ26, ISZUSATZ27, ISZUSATZ28, ISZUSATZ29, ISZUSATZ30, ISZUSATZ31, ISZUSATZ32, ISZUSATZ33, ISZUSATZ34, ISZUSATZ35, ISZUSATZ36, ISZUSATZ37, ISZUSATZ38, ISZUSATZ39, ISZUSATZ40, ISZUSATZ41, ISZUSATZ42, ISZUSATZ43, ISZUSATZ44, ISZUSATZ45, ISZUSATZ46, ISZUSATZ47, ISZUSATZ48, ISZUSATZ49, ISZUSATZ50, ISZUSATZ51, ISZUSATZ52, ISZUSATZ53, ISZUSATZ54, ISZUSATZ55, ISZUSATZ56, KAT_1, KAT_2, KAT_3, KAT_4, KAT_5, pflanzen_type, LAUB_IG, LAUB_LA, LAUB_WG, BESTELLT, GELIEFERT, OFFEN, LIETERMIN, GEWICHT, VERF_BEST, MARKE, BF_id, FF_id) VALUES ($values)";

        try{
            $conn->exec($sql);
            echo PHP_EOL . "install is ok in row : " . $row["NUMMER"] . $row["WM"];
        }catch(PDOException $e){
            echo PHP_EOL . $sql . PHP_EOL . $e->getMessage() . PHP_EOL. "error in row : " .$row["id"] .PHP_EOL;
        }

    }
}

// if integer , with monthes
function result_integer($cn){
    if($cn == "" || $cn == " ")
    {
        return 0;
    }
    elseif(is_numeric($cn))
    {
        return $cn;
    }
    else{
        try{
            // if it is monthe
            $month_number = date('m', strtotime($cn));
            return str_replace("0", "", $month_number);
        }catch (Exception $e)
        {
            // if not number or white space or month
        }
    }

    echo PHP_EOL . "cann't convert to ineger : " . $cn . PHP_EOL;
    return 0;
}

// if boolean
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

// change data form
function result_date($cn){
    if($cn == "" || $cn == "0" || $cn == " ")
    {
        return "NULL";
    }
    return "'" . date("Y.d.m", strtotime($cn)) . "'" ;
}

// change steuter form
function get_steuer($cn){
    if($cn == "1B"){ $cn = "7%"; } elseif ($cn == "1A"){ $cn = "19%"; } else{ $cn = "0%"; }
    return "'" . $cn . "'" ;
}

/**
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
        $gg = str_replace(",", ".", $row["VKPREIS2"]);
        return floatval(str_replace(",", ".", $row["VKPREIS2"]));
    }
    else{
        return floatval(str_replace(",", ".", $row["VKPREIS1"]));
    }
}

function get_comun_form($cn)
{
    if($cn == "")
    {
        return "NULL,";
    }
    return "'$cn',";
}

/**
 * @param $row
 *
 * @return string
 */
function change_value($row)
{
    //WM,NUMMER,MWST,RES_ZEIT,WARENGR,INTERNET,SAISON_KZ,BANAME1,BANAME2,BANAME3,DKZ1,DKZ2,DKZ3,SYS_ANLAGE,DKZ4,PREIS_GRP,BIS_MENGE,VKVALIDD1,VKPREIS1,VKVALIDD2,VKPREIS2,STKPREIS,GP_MENGE,GP_EINHEIT,PACK_MENGE,RABATT,VKPREIS3,VKVALIDD3,VKBISDT1,VKBISDT2,VKBISDT3,STAFRABATT,FAKTOR,ISZUSATZ00,ISZUSATZ01,ISZUSATZ02,ISZUSATZ03,ISZUSATZ04,ISZUSATZ05,ISZUSATZ06,ISZUSATZ07,ISZUSATZ08,ISZUSATZ09,ISZUSATZ10,ISZUSATZ11,ISZUSATZ12,ISZUSATZ13,ISZUSATZ14,ISZUSATZ15,ISZUSATZ16,ISZUSATZ17,ISZUSATZ18,ISZUSATZ19,ISZUSATZ20,ISZUSATZ21,ISZUSATZ22,ISZUSATZ23,ISZUSATZ24,ISZUSATZ25,ISZUSATZ26,ISZUSATZ27,ISZUSATZ28,ISZUSATZ29,ISZUSATZ30,ISZUSATZ31,ISZUSATZ32,ISZUSATZ33,ISZUSATZ34,ISZUSATZ35,ISZUSATZ36,ISZUSATZ37,ISZUSATZ38,ISZUSATZ39,ISZUSATZ40,ISZUSATZ41,ISZUSATZ42,ISZUSATZ43,ISZUSATZ44,ISZUSATZ45,ISZUSATZ46,ISZUSATZ47,ISZUSATZ48,ISZUSATZ49,ISZUSATZ50,KAT_1,KAT_2,KAT_3,KAT_4,KAT_5,,BF_LILA,BF_ROSA,BF_WEISS,BF_GELB,BF_ORANGE,BF_ROT,BF_MEHRF,BF_GRUEN,BF_PINK,BF_SCHWARZ,FF_BLAU,FF_ORANGE,FF_WEISS,FF_GELB,FF_ROT,FF_GRUEN,FF_SCHWARZ,ISZUSATZ51,ISZUSATZ52,ISZUSATZ53,ISZUSATZ54,ISZUSATZ55,ISZUSATZ56,LAUB_IG,LAUB_LA,LAUB_WG,BESTELLT,GELIEFERT,OFFEN,LIETERMIN,GEWICHT,VERF_BEST,MARKE

    $result = "'" . $row["WM"] . $row["NUMMER"] . "'," .  //id varchar(255),
        get_steuer($row["mwst"]) . ","  .   //mwst varchar(255),
        result_integer($row["RES_ZEIT"]) . "," .    // RES_ZEIT integer,
        result_integer($row["WARENGR"]) . "," .    //WARENGR integer,
        result_bool($row["INTERNET"]) . "," .    //INTERNET bool,
        "'" . $row["SAISON_KZ"]  . "',"  .     //SAISON_KZ varchar(255),
        "'" . $row["BANAME1"]  . "',"  .     //title varchar(255), #BANAME1
        "'" . $row["BANAME2"] . $row["BANAME3"]  . "',"  .     //description varchar(255) , #BANAME2 und BANAME3
        result_integer($row["DKZ1"]) . "," .    //DKZ1 integer,
        result_integer($row["DKZ2"]) . "," .    //DKZ2 integer,
        result_integer($row["DKZ3"]) . "," .    //DKZ3 integer,
        result_date($row["SYS_ANLAGE"])  . ","  .     //SYS_ANLAGE datetime, # muss syntax wechseln
        result_integer($row["DKZ4"]) . "," .    //DKZ4 integer,
        "'" . $row["PREIS_GRP"]  . "',"  .     //PREIS_GRP varchar(255),
        result_integer($row["BIS_MENGE"]) . "," .    //BIS_MENGE integer,
        get_last_prise($row) . "," .                   //price decimal,  #VKVALIDD1	VKPREIS1	VKVALIDD2	VKPREIS2
        result_bool($row["STKPREIS"]) . "," .    //STKPREIS bool,# falsch = 0 , wahr ? true
        result_integer($row["GP_MENGE"]) . "," .    //GP_MENGE integer,
        result_integer($row["GP_EINHEIT"]) . "," .    //GP_EINHEIT integer,
        result_integer($row["PACK_MENGE"]) . "," .    //PACK_MENGE integer,
        result_bool($row["RABATT"]) . "," .    //RABATT bool, # falsch = 0 , wahr ? true
        result_bool($row["VKPREIS3"]) . "," .    //VKPREIS3 bool,
        result_bool($row["VKVALIDD3"]) . "," .    //VKVALIDD3 bool,
        result_date($row["VKBISDT1"]) . "," .    //VKBISDT1 integer,
        result_date($row["VKBISDT2"]) . "," .    //VKBISDT2 date,
        result_date($row["VKBISDT3"]) . "," .    //VKBISDT3 date,
        result_date($row["STAFRABATT"]) . "," .    //STAFRABATT date, #isnull
        result_date($row["FAKTOR"]) . "," .    //FAKTOR date,
        "'" . $row["ISZUSATZ00"]  . "',"  .     //ISZUSATZ00 varchar(255),
        "'" . $row["ISZUSATZ01"]  . "',"  .     //ISZUSATZ01 varchar(255),
        "'" . $row["ISZUSATZ02"]  . "',"  .     //ISZUSATZ02 varchar(255),
        "'" . $row["ISZUSATZ03"]  . "',"  .     //ISZUSATZ03 varchar(255),
        "'" . $row["ISZUSATZ04"]  . "',"  .     //ISZUSATZ04 varchar(255),
        "'" . $row["ISZUSATZ05"]  . "',"  .     //ISZUSATZ05 varchar(255),
        "'" . $row["ISZUSATZ06"]  . "',"  .     //ISZUSATZ06 varchar(255),
        "'" . $row["ISZUSATZ07"]  . "',"  .     //ISZUSATZ07 varchar(255),
        "'" . $row["ISZUSATZ08"]  . "',"  .     //ISZUSATZ08 varchar(255),
        "'" . $row["ISZUSATZ09"]  . "',"  .     //ISZUSATZ09 varchar(255),
        "'" . $row["ISZUSATZ10"]  . "',"  .     //ISZUSATZ10	Varchar(255),
        "'" . $row["ISZUSATZ11"]  . "',"  .     //ISZUSATZ11	Varchar(255),
        "'" . $row["ISZUSATZ12"]  . "',"  .     //ISZUSATZ12	Varchar(255),
        "'" . $row["ISZUSATZ13"]  . "',"  .     //ISZUSATZ13	Varchar(255),
        "'" . $row["ISZUSATZ14"]  . "',"  .     //ISZUSATZ14	Varchar(255),
        "'" . $row["ISZUSATZ15"]  . "',"  .     //ISZUSATZ15	Varchar(255),
        "'" . $row["ISZUSATZ16"]  . "',"  .     //ISZUSATZ16	Varchar(255),
        "'" . $row["ISZUSATZ17"]  . "',"  .     //ISZUSATZ17	Varchar(255),
        "'" . $row["ISZUSATZ18"]  . "',"  .     //ISZUSATZ18	Varchar(255),
        "'" . $row["ISZUSATZ19"]  . "',"  .     //ISZUSATZ19	Varchar(255),
        "'" . $row["ISZUSATZ20"]  . "',"  .     //ISZUSATZ20	Varchar(255),
        "'" . $row["ISZUSATZ21"]  . "',"  .     //ISZUSATZ21	Varchar(255),
        "'" . $row["ISZUSATZ22"]  . "',"  .     //ISZUSATZ22	Varchar(255),
        "'" . $row["ISZUSATZ23"]  . "',"  .     //ISZUSATZ23	Varchar(255),
        "'" . $row["ISZUSATZ24"]  . "',"  .     //ISZUSATZ24	Varchar(255),
        "'" . $row["ISZUSATZ25"]  . "',"  .     //ISZUSATZ25	Varchar(255),
        "'" . $row["ISZUSATZ26"]  . "',"  .     //ISZUSATZ26	Varchar(255),
        "'" . $row["ISZUSATZ27"]  . "',"  .     //ISZUSATZ27	Varchar(255),
        "'" . $row["ISZUSATZ28"]  . "',"  .     //ISZUSATZ28	Varchar(255),
        "'" . $row["ISZUSATZ29"]  . "',"  .     //ISZUSATZ29	Varchar(255),
        "'" . $row["ISZUSATZ30"]  . "',"  .     //ISZUSATZ30	Varchar(255),
        "'" . $row["ISZUSATZ31"]  . "',"  .     //ISZUSATZ31	Varchar(255),
        "'" . $row["ISZUSATZ32"]  . "',"  .     //ISZUSATZ32	Varchar(255),
        "'" . $row["ISZUSATZ33"]  . "',"  .     //ISZUSATZ33	Varchar(255),
        "'" . $row["ISZUSATZ34"]  . "',"  .     //ISZUSATZ34	Varchar(255),
        "'" . $row["ISZUSATZ35"]  . "',"  .     //ISZUSATZ35	Varchar(255),
        "'" . $row["ISZUSATZ36"]  . "',"  .     //ISZUSATZ36	Varchar(255),  # löchen "cm"
        "'" . $row["ISZUSATZ37"]  . "',"  .     //ISZUSATZ37	Varchar(255),
        "'" . $row["ISZUSATZ38"]  . "',"  .     //ISZUSATZ38	Varchar(255),
        "'" . $row["ISZUSATZ39"]  . "',"  .     //ISZUSATZ39	Varchar(255),
        "'" . $row["ISZUSATZ40"]  . "',"  .     //ISZUSATZ40	Varchar(255),
        "'" . $row["ISZUSATZ41"]  . "',"  .     //ISZUSATZ41	Varchar(255),
        "'" . $row["ISZUSATZ42"]  . "',"  .     //ISZUSATZ42	Varchar(255),
        "'" . $row["ISZUSATZ43"]  . "',"  .     //ISZUSATZ43	Varchar(255),
        "'" . $row["ISZUSATZ44"]  . "',"  .     //ISZUSATZ44	Varchar(255),
        "'" . $row["ISZUSATZ45"]  . "',"  .     //ISZUSATZ45	Varchar(255),
        "'" . $row["ISZUSATZ46"]  . "',"  .     //ISZUSATZ46	Varchar(255),
        "'" . $row["ISZUSATZ47"]  . "',"  .     //ISZUSATZ47	Varchar(255),
        "'" . $row["ISZUSATZ48"]  . "',"  .     //ISZUSATZ48	Varchar(255),
        "'" . $row["ISZUSATZ49"]  . "',"  .     //ISZUSATZ49	Varchar(255),
        "'" . $row["ISZUSATZ50"]  . "',"  .     //ISZUSATZ50	Varchar(255),
        result_integer($row["ISZUSATZ51"]) . "," .    //ISZUSATZ51 integer , # monthes id
        result_integer($row["ISZUSATZ52"]) . "," .    //ISZUSATZ52 integer, # monthes id
        result_integer($row["ISZUSATZ53"]) . "," .    //ISZUSATZ53 integer, # monthes id
        "'" . $row["ISZUSATZ54"]  . "'," .    //ISZUSATZ54 VARCHAR(255),
        result_integer($row["ISZUSATZ55"]) . "," .    //ISZUSATZ55 integer, # löchen "cm"
        result_integer($row["ISZUSATZ56"]) . "," .    //ISZUSATZ56 integer, # löchen "cm"
        "'" . $row["KAT_1"]  . "',"  .     //KAT_1	Varchar(255),
        "'" . $row["KAT_2"]  . "',"  .     //KAT_2	Varchar(255),
        "'" . $row["KAT_3"]  . "',"  .     //KAT_3	Varchar(255),
        "'" . $row["KAT_4"]  . "',"  .     //KAT_4	Varchar(255),
        "'" . $row["KAT_5"]  . "',"  .     //KAT_5	Varchar(255),
        result_integer($row["pflanzen_type"]) . "," .    //pflanzen_type integer, # id von : halbschattig, sonnig
        result_bool($row["LAUB_IG"]) . "," .    //LAUB_IG bool,
        result_bool($row["LAUB_LA"]) . "," .    //LAUB_LA bool,
        result_bool($row["LAUB_WG"]) . "," .    //LAUB_WG bool,
        result_integer($row["BESTELLT"]) . "," .    //BESTELLT integer,
        result_bool($row["GELIEFERT"]) . "," .    //GELIEFERT bool,
        result_bool($row["OFFEN"]) . "," .    //OFFEN bool,
        result_date($row["LIETERMIN"]) . "," .    //LIETERMIN DATETIME,
        result_integer($row["GEWICHT"]) . "," .    //GEWICHT float,
        5 . "," .    //VERF_BEST decimal,
        "'" . $row["MARKE"]  . "',"  .     //MARKE VARCHAR(255)
        0 . "," .    //BF_id integer, # BF_BLAU	BF_LILA	BF_ROSA	BF_WEISS	BF_GELB	BF_ORANGE	BF_ROT	BF_MEHRF	BF_GRUEN	BF_PINK	BF_SCHWARZ
        0;    //FF_id integer, # 	FF_BLAU	FF_ORANGE	FF_WEISS	FF_GELB	FF_ROT	FF_GRUEN	FF_SCHWARZ

    $result = str_replace("''", "NULL", $result);
    return $result;

}



function read_file()
{
    // scv datei complet path
    $file="/var/www/html/importcsvfile/Artikeldaten-Shop.csv";
    global $first_row;
    global $values_list;
    $row = 1;

    if (($handle = fopen($file, "r")) !== FALSE) {
        while (($data = fgetcsv($handle, 100000, ";")) !== FALSE) {
            //$data = iconv( "Windows-1252", "UTF-8", $data );
            $num = count($data);

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

            array_push($values_list, $array);

            if(count($values_list) > 10){
                ////////////////////////////////////////////////////////////
                return;
            }
        }
        fclose($handle);
    }
}

home();
$conn = null;
echo PHP_EOL;



//RES_ZEIT"
//WARENGR"
//DKZ1"
//DKZ2"
//DKZ3"
//DKZ4"
//BIS_MENGE"
//GP_MENGE"
//GP_EINHEIT"
//PACK_MENGE"
//VKBISDT1"
//BF_id"
//ISZUSATZ51 i"
//ISZUSATZ52 "
//ISZUSATZ53 "
//pflanzen_type "
//ISZUSATZ55 "
//ISZUSATZ56"
//BESTELLT"


//INTERNET
//RABATT
//VKPREIS3
//VKVALIDD3
//STKPREIS
//LAUB_IG
//LAUB_LA
//LAUB_WG
//GELIEFERT
//OFFEN









//id
//mwst
//RES_ZEIT
//WARENGR
//INTERNET
//SAISON_KZ
//title
//description
//DKZ1
//DKZ2
//DKZ3
//SYS_ANLAGE
//DKZ4
//PREIS_GRP
//BIS_MENGE
//price
//STKPREIS
//GP_MENGE
//GP_EINHEIT
//PACK_MENGE
//RABATT
//VKPREIS3
//VKVALIDD3
//VKBISDT1
//VKBISDT2
//VKBISDT3
//STAFRABATT
//FAKTOR
//ISZUSATZ00
//ISZUSATZ01
//ISZUSATZ02
//ISZUSATZ03
//ISZUSATZ04
//ISZUSATZ05
//ISZUSATZ06
//ISZUSATZ07
//ISZUSATZ08
//ISZUSATZ09
//ISZUSATZ10
//ISZUSATZ11
//ISZUSATZ12
//ISZUSATZ13
//ISZUSATZ14
//ISZUSATZ15
//ISZUSATZ16
//ISZUSATZ17
//ISZUSATZ18
//ISZUSATZ19
//ISZUSATZ20
//ISZUSATZ21
//ISZUSATZ22
//ISZUSATZ23
//ISZUSATZ24
//ISZUSATZ25
//ISZUSATZ26
//ISZUSATZ27
//ISZUSATZ28
//ISZUSATZ29
//ISZUSATZ30
//ISZUSATZ31
//ISZUSATZ32
//ISZUSATZ33
//ISZUSATZ34
//ISZUSATZ35
//ISZUSATZ36
//ISZUSATZ37
//ISZUSATZ38
//ISZUSATZ39
//ISZUSATZ40
//ISZUSATZ41
//ISZUSATZ42
//ISZUSATZ43
//ISZUSATZ44
//ISZUSATZ45
//ISZUSATZ46
//ISZUSATZ47
//ISZUSATZ48
//ISZUSATZ49
//ISZUSATZ50
//ISZUSATZ51
//ISZUSATZ52
//ISZUSATZ53
//ISZUSATZ54
//ISZUSATZ55
//ISZUSATZ56
//KAT_1
//KAT_2
//KAT_3
//KAT_4
//KAT_5
//pflanzen_type
//LAUB_IG
//LAUB_LA
//LAUB_WG
//BESTELLT
//GELIEFERT
//OFFEN
//LIETERMIN
//GEWICHT
//VERF_BEST
//MARKE
//BF_id
//FF_id




