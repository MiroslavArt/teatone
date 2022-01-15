<?php
require_once ($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_before.php');
\Bitrix\Main\UI\Extension::load("ui.forms");
\Bitrix\Main\UI\Extension::load("ui.buttons");
//\Bitrix\Main\UI\Extension::load("ui.icons");
\Bitrix\Main\UI\Extension::load("ui.notification");
\Bitrix\Main\UI\Extension::load("ui.hint");
\Bitrix\Main\UI\Extension::load("ui.alerts");
?>
<html lang="ru">
<head>
    <title>Учет финансов</title>
    <meta charset="utf-8">
    <link rel="stylesheet" href="css/style.css?<?=time()?>" type='text/css' />
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet"/>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="css/index.css?<?=time()?>">
    <script src="js/heartcode-canvasloader.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="//api.bitrix24.com/api/v1/dev/"></script>
    <script src="js/index.js"></script>
    <?
    $APPLICATION->ShowHead(false);
    ?>
    <script src="js/newscript.js?<?=time()?>"></script>
</head>
<body>
<div class="db">
    <ul class="titles">
        <li class="title active">Просмотр и ввод плановых значений</li>
        <li class="title">Построение отчета</li>
    </ul>
    <div class="tabs">
        <div id="planinput" class="tab active">
            <p>Выберите сотрудника, год и введите плановые значения в разрезе недель</p>
            <p class="label_title">Год</p>
            <div id="container"></div>
            <p class="label_title">Сотрудник</p>
            <div id="employees"></div>
            <p class="label_title">Вид показателя</p>
            <div id="types"></div>
            <input id="btnSubmitplan" class="ui-btn ui-btn-primary" type="submit" value="Вывести план"/>
            <div id="results" class="blocks">
                <div id="result1"></div>
                <div id="result2"></div>
                <div id="result3"></div>
            </div>
            <div id="submit"></div>
        </div>
        <div class="tab">
            <!-- <b>.toggle()</b> -->
            <div id="content">
                <p>Выберите даты начала, окончания, сотрудника и типы показателей</p>
                <div class="block__date">
                    <div>
                        <p>Дата начала</p>
                        <input type="text" id="datepickerstart">
                    </div>
                    <div>
                        <p>Дата окончания</p>
                        <input type="text" id="datepickerfinish">
                    </div>
                </div>
                <p>Сотрудник</p><div id="employeesf"></div>
                <p>Вид показателя</p><div id="typesf"></div>
                <input id="btnSubmitfact" class="ui-btn ui-btn-primary" type="submit" value="Сформировать отчет"/>
                <input id="btnClearfact" class="ui-btn ui-btn-primary" type="submit" value="Очистить данные отчетов"/>
                <!-- <button id="button-a">Выгрузить в Excel</button> -->
                <div id="resultfact">
                    <div id="resultfacttext"></div>
                    <div id='round'></div>
                    <div id="resultfactdate"></div>
                </div>
            </div>
        </div>
    </div>
</div>
</body>


