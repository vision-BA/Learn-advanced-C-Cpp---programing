<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Library Member Registration</title>
    <style>
        body { font-family: Arial; margin: 20px; }
        form { max-width: 400px; }
        input { width: 100%; padding: 8px; margin: 5px 0 15px 0; }
        input[type="submit"] { background-color: #4CAF50; color: white; cursor: pointer; }
        .success { color: green; padding: 10px; background: #d4edda; border-radius: 4px; margin: 10px 0; }
        .error { color: red; padding: 10px; background: #f8d7da; border-radius: 4px; margin: 10px 0; }
    </style>
</head>
<body>
    <h2>Library Member Registration</h2>
    
    <form method="post" action="">
        <input type="text" name="memberID" placeholder="Member ID" required>
        <input type="text" name="firsName" placeholder="First Name" required>
        <input type="text" name="lastName" placeholder="Last Name" required>
        <input type="date" name="dateRegistered" required>
        <input type="text" name="phoneNumber" placeholder="Phone Number" required>
        <input type="submit" name="submit" value="Register Member">
    </form>

    <?php 
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    
    // Database connection
    $conn = new mysqli("localhost", "root", "Bright@2025", "library");
    
    // Check connection
    if($conn->connect_error){
        echo '<div class="error"><strong>Database Connection Failed:</strong> ' . $conn->connect_error . '</div>';
        exit();
    }
    
    // Process form when submit button is clicked
    if(isset($_POST["submit"])){
        // Get form data with validation
        $memberID = !empty($_POST["memberID"]) ? trim($_POST["memberID"]) : '';
        $firsName = !empty($_POST["firsName"]) ? trim($_POST["firsName"]) : '';
        $lastName = !empty($_POST["lastName"]) ? trim($_POST["lastName"]) : '';
        $dateRegistered = !empty($_POST["dateRegistered"]) ? trim($_POST["dateRegistered"]) : '';
        $phoneNumber = !empty($_POST["phoneNumber"]) ? trim($_POST["phoneNumber"]) : '';
        
        // Validate all fields are filled
        if(empty($memberID) || empty($firsName) || empty($lastName) || empty($dateRegistered) || empty($phoneNumber)){
            echo '<div class="error">⚠ All fields are required!</div>';
        } else {
            // Prepare SQL statement
            $sql = "INSERT INTO members(memberID, firsName, lastName, dateRegistered, phoneNumber) VALUES(?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            
            if($stmt === false){
                echo '<div class="error"><strong>SQL Prepare Error:</strong> ' . $conn->error . '</div>';
            } else {
                // Bind parameters
                $stmt->bind_param("sssss", $memberID, $firsName, $lastName, $dateRegistered, $phoneNumber);
                
                // Execute query
                if($stmt->execute()){
                    echo '<div class="success">✓ Member registered successfully!</div>';
                } else {
                    echo '<div class="error"><strong>Error inserting record:</strong> ' . $stmt->error . '</div>';
                }
                $stmt->close();
            }
        }
    }
    
    $conn->close();
    ?>
</body>
</html>