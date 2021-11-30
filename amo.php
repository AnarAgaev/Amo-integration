<?php
header('Content-type: text/plain; charset=utf-8');
ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
include "vendor/autoload.php";

use AmoCRM\{AmoAPI, AmoLead, AmoContact, AmoNote, AmoIncomingLeadForm, AmoAPIException};

$file_response = 'log.log';
// if (!empty($_GET)) {
// 	$fw = fopen($file_response, "a");
// 	fwrite($fw, "GET " . var_export($_GET, true) . "\n");
// 	fclose($fw);
// }
// if (!empty($_POST)) {
// 	$fw = fopen($file_response, "a");
// 	fwrite($fw, "POST " . var_export($_POST, true) . "\n");
// 	fclose($fw);
// }

try {	
	// Авторизация
	$subdomain = 'tatama7206';
	AmoAPI::oAuth2($subdomain);
	// AmoAPI::$debug = true;
	// AmoAPI::$debugLogFile = __DIR__.'/log.log';
	// Создаем новую заявку в неразобранном при добавлении из веб-формы
	$incomingLead = new AmoIncomingLeadForm();

  $subject = 'Тестовая заявка в Amo CRM';

	// Добавляем параметры сделки
	$lead = new AmoLead([
		'name' => $subject
	]);
	$lead->addTags([ 'название' ]);
	// $lead->setCustomFields([ 
	// 	603239 => [[

	// 		'value'  => 1323479
	// 	]]
	//  ]);
  
  // Пример добавления utm метки. Основной код ниже закоментирован
  $utm_source = 'utm-mark-source-write-here';
  $lead->setCustomFields([ 783935 => $utm_source ]);

	// if($_COOKIE['utm_source']){
		// $lead->setCustomFields([ 31943 => $_COOKIE['utm_source'] ]);
		// $lead->setCustomFields([ 31937 => $_COOKIE['utm_content'] ]);
		// $lead->setCustomFields([ 31939 => $_COOKIE['utm_medium'] ]);
		// $lead->setCustomFields([ 31941 => $_COOKIE['utm_campaign'] ]);
		// $lead->setCustomFields([ 31945 => $_COOKIE['utm_term'] ]);
	// }
	$incomingLead->addIncomingLead($lead);

  $name = isset($_POST['name']) ? $_POST['name'] : 'Уточнить имя!';
	$contact = new AmoContact([
		'name' => $name
	]);
  
  $phone = $_POST['phone'];
 	$contact->setCustomFields([
		783921 => [[
			'value' => $phone,
			'enum'  => 'WORK'
		]],
	]);
  
  $email = $_POST['mail'];
	if ($email != '') {
		$contact->setCustomFields([
			783923 => [[
				'value' => $email,
				'enum'  => 'WORK'
			]]
		]);
	}

	$incomingLead->addIncomingContact($contact);
	// Устанавливаем обязательные параметры 
	$incomingLead->setIncomingLeadInfo([
		'form_id'   => 860245,
		'form_page' => 'http://kitchen.g99322e8.beget.tech/temp.php',
		'form_name' => 'Тестовая интеграция AMO CRM',
		'form_send_at' => time(),
	]);
	// Сохраняем заявку
	$data = $incomingLead->save();

	$incomingLead2 = new AmoIncomingLeadForm();
	$incomingLead2->fillByUid($data[0]);
	$lead = $incomingLead2->getParams();

	//  echo "<pre>";
  // print_r($lead);
	//  echo "</pre>";
	$leadId = $lead['incoming_entities']['leads'][0]['id'];
	// echo $leadId;
  
  // В $message_for_amo добавляем отдельное сообщение для AMO в котором переносы как /n - как комментарий
  $message_for_amo = 'Здесь должны быть все данные с клиента (в том числе телефон, имя, емэйл, все данные квиза и т.д.)';
  
	if ($message_for_amo !='') {
		// Создание нового события типа "обычное примечание", привязанного к сделке
		$note = new AmoNote([
			'element_id'   => $leadId,
			'note_type'    => AmoNote::COMMON_NOTETYPE,
			'element_type' => AmoNOTE::LEAD_TYPE,
			'text'         => $message_for_amo
		]);

	  // Сохранение события и получение его ID
		$noteId = $note->save();
	}
	// Принимаем заявки из неразобранного
	AmoAPI::acceptIncomingLeads([
		'accept' => [
			$data[0],
		],
    
    // user_id взять из AMO из профиля пользователя id пользователя
    // status_id это идентификатор лида внутри отдельной воронки (pipe-line) Посмотреть можно в консоли у конкретного этапа прохождения лида либо в файле вывода данные /oauth2/index.php с параметром print_r(AmoAPI::getAccount($with = 'pipelines'));
		'user_id'   => 7667695,
		'status_id' => 44297998
	]);
	// print_r(AmoAPI::getAccount());
} catch (AmoAPIException $e) {
	printf('Ошибка (%d): %s' . PHP_EOL, $e->getCode(), $e->getMessage());
}