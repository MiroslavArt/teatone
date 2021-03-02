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
}