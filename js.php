<html>
    <body>
    <script>
        function toggleDisplay() {
            var brackets = document.getElementById('bracketView');
            var bracketQs = document.getElementById('Qs');
            var ordersSort = document.getElementById('SortBy');


            if(brackets.style.display == 'none') {
                brackets.style.display = 'inline';
                bracketQs.style.display = 'inline';
                ordersSort.style.visibility = 'collapse';
            }
            else {
                brackets.style.display = 'none';
                bracketQs.style.display = 'none';
                ordersSort.style.visibility = 'visible';
            }
        }
    </script>
    </body>
</html>
