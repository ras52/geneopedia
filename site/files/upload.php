<?php
set_include_path('..');

include_once('include/database.php');
include_once('include/forms.php');

function do_upload($path, $type, $extension, $size, $mtime = 'now') {
  $mog = new MogileFs();

  global $config;
  $cfg = $config['mogilefs'];
  $mog->connect( $cfg['hostname'], $cfg['port'], $cfg['domain'] );

  $file_id = insert_array_contents( 'files', array(
    'user_id'       => user_logged_in(),
    'date_uploaded' => date_format( date_create($mtime), 'Y-m-d H:i:s'),
    'mime_type'     => $type,
    'extension'     => $extension,
    'sha1'          => sha1_file($path),
    'length'        => $size,
  ) );

  $mog->put($path, $file_id, 'files');

  # Upload RDF

  return $file_id;
}

function content() {
  if (!user_logged_in()) return must_log_in();
  $errors = array();

  if (array_key_exists('upload', $_POST)) {
    if (!array_key_exists('file', $_FILES)
        || filesize($_FILES['file']['tmp_name']) == 0)
      $errors[] = 'Please supply a file';

    if (count($errors) == 0) {
      preg_match( '/\.([^\/.]+)$/', $_FILES['file']['name'], $matches);
      $file_id = do_upload( $_FILES['file']['tmp_name'],
                            $_FILES['file']['type'], $matches[1],
                            $_FILES['file']['size'] );
      page_header('File uploaded'); ?>


      <?php return;
    }
  }


  page_header('Upload file');
  show_error_list($errors); ?>

    <form enctype="multipart/form-data" action="" method="post">
      <div class="fieldrow">
        <div>
          <label for="file">Select an image 
            <span class="label-extra">(size limit: 8MB)</span></label>
          <input id="file" name="file" type="file" />
        </div>
      </div>

  
      <div class="fieldrow">
        <input type="submit" name="upload" value="Upload" />
      </div>
    </form>

<?php }

include('include/template.php');
