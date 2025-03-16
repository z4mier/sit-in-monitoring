<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laboratory Rules and Regulations - University of Cebu</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@100..900&display=swap');
        body {
            font-family: 'Inter', sans-serif;
            background-color: #0d121e;
            margin: 0;
            padding: 0;
        }
        .header {
            color: white;
            padding: 20px 20px;
            text-align: center;
        }
        .logos {
            display: flex;
            justify-content: center;
            gap: 20px; /* Adds spacing between logos */
            margin-bottom: 20px;
        }
        .logos img {
            width: 95px; /* Adjust the size as needed */
            height: auto;
        }
        .container {
            max-width: 800px;
            margin: 30px auto;
            display: flex;
            flex-direction: column;
            gap: 20px; /* Adds spacing between cards */
        }
        .card {
            border: 2px solid white; /* Adds a white border */
            box-shadow: 0 0 10px rgba(255, 255, 255, 0.3); /* Optional, adds a soft glow */
            color: white;
            padding: 20px;
            border-radius: 20px;
        }
        .card h3 {
            color: white;
            font-size: 25px;
            text-align: center;
        }
        .card p, .card ul, .card li {
            text-align: justify;
            line-height: 1.6;
        }
        ul {
            padding-left: 20px;
            list-style-type: disc; /* Displays bullets */
        }
    </style>
</head>
<body>
    <div class="header">
        <!-- Logos Section -->
        <h1>CCS Sit-In Monitoring System</h1>
    </div>
    <?php include 'includes/sidebar.php'; ?>
    <div class="container">
        <!-- Lab Rules Card -->
        <div class="card">
            <div class="logos">
                <img src="assets/uc.png" alt="UC Logo">
                <img src="assets/ccs.png" alt="CCS Logo">
            </div>
            <h3>LAB RULES</h3>
            <p>To avoid embarrassment and maintain camaraderie with your friends and superiors at our laboratories, please observe the following:</p>
            <ul>
                <li>Maintain silence, proper decorum, and discipline inside the laboratory. Mobile phones, walkmans, and other personal equipment must be switched off.</li>
                <li>Games are not allowed inside the lab. This includes computer-related games, card games, and other games that may disturb the operation of the lab.</li>
                <li>Surfing the Internet is allowed only with the permission of the instructor. Downloading and installing software are strictly prohibited.</li>
                <li>Getting access to websites not related to the course (especially pornographic and illicit sites) is strictly prohibited.</li>
                <li>Deleting computer files and changing the computer setup is a major offense.</li>
                <li>Observe computer time usage carefully. A fifteen-minute allowance is given for each use. Otherwise, the unit will be given to those who wish to "sit-in".</li>
                <li>Observe proper decorum while inside the laboratory.
                    <ol>
                        <li>Do not enter the lab unless the instructor is present.</li>
                        <li>All bags and similar items must be deposited at the counter.</li>
                        <li>Follow the seating arrangement of your instructor.</li>
                        <li>At the end of class, all software programs must be closed.</li>
                        <li>Return all chairs to their proper places after use.</li>
                    </ol>
                </li>
                <li>Chewing gum, eating, drinking, smoking, and vandalism are prohibited inside the lab.</li>
                <li>Anyone causing continual disturbances will be asked to leave the lab.</li>
                <li>Persons exhibiting hostile or threatening behavior, such as yelling, swearing, or disregarding requests made by lab personnel, will be asked to leave the lab.</li>
                <li>For serious offenses, lab personnel may call the Civil Security Office (CSU) for assistance.</li>
                <li>Any technical problem or difficulty must be addressed to the lab supervisor, student assistant, or instructor immediately.</li>
            </ul>
        </div>

        <!-- Disciplinary Action Card -->
        <div class="card">
            <h3>DISCIPLINARY ACTION</h3>
            <p><strong>First Offense:<br></strong> The Head, Dean, or OIC recommends to the Guidance Center a suspension from classes for the offender.</p>
            <p><strong>Second and Subsequent Offenses:<br></strong> A recommendation for a heavier sanction will be endorsed to the Guidance Center.</p>
        </div>
    </div>
</body>
</html>
