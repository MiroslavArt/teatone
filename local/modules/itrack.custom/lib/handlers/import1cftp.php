<?php

namespace iTrack\Custom\Handlers;

use iTrack\Custom\Entity\SignalsTable;
use Bitrix\Main\Type;
use Bitrix\Main\Loader;
use Bitrix\Main\Config\Option;
use Bitrix\Crm;


class Import1cftp
{

    public static function makeimport() {
        //$ftp_server = 'teatone.softmonster.ru';
        //$ftp_user_name = 'teatone-ftp';
        //$ftp_user_pass = 'ckznOt5!v44tJMsz';
        $ftp_server = \COption::GetOptionString("itrack.custom", "main_ftp");
        $ftp_user_name = \COption::GetOptionString("itrack.custom", "main_ftpl");
        $ftp_user_pass = \COption::GetOptionString("itrack.custom", "main_ftpp");
        $ufield = \COption::GetOptionString("itrack.custom", "main_uf");



        $conn_id = ftp_ssl_connect($ftp_server);
        Loader::includeModule('itrack.custom');
        echo Loader::includeModule('crm');
        // проверка имени пользователя и пароля
        $login_result = ftp_login($conn_id, $ftp_user_name, $ftp_user_pass);

        echo ftp_pwd($conn_id); // /

        $contents = ftp_nlist($conn_id, '');

        print_r($contents);
        $file = 'categories.xml';
        if(ftp_chdir($conn_id, 'import1c')) {
            $file_size = ftp_size($conn_id, $file);
            if ($file_size != -1) {
                echo 'Файл существует';
                $handle = fopen('php://temp', 'r+');
                if (ftp_fget($conn_id, $handle, $file, FTP_BINARY, 0)) {
                    $fstats = fstat($handle);
                    fseek($handle, 0);
                    $contents = fread($handle, $fstats['size']);
                    echo $contents;
                    $data = simplexml_load_string($contents);
                    foreach ($data as $doc) {
                        $id = (string)$doc->Ид;
                        //echo "<pre>";
                        //print_r($id);
                        //echo "</pre>";
                        $status = (string)$doc->СтатусДокумента;
                        //echo "<pre>";
                        //print_r($status);
                        //echo "</pre>";
                        $statusb = "";

                        $arFilterDeal = array('ORIGIN_ID'=>$id);
                        $arSelectDeal = array('ID');
                        $obResDeal = \CCrmDeal::GetListEx(false,$arFilterDeal,false,false,$arSelectDeal)->Fetch();
                        $dealid = $obResDeal['ID'];

                        if($dealid) {
                            echo "<pre>";
                            print_r($dealid);
                            echo "</pre>";
                            switch ($status) {
                                case "Занесения данных в 1С нового клиента. Карточка клиента в Битриксе есть.":
                                    //$statusb = 116;
                                    $statusb = 'Клиент занесен в 1С';
                                    break;
                                case "Сформирован заказ с бланка заказа":
                                    //$statusb = 117;
                                    $statusb = 'Заказ сформирован в 1С';
                                    break;
                                case "Проблема":
                                    //$statusb = 118;
                                    $statusb = 'Документ с ошибкой';
                                    break;
                                case "Есть оригинал":
                                    //$statusb = 119;
                                    $statusb = 'Есть оригинал';
                                    break;
                                case "Вторая категория":
                                    //$statusb = 120;
                                    $statusb = 'Есть скан';
                                    break;
                                case "Выписан":
                                    //$statusb = 121;
                                    $statusb = 'Нет документов';
                                    break;
                            }
                            //echo $statusb;
                            //$arParams = array('UF_CRM_1612334271'=>$statusb);
                            $arParams = array($ufield => $statusb);
                            $CCrmDeal = new \CCrmDeal(false);
                            $CCrmDeal->Update($dealid, $arParams);

                            $fields = array(
                                'STATUS' => $status,
                                'DEALID' => $dealid,
                                'DATESTATUS' =>  new Type\Date()
                            );
                            $signal = new SignalsTable(false);
                            $signal->Add($fields);

                        }
                    }
                } else {
                    echo 'Произошла ошибка при чтении файла';
                }
            } else {
                echo 'Файл не найден';
            }
        }

// закрытие ssl-соединения
        ftp_close($conn_id);
        return "iTrack\Custom\Handlers\Import1cftp::makeimport();";
    }



}