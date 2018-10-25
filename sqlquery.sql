DROP table if exists article;

create table article(
  id varchar(255),
  mwst varchar(255),
  RES_ZEIT integer,
  WARENGR integer,
  INTERNET bool,
  SAISON_KZ varchar(255),
  title varchar(255), #BANAME1
  description varchar(255) , #BANAME2 und BANAME3
  DKZ1 integer,
  DKZ2 integer,
  DKZ3 integer,
  SYS_ANLAGE datetime, # muss syntax wechseln
  DKZ4 integer,
  PREIS_GRP varchar(255),
  BIS_MENGE integer,
  price decimal,  #VKVALIDD1	VKPREIS1	VKVALIDD2	VKPREIS2
  STKPREIS bool,# falsch = 0 , wahr ? true
  GP_MENGE integer,
  GP_EINHEIT integer,
  PACK_MENGE integer,
  RABATT bool, # falsch = 0 , wahr ? true
  VKPREIS3 bool,
  VKVALIDD3 bool,
  VKBISDT1 int,
  VKBISDT2 date,
  VKBISDT3 date,
  STAFRABATT date, # 0 is null
  FAKTOR date,
  ISZUSATZ00 varchar(255),
  ISZUSATZ01 varchar(255),
  ISZUSATZ02 varchar(255),
  ISZUSATZ03 varchar(255),
  ISZUSATZ04 varchar(255),
  ISZUSATZ05 varchar(255),
  ISZUSATZ06 varchar(255),
  ISZUSATZ07 varchar(255),
  ISZUSATZ08 varchar(255),
  ISZUSATZ09 varchar(255),
  ISZUSATZ10	Varchar(255),
  ISZUSATZ11	Varchar(255),
  ISZUSATZ12	Varchar(255),
  ISZUSATZ13	Varchar(255),
  ISZUSATZ14	Varchar(255),
  ISZUSATZ15	Varchar(255),
  ISZUSATZ16	Varchar(255),
  ISZUSATZ17	Varchar(255),
  ISZUSATZ18	Varchar(255),
  ISZUSATZ19	Varchar(255),
  ISZUSATZ20	Varchar(255),
  ISZUSATZ21	Varchar(255),
  ISZUSATZ22	Varchar(255),
  ISZUSATZ23	Varchar(255),
  ISZUSATZ24	Varchar(255),
  ISZUSATZ25	Varchar(255),
  ISZUSATZ26	Varchar(255),
  ISZUSATZ27	Varchar(255),
  ISZUSATZ28	Varchar(255),
  ISZUSATZ29	Varchar(255),
  ISZUSATZ30	Varchar(255),
  ISZUSATZ31	Varchar(255),
  ISZUSATZ32	Varchar(255),
  ISZUSATZ33	Varchar(255),
  ISZUSATZ34	Varchar(255),
  ISZUSATZ35	Varchar(255),
  ISZUSATZ36	Varchar(255),  # löchen "cm"
  ISZUSATZ37	Varchar(255),
  ISZUSATZ38	Varchar(255),
  ISZUSATZ39	Varchar(255),
  ISZUSATZ40	Varchar(255),
  ISZUSATZ41	Varchar(255),
  ISZUSATZ42	Varchar(255),
  ISZUSATZ43	Varchar(255),
  ISZUSATZ44	Varchar(255),
  ISZUSATZ45	Varchar(255),
  ISZUSATZ46	Varchar(255),
  ISZUSATZ47	Varchar(255),
  ISZUSATZ48	Varchar(255),
  ISZUSATZ49	Varchar(255),
  ISZUSATZ50	Varchar(255),
  KAT_1	Varchar(255),
  KAT_2	Varchar(255),
  KAT_3	Varchar(255),
  KAT_4	Varchar(255),
  KAT_5	Varchar(255),
  BF_id int,

  ISZUSATZ51 int, # monthes id
  ISZUSATZ52 int, # monthes id
  ISZUSATZ53 int, # monthes id
  ISZUSATZ54 VARCHAR(255),
  pflanzen_type int, # id von : halbschattig, sonnig
  ISZUSATZ55 int, # löchen "cm"
  ISZUSATZ56 int, # löchen "cm"
  LAUB_IG bool,
  LAUB_LA bool,
  LAUB_WG bool,
  BESTELLT int,
  GELIEFERT bool,
  OFFEN bool,
  LIETERMIN DATETIME,
  GEWICHT float,
  VERF_BEST decimal,
  MARKE VARCHAR(255)
); # MARKE


DROP table if exists colors;

create table colors (
  id int,
  name VARCHAR(50)
);

# BF_BLAU	BF_LILA	BF_ROSA	BF_WEISS	BF_GELB	BF_ORANGE	BF_ROT	BF_MEHRF	BF_GRUEN	BF_PINK	BF_SCHWARZ	FF_BLAU	FF_ORANGE	FF_WEISS	FF_GELB	FF_ROT	FF_GRUEN	FF_SCHWARZ
INSERT INTO colors (id, name)
values
(1,  "BLAU"),
(2,  "LILA"),
(3,  "ROSA"),
(4,  "WEISS"),
(5,  "GELB"),
(6,  "ORANGE"),
(7,  "ROT"),
(8,  "MEHRF"),
(9,  "GRUEN"),
(10, "PINK"),
(11, "SCHWARZ"),
(12, "BLAU"),
(13, "ORANGE"),
(14, "WEISS"),
(15, "GELB"),
(16, "ROT"),
(17, "GRUEN"),
(18, "SCHWARZ")
;
