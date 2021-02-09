<?php

use Bitrix\Main\Config\Option;
use Bitrix\Main\Context;
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Diag\Debug;
use iTrack\Custom\Entity\SignalsTable;
use Bitrix\Main\Type;

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

class ItrImport1c extends CBitrixComponent
{
    protected $fileName = '';
    protected $filePath = '';
    protected $dirName = '';
    protected $scenario = '';
    public $debugMode = true;
    public $message = '';
    public $errorMessage = '';
    public $successMessage = '';
    public $progressMessage = '';

    public function onPrepareComponentParams($arParams)
    {
        $arParams['GROUP_PERMISSIONS'] = !empty($arParams['GROUP_PERMISSIONS']) ? $arParams['GROUP_PERMISSIONS'] : [];
        $arParams["USE_TEMP_DIR"] = $arParams["USE_TEMP_DIR"] !== 'Y' ? false : true;
        $arParams['USE_SESSION_ID_TTL'] = Option::get('main', 'use_session_id_ttl', 'N') == 'Y';
        $arParams['SESSION_ID_TTL'] = Option::get('main', 'session_id_ttl', 0) > 0;
        $arParams['USE_ZIP'] = $arParams['USE_ZIP'] !== 'Y' ? false : true;
        $arParams['FILE_SIZE_LIMIT'] = intval(Option::get("catalog", "1C_FILE_SIZE_LIMIT", 200 * 1024));
        $arParams['READER'] = strlen($arParams['READER'])  > 0 ? $arParams['READER'] : false;
        $arParams['DEBUG'] = $arParams['DEBUG'] === 'Y';
        $arParams['PATH_TO_LOG'] = !empty($arParams['PATH_TO_LOG']) ? $arParams['PATH_TO_LOG'] : '/local/exchange';
        $arParams['LOG_FILE'] = $_SERVER['DOCUMENT_ROOT'] . $arParams['PATH_TO_LOG'] . '/import.log';
        return $arParams;
    }

    /**
     * Проверяем, может ли авторизованный пользователь совершать импорт
     * @return bool
     */
    protected function userHaveAccess()
    {
        global $USER;
        $bHaveAccess = false;
        if (is_object($USER)) {
            $bHaveAccess = $USER->IsAdmin();
            if (!$bHaveAccess) {
                $arUserGroups = $USER->GetUserGroupArray();
                foreach ($this->arParams['GROUP_PERMISSIONS'] as $groupPermission) {
                    if (in_array($groupPermission, $arUserGroups)) {
                        $bHaveAccess = true;
                        break;
                    }
                }
            }
        }
        return $bHaveAccess;
    }

    /**
     * Определим текущий этап импорта
     */
    protected function checkScenario()
    {
        $this->scenario = $this->request->get('mode');
    }

    /**
     * Определяем рабочую директорию
     * @throws \Bitrix\Main\ArgumentNullException
     */
    protected function checkDirName()
    {
        if ($this->arParams['USE_TEMP_DIR'] && strlen($_SESSION["BX_CML2_IMPORT"]["TEMP_DIR"]) > 0) {
            $this->dirName = $_SESSION["BX_CML2_IMPORT"]["TEMP_DIR"];
        } else {
            $this->dirName = Context::getCurrent()->getServer()->getDocumentRoot() . DIRECTORY_SEPARATOR . Option::get("main", "upload_dir", "upload") . "/1c_catalog";
        }
    }

    /**
     * Определим имя рабочего фала и путь к нему
     * @throws \Bitrix\Main\ArgumentNullException
     */
    protected function checkFileName()
    {
        $this->checkDirName();
        $reqFileName = $this->request->get('filename');
        if (strlen($reqFileName) > 0 && strlen($this->dirName) > 0) {
            // Подготовим название файла
            $fileName = preg_replace("#^(/tmp/|upload/1c/webdata)#", "", $reqFileName);
            $fileName = trim(str_replace("\\", "/", trim($fileName)), "/");

            $io = CBXVirtualIo::GetInstance();
            // Проверим, что файл безопасен
            $bBadFile = HasScriptExtension($fileName) || IsFileUnsafe($fileName) || !$io->ValidatePathString("/" . $fileName);

            if (!$bBadFile) {
                $this->fileName = rel2abs($this->dirName, DIRECTORY_SEPARATOR . $fileName);
                if (strlen($this->fileName) > 1 && ($this->fileName === "/" . $fileName)) {
                    $this->filePath = $this->dirName . DIRECTORY_SEPARATOR . $fileName;

                }
            }
        }
    }

    /**
     * Начало обработки шагов иморта
     */
    protected function startImport()
    {
        ob_start();
    }

