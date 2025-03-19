<style>
    /* Modal Background Styling */
    .modal {
        display: none; /* Hidden by default */
        position: fixed;
        z-index: 1;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        overflow: auto; /* Allows scrolling if content overflows */
        background-color: rgba(0, 0, 0, 0.5); /* Dim overlay */
        display: flex; /* Flexbox to center modal */
        justify-content: center; /* Center horizontally */
        align-items: center; /* Center vertically */
        align-items: flex-start; 
    }

    /* Modal Content Styling */
    .modal-content {
        background-color: #0d121e;
        margin: 0 auto; /* Removes extra margin that pushes it down */
        padding: 20px;
        border-radius: 8px;
        width: 50%;
        max-width: 500px; /* Ensures it doesn’t get too wide */
        box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.2);
        animation: fadeIn 0.5s;
        margin-top: 50px;
    }

    /* Header Section Styling */
    .modal-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-bottom: 2px solid #ddd;
    }

    .modal-header h2 {
        margin: 0;
        font-size: 1.5em;
        color: white;
    }

    .close {
        cursor: pointer;
        font-size: 1.5em;
        color: #777;
        transition: color 0.3s;
    }

    .close:hover {
        color: #ff5c5c;
    }

    /* Form Styling */
    .modal-body form {
        display: flex;
        flex-direction: column;
        gap: 15px;
    }

    .modal-body label {
        font-weight: bold;
        color: white;
        display: block; /* Ensures the label takes a full line */
        margin-bottom: 10px; 
    }

    .modal-body input {
        width: 100%; /* Ensures all inputs take up the same width */
        box-sizing: border-box;
        padding: 10px;
        border: 1px solid #333;
        border-radius: 5px;
        font-size: 1em;
        background-color: #212b40;
        color: white;
        transition: border 0.3s;
        outline: none;
    }

    .modal-body input::placeholder {
        color: #777;
    }

    .modal-body input:focus {
        outline: none;
        border: 1px solid #007bff;
        box-shadow: 0px 0px 5px rgba(0, 123, 255, 0.5);
    }
    .modal-body label[for="idNumber"] {
         margin-top: 20px; 
    }

    /* Footer Button Styling */
    .modal-footer {
        display: flex;
        justify-content: flex-end;
        gap: 10px;
        border-top: 2px solid #ddd;
        margin-top: 20px;
        padding-top: 10px;
    }

    .btn-close,
    .btn-sit-in {
        padding: 10px 15px;
        border: none;
        border-radius: 5px;
        font-size: 1em;
        cursor: pointer;
        transition: background-color 0.3s, color 0.3s;
    }

    .btn-close {
        background-color: #f44336;
        color: white;
    }

    .btn-close:hover {
        background-color: #d32f2f;
    }

    .btn-sit-in {
        background-color: #007bff;
        color: white;
    }

    .btn-sit-in:hover {
        background-color:rgb(21, 88, 245);
    }

    /* Modal Fade-In Animation */
    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(-10%);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
</style>

<div id="sitInModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2>Sit In Form</h2>
            <span class="close" id="closeModalBtn">&times;</span>
        </div>
        <div class="modal-body">
        <form id="sitInForm" action="../includes/sit-in-process.php" method="POST">

    <div>
        <label for="idNumber">ID Number:</label>
        <input type="text" id="idNumber" name="idNumber" placeholder="Enter ID Number" required>
    </div>
    <div>
        <label for="studentName">Student Name:</label>
        <input type="text" id="studentName" name="studentName" placeholder="Enter Student Name" required>
    </div>
    <div>
        <label for="purpose">Purpose:</label>
        <select id="purpose" name="purpose" required>
            <option value="" disabled selected>Select Purpose</option>
            <option value="Practice Session">Practice Session</option>
            <option value="Laboratory Access">Laboratory Access</option>
            <option value="Special Project">Special Project</option>
            <option value="Extra Session">Extra Session</option>
        </select>
    </div>
    <div>
        <label for="lab">Lab #:</label>
        <input type="text" id="lab" name="lab" placeholder="Enter Lab #" required>
    </div>
    <div>
        <label for="remainingSession">Remaining Session:</label>
        <input type="number" id="remainingSession" name="remainingSession" value="30" readonly>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn-close" id="closeModalBtnFooter">Cancel</button>
        <button type="submit" class="btn-sit-in">Sit In</button> <!-- Ensure this is inside the form -->
    </div>
</form>

</div>

<script>
    document.addEventListener("DOMContentLoaded", function () {
    var modal = document.getElementById("sitInModal");
    var btn = document.getElementById("openModalBtn");
    var span = document.getElementById("closeModalBtn");
    var footerBtn = document.getElementById("closeModalBtnFooter");

    // Ensure the modal is hidden initially
    if (modal) {
        modal.style.display = "none";
    }

    // Check if button exists before adding event listener
    if (btn) {
        btn.addEventListener("click", function () {
            modal.style.display = "block";
        });
    }

    // Close modal when clicking the close button (X)
    if (span) {
        span.addEventListener("click", function () {
            modal.style.display = "none";
        });
    }

    // Close modal when clicking the cancel button
    if (footerBtn) {
        footerBtn.addEventListener("click", function () {
            modal.style.display = "none";
        });
    }

    // Close modal when clicking outside of the modal content
    window.addEventListener("click", function (event) {
        if (event.target === modal) {
            modal.style.display = "none";
        }
    });
});

</script>