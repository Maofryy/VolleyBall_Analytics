<?php
$level = ['dep', 'reg', 'nat'];
if (!isset($_GET['level']) || !in_array($_GET['level'], $level))
{
	echo "Veuillez ajouter le niveau dans l'url pour exporter les liens des poules.<br>";
	echo "Valeur disponible : dep, reg, nat.<br>";
	echo "exemple: ?level=nat";
	die;
}


$seasons = array(
	/*"2012/2013",
	"2013/2014",
	"2014/2015",*/
	"2015/2016",
	"2016/2017",
	"2017/2018",
	"2018/2019",
	"2019/2020",
	"2020/2021",
	"2021/2022",
);

$poules_nat = array();
$poules_reg = array();
$abcdefu = ["A", "B", "C", "D", "E", "F", "G"];
$regions = ['LIRA', 'LIBOUR', 'LIBR','LICE','LICO','LILO', 'LIGU','LIGY','LIFL', 'LIIDF','LIRE','LIMART','LIMY','LILBNV','LIAQ','LILR','LIPL','LICA'];
$departements = [
	'PTRA01',
	'PTRA26',
	'PTAU15',
	'PTRA38',
	'PTRA42',
	'PTAU43',
	'PTAU63',
	'PTRA69',
	'PTBO21',
	'PTBO71',
	'PTBO89',
	'PTBR22',
	'PTBR29',
	'PTBR35',
	'PTBR56',
	'PTCE28',
	'PTCE37',
	'PTCE45',
	'PTLO52',
	'PTLO54',
	'PTLO57',
	'PTAL67',
	'PTAL68',
	'PTLO88',
	'PTPI02',
	'PTFL59',
	'PTPI60',
	'PTFL62',
	'PTPI80',
	'PTIDF7',
	'PTIDF7',
	'PTIDF7',
	'PTIDF9',
	'PTIDF9',
	'PTIDF9',
	'PTIDF9',
	'PTIDF9',
	'PTLB14',
	'PTLH27',
	'PTLH76',
	'PTPO16',
	'PTPO17',
	'PTAQ24',
	'PTAQ33',
	'PTAQ40',
	'PTAQ64',
	'PTPO79',
	'PTPO86',
	'PTLR11',
	'PTLR30',
	'PTMP31',
	'PTLR34',
	'PTMP65',
	'PTLR66',
	'PTMP81',
	'PTPL44',
	'PTPL49',
	'PTPL53',
	'PTPL72',
	'PTPL85',
	'PTPR04',
	'PTCA06',
	'PTPR13',
	'PTCA83',
	'PTPR84'
];
foreach ($abcdefu as $letter)
{
	// élite
	$poules_nat[] = 'EF' . $letter;
	$poules_nat[] = 'EM' . $letter;
	// N2
	$poules_nat[] = '2F' . $letter;
	$poules_nat[] = '2M' . $letter;
	// N3
	$poules_nat[] = '3F' . $letter;
	$poules_nat[] = '3M' . $letter;
	// prénat
	$poules_reg[] = 'PF' . $letter;
	$poules_reg[] = 'PM' . $letter;
}

$nationaux_url = '';
$regionaux_url = '';
$departement_url = '';
foreach ($seasons as $season)
{
	foreach ($poules_nat as $poule)
	{
		$nationaux_url .= '{
		      "url": "' . sprintf('https://www.ffvbbeach.org/ffvbapp/resu/vbspo_calendrier.php?saison=%s&codent=ABCCS&poule=%s', $season, $poule) . '"
		    },';
	}
	foreach ($poules_reg as $poule)
	{
		foreach ($regions as $region)
		{
			$regionaux_url .= '{
			      "url": "' . sprintf('https://www.ffvbbeach.org/ffvbapp/resu/vbspo_calendrier.php?saison=%s&codent=%s&poule=%s', $season, $region, $poule) . '"
			    },';
		}
	}
}
if ($_GET['level'] == 'nat')
{
	echo '[' . substr($nationaux_url, 0, -1) . ']';
}
else if ($_GET['level'] == 'reg')
{
	echo '[' . substr($regionaux_url, 0, -1) . ']';
}
else if($_GET['level'] == 'dep')
{
	echo '[' . substr($departement_url, 0, -1) . ']';
}


/*
$myfile = fopen("link_match_nationaux.json", "r") or die("Unable to open file!");
$content = fread($myfile,filesize("link_match_nationaux.json"));
var_dump(json_decode($content));die;

fclose($myfile);*/