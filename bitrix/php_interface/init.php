<?php

use \Bitrix\Main;

if (\Bitrix\Main\Loader::includeModule('itrack.custom')) {
    \iTrack\Custom\Application::init();
}


