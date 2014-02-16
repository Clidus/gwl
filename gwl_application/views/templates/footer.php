    <hr>

    <footer>
      <p>Website created by <a href="http://www.clidus.com/" target="_blank">Joshua Marketis</a>. Logo design by <a href="http://www.drugs4kids.com/" target="_blank">Michael Orson</a>. Video game data provided by the top men at <a href="http://www.giantbomb.com" target="_blank">Giant Bomb</a>.</p>
    </footer>

  </div>

  <!-- Javascript-->
  <script>
    var baseUrl = "<?php echo $baseUrl ?>";
  </script>
  <script src="<?php echo $baseUrl ?>js/jquery-2.0.3.min.js"></script>
  <script src="<?php echo $baseUrl ?>bootstrap/js/bootstrap.min.js"></script>
  <script src="<?php echo $baseUrl ?>js/script.js"></script>
  <script>
    $(document).ready(function() {
      <?php
          echo "$('#nav" . $pagetitle . "').addClass('active');";
      ?>
    });
  </script>
  <script>
    (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
    (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
    m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
    })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

    ga('create', 'UA-19451189-7', 'gamingwithlemons.com');
    ga('send', 'pageview');
  </script>
</body>
</html>
