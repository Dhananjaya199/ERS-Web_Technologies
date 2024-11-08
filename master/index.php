<?php
ob_start();
if (!isset($_SESSION)) {
    session_start();
}

if (!isset($_SESSION['role']) || $_SESSION['role'] != "Admin_Master") {
    header("location:../login.php");
    exit();
}
require_once("../config/connect.php");
require_once("../config/adminName.php");
require_once("../config/postSender.php");
?>


<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link
            rel="shortcut icon"
            href="../assets/img/logo/ERS_logo_icon.ico"
            type="image/x-icon"/>
    <title>ERS | Master Admin</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <link
      rel="stylesheet"
      href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" />
    <link rel="stylesheet" type="text/css" href="../assets/css/master.css" />

</head>
<body class="bg-gray-200 sm:text-xs xl:text-sm 2xl:text-base">

<?php
    $rpath = "";
    require_once("navbar.php")
?>



<div id="nextSibling" class="transition-all ml-[300px] h-auto grid py-20">
    <div class="card <?php if (isset($_GET['page']) && ($_GET['page'] === "viewReg")) echo "overflow-y-auto" ?>">
        <?php
        // print_r($_POST);
        // echo "<br>";
        // print_r($_GET);
        if (isset($_GET['page'])) {
            if ($_GET['page'] === "listAdmins") {
                include("list_admins.php");
            }
            else if ($_GET['page'] === "viewReg") {
                include("viewReg.php");
            }
            else if ($_GET['page'] === "viewAdmin") {
               if(isset($_POST['adminId']))
                    include("viewAdmin.php");
               else
                   header("Location:index.php?page=listAdmins");
            }
            else if ($_GET['page'] === "editAdmin") {
                if (isset($_POST['editAdminId']))
                    include("editAdmin.php");
                else
                    header("Location:index.php?page=listAdmins");
            }else if ($_GET['page'] === "profile") {
                include("../config/profile.php");
            } else if ($_GET['page'] === "updateProfile") {
                include("../config/updateProfile.php");
            }else if ($_GET['page'] === "pwdChg") {
                include("../login/pwd_change.php");
            }else if ($_GET['page'] === "addAdmin") {
                    include("add_admin.php");
            } else
                include("admin_dashboard.php");
        } else
            include("admin_dashboard.php");

        ?>
    </div>
    <?php
    $examQuery = "select * from exam_reg where status='registration';";
    $result = mysqli_query($con, $examQuery);
    if(mysqli_num_rows($result) > 0){
    ?>
    <p class="font-bold text-2xl p-5 border rounded-xl bg-white mx-24 mt-4">Summary Of Processing Exams</p>
    <?php

    while($exams = mysqli_fetch_assoc($result)){
    $currentExamId = $exams['exam_id'];

    $eligibleStudentsQuery = "Select * from exam_stud_index where exam_id=$currentExamId;";
    $eligibleStudentResult = mysqli_query($con, $eligibleStudentsQuery);
    $numOfElibleStudents = mysqli_num_rows($eligibleStudentResult);

    $registeredStudents = "select * from stud_exam_reg where exam_id=$currentExamId;";
    $registeredStudentsResult = mysqli_query($con, $registeredStudents);
    $numberOfRegisteredStudent = mysqli_num_rows($registeredStudentsResult);

    $numberOfNotRegStud = ($numOfElibleStudents - $numberOfRegisteredStudent);

    $registeredLevel1StudQuery = "Select * from stud_exam_reg where exam_id=$currentExamId and level=1;";
    $registeredLevel1StudResult = mysqli_query($con, $registeredLevel1StudQuery);
    $numOfRegisteredLevel1Stud = mysqli_num_rows($registeredLevel1StudResult);

    $registeredLevel2StudQuery = "Select * from stud_exam_reg where exam_id=$currentExamId and level=2;";
    $registeredLevel2StudResult = mysqli_query($con, $registeredLevel2StudQuery);
    $numOfRegisteredLevel2Stud = mysqli_num_rows($registeredLevel2StudResult);

    $registeredLevel3StudQuery = "Select * from stud_exam_reg where exam_id=$currentExamId and level=3;";
    $registeredLevel3StudResult = mysqli_query($con, $registeredLevel3StudQuery);
    $numOfRegisteredLevel3Stud = mysqli_num_rows($registeredLevel3StudResult);

    $registeredLevel4StudQuery = "Select * from stud_exam_reg where exam_id=$currentExamId and level=4;";
    $registeredLevel4StudResult = mysqli_query($con, $registeredLevel4StudQuery);
    $numOfRegisteredLevel4Stud = mysqli_num_rows($registeredLevel4StudResult);

    ?>
    <div class="">
        <?php
        if($numOfElibleStudents != null){
            ?>
    <div class="grid grid-cols-2 gap-2 mx-24">
        <div id="donutchart" class=" h-56 border rounded-xl  shadow-lg mt-4 overflow-hidden hover:cursor-pointer hover:shadow-2xl"></div>
        <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
        <script type="text/javascript">
            google.charts.load("current", {packages:["corechart"]});
            google.charts.setOnLoadCallback(drawChart);
            function drawChart() {
                var data = google.visualization.arrayToDataTable([
                    ['Status', 'No. of students'],
                    ['Registered',     <?php echo $numberOfRegisteredStudent;?>],
                    ['Not Registered',      <?php echo $numberOfNotRegStud;?>],
                ]);

                var options = {
                    title: "Academic year <?php echo $exams['academic_year']?>" ,
                    pieHole: 0.4,
                };

                var chart = new google.visualization.PieChart(document.getElementById('donutchart'));
                chart.draw(data, options);
            }
        </script>
        <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
        <script type="text/javascript">
            google.charts.load("current", {packages:['corechart']});
            google.charts.setOnLoadCallback(drawChart);
            function drawChart() {
                var data = google.visualization.arrayToDataTable([
                    ["Element", "Density", { role: "style" } ],
                    ["Level 1", <?php echo $numOfRegisteredLevel1Stud ?>, "#b87333"],
                    ["Level 2", <?php echo $numOfRegisteredLevel2Stud ?>, "silver"],
                    ["Level 3", <?php echo $numOfRegisteredLevel3Stud ?>, "gold"],
                    ["Level 4", <?php echo $numOfRegisteredLevel4Stud ?>, "color: #e5e4e2"]
                ]);

                var view = new google.visualization.DataView(data);
                view.setColumns([0, 1,
                    { calc: "stringify",
                        sourceColumn: 1,
                        type: "string",
                        role: "annotation" },
                    2]);

                var options = {
                    title: "Academic year <?php echo $exams['academic_year']?> (Registered students by levels)",
                    bar: {groupWidth: "95%"},
                    legend: { position: "none" },
                };
                var chart = new google.visualization.ColumnChart(document.getElementById("columnchart_values"));
                chart.draw(view, options);
            }
        </script>
        <div id="columnchart_values" class="h-56 border rounded-xl  shadow-lg mt-4 overflow-hidden hover:cursor-pointer hover:shadow-2xl"></div>
        <?php
            } else{?>
            <div class="p-10 h-56 border rounded-xl bg-white  shadow-lg mx-24 mt-4 overflow-hidden hover:cursor-pointer hover:shadow-2xl">
                <p class="bold text-xl text-center">Students are not assigned yet!</p>
                <p class="text-center text-gray-300">Academic year:  <?php echo $exams['academic_year'];?></p>
                <p class="text-center text-md text-blue-600 my-5">Inform to the student admin</p>
                <form action="send_email.php" onsubmit="showAlert(event)" method="POST">
                    <button type="submit" id="send_email" name="send_email" class="w-full text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">Send mail</button>
                </form>
            </div>
        <?php }}} ?>
    </div>
    </div>

<?php if (isset($_GET['error'])) { ?>
    <div class="exam-false fixed inset-0 bg-black bg-opacity-30 backdrop-blur-sm flex justify-center items-center">
        <form class="card h-40 w-1/2 flex flex-col items-center justify-around gap-7" action="index.php<?php echo (isset($_GET['page']))?"?page=".$_GET['page']:""?>" method="POST">
            <p class="text-center"><?php echo $_GET['error'] ?></p>
            <input class="btn fill-btn" type="submit" value="OK" name="ok">
        </form>
    </div>
<?php } elseif (isset($_GET['success'])) { ?>
    <div class="exam-false fixed inset-0 bg-black bg-opacity-30 backdrop-blur-sm flex justify-center items-center">
        <form class="card h-40 w-1/2 flex flex-col items-center justify-around gap-7" action="index.php<?php echo (isset($_GET['page']))?"?page=".$_GET['page']:""?>" method="POST">
            <p class="text-center text-green-700"><?php echo $_GET['success'] ?></p>
            <input class="btn fill-btn !bg-green-700" type="submit" value="OK" name="ok">
        </form>
    </div>
<?php } ?>
    <script src="sweetalert2.all.min.js"></script>
</body>
</html>