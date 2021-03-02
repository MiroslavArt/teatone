<?php

namespace iTrack\Custom;

use Bitrix\Main\Config\Option;
use Bitrix\Main\Loader;

class handleGUID
{
    public static function fOnAfterCrmCompanyAdd(&$arFields)
    {
        if (empty($arFields['ORIGIN_ID'])) {
            self::updateEntity($arFields['ID'], \CCrmOwnerType::Company);
        }
    }

    public static function fOnAfterCrmContactAdd(&$arFields)
    {
        if (empty($arFields['ORIGIN_ID'])) {
            self::updateEntity($arFields['ID'], \CCrmOwnerType::Contact);
        }
    }

    public static function fOnAfterCrmDealAdd(&$arFields)
    {
        if (empty($arFields['ORIGIN_ID'])) {
            self::updateEntity($arFields['ID'], \CCrmOwnerType::Deal);
        }
    }

    public static function fOnAfterCrmLeadAdd(&$arFields)
    {
        if (empty($arFields['ORIGIN_ID'])) {
            self::updateEntity($arFields['ID'], \CCrmOwnerType::Lead);
        }
    }

    public static function makeGUID($strdata = '')
    {
        //CIntranetUtils::makeGUID(md5(123)); //{202cb962-ac59-075b-964b-07152d234b70} str_replace([],'',$)
        if (mb_strlen($strdata) !== 32) {
            $strdata .= $randomstr = substr(str_shuffle('0123456789abcdefghjkloiuhytfarsvwf'), 1, 32);
        }
        $strdata = mb_substr($strdata, 0, 8) . '-' . mb_substr($strdata, 8, 4) . '-' . mb_substr($strdata, 12, 4) . '-' . mb_substr($strdata, 16, 4) . '-' . mb_substr($strdata, 20);
        return $strdata;
    }

    protected static function updateEntity($entityId, $entityType)
    {
        if(Loader::includeModule('crm')) {
            $ufField = Option::get('itrack.custom','uf_'.strtolower(\CCrmOwnerType::ResolveName($entityType)).'_guid','');
            if(!empty($ufField)) {
                $guid = self::makeGUID();
                switch($entityType) {
                    case \CCrmOwnerType::Lead:
                        $obEntity = new \CCrmLead(false);
                        break;
                    case \CCrmOwnerType::Deal:
                        $obEntity = new \CCrmDeal(false);
                        break;
                    case \CCrmOwnerType::Contact:
                        $obEntity = new \CCrmContact(false);
                        break;
                    case \CCrmOwnerType::Company:
                        $obEntity = new \CCrmCompany(false);
                        break;
                }
                if($obEntity) {
                    $arUpdateFields = [
                        'ORIGIN_ID' => $guid,
                        $ufField => $guid
                    ];
                    $res = $obEntity->Update(
                        $entityId,
                        $arUpdateFields,
                        true,
                        false,
                        [
                            "ENABLE_SYSTEM_EVENTS" => false,
                            "REGISTER_STATISTICS" => false
                        ]
                    );
                }
            }
        }
    }
}
