/**
 * 刷新验证码
 * @param img 图片对象或者图片selector
 */
;

function flushCaptch(img) {
    var o = $(img),
        d = new Date() - 0,
        osrc = o.data('osrc');
    $(img).attr('src', osrc + '?t=' + d);
}
/**
 * 友好日期格式化
 * @param  int times  时间戳毫秒
 * @param  string format 格式化字符串,例如（yyyy-mm-dd）
 * @return string
 */
function formatDate(times, format) {
    var t = new Date(d),
        o = {
            "M+": t.getMonth() + 1, //month
            "d+": t.getDate(), //day
            "h+": t.getHours(), //hour
            "m+": t.getMinutes(), //minute
            "s+": t.getSeconds(), //second
            "q+": Math.floor((t.getMonth() + 3) / 3), //quarter
            "S": t.getMilliseconds() //millisecond
        };
    if (/(y+)/.test(format)) {
        format = format.replace(RegExp.$1, (t.getFullYear() + "").substr(4 - RegExp.$1.length));
    }
    if (new RegExp("(" + k + ")").test(format)) {
        format = format.replace(RegExp.$1, RegExp.$1.length == 1 ? o[k] : ("00" + o[k]).substr(("" + o[k]).length));
    }

    return format;
}

var __finderhtml = '/datas/elfinder/elfinder.html';

