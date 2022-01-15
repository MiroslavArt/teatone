<?php

namespace iTrack\Custom\Agent;

class Exchange
{
    public static function import1c($type = 'local')
    {
        switch ($type) {
            case 'local':
                try {
                    $import = new \iTrack\Custom\Exchange\FileImport();
                    $import->run();
                } catch(\Exception $e) {
                    \iTrack\Custom\Application::log('Error import '.$e, 'import1c.log');
                }
                break;
            case 'ftp':
                \iTrack\Custom\Handlers\Import1cftp::makeimport();
                break;
        }

        return '\iTrack\Custom\Agent\Exchange::import1c('.$type.');';
    }

    public static function cleanduplicates()
    {
        \CModule::IncludeModule("iblock");
        $iblockid = 20;
        $arFilter = Array(
            "IBLOCK_ID"=>$iblockid,
            "ACTIVE"=>"Y"
        );
        $res = \CIBlockElement::GetList(Array(), $arFilter, Array("CODE"));
        while($ar_fields = $res->GetNext())
        {
            if($ar_fields['CNT']>1) {
                $arFilter = Array(
                    "IBLOCK_ID"=>$iblockid,
                    "CODE"=>$ar_fields['CODE']
                );
                $res2 = \CIBlockElement::GetList(Array("ÏD"=>DESC), $arFilter, false, false, ["ÏD"]);

                $maxcount = $ar_fields['CNT'];
                $curcount = 1;

                while($ar_fields2 = $res2->GetNext())
                {
                    if($curcount>1 && $curcount<=$maxcount) {
                        echo \CIBlockElement::Delete($ar_fields2["ID"]);
                    }
                    $curcount++;
                }
            }
        }
        return '\iTrack\Custom\Agent\Exchange::cleanduplicates();';
    }
}