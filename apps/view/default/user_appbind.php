<?php
include('wwwheader.php');
?>
<div class="wraper">
    <div class="userbox clearfix">
        <h3 class="ctitle m20">绑定已有账号</h3>
        <hr>
        <form action="<?php echo Uri::build('user','appbind'); ?>" method="post">
            <div class="userli">
                <label>用户名*:</label>
                <input name="username" type="text" placeholder="请输入用户名">
            </div>
            <div class="userli">
                <label>密 码*:</label>
                <input name="password" type="password" placeholder="请输入密码">
            </div>
            <div class="userli">
                <label>验证码*:</label>
                <input name="captcha" type="text" class="captchainput" placeholder="请输入验证码">
                <img onclick="flushCaptch(this)" class="captcha" data-osrc="<?php echo Uri::build('user','captcha'); ?>" src="<?php echo Uri::build('user','captcha'); ?>"/>
            </div>
            <hr>
            <div>
                <span class="red"><?php if(isset($error)){echo $error;} ?></span>
                <input type="submit" name="subbtn" class="submitbtn" value="关联登陆">
            </div>
        </form>
    </div>
</div>
<?php
include('wwwfooter.php');
?>