<div style="text-align: center; margin-bottom: 50px;">
  <h1>
    Settings
  </h1>
</div>

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
        <p class="text-muted"><small>Records available: 
          <span id="records_total">Calculating...</span>
        </small></p>
      </td>
    </tr>
    <tr>
      <td colspan="5">&nbsp;</td>
    </tr>
  </table>
</form>

<script type="text/javascript">

$.ajax("index.php?page=calculate-total" )
  .done(function(msg) {
    console.log(msg)
    $('#records_total').html(msg.total)
  })
  .fail(function() {
    $('#records_total').html('Error while fetching the total')
  });
  
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