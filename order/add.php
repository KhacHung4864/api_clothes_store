<?php
include '../connection.php';

// Tạo một mảng phản hồi chung
$response = array("status" => "", "message" => "", "data" => null);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Đọc dữ liệu JSON từ body của yêu cầu
    $jsonData = file_get_contents("php://input");

    // Chuyển dữ liệu JSON thành mảng
    $data = json_decode($jsonData, true);

    // kiểm tra xem mảng $data có các khóa hợp lệ không
    if (
        $data && isset($data["user_id"])
        && isset($data["selectedItems"])
        && isset($data["deliverySystem"])
        && isset($data["paymentSystem"])
        && isset($data["note"])
        && isset($data["totalAmount"])
        && isset($data["image"])
        && isset($data["status"])
        && isset($data["shipmentAddress"])
        && isset($data["phoneNumber"])
        && isset($data["imageFile"])
    ) {
        $userID = $data["user_id"];
        $selectedItems = $data["selectedItems"];
        $deliverySystem = $data["deliverySystem"];
        $paymentSystem = $data["paymentSystem"];
        $note = $data["note"];
        $totalAmount = $data["totalAmount"];
        $image = $data["image"];
        $status = $data["status"];
        $shipmentAddress = $data["shipmentAddress"];
        $phoneNumber = $data["phoneNumber"];
        $imageFileBase64 = $data["imageFile"];

        $sqlQuery = "INSERT INTO orders_table SET user_id='$userID', selectedItems='$selectedItems', deliverySystem='$deliverySystem', paymentSystem='$paymentSystem', note='$note', totalAmount='$totalAmount', image='$image', status='$status', shipmentAddress='$shipmentAddress', phoneNumber='$phoneNumber'";

        $resultOfQuery = $connectNow->query($sqlQuery);
        // kiểm tra Lấy dữ liệu thành công không
        if ($resultOfQuery) {

            //upload image to server
            $imageFileOfTransactionProof = base64_decode($imageFileBase64);
            file_put_contents("../transactions_proof_images/" . $image, $imageFileOfTransactionProof);
            http_response_code(200);
            $response["status"] = "success";
            $response["message"] = "Add item Successfully.";
        } else {
            http_response_code(500);
            $response["status"] = "error"; 
            $response["message"] = "Error inserting data.";
        }
    } else {
        // Trả về mã lỗi 400 Bad Request
        http_response_code(400);
        $response["status"] = "error";
        $response["message"] = "The JSON data is invalid or missing required information.";
    }
} else {
    // Trả về mã lỗi 405 Method Not Allowed
    http_response_code(405);
    $response["status"] = "error";
    $response["message"] = "Invalid request. Please use the POST method.";
}

// Gửi phản hồi JSON
echo json_encode($response);

// Đóng kết nối cơ sở dữ liệu
$connectNow->close();
