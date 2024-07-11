<?php
include 'config.php';


if (isset($_POST['save-neuro'])) {
    //Response from Patient Details
    $patient_id = $_POST['patient_id'];
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $email = $_POST['email'];
    $date_of_birth = $_POST['date_of_birth'];

    //Form Response from Pain INventory
    $pain_clinic_rating = $_POST['pain_clinic_rating'];
    $pain_worst_rating = $_POST['pain_worst_rating'];
    $pain_least_rating = $_POST['pain_least_rating'];
    $pain_average_rating = $_POST['pain_average_rating'];
    $pain_right_now_rating = $_POST['pain_right_now_rating'];
    $effect_on_activity = $_POST['effect_on_activity'];
    $effect_on_mood = $_POST['effect_on_mood'];
    $effect_on_walking = $_POST['effect_on_walking'];
    $effect_on_work = $_POST['effect_on_work'];
    $effect_on_people = $_POST['effect_on_people'];
    $effect_on_sleep = $_POST['effect_on_sleep'];
    $effect_on_enjoyment = $_POST['effect_on_enjoyment'];

    if (
        is_numeric($pain_worst_rating) &&
        is_numeric($pain_least_rating) &&
        is_numeric($pain_average_rating) &&
        is_numeric($pain_right_now_rating) &&
        is_numeric($effect_on_activity) &&
        is_numeric($effect_on_mood) &&
        is_numeric($effect_on_walking) &&
        is_numeric($effect_on_work) &&
        is_numeric($effect_on_people) &&
        is_numeric($effect_on_sleep) &&
        is_numeric($effect_on_enjoyment)
    ) {
        // Calculate rating
        $ratingScore = $pain_worst_rating + $pain_least_rating + $pain_average_rating + $pain_right_now_rating + $effect_on_activity + $effect_on_mood + $effect_on_walking + $effect_on_work + $effect_on_people + $effect_on_sleep + $effect_on_enjoyment;
        // Check if email already exists
        $verify_email = "SELECT COUNT(*) FROM Patients WHERE email = :email and patient_id != :patient_id";
        $email_stmt = $conn->prepare($verify_email);
        $email_stmt->bindParam(':email', $email);
        $email_stmt->bindParam(':patient_id', $patient_id);
        $email_stmt->execute();
        $email_in_use = $email_stmt->fetchColumn();

        if ($email_in_use) {
            echo "<script>toastr.error('Email Address already registered please try a different Email');</script>";
        } else {
            try {
                $sql = "UPDATE Patients SET first_name=:first_name, last_name=:last_name, email=:email, date_of_birth=:date_of_birth, ratingScore=:ratingScore WHERE patient_id=:patient_id";
                $stmt = $conn->prepare($sql);
                $stmt->bindParam(':first_name', $first_name);
                $stmt->bindParam(':last_name', $last_name);
                $stmt->bindParam(':email', $email);
                $stmt->bindParam(':date_of_birth', $date_of_birth);
                $stmt->bindParam(':ratingScore', $ratingScore);
                $stmt->bindParam(':patient_id', $patient_id);
                $stmt->execute();

                $painSql = "UPDATE PatientPainDetails SET patient_id=?, pain_clinic_rating=?, pain_worst_rating=?, pain_least_rating=?, pain_average_rating=?, pain_right_now_rating=?, effect_on_activity=?, effect_on_mood=?, effect_on_walking=?, effect_on_work=?, effect_on_people=?, effect_on_sleep=?, effect_on_enjoyment=? WHERE patient_id='$patient_id'";
                $stmt = $conn->prepare($painSql);
                $stmt->execute([
                    $patient_id, $pain_clinic_rating, $pain_worst_rating, $pain_least_rating, $pain_average_rating, $pain_right_now_rating,
                    $effect_on_activity, $effect_on_mood, $effect_on_walking, $effect_on_work,
                    $effect_on_people, $effect_on_sleep, $effect_on_enjoyment
                ]);
                echo "<script>toastr.success('Record Successfully Updated');</script>";
            } catch (PDOException $e) {
                // $error_message = $e->getMessage();
                // echo "<script>toastr.error('Error Occur: $error_message');</script>";
                echo "Connection failed: " . $e->getMessage();
            }
        }
    } else {
        echo "<script>toastr.error('Ensure your rating are in numerics');</script>";
    }
    exit();
}
$patient_id = $_GET['patient_id'];
// Query to fetch data based on email
$query = "SELECT * FROM Patients WHERE patient_id = :patient_id";
$stmt = $conn->prepare($query);
$stmt->bindParam(':patient_id', $patient_id);
$stmt->execute();
// Fetch the row
$row = $stmt->fetch(PDO::FETCH_ASSOC);


