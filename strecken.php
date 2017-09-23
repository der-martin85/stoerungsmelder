<!DOCTYPE html>
<html lang="de">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Meine Pendlerstrecken</title>

    <!-- Bootstrap core CSS -->
    <link href="css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="starter-template.css" rel="stylesheet">
    <link href="css/typeaheadjs.css" rel="stylesheet">
    <link href="css/bootstrap-timepicker.min.css" rel="stylesheet">
    <link href="css/font-awesome.min.css" rel="stylesheet">
    
  </head>

  <body>

    <nav class="navbar navbar-expand-md navbar-dark bg-dark fixed-top">
      <a class="navbar-brand" href="#">#störungsmelder</a>
      <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarsExampleDefault" aria-controls="navbarsExampleDefault" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse" id="navbarsExampleDefault">
        <ul class="navbar-nav mr-auto">
          <li class="nav-item">
            <a class="nav-link" href="NeuePendlerstrecke.html">Neue Pendlerstrecke</a>
          </li>
          <li class="nav-item active">
            <a class="nav-link" href="#">Meine Pendlerstrecken<span class="sr-only">(current)</span></a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="#">Einstellungen</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="index.html">Abmelden</a>
          </li>
        </ul>
      </div>
    </nav>

    <div class="container">

  <h2>Meine Pendelstecken</h2>

  <div class="table-responsive">          
  <table class="table">
    <thead>
      <tr>
        <th>Line</th>
        <th>Von</th>
        <th>Nach</th>
        <th>Tage</th>
        <th>Zeit</th>
        <th></th>
	<th></th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td>S3</td>
        <td>Neuwiedenthal</td>
        <td>S Reeperbahn</td>
        <td>Mo,Di,Mi,Do,Fr</td>
        <td>07:00-10:00</td>
        <td>
	  <button id="btnFA" class="btn">
	    <i class="fa fa-pencil">
	    </i>
	  </button>
	      </td>
	      <td>
	  <button id="btnFA" class="btn">
	    <i class="fa fa-trash">
	    </i>
	  </button>
	</td>
      </tr>
      <?php if (isset($_GET['succes'])) {?>
      <tr>
              <td>36</td>
	      <td>S Reeperbahn</td>
              <td>Johannes-Brahms-Platz</td>
              <td>Mo,Di,Mi,Do,Fr</td>
              <td>07:00-10:00</td>
              <td>
		<button id="btnFA" class="btn">
		  <i class="fa fa-pencil">
		  </i>
		</button>
	      </td>
	      <td>
		<button id="btnFA" class="btn">
		  <i class="fa fa-trash">
		  </i>
		</button>
	      </td>
	    </tr>

      <?php } ?>
    </tbody>
  </table>

  
<a class="nav-link" href="NeuePendlerstrecke.html"><button id="btnFA" class="btn btn-primary">
    <i class="fa fa-plus-circle">
      Neue Strecke
    </i>
  </button></a>
  </div>
</div>

    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="js/jquery-3.2.1.min.js"></script>
    <script src="js/popper.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/bootstrap-timepicker.min.js"></script>
    <script src="js/bootstrap3-typeahead.min.js"></script>
    <script src="js/pendelstrecke.js"></script>
    </body>
</html>
