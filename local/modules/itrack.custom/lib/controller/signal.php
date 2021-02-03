<?php
namespace iTrack\Custom\Controller;

use Bitrix\Main\Engine\Controller;

class Signal extends Controller
{
    public function getSignalAction($phone)
    {
        $phoneHelper = new PhoneHelper($phone);
        return $phoneHelper->getTimezone();
    }


}

