<?php
include 'db-connection.php';

if (!isset($_POST['query'])) {
    echo "<script>alert('No input provided.');</script>";
    exit;
}

$query = trim($_POST['query']);

$stmt = $conn->prepare("SELECT * FROM users WHERE id_no = ? OR CONCAT(firstname, ' ', middlename, ' ', lastname) LIKE ?");
$like = "%$query%";
$stmt->bind_param("ss", $query, $like);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "<script>alert('No student found.');</script>";
    exit;
}

$row = $result->fetch_assoc();
$id = $row['id_no'];
$fullname = $row['firstname'] . ' ' . $row['middlename'] . ' ' . $row['lastname'];
$sessions = $row['remaining_sessions'];
$course = $row['course'];
$year = $row['yr_level'];
?>

<style>
.modal {
    display: flex;
    position: fixed;
    z-index: 9999;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    padding: 30px 15px;
    background-color: rgba(0, 0, 0, 0.6);
    justify-content: center;
    align-items: center;
    overflow: auto;
}

.modal-content {
    background-color: #0d121e;
    padding: 25px;
    border-radius: 10px;
    width: 100%;
    max-width: 550px;
    max-height: 95vh;
    overflow-y: scroll;
    box-sizing: border-box;
    animation: fadeIn 0.4s ease-in-out;
    box-shadow: 0px 5px 15px rgba(0,0,0,0.4);
    scrollbar-width: none; 
}
.modal-content::-webkit-scrollbar {
    display: none;
}

.modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-bottom: 2px solid #333;
    padding-bottom: 10px;
}

.modal-header h2 {
    margin: 0;
    font-size: 1.5rem;
    color: white;
}

.close {
    font-size: 1.5rem;
    cursor: pointer;
    color: #aaa;
}

.close:hover {
    color: #ff5c5c;
}

.modal-body form {
    display: flex;
    flex-direction: column;
    gap: 15px;
    margin-top: 20px;
}

.modal-body label {
    font-weight: bold;
    color: white;
}

.modal-body input,
.modal-body select {
    width: 100%;
    padding: 10px;
    border-radius: 5px;
    border: 1px solid #333;
    background-color: #212b40;
    color: white;
}

.modal-body select option {
    background-color: #212b40;
    color: white;
}

.modal-footer {
    display: flex;
    justify-content: flex-end;
    gap: 10px;
    border-top: 2px solid #333;
    padding-top: 10px;
    margin-top: 15px;
}

.btn-sit-in,
.btn-close {
    padding: 10px 15px;
    border: none;
    border-radius: 5px;
    font-size: 1rem;
    cursor: pointer;
}

.btn-sit-in {
    background-color: #007bff;
    color: white;
}

.btn-sit-in:hover {
    background-color: #0056b3;
}

.btn-close {
    background-color: #f44336;
    color: white;
}

.btn-close:hover {
    background-color: #d32f2f;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(-20px); }
    to   { opacity: 1; transform: translateY(0); }
}

@media (max-height: 600px) {
    .modal-content {
        max-height: 90vh;
        overflow-y: auto;
    }
}
</style>

<div class="modal" id="sitInModal">
    <div class="modal-content">
        <div class="modal-header">
            <h2>Sit-In Form</h2>
            <span class="close" onclick="document.getElementById('sitInModal').remove();">&times;</span>
        </div>
        <div class="modal-body">
            <form action="admin-current.php" method="POST">
                <input type="hidden" name="id_no" value="<?= htmlspecialchars($id) ?>">
                <input type="hidden" name="name" value="<?= htmlspecialchars($fullname) ?>">
                <input type="hidden" name="remaining_sessions" value="<?= htmlspecialchars($sessions) ?>">
                <input type="hidden" name="course" value="<?= htmlspecialchars($course) ?>">
                <input type="hidden" name="year_level" value="<?= htmlspecialchars($year) ?>">

                <label>ID No:</label>
                <input type="text" value="<?= htmlspecialchars($id) ?>" readonly>

                <label>Name:</label>
                <input type="text" value="<?= htmlspecialchars($fullname) ?>" readonly>

                <label>Course | Year:</label>
                <input type="text" value="<?= htmlspecialchars($course) ?> | <?= htmlspecialchars($year) ?>" readonly>

                <label>Remaining Sessions:</label>
                <input type="text" value="<?= htmlspecialchars($sessions) ?>" readonly>

                <label for="purpose">Purpose:</label>
                <select name="purpose" required>
                    <option value="">Select</option>
                    <option value="PHP Programming">PHP Programming</option>
                    <option value="Java Programming">Java Programming</option>
                    <option value="ASP.Net Programming">ASP.Net Programming</option>
                    <option value="C Programming">C Programming</option>
                    <option value="C# Programming">C# Programming</option>
                </select>

                <label for="lab_number">Lab Number:</label>
                <select name="lab_number" required>
                    <option value="">Select</option>
                    <option value="524">524</option>
                    <option value="526">526</option>
                    <option value="528">528</option>
                    <option value="530">530</option>
                    <option value="540">540</option>
                    <option value="Mac Laboratory">Mac Laboratory</option>
                </select>

                <div class="modal-footer">
                    <button type="button" class="btn-close" onclick="document.getElementById('sitInModal').remove();">Cancel</button>
                    <button type="submit" class="btn-sit-in">Submit Sit-In</button>
                </div>
            </form>
        </div>
    </div>
</div>