    /**
     * Завершение импорта вывод сообщения о результате
     */
    protected function endImport()
    {
        global $APPLICATION;

        if (!empty($this->errorMessage)) {
            echo "failure\n";
            echo $this->errorMessage;
        } elseif (!empty($this->successMessage)) {
            $_SESSION["BX_CML2_IMPORT"] = array(
                "zip" => $_SESSION["BX_CML2_IMPORT"]["zip"], //save from prev load
                "TEMP_DIR" => $_SESSION["BX_CML2_IMPORT"]["TEMP_DIR"], //save from prev load
                "NS" => array(
                    "STEP" => 0,
                ),
            );
            echo "success\n";
            echo $this->successMessage;
            if ($this->debugMode) {
                file_put_contents($this->arParams['LOG_FILE'], date('d.m.Y, H:i:s') . " Окончание импорта\r\n*******************************\r\n\r\n", FILE_APPEND);
            }
        } elseif (!empty($this->progressMessage)) {
            echo "progress\n";
            echo $this->progressMessage;
        } else {
            echo $this->message;
        }

        $contents = ob_get_contents();

        if (!empty($this->dirName)) {
            $ht_name = $this->dirName.".htaccess";
            CheckDirPath($ht_name);
            file_put_contents($ht_name, "Deny from All");
            @chmod($ht_name, BX_FILE_PERMISSIONS);
        }

        $APPLICATION->RestartBuffer();
        if (toUpper(LANG_CHARSET) != "WINDOWS-1251")
            $contents = $APPLICATION->ConvertCharset($contents, LANG_CHARSET, "windows-1251");
        header("Content-Type: text/html; charset=windows-1251");

        echo $contents;
        die();
    }

    /**
     * @param int $secondsDrift
     * @return bool
     */
    protected function checkDatabaseServerTime($secondsDrift = 600)
    {
        global $DB;

        CTimeZone::Disable();
        $sql = "select " . $DB->DateFormatToDB("YYYY-MM-DD HH:MI:SS", $DB->GetNowFunction()) . " DB_TIME from b_user";
        $query = $DB->Query($DB->TopSql($sql, 1));
        $record = $query->Fetch();
        CTimeZone::Enable();

        $dbTime = $record ? MakeTimeStamp($record["DB_TIME"], "YYYY-MM-DD HH:MI:SS") : 0;
        $webTime = time();

        if ($dbTime) {
            if ($dbTime > ($webTime + $secondsDrift))
                return false;
            elseif ($dbTime < ($webTime - $secondsDrift))
                return false;
            else
                return true;
        }

        return true;
    }

    protected function cleanUpDirectory($directoryName)
    {
        //Cleanup previous import files
        $directory = new \Bitrix\Main\IO\Directory($directoryName);
        if ($directory->isExists()) {
            if (defined("BX_CATALOG_IMPORT_1C_PRESERVE")) {
                $i = 0;
                while (\Bitrix\Main\IO\Directory::isDirectoryExists($directory->getPath() . $i)) {
                    $i++;
                }
                $directory->rename($directory->getPath() . $i);
            } else {
                foreach ($directory->getChildren() as $directoryEntry) {
                    $match = array();
                    if ($directoryEntry->isDirectory() && $directoryEntry->getName() === "Reports") {
                        $emptyDirectory = true;
                        $reportsDirectory = new \Bitrix\Main\IO\Directory($directoryEntry->getPath());
                        foreach ($reportsDirectory->getChildren() as $reportsEntry) {
                            $match = array();
                            if (preg_match("/(\\d\\d\\d\\d-\\d\\d-\\d\\d)\\./", $reportsEntry->getName(), $match)) {
                                if (
                                    $match[1] >= date("Y-m-d", time() - 5 * 24 * 3600) //no more than 5 days old
                                    && $match[1] < date("Y-m-d") //not today or future
                                ) {
                                    //Preserve the file
                                    $emptyDirectory = false;
                                } else {
                                    $reportsEntry->delete();
                                }
                            } else {
                                $reportsEntry->delete();
                            }
                        }

                        if ($emptyDirectory) {
                            $directoryEntry->delete();
                        }
                    } else {
                        $directoryEntry->delete();
                    }
                }
            }
        }
    }

