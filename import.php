<?php
header('Content-Encoding: UTF-8');

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

$first_row = array();
$values_list = array();
home();

function home()
{
    read_file();

}

function read_file()
{
    $file="/var/www/html/importcsvfile/Artikeldaten-Shop.csv";
    global $first_row;
    global $values_list;
    $row = 1;

    if (($handle = fopen($file, "r")) !== FALSE) {
        while (($data = fgetcsv($handle, 1000000, ";")) !== FALSE) {
            //$data = iconv( "Windows-1252", "UTF-8", $data );
            $num = count($data);

            if($first_row == null)
            {
                $first_row = $data;
                continue;
            }
//            echo "<p> $num fields in line $row: <br /></p>\n";

            $row++;
            $array = array();
            for ($c = 0; $c < $num; $c++) {
                $array[$first_row[$c]] = $data[$c];
            }


            array_push($values_list, $array);
//            echo "xx";
        }
        fclose($handle);
    }
}


$conn = null;
?>

Array
(
    $first_row[$c] => $data[$c]
)
