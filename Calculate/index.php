<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Калькулятор расчета платы");
?> 
<?php
$connection = Bitrix\Main\Application::getConnection();
$sqlHelper = $connection->getSqlHelper();
$get_tarifs = 'SELECT * FROM `tarifs`';
$t_vodosnab = $connection->query($get_tarifs);

$t1 = 0;    /* Тариф водоснабжения */
$t2 = 0;    /* Тариф водоотведения пока не определен*/

if ($t = $t_vodosnab->fetch()) {
	echo "<script>console.log('".$t['vodosnab'].' '.$t['vodootved']."')</script>";
	$t1 = $t['vodosnab'];
	$t2 = $t['vodootved'];
}

if (isset($_POST["vodosnab"]) && isset($_POST["vodootved"])) {
	$querry = 'UPDATE `tarifs` SET `vodosnab`='.$_POST["vodosnab"].', `vodootved`='.$_POST["vodootved"];
	echo "<script>console.log('".$_POST['vodosnab'].' '.$_POST['vodootved']."')</script>";
	$change_tarifs = $connection->queryExecute($querry);
}

if (isset($_GET['num1'])) $string_n1 = htmlentities($_GET['num1']); /* Нагрузка водоснабжения */
if (isset($_GET['num2'])) $string_l1 = htmlentities($_GET['num2']); /* Длинная трубы водоснабжения */
if (isset($_GET['select1']) ? $string_d1 = htmlentities($_GET['select1']) : 1) ; /* Диаметр трубы водоснабжения */
if (isset($_GET['num3'])) $string_n2 = htmlentities($_GET['num3']); /* Нагрузка водоотведения */
if (isset($_GET['num4'])) $string_l2 = htmlentities($_GET['num4']); /* Длинная трубы водоотведения */
if (isset($_GET['select2']) ? $string_d2 = htmlentities($_GET['select2']) : 1) ; /* Диаметр трубы водоотведения */

$isEmpty = false;
if ((empty($string_n1)) && (empty($string_l1)) && (empty($string_d1))
    && (empty($string_n2)) && (empty($string_l2)) && (empty($string_d2))) $isEmpty = true; /* проверка на заполеные строки*/

if ($isEmpty == false) {
    $n1 = (float)$string_n1;
    $l1 = (int)$string_l1;
    $d1 = (float)$string_d1;
    $n2 = (float)$string_n2;
    $l2 = (int)$string_l2;
    $d2 = (float)$string_d2;

    /* расчет водоснабжения */
    if (isset($_GET['vodosnab'])) {
        if ($_GET['str1'] == '') /* расчет без стройки */ {
            $c = $n1 * $t1;
            $k = 0;
        } else /* расчет со стройкой */ {
            $c = (($n1 * $t1) + ($d1 * $l1));
            $k = 0;
        }
    }
    /* расчет водоотведения */
    if (isset($_GET['kanal'])) {
        if ($_GET['str2'] == '') /* расчет без стройки */ {
            $k = $n2 * $t2;
            $c = 0;
        } else /* расчет со стройкой */ {
            $k = (($n2 * $t2) + ($d2 * $l2));
            $c = 0;
        }
    }

} else {
    $n1 = 0;
    $l1 = 0;
    $d1 = 0;
    $n2 = 0;
    $l3 = 0;
    $d4 = 0;
}

?> 
<style type="text/css">
	.block1 {
		margin-top: -30px;
		color: black;
		font-size: 16px;
		font-weight: 500;
		line-height: 1.5;
		width: 360px;
		background: #fff;
		padding: 5px;
		padding-right: 0px;
		border-right: solid 1px #39379e;
		float: left;
	}

	.block1 input[type="number"] {
		position: relative;
		top: -3px;
		color: black;
		font-size: 14px;
		// font-weight: 600;
		background-color: #dce3fc;
		outline: none;
		line-height: 20px;
		text-indent: 5px;
		font-size: 14px;
		border: 1px solid #6c77a1;
		width: 90%;
		border-radius: 3px
	}

	.block1 select {
		position: relative;
		top: -3px;
		background-color: #dce3fc;
		font-size: 14px;
		outline: none;
		line-height: 20px;
		text-indent: 1px;
		border: 1px solid #6c77a1;
		width: 90%;
		border-radius: 3px;
		cursor: pointer;
	}

	.block1 span[id="span"] {
		position: relative;
		width: 100%
		text-align: center;
		left: calc(45% - 75px);
		margin-bottom: 50px;
		font-weight: 700;
	}

	.block2 {
		margin-top: -30px;
		color: black;
		font-size: 16px;
		font-weight: 500;
		line-height: 1.5;
		width: 360px;
		background: #fff;
		padding: 5px;
		border: solid 0px #6c77a1;
		float: left;
		position: relative;
		left: 40px;
	}

	.block2 input[type="number"] {
		position: relative;
		top: -3px;
		background-color: #dce3fc;
		outline: none;
		line-height: 20px;
		font-size: 14px;
		text-indent: 5px;
		font-size: 14px;
		border: 1px solid #6c77a1;
		width: 90%;
		border-radius: 3px
	}

	.block2 select {
		position: relative;
		top: -3px;
		background-color: #dce3fc;
		color: black;
		outline: none;
		line-height: 20px;
		font-size: 14px;
		text-indent: 1px;
		border: 1px solid #6c77a1;
		width: 90%;
		border-radius: 3px;
		cursor: pointer;
	}

	.block2 span[id="span"] {
		position: relative;
		width: 100%
		text-align: center;
		left: calc(45% - 62px);
		margin-bottom: 50px;
		font-weight: 700;
	}

	.rasch {
		position: relative;
		width: 150px;
		color: white;
		border: 0;
		left: calc(45% - 75px - 0.5em);
		text-decoration: none;
		padding: .4em 1em calc(.4em + 3px);
		border-radius: 1px;
		background: #1e3c50;
		transition: 0.2s;
		cursor: pointer;
	}

	.rasch:hover {
		background: #ff424f;
		border-radius: 10px;
		box-shadow: none;
	}

	.rasch:active {
		background: #9391f2;
		box-shadow: 0 3px #aaa8f7 inset;
	}

	.borderbottom {
		padding: 5px;
		width: 90%;
		border-bottom: solid 1px #39379e;
	}

	.for-admin-background {
		position: relative;
		width: 100%;
		height: 200px;

		border: 1px black solid;
		border-radius: 10px;

		background-color: #dce3fc;
	}

	.admin-form {
		width: 100%;
		text-align: center;
	}

	.admin-form input[type="text"] {
		position: relative;
		width: 80%;

		line-height: 20px;
		font-size: 14px;
		padding: 2px 8px;

		border: 1px black solid;
		border-radius: 3px;

	}

	.admin-form input[type="submit"] {
		position: relative;
		width: 150px;
		left: calc(50% - 75px;);

		padding: .4em 1em calc(.4em + 3px);
		border-radius: 1px;

		color: white;
		background: #1e3c50;
		transition: 0.2s;
		cursor: pointer;
	}

	.admin-form input[type="submit"]:hover {
		background: #ff424f;
		border-radius: 10px;
		box-shadow: none;
	}

	.input-span {
		position: relative;
		font-size: 12px;
		left: 0;
		margine: 0;
	}

