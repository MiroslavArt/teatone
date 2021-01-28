var options = {};
var setuser;
var settype;
var setyear;
var explan = [];
var planvaluefield;
var planmonthfield;
var plantypefield;
var planuserfield;
var planyearfield;

BX24.init(function(){
    options.groups = JSON.parse(BX24.appOption.get('planfact_uv_groups'));
    //console.log("Первый вызов BX24init");
    //console.log(options.groups);
});

$(document).ready(function() {
    var users = [];
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
    var types = [
        {value: "IC", text: "Входящие звонки"},
        {value: "OC", text: "Исходящие звонки"},
        {value: "CL", text: "Заполненные чек-листы"},
        {value: "SL", text: "Сумма отгрузок"}
    ];

    BX24.callBatch({
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
    });

    //select.change(function () {
    //    alert($(this).val())
    //})

    $("#btnSubmitplan").click(function(){
        generateplan()
        //alert("button");
        //var data1 = '<p>Yes</p>'
        //$("#result").html(data1);
    });


    $("#btnSubmitfact").click(function(){
        alert("button");
        var data2 = '<p>No</p>'
        $("#result1").html(data2);
    });
})


function arrayuserfill(users) {
    //console.log(users);
    //console.log(users.length);
    //json = users
    //onsole.log(json)
    //console.log(typeof json)
    //console.log(Array.isArray(users))

    //users.forEach(function (entry) {
    //    console.log(entry)
    //})
    users.sort((prev, next) => {
        if ( prev.text < next.text ) return -1;
        if ( prev.text > next.text ) return 1;
        return 0;
    });
    var select2 = $("<select></select>").attr("id", "user").attr("name", "user");
    $.each(users,function(index,users){
        //console.log(users)
        select2.append($("<option></option>").attr("value", users.value).text(users.text));
    });
    $("#employees").html(select2);
    $("#user :first").attr("selected", "selected");
}

function generateplan() {
    setyear = $("#year").val();
    setuser = $("#user").val();
    settype = $("#type").val();
    //console.log(setyear)
    //console.log(setuser)
    //console.log(settype)


    //var textinsert = $("<p></p>").text("План за" + $("#year").val() + "по пользователю" + $("#user").val() + "с типом" + $("#type").val())
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

        var saveplan = $("<input>").attr({"id": "btnsaveplan", "value": "Сохранить план", "type": "submit"})

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
            alert("План сохранен")
        })

        $("#submit").append(saveplan)
    });
}

