
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
    if ($data && isset($data["token"])) {
        $token = $data["token"];

        try {
            $decoded = JWT::decode($token, new Key('admin', 'HS256'));
            // Token hợp lệ, tiếp tục xử lý yêu cầu

            $sqlQuery = "SELECT * FROM items_table ORDER BY item_id DESC";

            $resultOfQuery = $connectNow->query($sqlQuery);
            // kiểm tra Lấy dữ liệu thành công không
            if ($resultOfQuery->num_rows > 0) {
                // lấy từng bản ghi và đưa vào clothItemRecord 
                $clothItemRecord = array();
                while ($rowFound = $resultOfQuery->fetch_assoc()) {
                    $clothItemRecord[] = $rowFound;
                }


                //Gửi phản hồi
                http_response_code(200);
                $response["status"] = "success";
                $response["message"] = "successfully.";
                $response['data'] = $clothItemRecord;
            } else {
                // Trả về không có data nào 
                http_response_code(200);
                $response["status"] = "success";
                $response["message"] = "No data.";
            }
        } catch (Exception $e) {
            // Lỗi khi xác minh token, trả về lỗi 401 Unauthorized
            http_response_code(401);
            $response["status"] = "error";
            $response["message"] = "Unauthorized. Invalid token.";
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