</style>
<div>
	<h1>Калькулятор расчета платы за подключение к Водоснабжению и Водоотведению</h1>
</div>
<br/>
<div style="width: 100%; height: 370px;">
	<div class="block1">
		<form action="" method="get">
			<p>
				<span id="span">Водоснабжение</span><br/><br/>
				<input type="checkbox" name="str1" value="1"> - со стройкой <br/>
				<span class="input-span">Нагрузка, м3 </span><br/>
				<input type="number" size="3" name="num1" min="0" max="500" step="0.1"> <br>
				<span class="input-span">Диаметр,мм </span><br>
				<select name="select1" size="1">
					<option selected="" value="730.06">20</option>
					<option value="772.06">25</option>
					<option value="824.40">32</option>
					<option value="985.06">40</option>
					<option value="1104.76">50</option>
					<option value="1265.40">63</option>
					<option value="2626.80">75</option>
				</select>
				<br>
				<span class="input-span">Количество </span><br/>
				<input type="number" size="3" name="num2" min="1" max="500">
			</p>
			<p>
				<input type="submit" class="rasch" name="vodosnab" value="Отправить">
			</p>
		</form>
		<p>
		</p>
		<div class="borderbottom"></div>
		<p>
			Расчет стоимости подключения: <br>
			Плата за подключение - <?php
			if ($isEmpty == false) {
				echo number_format($c, 2, ',', ' ') . ' руб.';
			}
			?>
		</p>
	</div>
	<div class="block2">
		<form action="" method="get">
			<p>
				<span id="span">Канализация</span><br/><br/>
				<input type="checkbox" name="str2" value="1">- со стройкой <br>
				<span class="input-span">Нагрузка, м3 </span><br/>
				<input type="number" size="3" name="num3" min="1" max="500"> <br>
				<span class="input-span">Диаметр,мм </span><br>
				<select name="select2" size="1">
					<option selected="" value="911.26">110</option>
					<option value="1173.37">160</option>
					<option value="1677.76">200</option>
				</select>
				<br>
				<span class="input-span">Количество </span><br/>
				<input type="number" size="3" name="num4" min="1" max="500">
			</p>
			<p>
				<input type="submit" class="rasch" name="kanal" value="Отправить">
			</p>
		</form>
		<p>
		</p>
		<div class="borderbottom"></div>
		<p>
			Расчет стоимости подключения: <br>
			Плата за подключение - <?php
				if ($isEmpty == false) {
					echo number_format($k, 2, ',', ' ') . ' руб.';
				}
			?>
		</p>
	</div>
</div>
<br/>
<? if (in_array(5, $USER->GetUserGroupArray()) || in_array(8, $USER->GetUserGroupArray()) || in_array(1, $USER->GetUserGroupArray())): ?>
	<span style="font-weight: 700;">Здесь администратор может поменять тарифы на водоотведение и водоснабжение</span><br/><br/>
	<div class="for-admin-background">
		<form action="" method="post" class="admin-form" >
			<br/>
			<span style="position: relative; font-size: 12px; right: 221px;">Новый тариф водоснабжения *</span><br/>
			<input name="vodosnab" type="text" required /> <br/>
			<span style="position: relative; font-size: 12px; right: 224px;">Новый тариф водоотведения *</span><br/>
			<input name="vodootved" type="text" required /> <br/><br/>
			<input name="send" type="submit" value="Отправить" />
		</form>
	</div>
<? endif; ?>
<?
	require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");
?>