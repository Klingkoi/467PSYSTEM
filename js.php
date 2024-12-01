<html>
    <body>
    <script>
        function toggleDisplay() {
            var brackets = document.getElementById('bracketView');
            var bracketQs = document.getElementById('Qs');
            var ordersSort = document.getElementById('SortBy');
            var orders = document.getElementById('Orders');


            if(brackets.style.display == 'none') {
                brackets.style.display = 'inline';
                bracketQs.style.display = 'inline';
                ordersSort.style.visibility = 'collapse';
                orders.style.visibility = 'collapse'
            }
            else {
                brackets.style.display = 'none';
                bracketQs.style.display = 'none';
                ordersSort.style.visibility = 'visible';
                orders.style.visibility = 'visible';
            }
        }
      var tableView = document.getElementById("sortingForm");
      function submitForm(event) {
         event.preventDefault();
      }
      form.addEventListener('submit', submitForm);
      toggleDisplay();
      </script>
    </body>
</html>
