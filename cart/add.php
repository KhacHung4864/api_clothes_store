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
    if ($data && isset($data["user_id"])  && isset($data["item_id"])  && isset($data["quantity"]) && isset($data["color"]) && isset($data["size"])) {
        $user_id = $data["user_id"];
        $item_id = $data["item_id"];
        $quantity = $data["quantity"];
        $color = $data['color'];
        $size = $data['size'];

        // Kiểm tra xem phần tử đã tồn tại chưa
        $checkQuery = "SELECT * FROM cart_table WHERE user_id = '$user_id' AND item_id = '$item_id' AND color = '$color' AND size = '$size'";
        $checkResult = $connectNow->query($checkQuery);

        if ($checkResult->num_rows > 0) {
            // Nếu phần tử đã tồn tại, cập nhật quantity
            $updateQuery = "UPDATE cart_table SET quantity = quantity + $quantity WHERE user_id = '$user_id' AND item_id = '$item_id' AND color = '$color' AND size = '$size'";
            $updateResult = $connectNow->query($updateQuery);

            if ($updateResult) {
                http_response_code(200);
                $response["status"] = "success";
                $response["message"] = "Add item Successfully.";
            } else {
                http_response_code(500);
                $response["status"] = "error";
                $response["message"] = "Error inserting data.";
            }
        } else {
            // Nếu phần tử chưa tồn tại, thêm mới
            $insertQuery = "INSERT INTO cart_table SET user_id = '$user_id', item_id = '$item_id', quantity = '$quantity', color = '$color', size = '$size'";
            $insertResult = $connectNow->query($insertQuery);

            if ($insertResult) {
                http_response_code(200);
                $response["status"] = "success";
                $response["message"] = "Add item Successfully.";
            } else {
                http_response_code(500);
                $response["status"] = "error";
                $response["message"] = "Error inserting data.";
            }
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
?>
