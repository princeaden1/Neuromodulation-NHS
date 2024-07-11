<?php
include 'config.php';

if (isset($_POST['trash'])) {
    $patient_id = $_GET['patient_id'];
    try {
        $sql = "DELETE FROM PatientPainDetails WHERE patient_id=:patient_id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':patient_id', $patient_id);
        $stmt->execute();

        $sql = "DELETE FROM Patients WHERE patient_id=:patient_id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':patient_id', $patient_id);
        $stmt->execute();


        echo "<script>toastr.success('Record Successfully Deleted');</script>";
    } catch (PDOException $e) {
        echo "Connection failed: " . $e->getMessage();
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

if (!$row) {
    header("Location: admin.php"); // Redirect to another file
    exit();
}
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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" >
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"  />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

</head>

<body>


    <nav class="navbar navbar-light justify-content-center fs-3 mb-2" style="background-color: #00ff5573;">
        The Walton Centre NHS Foundation
    </nav>

    <div class="container">
        <div class="text-center mb-4">
            <h3>Neuromodulation (Admin Dashboard)</h3>
            <p class="text-muted">View Submitted Form</p>
        </div>

        <div class="container d-flex justify-content-center">
            <form action="" method="post" style="width:50vw; min-width:300px;" id="neuro_form">
                <div class=" card mb-3">
                    <div class="card-header bg-green">Patient Details/Information</div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col">
                                <label class="form-label">First Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="first_name" placeholder="First Name" value="<?php echo $row['first_name']; ?>" readonly>
                            </div>

                            <div class="col">
                                <label class="form-label">Surname <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="last_name" placeholder="Surname" value="<?php echo $row['last_name']; ?>" readonly>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email" class="form-control" name="email" placeholder="Email address" value="<?php echo $row['email']; ?>" readonly>
                        </div>

                        <div class="row mb-3">
                            <div class="col">
                                <label class="form-label">Date of Birth <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="date_of_birth" name="date_of_birth" max="<?php echo date('Y-m-d'); ?>" placeholder="Date of Birth" value="<?php echo $row['date_of_birth']; ?>" readonly>
                            </div>

                            <div class="col">
                                <label class="form-label">Patient Age <span class="text-danger"></span></label>
                                <input type="text" class="form-control" id="age" placeholder="Age" value="<?php echo $patient_age; ?>" readoly>
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
                            <input type="number" class="form-control" id="pain_worst_rating" name="pain_worst_rating" min="0" max="10" value="<?php echo $row2['pain_worst_rating']; ?>" readonly>
                        </div>
                        <div class="form-group">
                            <label for="pain_least_rating">Please rate your pain based on the number that best describes your pain at it’s LEAST in the past week. (0-10)<span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="pain_least_rating" name="pain_least_rating" min="0" max="10" value="<?php echo $row2['pain_least_rating']; ?>" readonly>
                        </div>
                        <div class="form-group">
                            <label for="pain_average_rating">Please rate your pain based on the number that best describes your pain on the Average. (0-10)<span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="pain_average_rating" name="pain_average_rating" min="0" max="10" value="<?php echo $row2['pain_average_rating']; ?>" readonly>
                        </div>
                        <div class="form-group">
                            <label for="pain_right_now_rating">Please rate your pain based on the number that best describes your pain that tells how much pain you have RIGHT NOW. (0-10)<span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="pain_right_now_rating" name="pain_right_now_rating" min="0" max="10" value="<?php echo $row2['pain_right_now_rating']; ?>" readonly>
                        </div>
                        <div class="form-group">
                            <label for="effect_on_activity">Based on the number that best describes how during the past week pain has INTERFERED with your: General AcTvity. (0-10)<span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="effect_on_activity" name="effect_on_activity" min="0" max="10" onfocusout="calculate_total_score()" value="<?php echo $row2['effect_on_activity']; ?>" readonly>
                        </div>
                        <div class="form-group">
                            <label for="effect_on_mood">Based on the number that best describes how during the past week pain has
                                INTERFERED with your: Mood. (0-10)<span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="effect_on_mood" name="effect_on_mood" min="0" max="10" onfocusout="calculate_total_score()" value="<?php echo $row2['effect_on_mood']; ?>" readonly>
                        </div>
                        <div class="form-group">
                            <label for="effect_on_walking">Based on the number that best describes how during the past week pain has
                                INTERFERED with your: Walking ability. (0-10)<span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="effect_on_walking" name="effect_on_walking" min="0" max="10" onfocusout="calculate_total_score()" value="<?php echo $row2['effect_on_walking']; ?>" readonly>
                        </div>
                        <div class="form-group">
                            <label for="effect_on_work">Based on the number that best describes how during the past week pain has INTERFERED with your: Normal work (includes work both outside the home and housework). (0-10)<span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="effect_on_work" name="effect_on_work" min="0" max="10" onfocusout="calculate_total_score()" value="<?php echo $row2['effect_on_work']; ?>" readonly>
                        </div>
                        <div class="form-group">
                            <label for="effect_on_people">Based on the number that best describes how during the past week pain has INTERFERED with your: RelaTonships with other people. (0-10)<span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="effect_on_people" name="effect_on_people" min="0" max="10" onfocusout="calculate_total_score()" value="<?php echo $row2['effect_on_people']; ?>" readonly>
                        </div>
                        <div class="form-group">
                            <label for="effect_on_sleep">Based on the number that best describes how during the past week pain has
                                INTERFERED with your: Sleep. (0-10)<span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="effect_on_sleep" name="effect_on_sleep" min="0" max="10" onfocusout="calculate_total_score()" value="<?php echo $row2['effect_on_sleep']; ?>" readonly>
                        </div>
                        <div class="form-group">
                            <label for="effect_on_enjoyment">Based on the number that best describes how during the past week pain has INTERFERED with your: Enjoyment of life. (0-10)<span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="effect_on_enjoyment" name="effect_on_enjoyment" min="0" max="10" onfocusout="calculate_total_score()" value="<?php echo $row2['effect_on_enjoyment']; ?>" readonly>
                        </div>
                    </div>
                </div>

                <div class="card mb-3">
                    <div class="card-header bg-green" id="see_total">Total: <?php echo $row['ratingScore']; ?></div>
                </div>

                <div>
                    <a href="edit.php?patient_id=<?php echo $patient_id; ?>" class="btn btn-info">Edit</a>
                    <button id="dbuttonName" type="button" onclick="trash('<?php echo $patient_id; ?>')" class="btn btn-danger">Delete</button>
                </div>
            </form>
        </div>
    </div>

    <div id="notification"></div>
    <script>
        function trash(i) {
            Swal.fire({
                text: "Are you sure you want to delete patient form?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Delete',
                cancelButtonText: 'Cancel',
                customClass: {
                    confirmButton: 'btn btn-green',
                    cancelButton: 'btn btn-red'
                },
                buttonsStyling: false
            }).then((proceed) => {
                if (proceed) {
                    $("#dbuttonName").attr('disabled', true).html('<i class="fa fa-spinner fa-spin"></i>');

                    var form = $("#neuro_form")[0];
                    var formData = new FormData(form);
                    formData.append('trash', 1);
                    $.ajax({
                        url: "view.php?patient_id=" + i,
                        data: formData,
                        type: 'POST',
                        contentType: false,
                        processData: false,
                        success: function(d) {
                            $("#notification").html(d);
                            Swal.fire({
                                text: "Patient form successfully deleted, Press Ok to continuee.",
                                icon: 'info',
                                confirmButtonText: 'OK',
                                allowOutsideClick: true
                            }).then((result) => {
                                if (result.isConfirmed || result.isDismissed) {
                                    window.location.href = 'admin.php'; // Replace with your desired URL
                                }
                            });
                        },
                        error: function(jqKHR, textStatus, errorThrown) {
                            swal({
                                text: textStatus + ", Try Again!",
                                type: 'warning',
                                icon: 'warning'
                            });
                            $("#dbuttonName").html('<i class="fa fa-trash"></i>');
                        }
                    });
                } else {
                    swal.close();
                }
            });
        }
    </script>

    <!-- Bootstrap start -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Bootstrap End -->
</body>

</html>