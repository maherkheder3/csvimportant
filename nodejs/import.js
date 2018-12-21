console.clear();
var startDate = new Date();

// 24 second    // 36 % schneller als php
console.log("start");


var path = "/var/www/html/importcsvfile/Artikeldaten-Shop.csv";

var fs = require("fs");
var csv = require("fast-csv");
var parse = require('csv-parse');
var mysql = require('mysql');

var con = mysql.createConnection({
    host: "localhost",
    user: "admin",
    password: "1",
    database: "nodecsv"
});

const monthes = ["justStart", "Januar", "Februar", "März", "April", "Mai",
    "Juni", "Juli", "August", "September", "Oktober", "November", "Dezember"];

// drop all tables and create again new tables
// fs.createReadStream("/var/www/html/importcsvfile/sqlquery.sql", "utf8", function(err, data) {
//     console.log(data);
// });


// test the connect
con.connect(function(err) {
    if (err) {
        console.log(err);
        return null;
    }
    console.log("Connected!");
    // console.log(fs.readFileSync('../var/www/html/importcsvfile/sqlquery.sql').toString());

    // con.query("SELECT * FROM colors", function (err, result) {
    //     if (err) throw err;
    //     console.log(result);
    // });
});


/**
 * to quote and return the integer values , ( convert monthes to integer )
 * @param cn
 * @return int
 */
function result_integer(cn){
    if(isNaN(cn) || cn == null || cn === "" || cn === " ")
    {
        return 0;
    }
    else if(/^\d+$/.test(cn))
    {
        return parseInt(cn);
    }
    else{
        try{
            if(cn.indexOf(" cm") !== false )
            {
                var int = cn.replace(" cm", "");
                return parseInt(int, 10);
            }
            // if it is monthe
            int = monthes.indexOf(cn);
            return int ;
        }catch (e)
        {
            // if not number or white space or month
            return 0;
        }
    }
}

/**
 * if boolean
 * @param cn
 *
 * @return int
 */
function result_bool(cn){
    if(cn == "Falsch" || cn == "false" || cn == "0" || cn == "")
    {
        return 0;
    }
    else if(cn == "Wahr" || cn == "true" || cn == "1")
    {
        return 1;
    }
    return 0;
}

/**
 * to quote and return the date values
 * @param cn
 * @return false|null|string
 */
function result_date(cn){
    if(cn == null || cn === "" || cn === "0" || cn === " ")
    {
        return null;
    }
    var mydate = new Date(cn);
    if(isNaN(mydate.getFullYear())){
        return null;
    }
    var str = mydate.getFullYear() + "-" + (mydate.getMonth() + 1) + "-" +  mydate.getDate();
    return str;
}

/**
 *  take and return string ( quote and clear )
 * @param cn
 * @return string
 */
function result_varchar(cn)
{
    if (cn.indexOf(" cm") !== false) {
        cn = cn.replace(" cm", "");
    }
    if(cn == "'" || cn == " " || cn == "")
    {
        return null;
    }
    return cn;
}

/**
 * get and return string ( tax )
 * @param cn
 * @return string
 */
function get_steuer(cn){
    if(cn == "1B"){ cn = "7%"; }
    else if (cn == "1A"){ cn = "19%"; }
    else{ cn = "0%"; }
    return cn ;
}

/**
 * get the prices plan
 * @param row
 *
 * @return float
 */
function get_last_prise(row)
{
    var prise = null;
    try{
        // VKVALIDD1	VKPREIS1	VKVALIDD2	VKPREIS2
        //Wenn VKVALIDD2 > VKVALIDD1 muss VKPREIS2 => price; ansonsten VKPREIS1 => price (float)
        if(new Date(row.VKVALIDD2) > new Date(row.VKVALIDD1))
        {
            prise = row.VKPREIS2.replace(",", ".");
        }
        else{
            prise = row.VKPREIS1.replace(",", ".");
        }
    }catch (e) {
        console.log(prise + "-" + row);
        
    }

    if(prise === "")
    {
        prise = null
    }
    return prise;
}


/**
 * /** return the colors value
 * @param row
 *
 * @return int
 */
