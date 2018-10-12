<div style="text-align: center; margin-bottom: 50px;">
  <h1>
    Settings
  </h1>
</div>

<? if (!empty($errorMessage)): ?>
  <div class="alert alert-danger" role="alert">
    <?=$errorMessage?>
  </div>
  <br/>
<? endif; ?>

<? if (!empty($message)): ?>
  <div class="alert alert-info" role="alert">
    <?=$message?>
  </div>
  <br/>
<? endif; ?>

<form method="post" enctype="multipart/form-data" onsubmit="$('#loader').show();">
   <table border="0" align="center">
    <tr>
      <td><label for="remote_file_url">Remote file URL:</label></td>
      <td width="10px">&nbsp;</td>
      <td><input type="text" name="remote_file_url" id="remote_file_url" class="edt" value="<?=$remoteFileUrl?>" /></td>
      <td>&nbsp;</td>
      <td><input id="regular_user_submit" type="Submit" name="remote_file_url_submit" value="Save" class="btn btn-primary" style="padding: 3px 15px" /></td>
    </tr>
    <tr>
      <td colspan="5">&nbsp;</td>
    </tr>
    <tr>
      <td colspan="5">
        <p class="text-muted" id="file_availability_container">&nbsp;</p>
      </td>
    </tr>
  </table>
</form>

<hr/>
<form method="post" enctype="multipart/form-data" onsubmit="$('#loader').show();">
   <table border="0" align="center">
    <tr>
      <td><input type="submit" name="erase_database" id="erase_database" class="btn btn-primary" value="Erase the whole database" /></td>
    </tr>
    <tr>
      <td>
        <p class="text-muted"><small>Records available: <?=number_format($numbersCount)?></small></p>
      </td>
    </tr>
    <tr>
      <td colspan="5">&nbsp;</td>
    </tr>
  </table>
</form>

<script type="text/javascript">
function checkFileAvailability() {
  var url = $('#remote_file_url').val();
  if ('' == url) {
    return;
  }

  $('#file_availability_container').html('<small>Checking the file availability...</small>');
  $.getJSON(
    'index.php?page=check-availability',
    function(response) {
      if (!response || 'fail' == response.status) {
        $('#file_availability_container').html('<small>' + (response.message || 'Erorr!') + '</small>');
        return;
      }

      $('#file_availability_container').html(
        '<small>'
        + 'File size: '
        + round(response.content_length / 1024 / 1024, 2)
        + ' Mb <br/>'

        + 'Updated at: '
        + response.last_updated
        + '</small>'
      );

    }
  );
}

function round(number, precision) {
  var shift = function (number, precision, reverseShift) {
    if (reverseShift) {
      precision = -precision;
    }  
    numArray = ("" + number).split("e");
    return +(numArray[0] + "e" + (numArray[1] ? (+numArray[1] + precision) : precision));
  };
  return shift(Math.round(shift(number, precision, false)), precision, true);
}

$( document ).ready(function() {
  checkFileAvailability();
});
</script>