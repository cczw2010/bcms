<?php
    include('wwwheader.php');
?>
<div class="wraper">
    <div class="userbox clearfix">
        <form action="<?php echo Uri::build('user','appnew'); ?>" method="post">
            <h3>完善资料</h3>
            <hr>
            <div class="userli">
                <label>用户名*:</label>
                <input name="username" type="text" placeholder="请输入用户名">
            </div>
            <div class="userli">
                <label>密 码*:</label>
                <input name="password" type="password" placeholder="请输入密码">
            </div>
            <div class="userli">
                <label>邮 箱*:</label>
                <input name="email" type="text" placeholder="请输入邮箱">
            </div>
            <hr>
            <div>
                <span class="red"><?php if(isset($error)){echo $error;} ?></span>
                <input type="submit" name="subbtn" class="submitbtn" value="保存登陆">
            </div>
        </form>
    </div>
</div>
    
<?php
    include('wwwfooter.php');
?>