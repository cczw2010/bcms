/**
* 通用上传模块 by awen
* ---------------------------------
* 上传成功后会再表单中生成名为：
* uplodify_fpaths[] 的表单项，提交表单时可处理改表单名为实际上传的图片地址。
* 另外与和 uplodify_fpaths[]一一对应的有uplodify_fnames[]和 uplodify_ids[](新增时该项的值为空，如果addUpload时传入了json则为该项的id,代表着修改该项）
* ---------------------------------
* wraper  容器选择器，将在该容器下增加一个上传
* cfg 配置文件对象
*     params 用户自定义的上传参数
*     fieldid   id字段input的name
*     fieldpath path字段input的name
*     fieldname name字段input的name
*     uploadurl 处理上传图片的后端地址
*     buttonimg 按钮图片，如果不传则自动是一个透明像素占位
*     objtype 上传分组，代表本地上传的目的,后端可以根据该值做一些相应的操作
*     json 如果有数据的话将直接生成显示区域（json中应有id和path 两个键值）
*     hidedel 是否隐藏删除按钮
*     hidenew 是否隐藏新增按钮
*     showoname 是否显示原名称
*     fileexts 允许的文件后缀，多个用;隔开，默认图片,
*     replacepic 占位图片
* callback 上传成功后的回调方法，方便用户自定义一些展现，获得 当前上传dom和json返回值两个参数
*/
function addUpload(wraper,cfg,callback) {
    var domfile,upcfg,px1png,idx,queueid,upid,setting,bgpic,pparams,objtype,params,fieldid,fieldpath,fieldname,json,uploadurl,fileexts,picexts,getholderpic,clearupfile;
    // 参数处理
    picexts = '*.gif;*.jpg;*.png;*.jpeg';
    cfg = cfg?cfg:{};
    objtype = cfg.objtype?cfg.objtype:'default';
    params = cfg.params||false;
    fieldid = cfg.fieldid||'uplodify_ids';
    fieldpath = cfg.fieldpath||'uplodify_fpaths';
    fieldname = cfg.fieldname||'uplodify_fnames';
    px1png = '/static/dist/img/1opicity.png';
    json = cfg.json||false;
    uploadurl = cfg.uploadurl||'';
    fileexts = cfg.fileexts||picexts;
    getholderpic = function(json){
        // 整理显示的图片（如果有）
        var replacepic =  '',index,fext,
            fpath = json.fpath||'',
            fname = json.fname||'';
        if (json) {
            index =fname.lastIndexOf('.');
            if (index>=0) {
                fext = fname.slice(index).toLowerCase();
                if(picexts.indexOf(fext)==-1){  //是图片就显示
                    replacepic = cfg.replacepic||'';
                }else{
                    replacepic = fpath+'/'+fname;
                }
            }
        }
        return replacepic;
    };
    clearupfile = function(updom){
        updom.find('.uploadify_showpic').css('background-image','url()');
        updom.find('.uploadify_showname').html('');
        updom.find('.uplodify_ids').val('');
        updom.find('.uplodify_fpaths').val('');
        updom.find('.uplodify_fnames').val('');
    };
    // 全局变量
    if (!('UPLOADIFY_CNT' in window)) {
        // 一些初始化的东西
        window.UPLOADIFY_CNT = {};
        // 新增按钮
        $('.uploadify_add').live('click',function(){
            var data = $(this).parents('.uploadifyli').eq(0).data(),
                objtype = data.objtype,
                cfg = UPLOADIFY_CNT[objtype].cfg,
                callback = UPLOADIFY_CNT[objtype].callback;
            addUpload(data.wraper,cfg,callback);
        });
        // 删除按钮
        $('.uploadify_del').live('click',function(){
            var uploadifyli = $(this).parents('.uploadifyli').eq(0),
                fpath = uploadifyli.find('.uplodify_fpaths').val(),
                data = uploadifyli.data();
            // 如果是最后一个上传框，只清除数据
            if (UPLOADIFY_CNT[data.objtype].upidx>1) {
                // 有数据
                if (fpath.length===0 || confirm('确认删除吗？没保存前删除的上传项不影响原有数据。')) {
                    $('#'+data.upid).uploadify('destroy');
                    UPLOADIFY_CNT[data.objtype].upidx--;
                    uploadifyli.remove();
                }
            }else{
                clearupfile(uploadifyli);
            }
        });
    }
    // 为每组上传定义的计数器
    if (!(objtype in UPLOADIFY_CNT)) {
        UPLOADIFY_CNT[objtype] = {
            upidx : 0,          //上传次数
            isuploading :0,     //是否正在上传
            cfg:cfg,
            callback:callback
        };
    }
    upcfg = UPLOADIFY_CNT[objtype];
    idx = ++upcfg.upidx;
    upid = 'up_'+objtype+'_'+idx;
    queueid = upid + '_qid';

    var replacepic = getholderpic(json);
    // 构建上传区域html
    var uphtml = $('<div class="uploadifyli ccenter" data-upidx="'+idx+'" data-queueid="'+queueid+'" data-upid='+upid+' data-objtype="'+objtype+'" data-wraper="'+wraper+'" >'+
                    '<div class="">'+(!cfg.hidedel?'<span class="uploadify_del">删除</span>':'')+
                    (!cfg.hidenew?'<span class="uploadify_add">新增</span>':'')+
                    '</div>'+
                    '<div class="uploadify_showpic" style="background-image:url('+replacepic+');"></div>'+
                    (cfg.showoname?'<div class="uploadify_showname breakword">'+(json?json.oname:'')+'</div>':'')+
                    '<input type="hidden" class="uplodify_ids" name="'+fieldid+'[]" value="'+(json?json.id:0)+'" >'+
                    '<input type="hidden" class="uplodify_fpaths" name="'+fieldpath+'[]" value="'+(json?json.fpath:'')+'">'+
                    '<input type="hidden" class="uplodify_fnames" name="'+fieldname+'[]" value="'+(json?json.fname:'')+'">'+
                '</div>').appendTo(wraper); 
    domfile = $('<input class="uploadify_step" type="file" id="' + upid + '" value="">').appendTo(uphtml);
    // 初始化上传插件
    pparams = params||{};
    //用户自定义的一些参数,将每个字段的名字发给后台
    pparams._fieldname = 'Filedata';    //uploadify上传区域的文件名
    pparams.objtype = objtype;
    setting = {
        'auto': true,
        'removeCompleted': true,
        'removeTimeout': 60,
        'queueID': queueid,  //省得缺省的进度区域一直显示，给个值，后期可以自己实现
        'queueSizeLimit': 1,
        // 'buttonImage':'',
        'fileTypeDesc': '文件',
        'fileTypeExts': fileexts,
        'buttonText': '选择文件',
        'swf': '/datas/uploadify/uploadify.swf',
        'uploader': uploadurl,
        'width': 120,
        'method': 'post',
        'debug'    : false,
        'multi': true,
        'formData' : pparams,   
        'fileSizeLimit': '6MB',
        'prevent_swf_caching':true, //不为true chrome会频繁崩溃
        'button_image_url':cfg.buttonimg||px1png,
        // 如果不设置button_image_url 或者设置为空，
        // 则uplodify会自动请求以此当前地址，浪费资源，
        // 且当前页特复杂的话可能造成网页崩溃,这里用一个透明像素站位，后期配置文件自己可传
        'overrideEvents' : ['onUploadProgress','onSelectError'],
        'onUploadProgress' : function(file,bytesUploaded,bytesTotal){
            // console.log('onUploadProgress',arguments);
            if (bytesUploaded<bytesTotal) {
                var pre = (bytesUploaded/bytesTotal)*100;
                pre = pre.toFixed(2);
                this.wrapper.uploadify('settings','buttonText',pre+'%');
            }else{
                this.wrapper.uploadify('settings','buttonText','loading...');
            }
        },
        // 'onSelect':function(){
        //     console.log('select');
        // },
        'onUploadStart': function() {
            upcfg.isuploading++;
        },
        'onUploadComplete': function() {
            upcfg.isuploading--;
        },
        'onUploadSuccess': function(file,ret) {
            // console.log('onUploadSuccess',arguments,this);
            var json =$.parseJSON(ret),
                pdata = json.data,
                uploadifyli = this.wrapper.parent();
            if (json.code>0) {
                var replacepic = getholderpic(pdata);
                uploadifyli.find('.uploadify_showpic').css('background-image','url('+replacepic+')');
                uploadifyli.find('.uploadify_showname').html(pdata.oname);
                uploadifyli.find('.uplodify_fpaths').val(pdata.fpath);
                uploadifyli.find('.uplodify_fnames').val(pdata.fname);
            }
            this.wrapper.uploadify('settings','buttonText','选择文件');
            // 删除队列中的fileid,防止选择相同文件的时候提示已经在队列中，考虑后期自己缓存
            delete this.queueData.files[file.id];
            // 执行回调
            if (callback) {
                callback(uploadifyli,json);
            }
        },
        'onUploadError': function(file) {
           alert('对不起，"'+file.name+'"上传失败，请重试');
        }
    };
    domfile.uploadify(setting);
}