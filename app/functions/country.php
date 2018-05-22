<?php

/**
 * Candy-PHP - Code the simpler way.
 *
 * The open source PHP Model-View-Template framework.
 *
 * This content is released under the MIT License (MIT)
 *
 * Copyright (c) 2017 Onehyr Technologies Limited
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * @package	Candy-PHP
 * @author		Ore Richard Muyiwa
 * @copyright      2017 Ore Richard Muyiwa
 * @license	https://opensource.org/licenses/MIT	MIT License
 * @link	https://candy-php.com/
 * @since	Version 1.0.0
 */


$_COUNTRIES = [
    'en' => [
        "ad" => "Andorra",
        "ae" => "United Arab Emirates",
        "af" => "Afghanistan",
        "ag" => "Antigua and Barbuda",
        "ai" => "Anguilla",
        "al" => "Albania",
        "am" => "Armenia",
        "ao" => "Angola",
        "aq" => "Antarctica",
        "ar" => "Argentina",
        "as" => "American Samoa",
        "at" => "Austria",
        "au" => "Australia",
        "aw" => "Aruba",
        "ax" => "Aland",
        "az" => "Azerbaijan",
        "ba" => "Bosnia and Herzegovina",
        "bb" => "Barbados",
        "bd" => "Bangladesh",
        "be" => "Belgium",
        "bf" => "Burkina Faso",
        "bg" => "Bulgaria",
        "bh" => "Bahrain",
        "bi" => "Burundi",
        "bj" => "Benin",
        "bl" => "Saint Barthelemy",
        "bm" => "Bermuda",
        "bn" => "Brunei",
        "bo" => "Bolivia",
        "bq" => "Bonaire",
        "br" => "Brazil",
        "bs" => "Bahamas",
        "bt" => "Bhutan",
        "bv" => "Bouvet Island",
        "bw" => "Botswana",
        "by" => "Belarus",
        "bz" => "Belize",
        "ca" => "Canada",
        "cc" => "Cocos 'Keeling' Islands",
        "cd" => "D.R.C",
        "cf" => "Central African Republic",
        "cg" => "Republic of the Congo",
        "ch" => "Switzerland",
        "ci" => "Ivory Coast",
        "ck" => "Cook Islands",
        "cl" => "Chile",
        "cm" => "Cameroon",
        "cn" => "China",
        "co" => "Colombia",
        "cr" => "Costa Rica",
        "cu" => "Cuba",
        "cv" => "Cape Verde",
        "cw" => "Curacao",
        "cx" => "Christmas Island",
        "cy" => "Cyprus",
        "cz" => "Czech Republic",
        "de" => "Germany",
        "dj" => "Djibouti",
        "dk" => "Denmark",
        "dm" => "Dominica",
        "do" => "Dominican Republic",
        "dz" => "Algeria",
        "ec" => "Ecuador",
        "ee" => "Estonia",
        "eg" => "Egypt",
        "eh" => "Western Sahara",
        "er" => "Eritrea",
        "es" => "Spain",
        "et" => "Ethiopia",
        "fi" => "Finland",
        "fj" => "Fiji",
        "fk" => "Falkland Islands",
        "fm" => "Micronesia",
        "fo" => "Faroe Islands",
        "fr" => "France",
        "ga" => "Gabon",
        "gb" => "United Kingdom",
        "gd" => "Grenada",
        "ge" => "Georgia",
        "gf" => "French Guiana",
        "gg" => "Guernsey",
        "gh" => "Ghana",
        "gi" => "Gibraltar",
        "gl" => "Greenland",
        "gm" => "Gambia",
        "gn" => "Guinea",
        "gp" => "Guadeloupe",
        "gq" => "Equatorial Guinea",
        "gr" => "Greece",
        "gs" => "South Georgia",
        "gt" => "Guatemala",
        "gu" => "Guam",
        "gw" => "Guinea-Bissau",
        "gy" => "Guyana",
        "hk" => "Hong Kong",
        "hn" => "Honduras",
        "hr" => "Croatia",
        "ht" => "Haiti",
        "hu" => "Hungary",
        "id" => "Indonesia",
        "ie" => "Ireland",
        "il" => "Israel",
        "im" => "Isle of Man",
        "in" => "India",
        "io" => "British Indian Ocean Territory",
        "iq" => "Iraq",
        "ir" => "Iran",
        "is" => "Iceland",
        "it" => "Italy",
        "je" => "Jersey",
        "jm" => "Jamaica",
        "jo" => "Jordan",
        "jp" => "Japan",
        "ke" => "Kenya",
        "kg" => "Kyrgyzstan",
        "kh" => "Cambodia",
        "ki" => "Kiribati",
        "km" => "Comoros",
        "kn" => "Saint Kitts and Nevis",
        "kp" => "North Korea",
        "kr" => "South Korea",
        "kw" => "Kuwait",
        "ky" => "Cayman Islands",
        "kz" => "Kazakhstan",
        "la" => "Laos",
        "lb" => "Lebanon",
        "lc" => "Saint Lucia",
        "li" => "Liechtenstein",
        "lk" => "Sri Lanka",
        "lr" => "Liberia",
        "ls" => "Lesotho",
        "lt" => "Lithuania",
        "lu" => "Luxembourg",
        "lv" => "Latvia",
        "ly" => "Libya",
        "ma" => "Morocco",
        "mc" => "Monaco",
        "md" => "Moldova",
        "me" => "Montenegro",
        "mf" => "Saint Martin",
        "mg" => "Madagascar",
        "mh" => "Marshall Islands",
        "mk" => "Macedonia",
        "ml" => "Mali",
        "mm" => "Myanmar 'Burma'",
        "mn" => "Mongolia",
        "mo" => "Macao",
        "mp" => "Northern Mariana Islands",
        "mq" => "Martinique",
        "mr" => "Mauritania",
        "ms" => "Montserrat",
        "mt" => "Malta",
        "mu" => "Mauritius",
        "mv" => "Maldives",
        "mw" => "Malawi",
        "mx" => "Mexico",
        "my" => "Malaysia",
        "mz" => "Mozambique",
        "na" => "Namibia",
        "nc" => "New Caledonia",
        "ne" => "Niger",
        "nf" => "Norfolk Island",
        "ng" => "Nigeria",
        "ni" => "Nicaragua",
        "nl" => "Netherlands",
        "no" => "Norway",
        "np" => "Nepal",
        "nr" => "Nauru",
        "nu" => "Niue",
        "nz" => "New Zealand",
        "om" => "Oman",
        "pa" => "Panama",
        "pe" => "Peru",
        "pf" => "French Polynesia",
        "pg" => "Papua New Guinea",
        "ph" => "Philippines",
        "pk" => "Pakistan",
        "pl" => "Poland",
        "pm" => "Saint Pierre and Miquelon",
        "pn" => "Pitcairn Islands",
        "pr" => "Puerto Rico",
        "ps" => "Palestine",
        "pt" => "Portugal",
        "pw" => "Palau",
        "py" => "Paraguay",
        "qa" => "Qatar",
        "re" => "Reunion",
        "ro" => "Romania",
        "rs" => "Serbia",
        "ru" => "Russia",
        "rw" => "Rwanda",
        "sa" => "Saudi Arabia",
        "sb" => "Solomon Islands",
        "sc" => "Seychelles",
        "sd" => "Sudan",
        "se" => "Sweden",
        "sg" => "Singapore",
        "sh" => "Saint Helena",
        "si" => "Slovenia",
        "sj" => "Svalbard and Jan Mayen",
        "sk" => "Slovakia",
        "sl" => "Sierra Leone",
        "sm" => "San Marino",
        "sn" => "Senegal",
        "so" => "Somalia",
        "sr" => "Suriname",
        "ss" => "South Sudan",
        "st" => "Sao Tome and Principe",
        "sv" => "El Salvador",
        "sx" => "Sint Maarten",
        "sy" => "Syria",
        "sz" => "Swaziland",
        "tc" => "Turks and Caicos Islands",
        "td" => "Chad",
        "tf" => "French Southern Territories",
        "tg" => "Togo",
        "th" => "Thailand",
        "tj" => "Tajikistan",
        "tk" => "Tokelau",
        "tl" => "East Timor",
        "tm" => "Turkmenistan",
        "tn" => "Tunisia",
        "to" => "Tonga",
        "tr" => "Turkey",
        "tt" => "Trinidad and Tobago",
        "tv" => "Tuvalu",
        "tw" => "Taiwan",
        "tz" => "Tanzania",
        "ua" => "Ukraine",
        "ug" => "Uganda",
        "um" => "U.S. Minor Outlying Islands",
        "us" => "United States",
        "uy" => "Uruguay",
        "uz" => "Uzbekistan",
        "va" => "Vatican City",
        "vc" => "Saint Vincent",
        "ve" => "Venezuela",
        "vg" => "British Virgin Islands",
        "vi" => "U.S. Virgin Islands",
        "vn" => "Vietnam",
        "vu" => "Vanuatu",
        "wf" => "Wallis and Futuna",
        "ws" => "Samoa",
        "xk" => "Kosovo",
        "ye" => "Yemen",
        "yt" => "Mayotte",
        "za" => "South Africa",
        "zm" => "Zambia",
        "zw" => "Zimbabwe"
    ]
];

