var options = {}
var setuser
var settype
var setyear
var setdatebegf
var setdateendp
//var setusersf
//var managername
var settypesf = []
var explan = []
var types = []
var planvaluefield
var planmonthfield
var plantypefield
var planuserfield
var planyearfield
var factvaluefield
var factdatefield
var users = []
var datendft
var datebeg
var dateend
var yearbegf
var monthbegf
var yearendf
var monthendf
var stepiter = 0
var countusers = 1
var counttables = 1


$.datepicker.regional['ru'] = {
    closeText: 'Закрыть',
    prevText: 'Предыдущий',
    nextText: 'Следующий',
    currentText: 'Сегодня',
    monthNames: ['Январь','Февраль','Март','Апрель','Май','Июнь','Июль','Август','Сентябрь','Октябрь','Ноябрь','Декабрь'],
    monthNamesShort: ['Янв','Фев','Мар','Апр','Май','Июн','Июл','Авг','Сен','Окт','Ноя','Дек'],
    dayNames: ['воскресенье','понедельник','вторник','среда','четверг','пятница','суббота'],
    dayNamesShort: ['вск','пнд','втр','срд','чтв','птн','сбт'],
    dayNamesMin: ['Вс','Пн','Вт','Ср','Чт','Пт','Сб'],
    weekHeader: 'Не',
    dateFormat: 'dd.mm.yy',
    firstDay: 1,
    isRTL: false,
    showMonthAfterYear: false,
    yearSuffix: ''
};
$.datepicker.setDefaults($.datepicker.regional['ru']);

BX24.init(function(){
    options.groups = JSON.parse(BX24.appOption.get('planfact_uv_groups'));
    //console.log("Первый вызов BX24init");
    //console.log(options.groups);
});

jQuery(function ($) {
	var element = $('#round')    

	element.css({
		width: 100,
		height: 100,
		display: "none"
		//border: '3px dashed green'
	})

	element.canvasLoader({
    	color: '#008000'
    });

	element.canvasLoader(false);
})

