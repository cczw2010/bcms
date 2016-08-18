<?php
include('wwwheader.php');
?>
<div id="sels"></div>
<div id="sels1"></div>
<h1>你好!</h1>
<script>
addUpload('#sels',{},function(){console.log(arguments)});
addUpload('#sels',{},function(){console.log(arguments)});
addUpload('#sels',{},function(){console.log(arguments)});
</script>
<?php
include('wwwfooter.php');
?>