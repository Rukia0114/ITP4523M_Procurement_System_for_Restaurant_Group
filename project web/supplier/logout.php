<?php
session_start();

// Check if the user is logged in
if (isset($_SESSION['userID']) && isset($_SESSION['companyName'])) {
  // Display a confirmation message to the user
  echo <<<EOD
<script>
    if (confirm("Are you sure you want to logout?")) {
        window.location.href = "logout_process.php";
    } else {
        window.history.back();
    }
</script>
EOD;
} else {
  // Redirect the user to the login page if not logged in
  header("Location: ../index.php");
  exit();
}
?>


