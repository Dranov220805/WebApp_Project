<?php

class LogController{
    private AccountService $accountService;
    public function __construct()
    {
        $this->accountService = new AccountService();
    }

    public function index($username, $pwd){
        echo 'hi world';
        echo 'Hi WORLD 1234';
//        code
    }

//    [POST, FETCH] /log/login
    public function checkLogin($username, $pwd){
        if($this->accountService->checkLogin($username, $pwd)){
            echo json_encode(array(
                'status' => true,
                'roleId' => $_SESSION['roleId'],
                'message' => 'Đăng nhập thành công'
            ));
        } else{
            echo json_encode(array(
                'status' => false,
                'message' => 'Tên đăng nhập hoặc mật khẩu sai'
            ));
        }
    }

//    [GET] /log/login
    public function login(){

        include "./views/log/login.php";
    }

//    [GET] /log/logout
    public function logout()
    {
        session_destroy();
        header('location: /log/login');
    }
}

?>