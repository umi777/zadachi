<?php
	session_start();
?>
<!DOCTYPE html>
<html dir="ltr" lang="ru" class="smallTop">
<head>

	<title>Задачи</title>
	<meta charset="UTF-8">
	<style>
		#top h3.logo
		{
			background-image: url(/img/logo_zzz.png);
		}
		#topmovie {
		background: #0E56C2 url('https://www.zzz.com.ua/img/movie_poster.png') no-repeat top;
		height: auto;
		max-width: 1920px;
		min-width: auto;
		width: 100%;
		left: 0;
		right: 0;
		margin: auto;
		text-align: center;
		vertical-align: bottom;
		display: inline-block;
	
		}
		#ord button {
			cursor:pointer;
		}
	</style>
</head>
<body>
	<div id="topmovie">
	<h1 style="height:auto;">
	Задачи
	</h1>
	</div>
<?php
    $host = 'mysql.zzz.com.ua'; // имя хоста (уточняется у провайдера)
    $database = 'umi9956'; // имя базы данных, которую вы должны создать
    $user = 'umi777'; // заданное вами имя пользователя, либо определенное провайдером
    $pswd = '000000Az'; // заданный вами пароль
	echo 'База данных - '.$database.' Имя пользователя - '.$user.'<br>';
    $mysqli = @new mysqli($host, $user, $pswd, $database);
    if ($mysqli->connect_errno) {
        echo "Не удалось подключиться к MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
    }
    echo $mysqli->host_info . "\n";
	//$sql  = "SELECT `text` FROM `zadachi` WHERE `username` = 'master' AND `status` = 0"  ;
	$sql  = "SELECT * FROM `zadachi`  \n" . "ORDER BY `zadachi`.`username` ASC"  ;
    
    
    //if (isset($_GET['page'])) {echo $_GET['page'] . "\n";};

    //print_r ($_POST);
	echo '<br>';
	/* Авторизация */
	$errorauth = false;
    if (isset($_POST['auth'])) {
		$login = $_POST['login'];
		$password = md5($_POST['password']);
		$_SESSION['login'] = $login;
		$_SESSION['password'] = $password;
		$errorauth = true;
	}

    if (isset($_GET['f']) && $_GET['f'] == 'logout') {
        unset($_SESSION['login']);
        unset($_SESSION['password']);
		$auth = false;
    }

	$login = 'admin';
	$password = '202cb962ac59075b964b07152d234b70';
	$auth = false;
	$iss = (isset($_SESSION['login']) && isset($_SESSION['password']));
	if ($iss && $_SESSION['login'] === $login && $_SESSION['password'] === $password){
		$auth = true;
		$errorauth = false;
	}

	/* авторизациЯ*/
	//if ($auth) echo '1'; else echo '0';
	if (isset($_POST['send']) && $auth) {
		$id = $_POST['id'];
		$text = $_POST['text'];
		$sql  = 'UPDATE `zadachi` SET `text` = '.$text.' WHERE `zadachi`.`ID` = '.$id.'';
		$sql  = 'UPDATE `zadachi` SET `text` = \''.$text.'\', `adminedit` = 1 WHERE `zadachi`.`ID` = '.$id.'';
		if ($mysqli->query($sql) === TRUE) { 
			echo "<script>alert(\"Задача успешно отредактирована\");</script>";
		} else {
			echo "Error updating record: " . $conn->error;
		}	
	}
	else if (isset($_POST['send']) && !$auth) {echo "<script>alert(\"Вы не авторизованы\");</script>";}
	
    if (isset($_POST['sort'])) {
        $username = $_POST['username']?? false;
        $email = $_POST['email']?? false;
        $txt = $_POST['txt']?? false;
    if (!$username || !$email || !$txt) {echo "<script>alert('Введите данные!!!');</script>";
    }
        else {
			$array1[] = ['family' => $username, 'name' => $email, 'date' => $txt];
            //print_r ($array1);
			$array1[] = ['family' => ($_POST['family1']?? false), 'name' => ($_POST['name1']?? false), 'date' => ($_POST['date1']?? false)];
            //print_r ($array1);
            $array1[] = ['family' => ($_POST['family2']?? false), 'name' => ($_POST['name2']?? false), 'date' => ($_POST['date2']?? false)];
            //print_r ($array1);
            // $array2[] = round((mt_rand(1000, 9999)/100), 2);
        }
    }

    if (isset($_POST['newz'])) if ($_POST['newz'] == 'Сохранить') {
        $username = htmlspecialchars($_POST['username']?? false);
        $email = $_POST['email']?? false;
        $txt = htmlspecialchars($_POST['txt']?? false);
		if (!$username || !$email || !$txt) {
				echo 'Введите данные!!!';
			}
			else {
				$err = "INSERT INTO zadachi (username,email,text) VALUES('$username','$email','$txt')";
				if ($mysqli->query($err) === TRUE) { 
					echo "<script>alert(\"Задача успешно создана\");</script>";
				} else {
					echo "Error updating record: " . $conn->error;
				}	

			}
	}	