$(document).ready(function() {
    //let objuser = {value: "0", text: "Нет данных"};
    //users.push(objuser)
    //console.log(users)
    var $tabs = $('.tabs .tab');
    var ACTIVE = 'active';
    var ACTIVE_DOT = '.' + ACTIVE;
    $tabs.not(ACTIVE_DOT).hide();
    // Обработка переключений закладок <ul>
    $('ul.titles').on('click', 'li:not(ACTIVE_DOT)', function () {
        // Заголовки
        $(this).addClass(ACTIVE).siblings().removeClass(ACTIVE);
        // Тексты
        $tabs.hide().eq($(this).index()).show().addClass(ACTIVE).siblings().removeClass(ACTIVE);
    });
    var datep = new Date();
    datep.setDate(datep.getDate()-7)
    // календари ввод значений
    $("#datepickerstart").datepicker()
    $("#datepickerstart").datepicker( "setDate", datep)
    $("#datepickerfinish").datepicker()
    $("#datepickerfinish").datepicker( "setDate", new Date())

    // добавляем года
    var year = new Date();
    var json = [
        {value: year.getFullYear(), text: year.getFullYear()},
        {value: year.getFullYear()+1, text: year.getFullYear()+1},
        {value: year.getFullYear()+2, text: year.getFullYear()+2}
    ];
    //console.log(json)
    //console.log(typeof json)

    var select = $("<select></select>").attr("id", "year").attr("name", "year");
    $.each(json,function(index,json){
        select.append($("<option></option>").attr("value", json.value).text(json.text));
    });
    $("#container").html(select);
    $("#year :first").attr("selected", "selected");

    // добавляем список пользователей
    BX24.callBatch({
        user_list: ['user.get',
            {
                FILTER: {
                    ACTIVE: 'Y'
                }
            }
        ]
    }, function (result) {
        //console.log(result)
        var totalusers = result.user_list.answer.total;
        var j = Math.ceil(totalusers/50)
        var e = 0;
        //console.log(j)
        for(var i=0;i<j;i++) {
            //console.log(i)
            var startcount = i*50
            //console.log(startcount)
            BX24.callBatch({
                user_list: ['user.get',
                    {
                        FILTER: {
                            ACTIVE: 'Y'
                        },
                        start: startcount
                    }
                ]
            }, function (resultplus) {
                //console.log(resultplus)
                resultplus.user_list.answer.result.forEach(function(userel) {
                    let objuser = {value: userel['ID'], text: userel['NAME'] + ' ' + userel['LAST_NAME']};
                    var length=users.push(objuser)
                    if(length==totalusers) {
                        arrayuserfill(users)
                    }

                });
                //console.log(i)
            });
        }
        //arrayuserfill(users)
    });

    //  добавляем типы событий
    types = [
        {value: "IC", text: "Входящие звонки"},
        {value: "OC", text: "Исходящие звонки"},
        {value: "LT", text: "Отправленные письма"},
        {value: "CL", text: "Заполненные чек-листы"},
        {value: "SL", text: "Сумма отгрузок(все воронки)"},
        {value: "MT", text: "Встреч проведено"},
    ];


    BX24.callBatch({
        list_dls: ['crm.dealcategory.list',
            {
            }
        ]
    }, function (resultdealscat) {
        var stagesrequest = {}
        var stagesrequestparam = {}
        stagesrequest['stages_fields_gen'] = ['crm.dealcategory.stage.list']
        stagesrequest['stages_fields_gen'].push(stagesrequestparam)

        var liststunnels = resultdealscat.list_dls.answer.result

        if(liststunnels.length>0) {
            liststunnels.forEach(function (tunnel) {
                stagesrequestparam = {}
                stagesrequest['stages_fields_'+tunnel['ID']] = ['crm.dealcategory.stage.list']
                stagesrequestparam['id'] = tunnel['ID']
                stagesrequest['stages_fields_'+tunnel['ID']].push(stagesrequestparam)
            })
        }
        BX24.callBatch(stagesrequest, function (resultdealsstages) {
            //console.log(resultdealsstages)
            BX24.callBatch(stagesrequest, function (resultdealsstages) {
                //console.log(resultdealsstages)
                for (var tunnelres in resultdealsstages) {
                    // условие для А-трейд
                    resultdealsstages[tunnelres]['answer']['result'].forEach(function(restunnel) {
                            if(restunnel['STATUS_ID']=='C1:PREPARATION' || restunnel['STATUS_ID']=='C1:1' ||
                                restunnel['STATUS_ID']=='C1:4') {
                                let objstage = {value: restunnel['STATUS_ID'], text: restunnel['NAME'] /*+ "(" + restunnel['STATUS_ID'] + ")"*/}
                                types.push(objstage)
                            }
                        }
                    )
                    //}
                }
                var select3 = $("<select class=\"js-select2\"></select>").attr("id", "type").attr("name", "type");
                $.each(types,function(index,types){
                    //console.log(json)
                    select3.append($("<option data-badge=\"\"></option>").attr("value", types.value).text(types.text));
                });
                $("#types").html(select3);
                //$("#type :first").attr("selected", "selected");

                $(".js-select2").select2({
                    closeOnSelect: false,
                    placeholder: "Показатели",
                    allowHtml: true,
                    allowClear: true
                });

                var select4 = $("<select class=\"js-select4\" multiple=\"multiple\"></select>").attr("id", "typef").attr("name", "typef").attr("multiple", "multiple");
                select4.append($("<option></option>").attr("value", 'all').text('Выбрать все'));
                $.each(types,function(index,types){
                    //console.log(json)
                    select4.append($("<option></option>").attr("value", types.value).text(types.text));
                });
                $("#typesf").html(select4);
                //$("#typef :first").attr("selected", "selected");

                $(".js-select4").select2({
                    closeOnSelect: false,
                    placeholder: "Показатели",
                    allowHtml: true,
                    allowClear: true
                });

                $('.js-select4').on("select2:select", function (e) {
                    var data = e.params.data.text;
                    if(data=='Выбрать все'){
                        $(".js-select4 > option").prop("selected","selected");
                        $(".js-select4").trigger("change");
                    }
                });
            })

        })
    })


    // стадии, если воронка одна
    /*BX24.callBatch({
        deal_cat: ['crm.dealcategory.stage.list',
            {
            }
        ]
    }, function (resultplusplus) {
        //console.log(result)
        resultplusplus.deal_cat.answer.result.forEach(function(typel) {
            let objuser = {value: typel['STATUS_ID'], text: "Переход на этап " + typel['NAME']};
            var length=types.push(objuser)

        })



        var select3 = $("<select></select>").attr("id", "type").attr("name", "type");
        $.each(types,function(index,types){
            //console.log(json)
            select3.append($("<option></option>").attr("value", types.value).text(types.text));
        });
        $("#types").html(select3);
        $("#type :first").attr("selected", "selected");

        var select4 = $("<select></select>").attr("id", "typef").attr("name", "typef").attr("multiple", "multiple");
        $.each(types,function(index,types){
            //console.log(json)
            select4.append($("<option></option>").attr("value", types.value).text(types.text));
        });
        $("#typesf").html(select4);
        $("#typef :first").attr("selected", "selected");
    });*/

    // событие кнопки сформировать план
    $("#btnSubmitplan").click(function(){
        generateplan()
    });

    // событие кнопки сформировать факт
    $("#btnSubmitfact").click(function(){
        $("#resultfacttext").empty()
        $("#resultfactdate").empty()
        $(".tabs").width('100%')
		$("#round").css({
			display: "block"
        })
		$('#round').trigger('start.canvasLoader')
        var setusersf = $("#userf").val()
        setusersf.forEach(function (setuserf) {
            if(setuserf!='all') {
                //generatefact(setuserf)
				countusers++
				generatefact(setuserf).then(function(uid) {
                    /* stuff */
                    //console.log('start')
                    //console.log(uid)

                    //drawfact(uid, setuserf)
					drawfactprom(uid, setuserf).then(function(res) {
                    /* stuff */
                    //console.log('start')
                    	//console.log(res)
                	})	
                })
            }
        })
    });

    // событие кнопки очистки факта
    $("#btnClearfact").click(function(){
        $("#resultfacttext").empty()
        $("#resultfactdate").empty()
        $(".tabs").width('100%')
    });
})


