<?php
include '../connection.php';
include '../vendor/autoload.php';

use Firebase\JWT\JWT;

// Tạo một mảng phản hồi chung
$response = array("status" => "", "message" => "", "data" => null);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Đọc dữ liệu JSON từ body của yêu cầu
    $jsonData = file_get_contents("php://input");

    // Chuyển dữ liệu JSON thành mảng
    $data = json_decode($jsonData, true);

    // kiểm tra xem mảng $data có các khóa hợp lệ không
    if ($data && isset($data["admin_email"])  && isset($data["admin_password"])) {
        $adminEmail = $data["admin_email"];
        $adminPassword = $data["admin_password"];

        $sqlQuery = "SELECT * FROM admins_table WHERE admin_email = '$adminEmail' AND admin_password = '$adminPassword'";
        $resultOfQuery = $connectNow->query($sqlQuery);
        // kiểm tra Lấy dữ liệu thành công không
        if ($resultOfQuery -> num_rows > 0) {
        
            $adminRecord = array();
            while($rowFound = $resultOfQuery->fetch_assoc())
            {
                $adminRecord[] = $rowFound;
            }
            $token = JWT::encode($adminRecord, 'admin', 'HS256');
            http_response_code(200);
            $response["status"] = "success";
            $response["message"] = "Logged in successfully.";
            $response["token"] = $token;
        } else {
            http_response_code(500);
            $response["status"] = "error";
            $response["message"] = "account and password are incorrect.";
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
