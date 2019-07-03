<?php


// POST送信時のチェック
function post_size_check() {
	//$post_max_size    = ini_get('post_max_size') * pow(1024, 2); // POSTデータに許可される最大サイズ
	$post_max_size    = ini_get('upload_max_filesize') * pow(1024, 2); // アップロードされるファイルの最大サイズ
	$post_upload_size = $_SERVER["CONTENT_LENGTH"];

	if ($post_max_size <= $post_upload_size) {
		header ("Refresh: 2; URL={$_SERVER['HTTP_REFERER']}");
		exit('sizeOver');
	}
}


/**
 * 画像アップロード関数
 * @ $_temp: 一時保存ファイル
 * @ $_uploadDir アップロードディレクトリ
 * @ $_fileName 出力ファイル名
 */
function uploadFile($_temp, $uploadDir, $_fileName, $_file_type, $_key ) {

		
		if (!is_dir($_fileName)) {
			exec("/bin/mkdir -m 777 $uploadDir");
		}

		//アップロード処理
		move_uploaded_file($_temp[$_key], $_fileName );
		system ("chmod 777 {$_fileName}");

		// 画像加工
		if ($_file_type == 'pdf' || $_file_type == 'csv' || $_file_type == 'xml') {

			$img_array = array(
				"file_type"  => $_FILES['picture']['type'][$_key],
				"file_path"  => $_fileName,
				"img_path"   => $_fileName,
				"file_name"  => basename( $_fileName )
			);

		} else {

			$img_array = array(
				"file_type"  => $_FILES['picture']['type'][$_key],
				"file_path"  => $_fileName,
				"img_path"   => $_fileName,
				"file_name"  => basename( $_fileName )
			);

		}//if (file_type)

	return $img_array;

}