function arrayuserfill(users) {
    users.sort((prev, next) => {
        if ( prev.text < next.text ) return -1;
        if ( prev.text > next.text ) return 1;
        return 0;
    });
    var select2 = $("<select class=\"js-select3\"></select>").attr("id", "user").attr("name", "user");


    $.each(users,function(index,users){
        //console.log(users)
        select2.append($("<option></option>").attr("value", users.value).text(users.text));
    });
    $("#employees").html(select2);
    //$("#user :first").attr("selected", "selected");

    var select3 = $("<select class=\"js-select3\" multiple=\"multiple\"></select>").attr("id", "userf").attr("name", "userf").attr("multiple", "multiple");
    select3.append($("<option></option>").attr("value", 'all').text('Выбрать всех'));
    $.each(users,function(index,users){
        //console.log(users)
        select3.append($("<option></option>").attr("value", users.value).text(users.text));
    });
    $("#employeesf").html(select3);
    //$("#userf :first").attr("selected", "selected");


    $(".js-select3").select2({
        closeOnSelect: false,
        placeholder: "Сотрудники",
        allowHtml: true,
        allowClear: true
    });

    $('.js-select3').on("select2:select", function (e) {
        var data = e.params.data.text;
        if(data=='Выбрать всех'){
            $(".js-select3 > option").prop("selected","selected");
            $(".js-select3").trigger("change");
        }
    });
}

