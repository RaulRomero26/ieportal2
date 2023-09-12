$(document).ready(function() {
    $("#sidebar").mCustomScrollbar({
        theme: "minimal"
    });

    $('#dismiss, .overlay').on('click', function() {
        $('#sidebar').removeClass('active');
        $('.overlay').removeClass('active');
        $('#sidenav_p').addClass('fade')
        document.getElementById("sidenav_p").style.visibility = 'visible'
        $('#sidenav_p').removeClass('fade')
            //alert("Se desactivo el side nav")
    });

    $('#sidebarCollapse').on('click', function() {
        $('#sidebar').addClass('active');
        $('.overlay').addClass('active');
        $('.collapse.in').toggleClass('in');
        $('a[aria-expanded=true]').attr('aria-expanded', 'false');
        $('#sidenav_p').addClass('fade')
    });
});