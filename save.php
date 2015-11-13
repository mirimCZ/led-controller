<?php
// hlavne tu neukladej zadnou neosetrenou hodnotu, zvenci ti muze prijit cokoliv
if(!empty($_POST)) {
  $cache = [];
  for($x = 1; $x <= 5; $x++) {
    $cache[] = 'io' . $x . ' '. intval(isset($_POST['io' . $x]));
  }

  $cache[] = 'pwm ' . intval($_POST['pwm'][0]) . ' ' . intval($_POST['pwm'][1]) . ' ' . intval($_POST['pwm'][2]);
  $cache[] = 'adc ' . floatval($_POST['adc']);

  file_put_contents('values.txt', join("\n", $cache));
  echo join("<br/>", $cache);
}
