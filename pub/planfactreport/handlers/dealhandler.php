<?php
//echo "click";
//writeToLog("yes", 'calls');
writeToLog($_REQUEST, 'calls');
function writeToLog($data, $title = '') {
    $log = "\n------------------------\n";
    $log .= date("Y.m.d G:i:s") . "\n";
    $log .= (strlen($title) > 0 ? $title : 'DEBUG') . "\n";
    $log .= print_r($data, 1);
    $log .= "\n------------------------\n";
    file_put_contents(getcwd() . '/calls.log', $log, FILE_APPEND);
    return true;
}

$auth = $_REQUEST["auth"]["access_token"];
//$auth = "1fbc12600051afdf0050a120000000010000038c18c7b9c49e24c201e9969100064104";
//$domain = "lyantsevich.teatone.softmonster.ru";
$domain = $_REQUEST["auth"]["domain"];
$dealid = $_REQUEST["data"]["FIELDS"]["ID"];

$params = array(
    'get_deal' => 'crm.deal.get?'
        .http_build_query(array(
            'id' => $dealid
        )),
    "get_group" => 'app.option.get?'
        .http_build_query(array(
            'option' => 'planfact_uv_groups'
        ))
);

$out = executeBATCH($params,$domain, $auth);
//echo "<pre>";
//print_r($out);
//echo "</pre>";

$out = executeBATCH($params,$domain, $auth);

//$dealid = $out['result']['result']['get_deal']['ID'];
$stage = $out['result']['result']['get_deal']['STAGE_ID'];
// тут поменять код поле на то, которое на портале
$checklist = $out['result']['result']['get_deal']['UF_CRM_1612443146'];
$meeting = $out['result']['result']['get_deal']['UF_CRM_1612349525'];
$date = date("d.m.Y");
$assigned = $out['result']['result']['get_deal']['MODIFY_BY_ID'];
$sum = $out['result']['result']['get_deal']['OPPORTUNITY'];
$sonetgroup = preg_replace("/[^0-9]/", '', $out['result']['result']['get_group']);

$params = array(
    'get_fields' => 'lists.field.get?'
        .http_build_query(array(
            'IBLOCK_TYPE_ID' => 'lists_socnet',
            'IBLOCK_CODE' => 'listfacts' . $sonetgroup,
            'SOCNET_GROUP_ID' => $sonetgroup
        )),
);
$out = executeBATCH($params,$domain, $auth);
//echo "<pre>";
//print_r($out);
//echo "</pre>";

foreach($out['result']['result']['get_fields'] as $key => $fieldval) {
    if($fieldval['NAME'] == 'value') {
        $propvalue = $key;
    } elseif ($fieldval['NAME'] == 'date') {
        $propdate = $key;
    } elseif ($fieldval['NAME'] == 'type') {
        $proptype = $key;
    } elseif ($fieldval['NAME'] == 'employee') {
        $propasn = $key;
    }
}
// поправить
//if($stage != 'WON') {
//if(!strpos($stage, 'WON')) {
if(!preg_match("/WON/", $stage)) {
    $params = array(
        'add_value' => 'lists.element.add?'
            . http_build_query(array(
                    'IBLOCK_TYPE_ID' => 'lists_socnet',
                    'IBLOCK_CODE' => 'listfacts'.$sonetgroup,
                    'ELEMENT_CODE' => $dealid.'S'.$stage.'A'.$assigned,
                    'FIELDS' => array(
                        'NAME' => $dealid.'S'.$stage.'A'.$assigned,
                        $propvalue => 1,
                        $propdate => $date,
                        $proptype => $stage,
                        $propasn => $assigned
                    )
            ))
    );
} else {
    $params = array(
        'add_value1' => 'lists.element.add?'
            . http_build_query(array(
                'IBLOCK_TYPE_ID' => 'lists_socnet',
                'IBLOCK_CODE' => 'listfacts'.$sonetgroup,
                'ELEMENT_CODE' => $dealid.'S'.$stage.'A'.$assigned.'wonreg',
                'FIELDS' => array(
                    'NAME' => $dealid.'S'.$stage.'A'.$assigned.'wonreg',
                    $propvalue => 1,
                    $propdate => $date,
                    $proptype => $stage,
                    $propasn => $assigned
                )
            )),
        'add_value2' => 'lists.element.add?'
            . http_build_query(array(
                'IBLOCK_TYPE_ID' => 'lists_socnet',
                'IBLOCK_CODE' => 'listfacts'.$sonetgroup,
                'ELEMENT_CODE' => $dealid.'S'.$stage.'A'.$assigned.'wonsum',
                'FIELDS' => array(
                    'NAME' => $dealid.'S'.$stage.'A'.$assigned.'wonsum',
                    $propvalue => $sum,
                    $propdate => $date,
                    $proptype => 'SL',
                    $propasn => $assigned
                )
            )),

    );
}

$out = executeBATCH($params,$domain, $auth);

if($meeting) {
    $params = array(
        'add_value' => 'lists.element.add?'
            . http_build_query(array(
                'IBLOCK_TYPE_ID' => 'lists_socnet',
                'IBLOCK_CODE' => 'listfacts'.$sonetgroup,
                'ELEMENT_CODE' => $dealid.'MT',
                'FIELDS' => array(
                    'NAME' => $dealid.'MT',
                    $propvalue => 1,
                    $propdate => $date,
                    $proptype => 'MT',
                    $propasn => $assigned
                )
            ))
    );
    $out = executeBATCH($params,$domain, $auth);
}

if($checklist) {
    $params = array(
        'add_value' => 'lists.element.add?'
            . http_build_query(array(
                'IBLOCK_TYPE_ID' => 'lists_socnet',
                'IBLOCK_CODE' => 'listfacts'.$sonetgroup,
                'ELEMENT_CODE' => $dealid.'CL',
                'FIELDS' => array(
                    'NAME' => $dealid.'CL',
                    $propvalue => 1,
                    $propdate => $date,
                    $proptype => 'CL',
                    $propasn => $assigned
                )
            ))
    );
    $out = executeBATCH($params,$domain, $auth);
}

//echo "<pre>";
//print_r($out);
//echo "</pre>";


function executeBATCH (array $params, $domain, $auth) {
    $appParams = http_build_query(array(
        'auth' => $auth,
        'halt' => 0,
        'cmd' => $params
    ));
    $appRequestUrl = 'https://'.$domain.'/rest/batch.json';
    $curl=curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_SSL_VERIFYPEER => 0,
        CURLOPT_POST => 1,
        CURLOPT_HEADER => 0,
        CURLOPT_RETURNTRANSFER => 1,
        CURLOPT_URL => $appRequestUrl,
        CURLOPT_POSTFIELDS => $appParams
    ));
    $out=curl_exec($curl);
    $out = json_decode($out, 1);
    return $out;
}



