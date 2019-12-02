<style>
  .uploadifive-queue-item {
    width: 45%;
    display: inline-block;
  }
</style>
<form action="/manage/picture/edit?moduleid=<?php echo $moduleid; ?>">
  <table class="table table-striped table-bordered table-hover dataTable">
    <thead>
      <tr>
        <th colspan="3">图片管理</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td>
          <div id="btnwrap"></div>
          <div id="pictures"></div>
        </td>
        <td width="250">
          <div class="alert alert-block alert-danger">
            <?=$sizes[$moduleid]?>
          </div>
        </td>
      </tr>
      <tr>
        <td colspan="3">
          <input type="hidden" name="moduleid" value="<?php echo $moduleid; ?>">
          <input type="button" name="submitbtn" class="submitbtn btn btn-info" value="提 交" data-verify="1">
        </td>
      </tr>
    </tbody>
  </table>
</form>
<script>
  $(function () {
    // 上传
    var jsons = <?=json_encode($pics);?>,
      objtype = "<?=$moduleid;?>",
      setting = {
        queueID: 'pictures',
        fileObjName: 'file',
        // uploadLimit:1,
        queueSizeLimit: <?=$num?>,
        formData: {
          'objtype': objtype,
        },
        uploadScript: '/manage/widget/upload/'
      },
      params={
        callback:function (error, file, data) {
          if (error) {
            alert(error)
            return false;
          }
          console.log(file, data);
        },
        btnWraper:'#btnwrap', 
      };
    if(setting.queueSizeLimit!=1){
      params.canSort = true;
    }
    if(objtype=='business_slider'){
      params.hasDesc =true;
      params.hasLink =true;
    }
    // console.log(jsons);
    var supload = new SUplodiFive(setting,params);
    supload.prepare(jsons);
  });
</script>