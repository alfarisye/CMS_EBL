
    <?php
    set_time_limit(0);
    $servername_wb  = "192.168.35.10";
    $database_wb = "weighbridge";
    $username_wb = "developer";
    $password_wb = "H4rd2uess?!";

    $servername_cms  = "192.168.13.17";
    $database_cms = "cms-ebl-prd";
    $username_cms = "mysqladmin";
    $password_cms = "H1tH4snur#88";

    try {
        // Connect to weighbridge database
        $conn_wb = new PDO("mysql:host=$servername_wb;dbname=$database_wb", $username_wb, $password_wb);
        $conn_wb->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Connect to cms database
        $conn_cms = new PDO("mysql:host=$servername_cms;dbname=$database_cms", $username_cms, $password_cms);
        $conn_cms->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $sql_wb = "SELECT * FROM inquiry_transfer ORDER BY Ticket_Code DESC LIMIT 5000";
        $stmt_wb = $conn_wb->prepare($sql_wb);
        $stmt_wb->execute();
        $data_wb = $stmt_wb->fetchAll(PDO::FETCH_ASSOC);

        foreach ($data_wb as $row) {
            $sql_cms = "SELECT * FROM inquiry_transfer WHERE Ticket_Code = :ticket_code";
            $stmt_cms = $conn_cms->prepare($sql_cms);
            $stmt_cms->bindParam(':ticket_code', $row['Ticket_Code']);
            $stmt_cms->execute();
            $result_cms = $stmt_cms->fetchAll(PDO::FETCH_ASSOC);

            if (count($result_cms) == 0) {
                if (empty($row['Weigh_In'])) {
                    $row['Weigh_In'] = 0;
                } else if (empty($row['Weigh_Out'])) {
                    $row['Weigh_Out'] = 0;
                } else if (empty($row['Deduction_Weigh'])) {
                    $row['Deduction_Weigh'] = 0;
                } else if (empty($row['Net_Weigh'])) {
                    $row['Net_Weigh'] = 0;
                } else if (empty($row['STAT'])) {
                    $row['STAT'] = 0;
                } else if (empty($row['Flag'])) {
                    $row['Flag'] = 0;
                } else if (empty($row['Sync'])) {
                    $row['Sync'] = 0;
                }

                $sql_insert = "INSERT INTO inquiry_transfer (DO,Ticket_Code,Code_Number,Weigh_In,Weigh_Out,Deduction_Weigh,
                Net_Weigh,Driver_Id,Driver_Name,Product_Code,Product_Name,Destination_Code,Destination_Description,Transporter_Id,Transporter_Description,
                Remarks,User_Id,Plate_Number,Supplier_Id,Supplier_Name,Transfer_Type,Posting_Date,Posting_Time,Depart_Date,Depart_Time,Storage,Weigh,Seal,
                Crusher_Code,Crusher_Description,Transfer_Code,Shift,Modified_By,UPDT,USR,STAT,Block_Code,Block_Description,Flag,Jetty,Sync)
                VALUES (:do, :ticket_code, :code_number, :weigh_in, :weigh_out, :deduction_weigh, :net_weigh, :driver_id, :driver_name, :product_code,
                :product_name, :Destination_Code, :Destination_Description, :Transporter_Id, :Transporter_Description,
                :Remarks, :User_Id, :Plate_Number, :Supplier_Id, :Supplier_Name,:Transfer_Type, :Posting_Date, :Posting_Time,:Depart_Date,:Depart_Time,:Storage,:Weigh,:Seal,
                :Crusher_Code,:Crusher_Description,:Transfer_Code,:Shift,:Modified_By,:UPDT,:USR,:STAT,:Block_Code,:Block_Description,:Flag,:Jetty,:Sync)";
                // $hasil = $conn_cms->prepare($sql_insert);
                // $hasil->execute();
            }
            echo "<br> Code : " . $row["Ticket_Code"] . "; ---" . $row["UPDT"] . "; ------->  Add Data Successfully   ! <br>";
        }
        echo "BJ Selesai ----------------";
    } catch (Exception $e) {
        echo "Error : " . $e->getMessage();
    }


    ?>