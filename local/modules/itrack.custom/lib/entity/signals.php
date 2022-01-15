<?php

namespace iTrack\Custom\Entity;

use Bitrix\Main\Entity\DataManager;
use Bitrix\Main\Entity\IntegerField;
use Bitrix\Main\Entity\ReferenceField;
use Bitrix\Main\Entity\StringField;
use Bitrix\Main\Entity\DateField;
use Bitrix\Main\Entity\TextField;
use Bitrix\Main\UserTable;
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Bitrix\Crm;

class SignalsTable extends DataManager
{

    public static function getTableName()
    {
        return 'itrack_signals';
    }

    public static function getFilePath()
    {
        return __FILE__;
    }

    public static function getMap()
    {
        return array(
            new IntegerField('ID', array('primary' => true, 'autocomplete' => true)),
            new StringField('STATUS'),
            new StringField('DEALID'),
            new DateField('DATESTATUS'),

        );
    }

}
