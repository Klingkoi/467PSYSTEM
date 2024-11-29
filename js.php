<html>
    <body>
    <script>
        function toggleDisplay() {
            var brackets = document.getElementById('bracketView');
            var bracketQs = document.getElementById('Qs');


            if(brackets.style.visibility == 'collapse') {
               brackets.style.visibility = 'visible';
               bracketQs.style.visibility = 'visible';
            }
            else {
                brackets.style.visibility = 'collapse';
                bracketQs.style.visibility = 'collapse';
            }
        }
    </script>
    </body>
</html>