function get_BF_value(row)
{
    if(row["BF_BLAU"]      == "Wahr")  { return 1  ;}
    if(row["BF_LILA"]      == "Wahr")  { return 2  ;}
    if(row["BF_ROSA"]      == "Wahr")  { return 3  ;}
    if(row["BF_WEISS"]     == "Wahr")  { return 4  ;}
    if(row["BF_GELB"]      == "Wahr")  { return 5  ;}
    if(row["BF_ORANGE"]    == "Wahr")  { return 6  ;}
    if(row["BF_ROT"]       == "Wahr")  { return 7  ;}
    if(row["BF_MEHRF"]     == "Wahr")  { return 8  ;}
    if(row["BF_GRUEN"]     == "Wahr")  { return 9  ;}
    if(row["BF_PINK"]      == "Wahr")  { return 10 ;}
    if(row["BF_SCHWARZ"]   == "Wahr")  { return 11 ;}
    if(row["BF_BLAU"]      == "Wahr")  { return 12 ;}
    if(row["BF_ORANGE"]    == "Wahr")  { return 13 ;}
    if(row["BF_WEISS"]     == "Wahr")  { return 14 ;}
    if(row["BF_GELB"]      == "Wahr")  { return 15 ;}
    if(row["BF_ROT"]       == "Wahr")  { return 16 ;}
    if(row["BF_GRUEN"]     == "Wahr")  { return 17 ;}
    if(row["BF_SCHWARZ"]   == "Wahr")  { return 18 ;}
    return 0;
}

/**
 * return the colors value
 * @param row
 *
 * @return int
 */
function get_FF_value(row)
{
    if(row["FF_BLAU"]      == "Wahr")  { return 1  ;}
    if(row["FF_LILA"]      == "Wahr")  { return 2  ;}
    if(row["FF_ROSA"]      == "Wahr")  { return 3  ;}
    if(row["FF_WEISS"]     == "Wahr")  { return 4  ;}
    if(row["FF_GELB"]      == "Wahr")  { return 5  ;}
    if(row["FF_ORANGE"]    == "Wahr")  { return 6  ;}
    if(row["FF_ROT"]       == "Wahr")  { return 7  ;}
    if(row["FF_MEHRF"]     == "Wahr")  { return 8  ;}
    if(row["FF_GRUEN"]     == "Wahr")  { return 9  ;}
    if(row["FF_PINK"]      == "Wahr")  { return 10 ;}
    if(row["FF_SCHWARZ"]   == "Wahr")  { return 11 ;}
    if(row["FF_BLAU"]      == "Wahr")  { return 12 ;}
    if(row["FF_ORANGE"]    == "Wahr")  { return 13 ;}
    if(row["FF_WEISS"]     == "Wahr")  { return 14 ;}
    if(row["FF_GELB"]      == "Wahr")  { return 15 ;}
    if(row["FF_ROT"]       == "Wahr")  { return 16 ;}
    if(row["FF_GRUEN"]     == "Wahr")  { return 17 ;}
    if(row["FF_SCHWARZ"]   == "Wahr")  { return 18 ;}
    return 0;
}

var first_row = []     // columns name from csv file to use als key in values list
var values_list = []
var old_record = [];
var last_array = null;

function testValue(value){
    if(value == null || isNaN(value) || value === "" || value < 0){
        return 0;
    }
    return value;
}

