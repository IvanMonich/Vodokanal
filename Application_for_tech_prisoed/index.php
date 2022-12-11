<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Заявка на техническое присоединение");
include_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/classes/general/captcha.php");

$cpt = new CCaptcha();
$captchaPass = COption::GetOptionString("main", "captcha_password", "");

if(strlen($captchaPass) <= 0)
{
    $captchaPass = randString(10);
    COption::SetOptionString("main", "captcha_password", $captchaPass);
}
$cpt->SetCodeCrypt($captchaPass);

$captcha_check = false;

if($APPLICATION->CaptchaCheckCode($_POST["captcha_word"], $_POST["captcha_code"]))
{
	$captcha_check = true;

	$connection = Bitrix\Main\Application::getConnection();
	$sqlHelper = $connection->getSqlHelper();

	$current_date = $DB->FormatDate(date($DB->DateFormatToPHP(CSite::GetDateFormat("SHORT")), time()), "DD.MM.YYYY", "YYYY-MM-DD");
	$stmp = MakeTimeStamp(date($DB->DateFormatToPHP(CSite::GetDateFormat("SHORT")), time()), "DD.MM.YYYY");
	$stmp = AddToTimeStamp(array("DD" => +19), $stmp);
	$end_date = date("Y-m-d", $stmp);

	$id = 0;
	$query_id = 'SELECT MAX(id) FROM `applications` WHERE 1';
	$get_id = $connection->query($query_id);
	if ($id_last = $get_id->fetch()) {
		$id = $id_last['MAX(id)'] + 1;
	}

	$address = $_POST["rayon"].', '.$_POST["city"].', ул. '.$_POST["street"].' д. '.$_POST["house"].get_isset(", корпус ", $_POST["korpus"]).get_isset(", кв. ", $_POST["kvartira"]);
	$querry = "INSERT INTO applications (id, date1, date2, user, tel, email, address, status) VALUES (".$id.", '".$current_date."','".$end_date."', '".$_POST["fio"]."', '".$_POST["phone"]."', '".$_POST["email"]."', '".$address."', 'В работе');";
	$add_app = $connection->queryExecute($querry);

	// _____________________________________Отправка сообщения на почту______________________________________

	// Инициализация переменных, необходимых для отправки сообщения
	$to_user = $_POST["email"];
	$to_admin = "pto@avodokanal.ru";
	$to_test = "test-20aajyt0l@srv1.mail-tester.com"; // test-20aajyt0l@srv1.mail-tester.com monichivan1@gmail.com
	$subject = "Application for technical connection";
	$message_for_user = "";
	$message_for_admin = "";

	$uid = md5(uniqid(time()));
	$from_admin = "pto@avodokanal.ru";
	$from_test = "mvpkappalulw@gmail.com";
	$headers = "From: ".$from_test."\r\n";
	$headers = "Reply-To: ".$from_test."\r\n";
	$headers .= "Return-Path: ".$from_test."\r\n"; 
	$headers .= "Organization: ОАО 'Водоканал Апшеронского района'\r\n";

	$headers .= "X-Priority: 3\r\n";
	$headers .= "X-Mailer: PHP". phpversion() ."\r\n" ;

	$headers .= "MIME-Version: 1.0\r\n";
	$headers .= "Content-type: multipart/mixed;boundary=\"".$uid."\"\r\n\r\n";

	// Формирование сообщения для клиента
	$message_for_user .= "--".$uid ."\n";
	$message_for_user .= 'Content-type: text/html; charset=utf-8' . "\r\n";
	$message_for_user .= 
	'
		<html>
			<head>
				<title>Заявка на техническое присоединение</title>
			</head>
			<body>
				<div style="color: black; font-size: 16px;">
					<h1 style="color: black;">Заявка №'.$id.'</h1>
					<h2 style="color: black;">'.$_POST["fio"].'<span style="font-weight: 400; color: black;">, вы оставляли заявку на техническое присоединение к сети
												ОАО "Водоканал Апшеронского района", номер  заявки, по которому можно отследить статус ее готовности - '.$id.'<br/>
												<a href="https://avodokanal.ru/status-zayavki/">Узнать статус исполнения</a>
												</span> </h2>
					<span style="font-style: italic; color: black;">Ниже вы видите данные, которые указали при отправке заявления</span><br/><br/>
					<span style="font-style: italic; color: black;">Телефон заявителя:</span> <span style="font-weight: 700; color: black;">'.$_POST["phone"].'</span><br/>
					<span style="font-style: italic; color: black;">Email заявителя:</span> <span style="font-weight: 700; color: black;">'.$_POST["email"].'</span><br/>
					<span style="font-style: italic; color: black;">Адрес указанный заявителем:</span> <span style="font-weight: 700; color: black;">'
						.$_POST["rayon"].', '.$_POST["city"].', ул. '.$_POST["street"].' д. '.$_POST["house"].get_isset(", корпус ", $_POST["korpus"]).get_isset(", кв. ", $_POST["kvartira"]).'</span>
					<br/>
					<span style="font-style: italic; color: black;">Дата подачи заявления - <span style="font-weight: 700; color: black;">'.$DB->FormatDate($current_date, "YYYY-MM-DD", "DD.MM.YYYY").'</span></span>
				</div>
			</body>
		</html>
	';
	$message_for_user .= "\n\n";

	// Формирование сообщения для админа
	$message_for_admin .= "--".$uid ."\n";
	$message_for_admin .= 'Content-type: text/html; charset=utf-8' . "\r\n";
	$message_for_admin .= 
	'
		<html>
			<head>
				<title>Заявка на техническое присоединение</title>
			</head>
			<body>
				<div style="color: black; font-size: 16px;">
					<h1 style="color: black;">Новая заявка на тех. присоединение (№'.$id.')</h1>
					<h2 style="color: black;"><span style="font-weight: 400; color: black;">Заявитель</span> '.$_POST["fio"].'</h2>
					<span style="font-style: italic; color: black;">Телефон заявителя:</span> <span style="font-weight: 700; color: black;">'.$_POST["phone"].'</span><br/>
					<span style="font-style: italic; color: black;">Email заявителя:</span> <span style="font-weight: 700; color: black;">'.$_POST["email"].'</span><br/>
					<span style="font-style: italic; color: black;">Адрес указанный заявителем:</span> <span style="font-weight: 700; color: black;">'
						.$_POST["rayon"].', '.$_POST["city"].', ул. '.$_POST["street"].' д. '.$_POST["house"].get_isset(", корпус ", $_POST["korpus"]).get_isset(", кв. ", $_POST["kvartira"]).'</span>
					<br/>
					<span style="font-style: italic; color: black;">Дата подачи заявления - <span style="font-weight: 700; color: black;">'.$DB->FormatDate($current_date, "YYYY-MM-DD", "DD.MM.YYYY").'</span></span>
					<br/><br/>
					<span style="font-style: italic; color: black;">Ниже прикреплен пакет документов, указанных пользователем</span>
				</div>
			</body>
		</html>
	';
	$message_for_admin .= "\n\n";

	// Прикрепление файлов
	for ($i = 0; $i < count($_FILES); $i++) {
		$doc_name = 'doc'.($i + 1);
		$file_name = $_FILES[$doc_name]['name'];
		$temp_name = $_FILES[$doc_name]['tmp_name'];
		$file_type = $_FILES[$doc_name]['type'];

		$base = basename($file_name);
		$extension = substr($base, strlen($base)-4, strlen($base));

		$file = $temp_name;
		$content = chunk_split(base64_encode(file_get_contents($file)));
	
		# Файлы для клиента
		$message_for_user .= "--".$uid ."\n";
		$message_for_user .= "Content-Type: ". $file_type ."; name=\"".$file_name."\"\n";
		$message_for_user .= "Content-Transfer-Encoding: base64\n";
		$message_for_user .= "Content-Disposition: attachment; filename=\"".$file_name."\"\n\n";
		$message_for_user .= $content."\n\n";
		$message_for_user .= "--".$uid ."\n";

		# Файлы для админа
		$message_for_admin .= "--".$uid ."\n";
		$message_for_admin .= "Content-Type: ". $file_type ."; name=\"".$file_name."\"\n";
		$message_for_admin .= "Content-Transfer-Encoding: base64\n";
		$message_for_admin .= "Content-Disposition: attachment; filename=\"".$file_name."\"\n\n";
		$message_for_admin .= $content."\n\n";
		$message_for_admin .= "--".$uid ."\n";
	}

	$message_for_user .= "--".$uid ."--\n";
	$message_for_admin .= "--".$uid ."--\n";

	$to_user_message = mail($to_user, $subject, $message_for_user, $headers);
	$to_admin_message = mail($to_admin, $subject, $message_for_admin, $headers);

	echo '<script>console.log('.$to_user_message.');</script>';
}

