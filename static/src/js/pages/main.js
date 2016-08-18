/**
 * 刷新验证码
 * @param img 图片对象或者图片selector
 */
;function flushCaptch(img){
	var o=$(img),
		d = new Date()-0,
		osrc = o.data('osrc');
	$(img).attr('src',osrc+'?t='+d);
}
/**
 * 友好日期格式化
 * @param  int times  时间戳毫秒
 * @param  string format 格式化字符串,例如（yyyy-mm-dd）
 * @return string
 */
function formatDate(times,format){
	var t = new Date(d),
		o = {
			"M+" : t.getMonth()+1, //month
			"d+" : t.getDate(), //day
			"h+" : t.getHours(), //hour
			"m+" : t.getMinutes(), //minute
			"s+" : t.getSeconds(), //second
			"q+" : Math.floor((t.getMonth()+3)/3), //quarter
			"S" : t.getMilliseconds() //millisecond
		};
	if(/(y+)/.test(format)){
		format=format.replace(RegExp.$1,(t.getFullYear()+"").substr(4- RegExp.$1.length));
	}
	if(new RegExp("("+ k +")").test(format)){
		format = format.replace(RegExp.$1,RegExp.$1.length==1? o[k] :("00"+ o[k]).substr((""+ o[k]).length));
	}
	
	return format;
}