function generateplan() {
    setyear = $("#year").val();
    setuser = $("#user").val();
    settype = $("#type").val();
    $("#result1").empty();
    $("#result2").empty();
    $("#result3").empty();
    $("#submit").empty();
    BX24.callBatch({
        plan_get: ['lists.element.get',
            {
                IBLOCK_TYPE_ID: 'lists_socnet',
                IBLOCK_CODE: 'listplans'+options.groups.planfact,
                SOCNET_GROUP_ID: options.groups.planfact,
                FILTER: {
                    PROPERTY_YEAR: setyear,
                    PROPERTY_TYPE: settype,
                    PROPERTY_EMPLOYEE: setuser
                }

            }
        ],
        plan_fields: ['lists.field.get',
            {
                IBLOCK_TYPE_ID: 'lists_socnet',
                IBLOCK_CODE: 'listplans'+options.groups.planfact,
                SOCNET_GROUP_ID: options.groups.planfact
            }
        ]
    }, function (resultplusplusplus) {
        var readyplan = ["0","0","0","0","0","0","0","0","0","0","0", "0"];
        //console.log(resultplusplusplus)
        var fields = resultplusplusplus.plan_fields.answer.result
        for(var key in fields) {
            //console.log(fields[key])
            if(fields[key]['NAME']=='value') {
                planvaluefield = fields[key]['FIELD_ID']
            } else if(fields[key]['NAME']=='month') {
                planmonthfield = fields[key]['FIELD_ID']
            } else if(fields[key]['NAME']=='type') {
                plantypefield = fields[key]['FIELD_ID']
            } else if(fields[key]['NAME']=='employee') {
                planuserfield = fields[key]['FIELD_ID']
            } else if(fields[key]['NAME']=='year') {
                planyearfield = fields[key]['FIELD_ID']
            }
        }
        //console.log(planvaluefield)
        //console.log(planmonthfield)
        if(resultplusplusplus.plan_get.answer.result.length>0) {
            explan = resultplusplusplus.plan_get.answer;
            resultplusplusplus.plan_get.answer.result.forEach(function (planel) {
                //console.log(planel[planmonthfield])
                //console.log(planel[planvaluefield])
                readyplan[Object.values(planel[planmonthfield])[0]-1] = Object.values(planel[planvaluefield])[0];
            });

        }
        readyplan.forEach(function(itemplan,i) {
            var month = i + 1
            var monthname=['Январь', 'Февраль', 'Март', 'Апрель', 'Май', 'Июнь', 'Июль', 'Август', 'Сентябрь', 'Октябрь', 'Ноябрь',
                'Декабрь'
            ];
            var inputlabel = $("<p></p>").text(monthname[i])

            var inputplan = $("<input>").attr({"id": "Month"+month, "class": "inputplan", "type": "text",
                "value": itemplan, "name": month})
            if(i<4) {
                $("#result1").append(inputlabel)
                $("#result1").append(inputplan)
            } else if(i>=4 && i<8) {
                $("#result2").append(inputlabel)
                $("#result2").append(inputplan)
            } else {
                $("#result3").append(inputlabel)
                $("#result3").append(inputplan)
            }
        })

        var saveplan = $("<input class=\"ui-btn ui-btn-primary\">").attr({
            "id": "btnsaveplan",
            "value": "Сохранить план",
            "type": "submit"
        })

        // функция сохранения плана
        saveplan.click(function () {
            //console.log(explan)
            //console.log(setyear)
            //console.log(setuser)
            //console.log(settype)
            $('.inputplan').each(function() {
                var setfields = {};
                setfields['NAME'] = 'Новая запись'
                setfields[planyearfield] = setyear
                setfields[plantypefield] = settype
                setfields[planuserfield] = setuser
                setfields[planmonthfield] = $(this).attr('name')
                setfields[planvaluefield] = $(this).val()

                //console.log(setfields)
                //var valuefound = false;

                var elcode = 'val' +  setyear + + $(this).attr('name') + settype + setuser;

                BX24.callBatch({
                    plan_add: ['lists.element.add',
                        {
                            IBLOCK_TYPE_ID: 'lists_socnet',
                            IBLOCK_CODE: 'listplans'+options.groups.planfact,
                            ELEMENT_CODE: elcode,
                            FIELDS: setfields
                        }
                    ]
                }, function (result) {
                    //console.log(result)
                    if(result.plan_add.answer.hasOwnProperty('error')) {
                        if (result.plan_add.answer.error.error == "ERROR_ELEMENT_ALREADY_EXISTS") {
                            //console.log(elcode)
                            //console.log(setfields)
                            BX24.callBatch({
                                plan_add: ['lists.element.update',
                                    {
                                        IBLOCK_TYPE_ID: 'lists_socnet',
                                        IBLOCK_CODE: 'listplans' + options.groups.planfact,
                                        ELEMENT_CODE: elcode,
                                        FIELDS: setfields
                                    }
                                ]
                            }, function (result) {
                                //console.log(result)
                            });
                        }
                    }
                });
            });
            alert("План сохранен!")
        })
        $("#submit").append(saveplan)
    });
}

