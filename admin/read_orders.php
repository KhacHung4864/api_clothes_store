<?php

include '../connection.php';

include '../vendor/autoload.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

// Tạo một mảng phản hồi chung
$response = array("status" => "", "message" => "", "data" => null);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Đọc dữ liệu JSON từ body của yêu cầu
    $jsonData = file_get_contents("php://input");

    // Chuyển dữ liệu JSON thành mảng
    $data = json_decode($jsonData, true);

    // kiểm tra xem mảng $data có các khóa hợp lệ không
    if ($data) {
            $sqlQuery = "SELECT * FROM orders_table WHERE status = '0' ORDER BY dateTime DESC";  
            $resultOfQuery = $connectNow->query($sqlQuery);
            // kiểm tra Lấy dữ liệu thành công không
            if ($resultOfQuery->num_rows > 0) {
                // lấy từng bản ghi và đưa vào clothItemRecord 
                $orderRecord = array();
                while ($rowFound = $resultOfQuery->fetch_assoc()) {
                    $orderRecord[] = $rowFound;
                }
                //Gửi phản hồi
                http_response_code(200);
                $response["status"] = "success";
                $response["message"] = "successfully.";
                $response['data'] = $orderRecord;
            } else {
                // Trả về không có data nào 
                http_response_code(200);
                $response["status"] = "success";
                $response["message"] = "No data.";
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


