<?php

require_once __DIR__ . "/config.php";

ob_start();
define('API_KEY',$TG_BOT_TOKEN);
ini_set("log_errors","off");
date_default_timezone_set('Asia/Kolkata');
function Alvi($method,$datas=[]){
$url = "https://api.telegram.org/bot".API_KEY."/".$method;
$ch = curl_init();
curl_setopt($ch,CURLOPT_URL,$url);
curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
curl_setopt($ch,CURLOPT_POSTFIELDS,$datas);
$res = curl_exec($ch);
if(curl_error($ch)){
var_dump(curl_error($ch));
}else{
return json_decode($res);
}
}
function AlviReplay($image_url)
{
    $ch = curl_init('https://captionbot.azurewebsites.net/api/messages');
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(['Type' => 'CaptionRequest', 'Content' => $image_url]));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    $response = curl_exec($ch);
    curl_close($ch);
    $response = json_decode($response);

    return $response;
}
$update = json_decode(file_get_contents('php://input'));
$message = $update->message;
$chat_id = $message->chat->id;
$message_id = $message->message_id;
$from_id = $message->from->id;
$msg = $message->text;
$first_name = $message->from->first_name;
$last_name = $message->from->last_name;
$username = $message->from->username;
$reply = $update->message->reply_to_message->message_id;
$photo = $message->photo;
if($photo){
$file = $photo[count($photo)-1]->file_id;
$get = Alvi('getfile',['file_id'=>$file]);
$patch = $get->result->file_path;
$URL = 'https://api.telegram.org/file/bot'.API_KEY.'/'.$patch;
Alvi('sendMessage',[
'chat_id'=>$chat_id,
'text'=> AlviReplay($URL),
'reply_to_message_id'=>$message_id,
]);
}
if($msg == "/start" or $msg == "/start@Bot"){
Alvi('sendMessage',[
'chat_id'=>$chat_id,
'text'=>"***Merhaba  👋 $first_name,
Ben $BOT_NAME / Ben yapay zekayım ve senin attığın fotoları tanıyorum.",
'reply_to_message_id'=>$message_id,
'parse_mode'=>"MarkDown",
'reply_markup' =>  json_encode([
'inline_keyboard' => [
[['text' => "DESTEK",'url' => "https://telegram.me/ubsohbet"],['text' => "CREATOR", 'url' => "https://telegram.me/wertin"]],
[['text' => "EKLE", 'url' => "https://telegram.me/$BOT_USERNAME?startgroup=False"],['text' => "Repo", 'url' => "https://github.com/wertinium/photoaibot"]], 
]])
]);
}
?>