var generatefact = function(setuserf) {
    return new Promise(function(resolve) {
        // забираем данные с формы
        datebeg = $("#datepickerstart").datepicker( "getDate" )
        dateend = $("#datepickerfinish").datepicker( "getDate" )
        yearbegf = datebeg.getFullYear()
        monthbegf = datebeg.getMonth()+1
        yearendf = dateend.getFullYear()
        monthendf = dateend.getMonth()+1
        var iterations = {}
        setdatebegf = $("#datepickerstart").val()
        setdateendp = $("#datepickerfinish").val()
        //setuserf = $("#userf").val()
        //managername = $("#userf option:selected").text()
        settypesf = $("#typef").val()

        if(setdatebegf=="" || setdatebegf=="") {
            alert("Даты должны быть заполнены")
            return false
        }

        if(datebeg > dateend) {
            alert("Дата начала должна быть меньше даты завершения")
            return false
        } else {
            dateend.setDate(dateend.getDate()+1)
            datendft  = dateend.getDate()+'.'+(dateend.getMonth()+1)+'.'+dateend.getFullYear()
            //console.log(datendft)
            let resultarr = {};
            var request = {}
            var requestparam = {}
            var call = settypesf.indexOf('all')
            if (call !== -1) {
                // Выполнение кода, если элемент в массиве найден
                settypesf.splice(call,1)
            }


            var clind = settypesf.indexOf('CL')
            if (clind !== -1) {
                // Выполнение кода, если элемент в массиве найден
                settypesf.splice(clind,1)
                settypesf.push("CL")
            }
            var drawfcl = false

            // поля плана
            request['plan_fields'] = ['lists.field.get']
            requestparam['IBLOCK_TYPE_ID'] = 'lists_socnet'
            requestparam['IBLOCK_CODE'] = 'listplans'+options.groups.planfact
            requestparam['SOCNET_GROUP_ID'] = options.groups.planfact
            request['plan_fields'].push(requestparam)

            requestparam = {}
            // поля факта
            request['fact_fields'] = ['lists.field.get']
            requestparam['IBLOCK_TYPE_ID'] = 'lists_socnet'
            requestparam['IBLOCK_CODE'] = 'listfacts'+options.groups.planfact
            requestparam['SOCNET_GROUP_ID'] = options.groups.planfact
            request['fact_fields'].push(requestparam)

            $("#resultfacttext").text("Подождите, отчет формируется...")
            // собираем первоначальный батч
            $.each(settypesf,function(index,settypesf){
                resultarr[settypesf] = {}
                //console.log(settypesf)
                requestparam = {}
                request['planv_'+settypesf] = ['lists.element.get']
                requestparam['IBLOCK_TYPE_ID'] = 'lists_socnet'
                requestparam['IBLOCK_CODE'] = 'listplans'+options.groups.planfact
                requestparam['SOCNET_GROUP_ID'] = options.groups.planfact
                requestparam['FILTER'] = {
                    ">=PROPERTY_YEAR": yearbegf,
                    "<=PROPERTY_YEAR": yearendf,
                    ">=PROPERTY_MONTH": monthbegf,
                    "<=PROPERTY_MONTH": monthendf,
                    "PROPERTY_TYPE": settypesf,
                    "PROPERTY_EMPLOYEE": setuserf
                }
                request['planv_'+settypesf].push(requestparam)

                requestparam = {}

                if(settypesf=="IC") {
                    // логика входящих звонков - исправить потом фильтр с датой
                    request['fact_'+settypesf] = ['crm.activity.list']
                    requestparam['ORDER'] = { "ID": "DESC" }
                    requestparam['FILTER'] = { "TYPE_ID": 2,
                        ">=START_TIME": setdatebegf, "<=END_TIME": datendft,
                        'AUTHOR_ID': setuserf, "DIRECTION": 1
                    }
                    requestparam['SELECT'] = ["*"]
                    request['fact_'+settypesf].push(requestparam)
                } else if(settypesf=="OC") {
                    // логика исходящих звонков - исправить потом фильтр с датой
                    request['fact_'+settypesf] = ['crm.activity.list']
                    requestparam['ORDER'] = { "ID": "DESC" }
                    requestparam['FILTER'] = { "TYPE_ID": 2,
                        ">=START_TIME": setdatebegf, "<=END_TIME": datendft,
                        'AUTHOR_ID': setuserf, "DIRECTION": 2
                    }
                    requestparam['SELECT'] = ["*"]
                    request['fact_'+settypesf].push(requestparam)
                } else if(settypesf=="LT") {
                    // логика исходящих писем - исправить потом фильтр с датой
                    request['fact_'+settypesf] = ['crm.activity.list']
                    requestparam['ORDER'] = { "ID": "DESC" }
                    requestparam['FILTER'] = { "TYPE_ID": 4,
                        ">=START_TIME": setdatebegf, "<=END_TIME": datendft,
                        'AUTHOR_ID': setuserf, "DIRECTION": 2
                    }
                    requestparam['SELECT'] = ["*"]
                    request['fact_'+settypesf].push(requestparam)
                } else {
                    // логика анализа завершенных сделок
                    request['fact_'+settypesf] = ['lists.element.get']
                    requestparam['IBLOCK_TYPE_ID'] = 'lists_socnet'
                    requestparam['IBLOCK_CODE'] = 'listfacts'+options.groups.planfact
                    requestparam['SOCNET_GROUP_ID'] = options.groups.planfact
                    requestparam['FILTER'] = {
                        ">=DATE_CREATE": setdatebegf,
                        "<=DATE_CREATE": datendft,
                        "PROPERTY_TYPE": settypesf,
                        "PROPERTY_EMPLOYEE": setuserf
                    }
                    request['fact_'+settypesf].push(requestparam)
                }
            });

            // делаем запрос
            //console.log(request)
            BX24.callBatch(request, function (resultplus) {
                //console.log(resultplus)
                var fields = resultplus.fact_fields.answer.result
                for(var key in fields) {
                    //console.log(fields[key])
                    if(fields[key]['NAME']=='value') {
                        factvaluefield = fields[key]['FIELD_ID']
                    } else if (fields[key]['NAME']=='date') {
                        factdatefield = fields[key]['FIELD_ID']
                    }
                }

                fields = resultplus.plan_fields.answer.result
                for(var key in fields) {
                    //console.log(fields[key])
                    if(fields[key]['NAME']=='value') {
                        planvaluefield = fields[key]['FIELD_ID']
                    }
                }
                //console.log(resultplus)
                $.each(settypesf,function(index,settypesf){
                    // забираем план по первым 50-ти записям
                    //console.log(settypesf)
                    //console.log(resultplus["planv_"+settypesf]['answer']['result'])


                    var planarr = resultplus["planv_"+settypesf]['answer']['result']
                    resultarr[settypesf]['plan'] = 0
                    planarr.forEach(function (plan) {
                        //console.log(plan)
                        resultarr[settypesf]['plan'] = Number(resultarr[settypesf]['plan']) + Number(Object.values(plan[planvaluefield])[0])
                    })
                    //resultplus["planv_"+settypesf]['answer']['result'].forEach(function (planel) {
                    //    resultarr[settypesf]['plan'] += Object.values(planel[planvaluefield])[0]
                    //})
                    //var factarr = resultplus['fact_'+settypesf]['answer']['result']
                    var totalusers = resultplus['fact_'+settypesf]['answer']['total'];
                    if(totalusers >= 50) {
                        iterations[settypesf] = Math.floor(totalusers/50)
                    }

                    //if(settypesf != "CL") {
                    var factarr = resultplus['fact_'+settypesf]['answer']['result']
                    //} else {
                    //    var factarr = resultplus['fact_'+settypesf]['answer']['result']['tasks']
                    //}

                    if(factarr.length>0) {
                        if (settypesf == "IC" || settypesf == "OC" || settypesf == "LT") {
                            factarr.forEach(function (fact) {
                                var datefact = fact['START_TIME'].slice(8,10) + '.' + fact['START_TIME'].slice(5,7) +
                                    '.' + fact['START_TIME'].slice(0,4)
                               if(resultarr[settypesf][datefact]==undefined) {
                                    resultarr[settypesf][datefact] = 0
                               }
                               resultarr[settypesf][datefact] = Number(resultarr[settypesf][datefact]) + 1
                            })
                        } else {
                            factarr.forEach(function (fact) {
                                var listdate = Object.values(fact[factdatefield])[0]

                                if(resultarr[settypesf][listdate]==undefined) {
                                    resultarr[settypesf][listdate] = 0
                                }
                                resultarr[settypesf][listdate] = Number(resultarr[settypesf][listdate])
                                    + Number(Object.values(fact[factvaluefield])[0])
                            })
                        }
                    }
                })
                // стартуем функцию если нет чек-листа
				//console.log(setuserf)
				//console.log(resultarr)
				if(Object.keys(iterations).length>0) {
                    //resultarr = additionalfactfifty(resultarr, iterations, setuserf)
					//resultarr[settypesf] = {}
					var stepiter = 0
					let resultaddarr = {}
                    var arr = Object.keys( iterations ).map(function ( key ) { return iterations[key]; });
                    var maxiterations = Math.max.apply( null, arr )

                    do {
						stepiter++
                        console.log(iterations)
						var request = {}

						for(var iterkey in iterations) {
                            //console.log(iterkey)
							resultaddarr[iterkey] = {};	

                            if(iterkey=="IC" && stepiter<=iterations[iterkey]) {
                                // логика входящих звонков - исправить потом фильтр с датой
								var requestparam = {}
                                request['fact_' + iterkey] = ['crm.activity.list']
                                requestparam['ORDER'] = {"ID": "DESC"}
                                requestparam['FILTER'] = {
                                    "TYPE_ID": 2,
                                    ">=START_TIME": setdatebegf, "<=END_TIME": datendft,
                                    'AUTHOR_ID': setuserf, "DIRECTION": 1
                                }
                                requestparam['SELECT'] = ["*"]
                                requestparam['SELECT'] = ["*"]
                                requestparam['start'] = 50*stepiter
                                request['fact_' + iterkey].push(requestparam)
                            } else if(iterkey=="OC" && stepiter<=iterations[iterkey]) {
                                // логика исходящих звонков - исправить потом фильтр с датой
                                var requestparam = {}
                                request['fact_' + iterkey] = ['crm.activity.list']
                                requestparam['ORDER'] = {"ID": "DESC"}
                                requestparam['FILTER'] = {
                                    "TYPE_ID": 2,
                                    ">=START_TIME": setdatebegf, "<=END_TIME": datendft,
                                    'AUTHOR_ID': setuserf, "DIRECTION": 2
                                }
                                requestparam['SELECT'] = ["*"]
                                requestparam['start'] = 50*stepiter
                                request['fact_' + iterkey].push(requestparam)
                            } else if(iterkey=="LT" && stepiter<=iterations[iterkey]) {
                                // логика отправленных писем
                                var requestparam = {}
                                request['fact_' + iterkey] = ['crm.activity.list']
                                requestparam['ORDER'] = {"ID": "DESC"}
                                requestparam['FILTER'] = {
                                    "TYPE_ID": 4,
                                    ">=START_TIME": setdatebegf, "<=END_TIME": datendft,
                                    'AUTHOR_ID': setuserf, "DIRECTION": 2
                                }
                                requestparam['SELECT'] = ["*"]
                                requestparam['start'] = 50*stepiter
                                request['fact_' + iterkey].push(requestparam)
                            } else {
                                if(stepiter<=iterations[iterkey]) {
                                    // логика анализа завершенных сделок
									var requestparam = {}
                                    request['fact_' + iterkey] = ['lists.element.get']
                                    requestparam['IBLOCK_TYPE_ID'] = 'lists_socnet'
                                    requestparam['IBLOCK_CODE'] = 'listfacts' + options.groups.planfact
                                    requestparam['SOCNET_GROUP_ID'] = options.groups.planfact
                                    requestparam['FILTER'] = {
                                        ">=DATE_CREATE": setdatebegf,
                                        "<=DATE_CREATE": datendft,
                                        "PROPERTY_TYPE": iterkey,
                                        "PROPERTY_EMPLOYEE": setuserf
                                    }
                                    requestparam['start'] = 50 * stepiter
                                    request['fact_' + iterkey].push(requestparam)
                                }
                            }
                        }
                        //console.log(request)
                        BX24.callBatch(request, function (resultiterations) {
                            //console.log(resultiterations)
                            for(var iterkey in iterations) {
                                //console.log(iterkey)
                                if(iterkey=="IC" || iterkey=="OC" || iterkey=="LT") {
                                    // логика входящих звонков - исправить потом фильтр с датой
                                    if(resultiterations['fact_' + iterkey]!=undefined) {
                                        var factarr = resultiterations['fact_' + iterkey]['answer']['result']
                                        if (factarr.length > 0) {
                                            factarr.forEach(function (fact) {
                                                var datefact = fact['START_TIME'].slice(8, 10) + '.' + fact['START_TIME'].slice(5, 7) +
                                                    '.' + fact['START_TIME'].slice(0, 4)
                                                if (resultarr[iterkey][datefact] == undefined) {
                                                    resultarr[iterkey][datefact] = 0
                                                }
                                                resultarr[iterkey][datefact] = Number(resultarr[iterkey][datefact]) + 1
                                            })
                                        }
                                    }
                                } else {
                                    // логика анализа сделок
                                    if (resultiterations['fact_' + iterkey] != undefined) {
                                        var factarr = resultiterations['fact_' + iterkey]['answer']['result']
                                        factarr.forEach(function (fact) {
                                            var listdate = Object.values(fact[factdatefield])[0]
                                            if (resultarr[iterkey][listdate] == undefined) {
                                                resultarr[iterkey][listdate] = 0
                                            }
                                            resultarr[iterkey][listdate] = Number(resultarr[iterkey][listdate])
                                                + Number(Object.values(fact[factvaluefield])[0])
                                        })
                                    }
                                }
                            }
                        })

					} while(stepiter<maxiterations)
					//console.log(stepiter)
					//console.log(maxiterations)
					var timelag = maxiterations*2000
					if(stepiter==maxiterations) {
						//console.log(resultaddarr)	
						//resultaddarr = resultarr
						setTimeout(() => {
							// переведёт промис в состояние fulfilled с результатом "result"
							resolve(resultarr);
						}, timelag);

						//resolve(resultarr)
					}

                } else {
                    resolve(resultarr)
                    //drawfact(resultarr, setuserf)
                }
            })
        }
    });
}

