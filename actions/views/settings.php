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

<? 
  for ($i = 1; $i <= 2; $i++) : 
    $latestImport = $latestImports[$i];
    $isImportInProgress = !empty($latestImport) && ('in progress' == $latestImport['status']);
  ?>
<fieldset>
  <legend>Remote file <?=$i?></legend>
  <form method="post" enctype="multipart/form-data" onsubmit="$('#loader').show(); $('#regular_user_submit1, #regular_user_submit2').attr('disabled', 'disabled');">
    <table border="0" align="center">
      <tr>
        <th style="text-align: right;"><label for="remote_file_url">URL:</label></th>
        <td width="10px">&nbsp;</td>
        <td><input type="text" name="remote_file_url[<?=$i?>]" id="remote_file_url<?=$i?>" class="edt" value="<?=$remoteFileUrls[$i]?>" style="width: 40em" /></td>
        <td>&nbsp;</td>
        <td><input id="regular_user_submit<?=$i?>" type="Submit" name="remote_file_url_submit[<?=$i?>]" value="Fetch" class="btn btn-primary" style="padding: 3px 15px" <?=$isImportInProgress ? 'disabled="disabled"' : ''?> /></td>
      </tr>
      <tr>
        <th style="text-align: right;">
          Last import status:
        </th>
        <td></td>
        <td colspan="3" style="text-align: left;">
          <? if (!empty($latestImport)) : ?>
            <?=$latestImport['status']?>
            (<?=$latestImport['way']?> mode)
          <? else : ?>
            <span>Never started</span>
          <? endif; ?>
        </td>
      </tr>
      <tr>
        <th style="text-align: right;">
          Records number:
        </th>
        <td></td>
        <td colspan="3" style="text-align: left;">
          <?=!empty($latestImport['records_number'])
                ? number_format($latestImport['records_number'], 0)
                : '—';?>
        </td>
      </tr>
      <tr>
        <th style="text-align: right;">
          Last fetched at:
        </th>
        <td></td>
        <td colspan="3" style="text-align: left;">
          <?=!empty($latestImport['finished_at'])
                ? date('jS \of F Y (h:i:s A)', $latestImport['finished_at'])
                : '—';?>
        </td>
      </tr>
      <tr>
        <th style="text-align: right;">
          The fetch took:
        </th>
        <td></td>
        <td colspan="3" style="text-align: left;">
          <?=format_time(time_ago($latestImport['started_at'], $latestImport['finished_at']));?>
        </td>
      </tr>
      <tr>
        <th style="text-align: right;">
          Filesize:
        </th>
        <td></td>
        <td colspan="3" style="text-align: left;">
          <?=!empty($latestImport['filesize'])
            ? number_format($latestImport['filesize'] / 1024 / 1024, 2) .  ' Mb'
            : '—'
            ?>
        </td>
      </tr>

      <tr>
        <th style="text-align: right;">
          Last erorr:
        </th>
        <td></td>
        <td colspan="3" style="text-align: left;">
          <?=!empty($latestImport['error_message']) ? $latestImport['error_message'] : '—'?>
        </td>
      </tr>
    </table>
  </form>
</fieldset>
<? endfor; ?>

<form method="post" enctype="multipart/form-data" onsubmit="$('#loader').show();" style="margin-top: 30px">
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