<?php

/*
require_once "./inc/db.php";
require_once "./inc/function.php";
*/

//添付画像枚数
$imgMax = 2;

?>
<!DOCTYPE html>
<html lang="ja" dir="ltr">
<head>
<?php /* ★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★
ここにヘッドの内容を入れる
★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★ */ ?>

<link rel="stylesheet" href="./css/default.css" media="screen,print">
<link rel="stylesheet" href="./css/common.css" media="screen,print">
<link rel="stylesheet" href="./css/validationEngine.jquery.css">
<link rel="stylesheet" href="./css/drug.css" />
<link href="./plugin/jquery-ui-1.11.4.custom/jquery-ui.min.css" rel="stylesheet">

<script src="//ajax.googleapis.com/ajax/libs/jquery/1.12.0/jquery.min.js"></script>
<script src="./js/jquery.autoKana.js"></script>
<script src="./js/jquery.jpostal.js"></script>
<script src="./js/jquery.validationEngine.js"></script>
<script src="./js/jquery.validationEngine-ja.js"></script>
<script src="./plugin/jquery-ui-1.11.4.custom/jquery-ui.min.js"></script>
<script src="./js/mail_send.js"></script>
</head>



<body id="pTop">


<?php if ($_REQUEST['sendFlg'] == 1) { ?>

<p>お問い合わせありがとうございます。<br>
内容などにより回答に時間がかかる場合がございますので、予めご了承ください。</p>
<a href="./">HOMEへ戻る</a>

<?php } else { ?>

<script src="./js/drug.js"></script>
<script type="text/javascript">

$(function() {
<?php for ( $i = 0; $i < $imgMax; $i++ ) { ?>
	fileupFunc( 'fileBox<?=$i?>', 'inputFile<?=$i?>', 'receiveFile<?=$i?>'  ); // 引数:ドラッグエリア, inputのid, 結果を返すエリア
<?php } ?>
});

</script>
<p>FTPであがれー</p>
<form id="contactForm" method="POST" enctype="multipart/form-data">

<table class="tb tb2 contact_tb">

<tr>
<th><label for="name">お名前<em>必須</em></label></th>
<td><input type="text" name="name" value="" id="name" class="txtfiled validate[required]"></td>
</tr>

<tr>
<th><label for="kana">お名前　ふりがな<em>必須</em></label></th>
<td><input type="text" name="kana" value="" id="kana" class="txtfiled validate[required]"></td>
</tr>

<tr>
<th><label for="zip">ご住所<em>必須</em></label></th>
<td class="zip1">
〒<input type="text" name="zip1" maxlength="3" id="zip1" class="txtfiled validate[required,custom[integer]]"> - <input type="text" name="zip2" maxlength="4" id="zip2" class="txtfiled validate[required,custom[integer]]">
<em class="form_notice1">郵便番号を入力すると、自動で都道府県と市町村郡が表示されます。</em><br>

<label>都道府県<input type="text" name="pref" value="" id="pref" class="txtfiled validate[required]"></label><br>

<label>市区町村<input type="text" name="city" value="" id="city" class="txtfiled validate[required]"></label><br>

<label>番地<input type="text" name="add" value="" id="add" class="txtfiled validate[required]"></label>
</td>
</tr>

<tr>
<th><label for="tel">お電話番号<em>必須</em></label></th>
<td><input type="text" name="tel" value="" class="txtfiled validate[required,custom[phone]]"></td>
</tr>
<tr>
<th><label for="mail">メールアドレス<em>必須</em></label></th>
<td><input type="text" name="mail" value="" class="txtfiled validate[required,custom[email]]"></td>
</tr>

<tr>
<th>性別</th>
<td>
<label><input type="radio" name="sex" value="男" checked="checked">男</label>
<label><input type="radio" name="sex" value="女">女</label>
</td>
</tr>

<tr>
<th>カテゴリー</th>
<td>
<select name="cate">
<option value="カテゴリー1">カテゴリー1</option>
<option value="カテゴリー2">カテゴリー2</option>
<option value="カテゴリー3">カテゴリー3</option>
<option value="カテゴリー4">カテゴリー4</option>
<option value="カテゴリー5">カテゴリー5</option>
</select>
</td>
</tr>


<tr>
<th>お問合せ項目（複数可）</th>
<td>
<?php

//項目を配列化
$contents = array("項目1","項目2","項目3","項目4","項目5","項目6");

	foreach ( (array)$contents as $key => $val) {

?>
	<label><input type="checkbox" name="item[]" value="<?=$val?>"><?=$val?></label>
	<?php } ?>
</td>
</tr>


<?php for ( $i = 0; $i < $imgMax; $i++ ) { ?>
<tr>
<th>画像添付</th>
<td>
<div class="fileWrap cf">
<div id="fileBox<?=$i?>" class="dragArea">
<span>ファイルをここにドラッグ</span>
<input type="file" id="inputFile<?=$i?>" name="picture[]" class="dragInput">
</div>
<div id="receiveFile<?=$i?>"></div>
</div>
</td>
</tr>
<?php } ?>


<tr>
<th><label for="msg">お問い合わせ内容</label></th>
<td><textarea name="msg" class="txtfiled"></textarea></td>
</tr>

</table>
<input type="hidden" name="upflg" value="1">

<ul class="choose_list1 cf">
<li><input class="clearForm" type="reset" name="reset" value="リセットする"></li>
<li><input class="clearForm2" type="submit" name="send" value="入力内容の確認"></li>
</ul>

</form>

<?php } ?>

</body>
</html>
