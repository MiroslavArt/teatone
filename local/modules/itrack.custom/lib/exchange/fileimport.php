<?php

namespace iTrack\Custom\Exchange;

use Bitrix\Main\Config\Option;
use iTrack\Custom\Entity\SignalsTable;
use Bitrix\Main\Loader;
use Bitrix\Main\Type;
use iTrack\Custom\Application;
use Bitrix\Main\Result;
use Bitrix\Main\Error;

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
                    $result = self::processDocumentsFile($this->filePath.'/'.$filename);
                    if($result->isSuccess()) {
                        if(!rename($this->filePath.'/'.$filename, $this->filePath.'/processed/'.$filename)) {
                            Application::log('Error move processed file '.$filename, 'fileimport.log');
                        }
                    }
                }
                if(stripos($filename, 'Контрагенты') !== false) {
                    $result = self::processContagentsFile($this->filePath.'/'.$filename);
                    if($result->isSuccess()) {
                        if(!rename($this->filePath.'/'.$filename, $this->filePath.'/processed/'.$filename)) {
                            Application::log('Error move processed file '.$filename, 'fileimport.log');
                        }
                    }
                }
            }
        }
    }

    public static function processDocumentsFile($filepath)
    {
        $result = new Result();
        Application::log('Start process documentsfile '.$filepath, 'fileimport.log');
        $ufield = \COption::GetOptionString("itrack.custom", "main_uf", "");
        if(!empty($ufield)) {
            $data = simplexml_load_file($filepath);
            Application::log('File loaded. Count documents: '.count($data), 'fileimport.log');
            foreach ($data as $doc) {
                $id = (string)$doc->Ид;
                $status = (string)$doc->СтатусДокумента;
				$sum = (float)$doc->СуммаДокумента;
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

                    $fields = array(
                        'STATUS' => $status,
                        'DEALID' => $dealid,
                        'DATESTATUS' => new Type\Date()
                    );
                    $signal = new SignalsTable(false);
                    $addResult = $signal->Add($fields);
                    if(!$addResult->isSuccess()) {
                        Application::log('Error add signal data '.$dealid.', error: '.print_r($addResult->getErrorMessages(), true), 'fileimport.log');
                    } else {
                        $arParams = array($ufield => $statusb);
						if(!empty($sum) && $sum > 0) {
							$arParams['OPPORTUNITY'] = $sum;
						}
                        $CCrmDeal = new \CCrmDeal(false);
                        $updateResult = $CCrmDeal->Update($dealid, $arParams);
                        if(!$updateResult) {
                            Application::log('Error update deal : '.$dealid.', error: '.$CCrmDeal->LAST_ERROR, 'fileimport.log');
                        }
                    }
                }
            }
            Application::log('End process', 'fileimport.log');
        } else {
            Application::log('Uf field not defined', 'fileimport.log');
            $result->addError(new Error('Uf field not defined'));
        }

        return $result;
    }

    public static function processContagentsFile($filepath)
    {
        $result = new Result();
        Application::log('Start process contragentsfile '.$filepath, 'fileimport.log');

        $data = simplexml_load_file($filepath);
        Application::log('File loaded. Count contragents: '.count($data), 'fileimport.log');
        foreach ($data as $contragent) {
            $xmlId = (string)$contragent->Ид;
            $name = (string)$contragent->Наименование;
            $legalName = (string)$contragent->ОфициальноеНаименование;
            $priceType = (string)$contragent->ТипЦен;
            $inn = (string)$contragent->ИНН;
            $kpp = (string)$contragent->КПП;
            $region = (string)$contragent->Регион;
            $status = trim((string)$contragent->СтатусКлиента);
            $edi = (string)$contragent->EDI === 'True' ? 1 : 0;
            $dz = (string)$contragent->ДЗ;
            $pdz = (string)$contragent->ПДЗ;

            $docNumber = [];
            $docDate = [];
            $limit = '';
            $delay = '';
            if(!empty($contragent->Договоры)) {
                foreach ($contragent->Договоры->Договор as $doc) {
                    $docNumber[] = (string)$doc->Номер;
                    $date = (string)$doc->Дата;
                    if(!empty($date)) {
                        $obDate = \DateTime::createFromFormat('Y-m-d', $date);
                        $docDate[] = $obDate->format('d.m.Y');
                    }
                    $limit = (string)$doc->ДенежныйЛимит;
                    $delay = (string)$doc->Отсрочка;
                }
            }
            $address = '';
            if(!empty($contragent->Адреса) && !empty($contragent->Адреса->АдресРегистрации)) {
                $address = (string)$contragent->Адреса->АдресРегистрации->Представление;
            }
            $phone = '';
            if(!empty($contragent->Контакты)) {
                foreach($contragent->Контакты->Контакт as $contact) {
                    if((string)$contact->Тип === 'Телефон') {
                        $phone = (string)$contact->Значение;
                    }
                }
            }

            $dbStatus = \Bitrix\Crm\StatusTable::query()
                ->setFilter(['ENTITY_ID' => 'COMPANY_TYPE'])
                ->setSelect(['STATUS_ID','NAME'])
                ->exec();
            $arStatuses = [];
            while($arStatus = $dbStatus->fetch()) {
                $arStatuses['ID2VAL'][$arStatus['STATUS_ID']] = strtoupper($arStatus['NAME']);
                $arStatuses['VAL2ID'][strtoupper($arStatus['NAME'])] = $arStatus['STATUS_ID'];
            }

            $dbCompany = \CCrmCompany::GetListEx(
                [],
                ['=ORIGIN_ID' => $xmlId, 'CHECK_PERMISSIONS' => 'N'],
                false,
                false,
                [
                    'ID',
                    'TITLE',
                    'COMPANY_TYPE',
                    'ADDRESS',
                    'PHONE',
                    'UF_CRM_1615807602912', // тип цен
                    'UF_CRM_1615805715', // регион
                    'UF_CRM_1611170702', // EDI
                    'UF_CRM_1615807471335', // ДЗ
                    'UF_CRM_1615807483687', // ПДЗ
                    'UF_CRM_1615807068525', // номер договора
                    'UF_CRM_1615807112712', // дата договора
                    'UF_CRM_1615807152998', // денежный лимит
                    'UF_CRM_1615807175271', // отсрочка

                ]
            );
            if($arCompany = $dbCompany->Fetch()) {
                $arUpdateFields = [];
                if($arCompany['UF_CRM_1611170702'] !== $edi) {
                    $arUpdateFields['UF_CRM_1611170702'] = $edi;
                }
                if($arCompany['UF_CRM_1615807602912'] !== $priceType) {
                    $arUpdateFields['UF_CRM_1615807602912'] = $priceType;
                }
                if($arCompany['UF_CRM_1615807471335'] !== $dz) {
                    $arUpdateFields['UF_CRM_1615807471335'] = $dz;
                }
                if($arCompany['UF_CRM_1615807483687'] !== $pdz) {
                    $arUpdateFields['UF_CRM_1615807483687'] = $pdz;
                }
                if($arCompany['UF_CRM_1615807152998'] !== $limit) {
                    $arUpdateFields['UF_CRM_1615807152998'] = $limit;
                }
                if($arCompany['UF_CRM_1615807175271'] !== $delay) {
                    $arUpdateFields['UF_CRM_1615807175271'] = $delay;
                }
                if($arCompany['COMPANY_TYPE'] !== $arStatuses['VAL2ID'][strtoupper($status)]) {
                    $arUpdateFields['COMPANY_TYPE'] = $arStatuses['VAL2ID'][strtoupper($status)];
                }
                if($arCompany['PHONE'] !== $phone && empty($arCompany['PHONE'])) {
                    $arUpdateFields['HAS_PHONE'] = 'Y';
                    $arUpdateFields['FM']['PHONE']['n0'] = [
                        'VALUE' => $phone,
                        'VALUE_TYPE' => 'WORK'
                    ];
                }
                if(!empty($arUpdateFields)) {
                    Application::log('Start update contragent: '.$xmlId, 'fileimport.log');
                    $obCompany = new \CCrmCompany(false);
                    $updRes = $obCompany->Update($arCompany['ID'], $arUpdateFields);
                    if(!$updRes) {
                        Application::log('Error update: '.$obCompany->LAST_ERROR.PHP_EOL.print_r($arUpdateFields, true), 'fileimport.log');
                    }
                    Application::log('End update contragent', 'fileimport.log');
                }
            } else {
                Application::log('Start add contragent: '.$xmlId, 'fileimport.log');
                $arAddFields = [
                    'ORIGIN_ID' => $xmlId,
                    'ORIGINATOR_ID' => 'ONEC_FTP',
                    'TITLE' => $name,
                    'COMPANY_TYPE' => $arStatuses['VAL2ID'][strtoupper($status)],
                    'ADDRESS' => $address,
                    'ADDRESS_LEGAL' => $address,
                    'UF_CRM_1615807602912' => $priceType,
                    'UF_CRM_1615805715' => $region,
                    'UF_CRM_1611170702' => $edi,
                    'UF_CRM_1615807471335' => $dz,
                    'UF_CRM_1615807483687' => $pdz,
                    'UF_CRM_1615807068525' => $docNumber,
                    'UF_CRM_1615807112712' => $docDate,
                    'UF_CRM_1615807152998' => $limit,
                    'UF_CRM_1615807175271' => $delay
                ];
                if(!empty($phone)) {
                    $arAddFields['HAS_PHONE'] = 'Y';
                    $arAddFields['FM']['PHONE']['n0'] = [
                        'VALUE' => $phone,
                        'VALUE_TYPE' => 'WORK'
                    ];
                }
                $obCompany = new \CCrmCompany(false);
                $addResult = $obCompany->Add($arAddFields);
                if($addResult > 0) {
                    $reqFields = [
                        "ENTITY_TYPE_ID" => \CCrmOwnerType::Company,
                        "ENTITY_ID" => $addResult,
                        "PRESET_ID" => (stripos($legalName, 'ИП') === 0 || stripos($legalName, 'Индивидуальный предприниматель') === 0) ? 2 : 1,
                        'NAME' => $name,
                        'RQ_COMPANY_NAME' => $name,
                        'RQ_COMPANY_FULL_NAME' => $legalName,
                        'RQ_INN' => $inn,
                        'RQ_KPP' => $kpp
                    ];
                    $requisitesEntity = new \Bitrix\Crm\EntityRequisite();
                    $reqAdd = $requisitesEntity->add($reqFields);
                    if(!$reqAdd->isSuccess()) {
                        Application::log('Error add requisite for contragent : '.$addResult.'. Error: '.print_r($reqAdd->getErrorMessages(), true).PHP_EOL.'Fields: '.print_r($reqFields, true), 'fileimport.log');
                    }
                } else {
                    Application::log('Error add contragent: '.$obCompany->LAST_ERROR.PHP_EOL.print_r($arAddFields, true), 'fileimport.log');
                }
                Application::log('End add contragent: '.$xmlId, 'fileimport.log');
            }
        }

        Application::log('End process contragentsfile '.$filepath, 'fileimport.log');

        return $result;
    }
}