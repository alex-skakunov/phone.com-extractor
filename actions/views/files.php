
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
            <span id="basename_<?=$i?>"><?=basename($remoteFileUrls[$i])?></span>
            <span id="basename_<?=$i?>_updating" style="display: none"><small class="text-muted">wait</small></span>
            <a href="#" onclick="editUrl(<?=$i?>, this); return false;"><small>Edit</small></a>
          </td>

          <td id="status_<?=$i?>">
              <?=!empty($latestImport) ? $latestImport['status'] : 'Never started' ?>
            </span>
          </td>

          <td id="way_<?=$i?>">
            <?=(!empty($latestImport)) ? $latestImport['way'] : '—' ?>
          </td>

          <td id="records_number_<?=$i?>">
            <?=!empty($latestImport['records_number'])
                  ? number_format($latestImport['records_number'], 0)
                  : '—';?>
          </td>

          <td id="finished_at_<?=$i?>">
            <?=!empty($latestImport['finished_at'])
                  ? format_datetime($latestImport['finished_at'])
                  : '—';?>
          </td>

          <td id="fetch_took_<?=$i?>">
            <? if(!empty($latestImport['started_at'])) : ?>
              <?=format_time(time_ago($latestImport['started_at'], $latestImport['finished_at']));?>
            <? else: ?>
              —
            <? endif; ?>
          </td>

          <td id="filesize_<?=$i?>">
            <?=!empty($latestImport['filesize'])
              ? number_format($latestImport['filesize'] / 1024 / 1024, 2) .  ' Mb'
              : '—'
              ?>
          </td>

          <td id="error_message_<?=$i?>">
            <?=!empty($latestImport) ? $latestImport['error_message'] : ''?>
          </td>

          <td>
            <? if ($isImportInProgress) : ?>
              <small>started</small>
            <? else : ?>
              <a href="#" onclick="startExport(<?=$i?>, this); return false;">Refetch</a>
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

function editUrl(id, link) {
  var newUrl = prompt('Please enter a new URL');
  if (!newUrl) {
    return;
  }
  $(link).hide();
  $('#basename_' + id + '_updating').show();
  $('#loader').show();
  $.ajax({
      method: "POST",
      url: "index.php?page=update-file-url",
      data: { file_id: id, url: newUrl }
    })
    .done(function(data) {
      $('#loader').hide();
      $(link).show();
      $('#basename_' + id + '_updating').hide();
      $('#basename_' + id).html(data.basename).attr('title', newUrl);
      $('#status_' + id).html('Never started');
      $('#way_' + id
        + ', #way_' + id
        + ', #records_number_' + id
        + ', #finished_at_' + id
        + ', #fetch_took_' + id
        + ', #filesize_' + id).html('—')
        $('#error_message_' + id).html('')
      
    });
}
</script>