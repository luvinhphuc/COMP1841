<?php
namespace App\Core;

class App {
    // Thiết lập Controller, Hàm và Tham số mặc định nếu URL trống
    protected $controller = "HomeController";
    protected $action = "index";
    protected $params = [];

    public function __construct() {
        // 1. Phân tích URL thành mảng [“controller”, “action”, “param1”]
        $url = $this->parseUrl();

        // 2. XỬ LÝ CONTROLLER
        // Kiểm tra xem file Controller có tồn tại trong thư mục app/Controllers không
        if (isset($url[0]) && file_exists(ROOT_PATH . '/app/Controllers/' . ucfirst($url[0]) . 'Controller.php')) {
            $this->controller = ucfirst($url[0]) . 'Controller';
            unset($url[0]); //Xóa khỏi mảng (để giữ lại tham số)
        }

        // Khởi tạo Class Controller (Ví dụ: $this->controller = "App\Controllers\UserController")
        $controllerClass = "\\App\\Controllers\\" . $this->controller;

        if (class_exists($controllerClass)) {
            $this->controller = new $controllerClass;
        } else {
            $this->show404();
//            die("Trang không tồn tại (Controller " . $controllerClass . " không tìm thấy)");
        }

        // 3. XỬ LÝ ACTION (HÀM TRONG CONTROLLER)
        if (isset($url[1])) {
            // Kiểm tra xem trong Class Controller có hàm này không
            if (method_exists($this->controller, $url[1])) {
                $this->action = $url[1];
                unset($url[1]); // Xóa khỏi mảng
            } else {
                $this->show404();
//                die("Hành động không tồn tại (Method " . $url[1] . " không tìm thấy)");
            }
        }

        // 4. XỬ LÝ THAM SỐ (PARAMS)
        // Nếu mảng $url còn phần tử thì gán vào $params, nếu không thì để mảng rỗng
        $this->params = $url ? array_values($url) : [];

        // 5. KÍCH HOẠT CONTROLLER VÀ HÀM
        // Chạy hàm $this->action nằm trong class $this->controller với các tham số $this->params
        call_user_func_array([$this->controller, $this->action], $this->params);
    }

    /**
     * Hàm cắt, lọc và làm sạch URL từ biến $_GET['url']
     */
    private function parseUrl() {
        if (isset($_GET['url'])) {
            // Rtrim để bỏ dấu gạch chéo cuối cùng (ví dụ: user/profile/ sẽ thành user/profile)
            // Filter_var để xóa các ký tự lạ, độc hại trên URL
            // Explode để cắt chuỗi thành mảng dựa vào dấu "/"
            return explode('/', filter_var(rtrim($_GET['url'], '/'), FILTER_SANITIZE_URL));
        }
        return [];
    }

    private function show404()
        {
            http_response_code(404);

            require dirname(__DIR__) . '/Views/errors/404.php';

            exit;
        }
}