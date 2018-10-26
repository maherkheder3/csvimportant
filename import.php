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
        # mwst,RES_ZEIT,WARENGR,INTERNET,SAISON_KZ,title,description,DKZ1,DKZ2,DKZ3,SYS_ANLAGE,DKZ4,PREIS_GRP,BIS_MENGE,price,STKPREIS,GP_MENGE,GP_EINHEIT,PACK_MENGE,RABATT,VKPREIS3,VKVALIDD3,VKBISDT1,VKBISDT2,VKBISDT3,STAFRABATT,FAKTOR,ISZUSATZ00,ISZUSATZ01,ISZUSATZ02,ISZUSATZ03,ISZUSATZ04,ISZUSATZ05,ISZUSATZ06,ISZUSATZ07,ISZUSATZ08,ISZUSATZ09,ISZUSATZ10,ISZUSATZ11,ISZUSATZ12,ISZUSATZ13,ISZUSATZ14,ISZUSATZ15,ISZUSATZ16,ISZUSATZ17,ISZUSATZ18,ISZUSATZ19,ISZUSATZ20,ISZUSATZ21,ISZUSATZ22,ISZUSATZ23,ISZUSATZ24,ISZUSATZ25,ISZUSATZ26,ISZUSATZ27,ISZUSATZ28,ISZUSATZ29,ISZUSATZ30,ISZUSATZ31,ISZUSATZ32,ISZUSATZ33,ISZUSATZ34,ISZUSATZ35,ISZUSATZ36,ISZUSATZ37,ISZUSATZ38,ISZUSATZ39,ISZUSATZ40,ISZUSATZ41,ISZUSATZ42,ISZUSATZ43,ISZUSATZ44,ISZUSATZ45,ISZUSATZ46,ISZUSATZ47,ISZUSATZ48,ISZUSATZ49,ISZUSATZ50,KAT_1,KAT_2,KAT_3,KAT_4,KAT_5,ISZUSATZ51,ISZUSATZ52,ISZUSATZ53,ISZUSATZ54,pflanzen_type,ISZUSATZ55,ISZUSATZ56,LAUB_IG,LAUB_LA,LAUB_WG,BESTELLT,GELIEFERT,OFFEN,LIETERMIN,GEWICHT,VERF_BEST,MARKE
        $values = change_value($row);

        $sql = "INSERT INTO article (id,mwst,RES_ZEIT,WARENGR,INTERNET,SAISON_KZ,title,description,DKZ1,DKZ2,DKZ3,SYS_ANLAGE,DKZ4,PREIS_GRP,BIS_MENGE,price,STKPREIS,GP_MENGE,GP_EINHEIT,PACK_MENGE,RABATT,VKPREIS3,VKVALIDD3,VKBISDT1,VKBISDT2,VKBISDT3,STAFRABATT,FAKTOR,ISZUSATZ00,ISZUSATZ01,ISZUSATZ02,ISZUSATZ03,ISZUSATZ04,ISZUSATZ05,ISZUSATZ06,ISZUSATZ07,ISZUSATZ08,ISZUSATZ09,ISZUSATZ10,ISZUSATZ11,ISZUSATZ12,ISZUSATZ13,ISZUSATZ14,ISZUSATZ15,ISZUSATZ16,ISZUSATZ17,ISZUSATZ18,ISZUSATZ19,ISZUSATZ20,ISZUSATZ21,ISZUSATZ22,ISZUSATZ23,ISZUSATZ24,ISZUSATZ25,ISZUSATZ26,ISZUSATZ27,ISZUSATZ28,ISZUSATZ29,ISZUSATZ30,ISZUSATZ31,ISZUSATZ32,ISZUSATZ33,ISZUSATZ34,ISZUSATZ35,ISZUSATZ36,ISZUSATZ37,ISZUSATZ38,ISZUSATZ39,ISZUSATZ40,ISZUSATZ41,ISZUSATZ42,ISZUSATZ43,ISZUSATZ44,ISZUSATZ45,ISZUSATZ46,ISZUSATZ47,ISZUSATZ48,ISZUSATZ49,ISZUSATZ50,KAT_1,KAT_2,KAT_3,KAT_4,KAT_5,ISZUSATZ51,ISZUSATZ52,ISZUSATZ53,ISZUSATZ54,pflanzen_type,ISZUSATZ55,ISZUSATZ56,LAUB_IG,LAUB_LA,LAUB_WG,BESTELLT,GELIEFERT,OFFEN,LIETERMIN,GEWICHT,VERF_BEST,MARKE, BF_id, FF_id) VALUES ($values)";

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
    //SYS_ANLAGE => date_created (date)
    $array_ignore = ["NUMMER", "WM", "MWST", "BANAME2", "BANAME3", "BF_BLAU","BF_LILA","BF_ROSA","BF_WEISS","BF_GELB"
                     ,"BF_ORANGE","BF_ROT","BF_MEHRF","BF_GRUEN","BF_PINK","BF_SCHWARZ","FF_BLAU","FF_ORANGE"
                     ,"FF_WEISS","FF_GELB","FF_ROT","FF_GRUEN","FF_SCHWARZ", "VKVALIDD1", "VKPREIS1", "VKVALIDD2",
                     "VKPREIS2"];
    $result = "'" . $row["WM"] . $row["NUMMER"] . "',";

    foreach ($row as $key => $cn){

        // if key in ignore arry , must change value then save in result
        if ($key == "MWST"){
            if($cn == "1B"){ $cn = "7%"; } elseif ($cn == "1A"){ $cn = "19%"; } else{ $cn = "0%"; }
            $result .= get_comun_form($cn);
        }

        if($key == "BANAME2"){ // BANAME2 & BANAME3 => description
            $result .= get_comun_form($row["BANAME2"] . $row["BANAME3"]);
        }

        if($key == "VKVALIDD1")
        {
            $result .= 5 . ",";
        }

        // if not in ignore list , add to query
        if (!in_array($key, $array_ignore)){


            // if bool group
            if(in_array($key, ["INTERNET", "RABATT", "VKPREIS3", "VKVALIDD3", "STKPREIS", "LAUB_IG", "LAUB_LA",
                                   "LAUB_WG", "GELIEFERT", "OFFEN",]))
            {
                $result .= result_bool($cn) . ",";
            }
            // if date type
            elseif(in_array($key, ["SYS_ANLAGE", "VKBISDT2", "VKBISDT3", "STAFRABATT", "FAKTOR", "LIETERMIN"]))
            {
                $date = date("Y.d.m", strtotime($cn));
                $result .= "'" . $date . "',"; // 02.02.1999
            }

            // if is integer colmun
            elseif(in_array($key, ["RES_ZEIT", "WARENGR", "DKZ1", "DKZ2", "DKZ3", "DKZ4", "BIS_MENGE", "GP_MENGE",
                               "GP_EINHEIT", "PACK_MENGE", "VKBISDT1", "ISZUSATZ51 i", "ISZUSATZ52 ",
                               "ISZUSATZ53 ", "pflanzen_type ", "ISZUSATZ55 ", "ISZUSATZ56", "BESTELLT", "VERF_BEST",
                               "GEWICHT"]))
            {
                $result .= result_integer($cn) . ",";
            }
            //if not integer type then save a string
            else{
                //BANAME1 = title , automaitc
                $result .= get_comun_form($cn);
            }
        }
    }


    return $result . "0,0";
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

