var __finderhtml = '/datas/elfinder/elfinder.html';
		
// elFinderBrowser 提供的正对于tinymce的文件点击回调
// m 是否管理员，决定了目标用户的调用
function elFinderBrowser (field_name, url, type, win) {
	var setting = {
    file: __finderhtml,// use an absolute path!
    title: 'elfinder文件管理器',
    onlyMimes:['image'],
    width: 900,
    height: 500,
    resizable: 'yes'
  };
  tinymce.activeEditor.windowManager.open(setting, {
    setUrl: function (url) {
      win.document.getElementById(field_name).value = url;
    }
  });
  return false;
}
/**
 * 编辑器初始化
 * by awen
 * 初始化tinymce,ajax时请注意，如果id相同，可能造成内容缓存
 * @param string sel 选择器
 * @param string styles 将注入编辑器中的样式，很重要，这里可以确保编辑器中的展示和目标页面展示效果相同,多个用,隔开
 * @param m 是否管理员
 */
function initTinymce(sel,styles,m){
	if (m) {
		__finderhtml = '/datas/elfinder/melfinder.html';
	}
	tinymce.init({
	    selector: sel,
	    theme: "modern",
	    file_browser_callback : elFinderBrowser,
	    convert_urls: false,
	    content_css:styles||'',
	    language:'zh_CN',
	    plugins: [
	        "advlist autolink lists link image charmap print preview hr anchor pagebreak",
	        "searchreplace wordcount visualblocks visualchars code fullscreen",
	        "insertdatetime media nonbreaking save table contextmenu directionality",
	        "emoticons template paste textcolor"
	    ],
	    toolbar1: "insertfile undo redo | styleselect | fontname fontsize |bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image",
	    toolbar2: "print preview media | fontsizeselect forecolor backcolor emoticons",
	    fontsize_formats: '8px 10px 12px 14px 18px 24px 36px',
	    image_advtab: true,
	    templates: [
	        {title: 'Test template 1', content: 'Test 1'},
	        {title: 'Test template 2', content: 'Test 2'}
	    ],
	    // for filemanager
	    // external_filemanager_path:'/datas/tinymce/plugins/filemanager/'
	});
}
