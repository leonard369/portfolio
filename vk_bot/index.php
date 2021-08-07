<?php
include_once "./db.php";
include './Vk.php';
$vk_secret="npadnWkdj12kjas89";
$vk_confirm_code="6c896d98";
$vk= new Vk('TOKEN');
$data= json_decode(file_get_contents('php://input'));
$database= new PDO ("mysql:host=localhost;dbname=auto_bot","leonid","");
$body=$data->object->text;
$id=$data->object->peer_id;
$db= new db($database);
$query=$db->rows("SELECT * FROM `trips` WHERE `user_id`=?",array($id));
if($query==0){
    $db->query("INSERT INTO `trips` SET `user_id`=?",array($id));
}
$trips=$db->fetch("SELECT * FROM `trips` WHERE `user_id`=?",array($id));
if($data->type == 'confirmation'){
    exit($vk_confirm_code);
}
if($data->type =='message_new') {
    if(isset($data->object->payload)){
        $payload= json_decode($data->object->payload);
    }else{
        $payload=null;
    }
if($trips['role']== 1){
    if($trips['step']==6){
        $messages="Ваша поездка #".$trips['id']."\n"
            ."Начало движения:".$trips['start']."\n"
            ."Конец движения:".$trips['finish']."\n"
            ."Дата отправки:".$trips['date']."\n"
            ."Марка авто:".$trips['auto']." Количество мест:".$trips['seat']."\n"
            ."Стоимость поездки: ".$trips['price'];
        $arr =  (object)[
            'one_time' => true,
            'buttons' => (array)[
                (array)[
                    (object)[
                        "action" => (object)[
                            "type" => "text",
                            "payload" => json_encode((object)["command"=>"delete"]),
                            "label" => "Удалить"
                        ],
                        "color" => "primary"
                    ]
                ]
            ]
        ];
        $db->query("UPDATE `trips` SET `seat`=?, `step`=9 WHERE `user_id`=?",array($body,$id));
        $vk->send($id,$arr,$messages);
        exit("ok");
} elseif($trips['step']==5){
    $db->query("UPDATE `trips` SET `auto`=?, `step`=6 WHERE `user_id`=?",array($body,$id));
    $vk->send($id,array(),"Укажите количество мест");
    exit("ok");
} elseif($trips['step']==4){
   $db->query("UPDATE `trips` SET `price`=?, `step`=5 WHERE `user_id`=?",array($body,$id));
    $vk->send($id,array(),"Укажите марку авто");
   exit("ok");
} elseif($trips['step']==3){
     $db->query("UPDATE `trips` SET `date`=?, `step`=4 WHERE `user_id`=?",array($body,$id));
    $vk->send($id,array(),"Укажите стоимость поездки");
     exit("ok");
}elseif($trips['step']==2){
     $db->query("UPDATE `trips` SET `finish`=?, `step`=3 WHERE `user_id`=?",array($body,$id));
    $vk->send($id,array(),"Укажите дату отправки");
     exit("ok");
}elseif($trips['step']==1){
        $db->query("UPDATE `trips` SET `start`=?, `step`=2 WHERE `user_id`=?",array($body,$id));
        $vk->send($id,array(),"Укажите город назначения");
        exit("ok");
}
}elseif($trips['role']==2){
if($trips['step']==5){
    $arr =  (object)[
        'one_time' => true,
        'buttons' => (array)[
            (array)[
                (object)[
                    "action" => (object)[
                        "type" => "text",
                        "payload" => json_encode((object)["command"=>"search"]),
                        "label" => "Искать"
                    ],
                    "color" => "primary"
                ]
            ]
        ]
    ];
    $db->query("UPDATE `trips` SET `seat`=?, `step`=5 WHERE `user_id`=?",array($body,$id));
    $vk->send($id,array(),"Давай теперь поищем поездки");
    exit("ok");
}elseif($trips['step']==4){
    $db->query("UPDATE `trips` SET `price`=?, `step`=5 WHERE `user_id`=?",array($body,$id));
    $vk->send($id,array(),"Укажите количество мест");
    exit("ok");
} elseif($trips['step']==3){
    $db->query("UPDATE `trips` SET `date`=?, `step`=4 WHERE `user_id`=?",array($body,$id));
    $vk->send($id,array(),"Укажите стоимость поездки");
    exit("ok");
}elseif($trips['step']==2){
    $db->query("UPDATE `trips` SET `finish`=?, `step`=3 WHERE `user_id`=?",array($body,$id));
    $vk->send($id,array(),"Укажите дату отправки");
    exit("ok");
}elseif($trips['step']==1){
    $db->query("UPDATE `trips` SET `start`=?, `step`=2 WHERE `user_id`=?",array($body,$id));
    $vk->send($id,array(),"Укажите город назначения");
    exit("ok");
}
 if($payload->command=="search"){
$query=$db->fetch("SELECT * FROM `trips` WHERE `start`=? and `finish`=? and `date`=? and `role`=? and `seat`>=? ORDER BY RAND() LIMIT 1",array($trips['start'],$trips['finish'],$trips['date'],1,1));
 if($query['seat'] >=1){
 $message="Найдена поездка #".$query['id']."\n"
        ." Начало движения:".$query['start']."\n"
        ."Конец движения:".$query['finish']."\n"
        ."Дата отправки:".$query['date']."\n"
        ."Марка авто:".$query['auto']." Количество мест:".$query['seat']."\n"
        ."Стоимость поездки: ".$query['price'];
         $arr =  (object)[
                'one_time' => true,
                'buttons' => (array)[
                    (array)[
                        (object)[
                            "action" => (object)[
                                "type" => "text",
                                "payload" => json_encode((object)["command" => "apply","bron"=>$query['id']]),
                                "label" => "Забронировать"
                            ],
                            "color" => "primary"
                        ]
                    ],(array)[
                        (object)[
                            "action" => (object)[
                                "type" => "text",
                                "payload" => json_encode((object)["command" => "search"]),
                                "label" => "Искать"
                            ],
                            "color" => "primary"
                        ]
                    ]

                ]
            ];
$vk->send($id,$arr,$message);
exit('ok');
}else{
    $vk->send($id,array(),"К сожалению мест по вашему маршруту нет. Попробуйте изменить параметры запроса");
    exit('ok');
}
    }
}
if($payload !== null){
    switch ($payload->command){
        case 'delete':
            $vk->send($id,array(),"Удаляю вашу поездку");
            $db->query("DELETE FROM `trips` WHERE `user_id`=?",array($id));
            $payload->command="start";
            exit('ok');
            break;
        default:
            break;
        case 'start':
            $arr =  (object)[
                'one_time' => true,
                'buttons' => (array)[
                    (array)[
                        (object)[
                            "action" => (object)[
                                "type" => "text",
                                "payload" => json_encode((object)["command" => "drive"]),
                                "label" => "Я водитель"
                            ],
                            "color" => "primary"
                        ],
                        (object)[
                            "action" => (object)[
                                "type" => "text",
                                "payload" => json_encode((object)["command" => "pass"]),
                                "label" => "Я попутчик"
                            ],
                            "color" => "negative"
                        ]
                    ]
                ]
            ];
            $vk->send($id,$arr,"Выберите роль поездки");
            exit('ok');
            break;
        case 'drive':
            $text="Укажите город отправки";
            $vk->send($id,$arr=array(),$text);
            $db->query("UPDATE `trips` SET `role`=1, `step`=1 WHERE `user_id`=?",array($id));
            break;
        case 'pass':
            $text="Укажите город отправки";
            $vk->send($id,$arr=array(),$text);
            $db->query("UPDATE `trips` SET `role`=2, `step`=1 WHERE `user_id`=?",array($id));
            break;
        case 'apply':

            $q=$db->fetch("SELECT * FROM `trips` WHERE `id`=?",array($payload->bron));
            $message="Вы забронировали поездку под номером #".$payload->bron."\n"
            ."Водитель: vk.com/id".$q['user_id'];
            $db->query("UPDATE `trips` SET `seat`=`seat`-? WHERE `id`=?",array($trips['seat'],$q['id']));
            $vk->send($q['user_id'],array(),"Пользователь vk.com/id".$trips['user_id']."\nЗабронировал вашу поездку:".$q['id']."\n С количеством мест:".$trips['seat']);
            $vk->send($id,array(),$message);
        break;

    }
}else{
}

}
echo "ok";
