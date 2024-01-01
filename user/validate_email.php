<?php
include '../connection.php';
include '../vendor/autoload.php';

// Tạo một mảng phản hồi chung
$response = array("status" => "", "message" => "", "data" => null);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Đọc dữ liệu JSON từ body của yêu cầu
    $jsonData = file_get_contents("php://input");

    // Chuyển dữ liệu JSON thành mảng
    $data = json_decode($jsonData, true);

    // kiểm tra xem mảng $data có các khóa hợp lệ không
    if ($data && isset($data["user_email"])) {
        $userEmail = $data["user_email"];

        $sqlQuery = "SELECT * FROM users_table WHERE user_email='$userEmail'";
        $resultOfQuery = $connectNow->query($sqlQuery);
        // kiểm tra Lấy dữ liệu thành công không
        if ($resultOfQuery->num_rows <= 0) {
            http_response_code(200);
            $response["status"] = "success";
            $response["message"] = "SignUp successfully.";
        } else {
            http_response_code(200);
            $response["status"] = "error";
            $response["message"] = "Account already exists.";
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
