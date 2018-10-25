<?php
header('Content-Encoding: UTF-8');
$first_row = array();       // colmun name
$values_list = array();     // list for all values

$servername = "localhost";
$username = "admin";
$password = "1";
$dbname = "shopware";

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
    $dbname = "shopware";

    try{
        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    }catch (Exception $e){ echo "error database connection"; return; }

    global $values_list;
    foreach ($values_list as $row)
    {
        # mwst,RES_ZEIT,WARENGR,INTERNET,SAISON_KZ,title,description,DKZ1,DKZ2,DKZ3,SYS_ANLAGE,DKZ4,PREIS_GRP,BIS_MENGE,price,STKPREIS,GP_MENGE,GP_EINHEIT,PACK_MENGE,RABATT,VKPREIS3,VKVALIDD3,VKBISDT1,VKBISDT2,VKBISDT3,STAFRABATT,FAKTOR,ISZUSATZ00,ISZUSATZ01,ISZUSATZ02,ISZUSATZ03,ISZUSATZ04,ISZUSATZ05,ISZUSATZ06,ISZUSATZ07,ISZUSATZ08,ISZUSATZ09,ISZUSATZ10,ISZUSATZ11,ISZUSATZ12,ISZUSATZ13,ISZUSATZ14,ISZUSATZ15,ISZUSATZ16,ISZUSATZ17,ISZUSATZ18,ISZUSATZ19,ISZUSATZ20,ISZUSATZ21,ISZUSATZ22,ISZUSATZ23,ISZUSATZ24,ISZUSATZ25,ISZUSATZ26,ISZUSATZ27,ISZUSATZ28,ISZUSATZ29,ISZUSATZ30,ISZUSATZ31,ISZUSATZ32,ISZUSATZ33,ISZUSATZ34,ISZUSATZ35,ISZUSATZ36,ISZUSATZ37,ISZUSATZ38,ISZUSATZ39,ISZUSATZ40,ISZUSATZ41,ISZUSATZ42,ISZUSATZ43,ISZUSATZ44,ISZUSATZ45,ISZUSATZ46,ISZUSATZ47,ISZUSATZ48,ISZUSATZ49,ISZUSATZ50,KAT_1,KAT_2,KAT_3,KAT_4,KAT_5,BF_id,ISZUSATZ51,ISZUSATZ52,ISZUSATZ53,ISZUSATZ54,pflanzen_type,ISZUSATZ55,ISZUSATZ56,LAUB_IG,LAUB_LA,LAUB_WG,BESTELLT,GELIEFERT,OFFEN,LIETERMIN,GEWICHT,VERF_BEST,MARKE
        $values = change_value($row);

        $sql = "INSERT INTO article (id, mwst, RES_ZEIT,WARENGR,INTERNET,SAISON_KZ,title,description,DKZ1,DKZ2,DKZ3,SYS_ANLAGE,DKZ4,PREIS_GRP,BIS_MENGE,price,STKPREIS,GP_MENGE,GP_EINHEIT,PACK_MENGE,RABATT,VKPREIS3,VKVALIDD3,VKBISDT1,VKBISDT2,VKBISDT3,STAFRABATT,FAKTOR,ISZUSATZ00,ISZUSATZ01,ISZUSATZ02,ISZUSATZ03,ISZUSATZ04,ISZUSATZ05,ISZUSATZ06,ISZUSATZ07,ISZUSATZ08,ISZUSATZ09,ISZUSATZ10,ISZUSATZ11,ISZUSATZ12,ISZUSATZ13,ISZUSATZ14,ISZUSATZ15,ISZUSATZ16,ISZUSATZ17,ISZUSATZ18,ISZUSATZ19,ISZUSATZ20,ISZUSATZ21,ISZUSATZ22,ISZUSATZ23,ISZUSATZ24,ISZUSATZ25,ISZUSATZ26,ISZUSATZ27,ISZUSATZ28,ISZUSATZ29,ISZUSATZ30,ISZUSATZ31,ISZUSATZ32,ISZUSATZ33,ISZUSATZ34,ISZUSATZ35,ISZUSATZ36,ISZUSATZ37,ISZUSATZ38,ISZUSATZ39,ISZUSATZ40,ISZUSATZ41,ISZUSATZ42,ISZUSATZ43,ISZUSATZ44,ISZUSATZ45,ISZUSATZ46,ISZUSATZ47,ISZUSATZ48,ISZUSATZ49,ISZUSATZ50,KAT_1,KAT_2,KAT_3,KAT_4,KAT_5,BF_id,ISZUSATZ51,ISZUSATZ52,ISZUSATZ53,ISZUSATZ54,pflanzen_type,ISZUSATZ55,ISZUSATZ56,LAUB_IG,LAUB_LA,LAUB_WG,BESTELLT,GELIEFERT,OFFEN,LIETERMIN,GEWICHT,VERF_BEST,MARKE) VALUES ('$values')";

        if ($conn->query($sql) === TRUE) {
            echo "New record created successfully";
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    }
}

/**
 * @param $row
 *
 * @return string
 */
function change_value($row)
{
    $array_ignore = ["NUMMER", "WM"];
    $result = $row["WM"] . $row["NUMMER"] . ",";

    foreach ($row as $key => $cn){
        // if result don#t need change , it will be save
        if ($key == "mwst"){
            if($cn == "1B"){ $cn = "7%"; } elseif ($cn == "1A"){ $cn = "19%"; } else{ $cn = "0%"; }
        }

        // if not in ignore list , add to query
        if (!in_array($key, $array_ignore)){
            // save result
            $result = $result . $cn . ",";
        }


//        break;
    }

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