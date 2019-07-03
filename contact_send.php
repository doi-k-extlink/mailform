<?php

require_once "./functions.php";


//POSTファイル容量チェック
post_size_check();


if ($_POST['upflg'] == 1) {


	// POSTデータのSQLの為のエスケープ処理
	foreach ( (array)$_POST as $key => $val ) {

		if ( !is_array($val) ) {
			$_POST["{$key}"] = htmlentities(pg_escape_string($val), ENT_QUOTES, "UTF-8");
		}
	}


	//チェックボックス
	$item = "";

	foreach ( (array)$_POST['item'] as $key => $val ) {
		$item .= $val."  ";
	}


/*************************************************
//ファイルアップロード関連
**************************************************/


	foreach ( (array)$_FILES['picture']['tmp_name'] as $key => $val ) {
		
		if ($val) {
		
		
		//拡張子取得
		$file_type = pathinfo($_FILES['picture']['name'][$key], PATHINFO_EXTENSION);

		//フォルダ名
		$uploadfolder = "./img/tmp";
		
		//ファイル名
		$fileName = "{$uploadfolder}/TMP_FILE_{$key}.{$file_type}";
		
		$img_array[] = uploadFile($_FILES['picture']['tmp_name'], $uploadfolder, $fileName, $file_type, $key );
		
		}
	}



/*************************************************
//ファイルアップロード関連ここまで
**************************************************/


	// ホームページ運営者に返信するメール
	mb_language("Japanese");
	mb_internal_encoding("UTF-8");
	$today = date("Y/m/d H:i:s");

	/* 案件によって調整
	------------------------------------------------------------ */

	/* 件名、本文冒頭 */
	$mail_title = "■■■";
	//$mail_title = $passmaster_array['hptitle'];

	/* クライアントメールアドレス */
	$mailto   = "koushin@extlink.co.jp";
	//$mailto = $infomail;
	
	/* エンドユーザー返信メール末尾の案件アドレス */
	$site_url_mail = "http://site-one.net/esite/●●●/";
	//$site_url_mail = $site_url;

	/* "メインの送信先アドレスをExtlink発行のメールアドレスにしない場合、
		BCC枠に「info@{各ドメインアドレス　例：info@extlink.co.jp}」を入れてください。
		メインアドレスがExtlink発行のメールの場合は別途BCC用のメールアドレスを
		発行し、そちらを設定してください */
	$header_bcc = "system@extlink.co.jp";
	
	/* --------------------------------------------------------- */


	$subject = "{$mail_title}ホームページの『お問い合わせ』より送信";

	$message = <<< maildata
{$mail_title}ホームページの『お問い合わせ』よりメールがありました。

送信日時：{$today}

＜送信内容＞

お名前：{$_POST['name']}
お名前　ふりがな：{$_POST['kana']}

ご住所：〒{$_POST['zip']} {$_POST['pref']} {$_POST['city']} {$_POST['add']}
お電話番号：{$_POST['tel']}
メールアドレス：{$_POST['mail']}

性別：{$_POST['sex']}
お問合せ項目：{$item}

お問い合わせ内容：{$_POST['msg']}

maildata;

	$fromName = mb_encode_mimeheader($mail_title);
	$header    = "From:{$fromName} <{$mailto}>" .PHP_EOL;


// 添付ファイルを付ける場合↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓

if ( count( $img_array) ) {

	$contentType = ( count( $img_array) ) ? 'multipart/mixed; boundary="simple boundary"': 'text/plain; charset="ISO-2022-JP"';
	$header = "";
	
	// ヘッダー情報
	$header = <<< headerdata
X-Mailer: PHP5
From:{$fromName } <{$mailto}>
Bcc:{$header_bcc}
MIME-Version: 1.0
Content-Type: {$contentType}
Content-Transfer-Encoding: 7bit
headerdata;

		
    // 添付ファイル付きメッセージ
    $message_temp .= <<< message_tempdata
--simple boundary
Content-Type: text/plain; charset="ISO-2022-JP"
Content-Transfer-Encoding: 7bit

{$message}

message_tempdata;
		
		foreach ( (array)$img_array as $key => $val ) {

	    // 添付するファイル
	    $file     = $img_array[$key]['file_name'];
	    $fileName = $img_array[$key]['img_path'];


	    // 添付ファイル処理
	    $handle = fopen($fileName, 'r');
	    $content = fread($handle, filesize($fileName));
	    fclose($handle);
	    $fencoded = chunk_split(base64_encode($content));


	    // 添付ファイル付きメッセージ
	    $message_temp .= <<< message_tempdata
--simple boundary
Content-Type: {$img_array[$key]['file_type']}; name="{$file}"
Content-Disposition: attachment; filename="{$file}"
Content-Transfer-Encoding: base64

{$fencoded}

message_tempdata;
	    
	    }
	    
	$message_temp .= "--simple boundary--";
    $message = $message_temp;
	
}




// 添付ファイルを付ける場合↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑

	//メール送信
	mb_send_mail($mailto, $subject, $message, $header);

	// ユーザー宛
	$header = "From:".mb_encode_mimeheader($mail_title)."<".$mailto.">\r\n";
	$mailto_guest = $_POST['mail'];
	$subject = "【{$mail_title}】お問い合わせフォーム確認メール";
	$message = <<<_message
『{$mail_title}』ホームページ より、
お問い合わせ頂きまして、誠にありがとうございます。
お問い合わせ内容については、後日こちらよりご連絡をさせて頂きます。

このメールは、メールサーバより自動送信しています。

送信日時：{$today}

----------------------------------------------------------------------

＜お問い合わせ内容＞

お名前：{$_POST['name']}
お名前　ふりがな：{$_POST['kana']}

ご住所：〒{$_POST['zip']} {$_POST['pref']} {$_POST['city']} {$_POST['add']}
お電話番号：{$_POST['tel']}
メールアドレス：{$_POST['mail']}

性別：{$_POST['sex']}
お問合せ項目：{$item}

お問い合わせ内容：{$_POST['msg']}

----------------------------------------------------------------------

{$mail_title}ホームページの『お問い合わせ』より送信
URL：{$site_url_mail}

_message;


	//メール送信
	mb_send_mail($mailto_guest,$subject,$message,$header);

} else {

	header("Location: ./contact.php");
	exit;

}