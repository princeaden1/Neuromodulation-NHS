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
            <h3>Admin Dashboard (For View, Edit, and Delete)</h3>
        </div>

        <div class="container">

            <a href="index.php" class="btn btn-dark mb-3">Add New</a>

            <table class="table table-bordered table-hover text-center" id="neuro-table">
                <thead class="table-dark">
                    <tr>
                        <th scope="col">S/N</th>
                        <th scope="col">First Name</th>
                        <th scope="col">Suname</th>
                        <th scope="col">Email</th>
                        <th scope="col">Age</th>
                        <th scope="col">Date of Birth</th>
                        <th scope="col">Total Score</th>
                        <th scope="col">Date of Submission</th>
                        <!-- <th scope="col">Action</th> -->
                    </tr>
                </thead>
                <tbody>
                    <?php
                    include 'config.php';
                    $stmt = $conn->prepare("SELECT  patient_id, first_name, last_name, email, date_of_birth,SubmissionDate, ratingScore FROM patients order by SubmissionDate desc");
                    $stmt->execute();
                    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    $i = 0;
                    if (count($rows) > 0) {
                        foreach ($rows as $row) {
                            $i++;

                            // Today's date
                            $today = date("Y-m-d");
                            // Calculate difference between today and date of birth
                            $diff = date_diff(date_create($row["date_of_birth"]), date_create($today));
                            // Extract years from the difference
                            $patient_age = $diff->format('%y');

                            // Create a DateTime object from the datetime string
                            $SubmissionDate = new DateTime($row["SubmissionDate"]);
                            // Format the datetime as per the desired format
                            $SubmissionDate = $SubmissionDate->format('d, F Y H:i');
                            $patient_id = $row["patient_id"];
                    ?>
                            <!-- <a href="view.php?id=<?php echo $patient_id; ?>"> -->
                            <tr data-href="view.php?patient_id=<?php echo $patient_id; ?>">
                                <td><?php echo $i ?></td>
                                <td><?php echo $row["first_name"] ?></td>
                                <td><?php echo $row["last_name"] ?></td>
                                <td><?php echo $row["email"] ?></td>
                                <td><?php echo $patient_age ?></td>
                                <td><?php echo $row["date_of_birth"] ?></td>
                                <td><?php echo $row["ratingScore"] ?></td>
                                <td><?php echo $SubmissionDate ?></td>
                                <!-- <td>
                                        <a href="edit.php?id=<?php echo $row["patient_id"] ?>" class="link-dark"><i class="fa-solid fa-pen-to-square fs-5 me-3"></i></a>
                                        <a href="delete.php?id=<?php echo $row["patient_id"] ?>" class="link-dark"><i class="fa-solid fa-trash fs-5"></i></a>
                                    </td> -->
                            </tr>
                            <!-- </a> -->
                    <?php
                        }
                    } else {
                        echo
                        '<div class="alert alert-warning alert-dismissible fade show" role="alert">No Record found in the database, Add items</div>';
                    }
                    ?>
                </tbody>
            </table>

        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            var table = document.getElementById("neuro-table");
            if (table != null) {
                table.addEventListener("click", function(e) {
                    var target = e.target;
                    while (target && target.tagName !== "TR") {
                        target = target.parentNode;
                    }
                    if (target) {
                        var link = target.getAttribute("data-href");
                        if (link) {
                            window.location.href = link;
                        }
                    }
                });
            }
        });
    </script>
    <!-- Bootstrap start -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4" crossorigin="anonymous"></script>
    <!-- Bootstrap End -->
</body>

</html>