<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Document</title>
    <script src="//api.bitrix24.com/api/v1/dev/"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

</head>
<body>


<script type="text/javascript">


    function main(){
        var is_group = false;
        //Проверим нет ли групп с нашими названиями
        var name_planfact = $('#planfact').val();


        BX24.callBatch({
            get_planfact: ['sonet_group.get',
                {
                    ORDER: {ID: 'ASC'},
                    FILTER: {NAME: name_planfact},

                }

            ]

        }, function(result)
        {
            //console.log(result);


            if(result.get_planfact.answer.total > 0){
                $('#result').append('<p>Группа с названием ' + name_planfact + ' уже существует, введите новое название</p>');
                is_group = true;
                //console.log('found')
                show_groups('planfact');
            } else if (result.get_planfact.answer.total == 0) {
                //console.log('not found')
                is_group = false;
            }


            if(!is_group){
                //console.log("to add")
                add_groups();
            }
        });
    }


    function add_groups() {

        var name_planfact = $('#planfact').val();
        //console.log(name_planfact)

        BX24.callBatch({
            group_planfact: ['sonet_group.create',
                {
                    NAME: name_planfact,
                    OPENED: 'Y',
                    INITIATE_PERMS: 'A',
                }

            ]
        }, function (result) {
            //console.log(result)
            // возвращает id группы
            var options_groups = {'planfact': result.group_planfact.answer.result};
            var groupid = result.group_planfact.answer.result;
            BX24.appOption.set('planfact_uv_groups', JSON.stringify(options_groups));
            //console.log(groupid)

            var codeplan = 'listplans' + groupid
            var codefact = 'listfacts' + groupid

            BX24.callBatch({
                add_list1: ['lists.add',
                    {
                        IBLOCK_TYPE_ID: 'lists_socnet',
                        IBLOCK_CODE: codeplan,
                        SOCNET_GROUP_ID: groupid,
                        FIELDS: {
                            NAME: 'planname'
                        }
                    }

                ],
                add_list2: ['lists.add',
                    {
                        IBLOCK_TYPE_ID: 'lists_socnet',
                        IBLOCK_CODE: codefact,
                        SOCNET_GROUP_ID: groupid,
                        FIELDS: {
                            NAME: 'factname'
                        }
                    }

                ],
                call_bind1: ['event.bind',
                    {
                        EVENT: 'onCrmDealUpdate',
                        HANDLER: 'https://lyantsevich.teatone.softmonster.ru/pub/planfactreport/handlers/dealhandler.php'

                    }
                ],
                call_bind2: ['event.bind',
                    {
                        EVENT: 'onCrmActivityUpdate',
                        HANDLER: 'https://lyantsevich.teatone.softmonster.ru/pub/planfactreport/handlers/activityhandler.php'

                    }
                ],
                call_bind3: ['event.bind',
                    {
                        EVENT: 'OnTaskUpdate',
                        HANDLER: 'https://lyantsevich.teatone.softmonster.ru/pub/planfactreport/handlers/activityhandler.php'

                    }
                ]

            }, function (resultplus) {
               // console.log(resultplus)
                var groupplanid = resultplus.add_list1.answer.result;
                var grouppfactid = resultplus.add_list2.answer.result;
                BX24.callBatch({
                    add_listpf1: ['lists.field.add',
                        {
                            IBLOCK_TYPE_ID: 'lists_socnet',
                            IBLOCK_ID: groupplanid,
                            //IBLOCK_CODE: 'listplans',
                            SOCNET_GROUP_ID: groupid,
                            FIELDS: {
                                NAME: 'employee',
                                IS_REQUIRED: 'N',
                                MULTIPLE: 'N',
                                TYPE: 'N',
                                CODE: 'EMPLOYEE'
                            }
                        }

                    ],
                    add_listpf2: ['lists.field.add',
                        {
                            IBLOCK_TYPE_ID: 'lists_socnet',
                            IBLOCK_ID: groupplanid,
                            //IBLOCK_CODE: 'listplans',
                            SOCNET_GROUP_ID: groupid,
                            FIELDS: {
                                NAME: 'type',
                                IS_REQUIRED: 'N',
                                MULTIPLE: 'N',
                                TYPE: 'S',
                                CODE: 'TYPE'
                            }
                        }

                    ],
                    add_listpf3: ['lists.field.add',
                        {
                            IBLOCK_TYPE_ID: 'lists_socnet',
                            IBLOCK_ID: groupplanid,
                            //IBLOCK_CODE: 'listplans',
                            SOCNET_GROUP_ID: groupid,
                            FIELDS: {
                                NAME: 'month',
                                IS_REQUIRED: 'N',
                                MULTIPLE: 'N',
                                TYPE: 'N',
                                CODE: 'MONTH'
                            }
                        }

                    ],
                    add_listpf4: ['lists.field.add',
                        {
                            IBLOCK_TYPE_ID: 'lists_socnet',
                            IBLOCK_ID: groupplanid,
                            //IBLOCK_CODE: 'listplans',
                            SOCNET_GROUP_ID: groupid,
                            FIELDS: {
                                NAME: 'year',
                                IS_REQUIRED: 'N',
                                MULTIPLE: 'N',
                                TYPE: 'N',
                                CODE: 'YEAR'
                            }
                        }

                    ],
                    add_listpf5: ['lists.field.add',
                        {
                            IBLOCK_TYPE_ID: 'lists_socnet',
                            IBLOCK_ID: groupplanid,
                            //IBLOCK_CODE: 'listplans',
                            SOCNET_GROUP_ID: groupid,
                            FIELDS: {
                                NAME: 'value',
                                IS_REQUIRED: 'N',
                                MULTIPLE: 'N',
                                TYPE: 'N',
                                CODE: 'VALUE'
                            }
                        }

                    ],
                    add_listff1: ['lists.field.add',
                        {
                            IBLOCK_TYPE_ID: 'lists_socnet',
                            IBLOCK_ID: grouppfactid,
                            //IBLOCK_CODE: 'listfacts',
                            SOCNET_GROUP_ID: groupid,
                            FIELDS: {
                                NAME: 'employee',
                                IS_REQUIRED: 'N',
                                MULTIPLE: 'N',
                                TYPE: 'N',
                                CODE: 'EMPLOYEE'
                            }
                        }

                    ],
                    add_listff2: ['lists.field.add',
                        {
                            IBLOCK_TYPE_ID: 'lists_socnet',
                            IBLOCK_ID: grouppfactid,
                            //IBLOCK_CODE: 'listfacts',
                            SOCNET_GROUP_ID: groupid,
                            FIELDS: {
                                NAME: 'type',
                                IS_REQUIRED: 'N',
                                MULTIPLE: 'N',
                                TYPE: 'S',
                                CODE: 'TYPE'
                            }
                        }

                    ],
                    add_listff3: ['lists.field.add',
                        {
                            IBLOCK_TYPE_ID: 'lists_socnet',
                            IBLOCK_ID: grouppfactid,
                            //IBLOCK_CODE: 'listfacts',
                            SOCNET_GROUP_ID: groupid,
                            FIELDS: {
                                NAME: 'date',
                                IS_REQUIRED: 'N',
                                MULTIPLE: 'N',
                                TYPE: 'S:Date',
                                CODE: 'DATE'
                            }
                        }

                    ],
                    add_listff4: ['lists.field.add',
                        {
                            IBLOCK_TYPE_ID: 'lists_socnet',
                            IBLOCK_ID: grouppfactid,
                            //IBLOCK_CODE: 'listfacts',
                            SOCNET_GROUP_ID: groupid,
                            FIELDS: {
                                NAME: 'value',
                                IS_REQUIRED: 'N',
                                MULTIPLE: 'N',
                                TYPE: 'N',
                                CODE: 'VALUE'
                            }
                        }

                    ]
                }, function (resultplus) {
                    //console.log(resultplus)
                    //console.log(BX24.appOption.get('planfact_uv_groups'));
                    BX24.installFinish();
                });
            });


            //BX24.callBind('onCrmDealUpdate', 'https://lyantsevich.teatone.softmonster.ru/pub/planfactreport/handlers/dealhandler.php');
            //BX24.callBind('onCrmActivityUpdate', 'https://lyantsevich.teatone.softmonster.ru/pub/planfactreport/handlers/activityhandler.php');
            //BX24.callBind('OnTaskUpdate', 'https://lyantsevich.teatone.softmonster.ru/pub/planfactreport/handlers/activityhandler.php');
            //BX24.appOption.set('planfact_uv_groups', JSON.stringify(options_groups));
            //window.location.replace('index.php');
            //редирект
            //BX24.installFinish();

        });
    }


    function show_groups(id){
        $('#save').show();
        $('#' + id).show();
    }



    $(document).ready(function() {
        BX24.init(function(){

            main();

            //add_groups();
            $('#save').click(function(event) {
                main();
            });

        });

    });

</script>
<style type="text/css">
    .hide{
        display: none;
    }
</style>
<div id="result"></div>
<input class='hide' type="text" name="planfact" id="planfact" value="План-факт отдела продаж" placeholder="Новое название для группы план-факт">
<button class='hide' id="save">Переименовать</button>

</body>
</html>