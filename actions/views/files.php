
<div style="text-align: center; margin-bottom: 50px;">
  <h1>
    Files
  </h1>
</div>

<table class="table table-striped table-hover">
  <thead class="thead-dark">
    <tr>
      <th scope="col">URL</th>
      <th scope="col">Last import status</th>
      <th scope="col">Mode</th>
      <th scope="col">Records number</th>
      <th scope="col">Last fetched at</th>
      <th scope="col">The fetch took</th>
      <th scope="col">Filesize</th>
      <th scope="col">Status</th>
      <th scope="col">Refetch</th>
    </tr>
  </thead>
  <tbody id="table-body">
    <? 
    
    for ($i = 1; $i <= 8; $i++) : 
      $latestImport = $latestImports[$i];
      $isImportInProgress = !empty($latestImport) && ('in progress' == $latestImport['status']);
    ?>
        <tr>
          <td title="<?=$remoteFileUrls[$i]?>">
            <?=basename($remoteFileUrls[$i])?>
          </td>

          <td>
              <?=!empty($latestImport) ? $latestImport['status'] : 'Never started' ?>
            </span>
          </td>

          <td>
            <?=(!empty($latestImport)) ? $latestImport['way'] : '—' ?>
          </td>

          <td>
            <?=!empty($latestImport['records_number'])
                  ? number_format($latestImport['records_number'], 0)
                  : '—';?>
          </td>

          <td>
            <?=!empty($latestImport['finished_at'])
                  ? format_datetime($latestImport['finished_at'])
                  : '—';?>
          </td>

          <td>
            <? if(!empty($latestImport['started_at'])) : ?>
              <?=format_time(time_ago($latestImport['started_at'], $latestImport['finished_at']));?>
            <? else: ?>
              —
            <? endif; ?>
          </td>

          <td>
            <?=!empty($latestImport['filesize'])
              ? number_format($latestImport['filesize'] / 1024 / 1024, 2) .  ' Mb'
              : '—'
              ?>
          </td>

          <td>
            <?=!empty($latestImport) ? $latestImport['error_message'] : ''?>
          </td>

          <td>
            <? if ($isImportInProgress) : ?>
              <small>started</small>
            <? else : ?>
              <a href="#" onclick="startExport(<?=$i?>, this); return true;">Refetch</a>
            <? endif; ?>
          </td>
        </tr>
    <?php endfor; ?>
  </tbody>
</table>

<script>
function startExport(id, link) {
  $(link).replaceWith('<small>started</small>');
  $('#loader').show();
  $.ajax( "index.php?page=grab-file&way=manual&file_id="+id)
    .done(document.location.reload());
}
</script>