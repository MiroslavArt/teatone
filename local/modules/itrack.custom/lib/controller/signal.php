<?php
namespace iTrack\Custom\Controller;

use Bitrix\Main;
use Bitrix\Crm;
use Bitrix\Main\Engine\Controller;
use Bitrix\Main\Config\Option;

class Signal extends Controller
{
    public function getSignalAction($signals)
    {
        //$phoneHelper = new PhoneHelper($phone);
        //\Bitrix\Main\Diag\Debug::writeToFile($signals, "signals", "__miros.log");
        $ufield = \COption::GetOptionString("itrack.custom", "main_uf");
        if (\Bitrix\Main\Loader::includeModule("crm")) {
            foreach ($signals as $signal) {
                $arFilterDeal = array('ID'=>$signal);
                // тут заменить коды полей при переносе на бой
                //$arSelectD = array('ID', 'UF_CRM_1612334271');
                $arSelectD = array('ID', $ufield);
                $obResDeal = \CCrmDeal::GetListEx(false,$arFilterDeal,false,false,$arSelectD)->Fetch();
                if($obResDeal[$ufield]) {
                    $signalarr[$signal] = $obResDeal[$ufield];
                } else {
                    $signalarr[$signal] = 'Empty';
                }

                /*if($obResDeal['UF_CRM_1612334271']==116) {
                    $signalarr[$signal] = 'Клиент_занесен_в_1С';
                } elseif ($obResDeal['UF_CRM_1612334271']==117) {
                    $signalarr[$signal] = 'Заказ_сформирован в 1С';
                } elseif ($obResDeal['UF_CRM_1612334271']==118) {
                    $signalarr[$signal] = 'Документ_с_ошибкой';
                } elseif ($obResDeal['UF_CRM_1612334271']==119) {
                    $signalarr[$signal] = 'Есть_оригинал';
                } elseif ($obResDeal['UF_CRM_1612334271']==120) {
                    $signalarr[$signal] = 'Есть_скан';
                } elseif ($obResDeal['UF_CRM_1612334271']==121) {
                    $signalarr[$signal] = 'Нет_документов';
                } elseif ($obResDeal['UF_CRM_1612334271']=="") {
                    $signalarr[$signal] = 'Empty';
                }*/
            }
        }
        //$signalarr[17] = '1C';
        return $signalarr;
    }
}