?>
<?php if($errorauth) { ?><p>Неверные логин и/или пароль!</p><?php } ?>
<?php if ($auth) { ?>
    <p>Вы вошли, как <?=$login?>!</p>
<form id="edit" action="" method="POST">
    <div>
        <input placeholder="№ п/п:" type="text" name="id" required />
        <textarea  placeholder="Текст задачи" type="text" name="text" required /></textarea >
		<input type="submit" name="send" value="Отправить"/>
    </div>
</form>	
    <a href='index.php?f=logout'>Выход</a>
<?php } else { ?>
<form id="auth" action="index.php" method="POST">
    <fieldset>
		<legend>Авторизация</legend>
		<div>
			<input placeholder="Логин:" type="text" name="login" value="<?=($_POST['login']?? false)?>" required />
			<input placeholder="Пароль:" type="password" name="password" required />
			<input type="submit" name="auth" value="Авторизоваться"/>
		</div>
	</fieldset>
</form>
<?php } ?>
<?php
  /* Входные параметры Pagination*/

    $result_set = $mysqli->query('SELECT COUNT(`id`) as `count` FROM `zadachi`');
	$table = [];
    while (($row = $result_set->fetch_assoc()) != false) {
        $table[] = $row;
    }

	$count_zadach = $table[0]['count'];
	//echo (1+intdiv($count_zadach, 3));
	
	if (fmod($count_zadach, 3) == 0) {$count_pages = (intdiv($count_zadach, 3));
		} else {$count_pages = 1+intdiv($count_zadach, 3);};
	if (isset($_GET['page']) and is_numeric($_GET['page']) and $_GET['page'] <= $count_pages) {$active = $_GET['page'];} else $active = 1;
	$count_show_pages = 3;

	$url = "/1/zadachi/index.php";
	$url_page = "/1/zadachi/index.php?page=";
	if ($count_pages > 1) { // Всё это только если количество страниц больше 1
    /* Дальше идёт вычисление первой выводимой страницы и последней (чтобы текущая страница была где-то посредине, если это возможно, и чтобы общая сумма выводимых страниц была равна count_show_pages, либо меньше, если количество страниц недостаточно) */
    $left = $active - 1;
    $right = $count_pages - $active;
    if ($left < floor($count_show_pages / 2)) $start = 1;
    else $start = $active - floor($count_show_pages / 2);
    $end = $start + $count_show_pages - 1;
    if ($end > $count_pages) {
      $start -= ($end - $count_pages);
      $end = $count_pages;
      if ($start < 1) $start = 1;
    } 
?>
<?php /*Вывод списка задач*/
	if (isset($_GET['page']) and is_numeric($_GET['page']) and ($_GET['page'] <= $count_pages)) {$active_page = $_GET['page'];} else $active_page = 1;;
	$sortlist = (isset($_GET['sortlist']) and ($_GET['sortlist'] == "username" or $_GET['sortlist'] == "email" or $_GET['sortlist'] == "text" or $_GET['sortlist'] == "status"))? $_GET['sortlist'] : "username";
	$ordby = (isset($_GET['ordby']) and ($_GET['ordby'] == "ASC" or $_GET['ordby'] == "DESC"))? $_GET['ordby'] : "ASC";
	$ordbyhead = (isset($ordby) and $ordby == "ASC")?  "DESC" : "ASC";
	$query = 'SELECT `ID`, `username` , `email` , `text` , `status` , `adminedit` FROM `zadachi` ORDER BY `'.($sortlist).'`'.($ordby).' LIMIT '. ($active_page*3-3) .' , ' . (3) . '';
    //echo $query;
	$result_set = $mysqli->query($query);
	$query = 'SELECT `username` , `email` , `text` , `status` , `adminedit` FROM `zadachi` ORDER BY `'.($sortlist).'`'.($ordby).' LIMIT '. ($active_page*3-3) .' , ' . (3) . '';
	
/*    $table = [];
	while (($row = $result_set->fetch_assoc()) != false) {
        $table[] = $row;
	}
	foreach ($result_set as $arr) { print_r ($arr);
	}	*/
	//print_r ($table);
	$s = 'status';
	$id = 'ID';
	$wid = '50px';
	$w30 = '60px';
	$w230 = '220px';
/*	echo '<div style="margin: 0 auto; width: 720px;">';
		foreach ($result_set as $arr) {
			echo ('<div style="background:bisque">');
			foreach ($arr as $key => $value) {if ($key != 'adminedit')
				echo ('<div style="background:'.(($key==$s and $value)? 'green' : 'bisque').'; float:left; width:'.(($key==$s)? $w30 : $w230).'">&nbsp;'.$value.''.(($arr['adminedit'] and $key=="text")?  '<sub>ред. админ.</sub>':'').'</div>');
					
			}
			echo '</div>';
		}
	echo '</div>';	*/
?>
	<div style="margin: 0 auto; width: 770px;">
		<div style="background:coral; float:left; width:50px">
			<span>№ п/п</span> 
		</div>

		<div style="background:coral; float:left; width:220px">
			<a href="<?=$url?>?page=<?=$active_page?>&sortlist=username&ordby=<?=$ordbyhead?>" title="Сортировать по имени пользователя">Имя пользователя</a> 
		</div>

		<div style="background:coral; float:left; width:220px">
			<a href="<?=$url?>?page=<?=$active_page?>&sortlist=email&ordby=<?=$ordbyhead?>" title="Сортировать по E-mail пользователя">E-mail пользователя</a> 
		</div>

		<div style="background:coral; float:left; width:220px">
			<a href="<?=$url?>?page=<?=$active_page?>&sortlist=text&ordby=<?=$ordbyhead?>" title="Сортировать по тексту задания">Текст задачи</a> 
		</div>

		<div style="background:coral; float:left; width:60px">
			<a href="<?=$url?>?page=<?=$active_page?>&sortlist=status&ordby=<?=$ordbyhead?>" title="Сортировать по статусу">Статус</a> 
		</div>
	
		<div style="background:bisque; clear:left; width:770px">&nbsp;</div>
	<?
	foreach ($result_set as $arr) {
		$adminedit = $arr['adminedit'];
		//print_r ($arr);
		//echo $adminedit; 
		foreach($arr as $key => $value) {if ($key != 'adminedit')
		echo ('<div style="background:'.(($key==$s and $value)? 'green' : 'bisque').'; float:left; width:'.(($key==$id)? $wid : (($key==$s)? $w30 : $w230)).'">&nbsp;'.$value.''.(($arr['adminedit'] and $key=="text")?  '<i> (ред. админ.)</i>':'').'</div>');
		}

		echo	'<div style="background:bisque; clear:left; width:770px; height:0">&nbsp;</div>';

	}  

?>
  <!-- Дальше идёт вывод Pagination -->
  <div id="pagination">
    <span>Страницы: </span>
    <?php if ($active != 1) { ?>
		<a href="<?=$url?>?page=<?=1?>&sortlist=<?=$sortlist?>&ordby=<?=$ordby?>" title="Первая страница">&lt;&lt;&lt;</a> 
		<a href="<?=$url?>?page=<?=($active-1)?>&sortlist=<?=$sortlist?>&ordby=<?=$ordby?>" title="Предыдущая страница">&lt;</a>
    <?php } ?>
    <?php for ($i = $start; $i <= $end; $i++) { ?>
      <?php if ($i == $active) { ?><span><?=$i?></span><?php } else { ?><a href="<?php if ($i == 1) { ?><?=$url?>?page=<?=$i?>&sortlist=<?=$sortlist?>&ordby=<?=$ordby?><?php } else { ?><?=$url?>?page=<?=$i?>&sortlist=<?=$sortlist?>&ordby=<?=$ordby?><?php } ?>"><?=$i?></a><?php } ?>
    <?php } ?>
    <?php if ($active != $count_pages) { ?>
      <a href="<?=$url?>?page=<?=($active+1)?>&sortlist=<?=$sortlist?>&ordby=<?=$ordby?>" title="Следующая страница">&gt;</a>
      <a href="<?=$url?>?page=<?=$count_pages?>&sortlist=<?=$sortlist?>&ordby=<?=$ordby?>" title="Последняя страница">&gt;&gt;&gt;</a>
    <?php } ?>
  </div>
<?php } ?>
</div>



