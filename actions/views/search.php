<style type="text/css">
  #numbers_list {
    font-size: 150%;
  }
</style>
<div style="text-align: center; margin-bottom: 50px;">
  <h1>
    Search by the area code
  </h1>
</div>

<form method="post" enctype="multipart/form-data" onsubmit="$('#submit').attr('disabled', 'disabled'); $('#loader').show();">
   <input type="hidden" name="version" value="1.0" />
   <table border="0" align="center">
    <tr>
      <td><label for="numbers_list">Area codes:<br/><small class="text-muted">(the parentheses are ignored)</small></label></td>
      <td rowspan="30" width="10px">&nbsp;</td>
      <td>
        <textarea name="numbers_list" id="numbers_list" class="edt" rows="8" cols="5" /><?=(!empty($cleanNumbersList) ? implode("\n", $cleanNumbersList) : '')?></textarea>
      </td>
    </tr>
    <tr>
      <td colspan="3">&nbsp;</td>
    </tr>
    <tr>
      <td colspan="3" align="center">
        <input id="submit" type="Submit" name="Go" value="Search" class="btn btn-primary" style="padding: 10px 15px" onclick="var s = document.getElementById('numbers_list'); if(null != s && '' == jQuery.trim(s.value)) {alert('Please enter some area codes'); s.focus(); return false;}">
    </td>
    </tr>
  </table>
</form>

<? if(isset($foundTotal)): ?>
  <hr />
  <h4>Found: <?=$foundTotal?> records</h4>
  <? if(!empty($foundTotal)): ?>
    <p><a href="index.php?page=download-dump&list=<?=implode(",", $cleanNumbersList)?>">Download the dump file</a></p>
  <? endif; ?>
<? endif; ?>

<br/>
<?
  return; /////////////////
?>
<table class="table" style="width: 500px" align="center">
  <tbody>
    <tr>
      <th scope="row">Total rows imported</th>
      <td><?=$rowsCount?></td>
    </tr>
    <tr>
      <th scope="row">Duplicates within the file</th>
      <td><?=$duplicatesWithinNumbers?></td>
    </tr>
    <tr>
      <th scope="row">Unique records</th>
      <td>
        <? if ($uniqueNumbers > 0) : ?>
          <a href="archive/<?=$batchId?>/unique.csv.zip" title="Click to download a zip archive"><?=$uniqueNumbers?></a>
        <? else : ?>
          <?=$uniqueNumbers?>
        <? endif; ?>
      </td>
    </tr>
    <tr>
      <th scope="row">Duplicate records</th>
      <td>
        <? if ($duplicateNumbers > 0) : ?>
          <a href="archive/<?=$batchId?>/duplicate.csv.zip" title="Click to download a zip archive"><?=$duplicateNumbers?></a>
        <? else : ?>
          <?=$duplicateNumbers?>
        <? endif; ?>
      </td>
    </tr>
  </tbody>
</table>
