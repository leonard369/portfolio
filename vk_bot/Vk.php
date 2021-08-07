<?php
class Vk{
	public $token;
    
    public $json=array();
    public function __construct($token) 
    {
    	$this->token=$token;
    }

    public function send($id,$k=array(), $message) {   //Задаём публичную функцию send для отправки сообщений
        //Заполняем массив $data инфой, которую мы через api отправим до вк. О функции api "messages.send" можно почитать в официальной документации вк
        $data = array(
            'peer_id'      => $id,
            'random_id'    =>rand(1,9999999),
            'message'      => $message,
            'keyboard'      => json_encode($k),
            'v'            => '5.90' //Версия для функции. Её передавать нужно обязательно. Узнать нужную можно через официальную документацию вк
        );
        //Получаем ответ через функцию отправки до апи, которую создадим ниже
        $out = $this->request('messages.send', $data);
        //И пусть функция вернёт ответ. Правда в данном примере мы это никак не будем использовать, пусть будет задаток на будущее
        return $out;
    }

    public  function request($method, $data = array()) {
        $curl = curl_init(); //мутим курл-мурл в переменную. Для отправки предпочтительнее использовать курл, но можно и через file_get_contents если сервер не поддерживает

        $data['access_token'] = $this->token; //токен, который нужно отправить вместе с запросом тоже нужно добавить в дату

        curl_setopt($curl, CURLOPT_URL, 'https://api.vk.com/method/' . $method); //Ссылки до разных методов апи вк выглядят так: https://api.vk.com/method/И_ТУТ_САМ_МЕТОД, поэтому метод вполне можно забивать в эту функцию и без всяких ссылок
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST'); //Отправляем через POST
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data); //Сами данные отправляемые

        $out = json_decode(curl_exec($curl), true); //Получаем результат выполнения, который сразу расшифровываем из JSON'a в массив для удобства

        curl_close($curl); //Закрываем курл
        return $out;

    }
    protected function sendOK() {
        set_time_limit(0);
        ini_set('display_errors', 'Off');
        if (is_callable('fastcgi_finish_request')) {
            echo 'ok';
            session_write_close();
            fastcgi_finish_request();
            return True;
        }
        ignore_user_abort(true);

        ob_start();
        header('Content-Encoding: none');
        header('Content-Length: 2');
        header('Connection: close');
        echo 'ok';
        ob_end_flush();
        flush();
        return True;
    }
}
