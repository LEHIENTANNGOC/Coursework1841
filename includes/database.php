<?php

require_once __DIR__ . '/connect.php';

if (!defined('_TanHien')) {
    die('Access denied');
}

// Multi-row query
function getAll($sql){
    global $conn;
    $stm = $conn -> prepare($sql);
    $stm -> execute();
    $result = $stm -> fetchAll(PDO::FETCH_ASSOC); 
    return $result;
}



// Count query returns 
function getRows($sql){
    global $conn;
    $stm = $conn -> prepare($sql);
    $stm -> execute();
     
    return $stm -> rowCount();
}

// Single-row query 
function getOne($sql){
    global $conn;
    $stm = $conn -> prepare($sql);
    $stm -> execute();
    $result = $stm -> fetch(PDO::FETCH_ASSOC); 
    return $result;
}


// Insert data  
function insert($table, $data){
    global $conn;

    $keys = array_keys($data);
    $cot = implode(',',$keys);
    $place = ':'.implode(',:',$keys);

    $sql = "INSERT INTO $table ($cot) VALUES($place)"; //:name -> placeholder
        
    $stm = $conn -> prepare($sql); // SQL Injection

    // thực thi câu lệnh
    $rel = $stm -> execute($data);
    return $rel;
}

//update
function update($table, $data, $condition = ''){
    global $conn;
    $update = '';

    foreach($data as $key => $value){
        $update .= $key . '=:' .$key .',';
    }

    $update = trim($update, ',');
   

    if(!empty($condition)){
        $sql = "UPDATE $table SET $update WHERE $condition";
    }else {
        $sql = "UPDATE $table SET $update ";
    }
   
    // chuẩn bị câu lệnh sql
    $tmp = $conn -> prepare($sql);
 
    // Thực thi câu lệnh
    $tmp -> execute($data);
    return $tmp;
}
//delete
function delete($table, $condition = ''){
    global $conn;

   if(!empty($condition)){
        $sql = "DELETE FROM $table WHERE $condition";
   }else {
        $sql = "DELETE FROM $table";
   }

    $stm = $conn -> prepare($sql);

    $stm -> execute();
    return $stm;

}


// lấy id dữ liệu mới
function lastID(){
    global $conn;
    return $conn -> lastInsertId();
}

// Hàm tìm kiếm bài viết theo từ khóa
function searchPosts($keyword) {
    global $conn;

    $sql = "
        SELECT 
            p.post_id,
            p.content,
            p.image_path,
            p.created_at,
            u.username,
            m.module_name
        FROM posts p
        LEFT JOIN users u ON p.user_id = u.user_id
        LEFT JOIN modules m ON p.module_id = m.module_id
        WHERE p.content LIKE :kw OR m.module_name LIKE :kw
        ORDER BY p.created_at DESC
    ";

    $stm = $conn->prepare($sql);
    $stm->execute([':kw' => '%' . $keyword . '%']);
    return $stm->fetchAll(PDO::FETCH_ASSOC);
}