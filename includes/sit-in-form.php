<div id="sitInModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2>Sit In Form</h2>
            <span class="close" id="closeModalBtn">&times;</span>
        </div>
        <div class="modal-body">
            <form>
                <div>
                    <label for="idNumber">ID Number:</label>
                    <input type="text" id="idNumber" name="idNumber">
                </div>
                <div>
                    <label for="studentName">Student Name:</label>
                    <input type="text" id="studentName" name="studentName">
                </div>
                <div>
                    <label for="purpose">Purpose:</label>
                    <input type="text" id="purpose" name="purpose">
                </div>
                <div>
                    <label for="lab">Lab:</label>
                    <input type="text" id="lab" name="lab">
                </div>
                <div>
                    <label for="remainingSession">Remaining Session:</label>
                    <input type="text" id="remainingSession" name="remainingSession">
                </div>
            </form>
        </div>
        <div class="modal-footer">
            <button class="btn-close" id="closeModalBtnFooter">Close</button>
            <button class="btn-sit-in">Sit In</button>
        </div>
    </div>
</div>

<script>
    // Get the modal
    var modal = document.getElementById("sitInModal");

    // Get the button that opens the modal
    var btn = document.getElementById("openModalBtn");

    // Get the <span> element that closes the modal
    var span = document.getElementById("closeModalBtn");
    var footerBtn = document.getElementById("closeModalBtnFooter");

    // When the user clicks the button, open the modal 
    btn.onclick = function() {
        modal.style.display = "block";
    }

    // When the user clicks on <span> (x), close the modal
    span.onclick = function() {
        modal.style.display = "none";
    }

    // When the user clicks on the footer close button, close the modal
    footerBtn.onclick = function() {
        modal.style.display = "none";
    }

    // When the user clicks anywhere outside of the modal, close it
    window.onclick = function(event) {
        if (event.target == modal) {
            modal.style.display = "none";
        }
    }
</script>