function get_isset($prefix, $value)
{
	if ($value != "")
	{
		return $prefix.$value;
	}
}
?>
<style type="text/css">
	.doc-form {
		font-weight: 300;
		user-select: none;
	}

	.doc-form select[name="rayon"] {
		width: 100%;
		border: 1px #6c77a1 solid;
		border-radius: 5px;
		background-color: #dce3fc;
		padding: 5px 8px;
	}

	.doc-form input {

	}

	.doc-form select[name="city"] {
		width: calc(50% - 8px);
		border: 1px #6c77a1 solid;
		border-radius: 5px;
		background-color: #dce3fc;
		padding: 5px 8px;
	}

	.doc-form h2 {
		font-weight: 500;
	}

	.doc-form h3 {
		font-weight: 500;
		text-align: center;
	}

	.doc-form span {
		font-size: 12px;
		font-weight: 300;
		user-select: none;
	}

	.fio {
		width: 100%;
		border: 1px #6c77a1 solid;
		border-radius: 5px;
		background-color: #dce3fc;
		padding: 5px 8px;
	}

	.phone {
		width: calc(50% - 8px);
		border: 1px #6c77a1 solid;
		border-radius: 5px;
		background-color: #dce3fc;
		padding: 5px 8px;
	}

	.email-span {
		position: relative;
		left: calc(266px + 5%);
	}

	.email {
		position: relative;
		width: calc(50% - 8px);
		left: 12px;
		border: 1px #6c77a1 solid;
		border-radius: 5px;
		background-color: #dce3fc;
		padding: 5px 8px;
	}

	.line {
		position: relative;
		width: 70%;
		left: 15%;
		border-bottom: 1px black solid;
	}

	.street {
		width: calc(50% - 8px);
		border: 1px #6c77a1 solid;
		border-radius: 5px;
		background-color: #dce3fc;
		padding: 5px 8px;
	}

	.address {
		position: relative;
		left: 14px;
		margine-left: 12px;
		width: 125px;
		border: 1px #6c77a1 solid;
		border-radius: 5px;
		background-color: #dce3fc;
		padding: 5px 8px;
	}

	.dom-span {
		position: relative;
		left: 355px;
	}

	.korpus-span {
		position: relative;
		left: 443px;
	}

	.kvartira-span {
		position: relative;
		left: 524px;
	}

	.doc-form input[type="file"] {
		visibility: hidden;
		opacity: 0;
		width: 1px;
		height: 1px;
	}

	.input-file-wrapper {
		position: relative;
		display: flex;
		width: 100%;
		height: 30px;
		border: 1px #6c77a1 solid;
		border-radius: 5px;
		background-color: #dce3fc; // a2b2e8
		padding: 5px 8px;
		cursor: pointer;
		transition: 0.2s;
		// animation: file-input-unhover 400ms ease-in-out;
	}

	.view {
		position: relative;
		left: calc(100% - 105px);
		top: 3px;
		width: 100px;
		height: 20px;
		background-color: #1e3c50;
		color: white;
		text-align: center;
		border-radius: 10px;
		line-height: 20px;
		font-size: 12px;
		transition: 0.2s;
	}

	.input-file-wrapper:hover .view {
		background-color: white;
		color: black;
		width: 100%;
		height: 100%;
		left: 0;
		top: 0;
		line-height: 26px;
		font-size: 16px;
		border-radius: 5px;
	}

	.for-files-span {
		font-style: italic;
		color: grey;
	}

	.send-button {
		position: relative;
		width: 150px;
		left:calc(50% - 75px);
		border: 0;
		color: white;
		border-radius: 1px;
		background-color: #1e3c50;
		padding: 5px 8px;
		cursor: pointer;
		transition: 0.2s;
	}

	.send-button:hover {
		background: #ff424f;
		border-radius: 10px;
		box-shadow: none;
	}

	.captcha-input {
		position: relative;
		width: 125px;
		top: -21px;
		border: 1px #6c77a1 solid;
		border-radius: 5px;
		background-color: #dce3fc;
		padding: 5px 8px;
		margin-right: 5px;
	}
