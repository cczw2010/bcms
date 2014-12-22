<div class="info"><span class="xicon mr10">R</span> tips:sdk都做了一些修改，改为从数据库中获取配置信息</div>
<form action="<?php echo Uri::build('manage','pappcfgedit'); ?>">
<table class="tablebox formtable" border="0" cellpadding="10" cellspacing="1" width="80%" >
	<thead>
		<tr>
			<th colspan="2">第三方登陆配置</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td width="120">标题*：</td>
			<td><input type="text" size="50" name="name" value="<?=$oitem['name']; ?>"></td>
		</tr>
		<?php if ($oitem['key']=='qq'): ?>
			<tr>
				<td>appid*：</td>
				<td>
					<input type="text" size="50" name="appid" value="<?=$oitem['appid']; ?>">
				</td>
			</tr>
			<tr>
				<td>appkey*：</td>
				<td>
					<input type="text" name="appkey" size="50" value="<?=$oitem['appkey']; ?>">
				</td>
			</tr>
			<tr>
				<td>请求授权列表：</td>
				<td>
					<div>！获取用户信息接口是必须得：get_user_info</div>
					<?php
						$scopeArr = array("get_user_info","list_album","add_album","upload_pic","add_topic","add_weibo","check_page_fans","add_t","add_pic_t","del_t","get_repost_list","get_info","get_other_info","get_fanslist","get_idolist","add_idol","del_idol","get_tenpay_addr");
						$cfgscopes = empty($oitem['scope'])?array():explode(',', $oitem['scope']);
						foreach($scopeArr as $key=> $val){
					?>
						<input type="checkbox" <?php if (in_array($val, $cfgscopes)||$key==0): ?>
							checked='checked'
						<?php endif ?> <?php if ($key==0): ?>
							readonly="readonly"
						<?php endif ?> name="scope[]" value="<?=$val?>" id="scope_<?=$val?>"/><label for="scope_<?=$val?>"><?=$val?></label>&nbsp;
					<?php
						}
					?>
				</td>
			</tr>
		<?php elseif($oitem['key']=='weibo'):?>
			<tr>
				<td>App Key*：</td>
				<td>
					<input type="text" size="50" name="appid" value="<?=$oitem['appid']; ?>">
				</td>
			</tr>
			<tr>
				<td>App Sercet*：</td>
				<td>
					<input type="text" size="50" name="secret" value="<?=$oitem['secret']; ?>">
				</td>
			</tr>
			<tr>
				<td>请求授权列表：</td>
				<td>
					<?php
						$scopeArr = array('all'=>'请求下列所有scope权限',
								'email'=>'用户的联系邮箱',
								'direct_messages_write'=>'私信发送接口',
								'direct_messages_read'=>'私信读取接口',
								'invitation_write'=>'邀请发送接口',
								'friendships_groups_read'=>'好友分组读取接口组',
								'friendships_groups_write'=>'好友分组写入接口组',
								'statuses_to_me_read'=>'定向微博读取接口组',
								'follow_app_official_microblog'=>'关注应用官方微博');
						$cfgscopes = empty($oitem['scope'])?array():explode(',', $oitem['scope']);
						foreach($scopeArr as $key=> $val){
					?>
						<input type="checkbox" <?php if (in_array($key, $cfgscopes)): ?>
							checked='checked'
						<?php endif ?> name="scope[]" value="<?=$key?>" id="scope_<?=$key?>"/><label for="scope_<?=$key?>"><?=$val?></label>&nbsp;
					<?php
						}
					?>
				</td>
			</tr>
		<?php elseif($oitem['key']=='douban'):?>
			<tr>
				<td>API Key*：</td>
				<td>
					<input type="text" size="50" name="appid" value="<?=$oitem['appid']; ?>">
				</td>
			</tr>
			<tr>
				<td>Secret*：</td>
				<td>
					<input type="text" size="50" name="secret" value="<?=$oitem['secret']; ?>">
				</td>
			</tr>
			<tr>
				<td>请求授权列表：</td>
				<td>
					<div>！用户API是必须的：douban_basic_common</div>
					<?php
						$scopeArr = array('douban_basic_common'=>'用户API',
								'community_basic_photo'=>'相册API',
								'community_basic_note'=>'日记API',
								'book_basic_r'=>'图书API',
								'movie_basic_r'=>'电影API',
								'music_basic_r'=>'音乐API',
								'event_basic_r'=>'同城API',
								'community_basic_online'=>'线上活动基础API',
								'community_advanced_online'=>'线上活动高级API',
								'douban_common_basic'=>'论坛API',
								'travel_basic_r'=>'我去API');
						$cfgscopes = empty($oitem['scope'])?array():explode(',', $oitem['scope']);
						foreach($scopeArr as $key=> $val){
					?>
						<input type="checkbox" <?php if (in_array($key, $cfgscopes)||$key=='douban_basic_common'): ?>
							checked='checked'
						<?php endif ?> <?php if ($key=='douban_basic_common'): ?>
							readonly="readonly"
						<?php endif ?> name="scope[]" value="<?=$key?>" id="scope_<?=$key?>"/><label for="scope_<?=$key?>"><?=$val?></label>&nbsp;
					<?php
						}
					?>
				</td>
			</tr>
		<?php elseif($oitem['key']=='renren'):?>
			<tr>
				<td>APP ID*：</td>
				<td>
					<input type="text" size="50" name="appid" value="<?=$oitem['appid']; ?>">
				</td>
			</tr>
			<tr>
				<td>API KEY*：</td>
				<td>
					<input type="text" size="50" name="appkey" value="<?=$oitem['appkey']; ?>">
				</td>
			</tr>
			<tr>
				<td>Secret Key*：</td>
				<td>
					<input type="text" size="50" name="secret" value="<?=$oitem['secret']; ?>">
				</td>
			</tr>
		<?php endif ?>
		<tr>
			<td>回调地址*：</td>
			<td>
				<div>(请注意与网站的uritype相符合的url类型。默认地址："http://xxxx/user/appcallback")</div>
				<input type="text" size="50" name="callback" value="<?=$oitem['callback']; ?>">
			</td>
		</tr>
		<tr>
			<td>状态:</td>
			<td>
				启用<input class="mr20" type="radio" <?=$oitem['status']==1?'checked="checked"':'';?> name="status" value="1" >
				不启用<input type="radio" <?=$oitem['status']==0?'checked="checked"':'';?> name="status" value="0" >
			</td>
		</tr>
		<tr>
			<td colspan="2" class="ccenter">
				<input type="hidden" name="id" value="<?=$oitem['id'];?>">
				<input type="hidden" name="key" value="<?=$oitem['key'];?>">
				<input type="button" name="submitbtn" class="submitbtn" value="提 交">
			</td>
		</tr>
	</tbody>
</table>
</form>