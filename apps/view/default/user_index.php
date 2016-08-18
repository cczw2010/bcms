<?php
	include('wwwheader.php');
?>
<div class="wraper">
	<img src="$user['avatar']" alt="">
	欢迎你：<?php  echo $user['username']?>
</div>
	<meter value="3" min="0" max="10">十分之三</meter>
	<meter value="0.6">60%</meter> 
	<progress value="22" max="100">111</progress> 

<?php
	include('wwwfooter.php');
?>