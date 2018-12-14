console.log("start");
var path = "/var/www/html/importcsvfile/Artikeldaten-Shop.csv";

var fs = require("fs");             // csv library
var csv = require("fast-csv");

var first_row;
var $values_list;


try{
    fs.createReadStream(path)
        .pipe(csv())
        .on("data", function(data){
            // read the line
            console.log(data[0]);
            return;
        })
        .on("end", function(){
            console.log("done");
        });
}catch (e) {
    
}


