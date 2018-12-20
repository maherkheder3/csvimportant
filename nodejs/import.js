console.clear();
console.log("start");

var path = "/var/www/html/importcsvfile/Artikeldaten-Shop.csv";

var windows1252 = require('windows-1252');
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

// drop all tables and create again new tables
// fs.createReadStream("/var/www/html/importcsvfile/sqlquery.sql", "utf8", function(err, data) {
//     console.log(data);
// });


// test the connect
// con.connect(function(err) {
//     if (err) {
//         console.log(err);
//         return null;
//     }
//     console.log("Connected!");
//     // console.log(fs.readFileSync('../var/www/html/importcsvfile/sqlquery.sql').toString());
//
//     con.query("SELECT * FROM FL_insurance_sample", function (err, result) {
//         if (err) throw err;
//         // console.log(result);
//     });
// });



var first_row = []     // columns name from csv file to use als key in values list
var values_list = []

function xx(record){
    console.log(record.NUMMER);
}

let csvStream = csv.fromPath(path, {
    headers: true,
    delimiter: ';',
    escape:'"',
    encoding: 'windows1252'
})
.on("data", function(record){
    console.log(record);
    
    // values_list.push(record);
    // console.log("finsh");
    
}).on("end", function(){
    console.log(values_list.length);

}).on("error", function(err){
    console.log(err);
});