</style>
<? if($to_user_message && $to_admin_message && $captcha_check): ?>
<h1>Заявка №<? echo $id; ?></h1>
<span>&#10004; Ваша заявка на техническое присоединение к сети ОАО "Водоканал Апшеронского района" была успешно отправлена.</span>
<br/>
<span>&#10004; В ближайшее время вам на почту придет письмо с номером заявки, по которому вы сможете отследить ее статус в разделе <a style="font-weight: bold; text-decoration: none;" href="../status-zayavki/">"Узнать статус заявки"</a>.</span>
<br/>
<span>&#10004; Если вы не видите сообщения с сайта на почте, попробуйте найти его в разделе спам.</span>
<? elseif(isset($_POST["send"]) && !$captcha_check): ?>
<h1>Что-то пошло не так...</h1>
<span>Вы не прошли проверку на робота (неправильно ввели CAPTCHA), вернитесь назад и попробуйте снова!</span>
<? elseif(!isset($_POST["send"])): ?>
<h1>Техническое присоединение к сети ОАО "Водоканал Апшеронского района"</h1>
<form action="" method="post" class="doc-form" enctype="multipart/form-data">
	<h2>Сформировать пакет документов</h2>
	<h3>Личные данные</h3>
	<span>Фамилия, Имя, Отчество *</span> <br>
	<input name="fio" type="text" class="fio" value"" required /> <br>
	<span>Ваш телефон *</span> <span class="email-span">Ваш e-mail *</span> <br>
	<input name="phone" type="tel" class="phone" required > <input name="email" type="email" class="email" required > <br>
	<span>Район вашего проживания *</span> <br>
	<select name="rayon" placeholder="Район вашего проживания" required >
		<option value="Апшеронский район">Апшеронсикй район</option>
	</select>
	<br/>
	<br/>
	<br/>
	<div class="line"></div>
	<h3>Адрес вашей регистрации</h3>
	<span>Город *</span> <br>
	<select name="city" placeholder="Город" required >
		<option value="Апшеронск">г. Апшеронск</option>
		<option value="Хадыженск">г. Хадыженск</option>
		<option value="Нефтегорск">пос. Нефтегорск</option>
		<option value="Черниговское">с. Черниговское</option>
		<option value="Тверская">ст. Тверская</option>
		<option value="Кабардинская">ст. Кабардинская</option>
		<option value="Нефтаная">ст. Нефтаная</option>
		<option value="Николаенко">хут. Николаенко</option>
		<option value="Новые Поляны">пос. Новые Поляны</option>
		<option value="Ширванская">ст. Ширванская</option>
		<option value="Асфальтовая Гора">пос. Асфальтовая Гора</option>
	</select> <br>
	<span>Улица *</span>
	<span class="dom-span">Дом *</span>
	<span class="korpus-span">Корпус</span>
	<span class="kvartira-span">Квартира *</span> <br>
	<input name="street" type="text" class="street" value"" required />
	<input name="house" type="text" class="address" value="" required />
	<input name="korpus" type="text" class="address" value=""/>
	<input name="kvartira" type="text" class="address" value="" />
	<br/>
	<br/>
	<br/>
	<div class="line"></div>
	<h3>Прикрепление документов</h3>
	<span class="for-files-span">Допустимые расширения файлов .doc .pdf .jpg .jpeg .rar .zip, до 20 мб </span>
	<input name="doc1" type="file" id="input-file-1" accept=".doc, .pdf, .jpg, .jpeg, .rar, .zip" required />
	<input name="doc2" type="file" id="input-file-2" accept=".doc, .pdf, .jpg, .jpeg, .rar, .zip" required />
	<input name="doc3" type="file" id="input-file-3" accept=".doc, .pdf, .jpg, .jpeg, .rar, .zip" required />
	<input name="doc4" type="file" id="input-file-4" accept=".doc, .pdf, .jpg, .jpeg, .rar, .zip" required />
	<input name="doc5" type="file" id="input-file-5" accept=".doc, .pdf, .jpg, .jpeg, .rar, .zip" required />
	<input name="doc6" type="file" id="input-file-6" accept=".doc, .pdf, .jpg, .jpeg, .rar, .zip" required />
	<input name="doc7" type="file" id="input-file-7" accept=".doc, .pdf, .jpg, .jpeg, .rar, .zip" required />
	<input name="doc8" type="file" id="input-file-8" accept=".doc, .pdf, .jpg, .jpeg, .rar, .zip" />
	<br/>
	<span>Заявление о выдаче условий подключения и заключении договора о подключении *</span><br/>
	<label for="input-file-1">
		<div class="input-file-wrapper">
			<div class="view">Обзор</div>
		</div>
	</label>
	<span id="selected-filename-1" style="color: green;"></span>
	<span>Копия паспорта (для физических лиц) *</span><br/>
	<label for="input-file-2">
		<div class="input-file-wrapper">
			<div class="view">Обзор</div>
		</div>
	</label>
	<span id="selected-filename-2" style="color: green;"></span>
	<span>Копия учередительных документов (для юридических лиц) *</span><br/>
	<label for="input-file-3">
		<div class="input-file-wrapper">
			<div class="view">Обзор</div>
		</div>
	</label>
	<span id="selected-filename-3" style="color: green;"></span>
	<span>Копия правоустанавливающих документов на земельный участок *</span><br/>
	<label for="input-file-4">
		<div class="input-file-wrapper">
			<div class="view">Обзор</div>
		</div>
	</label>
	<span id="selected-filename-4" style="color: green;"></span>
	<span>Топографическая карта участка (в масштабе 1:500) *</span><br/>
	<label for="input-file-5">
		<div class="input-file-wrapper">
			<div class="view">Обзор</div>
		</div>
	</label>
	<span id="selected-filename-5" style="color: green;"></span>
	<span>Ситуационный план *</span><br/>
	<label for="input-file-6">
		<div class="input-file-wrapper">
			<div class="view">Обзор</div>
		</div>
	</label>
	<span id="selected-filename-6" style="color: green;"></span>
	<span>Оригинал баланса водопотребления и водоотведения *</span><br/>
	<label for="input-file-7">
		<div class="input-file-wrapper">
			<div class="view">Обзор</div>
		</div>
	</label>
	<span id="selected-filename-7" style="color: green;"></span>
	<span style="font-weight: 700;">Прочие документы</span><br/>
	<span style="font-style: italic; color: grey;">Например, доверенность, подтверждающая полномочия лица, подписавшего запрос;
		технический паспорт на объект недвижимости (при необходимости)</span><br/>
	<label for="input-file-8">
		<div class="input-file-wrapper">
			<div class="view">Обзор</div>
		</div>
	</label>
	<span id="selected-filename-8" style="color: green;"></span>
	<script>
		$('#input-file-1').change(function() {
			document.getElementById("selected-filename-1").innerHTML = "Выбран файл - " +  $('#input-file-1')[0].files[0].name + "<br/>";
		});
		$('#input-file-2').change(function() {
			document.getElementById("selected-filename-2").innerHTML = "Выбран файл - " +  $('#input-file-2')[0].files[0].name + "<br/>";
		});
		$('#input-file-3').change(function() {
			document.getElementById("selected-filename-3").innerHTML = "Выбран файл - " +  $('#input-file-3')[0].files[0].name + "<br/>";
		});
		$('#input-file-4').change(function() {
			document.getElementById("selected-filename-4").innerHTML = "Выбран файл - " +  $('#input-file-4')[0].files[0].name + "<br/>";
		});
		$('#input-file-5').change(function() {
			document.getElementById("selected-filename-5").innerHTML = "Выбран файл - " +  $('#input-file-5')[0].files[0].name + "<br/>";
		});
		$('#input-file-6').change(function() {
			document.getElementById("selected-filename-6").innerHTML = "Выбран файл - " +  $('#input-file-6')[0].files[0].name + "<br/>";
		});
		$('#input-file-7').change(function() {
			document.getElementById("selected-filename-7").innerHTML = "Выбран файл - " +  $('#input-file-7')[0].files[0].name + "<br/>";
		});
		$('#input-file-8').change(function() {
			document.getElementById("selected-filename-8").innerHTML = "Выбран файл - " +  $('#input-file-8')[0].files[0].name + "<br/>";
		});
	</script>
	<input name="captcha_code" value="<?=htmlspecialchars($cpt->GetCodeCrypt());?>" type="hidden">
	<span>Подтвердите, что вы не робот (введите симолы с картинки) *</span><br/>
	<input id="captcha_word" name="captcha_word" type="text" class="captcha-input" required>
	<img src="/bitrix/tools/captcha.php?captcha_code=<?=htmlspecialchars($cpt->GetCodeCrypt());?>"><br/>
	<input type="submit" class="send-button" name="send" value="Отправить">
	<input name="captcha" id="captcha_trues" value="" type="hidden">
</form>
<? endif; ?>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>