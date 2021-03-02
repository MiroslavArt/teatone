<?php

namespace iTrack\Custom;

use Bitrix\Main\Config\Option;
use Bitrix\Main\EventManager;
use Bitrix\Main\Page\Asset;


class Application
{
    public static function init()
    {
        self::initJsHandlers();
        self::initEventHandlers();
    }

    protected static function initJsHandlers()
    {

    }

    public static function initEventHandlers()
    {
        $eventManager = EventManager::getInstance();
        $eventManager->addEventHandler("crm"
            , "OnAfterCrmCompanyAdd"
            ,  array("\iTrack\Custom\handleGUID", "fOnAfterCrmCompanyAdd"));

        $eventManager->addEventHandler("crm"
            , "OnAfterCrmContactAdd"
            ,  array("\iTrack\Custom\handleGUID", "fOnAfterCrmContactAdd"));

        $eventManager->addEventHandler("crm"
            , "OnAfterCrmDealAdd"
            ,  array("\iTrack\Custom\handleGUID", "fOnAfterCrmDealAdd")
        );

        $eventManager->addEventHandler("crm"
            , "OnAfterCrmLeadAdd"
            ,  array("\iTrack\Custom\handleGUID", "fOnAfterCrmLeadAdd"));


        $eventManager->addEventHandler('main','OnEpilog', ['\iTrack\Custom\Handlers\Main','onEpilog']);

    }


    public static function log($msg, $file = 'main.log')
    {
        if(!file_exists($_SERVER['DOCUMENT_ROOT'] . '/local/logs')) {
            mkdir($_SERVER['DOCUMENT_ROOT'] . '/local/logs');
        }
        file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/local/logs/' . $file, date(DATE_COOKIE) . ': ' . $msg . "\n", FILE_APPEND);
    }
}