var count = 0;
function saveInDatabase(row, prise_plan) {
    con.query("INSERT INTO article(id, mwst, RES_ZEIT, WARENGR, INTERNET, SAISON_KZ, title, description, DKZ1, DKZ2, DKZ3, SYS_ANLAGE, DKZ4, PREIS_GRP, BIS_MENGE, price, STKPREIS, GP_MENGE, GP_EINHEIT, PACK_MENGE, RABATT, VKPREIS3, VKVALIDD3, VKBISDT1, VKBISDT2, VKBISDT3, STAFRABATT, FAKTOR, ISZUSATZ00, ISZUSATZ01, ISZUSATZ02, ISZUSATZ03, ISZUSATZ04, ISZUSATZ05, ISZUSATZ06, ISZUSATZ07, ISZUSATZ08, ISZUSATZ09, ISZUSATZ10, ISZUSATZ11, ISZUSATZ12, ISZUSATZ13, ISZUSATZ14, ISZUSATZ15, ISZUSATZ16, ISZUSATZ17, ISZUSATZ18, ISZUSATZ19, ISZUSATZ20, ISZUSATZ21, ISZUSATZ22, ISZUSATZ23, ISZUSATZ24, ISZUSATZ25, ISZUSATZ26, ISZUSATZ27, ISZUSATZ28, ISZUSATZ29, ISZUSATZ30, ISZUSATZ31, ISZUSATZ32, ISZUSATZ33, ISZUSATZ34, ISZUSATZ35, ISZUSATZ36, ISZUSATZ37, ISZUSATZ38, ISZUSATZ39, ISZUSATZ40, ISZUSATZ41, ISZUSATZ42, ISZUSATZ43, ISZUSATZ44, ISZUSATZ45, ISZUSATZ46, ISZUSATZ47, ISZUSATZ48, ISZUSATZ49, ISZUSATZ50, ISZUSATZ51, ISZUSATZ52, ISZUSATZ53, ISZUSATZ54, ISZUSATZ55, ISZUSATZ56, KAT_1, KAT_2, KAT_3, KAT_4, KAT_5, pflanzen_type, LAUB_IG, LAUB_LA, LAUB_WG, BESTELLT, GELIEFERT, OFFEN, LIETERMIN, GEWICHT, VERF_BEST, MARKE, BF_id, FF_id) VALUES (?) ", [row], function (err) {
        if (err) { console.log(err); }
        else{
            if(prise_plan !== null && typeof prise_plan != 'undefined')
            {
                let price_list = prise_plan.split(';');
                price_list.forEach(function (item) {

                    if(item.includes(":")){
                        let price = [
                            row[0],
                            item.split(":")[0],
                            item.split(":")[1]
                        ];
                        con.query("INSERT INTO article_price(article_id, max_count, price) VALUES (?) ", [price], function (err) {
                            if (err) { console.log("."); }
                        });
                    }
                })
            }
        }
        // var endDate = new Date(Date.now() - startDate);
        // console.log(endDate.getSeconds());
    });
}

