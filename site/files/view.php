<?php
set_include_path('..');

include_once('include/database.php');
include_once('include/api.php');

function raw_content() {
  global $error_page;

  if ($_SERVER['REQUEST_METHOD'] != 'HEAD' &&
      $_SERVER['REQUEST_METHOD'] != 'GET') {
    http_response_code(405);
    header('Allow: GET, HEAD');
    exit;
  }

  if (!array_key_exists('id', $_GET)) {
    http_response_code(404);
    $error_page = "error_404_content"; 
    return; 
  }

  $f = fetch_one_or_none('files', 'id', $_GET['id']);
  if (is_null($f)) { 
    http_response_code(404);
    $error_page = "error_404_content"; 
    return; 
  }

  # If someone fetches /files/NN.png, give an error if this is not a png.
  if (array_key_exists('extension', $_GET)) {
    if ($f->extension != $_GET['extension']) {
      http_response_code(404);
      $error_page = "error_404_content"; 
      return; 
    }
  }

  header('Cache-Control: public');

  # TODO:  If it's not public:
  if (0) {
    # Cache-Control: private allows private caching, that is caching that 
    # is limited to a single user (as, for example, might happen in a web
    # browser).  Public caching, e.g. in a ISP's caching proxy, is not 
    # permitted.
    header('Cache-Control: private');

    # In principle this tells the browser to clear the cache on logout,
    # though in practice I'm not sure this does anything useful.
    header('Vary: Cookie');
  }

  # Send the Content-Type too, as otherwise PHP will use text/html
  header('Content-Type: '.$f->mime_type);

  # Only disclose the ETag now that we've checked the user is authorised.
  # We do this before handling If-None-Match, because that header may contain
  # multiple ETags, and if so, we need to disclose which one matched.
  header('ETag: ' . $f->sha1 );

  # This is complicated by the fact that If-None-Match can be combined with
  # If-Modified-Since, and then we only 304 if both match.  Once we set
  # $match = 0, we know we've failed.  If it is 1, we know that all of the 
  # conditions currently tested have succeed.  If it's null, then we've not
  # yet found a precondition to test.
  $match = null;

  if ($match !== 0 && array_key_exists('HTTP_IF_NONE_MATCH', $_SERVER)) {
    $match = 0;
    $etags = strtolower(str_replace(' ', '', $_SERVER['HTTP_IF_NONE_MATCH']));
    if ($etags == '*')
      $match = 1;
    else foreach (explode(',', $etags) as $e)
      if ($e == strtolower($f->sha1))
        $match = 1;
  }

  if ($match !== 0 && array_key_exists('HTTP_IF_MODIFIED_SINCE', $_SERVER)) {
    $match = 0;
    $fmt = 'Y-M-d\TH:i:s';
    if ( date_format(date_create($_SERVER['HTTP_IF_MODIFIED_SINCE']), $fmt)
         >= date_format(date_create($f->date_uploaded), $fmt) )
      $match = 1;
  }
  # If all conditions have succeeded, we send exit with a 304 Not Modified.
  if ($match) {
    http_response_code(304);
    exit;
  }

  header('Content-Size: '  . $f->length);
  header('Last-Modified: ' . date_format(date_create($f->date_uploaded),
                                         'D, d M Y H:i:s O'));

  # Add an RFC 5988 Link header.  This is the recommended means of linking
  # a resource to its description in POWDER, and, even though we're not
  # using POWDER, it's a standard means of metadata discovery c.f. various
  # W3C 'CSV on the Web' drafts.
  $base = "http://" . $config['domain'] . $config['http_path'] . 'files/';
  $url = $base.$f->id.'.'.$f->extension;
  header("Link: <$url.rdf>; "
         . 'rel="describedby"; type="application/rdf+xml"');

  if ($_SERVER['REQUEST_METHOD'] != 'HEAD') { 
    $mog = new MogileFs();

    global $config;
    $cfg = $config['mogilefs'];
    $mog->connect( $cfg['hostname'], $cfg['port'], $cfg['domain'] );

    # Fetch the metadata from mogilefs
    $metadata = $mog->get($f->id);

    # Pick a path at random
    $pathn = rand( 1, $metadata['paths'] );
    $path = $metadata[ 'path' . $pathn ];
    error_log("Fetching file #$id's data from $path");

    $fh = fopen($path, 'rb');
    fpassthru($fh);
    fclose($fh);
  }
}

raw_content();

global $error_page;
if ($error_page) 
  include('include/template.php');