// elFinderBrowser 提供的正对于tinymce的文件点击回调
// m 是否管理员，决定了目标用户的调用
function elFinderBrowser(field_name, url, type, win) {
    var setting = {
        file: __finderhtml, // use an absolute path!
        title: 'elfinder文件管理器',
        onlyMimes: ['image'],
        width: 900,
        height: 500,
        resizable: 'yes'
    };
    tinymce.activeEditor.windowManager.open(setting, {
        setUrl: function(url) {
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
function initTinymce(sel, styles, m) {
    if (m) {
        __finderhtml = '/datas/elfinder/melfinder.html';
    }
    tinymce.init({
        selector: sel,
        theme: "modern",
        file_browser_callback: elFinderBrowser,
        convert_urls: false,
        content_css: styles || '',
        language: 'zh_CN',
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
        templates: [{
                title: 'Test template 1',
                content: 'Test 1'
            },
            {
                title: 'Test template 2',
                content: 'Test 2'
            }
        ],
        // for filemanager
        // external_filemanager_path:'/datas/tinymce/plugins/filemanager/'
    });
}
(function(O) {
    /**
     * 基于uploadifive的上传控件   SUplodiFive
     * setting uploadifive的设置属性，事件除外,下面是主要的几个,其它查看代码中的默认选项
     * 		{
     *			'queueID':'queue',//必填	queueID选项允许你设置一个拥有唯一ID的DOM元素来作为显示上传队列的容器
     *			'uploadScript':'',//必填 	服务器处理脚本的路径
     *			'fileObjName':'file',//必填		定义上传数据处理文件中接收数据使用的文件对象名
     *			'formData': {},//必填 定义在文件上传时需要一同提交的其他数据对象
     *
     *          'queueSizeLimit':3, //定义队列中最大的数量， 建议使用这个来限制当前图片数量，因为prepare方法也会纳入计算
     *			'uploadLimit':10,//定义允许的最大上传数量,
     *			'multi':true,//设置值为false时，一次只能选中一个文件,
     * 		}
     * 
     * params 封装自定义的一些参数设定
     *  {
     *    callback 上传完成或者失败时的回调，参数：(error,file,data)  error时表示错误
     *    btnWraper 指定上传btn的位置selector，不指定则与上传文件显示区域在一起,
     *    canSort 是否允许排序
     *    hasDesc 是否显示说明框
     *    hasLink 是否显示链接框
     *  }
     */
    // 错误信息中文化
    var Errors = {
            'UPLOAD_LIMIT_EXCEEDED': '到达文件上传上限',
            'QUEUE_LIMIT_EXCEEDED': '当前队列已经达到到上限，请删除不必要的图片再试',
            '404_FILE_NOT_FOUND': '文件不存在',
            '403_FORBIDDEN': '文件禁止访问',
            'FORBIDDEN_FILE_TYPE': '非允许的文件类型',
            'FILE_SIZE_LIMIT_EXCEEDED': '文件超过上线',
            'defualt': '未知错误',
        },
        getErrorMsg = function(err) {
            return (err && (err in Errors)) ? Errors[err] : Errors.defualt;
        };
    // 默认数据上传域参数
    var uplodify_ids = 'uplodify_ids[]', //图片id
        uplodify_fpaths = 'uplodify_fpaths[]', //图片路径
        uplodify_fnames = 'uplodify_fnames[]', //图片名称
        uplodify_links = 'uplodify_links[]', //图片名称
        uplodify_descs = 'uplodify_descs[]'; //图片名称
    function SUplodiFive(setting, params) {
        // 包裹的容器
        this.wraper = $('#' + setting.queueID);

        params = params || {};
        callback = params.callback || false;
        this.hasDesc = params.hasDesc || false;
        this.canSort = params.canSort || false;
        this.hasLink = params.hasLink || false;

        // 默认参数
        var defSetting = {
            'auto': true, //设置auto为true，当文件被添加至上传队列时，将会自动上传
            // 'buttonClass':'.btn',//为上传按钮添加类名
            'buttonText': '选择图片', //定义显示在默认按钮上的文本
            'dnd': false, //如果设置为 false ， 拖放功能将不被启用
            'fileObjName': 'file', //定义上传数据处理文件中接收数据使用的文件对象名
            'fileSizeLimit': 1024 * 1024 * 10, //上传文件大小限制,单位KB
            'fileType': 'image/*', //允许上传的文件类型
            //'checkScript': 'check-exists.php',//定义检查目标文件夹中是否存在同名文件的脚本文件路径
            //'formData': {},//定义在文件上传时需要一同提交的其他数据对象
            'height': 40, //上传按钮的高度（单位：像素）
            'width': 100, //上传按钮的宽度（单位：像素）
            //指定上传队列的HTML
            'itemTemplate': this.getTemplateHtml(),
            'method': 'post', //上传文件的提交方法，取值‘post‘或‘get‘。
            'multi': true, //设置值为false时，一次只能选中一个文件
            //'overrideEvents':['onProgress'],//该项定义了一组默认脚本中你不想执行的事件名称
            'queueSizeLimit': 12, //上传队列中一次可容纳的最大条数
            'removeCompleted': false, //不设置该选项或者将其设置为false，将使上传队列中的项目始终显示于队列中，直到点击了关闭按钮或者队列被清空
            'simUploadLimit': 2, //所述多个文件同时上传 ， 可以在任何给定时间
            // 'truncateLength':10,//截断文件名字符
            'uploadLimit': 20, //定义允许的最大上传数量
            'queueID': '', //queueID选项允许你设置一个拥有唯一ID的DOM元素来作为显示上传队列的容器
            'uploadScript': '', //服务器处理脚本的路径
            "overrideEvents": ['onError'],
            // 上传进度
            'onProgress': function(file, e) {
                // console.log('onProgress',file, e.loaded);
                if (e.lengthComputable) {
                    var percent = Math.round((e.loaded / e.total) * 100);
                    file.queueItem.find('.fileinfo').html(percent + '%');
                    file.queueItem.find('.progress-bar').css('width', percent + '%');
                } else {
                    file.queueItem.find('.fileinfo').html('uploading');
                }
            },
            // 当上传完成时
            'onUploadComplete': function(file, data) {
                console.log('onUploadComplete', data);
                data = JSON.parse(data);
                // 业务逻辑
                if (data.code <= 0) {
                    file.queueItem.addClass('error').find('.fileinfo').html(data.msg);
                    callback(getErrorMsg(data.msg), file);
                    return false;
                }
                data = data.data;
                // 显示图片，注入字段  为页面上的form提供数据
                file.queueItem.find('.filecover').css('background-image', 'url(' + data.fpath + '/' + data.fname + ')');
                // file.queueItem.find('.fileinfo').html();
                file.queueItem.find('.uplodify_ids').val('0');
                file.queueItem.find('.uplodify_fpaths').val(data.fpath);
                file.queueItem.find('.uplodify_fnames').val(data.fname);
                callback(null, file, data);
            },
            // 当取消的时候
            'onCancel': function(file) {
                /* 注意：取消后应重新设置uploadLimit */
                uploadifive = $(this).data('uploadifive');
                uploadifive.settings.uploadLimit++;
                console.log('onCancel', file);
            },
            // 初始化失败时
            'onFallback': function() {
                console.log("该浏览器无法使用!");
            },
            //TODO 当队列开始上传文件时，可以做一些操作,比如禁止提交表单
            'onUpload': function(file) {
                console.log('onUpload', file);
            },
            // TODO 当每个文件开始上传时
            'onUploadFile': function(file) {
                console.log('The file ' + file.name + ' is being uploaded.');
            },
            // 错误
            'onError': function(errorType, file) {
                console.log('The error was: ' + errorType);
                callback(getErrorMsg(errorType), file);
            },
            // 实例销毁
            'onDestroy': function() {
                console.log('Oh noes!  you destroyed UploadiFive!');
            }
        };
        // 合并参数
        this.setting = $.extend(defSetting, setting);
        // 生成一个input来做上传控件
        this.uploadDom = params.btnWraper ? $(params.btnWraper) : $('<input type="file" name="uplodifive_file_upload" id="uplodifive_file_upload"  style=""/>').appendTo(this.wraper);
        this.uploadDom.uploadifive(this.setting);

        // 排序
        if (this.canSort) {
            this.wraper.on('click', '.uploadifive-sort span', function() {
                var item = $(this).parents('.uploadifive-queue-item'),
                    wraper = item.parent(),
                    index = item.index();
                if (this.dataset.dir == 'left') {
                    if (index > 0) {
                        item.insertBefore(item.prev('.uploadifive-queue-item'));
                    }
                } else {
                    var next = item.next('.uploadifive-queue-item');
                    if (next.length == 1) {
                        item.insertAfter(next);
                    }
                }
                console.log(this.dataset.dir, item);
            });
        }

    }

    // 获取模板HTML - 也可以注入数据
    SUplodiFive.prototype.getTemplateHtml = function(obj) {
        return '<div class="uploadifive-queue-item ' + (obj ? 'complete preparepic' : '') + '">' +
            // 隐藏数据域  根据自己的业务编写
            '<input type="hidden" class="uplodify_ids" name="' + uplodify_ids + '" value="' + (obj ? obj.id : '') + '" >' +
            '<input type="hidden" class="uplodify_fpaths" name="' + uplodify_fpaths + '" value="' + (obj ? obj.fpath : '') + '" >' +
            '<input type="hidden" class="uplodify_fnames" name="' + uplodify_fnames + '" value="' + (obj ? obj.fname : '') + '" >' +
            // 上传展示区域
            '<div class="filecover" style="background-image:url(' + (obj ? (obj.fpath + '/' + obj.fname) : '') + ')"></div>' +
            (obj ? '' : '<div class="progress"><div class="progress-bar"></div></div>') +
            '<div class="filename">' + (obj ? obj.fname : '') + '</div>' +
            '<div class="fileinfo">' + (obj ? '已有图片' : '') + '</div>' +
            '<textarea ' + (this.hasDesc ? '' : 'style="display:none"') + ' class="uplodify_descs" rows="2" name="' + uplodify_descs + '" placeholder="请输入描述">' + (obj && obj.desc ? obj.desc : '') + '</textarea>' +
            '<input ' + (this.hasLink ? '' : 'type="hidden"') + ' class="uplodify_links" name="' + uplodify_links + '" placeholder="请输入链接地址" value="' + (obj && obj.links ? obj.links : '') + '">' +
            (this.canSort ? '<div class="uploadifive-sort"><span class="uploadifive-sort-left" data-dir="left">←</span><span class="uploadifive-sort-right" data-dir="right">→</span></div>' : '') +
            '<div class="close ' + (obj ? 'uploadifive_prepare_close' : '') + '"></div>';
    }

    // 获取uploadifive实例
    SUplodiFive.prototype.getUploadifive = function() {
        return $(this.uploadDom).data('uploadifive')
    };
    /**
     *  按照上传区域的样式 初始化已经存在的图片，数据格式同上传返回的结果
     *  注意也会计入上传数量限制中
     *  json array  [{id,fpath,fname,...}]
     **/
    SUplodiFive.prototype.prepare = function(json) {
        var closeFunc = function(e) {
            if (confirm('确认删除原有图片吗？')) {
                $(e.target).parent().remove();
                this.getUploadifive().queue.count--;
                this.getUploadifive().queue.queued--;
            }
        }.bind(this);
        for (var k in json) {
            this.getUploadifive().queue.count++;
            this.getUploadifive().queue.queued++;
            var item = $(this.getTemplateHtml(json[k]));
            item.find('.uploadifive_prepare_close').on('click', closeFunc);
            this.wraper.append(item);
        }
    };

    O.SUplodiFive = SUplodiFive;
})(this);