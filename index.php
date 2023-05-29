<?
    require 'connect.php';
    
    $query = $_GET['q'];
    
    if ($query == "drivers_dates") {
        /*
         * Get list of worked in dates drivers
         *
         * params:
         * q=drivers_dates from=<yyyy-mm-dd> to=<yyyy-mm-dd>
         *
         * example url:
         * https://nav-com.ru/taxi?q=drivers_dates&from=2023-05-28&to=2023-05-29
         */
        
        $from_date = $_GET['from'];
        $to_date = $_GET['to'];
        
        $result = mysqli_query($link, "
            SELECT DISTINCT 
                d.id_driver, 
                d.first_name, 
                d.last_name 
            FROM 
                driver d
            JOIN 
                orders r
            ON 
                d.id_driver = r.id_driver
            WHERE 
                date_from >= '".$from_date."' AND
                date_from < '".$to_date."'
        ");
                
        $responsedArray = array();
        while ($drivers = mysqli_fetch_array($result)) {
            $tmpArray = array(
                "id" => (int)$drivers['id_driver'],
                "firstName" => $drivers['first_name'],
                "lastName" => $drivers['last_name']
            );
            array_push($responsedArray, $tmpArray);
        }
        
        json_answer(200, "OK", $responsedArray);
        
    } else if ($query == "sum_dates") {
        /*
         * Get sum of money in dates
         *
         * params:
         * q=sum_dates from=<yyyy-mm-dd> to=<yyyy-mm-dd>
         *
         * example url:
         * https://nav-com.ru/taxi?q=sum_dates&from=2023-05-28&to=2023-05-29
         */
         
        $from_date = $_GET['from'];
        $to_date = $_GET['to'];
        
        $result = mysqli_query($link, "
            SELECT 
                sum(cost) as sum 
            FROM 
                orders
            WHERE 
                date_to >= '".$from_date."' AND 
                date_to < '".$to_date."'
        ");
                
        $responsedArray = array();
        while ($sums = mysqli_fetch_array($result)) {
            $tmpArray = array(
                "sum" => (int)$sums['sum']
            );
            array_push($responsedArray, $tmpArray);
        }
        
        json_answer(200, "OK", $responsedArray);
        
    } else if ($query == "black_list") {
        /*
         * Get all users from black list
         *
         * params:
         * q=black_list
         *
         * example url:
         * https://nav-com.ru/taxi?q=black_list
         */
        
        $result = mysqli_query($link, "
            SELECT 
                id_passenger, 
                name 
            FROM 
                passengers
            WHERE 
                is_black_list = 1
        ");
                
        $responsedArray = array();
        while ($list = mysqli_fetch_array($result)) {
            $tmpArray = array(
                "id" => (int)$list['id_passenger'],
                "name" => $list['name']
            );
            array_push($responsedArray, $tmpArray);
        }
        
        json_answer(200, "OK", $responsedArray);
        
    } else if ($query == "find_cars") {
        /*
         * Get cars list by parameters
         *
         * params:
         * q=sum_dates type={'passenger_car', 'truck', 'minivan', 'minibus'} child={0, 1}
         *
         * example url:
         * https://nav-com.ru/taxi?q=find_cars&type=passenger_car&child=0
         */
         
        $type = $_GET['type'];
        $is_child_seat = $_GET['child'];
        
        $result = mysqli_query($link, "
            SELECT 
                id_car, 
                car_brand, 
                car_model, 
                car_number 
            FROM 
                car
            WHERE 
                type = '".$type."' AND 
                is_child_seat = ".$is_child_seat
        );
                
        $responsedArray = array();
        while ($cars = mysqli_fetch_array($result)) {
            $tmpArray = array(
                "id" => (int)$cars['id_car'],
                "carBrand" => $cars['car_brand'],
                "carModel" => $cars['car_model'],
                "carNumber" => $cars['car_number']
            );
            array_push($responsedArray, $tmpArray);
        }
        
        json_answer(200, "OK", $responsedArray);
        
    } else if ($query == "drivers_list_exp") {
        /*
         * Get drivers list ordered by expirience
         *
         * params:
         * q=drivers_list_exp
         *
         * example url:
         * https://nav-com.ru/taxi?q=drivers_list_exp
         */
        
        $result = mysqli_query($link, "
            SELECT DISTINCT 
                d.id_driver, 
                last_name, 
                first_name, 
                expirience, 
                car_brand, 
                car_model 
            FROM 
                driver d
            JOIN 
                car c
            ON 
                d.id_car = c.id_car
            ORDER BY 
                expirience DESC
        ");
                
        $responsedArray = array();
        while ($drivers = mysqli_fetch_array($result)) {
            $tmpArray = array(
                "id" => (int)$drivers['id_driver'],
                "firstName" => $drivers['first_name'],
                "lastName" => $drivers['last_name'],
                "experience" => $drivers['expirience'],
                "carBrand" => $drivers['car_brand'],
                "carModel" => $drivers['car_model']
            );
            array_push($responsedArray, $tmpArray);
        }
        
        json_answer(200, "OK", $responsedArray);
        
    } else if ($query == "drivers_list_mark") {
        /*
         * Get drivers list ordered by mark
         *
         * params:
         * q=drivers_list_mark
         *
         * example url:
         * https://nav-com.ru/taxi?q=drivers_list_mark
         */
        
        $result = mysqli_query($link, "
            SELECT 
                d.id_driver, 
                first_name, 
                last_name, 
                mark 
            FROM 
                driver d
            JOIN 
                marks m 
            JOIN 
                marks_to_drivers md
            ON 
                d.id_driver = md.id_driver AND 
                m.id_mark = md.id_mark
            ORDER BY 
                mark DESC
        ");
                
        $responsedArray = array();
        while ($drivers = mysqli_fetch_array($result)) {
            $tmpArray = array(
                "id" => (int)$drivers['id_driver'],
                "firstName" => $drivers['first_name'],
                "lastName" => $drivers['last_name'],
                "mark" => $drivers['mark']
            );
            array_push($responsedArray, $tmpArray);
        }
        
        json_answer(200, "OK", $responsedArray);
        
    } else {
        json_answer(400, "Bad Request");
    }
    
    function json_answer(
        $answer_code,
        $answer_message,
        $data = null
    ) {
        $answerArray = array(
            "status" => array (
                "code" => (int)$answer_code,
                "message" => $answer_message
            ),
            "data" => $data
        );
        
        echo json_encode($answerArray);
    }

?>
