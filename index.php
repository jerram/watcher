<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>Template &middot; Bootstrap</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">

    <!-- Le styles -->
    <link href="assets/css/bootstrap.css" rel="stylesheet">
    <link href="assets/css/watcher.css" rel="stylesheet">
    <link href="assets/css/bootstrap-responsive.css" rel="stylesheet">

    <!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
    <!--[if lt IE 9]>
      <script src="assets/js/html5shiv.js"></script>
    <![endif]-->

  </head>

  <body>
    <div class="container">
      <div class="masthead">
        <h3 class="muted">Watcher</h3>
        <div class="navbar">
          <div class="navbar-inner">
            <div class="container">
              <ul class="nav">
                <li class="active"><a href="#">Home</a></li>
                <li><a href="#">Requests</a></li>
                <li><a href="#">Clients</a></li>
                <li><a href="#">Downloads</a></li>
                <li><a href="#">Roadmap</a></li>
                <li><a href="#">Github</a></li>
              </ul>
            </div>
          </div>
        </div><!-- /.navbar -->
      </div>

      <!-- Example row of columns -->
      <div class="row-fluid">
        <div class="span4">
          <h2>requests</h2>
          <p><a class="btn" href="#">View details &raquo;</a></p>
          <p>Donec id elit non mi porta gravida at eget metus. Fusce dapibus, tellus ac cursus commodo, tortor mauris condimentum nibh, ut fermentum massa justo sit amet risus. Etiam porta sem malesuada magna mollis euismod. Donec sed odio dui. </p>
        </div>
        <div class="span4">
          <h2>Clients</h2>
          <p><a class="btn" href="#">View details &raquo;</a></p>
{{ content|raw }}
        </div>
        <div class="span4">
          <h2>Server</h2>
          <p><a class="btn" href="#">View details &raquo;</a></p>
          <p>Donec sed odio dui. Cras justo odio, dapibus ac facilisis in, egestas eget quam. Vestibulum id ligula porta felis euismod semper. Fusce dapibus, tellus ac cursus commodo, tortor mauris condimentum nibh, ut fermentum massa.</p>
        </div>
      </div>

      <hr>
    <h2>Aims</h2>
    <ul>
        <li>See where requests are being generated</li>
        <li>See what is being hit</li>
        <li>zoom in on ip</li>
        <li>realtime</li>
    </ul>

    <h2>Tasks</h2>
    <ul>
        <li><strike>remove image, css and js files from request list</strike></li>
        <li>twig templating</li>
        <li>show top paths</li>
        <li>show current log info</li>
        <li>read vhosts or a submitted file to match logs to domain</li>
        <li>Add some bootstrap</li>
        <li><strike>split demo into files and templates</strike></li>
        <li>show date / time range of results</li>
        <li>click ip to see details</li>
        <li>tail logs and refresh with ajax</li>
        <li>json and memcache or sql result sets</li>
        <li>export results</li>
        <li>force https</li>
        <li>block ip</li>
        <li>unzip logs</li>
        <li>show refferer</li>
        <li>read and show errors</li>
        <li>replay</a>
        <li>requests/s total by ip and url</a>
        <li>top burst request/s</li>
        <li>show heavy pages (by number of images / css etc)</li>
        <li>small panal for info on apache chlidren, mysql requests/s, memory, disk etc</li>
        <li>small panal for info on php version and settings like memory</li>
        <li>charts</li>
        <li>nginx support</li>
        <li>unit test each section</li>
        <li>speed test each section</li>
    </ul>

    <h2>Details</h2>
    <ul>
        <li>link to url</a>
        <li>location, whois on ip</a>
    </ul>

      <div class="footer">
        <p>&copy; Company 2013</p>
      </div>

    </div> <!-- /container -->

    <!-- Le javascript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="assets/js/jquery.js"></script>
    <script src="assets/js/bootstrap-transition.js"></script>
    <script src="assets/js/bootstrap-alert.js"></script>
    <script src="assets/js/bootstrap-modal.js"></script>
    <script src="assets/js/bootstrap-dropdown.js"></script>
    <script src="assets/js/bootstrap-scrollspy.js"></script>
    <script src="assets/js/bootstrap-tab.js"></script>
    <script src="assets/js/bootstrap-tooltip.js"></script>
    <script src="assets/js/bootstrap-popover.js"></script>
    <script src="assets/js/bootstrap-button.js"></script>
    <script src="assets/js/bootstrap-collapse.js"></script>
    <script src="assets/js/bootstrap-carousel.js"></script>
    <script src="assets/js/bootstrap-typeahead.js"></script>

  </body>
</html>
