<?php
/*	Для вызова необходимо сделать аякс запрос на эту страницу и передать username, passwordSteamGuardCode.
*	При вызове происходит релогин, для того чтобы на сервере Стима изменить активное устройство
*	Для исправления ошибок типа: Перезагрузите страницу или попробуйте позже.
*	Возвращает ответ сервера.
*/
session_start();
include_once("loginData.php");// Фуйл с данными пользователя, username, password, SteamGuardCode. 
include_once("lib/api/AuthFunctions.php");
define('php-steamlogin', true);
require('main.php');

$SteamLogin = new SteamLogin(array(
	'username' => $username,
    'password' => $password,
    'datapath' => "DATAPATH" // Указать путь к файлу с куки.
));
if($SteamLogin->success){
	$SteamAuth = new SteamAuth;
    $authcode = $SteamAuth->GenerateSteamGuardCode($SteamGuardCode);
	$twofactorcode = $authcode;
    $logindata = $SteamLogin->login($authcode,$twofactorcode); 
	$login = array_values($logindata); 
	$_SESSION['SessionId'] = $login[1];
	$_SESSION['Cookies'] =  $login[2];
}
$response = ("[" . date("d.m.Y H:i:s") . "] Server response: Auth FAIL!");
if($SteamLogin->error != ''){
	$response = ("[" . date("d.m.Y H:i:s") . "] Server response: Auth FAIL! " . $SteamLogin->error);
}else{
	$response = ("[" . date("d.m.Y H:i:s") . "] Server response: Login is success! SessionID: " . substr($login[1],0,10) . "...; Cookies: " . substr($login[2],13,10) . "...;");
}

$JSON_response = array(
	'status' => $response
);
echo json_encode($JSON_response);
?>