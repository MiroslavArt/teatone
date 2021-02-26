<?php

namespace iTrack\Custom\Exchange;

use Bitrix\Main\Config\Option;
use iTrack\Custom\Entity\SignalsTable;
use Bitrix\Main\Loader;
use Bitrix\Main\Type;
use iTrack\Custom\Application;

class FileImport
{
    protected $filePath;

    public function __construct()
    {
        $path = Option::get('itrack.custom','1c_import_path','');
        if(empty($path)) {
            throw new \Exception('Empty import path');
        } else {
            $this->filePath = $_SERVER['DOCUMENT_ROOT'].$path;
            if(!file_exists($this->filePath)) {
                throw new \InvalidArgumentException('import path not exists');
            }
        }
        Loader::includeModule('crm');
    }

    public function run()
    {
        $arFiles = scandir($this->filePath, SCANDIR_SORT_ASCENDING);
        if(!empty($arFiles)) {
            foreach($arFiles as $filename) {
                if(stripos($filename, 'Категории') !== false) {
                    self::processDocumentsFile($this->filePath.'/'.$filename);
                }
            }
        }
    }

    public static function processDocumentsFile($filepath)
    {
        Application::log('Start process documentsfile '.$filepath, 'fileimport.log');
        $ufield = \COption::GetOptionString("itrack.custom", "main_uf", "");
        if(!empty($ufield)) {
            $data = simplexml_load_file($filepath);
            Application::log('File loaded. Count documents: '.count($data), 'fileimport.log');
            foreach ($data as $doc) {
                $id = (string)$doc->Ид;
                $status = (string)$doc->СтатусДокумента;
                $statusb = "";

                $arFilterDeal = array('=ORIGIN_ID' => $id);
                $arSelectDeal = array('ID');
                $obResDeal = \Bitrix\Crm\DealTable::query()->setFilter($arFilterDeal)->setSelect($arSelectDeal)->exec()->fetch();
                $dealid = $obResDeal['ID'];

                if ($dealid) {
                    switch ($status) {
                        case "Занесения данных в 1С нового клиента. Карточка клиента в Битриксе есть.":
                            $statusb = 'Клиент занесен в 1С';
                            break;
                        case "Сформирован заказ с бланка заказа":
                            $statusb = 'Заказ сформирован в 1С';
                            break;
                        case "Проблема":
                            $statusb = 'Документ с ошибкой';
                            break;
                        case "Есть оригинал":
                            $statusb = 'Есть оригинал';
                            break;
                        case "Вторая категория":
                            $statusb = 'Есть скан';
                            break;
                        case "Выписан":
                            $statusb = 'Нет документов';
                            break;
                    }

                    $arParams = array($ufield => $statusb);
                    $CCrmDeal = new \CCrmDeal(false);
                    $updateResult = $CCrmDeal->Update($dealid, $arParams);
                    if(!$updateResult) {
                        Application::log('Error update deal : '.$dealid.', error: '.$CCrmDeal->LAST_ERROR, 'fileimport.log');
                    }

                    $fields = array(
                        'STATUS' => $status,
                        'DEALID' => $dealid,
                        'DATESTATUS' => new Type\Date()
                    );
                    $signal = new SignalsTable(false);
                    $addResult = $signal->Add($fields);
                    if(!$addResult->isSuccess()) {
                        Application::log('Error add signal data '.$dealid.', error: '.print_r($addResult->getErrorMessages(), true), 'fileimport.log');
                    }
                }
            }
            Application::log('End process', 'fileimport.log');
        } else {
            Application::log('Uf field not defined', 'fileimport.log');
        }
    }
}