// Query to fetch data based on email
$query = "SELECT * FROM PatientPainDetails WHERE patient_id = :patient_id";
$stmt = $conn->prepare($query);
$stmt->bindParam(':patient_id', $patient_id);
$stmt->execute();
// Fetch the row
$row2 = $stmt->fetch(PDO::FETCH_ASSOC);
// Today's date
$today = date("Y-m-d");
// Calculate difference between today and date of birth
$diff = date_diff(date_create($row["date_of_birth"]), date_create($today));
// Extract years from the difference
$patient_age = $diff->format('%y');
?>
<!DOCTYPE html>
<html lang="en">


<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Neuro Modulation</title>
    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />

</head>

<body>


    <nav class="navbar navbar-light justify-content-center fs-3 mb-2" style="background-color: #00ff5573;">
        The Walton Centre NHS Foundation
    </nav>

    <div class="container">
        <div class="text-center mb-4">
            <h3>Neuromodulation (Admin Dashboard)</h3>
            <p class="text-muted">Edit Patient Completed Form</p>
        </div>

        <div class="container d-flex justify-content-center">
            <form action="" method="post" style="width:50vw; min-width:300px;" id="neuro_form">
                <div class=" card mb-3">
                    <div class="card-header bg-green">Patient Details/Information</div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col">
                                <label class="form-label">First Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="first_name" placeholder="First Name" value="<?php echo $row['first_name']; ?>" required>
                            </div>

                            <div class="col">
                                <label class="form-label">Surname <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="last_name" placeholder="Surname" value="<?php echo $row['last_name']; ?>" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email" class="form-control" name="email" placeholder="Email address" value="<?php echo $row['email']; ?>" required>
                        </div>

                        <div class="row mb-3">
                            <div class="col">
                                <label class="form-label">Date of Birth <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="date_of_birth" name="date_of_birth" max="<?php echo date('Y-m-d'); ?>" placeholder="Date of Birth" value="<?php echo $row['date_of_birth']; ?>" required>
                            </div>

                            <div class="col">
                                <label class="form-label">Patient Age <span class="text-danger"></span></label>
                                <input type="text" class="form-control" id="age" placeholder="Age" value="<?php echo $patient_age; ?>" disabled>
                            </div>
                        </div>

                    </div>
                </div>






                <div class="card mb-3">
                    <div class="card-header bg-green">Brief Pain Inventory (BPI)</div>
                    <div class="card-body">
                        <div class="form-group">
                            <label for="pain_clinic_rating">How much relief have pain treatments or medicaTons FROM THIS CLINIC provided? <span class="text-danger">On a scale of 1 to 100</span></label>
                            <select class="form-control" name="pain_clinic_rating" name="pain_clinic_rating">
                                <option value="100">100%</option>
                                <option value="75">75%</option>
                                <option value="50">50%</option>
                                <option value="25">25%</option>
                                <option value="0">0%</option>
                            </select>


                        </div>
                        <div class="form-group">
                            <label for="pain_worst_rating">Please rate your pain based on the number that best describes your pain at it’s WORST in the past week. (0-10)<span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="pain_worst_rating" name="pain_worst_rating" min="0" max="10" value="<?php echo $row2['pain_worst_rating']; ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="pain_least_rating">Please rate your pain based on the number that best describes your pain at it’s LEAST in the past week. (0-10)<span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="pain_least_rating" name="pain_least_rating" min="0" max="10" value="<?php echo $row2['pain_least_rating']; ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="pain_average_rating">Please rate your pain based on the number that best describes your pain on the Average. (0-10)<span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="pain_average_rating" name="pain_average_rating" min="0" max="10" value="<?php echo $row2['pain_average_rating']; ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="pain_right_now_rating">Please rate your pain based on the number that best describes your pain that tells how much pain you have RIGHT NOW. (0-10)<span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="pain_right_now_rating" name="pain_right_now_rating" min="0" max="10" value="<?php echo $row2['pain_right_now_rating']; ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="effect_on_activity">Based on the number that best describes how during the past week pain has INTERFERED with your: General AcTvity. (0-10)<span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="effect_on_activity" name="effect_on_activity" min="0" max="10" onfocusout="calculate_total_score()" value="<?php echo $row2['effect_on_activity']; ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="effect_on_mood">Based on the number that best describes how during the past week pain has
                                INTERFERED with your: Mood. (0-10)<span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="effect_on_mood" name="effect_on_mood" min="0" max="10" onfocusout="calculate_total_score()" value="<?php echo $row2['effect_on_mood']; ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="effect_on_walking">Based on the number that best describes how during the past week pain has
                                INTERFERED with your: Walking ability. (0-10)<span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="effect_on_walking" name="effect_on_walking" min="0" max="10" onfocusout="calculate_total_score()" value="<?php echo $row2['effect_on_walking']; ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="effect_on_work">Based on the number that best describes how during the past week pain has INTERFERED with your: Normal work (includes work both outside the home and housework). (0-10)<span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="effect_on_work" name="effect_on_work" min="0" max="10" onfocusout="calculate_total_score()" value="<?php echo $row2['effect_on_work']; ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="effect_on_people">Based on the number that best describes how during the past week pain has INTERFERED with your: RelaTonships with other people. (0-10)<span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="effect_on_people" name="effect_on_people" min="0" max="10" onfocusout="calculate_total_score()" value="<?php echo $row2['effect_on_people']; ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="effect_on_sleep">Based on the number that best describes how during the past week pain has
                                INTERFERED with your: Sleep. (0-10)<span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="effect_on_sleep" name="effect_on_sleep" min="0" max="10" onfocusout="calculate_total_score()" value="<?php echo $row2['effect_on_sleep']; ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="effect_on_enjoyment">Based on the number that best describes how during the past week pain has INTERFERED with your: Enjoyment of life. (0-10)<span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="effect_on_enjoyment" name="effect_on_enjoyment" min="0" max="10" onfocusout="calculate_total_score()" value="<?php echo $row2['effect_on_enjoyment']; ?>" required>
                        </div>
                    </div>
                </div>

                <div class="card mb-3">
                    <div class="card-header bg-green" id="see_total">Total: <?php echo $row['ratingScore']; ?></div>
                </div>

                <div>
                    <input type="hidden" class="btn btn-success" name="save-neuro" value="Create">
                    <input type="hidden" class="btn btn-success" name="patient_id" value="<?php echo $patient_id; ?>">
                    <input type="submit" class="btn btn-success" name="Submit" value="Save">
                    <a href="index.php" class="btn btn-danger">Cancel</a>
                </div>
            </form>
        </div>
    </div>

    <div id="notification"></div>


    <script>
        function calculate_total_score() {
            var pain_worst_rating = parseInt($("#pain_worst_rating").val()) || 0;
            var pain_least_rating = parseInt($("#pain_least_rating").val()) || 0;
            var pain_average_rating = parseInt($("#pain_average_rating").val()) || 0;
            var pain_right_now_rating = parseInt($("#pain_right_now_rating").val()) || 0;
            var effect_on_activity = parseInt($("#effect_on_activity").val()) || 0;
            var effect_on_mood = parseInt($("#effect_on_mood").val()) || 0;
            var effect_on_walking = parseInt($("#effect_on_walking").val()) || 0;
            var effect_on_work = parseInt($("#effect_on_work").val()) || 0;
            var effect_on_people = parseInt($("#effect_on_people").val()) || 0;
            var effect_on_sleep = parseInt($("#effect_on_sleep").val()) || 0;
            var effect_on_enjoyment = parseInt($("#effect_on_enjoyment").val()) || 0;
            var total = pain_worst_rating + pain_least_rating + pain_average_rating + pain_right_now_rating + effect_on_activity + effect_on_mood + effect_on_walking + effect_on_work + effect_on_people + effect_on_sleep + effect_on_enjoyment;
            $(" #see_total").html("Total Score: " + total);

        }

        $("#date_of_birth").change(function() {
            var user_dob = new Date($(this).val());
            var get_date = new Date();
            var age = get_date.getFullYear() - user_dob.getFullYear();
            var m = get_date.getMonth() - user_dob.getMonth();
            if (m < 0 || (m === 0 && get_date.getDate() < user_dob.getDate())) {
                age--;
            }
            $("#age").val(age);
        });



        $(document).ready(function() {
            $('#neuro_form').on('submit', function(e) {
                e.preventDefault(); // Prevent the form from submitting the traditional way
                $.ajax({
                    url: 'edit.php', // URL to the PHP script
                    type: 'POST',
                    data: $(this).serialize(), // Serialize the form data
                    success: function(response) {
                        $(" #notification").html(response);
                    },
                    error: function(xhr, status, error) {
                        toastr.error('An error occurred: ' + error); // Display error notification
                    }
                });
            });
        });
    </script>

    <!-- Bootstrap start -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" ></script>
    <!-- Bootstrap End -->
</body>

</html>