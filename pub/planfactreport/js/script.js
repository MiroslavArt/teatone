var options = {};


BX24.init(function(){
    options.groups = JSON.parse(BX24.appOption.get('planfact_uv_groups'));
    //console.log("Первый вызов BX24init");
    //console.log(options.groups);
});

$(document).ready(function() {
    var users = [];
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

    // Получение списка пользователей
    BX24.callBatch({
        user_list: ['user.get',
            {
                //start: 50
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
                   e++;
                   users[e] = {
                       'value': userel['ID'],
                       'text': userel['NAME'] + ' ' + userel['LAST_NAME']
                   }
                   //let objuser = {value: userel['ID'], text: userel['LAST_NAME']};
                   //objuser.value = userel['ID'];
                   //objuser.text = userel['NAME'] + ' ' + userel['LAST_NAME'];
                    //console.log(objuser)
                   //users.push(objuser)

                });
            });

        }
    });

    console.log(users);


    var json = [
        {value: "pune", text: "Pune"},
        {value: "mumbai", text: "Mumbai"},
        {value: "nashik", text: "Nashik"}
    ];
    console.log(json)
    console.log(typeof json)

    var select = $("<select></select>").attr("id", "cities").attr("name", "cities");
    $.each(json,function(index,json){
        console.log(json)
        select.append($("<option></option>").attr("value", json.value).text(json.text));
    });
    $("#container").html(select);

    json = users

    console.log(json)
    console.log(typeof json)

    users.forEach(function (entry) {
        console.log(entry)
    })



    var select2 = $("<select></select>").attr("id", "cities").attr("name", "cities");

    $.each(json,function(index,json){
        console.log(json)
        select2.append($("<option></option>").attr("value", json.value).text(json.text));
    });




    $("#employees").html(select2);

    select.change(function () {
        alert($(this).val())
    })

    $("#btnSubmitplan").click(function(){
        alert("button");
        var data1 = '<p>Yes</p>'
        $("#result").html(data1);
    });

    $("#btnSubmitfact").click(function(){
        alert("button");
        var data2 = '<p>No</p>'
        $("#result1").html(data2);
    });
})




