<?php

function is_loaded($class = '') {
    static $_is_loaded = array();

    if ($class != '') {
        $_is_loaded[$class] = $class;
    }

    return $_is_loaded;
}

function getRealIpAddr() {
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {   //check ip from share internet
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {   //to check ip is pass from proxy
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
        $ip = $_SERVER['REMOTE_ADDR'];
    }
    return $ip;
}

function htmlimport($location) {

    $type = substr(strrchr($location, '.'), 1);

    switch ($type) {

        case 'js':

            $html = '<script type="text/javascript" src="' . $location . '"></script>' . "\n";

            break;

        case 'css':

            $html = '<link rel="stylesheet" href="' . $location . '" type="text/css" />' . "\n";

            break;
    }

    return $html;
}

function is_valid_email($email) {
    if (!preg_match("/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,4})$/i", $email)) {
        return FALSE;
    }
    return TRUE;
}

function cleanData($value) {
    // Stripslashes
    if (get_magic_quotes_gpc()) {
        $value = stripslashes($value);
    }
    // Quote if not a number or a numeric string
    if (!is_numeric($value)) {
        $value = mysql_real_escape_string($value);
    }

    return $value;
}

function insertQuery($table, $data) {

    $count = 0;
    $fields = '';
    $values = '';

    foreach ($data as $field => $value) {

        $glue = $count > 0 ? ', ' : '';

        $fields .= $glue . '`' . $field . '`';

        $values .= $value == 'NOW()' ? $glue . 'NOW()' : $glue . '"' . cleanData($value) . '"';

        $count++;
    }

    if (mysql_query('INSERT INTO ' . $table . ' (' . $fields . ') VALUES (' . $values . ')')) {

        return mysql_insert_id();
    }

    return false;
}

function convertUrl($string) {
    $string = preg_replace("`\[.*\]`U", "", $string);
    $string = preg_replace('`&(amp;)?#?[a-z0-9]+;`i', '-', $string);
    $string = htmlentities($string, ENT_COMPAT, 'utf-8');
    $string = preg_replace("`&([a-z])(acute|uml|circ|grave|ring|cedil|slash|tilde|caron|lig|quot|rsquo);`i", "\\1", $string);
    $string = preg_replace(array("`[^a-z0-9]`i", "`[-]+`"), "-", $string);
    return strtolower(trim($string, '-'));
}

function updateQuery($table, $data, $where) {

    $count = 0;

    foreach ($data as $field => $value) {

        $count > 0 ? $update .= ', ' : $update = '';

        $update .= $value === 'NOW()' ? $field . "= NOW()" : $field . '="' . cleanData($value) . '"';

        $count++;
    }

    if (mysql_query('UPDATE ' . $table . ' SET ' . $update . ' WHERE ' . $where . ' LIMIT 1')) {
        return true;
    } else {
        return false;
    }
}

function removeQuery($table, $where) {

    if (!mysql_query('DELETE FROM ' . $table . ' WHERE ' . $where . ' LIMIT 1')) {

        return false;
    }

    return true;
}

function checklogin($username, $password) {
    if (!$username || !$password) {
        echo 'Error! Please write Username';
        return false;
    }

    if (strlen($username) < 4) {
        echo 'Short username.';
        return false;
    }

    if (strlen($password) < 8) {
        echo 'short password';
        return false;
    }
}

function getSetting($name) {
    if ($data_query = mysql_query("SELECT * FROM " . TBL_SETTINGS . " WHERE name = '" . $name . "'")) {
        $data = mysql_fetch_array($data_query);
        return $data['value'];
    } else {
        return false;
    }
}

function getSettingArray($array) {

    $sql = 'SELECT * FROM ' . TBL_SETTINGS . ' WHERE ';
    if (is_array($array)) {
        $i = 0;
        foreach ($array as $name) {
            if ($i == 0) {
                $sql .= 'name="' . $name . '" ';
                $i = 1;
            } else {
                $sql .= 'OR name="' . $name . '" ';
            }
        }
    } else {
        return false;
    }

    if ($data_query = mysql_query($sql)) {
        $setting = array();
        while ($data = mysql_fetch_array($data_query)) {
            $setting[$data['name']] = $data['value'];
        }
        return $setting;
    } else {
        return false;
    }
}

function updateSettings($name, $value) {
    $data = array('value' => $value);
    if (!updateQuery(TBL_SETTINGS, $data, "name='" . $name . "'")) {
        return false;
    } else {
        return true;
    }
}

function limitstr($str, $max) {
    $newstr = substr($str, 0, $max);
    if (strlen($str) > strlen($newstr)) {
        return $newstr . ' . . . .';
    } else {
        return $newstr;
    }
}

function limittext($text, $max) {
    if (strlen($text) >= $max) {
        $text = trim(substr($text, 0, $max));
        $words = explode(' ', $text);
        $text = implode(' ', array_slice($words, 0, count($words) - 1)) . '...';
    }
    return $text;
}

function redirect($location) {
    header('Location: ' . $location . '');
    exit();
}

function getReferer($remove_id = false) {
    foreach ($_GET as $key => $value) {
        if ($value) {
            if ($key != 'id' || !$remove_id) {
                if (isset($referer)) {
                    $referer .= '&';
                } else {
                    $referer = '';
                }
                $referer .= $key . '=' . $value;
            }
        }
    }
    if (isset($referer)) {
        return '?' . $referer;
    }
}

function encrypt($str) {

    $str = sha1(sha1(md5($str)));

    return $str;
}

function isBlank($var) {
    if (!isset($var)) {
        return true;
    }

    if (empty($var)) {
        return true;
    }

    return false;
}

function checkPositiveINT($pagenumber) {
    if (preg_match('|^[1-9][0-9]*$|', $pagenumber)) {
        return true;
    }

    return false;
}

function containsTLD($string) {
    preg_match(
            "/(AC($|\/)|\.AD($|\/)|\.AE($|\/)|\.AERO($|\/)|\.AF($|\/)|\.AG($|\/)|\.AI($|\/)|\.AL($|\/)|\.AM($|\/)|\.AN($|\/)|\.AO($|\/)|\.AQ($|\/)|\.AR($|\/)|\.ARPA($|\/)|\.AS($|\/)|\.ASIA($|\/)|\.AT($|\/)|\.AU($|\/)|\.AW($|\/)|\.AX($|\/)|\.AZ($|\/)|\.BA($|\/)|\.BB($|\/)|\.BD($|\/)|\.BE($|\/)|\.BF($|\/)|\.BG($|\/)|\.BH($|\/)|\.BI($|\/)|\.BIZ($|\/)|\.BJ($|\/)|\.BM($|\/)|\.BN($|\/)|\.BO($|\/)|\.BR($|\/)|\.BS($|\/)|\.BT($|\/)|\.BV($|\/)|\.BW($|\/)|\.BY($|\/)|\.BZ($|\/)|\.CA($|\/)|\.CAT($|\/)|\.CC($|\/)|\.CD($|\/)|\.CF($|\/)|\.CG($|\/)|\.CH($|\/)|\.CI($|\/)|\.CK($|\/)|\.CL($|\/)|\.CM($|\/)|\.CN($|\/)|\.CO($|\/)|\.COM($|\/)|\.COOP($|\/)|\.CR($|\/)|\.CU($|\/)|\.CV($|\/)|\.CX($|\/)|\.CY($|\/)|\.CZ($|\/)|\.DE($|\/)|\.DJ($|\/)|\.DK($|\/)|\.DM($|\/)|\.DO($|\/)|\.DZ($|\/)|\.EC($|\/)|\.EDU($|\/)|\.EE($|\/)|\.EG($|\/)|\.ER($|\/)|\.ES($|\/)|\.ET($|\/)|\.EU($|\/)|\.FI($|\/)|\.FJ($|\/)|\.FK($|\/)|\.FM($|\/)|\.FO($|\/)|\.FR($|\/)|\.GA($|\/)|\.GB($|\/)|\.GD($|\/)|\.GE($|\/)|\.GF($|\/)|\.GG($|\/)|\.GH($|\/)|\.GI($|\/)|\.GL($|\/)|\.GM($|\/)|\.GN($|\/)|\.GOV($|\/)|\.GP($|\/)|\.GQ($|\/)|\.GR($|\/)|\.GS($|\/)|\.GT($|\/)|\.GU($|\/)|\.GW($|\/)|\.GY($|\/)|\.HK($|\/)|\.HM($|\/)|\.HN($|\/)|\.HR($|\/)|\.HT($|\/)|\.HU($|\/)|\.ID($|\/)|\.IE($|\/)|\.IL($|\/)|\.IM($|\/)|\.IN($|\/)|\.INFO($|\/)|\.INT($|\/)|\.IO($|\/)|\.IQ($|\/)|\.IR($|\/)|\.IS($|\/)|\.IT($|\/)|\.JE($|\/)|\.JM($|\/)|\.JO($|\/)|\.JOBS($|\/)|\.JP($|\/)|\.KE($|\/)|\.KG($|\/)|\.KH($|\/)|\.KI($|\/)|\.KM($|\/)|\.KN($|\/)|\.KP($|\/)|\.KR($|\/)|\.KW($|\/)|\.KY($|\/)|\.KZ($|\/)|\.LA($|\/)|\.LB($|\/)|\.LC($|\/)|\.LI($|\/)|\.LK($|\/)|\.LR($|\/)|\.LS($|\/)|\.LT($|\/)|\.LU($|\/)|\.LV($|\/)|\.LY($|\/)|\.MA($|\/)|\.MC($|\/)|\.MD($|\/)|\.ME($|\/)|\.MG($|\/)|\.MH($|\/)|\.MIL($|\/)|\.MK($|\/)|\.ML($|\/)|\.MM($|\/)|\.MN($|\/)|\.MO($|\/)|\.MOBI($|\/)|\.MP($|\/)|\.MQ($|\/)|\.MR($|\/)|\.MS($|\/)|\.MT($|\/)|\.MU($|\/)|\.MUSEUM($|\/)|\.MV($|\/)|\.MW($|\/)|\.MX($|\/)|\.MY($|\/)|\.MZ($|\/)|\.NA($|\/)|\.NAME($|\/)|\.NC($|\/)|\.NE($|\/)|\.NET($|\/)|\.NF($|\/)|\.NG($|\/)|\.NI($|\/)|\.NL($|\/)|\.NO($|\/)|\.NP($|\/)|\.NR($|\/)|\.NU($|\/)|\.NZ($|\/)|\.OM($|\/)|\.ORG($|\/)|\.PA($|\/)|\.PE($|\/)|\.PF($|\/)|\.PG($|\/)|\.PH($|\/)|\.PK($|\/)|\.PL($|\/)|\.PM($|\/)|\.PN($|\/)|\.PR($|\/)|\.PRO($|\/)|\.PS($|\/)|\.PT($|\/)|\.PW($|\/)|\.PY($|\/)|\.QA($|\/)|\.RE($|\/)|\.RO($|\/)|\.RS($|\/)|\.RU($|\/)|\.RW($|\/)|\.SA($|\/)|\.SB($|\/)|\.SC($|\/)|\.SD($|\/)|\.SE($|\/)|\.SG($|\/)|\.SH($|\/)|\.SI($|\/)|\.SJ($|\/)|\.SK($|\/)|\.SL($|\/)|\.SM($|\/)|\.SN($|\/)|\.SO($|\/)|\.SR($|\/)|\.ST($|\/)|\.SU($|\/)|\.SV($|\/)|\.SY($|\/)|\.SZ($|\/)|\.TC($|\/)|\.TD($|\/)|\.TEL($|\/)|\.TF($|\/)|\.TG($|\/)|\.TH($|\/)|\.TJ($|\/)|\.TK($|\/)|\.TL($|\/)|\.TM($|\/)|\.TN($|\/)|\.TO($|\/)|\.TP($|\/)|\.TR($|\/)|\.TRAVEL($|\/)|\.TT($|\/)|\.TV($|\/)|\.TW($|\/)|\.TZ($|\/)|\.UA($|\/)|\.UG($|\/)|\.UK($|\/)|\.US($|\/)|\.UY($|\/)|\.UZ($|\/)|\.VA($|\/)|\.VC($|\/)|\.VE($|\/)|\.VG($|\/)|\.VI($|\/)|\.VN($|\/)|\.VU($|\/)|\.WF($|\/)|\.WS($|\/)|\.XN--0ZWM56D($|\/)|\.XN--11B5BS3A9AJ6G($|\/)|\.XN--80AKHBYKNJ4F($|\/)|\.XN--9T4B11YI5A($|\/)|\.XN--DEBA0AD($|\/)|\.XN--G6W251D($|\/)|\.XN--HGBK6AJ7F53BBA($|\/)|\.XN--HLCJ6AYA9ESC7A($|\/)|\.XN--JXALPDLP($|\/)|\.XN--KGBECHTV($|\/)|\.XN--ZCKZAH($|\/)|\.YE($|\/)|\.YT($|\/)|\.YU($|\/)|\.ZA($|\/)|\.ZM($|\/)|\.ZW)/i", $string, $M);
    $has_tld = (count($M) > 0) ? true : false;
    return $has_tld;
}

/*
 * checking for urls in the message
 */

function cleaner($url) {
    $U = explode(' ', $url);

    $W = array();
    foreach ($U as $k => $u) {
        if (stristr($u, ".")) { //only preg_match if there is a dot    
            if (containsTLD($u) === true) {
                unset($U[$k]);
                return cleaner(implode(' ', $U));
            }
        }
    }
    return implode(' ', $U);
}

function remove_contacts($text) {
    $text = preg_replace('/\+?[0-9][0-9()-\s+]{4,20}[0-9]/', '[blocked]', $text); /* checking for contact number and replacing it */
    $text = preg_replace('#[^\s]+@[^\s]+#', ' [blocked] ', $text); /* checking and replacing email address */

    return cleaner($text);
}

?>