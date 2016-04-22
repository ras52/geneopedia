<?php
set_include_path('..');

include_once('include/database.php');

function format_size_2($val, $unit) {
  if ($val >= 10) return sprintf("%.0f $unit", $val);
  else return sprintf("%.1f $unit", $val);
}

function format_size($bytes) {
  if ($bytes > 1024*1024*1024) 
    return format_size_2( $bytes / (1024*1024*1024.0), 'GB' );
  elseif ($bytes > 1024*1024) 
    return format_size_2( $bytes / (1024*1024.0), 'MB' );
  elseif ($bytes > 1024) 
    return format_size_2( $bytes / 1024.0, 'kB' );
  else
    return "$bytes bytes";
}

function content() {
  if (!user_logged_in()) return must_log_in();

  $files = fetch_wol('*', 'files', sprintf("user_id=%d", user_logged_in()));

  if (count($files) == 0) { ?>
    <p>You have not <a href="upload">uploaded</a> any files.</p>
    <?php return;
  } 

  ?>
  <table class="data">
    <?php foreach ($files as $f) { ?>
      <tr><td class="file-id"><a href="<?php esc($f->id.'.'.$f->extension) 
        ?>"><?php esc(sprintf("%06d", $f->id)) ?></a></td>
        <td><?php esc(date_format('Y-m-d H:i:s', $f->date_uploaded)) ?></td>
        <td><?php esc(format_size($f->length)) ?></td>
      </tr>
    <?php } ?>
  </table>

<?php }

include('include/template.php');
