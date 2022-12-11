<DOCTYPE html>
<html>
<head>
<link rel="stylesheet" href="style.css">
</head>
<body>
<?php
$code_path = 'openstreetmap-date-format';

print "<form action='.' method='post'>\n";
if (!isset($_REQUEST['lang']) ) {
  print "<div class='locale'>";
  print "Locale: <input name='lang' value=''>";
  print "</div>";
} elseif (!preg_match('/^[a-z]+(_[a-z]+)?$/', $_REQUEST['lang'])) {
  print "<div class='locale'>";
  print "Invalid locale!";
  print "Locale: <input name='lang' value=\"" . htmlspecialchars($_REQUEST['lang']) . "\">";
  print "</div>";
} else {
  print "<div class='locale'>";
  print "Locale: <input type='hidden' name='lang' value=\"" . htmlspecialchars($_REQUEST['lang']) . "\">" . htmlspecialchars($_REQUEST['lang']) . "<br>";
  print "File: <select name='fileSelector' id='fileSelector'>";
  foreach (['templates', 'locale', 'test'] as $p) {
    print "<option value='{$p}'" . ($_REQUEST['fileSelector'] === $p ? " selected" : "") . ">{$p}/{$_REQUEST['lang']}.js</option>";
  }
  print "</select>";
  print "</div>";
  print "<div class='code'>\n";

  foreach (['templates', 'locale', 'test'] as $p) {
    $file_path = "{$code_path}/{$p}/{$_REQUEST['lang']}.js";
    if (isset($_REQUEST[$p])) {
      file_put_contents($file_path, $_REQUEST[$p]);
    }

    print "<textarea name='{$p}'>" . htmlspecialchars(file_get_contents($file_path)) . "</textarea>\n";
  }

  print "</div>\n";
  print "<div class='tests'>Running tests:\n";
  print "<pre wrap>" . htmlspecialchars(runTest($_REQUEST['lang'])) . "</pre>";
  print "</div>";
}
print "<input type='submit' value='Save and run tests'>\n";
print "</form>";
?>
</body>
<script>
function selectFile () {
  const textareas = document.querySelectorAll('.code > textarea')
  textareas.forEach(ta => ta.style.display = select.value === ta.name ? 'block' : 'none')
}

const select = document.getElementById('fileSelector')
select.onchange = () => selectFile()
selectFile()
</script>
</html>
<?php
function runTest ($lang) {
  global $code_path;

  exec("cd " . escapeshellarg($code_path) . "; node node_modules/.bin/mocha " . escapeshellarg("test/{$lang}.js"), $output);
  return implode("\n", $output);
}
