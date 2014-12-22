<?php
	// form 辅助类库
	class SForm{
		/**
		 * 创建input
		 * @param  string $name   标签name
		 * @param  string $val    标签value
		 * @param  string $type   标签类型
		 * @param  array  $extarr 一些其他属性的键值对数组
		 * @return string   返回html字符串
		 */
		static function build_input($name,$val='',$type='text',$extarr=false){
			$html = '<input name="'.$name.'" value="'.$val.'" type="'.$type.'"';
			if (!empty($extarr)) {
				foreach ($extarr as $attrname => $attrval) {
					$html.=' '.$attrname.'="'.$attrval.'"';
				}
			}
			$html.='>';
			return $html;
		}

		/**
		 * 创建option下拉菜单,基于二维数组
		 * @param  array $items     下来列表的数据二维数组
		 * @param  string $valkey    数据中代表value的键名称
		 * @param  string $namekey   数据中代表显示的值的键名称
		 * @param  string $selectval 初始选中的值
		 * @return string            返回html字符串
		 */
		static function build_options($items,$valkey,$namekey,$selectval=''){
			$html = '';
			if (!empty($items)) {
				foreach ($items as $item) {
					$val = $item[$valkey];
					$html.='<option value="'.$val.'" '.(strcmp($val, $selectval)==0?'selected="selected"':'').'>'.$item[$namekey].'</option>';
				}
			}
			return $html;
		}
		/**
		 * 创建option下拉菜单，基于简单数组
		 * @param  array $items      下来列表的数据简单一维数组
		 * @param  string $selectval 初始选中的值
		 * @return string            返回html字符串
		 */
		static function build_options_simple($items,$selectval=''){
			$html = '';
			if (!empty($items)) {
				foreach ($items as $k=>$v) {
					$html.='<option value="'.$k.'" '.(strcmp($k, $selectval)==0?'selected="selected"':'').'>'.$v.'</option>';
				}
			}
			return $html;
		}
		/**
		 * 创建checkbox或者radio组，根据二维数组
		 * @param  array $items				二维数据数组
		 * @param  string $name				input的name
		 * @param  string $valkey			数据中代表value的键名
		 * @param  string $namekey		数据中代表该值名称的键名
		 * @param  string $selectval	初始选中的值
		 * @param  string $type				checkbox|radio
		 * @return string							返回html字符串
		 */
		static function build_checks($items,$name,$valkey,$namekey,$selectval='',$type='checkbox'){
			$html = '';
			if (!empty($items)) {
				foreach ($items as $item) {
					$val = $item[$valkey];
					$html.='<input type="'.$type.'" name="'.$name.'" value="'.$item[$valkey].'" '.(strcmp($val, $selectval)==0?'checked="checked"':'').'>'.$item[$namekey];
				}
			}
			return $html;
		}
		/**
		 * 创建checkbox或者radio组，根据一维数组
		 * @param  array $items				一维数据数组
		 * @param  string $name				input的name
		 * @param  string $valkey			数据中代表value的键名称
		 * @param  string $selectval	初始选中的值
		 * @param  string $type				checkbox|radio
		 * @return string							返回html字符串
		 */
		static function build_checks_simple($items,$name,$selectval='',$type='checkbox'){
			$html = '';
			if (!empty($items)) {
				foreach ($items as  $k =>$v) {
					$html.='<input type="'.$type.'" name="'.$name.'" value="'.$k.'" '.(strcmp($k, $selectval)==0?'checked="checked"':'').'>'.$v;
				}
			}
			return $html;
		}
	}