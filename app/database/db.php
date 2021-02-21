<?php

session_start(); //Starts session
$username = "s5216193"; // Put your username in the quotations
$password = "JueTFrFXTYUTHuT97YAELujuPeokWFa7"; // Put your database password in the quotations
$host = "db.bucomputing.uk";
$port = 6612; // Note our MySQL server doesn't use the standard MySQL port, hence why we need to specify it
$database = $username; // In our case the database name is the same as the username (normally it is
// different) so we can set it as the same as the username
$conn = new mysqli(); // Create a MySQLi object
$conn->init(); // Initializes MySQLi and returns a resource for use with mysqli::real_connect()
if (!$conn)
{ // If initalising MySQLi failed (i.e. it didn't return true, hence the ! for checking not true)
    echo "<p>Initalising MySQLi failed</p>";
}
else
{
    // Establish secure connection using SSL for use with MySQLi
    $conn->ssl_set(NULL, NULL, NULL, '/public_html/sys_tests', NULL);

    // Connect the MySQL connection
    $conn->real_connect($host, $username, $password, $database, $port, NULL, MYSQLI_CLIENT_SSL_DONT_VERIFY_SERVER_CERT);
    if ($conn->connect_errno)
    { // If connection error
        // Display error message and stop the script. We can't do any database work as there is no connection to use
        echo "<p>Failed to connect to MySQL. " . "Error (" . $conn->connect_errno . "): " . $conn->connect_error . "</p>";
    }
    else
    {
        function dd($value) // to be deleted
        {
            echo "<pre>", print_r($value, true), "</pre>";
            die();
        }


        function executeQuery($sql, $data)
        {
            global $conn;
            $stmt = $conn->prepare($sql);
            $values = array_values($data);
            $types = str_repeat('s', count($values));
            $stmt->bind_param($types, ...$values);
            $stmt->execute();
            return $stmt;
        }


        function selectAll($table, $conditions = [])
        {
            global $conn;
            $sql = "SELECT * FROM $table";
            if (empty($conditions)) {
                $stmt = $conn->prepare($sql);
                $stmt->execute();
                $records = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
                return $records;
            } else {
                $i = 0;
                foreach ($conditions as $key => $value) {
                    if ($i === 0) {
                        $sql = $sql . " WHERE $key=?";
                    } else {
                        $sql = $sql . " AND $key=?";
                    }
                    $i++;
                }
                
                $stmt = executeQuery($sql, $conditions);
                $records = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
                return $records;
            }
        }


        function selectOne($table, $conditions)
        {
            global $conn;
            $sql = "SELECT * FROM $table";

            $i = 0;
            foreach ($conditions as $key => $value) {
                if ($i === 0) {
                    $sql = $sql . " WHERE $key=?";
                } else {
                    $sql = $sql . " AND $key=?";
                }
                $i++;
            }

            $sql = $sql . " LIMIT 1";
            $stmt = executeQuery($sql, $conditions);
            $records = $stmt->get_result()->fetch_assoc();
            return $records;
        }


        function create($table, $data)
        {
            global $conn;
            $sql = "INSERT INTO $table SET ";

            $i = 0;
            foreach ($data as $key => $value) {
                if ($i === 0) {
                    $sql = $sql . " $key=?";
                } else {
                    $sql = $sql . ", $key=?";
                }
                $i++;
            }
            
            $stmt = executeQuery($sql, $data);
            $id = $stmt->insert_id;
            return $id;
        }



        function update($table, $id, $data)
        {
            global $conn;
            $sql = "UPDATE $table SET ";

            $i = 0;
            foreach ($data as $key => $value) {
                if ($i === 0) {
                    $sql = $sql . " $key=?";
                } else {
                    $sql = $sql . ", $key=?";
                }
                $i++;
            }

            $sql = $sql . " WHERE id=?";
            $data['id'] = $id;
            $stmt = executeQuery($sql, $data);
            return $stmt->affected_rows;
        }



        function delete($table, $id)
        {
            global $conn;
            $sql = "DELETE FROM $table WHERE id=?";

            $stmt = executeQuery($sql, ['id' => $id]);
            return $stmt->affected_rows;
        }


        function getPublishedPosts()
        {
            global $conn;
            $sql = "SELECT p.*, u.username FROM posts AS p JOIN users AS u ON p.user_id=u.id WHERE p.published=?";

            $stmt = executeQuery($sql, ['published' => 1]);
            $records = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
            return $records;
        }


        function getPostsByTopicId($topic_id)
        {
            global $conn;
            $sql = "SELECT p.*, u.username FROM posts AS p JOIN users AS u ON p.user_id=u.id WHERE p.published=? AND topic_id=?";

            $stmt = executeQuery($sql, ['published' => 1, 'topic_id' => $topic_id]);
            $records = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
            return $records;
        }



        function searchPosts($term)
        {
            $match = '%' . $term . '%';
            global $conn;
            $sql = "SELECT 
                        p.*, u.username 
                    FROM posts AS p 
                    JOIN users AS u 
                    ON p.user_id=u.id 
                    WHERE p.published=?
                    AND p.title LIKE ? OR p.body LIKE ?";


            $stmt = executeQuery($sql, ['published' => 1, 'title' => $match, 'body' => $match]);
            $records = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
            return $records;
        }
    }
}
?>
