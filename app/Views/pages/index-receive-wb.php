<html>

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Background Job WB to CMS</title>
</head>

<body>
    <?php
    set_time_limit(0);
    $servername = "192.168.35.10";
    $database = "weighbridge";
    $username = "developer";
    $password = "H4rd2uess?!";
    $koneksi_wb = mysqli_connect($servername, $username, $password, $database); // mengecek koneksi
    if (!$koneksi_wb) {
        die("Koneksi wb gagal: " . mysqli_connect_error());
    } else {
        echo 'koneksi berhasil';
    }
    echo "--------- Sinkronisasi Data inquiry_receive WB to CMS ---------";
    echo "<br>";
    $sql = "SELECT * FROM inquiry_receive ORDER BY Ticket_Code DESC LIMIT 5000"; //
    // $sql = "SELECT * FROM inquiry_receive ORDER BY Ticket_Code DESC LIMIT 5000 OFFSET 5000"; //
    // $sql = "SELECT *
    // FROM inquiry_receive A
    // WHERE A.Posting_Date BETWEEN '2023-01-08' AND '2023-01-15 '
    // ORDER BY A.Ticket_Code DESC"; // jika ketinggaalan data
    $data_wb = mysqli_query($koneksi_wb, $sql);
    // $row = mysqli_fetch_all($data_wb);
    // var_dump($row);
    // if ($data_wb->num_rows > 0) {
    //     while ($row = $data_wb->fetch_assoc()) {
    //         if ($row["Supplier_Name"] == null) {
    //             // echo 'ini data before : ' . $row["Supplier_Name"] . "<br>";
    //             $row["Supplier_Name"] = 0;
    //             echo 'Supplier_Name null : ' . $row["Supplier_Name"] . "<br>";
    //             echo "------------------------------------------------------- <br>";
    //         } else {
    //             echo 'Supplier_Name : ' . $row["Supplier_Name"] . "-" . $row["Ticket_Code"] . "<br>";
    //         }
    //     }
    // }
    mysqli_close($koneksi_wb); //closes a previously opened database connection

    $servername = "192.168.13.17";
    $database = "cms-ebl-prd";
    $username = "mysqladmin";
    $password = "H1tH4snur#88";
    $koneksi_cms = mysqli_connect($servername, $username, $password, $database); // mengecek koneksi
    if (!$koneksi_cms) {
        die("Koneksi cms gagal: " . mysqli_connect_error());
    }

    if ($data_wb->num_rows > 0) {
        while ($row = $data_wb->fetch_assoc()) {
            $sql = "SELECT * FROM inquiry_receive WHERE Ticket_Code ='" . $row["Ticket_Code"] . "' ";
            $result_cms = mysqli_query($koneksi_cms, $sql);
            if ($result_cms->num_rows == 0) {
                if ($row["Weigh_In"] == '' || $row["Weigh_In"] == null) {
                    $row["Weigh_In"] = 0;
                } else if ($row["Weigh_Out"] == '' || $row["Weigh_Out"] == null) {
                    $row["Weigh_Out"] = 0;
                } else if ($row["Deduction_Weigh"] == '' || $row["Deduction_Weigh"] == null) {
                    $row["Deduction_Weigh"] = 0;
                } else if ($row["Net_Weigh"] == '' || $row["Net_Weigh"] == null) {
                    $row["Net_Weigh"] = 0;
                } else if ($row["STAT"] == '' || $row["STAT"] == null) {
                    $row["STAT"] = 0;
                }
                $result = mysqli_query($koneksi_cms, "INSERT INTO inquiry_receive (DO,Ticket_Code,Code_Number,Weigh_In,Weigh_Out,Deduction_Weigh,
                Net_Weigh,Plate_Number,Driver_Id,Driver_Name,Receive_Type,Product_Code,Product_Name,
                Transporter_Id,Transporter_Description,Supplier_Id,Supplier_Name,Destination,Remarks,
                User_Id,Shift,Posting_Date,Posting_Time,Depart_Date,Depart_Time,Block_Code,Block_Description,
                Destination_Code,Destination_Description,Modified_By,USR,UPDT,STAT)
                VALUES('" . $row["DO"] . "',
                '" . $row["Ticket_Code"] . "',
                '" . $row["Code_Number"] . "',
                '" . $row["Weigh_In"] . "',
                '" . $row["Weigh_Out"] . "',
                '" . $row["Deduction_Weigh"] . "',
                '" . $row["Net_Weigh"] . "',
                '" . $row["Plate_Number"] . "',
                '" . $row["Driver_Id"] . "',
                '" . $row["Driver_Name"] . "',
                '" . $row["Receive_Type"] . "',
                '" . $row["Product_Code"] . "',
                '" . $row["Product_Name"] . "',
                '" . $row["Transporter_Id"] . "',
                '" . $row["Transporter_Description"] . "',
                '" . $row["Supplier_Id"] . "',
                '" . $row["Supplier_Name"] . "',
                '" . $row["Destination"] . "',
                '" . $row["Remarks"] . "',
                '" . $row["User_Id"] . "',
                '" . $row["Shift"] . "',
                '" . $row["Posting_Date"] . "',
                '" . $row["Posting_Time"] . "',
                '" . $row["Depart_Date"] . "',
                '" . $row["Depart_Time"] . "',
                '" . $row["Block_Code"] . "',
                '" . $row["Block_Description"] . "',
                '" . $row["Destination_Code"] . "',
                '" . $row["Destination_Description"] . "',
                '" . $row["Modified_By"] . "',
                '" . $row["USR"] . "',
                '" . $row["UPDT"] . "',
                '" . $row["STAT"] . "')");
                if (!$result) {
                    echo "<br> Code : " . $row["Ticket_Code"] . "; ---" . $row["UPDT"] . "; ------->  Add Data Failed ! <br>";
                    echo ("Error desc" . mysqli_error($koneksi_cms) . "<br>");
                } else {
                    echo "<br> Code : " . $row["Ticket_Code"] . "; ---" . $row["UPDT"] . "; ------->  Add Data Successfully   ! <br>";
                }
            } else {
                echo  $row["Ticket_Code"] . "; ---" . $row["UPDT"] . ";------->   Data Already Exist ! <br>";
            }
        }
    } else {
        echo "0 results";
    }
    mysqli_close($koneksi_cms); //closes a previously opened database connection

    ?>
</body>

</html>`