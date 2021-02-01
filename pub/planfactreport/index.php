<html lang="ru">
<head>
    <title>Учет финансов</title>
    <meta charset="utf-8">
    <link rel="stylesheet" href="css/style.css?<?=time()?>" type='text/css' />
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script src="//api.bitrix24.com/api/v1/dev/"></script>
    <script src="js/script.js?<?=time()?>"></script>
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
            <p>Год</p></p><div id="container"></div>
            <p>Сотрудник</p><div id="employees"></div>
            <p>Вид показателя</p><div id="types"></div>
            <input id = "btnSubmitplan" type="submit" value="Вывести план"/>
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
                <p>Дата начала</p><input type="text" id="datepickerstart">
                <p>Дата окончания</p><input type="text" id="datepickerfinish">
                <p>Сотрудник</p><div id="employeesf"></div>
                <p>Вид показателя</p><div id="typesf"></div>
                <input id = "btnSubmitfact" type="submit" value="Сформировать отчет"/>
                <input id = "btnClearfact" type="submit" value="Очистить данные отчетов"/>
                <!-- <button id="button-a">Выгрузить в Excel</button> -->
                <div id="resultfact"><div id="resultfacttext"></div><div id="resultfactdate"></div></div>
            </div>
        </div>
    </div>
</div>
</body>


