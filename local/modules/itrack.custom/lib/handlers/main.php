<?php
namespace iTrack\Custom\Handlers;

use Bitrix\Main\Config\Option;
use Bitrix\Main\Page\Asset;
use iTrack\Custom\Constants;

class Main
{
    /*public static function onProlog()
    {
        global $USER;
        if($USER->IsAuthorized() && $USER->GetID() == Constants::ITRACK_USER_ID) {
            $asset = Asset::getInstance();
            $asset->addString('<script>BX.ready(function () {document.querySelector("body").classList.add("itrack-user");});</script>');
        }
    }*/

    public static function onEpilog()
    {
        $urlTemplates = [
            'deal_detail' => ltrim(Option::get('crm', 'path_to_deal_details', '', SITE_ID), '/'),
            'deal_kanban' => ltrim(Option::get('crm', 'path_to_deal_kanban', '', SITE_ID), '/'),
            'deal_kanban_category' => ltrim(Option::get('crm', 'path_to_deal_kanban', '', SITE_ID), '/').'category/#category_id#/',
            'deal_list' => ltrim(Option::get('crm', 'path_to_deal_list', '', SITE_ID), '/'),
            //'deal_list_category' => ltrim(Option::get('crm', 'path_to_deal_list', '', SITE_ID), '/').'category/#category_id#/',
            'deal_list_category' => 'crm/deal/category/#category_id#/'
        ];

        //$arVars = array();

        $page = \CComponentEngine::parseComponentPath('/', $urlTemplates, $arVars);
        $type = '';
        if($page !== false) {
            switch($page) {
                case 'deal_detail':
                    $type = 'detail';
                    break;
                case 'deal_kanban':
                case 'deal_kanban_category':
                    $type = 'kanban';
                    break;
                case 'deal_list':
                case 'deal_list_category':
                    $type = 'list';
                    break;
            }
        }

        if(!empty($type)) {
            \CJSCore::init('crm_fill_signal');
            $asset = Asset::getInstance();
            $asset->addString('<script>BX.ready(function () {BX.iTrack.Crm.FillSignal.init("'.$type.'");});</script>');
        }
    }
}