var drawfactprom = function(resultarr, setuserf) {
    return new Promise(function(resolve) {
        datebeg = $("#datepickerstart").datepicker( "getDate" )
        dateend = $("#datepickerfinish").datepicker( "getDate" )
        var typename
        var managername
        settypesf = $("#typef").val()
        var call = settypesf.indexOf('all')
        if (call !== -1) {
            // Выполнение кода, если элемент в массиве найден
            settypesf.splice(call,1)
        }
        //console.log(resultarr)
		counttables++
		if(counttables==countusers) {
			$("#resultfacttext").text("Отчет сформирован!")
			$('#round').trigger('stop.canvasLoader')
			$("#round").css({
				display: "none"
        	})
		}	

        $.each(users,function(index,users) {
            if (users.value == setuserf) {
                managername = users.text
            }
        })
        //$("#resultfactdate").empty()
        var manager = $("<p></p>").text("Менеджер "+ managername)
        $("#resultfactdate").append(manager)
        var table = $("<table></table>").attr("id", "tablefact").attr("name", "tablefact").attr("border", 1).attr("cellspacing",0)
        var tr = $("<tr></tr>")
        tr.append($("<th></th>").text("Показатель"))

        while(datebeg<=dateend) {
            var sday = datebeg.getDate()
            sday = (sday >= 10) ? sday : "0" + sday
            var smonth = datebeg.getMonth() + 1
            smonth = (smonth > 10) ? smonth : "0" + smonth
            tr.append($("<th></th>").text(sday + '.' + smonth)).width(50)
            datebeg.setDate(datebeg.getDate()+1)
        }
        tr.append($("<th></th>").text("Факт").width(70))
        tr.append($("<th></th>").text("План").width(70))
        tr.append($("<th></th>").text("Выполнено %").width(60))
        tr.append($("<th></th>").text("Осталось %").width(60))
        table.append(tr)
        //$.each(json,function(index,json){
        //    select.append($("<option></option>").attr("value", json.value).text(json.text));
        //});

        $.each(settypesf,function(index,settypesf){
            tr = $("<tr></tr>")
            var totaltype = 0

            $.each(types,function(index,types) {
                if (types.value == settypesf) {
                    typename = types.text
                }
            })
            tr.append($("<td></td>").text(typename))
            datebeg = $("#datepickerstart").datepicker( "getDate" )
            while(datebeg<=dateend) {
                var sday = datebeg.getDate()
                sday = (sday >= 10) ? sday : "0" + sday
                var smonth = datebeg.getMonth() + 1
                smonth = (smonth > 10) ? smonth : "0" + smonth
                var syear = datebeg.getFullYear()
                var sdate = sday + '.' + smonth + '.' + syear
                if(!resultarr[settypesf][sdate]) {
                    resultarr[settypesf][sdate] = 0
                }
				console.log(resultarr)
				console.log(settypesf)
				console.log(sdate)
				console.log(resultarr[settypesf][sdate])

				totaltype = Number(totaltype) + Number(resultarr[settypesf][sdate])
                //tr.append($("<td></td>").text(resultarr[settypesf][sdate]))
                tr.append($("<td></td>").text(Math.floor(resultarr[settypesf][sdate]*100)/100))

                datebeg.setDate(datebeg.getDate()+1)
            }
            //tr.append($("<td></td>").text(totaltype))
            tr.append($("<td></td>").text(Math.ceil(totaltype*100)/100))
            var plantype = Number(resultarr[settypesf]['plan'])
            tr.append($("<td></td>").text(plantype))
            if(totaltype>0 && plantype>0) {
                var experc = totaltype / plantype
                experc = Math.floor(experc * 100)
                if(experc>=100) {
                    tr.append($("<td></td>").text(experc).attr('bgcolor', 'mediumseagreen'))
                } else if (experc>=85 && experc<100) {
                    tr.append($("<td></td>").text(experc).attr('bgcolor', 'yellow'))
                } else {
                    tr.append($("<td></td>").text(experc).attr('bgcolor', 'red'))
                }
                tr.append($("<td></td>").text(100-experc))
            } else if (totaltype==0 && plantype>0) {
                tr.append($("<td></td>").text('0').attr('bgcolor', 'red'))
                tr.append($("<td></td>").text('100'))
            } else {
                tr.append($("<td></td>").text('0').attr('bgcolor', 'red'))
                tr.append($("<td></td>").text('0'))
            }
            table.append(tr)

        })
        $("#resultfactdate").append(table);
        var currentwidth = $(".tabs").width()
        var tablewidth = $("#tablefact").width()+30
        if(currentwidth < tablewidth) {
            $(".tabs").width(tablewidth)
        }
        $("#tablefact").wrap(function () {
            //return "<div class='new'></div>";
        })
        resolve('ok');
    });
}

