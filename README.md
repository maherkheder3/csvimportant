http://php.net/fgetcsv

```php
if ($zeile = 1) {
  dann merke dir die spaltenüberschriften
} else {
  hole dir die zeile, und mache daraus ein array mit den spaltenüberschriften

  $csvRow = [
    'WM': "SY",
    "NUMMER": "1",
    "MWST": "1F",
    ...
  ]

  schreibe das ganze in eine db-tabele
  habe ich die zeile schon importiert? dann 'UPDATE', ansonsten 'INSERT'
  $sql = 'INSERT ... SET mwst = ?';
  $db->exec($sql, [
    $csvRow['MWST']
  ])
}

```



Zur Datei:

* Das Feld WM.NUMMER ist eine eindeutige ID
* Die Datumsfelder in der CSV sind ein deutsches Datumsformat DD.MM.YYYY, in der Datenbank muss es ein SQL-Format YYYY-MM-DD sein.
* Die Preise sind ein deutsches Zahlenformat X,X, in der DB muss es ein Float sein X.X.
* In der CSV stehen boolsche Werte als FALSCH oder WAHR, soll Boolean in SQL werden

Importiert werden soll:

* `WM` und `NUMMER` => `ID`
* MWST: bei 1B wird mwst = 7%, bei 1A wird mwst = 19%, ansonsten wird mwst = 0% (ggf. über eine zweite Tabelle lösen)
* Nur Artikel mit INTERNET = WAHR (bool)
* `BANAME1` => title
* BANAME2 & BANAME3 => `description`
* SYS_ANLAGE => date_created (date)
* Wenn VKVALIDD2 > VKVALIDD1 muss VKPREIS2 => price; ansonsten VKPREIS1 => price (float)
* `RABATT` => rabatt (bool)
* ISZUSATZ36 => height, aber als Integer in cm; Beispiel: Im ISZUSATZ36 steht "40 cm", in height steht "40"
* Aus "BF_BLAU	BF_LILA	BF_ROSA	BF_WEISS	BF_GELB	BF_ORANGE	BF_ROT	BF_MEHRF	BF_GRUEN	BF_PINK BF_SCHWARZ" ist nur ein Wert WAHR; die Farbe wird in das Feld color geschrieben (ggf. über eine zweite Tabelle lösen)
* Aus "FF_BLAU	FF_ORANGE	FF_WEISS	FF_GELB	FF_ROT	FF_GRUEN	FF_SCHWARZ" ist nur ein Wert WAHR; die Farbe wird in das Feld color_2 geschrieben (ggf. über eine zweite Tabelle lösen)
* `ISZUSATZ51	ISZUSATZ52` beschreibt das Pflanzdatum von ... bis; das Datum wird in ein integer-Feld `date_plant_from` bis `date_plant_to` übertragen
* `GEWICHT` => `weight` (float)
* `VERF_BEST` => `stock` (integer)