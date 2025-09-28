<?php 
//db 연결 
$db_host="localhost"; 
$db_user = "root"; 
$db_pass=""; 
$db_name="my_db"; 
$db_port = 3306; 
$conn=new mysqli($db_host, $db_user, $db_pass, $db_name, $db_port);

if($conn->connect_error){
    die("데이터베이스 연결 실패: " . $conn->connect_error); // connect_error로 수정
}

//login.html에서 POST 방식으로 보낸 데이터 받기 
// POST 데이터 안전히 받기
$username = isset($_POST['username']) ? trim($_POST['username']) : '';
$password = isset($_POST['password']) ? $_POST['password'] : '';

// 보안 패치 부분
// SQL 문 안에서 사용자 입력값이 들어갈 자리를 '?'로 표시해서 나중에 안전하게 바인딩
// 실제 값은 DB 드라이버가 인전하게 처리해주기 떄문에 SQL구문 자체가 변조되지 않음
$sql = "SELECT * FROM users WHERE username = ? AND password = ?";

// DB에 "이런 모양의 SQL을 실행할 거야" 라고 먼저 준비(prepare)
$stmt = $conn->prepare($sql);

if ($stmt == false) {
    error_log("Prepare failed: " . $conn->error);
    die("서버 에러(Prepare 실패).");
}
// ? 자리에 실제 사용자 입력값을 "문자열"로 안전하게 바인딩
$stmt->bind_param("ss", $username, $password);
// 준비된 SQL 실행
if (!$stmt->execute()) { 
    error_log("Execute failed: " . $stmt->error);
    die("서버 에러(Execute 실패).");
}

// 결과 가져오기
$row_count = 0;
$result = null;

if (method_exists($stmt, 'get_result')) {
    $result = $stmt->get_result();
    $row_count = ($result ? $result->num_rows : 0);
} else {
    // get_result()가 없을 때: store_result()로 행 수 확인
    $stmt->store_result();
    $row_count = $stmt->num_rows;
}

// 결과 확인
if ($row_count > 0) {
    echo "<h1>로그인 성공!</h1>";
    echo "<p>'" . htmlspecialchars($username, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . "'님, 환영합니다.</p>";
} else {
    echo "<h1>로그인 실패</h1>";
    echo "<p>아이디 또는 비밀번호가 올바르지 않습니다.</p>";
    echo '<a href="login.html">다시 시도하기</a>';
}

//DB 연결 종료
$stmt->close(); 
$conn->close(); 
?>