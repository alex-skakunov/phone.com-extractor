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

<form method="post" enctype="multipart/form-data" onsubmit="$('#loader').show(); $('#regular_user_submit').attr('disabled', 'disabled');">
   <table border="0" align="center">
    <tr>
      <td><label for="remote_file_url">Remote file URL:</label></td>
      <td width="10px">&nbsp;</td>
      <td><input type="text" name="remote_file_url" id="remote_file_url" class="edt" value="<?=$remoteFileUrl?>" /></td>
      <td>&nbsp;</td>
      <td><input id="regular_user_submit" type="Submit" name="remote_file_url_submit" value="Fetch" class="btn btn-primary" style="padding: 3px 15px" <?=$isImportInProgress ? 'disabled="disabled"' : ''?> /></td>
    </tr>
    <tr>
      <td colspan="5">&nbsp;</td>
    </tr>
    <tr>
      <td style="text-align: right;">
        Last import status:
      </td>
      <td></td>
      <td colspan="3" style="text-align: left;">
        <?=$latestImport['status']?>
        (<?=$latestImport['way']?> mode)
      </td>
    </tr>
    <tr>
      <td style="text-align: right;">
        Last fetched at:
      </td>
      <td></td>
      <td colspan="3" style="text-align: left;">
        <?=!empty($latestImport['finished_at'])
              ? date('jS \of F Y (h:i:s A)', $latestImport['finished_at'])
              : '—';?>
      </td>
    </tr>
    <tr>
      <td style="text-align: right;">
        The fetch took:
      </td>
      <td></td>
      <td colspan="3" style="text-align: left;">
        <?
          $time = $latestImport['finished_at'] - $latestImport['started_at'];
          if ($time <= 0) {
            echo '—';
          }
          else if ($time < 60) {
            echo $time . ' seconds';
          }
          else {
            echo number_format($time / 60, 1) . ' minutes';
          }
        ?>
      </td>
    </tr>
    <tr>
      <td style="text-align: right;">
        Filesize:
      </td>
      <td></td>
      <td colspan="3" style="text-align: left;">
        <?=!empty($latestImport['filesize'])
          ? number_format($latestImport['filesize'] / 1024 / 1024, 2) .  ' Mb'
          : '—'
          ?>
      </td>
    </tr>

    <tr>
      <td style="text-align: right;">
        Last erorr:
      </td>
      <td></td>
      <td colspan="3" style="text-align: left;">
        <?=!empty($latestImport['error_message']) ? $latestImport['error_message'] : '—'?>
      </td>
    </tr>
  </table>
</form>

<hr/>
<form method="post" enctype="multipart/form-data" onsubmit="$('#loader').show();">
   <table border="0" align="center">
    <tr>
      <td><label for="user_password">Change the password:</label></td>
      <td width="10px">&nbsp;</td>
      <td><input type="password" name="user_password" id="user_password" class="edt" /></td>
      <td>&nbsp;</td>
      <td><input id="user_submit" type="Submit" name="user_submit" value="Save" class="btn btn-primary" style="padding: 3px 15px" /></td>
    </tr>
    <tr>
      <td colspan="5">&nbsp;</td>
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

</script>