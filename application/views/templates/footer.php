            </div>
        </div>
        <div class="overlay"></div>
        <div class="overlay"></div>

        <!-- Optional JavaScript -->
        <!-- jQuery first, then Popper.js, then Bootstrap JS -->
        <script type="text/javascript">
            const base_url_js = 'http://localhost/ieadmin/';
        </script>
        <script src="<?php echo base_url ?>public/js/template/footer/jquery.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js" integrity="sha384-9/reFTGAW83EW2RDu2S0VKaIzap3H66lZH81PoYlFhbGU+6BZp6G7niu735Sk7lN" crossorigin="anonymous"></script>
        <script src="<?php echo base_url ?>public/js/bootstrap/bootstrap.js" ></script>
        <script src="<?php echo base_url ?>public/js/template/footer/customScrollbar.min.js" ></script>
        <script src="<?php echo base_url ?>public/js/template/sidenav/principal.js"></script>
        <script src="<?php echo base_url ?>public/js/libraries/FormValidator.js"></script>
        <script>
            $(document).ready(function(){
                $('[data-toggle="popover"]').popover();
            });
        </script>

        <?php if (isset($data['extra_js']))  echo $data['extra_js'] ?>

        <footer class="d-flex justify-content-center align-items-center footer">
            <span class="footer-copyright text-center py-3 gray-font ">©2023 IE International English</span>
        </footer>
    </body>
</html>
