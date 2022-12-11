<?php
$code_path = 'openstreetmap-date-format';

print "<form action='index.php' method='post'>";
if (!isset($_REQUEST['lang']) ) {
  print "Locale: <input name='lang' value=''>";
} elseif (!preg_match('/^[a-z]+(_[a-z]+)?$/', $_REQUEST['lang'])) {
  print "Invalid locale!";
  print "Locale: <input name='lang' value=\"" . htmlspecialchars($_REQUEST['lang']) . "\">";
} else {
  print "Locale: <input type='hidden' name='lang' value=\"" . htmlspecialchars($_REQUEST['lang']) . "\">" . htmlspecialchars($_REQUEST['lang']) . "<br>";
  foreach (['templates', 'locale', 'test'] as $p) {
    $file_path = "{$code_path}/{$p}/{$_REQUEST['lang']}.js";
    if (isset($_REQUEST[$p])) {
      file_put_contents($file_path, $_REQUEST[$p]);
    }

    print "File {$p}/{$_REQUEST['lang']}:<br>\n";
    print "<textarea name='{$p}'>" . htmlspecialchars(file_get_contents($file_path)) . "</textarea><br>\n";
  }

  print "<div class='tests'>Running tests:\n";
  print "<pre>" . htmlspecialchars(runTest($_REQUEST['lang'])) . "</pre>";
  print "</div>";
}
print "<input type='submit' value='Save and run tests'>\n";
print "</form>";

function runTest ($lang) {
  global $code_path;

  exec("cd " . escapeshellarg($code_path) . "; node node_modules/.bin/mocha " . escapeshellarg("test/{$lang}.js"), $output);
  return implode("\n", $output);
}
