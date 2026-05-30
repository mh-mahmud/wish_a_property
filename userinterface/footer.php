
<!-- footer end -->

<!-- Copyright start from here -->
<section class="at-copyright">
    <div class="container">
        <div class="row">
            <p>Copyright &copy; <?=date("Y")?> <a href="<?=$HOMEPAGE_ROOT?>">wishaproperty</a> All Rights Reserved</p>
        </div>
    </div>
</section>
<!-- Copyright end -->

<!-- all plugins and JavaScript -->
<script type="text/javascript" src="<?=$HOMEPAGE_ROOT?>/assets/js/css3-animate-it.js"></script>
<script type="text/javascript" src="<?=$HOMEPAGE_ROOT?>/assets/js/bootstrap-dropdownhover.min.js"></script>
<script type="text/javascript" src="<?=$HOMEPAGE_ROOT?>/assets/js/featherlight.min.js"></script>
<script type="text/javascript" src="<?=$HOMEPAGE_ROOT?>/assets/js/featherlight.gallery.min.js"></script>
<script type="text/javascript" src="<?=$HOMEPAGE_ROOT?>/assets/js/jquery.flexslider.js"></script>
<script type="text/javascript" src="<?=$HOMEPAGE_ROOT?>/assets/js/owl.carousel.min.js"></script>
<script type="text/javascript" src="<?=$HOMEPAGE_ROOT?>/assets/js/jarallax.js"></script>
<script type="text/javascript" src="<?=$HOMEPAGE_ROOT?>/assets/js/slick.min.js"></script>
<script type="text/javascript" src="<?=$HOMEPAGE_ROOT?>/assets/js/jquery-ui.js"></script>
<script type="text/javascript" src="<?=$HOMEPAGE_ROOT?>/assets/js/jquery-scrolltofixed-min.js"></script>
<script type="text/javascript" src="<?=$HOMEPAGE_ROOT?>/assets/js/morphext.min.js"></script>
<script type="text/javascript" src="<?=$HOMEPAGE_ROOT?>/assets/js/dyscrollup.js"></script>
<script type="text/javascript" src="<?=$HOMEPAGE_ROOT?>/assets/js/jquery.ripples.js"></script>
<script type="text/javascript" src="<?=$HOMEPAGE_ROOT?>/assets/js/jquery.mb.YTPlayer.min.js"></script>
<script type="text/javascript" src="<?=$HOMEPAGE_ROOT?>/assets/js/app.js"></script>

<!-- Main Custom JS -->
<script type="text/javascript" src="<?=$HOMEPAGE_ROOT?>/assets/js/main.js"></script>

<link rel="stylesheet" href="<?= $HOMEPAGE_ROOT; ?>/assets/css/alertify.core.css">
<link rel="stylesheet" href="<?= $HOMEPAGE_ROOT; ?>/assets/css/alertify.default.css">
<script>
    //this is the useful function to scroll a text inside an element...
    function startScrolling(scroller_obj, velocity, start_from) {
        //bind animation  inside the scroller element
        scroller_obj.bind('marquee', function (event, c) {
            //text to scroll
            var ob = $(this);
            //scroller width
            var sw = parseInt(ob.parent().width());

            //text width
            var tw = parseInt(ob.width());

            tw = tw - 10;
            //text left position relative to the offset parent
            var tl = parseInt(ob.position().left);
            //velocity converted to calculate duration
            var v = velocity > 0 && velocity < 100 ? (100 - velocity) * 1000 : 5000;
            //same velocity for different text's length in relation with duration
            var dr = (v * tw / sw) + v;

            //is it scrolling from right or left?
            switch (start_from) {
                case 'right':
                    //is it the first time?
                    if (typeof c == 'undefined') {
                        //if yes, start from the absolute right
                        ob.css({
                            left: (sw - 10)
                        });
                        sw = -tw;
                    } else {
                        //else calculate destination position
                        sw = tl - (tw + sw);
                    };
                    break;
                default:
                    if (typeof c == 'undefined') {
                        //start from the absolute left
                        ob.css({
                            left: -tw
                        });
                    } else {
                        //else calculate destination position
                        sw += tl + tw;
                    };
            }
            //attach animation to scroller element and start it by a trigger
            ob.animate({
                left: sw
            }, {
                duration: dr,
                easing: 'linear',
                complete: function () {
                    ob.trigger('marquee');
                },
                step: function () {
                    // check if scroller limits are reached
                    if (start_from == 'right') {
                        if (parseInt(ob.position().left) < -parseInt(ob.width())) {
                            //we need to stop and restart animation
                            ob.stop();
                            ob.trigger('marquee');
                        };
                    } else {
                        if (parseInt(ob.position().left) > parseInt(ob.parent().width())) {
                            ob.stop();
                            ob.trigger('marquee');
                        };
                    };
                }
            });
        }).trigger('marquee');
        //pause scrolling animation on mouse over
        scroller_obj.mouseover(function () {
            $(this).stop();
        });
        //resume scrolling animation on mouse out
        scroller_obj.mouseout(function () {
            $(this).trigger('marquee', ['resume']);
        });
    };

    //the main app starts here...

    //settings to pass to function
    var scroller = $('#info'); // element(s) to scroll
    var scrolling_velocity = 80; // 1-99
    var scrolling_from = 'right'; // 'right' or 'left'

    //call the function and start to scroll..
    startScrolling(scroller, scrolling_velocity, scrolling_from);

    $("a#property").on("click", function() {
        var url = "<?php echo $HOMEPAGE_ROOT . '/index.php?page=property' ?>";
        window.location = url;
    });
    $("a#service").on("click", function() {
        var url = "<?php echo $HOMEPAGE_ROOT . '/index.php?page=manage_service' ?>";
        window.location = url;
    });
</script>

</body>

</html>