<?php
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

$file = file_get_contents('values.txt');
$lines = explode("\n", $file);
$values = [];
foreach($lines as $line) {
  $value = explode(" ", $line);
  $key = array_shift($value); // ulozi prvni index a smaze ho z $value

  if(count($value) === 1) {
    $values[$key] = floatval($value[0]);
  } else {
    $values[$key] = $value;
  }
}

function isChecked($values, $x) {
  return $values['io' . $x] === 1.0;
}

?><html>
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>LED for ESP8266</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css" integrity="sha512-dTfge/zgoMYpP7QbHy4gWMEGsbsdZeCXz7irItjcC3sPUFtf0kuFbDz/ixG7ArTxmDjLXDmezHubeNikyKGVyQ==" crossorigin="anonymous">
    <link rel="Stylesheet" href="https://code.jquery.com/ui/1.11.4/themes/ui-lightness/jquery-ui.css">
    <script src="https://code.jquery.com/jquery-2.1.4.min.js"></script>
    <script src="https://code.jquery.com/ui/1.11.4/jquery-ui.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js" integrity="sha512-K1qjQ+NcF2TYO/eI3M6v8EiNYZfA95pQumfvcVrTHtwQVDG+aHRqLi/ETn2uB+1JqwYqVG3LIvdm9lj6imS/pQ==" crossorigin="anonymous"></script>
    <style>
      #messages {
        margin-top: 24px;
      }
    </style>
  </head>
  <body>
    <div class="container">
      <form id="my-form" class="form-horizontal">
        <h1>IO</h1>
        <?php for($x = 1; $x <= 5; $x++) : ?>
          <div class="checkbox">
            <label>
              <?php if(isChecked($values, $x)): ?>
                <input class="input checked" name="io<?=$x?>" checked="checked" type="checkbox" /> IO <?=$x?>
              <?php else: ?>
                <input class="input" name="io<?=$x?>" type="checkbox" /> IO <?=$x?>
              <?php endif ?>
            </label>
          </div>
        <?php endfor ?>

        <h1>PWM</h1>
        <div class="row">
          <div class="col-md-3 col-xs-12">R <span id="r-value"><?=$values['pwm'][0]?></span></div>
          <div class="col-md-9 col-xs-12">
            <div id="slider-r" class="slider"></div>
            <input type="hidden" name="pwm[0]" id="r-input" value="<?=$values['pwm'][0]?>">
          </div>
        </div>

        <div class="row">
          <div class="col-md-3 col-xs-12">G <span id="g-value"><?=$values['pwm'][1]?></span></div>
          <div class="col-md-9 col-xs-12">
            <div id="slider-g" class="slider"></div>
            <input type="hidden" name="pwm[1]" id="g-input" value="<?=$values['pwm'][1]?>">
          </div>
        </div>

        <div class="row">
          <div class="col-md-3 col-xs-12">B <span id="b-value"><?=$values['pwm'][2]?></span></div>
          <div class="col-md-9 col-xs-12">
            <div id="slider-b" class="slider"></div>
            <input type="hidden" name="pwm[2]" id="b-input" value="<?=$values['pwm'][2]?>">
          </div>
        </div>

        <h1>ADC</h1>
        <div>
          <input type="text" class="form-control input" name="adc" value="<?=$values['adc']?>" />
        </div>

        <div id='messages'></div>
      </form>
    </div>
    <script type="text/javascript">
      function saveForm() {
        clearMessagesAfterTimeout();

        $.post("save.php", $('#my-form').serialize())
          .done(function(response) {
            $('#messages').html('<p id="success-message" class="alert alert-success">Uloženo</p><pre>' + response + '</pre>');
          })
          .fail(function(response) {
            alert('Saving failed, see console.');
            console.warn(response);
          })
      }

      // pri zmene nektere hodnoty odesle form
      $('.input').change(function(event) {
        saveForm()
      })

      $("#slider-r").slider({
        min: 0,
        max: 255,
        value: <?=$values['pwm'][0]?>,
        slide: function(event, ui) {
          $("#r-value").html(ui.value);
          $('#r-input').val(ui.value);
        },
        change: function() {
          saveForm()
        }
      });

      $("#slider-g").slider({
        min: 0,
        max: 255,
        value: <?=$values['pwm'][1]?>,
        slide: function(event, ui) {
          $("#g-value").html(ui.value);
          $('#g-input').val(ui.value);
        },
        change: function() {
          saveForm()
        }
      });

      $("#slider-b").slider({
        min: 0,
        max: 255,
        value: <?=$values['pwm'][2]?>,
        slide: function(event, ui) {
          $("#b-value").html(ui.value);
          $('#b-input').val(ui.value);
        },
        change: function() {
          saveForm()
        }
      });

      // unikatni timer na smazani zprav
      function clearMessagesAfterTimeout() {
        if (window.myTimeout) {
          clearTimeout(window.timeoutPointer)

          window.timeoutPointer = setTimeout(function() {
            $('#messages').empty();
            window.myTimeout = false
          }, 4000);
        } else {
          window.timeoutPointer = setTimeout(function() {
            $('#messages').empty();
            window.myTimeout = false
          }, 4000);
          window.myTimeout = true;
        }
      }
    </script>
  </body>
</html>
