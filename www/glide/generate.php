<?php
// site specific options
require_once(__DIR__.'/config.php');

function serve404() {
  http_response_code(404);
  die();
}
/* ==========================================================================
  Glide API Image Options
  http://glide.thephpleague.com/1.0/api/quick-reference/
  ========================================================================== */
$defaultOptions = array(
  'w' => 150, // Sets width
  'q'  => 80, // Defines the quality of the image.
  'fit' => 'crop', // Sets how the image is fitted to its target dimensions
  'dpr' => 2, // device pixel ratio - double the size of the image
  'or' => 0, // Sets orientation
);

// the cache url requested
$cacheSource = $_GET['url'];
if (!$cacheSource) serve404();

$pathInfo = pathinfo($cacheSource);

// get the individual parts of the url
$dirname = $pathInfo['dirname'];
$filename = $pathInfo['filename'];
$extension = $pathInfo['extension'];

// pull out the options string from the cache file name, and put it back together without them
$filename = explode('.', $filename);
$options_string = array_pop($filename);
$filename = implode('.', $filename);

// check if the option is a named type
if (is_array($imageTypes[$options_string])) {
  $options = $imageTypes[$options_string];
}



// if we don't have a valid options array bail out
if (!is_array($options)) serve404();
// compile default and selected options down to a single array
$glideParams = array_merge($defaultOptions, $defaultSiteOptions, $options);
// build path of original filename, with options removed
$imageSource = $dirname.'/'.$filename.'.'.$extension;


/* ==========================================================================
  Glide
  ========================================================================== */
if (!$glideParams['fm']) {
  switch(strtolower($extension)){
    case 'png':
        $glideParams['fm'] = 'png';
        break;
    case 'gif':
        $glideParams['fm'] = 'gif';
        break;
    case 'jpeg':
    case 'jpg':
    default:
        $glideParams['fm'] = 'pjpg';
  }
}

require __DIR__.'/../vendor/autoload.php';
use League\Glide\ServerFactory;
$GlideServer = ServerFactory::create([
    'source' => __DIR__ . '/../images/',
    'cache' => __DIR__ . '/cache/',
    'group_cache_in_folders' => false,
    'driver' => 'gd'
]);
// TESTING
$cacheFilePath = $GlideServer->makeImage($imageSource, $glideParams);
if (!is_dir(__DIR__ . '/../assets/' . $dirname)) {
  mkdir(__DIR__ . '/../assets/' . $dirname, false, true);
}
rename('cache/'.$cacheFilePath, __DIR__ . '/../assets/' . $cacheSource);

$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";

header('Location: '.$protocol.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
