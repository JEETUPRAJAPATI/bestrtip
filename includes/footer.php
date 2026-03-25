</div>
    </div>
    <!--<script src="assets/js/custom-script.js"></script>-->
    
    <!-- Success Popup -->
<div id="successPopup" class="popup success">
    <p id="successMessage">Success! Your action was completed.</p>
    <button onclick="closePopup('successPopup')">OK</button>
</div>

<!-- Error Popup -->
<div id="errorPopup" class="popup error">
    <p id="errorMessage">Error! Something went wrong.</p>
    <button onclick="closePopup('errorPopup')">OK</button>
</div>

<script>
    // Function to show the popup
    function showPopup(popupId, message) {
        const popup = document.getElementById(popupId);
        const popupMessage = popup.querySelector('p');
        popupMessage.textContent = message; // Set the message
        popup.style.display = 'block'; // Show the popup
    }

    // Function to close the popup
    function closePopup(popupId) {
        document.getElementById(popupId).style.display = 'none';
    }
</script>


  </body>
  <style>
    .pagination .page-item.active .page-link {
    background-color: black; /* Change the color to black */
}

    </style>
</html>