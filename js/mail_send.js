//送信先URL
var url_  = 'contact_send.php';

//フォームid名
var fName = '#contactForm';

//フォーム項目名
/*
 input[name] =　項目名
 nameが配列の場合 = name[]
 複数のフォームを繋げる場合 = {項目名 : [name1, name2]}
 */
var fInput = {
    "name"   : "お名前",
    "kana"   : "フリガナ",
    "zip"    : {"郵便番号" : ["zip1","zip2"]},
    "cate"   : "都道府県",
    "city"   : "市区町村",
    "add"    : "番地",
    "tel"    : "電話番号",
    "mail"   : "メールアドレス",
    "sex"    : "性別",
    "cate"   : "カテゴリー",
    "item[]" : "お問い合わせ項目",
    "msg"    : "お問い合わせ内容",
};


$(function() {

	$("body").append('<div id="dialog" style="display: none;"></div>');

	//$.fn.autoKana('#name',  '#kana', { katakana : true }); // フリガナ変換
	$.fn.autoKana('#name',  '#kana', { katakana : false }); // ふりがな変換


	// 郵便番号
	$('#zip1').jpostal({
		postcode : [
			'#zip1', //郵便番号上3ケタ
			'#zip2'  //郵便番号下4ケタ
		],
		address : {
			'#pref'  : '%3', //都道府県
			'#city'  : '%4%5' //市区町村 町域
		}
	});

	// バリデーションチェック
	/*
	validate[required] : requiredを入れる事により入力チェック
	custom[phone] : ハイフンを含む20文字以下の数値か判定
	custom[email] : ＠マークを含むメールアドレス形式
	custom[url] : httpを含むURL
	custom[integer] : 整数半角のみ
	custom[onlyNumberSp] : 半角数字のみ
	custom[onlyLetterSp] : 半角アルファベットのみ
	custom[onlyLetterNumber] : 半角英数のみ
	*/

	$(fName).validationEngine('attach', {
		ajaxFormValidation         : true,
		onBeforeAjaxFormValidation : submitForm
	});

	// 送信チェック
	function submitForm() {
        $.post( 'https://httpbin.org/post', $(fName).serialize() ).done(function( data ) {
            var dataCatch    = data.form,
                dataValue    = [],
                dataConfirm  = "",
                dataNull     = "未記入";
            Object.keys(dataCatch).forEach(function(key){
                dataValue[key] = dataCatch[key];
            });
            for(var parentKey in fInput){
                if( $.isPlainObject(fInput[parentKey])  && dataValue[parentKey] != "" ) { //複数のinputを連結させる場合
                    for(var childrenKey in fInput[parentKey]){
                        var grandchildValue = "";
                        for(var grandchildKey in fInput[parentKey][childrenKey]){
                            dataValue[fInput[parentKey][childrenKey][grandchildKey]] = dataValue[fInput[parentKey][childrenKey][grandchildKey]] != ""　? dataValue[fInput[parentKey][childrenKey][grandchildKey]] : dataNull;
                            grandchildValue += dataValue[fInput[parentKey][childrenKey][grandchildKey]]+" ";
                        }
                        dataConfirm += "【"+childrenKey+"】"+grandchildValue+"<br>\n";
                    }
                }
                else {
                    if(Array.isArray(dataValue[parentKey]) == true) { //要素が配列の場合（チェックボックス用）
                        dataValueJoin = dataValue[parentKey].length > 0 ? dataValue[parentKey].join('<br>') : dataNull;
                        dataConfirm += "【"+fInput[parentKey]+"】<br>"+dataValueJoin+"<br>\n";
                    }
                    else {  //通常のinputフォーム
                        if( dataValue[parentKey] != "" && dataValue[parentKey] === void 0) {
                            dataConfirm += "【"+fInput[parentKey]+"】"+dataNull+"<br>\n";
                        }
                        else if(dataValue[parentKey] == "") {
                            dataConfirm += "【"+fInput[parentKey]+"】"+dataNull+"<br>\n";
                        }
                        else {
                            dataConfirm += "【"+fInput[parentKey]+"】"+dataValue[parentKey]+"<br>\n";
                        }
                    }
                }
            }

    	    $("#dialog").html('下記入力内容を確認し、間違いがなければ送信ボタンを押してください。<br>'+dataConfirm);
    		    $("#dialog").dialog({
    	        resizable: false,
    	        draggable: false,
    	        closeOnEscape: false,
    	        open: function(event, ui) {
    	            $(".ui-dialog-titlebar-close").hide();
    	        },
    	        modal: true,
    	        title: '確認',
    	        width: 400,
    	        height: 400,
    	        buttons: {
    	            'OK': function() {
    	                submitData();
    	            },
    	            '閉じる': function() {
    	                $(this).dialog('close');
    	            }
    	        }
    	    });
        });
	}

	// 送信データ処理
	function submitData(){
		var f = $(fName);
		var method_ = f.prop('method');
		var formdata = new FormData( f.get(0) );


		// POSTでアップロード
		$.ajax({
			url    : url_,
			method : method_,
			type   : 'POST',
			data : formdata,
			cache       : false,
			contentType : false,
			processData : false,
			success: function(data) {
				console.log(data);
				$("#dialog").dialog({
			        buttons: {}
			    });

			    if (data == "sizeOver") {
					$("#dialog").html('添付ファイルサイズが大きすぎます。');
					    $("#dialog").dialog({
				        resizable: false,
				        draggable: false,
				        closeOnEscape: false,
				        open: function(event, ui) {
				            $(".ui-dialog-titlebar-close").hide();
				        },
				        modal: true,
				        title: 'エラー',
				        width: 400,
				        height: 400,
				        buttons: {
				            '閉じる': function() {
				                $(this).dialog('close');
				            }
				        }
				    });
	            } else {
					$("#dialog").html("送信完了しました。");
					setTimeout(function() {
	                compPage('contact.php');
		            }, 1000);
				}
			}
		}).fail(function(data) {
			alert('送信失敗しました！');
		});
	}


	// 完了後ページ
	function compPage(url) {
		location.href = url + '?sendFlg=1';
	}

});
