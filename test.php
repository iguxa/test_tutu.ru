<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>jQuery UI Datepicker - Default functionality</title>
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <link rel="stylesheet" href="/resources/demos/style.css">
    <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script>
        $( function() {
            $( "#datepicker" ).datepicker();
        } );
    </script>
</head>
<body>

<p>Date: <input type="text" id="datepicker"></p>


</body>
<script>
    $(document).ready(function(){

        $(function () {

            console.log(location.hash);

            var tabContainers = $('div.tabs > div');
            tabContainers.hide().filter(':first').show();
            $('div.tabs ul.tabNavigation a').click(function () {
                tabContainers.hide();
                tabContainers.filter(this.hash).show();
                $('div.tabs ul.tabNavigation a').removeClass('selected');
                $(this).addClass('selected');
                return false;
            }).filter(':first').click();

            if(location.hash != "")
            {
                $('div.tabs ul.tabNavigation a[href="' + location.hash + '"]').click();
            }

            $('body').on('click','a.goto3',function(e){
                $('div.tabs ul.tabNavigation a[href="#t3"]').click();
            });



        });

        jQuery(function ($) {
            $.datepicker.regional['ru'] = {
                closeText: 'Закрыть',
                prevText: '&#x3c;Пред',
                nextText: 'След&#x3e;',
                currentText: 'Сегодня',
                monthNames: ['Январь', 'Февраль', 'Март', 'Апрель', 'Май', 'Июнь',
                    'Июль', 'Август', 'Сентябрь', 'Октябрь', 'Ноябрь', 'Декабрь'],
                monthNamesShort: ['Январь', 'Февраль', 'Март', 'Апрель', 'Май', 'Июнь',
                    'Июль', 'Август', 'Сентябрь', 'Октябрь', 'Ноябрь', 'Декабрь'],
                dayNames: ['воскресенье', 'понедельник', 'вторник', 'среда', 'четверг', 'пятница', 'суббота'],
                dayNamesShort: ['вск', 'пнд', 'втр', 'срд', 'чтв', 'птн', 'сбт'],
                dayNamesMin: ['Вс', 'Пн', 'Вт', 'Ср', 'Чт', 'Пт', 'Сб'],
                weekHeader: 'Нед',
                dateFormat: 'dd.mm.yy',
                firstDay: 1,
                isRTL: false,
                showMonthAfterYear: false,
                yearSuffix: ''
            };
            $.datepicker.setDefaults($.datepicker.regional['ru']);
        });

        var mtop=0;
        var maxscroll=0;
        $(window).scroll(function() {
            if($(this).scrollTop() > 200) {
                $('.right-form').addClass('fixed');
                maxscroll=$(document).height()-888;
                if($(this).scrollTop() > maxscroll) {
                    mtop=maxscroll-$(this).scrollTop();
                    $('.right-form').css("margin-top",mtop+"px");
                }
                else {
                    $('.right-form').css("margin-top","0");
                }
            } else {
                $('.right-form').removeClass('fixed');
            }
        });
        $('.tabs .content .tab-zag').click(function() {
            $(this).toggleClass('active');
            $(this).next('.tabs .content .text').slideToggle();
        });




        $('#datepicker').datepicker({
            beforeShowDay: function(date){
                var string = jQuery.datepicker.formatDate('yy-mm-dd', date);
                return [ arrayGlobalActiveDate.indexOf(string) > -1 ];
            }
        });

    });
</script>

</html>