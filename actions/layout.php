<!DOCTYPE html>

<html>
<head>
    <title><?=ucfirst(CURRENT_ACTION)?> — Phone Number Extractor</title>
    <!-- Bootstrap core CSS -->
    <link href="//maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom styles for this template -->
    <style type="text/css">
    .starter-template {
      padding: 1rem 1.5rem;
      text-align: center;
    }
    label.disabled {
      color: lightgray;
    }
    
    body
    {
      background-color: #f5f5f5;
      padding-top: 5rem;
      background-image: url("images/background.jpg");
      background-position-x: center; 
    }

    .edt
    {
      background:#ffffff; 
      border:3px double #aaaaaa; 
      -moz-border-left-colors:  #aaaaaa #ffffff #aaaaaa; 
      -moz-border-right-colors: #aaaaaa #ffffff #aaaaaa; 
      -moz-border-top-colors:   #aaaaaa #ffffff #aaaaaa; 
      -moz-border-bottom-colors:#aaaaaa #ffffff #aaaaaa; 
      width: 350px;
    }
    .edt_30
    {
      background:#ffffff; 
      border:3px double #aaaaaa; 
      font-family: Courier;
      -moz-border-left-colors:  #aaaaaa #ffffff #aaaaaa; 
      -moz-border-right-colors: #aaaaaa #ffffff #aaaaaa; 
      -moz-border-top-colors:   #aaaaaa #ffffff #aaaaaa; 
      -moz-border-bottom-colors:#aaaaaa #ffffff #aaaaaa; 
      width: 30px;
    }
    
    input {
      font-size: 16px
    }
    input.btn
    {
      font-weight: bold;
      padding: 5px;
    }
    
    input.auto-map
    {
      font-weight: normal;
      font-size: 70%;
    }

    td {
      text-align: center;
    }

    fieldset {
      border: solid 1px gray;
      text-align: left;
      padding: 5px 20px;
    }

    fieldset legend {
      padding-left: 6px;
      padding-right: 6px;
      width: auto;
    }
    
  </style>
  <script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
  
  <script src="//maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>

</head>

<body>
    <nav class="navbar navbar-expand-md navbar-dark bg-dark fixed-top">
      <? if(!empty($_SESSION['authenticated'])) : ?>
        <div class="collapse navbar-collapse" id="navbarsExampleDefault">
          <ul class="navbar-nav mr-auto">

            <?
              $itemsList = array('search', 'files', 'settings');
              foreach ($itemsList as $item) {
                echo '<li class="nav-item ', (CURRENT_ACTION == $item ? 'active' : ''), '">';
                echo '<a  class="nav-link" href="index.php?page=' . $item . '">' . ucfirst($item) . '</a>';
                echo '</li>'; 
              }
            ?>
          </ul>
          <div style="display: none; text-align: center;" id="loader"  class="navbar-nav mr-auto">
              <img src="https://upload.wikimedia.org/wikipedia/commons/d/de/Ajax-loader.gif" width="32" height="32" alt="loader" />
              </div>
        </div>

        <div  class="navbar-nav" style="float: right;">
          <a class="nav-link" href="index.php?page=logout"><small>Logout</small></a>
        </div>
      <? endif; ?>
    </nav>
    <main role="main" class="container mw-100">
      <div style="text-align: center;">
        <? if (!empty($errorMessage)): ?>
          <div class="alert alert-danger" role="alert">
            <?=$errorMessage?>
          </div>
          <br/>
        <? endif; ?>

        <? if (!empty($message)): ?>
          <div class="alert alert-info" role="alert">
            <?=$message?>
          </div>
          <br/>
        <? endif; ?>
      </div>

      <div class="starter-template">
        [template]
      </div>

    </main><!-- /.container -->

</body>
</html>