$_DIAL_CODES = [
	['af' => '93'],
	['al' => '355'],
	['dz' => '213'],
	['as' => '1684'],
	['ad' => '376'],
	['ao' => '244'],
	['ai' => '1264'],
	['ag' => '1268'],
	['ar' => '54'],
	['am' => '374'],
	['aw' => '297'],
	['au' => '61'],
	['at' => '43'],
	['az' => '994'],
	['bs' => '1242'],
	['bh' => '973'],
	['bd' => '880'],
	['bb' => '1246'],
	['by' => '375'],
	['be' => '32'],
	['bz' => '501'],
	['bj' => '229'],
	['bm' => '1441'],
	['bt' => '975'],
	['bo' => '591'],
	['ba' => '387'],
	['bw' => '267'],
	['br' => '55'],
	['io' => '246'],
	['bn' => '673'],
	['bg' => '359'],
	['bf' => '226'],
	['bi' => '257'],
	['kh' => '855'],
	['cm' => '237'],
	['ca' => '1'],
	['cv' => '238'],
	['ky' => '1345'],
	['cf' => '236'],
	['td' => '235'],
	['cl' => '56'],
	['cn' => '86'],
	['cx' => '61'],
	['cc' => '672'],
	['co' => '57'],
	['km' => '269'],
	['cg' => '242'],
	['cd' => '242'],
	['ck' => '682'],
	['cr' => '506'],
	['ci' => '225'],
	['hr' => '385'],
	['cu' => '53'],
	['cy' => '357'],
	['cz' => '420'],
	['dk' => '45'],
	['dj' => '253'],
	['dm' => '1767'],
	['do' => '1809'],
	['ec' => '593'],
	['eg' => '20'],
	['sv' => '503'],
	['gq' => '240'],
	['er' => '291'],
	['ee' => '372'],
	['et' => '251'],
	['fk' => '500'],
	['fo' => '298'],
	['fj' => '679'],
	['fi' => '358'],
	['fr' => '33'],
	['gf' => '594'],
	['pf' => '689'],
	['ga' => '241'],
	['gm' => '220'],
	['ge' => '995'],
	['de' => '49'],
	['gh' => '233'],
	['gi' => '350'],
	['gr' => '30'],
	['gl' => '299'],
	['gd' => '1473'],
	['gp' => '590'],
	['gu' => '1671'],
	['gt' => '502'],
	['gn' => '224'],
	['gw' => '245'],
	['gy' => '592'],
	['ht' => '509'],
	['va' => '39'],
	['hn' => '504'],
	['hk' => '852'],
	['hu' => '36'],
	['is' => '354'],
	['in' => '91'],
	['id' => '62'],
	['ir' => '98'],
	['iq' => '964'],
	['ie' => '353'],
	['il' => '972'],
	['it' => '39'],
	['jm' => '1876'],
	['jp' => '81'],
	['jo' => '962'],
	['kz' => '7'],
	['ke' => '254'],
	['ki' => '686'],
	['kp' => '850'],
	['kr' => '82'],
	['kw' => '965'],
	['kg' => '996'],
	['la' => '856'],
	['lv' => '371'],
	['lb' => '961'],
	['ls' => '266'],
	['lr' => '231'],
	['ly' => '218'],
	['li' => '423'],
	['lt' => '370'],
	['lu' => '352'],
	['mo' => '853'],
	['mk' => '389'],
	['mg' => '261'],
	['mw' => '265'],
	['my' => '60'],
	['mv' => '960'],
	['ml' => '223'],
	['mt' => '356'],
	['mh' => '692'],
	['mq' => '596'],
	['mr' => '222'],
	['mu' => '230'],
	['yt' => '269'],
	['mx' => '52'],
	['fm' => '691'],
	['md' => '373'],
	['mc' => '377'],
	['mn' => '976'],
	['ms' => '1664'],
	['ma' => '212'],
	['mz' => '258'],
	['mm' => '95'],
	['na' => '264'],
	['nr' => '674'],
	['np' => '977'],
	['nl' => '31'],
	['an' => '599'],
	['nc' => '687'],
	['nz' => '64'],
	['ni' => '505'],
	['ne' => '227'],
	['ng' => '234'],
	['nu' => '683'],
	['nf' => '672'],
	['mp' => '1670'],
	['no' => '47'],
	['om' => '968'],
	['pk' => '92'],
	['pw' => '680'],
	['ps' => '970'],
	['pa' => '507'],
	['pg' => '675'],
	['py' => '595'],
	['pe' => '51'],
	['ph' => '63'],
	['pl' => '48'],
	['pt' => '351'],
	['pr' => '1787'],
	['qa' => '974'],
	['re' => '262'],
	['ro' => '40'],
	['ru' => '70'],
	['rw' => '250'],
	['sh' => '290'],
	['kn' => '1869'],
	['lc' => '1758'],
	['pm' => '508'],
	['vc' => '1784'],
	['ws' => '684'],
	['sm' => '378'],
	['st' => '239'],
	['sa' => '966'],
	['sn' => '221'],
	['cs' => '381'],
	['sc' => '248'],
	['sl' => '232'],
	['sg' => '65'],
	['sk' => '421'],
	['si' => '386'],
	['sb' => '677'],
	['so' => '252'],
	['za' => '27'],
	['es' => '34'],
	['lk' => '94'],
	['sd' => '249'],
	['sr' => '597'],
	['sj' => '47'],
	['sz' => '268'],
	['se' => '46'],
	['ch' => '41'],
	['sy' => '963'],
	['tw' => '886'],
	['tj' => '992'],
	['tz' => '255'],
	['th' => '66'],
	['tl' => '670'],
	['tg' => '228'],
	['tk' => '690'],
	['to' => '676'],
	['tt' => '1868'],
	['tn' => '216'],
	['tr' => '90'],
	['tm' => '7370'],
	['tc' => '1649'],
	['tv' => '688'],
	['ug' => '256'],
	['ua' => '380'],
	['ae' => '971'],
	['gb' => '44'],
	['us' => '1'],
	['um' => '1'],
	['uy' => '598'],
	['uz' => '998'],
	['vu' => '678'],
	['ve' => '58'],
	['vn' => '84'],
	['vg' => '1284'],
	['vi' => '1340'],
	['wf' => '681'],
	['eh' => '212'],
	['ye' => '967'],
	['zm' => '260'],
	['zw' => '263'],
	['rs' => '381'],
	['me' => '382'],
	['ax' => '358'],
	['bq' => '599'],
	['cw' => '599'],
	['gg' => '44'],
	['im' => '44'],
	['je' => '44'],
	['xk' => '381'],
	['bl' => '590'],
	['mf' => '590'],
	['sx' => '1'],
	['ss' => '211']
];


function countries($language = ''){

    global $_COUNTRIES;

    if(empty($language))
        $language = get_config('language', 'main');

    return isset($_COUNTRIES[$language]) ? $_COUNTRIES[$language] : $_COUNTRIES['en'];
}



function country_name($country_code, $language = ''){
    $countries = countries($language);
    return isset($countries[strtolower($country_code)]) ? $countries[strtolower($country_code)] : '';
}

function dial_code($country_code){
	global $_DIAL_CODES;
	return isset($_DIAL_CODES[strtolower($country_code)]) ? $_DIAL_CODES[strtolower($country_code)] : false;
}