<form id="newz" name="myform" action="" method="post">
    <fieldset>
		<legend>Добавить новую задачу:</legend>
			<div>
				<input placeholder="Имя пользователя" type="text" name="username" value="<?=($_POST['username']?? false)?>" required />
				<input placeholder="E-mail" type="email" name="email"  value="<?=($_POST['email']?? false)?>" required />
			</div>
			<div>
				<textarea  placeholder="Текст задачи" type="text" name="txt" required /></textarea >
			</div>
			<div>
				<input type="submit" name="newz" value="Сохранить" />
		<!--		<input type="submit" name="sort" value="По дате" />-->
			</div>
	</fieldset>
</form>

<?php
    if (isset($array1)) {var_dump($array1);
    echo '<br />';
    function byfamily($a, $b) {
        echo $a['family'].'--'.$b['family'].'<br />';
        // if ($a[3] > $b[3]) return -2;
        // if ($a[3] <= 29 && $b[3] > 30) return 21;
        return ($a['family'] > $b['family']);
    }
    function bydate($a, $b) {
        echo $a['date'].'--'.$b['date'].'<br />';
        // if ($a[3] > $b[3]) return -2;
        // if ($a[3] <= 29 && $b[3] > 30) return 21;
        return ($a['date'] > $b['date']);
    }
    
    echo '<br />';
    if (isset($_POST['sort'])) if ($_POST['sort'] == 'По фамилии') uasort($array1, 'byfamily');
    if (isset($_POST['sort'])) if ($_POST['sort'] == 'По дате') uasort($array1, 'bydate');
    echo '<br />';
    print_r($array1);
    echo '<br />';
	print_r($err . "\n");
	//echo $mysqli->host_info . "\n";
    }
    if (isset($array1)) foreach ($array1 as $v1) {foreach ($v1 as $v2) echo $v2.' ';
    echo '<br />';
   }
/*    print_r ($_POST);
	$res = $mysqli->query($sql);
//	$sth->execute();
	$sql1 = $res->fetch_assoc();
    echo ' <br /> $sql - '; 
    print_r ($sql1);
	$sql1 = $res->fetch_assoc();
    echo ' <br /> $sql - '; 
    print_r ($sql1);
	$sql1 = $res->fetch_assoc();
    echo ' <br /> $sql - '; 
    print_r ($sql1);*/
    $result_set = $mysqli->query('SELECT COUNT(`id`) as `count` FROM `zadachi`');
//    var_dump ($result_set);
	foreach ($result_set as $arr) {
		print_r ($arr);
		foreach($arr as $key => $value) {
			print_r ($key);
			print_r ($value);
		}
	}
	//print_r ($_GET);
	$mysqli->close();
?>
</body>
</html>