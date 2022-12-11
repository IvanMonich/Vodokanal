<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Узнать статус заявки");

$connection = Bitrix\Main\Application::getConnection();
$sqlHelper = $connection->getSqlHelper();

$status = "";
if(isset($_POST["number_of_application"])) {
	$querry = 'SELECT `status` FROM `applications` WHERE `id`='.$_POST["number_of_application"];
	$get_status = $connection->query($querry);
	if($s = $get_status->fetch()) {
		$status = $s['status'];
	}
}

if(isset($_POST["application_change_number"]) && isset($_POST["application_change_value"])) {
	$querry = 'UPDATE `applications` SET status="'.$_POST["application_change_value"].'" WHERE id='.$_POST["application_change_number"].'';
	$change_status = $connection->queryExecute($querry);
}
?>
<style type="text/css">
	.zoom-span {
		position: relative;
		font-style: italic;
		transition: 0.1s;
		cursor: default;
	}

	.zoom-span:hover {
		width: 1500px;
		height: 32px;
		font-size: 16px;
		color: #ff424f;
		font-style: normal;
	}

	.number {
		width: calc(50% - 8px);
		border: 1px #6c77a1 solid;
		border-radius: 5px;
		background-color: #dce3fc;
		padding: 5px 8px;
	}

	.send-button {
		position: relative;
		left: 20px;
		width: 150px;
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

	.line {
		position: relative;
		width: 70%;
		left: 15%;
		border-bottom: 1px black solid;
	}

	.for-admin-background {
		width: 100%;
		font-weight: 300;
		font-size: 12px;
	}

	.for-admin-background table {
		width: 100%;
	}

	.for-admin-background th {
	   border: 1px solid black;
	}

	.for-admin-background td {
	   border: 1px solid black;
		height: 30px;
		text-align: center;
	}

	input[type="number"] {
		width: 200px;
		border: 1px #6c77a1 solid;
		border-radius: 5px;
		background-color: #dce3fc;
		padding: 5px 8px;
	}

	select {
		position: relative;
		left: 20px;
		margin-right: 20px;
		width: 200px;
		border: 1px #6c77a1 solid;
		border-radius: 5px;
		background-color: #dce3fc;
		padding: 5px 8px;
	}

	td {
		border: 1px solid gray;
		border-radius: 5px;
		padding: 5px 10px;
	}

	th {
		border: 1px solid gray;
		border-radius: 5px;
		padding: 5px 10px;

		font-size: 14px;
		background-color: #dce3fc;
	}

	tr {
		cursor: default;
		transition: all 0.2s;
	}

	td:hover {
		position: relative;
		background-color: white;
		border: 1px solid white;
	}

	tr:hover {
		background-color: #00ff2ac1;
	}

	.status {
		font-size: 24px;
		font-weight: 700;
	}

</style>
<h1>Проверьте статус исполнения вашего заявления</h1>
<span>После отправки заявления и пакета документов вы получите номер
на адрес указанной электронной почты. По этому номеру вы сможете узнавать
	статус ваших документов онлайн, введя его в окно формы.</span><br/>
<h2>Статусы</h2>
<span class="zoom-span">&bull; Статус<span style="font-weight: 800;"> "на рассмотрении" </span> 
&mdash; специалист проверяет прикрепленные копии документов.</span><br/><br/>
<span class="zoom-span">&bull; Статус<span style="font-weight: 800;"> "принято" </span> 
&mdash; документы оформлены корректно, и вы можете записаться к специалисту для сдачи оригиналов.</span><br/><br/>
<span class="zoom-span">&bull; Статус<span style="font-weight: 800;"> "на доработку" </span> 
&mdash; представленные документы оформлены с ошибками, которые необходимо устранить в течение 20 рабочих дней. 
Свои комментарии специалист направит вам по указанной электронной почте или пояснит во время личной встречи.</span><br/><br/>
<span class="zoom-span">&bull; Статус<span style="font-weight: 800;"> "к выдаче" </span> 
&mdash; вы можете получить требуемый документ в Центре обслуживания абонентов в порядке очереди.</span><br/><br/>

<span>Введите номер заявления в представленной ниже форме, чтобы узнать статус его исполнения.</span><br/><br/>
<div class="line"></div><br/>
<form action="" method="post">
	<span style="font-size: 12px; float: left;">Номер заявления *</span><br/>
	<input name="number_of_application" class="number" id="application" type="text" required/>
	<input class="send-button" type="submit" value="Отправить" name="send" />
</form><br/>
<? if ($status != "" && isset($_POST["number_of_application"])): ?> 
<div class="status">
	<span>Статус заявления №<? echo $_POST["number_of_application"] ?>: <span style="color: green;"><? echo $status ?></span></span>
</div>
<br/>
<? elseif ($status == "" && isset($_POST["number_of_application"])): ?>
<div class="status">
	<span>Заявление с номером <span style="color: red;"><? echo $_POST["number_of_application"] ?> </span>в базе не найдено!</span>
</div>
<br/><br/>
<? endif; ?>
<div class="line"></div><br/>
<? if (in_array(5, $USER->GetUserGroupArray()) || in_array(8, $USER->GetUserGroupArray()) || in_array(1, $USER->GetUserGroupArray())): ?>
	<? 
		$querry = 'SELECT * FROM applications WHERE 1';
		$get_applications = $connection->query($querry);
		$applications_list = [];
		while ($apps = $get_applications->fetch()) {
			array_push($applications_list, [$apps["id"], $apps["date1"], $apps["user"], $apps["tel"], $apps["email"], $apps["address"], $apps["status"]]);
		}
	?>
	<span style="font-weight: 700;">Здесь администратор может посмотреть список заявок и поменять статус выбранной заявки</span><br/><br/>
	<div class="for-admin-background">
		<form action="" method="post">
			<span style="font-size: 12px; float: left;">Номер заявления *</span>
			<span style="position: relative; font-size: 12px; left: 110px;">Новый статус заявления *</span><br/>
			<input id="application_change_number" name="application_change_number" type="number" required />
			<select name="application_change_value" size="1" required>
				<option selected="" value="На рассмотрении">на рассмотрении</option>
				<option value="На доработку">на доработку</option>
				<option value="Принято">принято</option>
				<option value="К выдаче">к выдаче</option>
				<input class="send-button" type="submit" value="Изменить" name="send" />
			</select>
		</form>
		<br/>
		<table>
			<tr><th>№<br/>Заявки</th><th>Дата<br/>заявки</th><th>ФИО</th><th>Телефон</th><th>Email</th><th>Адрес</th><th>Статус</th></tr>
				<? 
					function setInputValue($value) {
						echo '<script>document.getElementById("application_change_number").setAttribute("value", $value); </script>';
						//echo '<script>document.getElementById("application_change_number").innerHTML = '.$value.'; </script>';
						echo '<script>console.log("ok")</script>';
						print_r($value);

						// fn($item) => show_td($item, $id)
					}

					function show_td($value) {
						$on_click = "document.write('<?php setInputValue(".$id.") ?>');";
						echo ('<td>'.
							$value
							.'</td>');
					}

					function show_tr($value) {
						$id = $value["id"];
						echo ('<tr > '.
							  array_map(
								'show_td', 
								$value)
							.' </tr>');
					}

					array_map('show_tr', $applications_list); 
				?>
		</table>
	</div>
<? endif; ?>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>