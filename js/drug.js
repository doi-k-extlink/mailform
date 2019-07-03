

function fileupFunc( filebox_id, inputFile_id, receiveFile_id ) {
	var	box = document.getElementById(filebox_id),
		elm = document.getElementById(receiveFile_id),
		input = document.getElementById(inputFile_id),
		ua = navigator.userAgent,
		ua_ie = false;

	// IEではinput type="file"にファイルをドロップできないのでinputを表示する
	if( ua.indexOf('MSIE') > -1 || ua.indexOf('Trident/7.0') > -1 ){
		ua_ie = true;
		elm.innerHTML = '<p>IEは10も11もinput type="file"にドロップできません！！！</p>';
		input.className = input.className + 'nodrop';
	}

	// ブラウザがドラッグイベントに対応しているかを確認
	if ('ondragover' in window && 'ondragleave' in window && 'ondrop' in window) {
		// ブラウザがFile APIに対応しているかを確認
		if (window.File && window.FileReader && window.FileList && window.Blob) {
			var	elm_className_base = elm.className;



			var file_select = function(evnt){
				var	files = evnt.target.files,
					img_num = 0;

				elm.className = elm_className_base;
				elm.innerHTML = '';

				for ( var i = 0; i < files.length; i++ ){
					var	indx = i;
						file = files[indx],
						reader = new FileReader();

					if( !file.type.match('image.*') && !file.type.match('application/pdf') && !file.type.match('text/xml')  && !file.type.match('application/vnd.ms-excel')){
						continue;
					}

					reader.onerror = function() {
						this.innerHTML = '<p>読み取り時にエラーが発生しました。</p>';
					};

					img_num += 1;
					
						
						reader.onload = (function(theFile){
							return function(e){
								var	item = document.createElement('div'),
									item_txt = '';

								item.className = 'item';
								item_txt = theFile.name;
								
								if (file.type.match('application/pdf') || file.type.match('text/xml') || file.type.match('application/vnd.ms-excel')) {
									item.innerHTML = '<div class="fileName">' + item_txt + '</div>';
								} else {
									item.innerHTML = '<div class="image"><div class="imageInner"><img src="' + e.target.result + '" title="' + item_txt + '" alt="' + item_txt + '"></div></div><div class="text">' + item_txt + '</div>';
								}
								
								elm.insertBefore(item, null);
							};
						})(file);

					reader.readAsDataURL(file);
				}
				if( img_num < 1 ){
					elm.innerHTML = '<p>画像ファイルが含まれていません。<br>画像ファイルをドロップしてください。</p>';
				}
			};

			if( ua_ie === false ){
				box.ondragover = function(evnt){
					evnt.stopPropagation();
					evnt.preventDefault();
					elm.className = elm_className_base + 'draggle';
				};

				box.ondragleave = function(evnt){
					evnt.stopPropagation();
					evnt.preventDefault();
					elm.className = elm_className_base;
				};
			}

			input.addEventListener('change', file_select, false);
		} else {
			elm.innerHTML = '<p>お使いのブラウザでは画像を表示できません。</p>';
			box.className = box.className + 'unsupported';
		}
	} else {
		elm.innerHTML = '<p>お使いのブラウザでは画像を表示できません。</p>';
		box.className = box.className + 'unsupported';
	}
}


