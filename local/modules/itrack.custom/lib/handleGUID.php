<?php

namespace iTrack\Custom;

class handleGUID
{

    public static $strufpropContact = 'WERTY';
    public static $strufpropDeal = 'ZXCVBN';
    public static $strufpropCompany = 'ABCDEF';
    public static $strufpropLead = 'FGHJKL';

    public static $debug = true;

    public function __construct()
    {
        if (!class_exists('CCrmContact') or
            !class_exists('CCrmDeal') or
            !class_exists('CCrmCompany')) {
            \Bitrix\Main\Loader::includeModule('crm');
        }
    }

    public function fOnAfterCrmCompanyAdd(&$arFields)
    {
        if (empty($arFields['ORIGIN_ID'])) {
            $strGUID = $this->makeGUID();
            $arFields['ORIGIN_ID'] = $strGUID;
            $arFields[self::$strufpropCompany] = $strGUID;
            //$this->updPropCompany($arFields,$strGUID);
        }
    }

    public function fOnAfterCrmContactAdd(&$arFields)
    {
        if (empty($arFields['ORIGIN_ID'])) {
            $strGUID = $this->makeGUID();
            $arFields['ORIGIN_ID'] = $strGUID;
            $arFields[self::$strufpropContact] = $strGUID;
            //$this->updPropContact($arFields,$strGUID);
        }
    }

    public function fOnAfterCrmDealAdd(&$arFields)
    {
        if (empty($arFields['ORIGIN_ID'])) {
            $strGUID = $this->makeGUID();
            $arFields['ORIGIN_ID'] = $strGUID;
            $arFields[self::$strufpropDeal] = $strGUID;
            //$this->updPropDeal($arFields,$strGUID);
        }
    }

    public function fOnAfterCrmLeadAdd(&$arFields)
    {
        if (empty($arFields['ORIGIN_ID'])) {
            $strGUID = $this->makeGUID();
            $arFields['ORIGIN_ID'] = $strGUID;
            $arFields[self::$strufpropLead] = $strGUID;
        }
    }

    public function makeGUID($strdata = '')
    {
        //CIntranetUtils::makeGUID(md5(123)); //{202cb962-ac59-075b-964b-07152d234b70} str_replace([],'',$)
        if (mb_strlen($strdata) !== 32) {
            $strdata .= $randomstr = substr(str_shuffle('0123456789abcdefghjkloiuhytfarsvwf'), 1, 32);
        }
        $strdata = mb_substr($strdata, 0, 8) . '-' . mb_substr($strdata, 8, 4) . '-' . mb_substr($strdata, 12, 4) . '-' . mb_substr($strdata, 16, 4) . '-' . mb_substr($strdata, 20);
        return $strdata;
    }

    public function updPropDeal($arfield, $sGUID)
    {
        $arUpdateDeal[self::$strufpropDeal] = $sGUID;
        $obDeal = new \CCrmDeal(false);
        if (!$obDeal->Update($arfield['ID']
            , $arUpdateDeal
            , $bCompare = true
            , $bUpdateSearch = false
            , $options = array(
                "ENABLE_SYSTEM_EVENTS" => false
            , "REGISTER_STATISTICS" => false
            , "CURRENT_USER" => $arfield['MODIFY_BY_ID']
            ))) {
        }
    }

    public function updPropContact($arfield, $sGUID)
    {
        $arUpdateContact[self::$strufpropContact] = $sGUID;
        $obContact = new \CCrmContact(false);
        if (!$obContact->Update($arfield['ID']
            , $arUpdateContact
            , $bCompare = true
            , $bUpdateSearch = false
            , $options = array(
                "ENABLE_SYSTEM_EVENTS" => false
            , "REGISTER_STATISTICS" => false
            , "CURRENT_USER" => $arfield['MODIFY_BY_ID']
            ))) {
        }
    }

    public function updPropCompany($arfield, $sGUID)
    {
        $arUpdateCompany[self::$strufpropCompany] = $sGUID;
        $obCompany = new \CCrmCompany(false);
        if (!$obCompany->Update($arfield['ID']
            , $arUpdateCompany
            , $bCompare = true
            , $bUpdateSearch = false
            , $options = array(
                "ENABLE_SYSTEM_EVENTS" => false
            , "REGISTER_STATISTICS" => false
            , "CURRENT_USER" => $arfield['MODIFY_BY_ID']
            ))) {
        }
    }


}