    public function executeComponent()
    {
        global $USER;

        //Loader::includeModule('itrack.base');

        $this->checkFileName();
        $this->checkScenario();
        $this->startImport();

        switch ($this->scenario) {
            case 'checkauth':
                if ($USER->IsAuthorized()) {
                    if (!$this->userHaveAccess()) {
                        $this->errorMessage = Loc::getMessage('CC_BSC1_PERMISSION_DENIED');
                    } elseif ($this->arParams['USE_SESSION_ID_TTL'] && $this->arParams['SESSION_ID_TTL'] && !defined("BX_SESSION_ID_CHANGE")) {
                        $this->errorMessage = Loc::getMessage('CC_BSC1_ERROR_SESSION_ID_CHANGE');
                    } elseif (!$this->checkDatabaseServerTime(600)) {
                        $this->errorMessage = Loc::getMessage('CC_BSC1_ERROR_DATABASE_SERVER_TIME');
                    } else {
                        $this->successMessage = session_name() . "\n";
                        $this->successMessage .= session_id() . "\n";
                        $this->successMessage .= bitrix_sessid_get() . "\n";
                        $this->successMessage .= "timestamp=" . time() . "\n";
                        if ($this->debugMode) {
                            file_put_contents($this->arParams['LOG_FILE'], date('d.m.Y, H:i:s') . " Авторизация\r\n", FILE_APPEND);
                        }
                    }
                } else {
                    $this->errorMessage = 'Ошибка авторизации. Неверное имя пользователя и пароль';
                }
                break;
            case 'init':
                if ($this->arParams['USE_TEMP_DIR']) {
                    $this->dirName = CTempFile::GetDirectoryName(6, "1c_catalog");
                } else {
                    $this->cleanUpDirectory($this->dirName);
                }
                CheckDirPath($this->dirName);
                if (!is_dir($this->dirName)) {
                    $this->errorMessage = Loc::getMessage('CC_BSC1_ERROR_INIT');
                } else {
                    $_SESSION["BX_CML2_IMPORT"] = array(
                        "zip" => $this->arParams["USE_ZIP"] && function_exists("zip_open"),
                        "TEMP_DIR" => ($this->arParams["USE_TEMP_DIR"] === "Y" ? $this->dirName : ""),
                        "NS" => array(
                            "STEP" => 0,
                        )
                    );
                    $this->message = "zip=" . ($_SESSION["BX_CML2_IMPORT"]["zip"] ? "yes" : "no") . "\n";
                    $this->message .= "file_limit=" . $this->arParams["FILE_SIZE_LIMIT"];
                    if ($this->debugMode) {
                        file_put_contents($this->arParams['LOG_FILE'], date('d.m.Y, H:i:s') . ' Инициализация импорта ' . "\r\n", FILE_APPEND);
                    }
                }
                break;
            case 'file':
                if ($this->filePath) {
                    $data = file_get_contents("php://input");
                    $dataLen = defined("BX_UTF") ? mb_strlen($data, 'latin1') : strlen($data);
                    if (isset($data) && $data !== false) {
                        CheckDirPath($this->filePath);
                        if ($fp = fopen($this->filePath, "ab")) {
                            $result = fwrite($fp, $data);
                            if ($result == $dataLen) {
                                $this->successMessage = 'Файл успешно прочитан';
                                if ($this->debugMode) {
                                    file_put_contents($this->arParams['LOG_FILE'], date('d.m.Y, H:i:s') . " Файл успешно загружен\r\n", FILE_APPEND);
                                }
                            } else {
                                $this->errorMessage = Loc::getMessage("CC_BSC1_ERROR_FILE_WRITE", ["#FILE_NAME#" => $this->fileName]);
                            }
                        } else {
                            $this->errorMessage = Loc::getMessage("CC_BSC1_ERROR_FILE_OPEN", ["#FILE_NAME#" => $this->fileName]);
                        }
                    } else {
                        $this->errorMessage = Loc::getMessage("CC_BSC1_ERROR_HTTP_READ");
                    }
                } else {
                    $this->errorMessage = 'Ошибка создания файла иморта';
                }
                break;
            case 'import':
                echo $this->filePath;
                $data = simplexml_load_file($this->filePath);
                foreach ($data as $doc) {
                    $id = (string)$doc->Ид;
                    echo "<pre>";
                    print_r($id);
                    echo "</pre>";
                    $status = (string)$doc->СтатусДокумента;
                    $statusb = "";
                    Loader::includeModule('crm');
                    $arFilterDeal = array('ORIGIN_ID'=>$id);
                    $arSelectDeal = array('ID');
                    $obResDeal = CCrmDeal::GetListEx(false,$arFilterDeal,false,false,$arSelectDeal)->Fetch();
                    $dealid = $obResDeal['ID'];

                    if($dealid) {
                        echo "<pre>";
                        print_r($dealid);
                        echo "</pre>";
                        switch ($status) {
                            case "Занесения данных в 1С нового клиента. Карточка клиента в Битриксе есть.":
                                $statusb = 116;
                                break;
                            case "Сформирован заказ с бланка заказа":
                                $statusb = 117;
                                break;
                            case "Проблема":
                                $statusb = 118;
                                break;
                            case "Есть оригинал":
                                $statusb = 119;
                                break;
                            case "Вторая категория":
                                $statusb = 120;
                                break;
                            case "Выписан":
                                $statusb = 121;
                                break;
                        }
                        echo $statusb;
                        $arParams = array('UF_CRM_1612334271'=>$statusb);
                        $CCrmDeal = new CCrmDeal(false);
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
                /*$reader = false;
                if (class_exists($this->arParams['READER'])) {
                    $reader = new $this->arParams['READER']($this->filePath);
                } else {
                    $this->errorMessage = 'Обработчик файла не найден';
                }
                if ($reader) {
                    $reader->runSteps($this);
                }*/
                echo "import";
                \Bitrix\Main\Diag\Debug::writeToFile("importfound", "reqread", "__miros.log");
                break;
            case 'reset':
                $_SESSION["BX_CML2_IMPORT"] = array(
                    "zip" => $_SESSION["BX_CML2_IMPORT"]["zip"], //save from prev load
                    "TEMP_DIR" => $_SESSION["BX_CML2_IMPORT"]["TEMP_DIR"], //save from prev load
                    "NS" => array(
                        "STEP" => 0,
                    ),
                );
                $this->successMessage = 'Данные импорта сброшены';
                break;
        }

        $this->endImport();
    }
}