let csvStream = csv.fromPath(path, {
    headers: true,
    delimiter: ";",
    escape:'"'
})
.on("data", function(array){        // save the row in values_list and save the price just one time
    csvStream.pause();

    if(last_array == null){
        last_array = array;
    }

    if(last_array != null)
    {
        if(last_array.NUMMER !== array.NUMMER)
        {
            // this is new
            //----------------------------------
            // save the last and save the new
            values_list.push(array);
            last_array = array;
        }
        else
        {
            // this is duplicated
            //----------------------------------
            // get the current prise and save in $array["VKPREIS1"]
            if(new Date(array.VKVALIDD2) > new Date(array.VKVALIDD1))
            {
                array.VKPREIS1 = array.VKPREIS2.replace(",", ".");
            }
            else{
                array.VKPREIS1 = array.VKPREIS1.replace(",", ".");
            }
            
            
            // save the last price in new array then save in last_array
            if(last_array.prise_plan)
            {

                array.prise_plan = last_array.prise_plan;
                last_array = array;
            }
            else
            {
                last_array.prise_plan = "";
            }

            let new_prise = array["BIS_MENGE"] + ":" + array["VKPREIS1"] + ";"; // get new price from new array 4;3.300;
            if(!last_array.prise_plan.includes(new_prise)){   // if not contains in the last array 4;3.300; than add 4;3.300;
                last_array.prise_plan += new_prise;
            }
        }
    }
    csvStream.resume();

}).on("end", function(){        // get the data from values_list and save in Database ( with save prise in atricle_price table

    if(last_array != null ) { values_list.push(last_array); } // save the last row

    values_list.forEach(function (record) {

        let row = [
            record.WM + record.NUMMER,  //id varchar(255),
            get_steuer(record.MWST) ,   //mwst varchar(255),
            result_integer(record.RES_ZEIT) ,    // RES_ZEIT integer,
            result_integer(record.WARENGR) ,    //WARENGR integer,
            result_bool(record.INTERNET) ,    //INTERNET bool,
            result_varchar(record.SAISON_KZ) ,     //SAISON_KZ varchar(255),
            result_varchar(record.BANAME1)  ,     //title varchar(255), #BANAME1
            result_varchar(record.BANAME2  + record.BANAME3),     //description varchar(255) , #BANAME2 und BANAME3
            result_integer(record.DKZ1) ,    //DKZ1 integer,
            result_integer(record.DKZ2) ,    //DKZ2 integer,
            result_integer(record.DKZ3) ,    //DKZ3 integer,
            result_date(record.SYS_ANLAGE)  ,     //SYS_ANLAGE datetime, # muss syntax wechseln
            result_integer(record.DKZ4) ,    //DKZ4 integer,
            result_varchar(record.PREIS_GRP) ,     //PREIS_GRP varchar(255),
            result_integer(record.BIS_MENGE) ,    //BIS_MENGE integer,
            get_last_prise(record) ,  //cimal,  #VKVALIDD1	VKPREIS1	VKVALIDD2	VKPREIS2
            result_bool(record.STKPREIS) ,    //STKPREIS bool,# falsch = 0 , wahr ? true
            result_integer(record.GP_MENGE) ,    //GP_MENGE integer,
            result_integer(record.GP_EINHEIT) ,    //GP_EINHEIT integer,
            result_integer(record.PACK_MENGE) ,    //PACK_MENGE integer,
            result_bool(record.RABATT) ,    //RABATT bool, # falsch = 0 , wahr ? true
            result_bool(record.VKPREIS3) ,    //VKPREIS3 bool,
            result_bool(record.VKVALIDD3) ,    //VKVALIDD3 bool,
            result_integer(record.VKBISDT1) ,    //VKBISDT1 integer,
            result_date(record.VKBISDT2) ,    //VKBISDT2 date,
            result_date(record.VKBISDT3) ,    //VKBISDT3 date,
            result_date(record.STAFRABATT) ,    //STAFRABATT date, #isnull
            result_date(record.FAKTOR) ,    //FAKTOR date,
            result_varchar(record.ISZUSATZ00) ,     //ISZUSATZ00 varchar(255),
            result_varchar(record.ISZUSATZ01) ,     //ISZUSATZ01 varchar(255),
            result_varchar(record.ISZUSATZ02) ,     //ISZUSATZ02 varchar(255),
            result_varchar(record.ISZUSATZ03) ,     //ISZUSATZ03 varchar(255),
            result_varchar(record.ISZUSATZ04) ,     //ISZUSATZ04 varchar(255),
            result_varchar(record.ISZUSATZ05) ,     //ISZUSATZ05 varchar(255),
            result_varchar(record.ISZUSATZ06) ,     //ISZUSATZ06 varchar(255),
            result_varchar(record.ISZUSATZ07) ,     //ISZUSATZ07 varchar(255),
            result_varchar(record.ISZUSATZ08) ,     //ISZUSATZ08 varchar(255),
            result_varchar(record.ISZUSATZ09) ,     //ISZUSATZ09 varchar(255),
            result_varchar(record.ISZUSATZ10) ,     //ISZUSATZ10	Varchar(255),
            result_varchar(record.ISZUSATZ11) ,     //ISZUSATZ11	Varchar(255),
            result_varchar(record.ISZUSATZ12) ,     //ISZUSATZ12	Varchar(255),
            result_varchar(record.ISZUSATZ13) ,     //ISZUSATZ13	Varchar(255),
            result_varchar(record.ISZUSATZ14) ,     //ISZUSATZ14	Varchar(255),
            result_varchar(record.ISZUSATZ15) ,     //ISZUSATZ15	Varchar(255),
            result_varchar(record.ISZUSATZ16) ,     //ISZUSATZ16	Varchar(255),
            result_varchar(record.ISZUSATZ17) ,     //ISZUSATZ17	Varchar(255),
            result_varchar(record.ISZUSATZ18) ,     //ISZUSATZ18	Varchar(255),
            result_varchar(record.ISZUSATZ19) ,     //ISZUSATZ19	Varchar(255),
            result_varchar(record.ISZUSATZ20) ,     //ISZUSATZ20	Varchar(255),
            result_varchar(record.ISZUSATZ21) ,     //ISZUSATZ21	Varchar(255),
            result_varchar(record.ISZUSATZ22) ,     //ISZUSATZ22	Varchar(255),
            result_varchar(record.ISZUSATZ23) ,     //ISZUSATZ23	Varchar(255),
            result_varchar(record.ISZUSATZ24) ,     //ISZUSATZ24	Varchar(255),
            result_varchar(record.ISZUSATZ25) ,     //ISZUSATZ25	Varchar(255),
            result_varchar(record.ISZUSATZ26) ,     //ISZUSATZ26	Varchar(255),
            result_varchar(record.ISZUSATZ27) ,     //ISZUSATZ27	Varchar(255),
            result_varchar(record.ISZUSATZ28) ,     //ISZUSATZ28	Varchar(255),
            result_varchar(record.ISZUSATZ29) ,     //ISZUSATZ29	Varchar(255),
            result_varchar(record.ISZUSATZ30) ,     //ISZUSATZ30	Varchar(255),
            result_varchar(record.ISZUSATZ31) ,     //ISZUSATZ31	Varchar(255),
            result_varchar(record.ISZUSATZ32) ,     //ISZUSATZ32	Varchar(255),
            result_varchar(record.ISZUSATZ33) ,     //ISZUSATZ33	Varchar(255),
            result_varchar(record.ISZUSATZ34) ,     //ISZUSATZ34	Varchar(255),
            result_varchar(record.ISZUSATZ35) ,     //ISZUSATZ35	Varchar(255),
            result_varchar(record.ISZUSATZ36) ,     //ISZUSATZ36	Varchar(255),  # löchen "cm"
            result_varchar(record.ISZUSATZ37) ,     //ISZUSATZ37	Varchar(255),
            result_varchar(record.ISZUSATZ38) ,     //ISZUSATZ38	Varchar(255),
            result_varchar(record.ISZUSATZ39) ,     //ISZUSATZ39	Varchar(255),
            result_varchar(record.ISZUSATZ40) ,     //ISZUSATZ40	Varchar(255),
            result_varchar(record.ISZUSATZ41) ,     //ISZUSATZ41	Varchar(255),
            result_varchar(record.ISZUSATZ42) ,     //ISZUSATZ42	Varchar(255),
            result_varchar(record.ISZUSATZ43) ,     //ISZUSATZ43	Varchar(255),
            result_varchar(record.ISZUSATZ44) ,     //ISZUSATZ44	Varchar(255),
            result_varchar(record.ISZUSATZ45) ,     //ISZUSATZ45	Varchar(255),
            result_varchar(record.ISZUSATZ46) ,     //ISZUSATZ46	Varchar(255),
            result_varchar(record.ISZUSATZ47) ,     //ISZUSATZ47	Varchar(255),
            result_varchar(record.ISZUSATZ48) ,     //ISZUSATZ48	Varchar(255),
            result_varchar(record.ISZUSATZ49) ,     //ISZUSATZ49	Varchar(255),
            result_varchar(record.ISZUSATZ50) ,     //ISZUSATZ50	Varchar(255),
            result_integer(record.ISZUSATZ51) ,    //ISZUSATZ51 integer , # monthes id
            result_integer(record.ISZUSATZ52) ,    //ISZUSATZ52 integer, # monthes id
            result_integer(record.ISZUSATZ53) ,    //ISZUSATZ53 integer, # monthes id
            result_varchar(record.ISZUSATZ54) ,    //ISZUSATZ54 VARCHAR(255),
            result_integer(record.ISZUSATZ55) ,    //ISZUSATZ55 integer, # löchen "cm"
            result_integer(record.ISZUSATZ56) ,    //ISZUSATZ56 integer, # löchen "cm"
            result_varchar(record.KAT_1) ,     //KAT_1	Varchar(255),
            result_varchar(record.KAT_2) ,     //KAT_2	Varchar(255),
            result_varchar(record.KAT_3) ,     //KAT_3	Varchar(255),
            result_varchar(record.KAT_4) ,     //KAT_4	Varchar(255),
            result_varchar(record.KAT_5) ,     //KAT_5	Varchar(255),
            result_integer(record.pflanzen_type) ,    //pflanzen_type integer, # id von : halbschattig, sonnig
            result_bool(record.LAUB_IG) ,    //LAUB_IG bool,
            result_bool(record.LAUB_LA) ,    //LAUB_LA bool,
            result_bool(record.LAUB_WG) ,    //LAUB_WG bool,
            result_integer(record.BESTELLT) ,    //BESTELLT integer,
            result_bool(record.GELIEFERT) ,    //GELIEFERT bool,
            result_bool(record.OFFEN) ,    //OFFEN bool,
            result_date(record.LIETERMIN) ,    //   IN date,
            result_integer(record.GEWICHT) ,    //GEWICHT float,
            testValue(record.VERF_BEST) ,    //VERF_BEST ,ecimal,
            result_varchar(record.MARKE) ,     //MARKE VARCHAR(255)
            get_BF_value(record), // BF_id ;integer, # BF_BLAU	BF_LILA	BF_ROSA	BF_WEISS BF_GELB BF_ORANGE	BF_ROT	BF_MEHRF	BF_GRUEN	BF_PINK	BF_SCHWARZ
            get_FF_value(record)
        ];

        saveInDatabase(row, record.prise_plan);
    });


    console.log(values_list.length);
    console.log("done");
    var endDate = new Date(Date.now() - startDate);
    console.log(endDate.getSeconds());
}).on("error", function(err){
    console.log("..");
});




