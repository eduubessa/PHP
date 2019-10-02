<?php

class Database {

    protected $host = "localhost";
    protected $port = 3306;
    protected $user = "root";
    protected $pass = "";
    protected $database = "shortcode";
    protected $encode = "utf8";

    private $connection = null;
    private $table = null;
    private $primaryKey = null;
    private $sql = null;
    private $fields = [];
    private $data = [];


    /** 
     * 
     * Método constructor (Método que inicializa automaticamente)
     * 
     * **/
    public function __construct(){
        try {
            if($this->connection == null){
                $this->connection = new \PDO('mysql:host=' . $this->host . ';port=' . $this->port . ';dbname=' . $this->database . ';encode=' . $this->encode, $this->user, $this->pass);
                $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            }else{
                throw new Exception('Faild connection, connection not empty!');
            }

        }catch(PDOException $e){
            echo "<h2>Connection Failed: " . $e->getCode() ." </h2>";
            echo "<p><b>Message:</b> " . $e->getMessage() . "</p>";
            echo "<p><b>Line:</b> " . $e->getLine() . "</p>";
            echo "<p><b>File:</b> " . $e->getFile() . "</p>";
        }
    }

    /**
     * Método responsável para editar a variável $table (setTable)
     */
    public function table($table){
        $this->table = $table; 
        return $this;
    }

    public function insert($data) {
        try {
            if($this->table != null){
                $this->sql = "INSERT INTO `" . $this->table . "` (";
                $i = 0;

                foreach(array_keys($data) as $key => $value){
                    $i++;
                    $this->sql .= "`" . $value . "`";
                    if($i < count($data)){
                        $this->sql .= ", ";
                    }
                }

                $this->sql .= ") VALUES (";
                $i = 0;

                foreach($data as $key => $value){
                    $i++;
                    $this->sql .= "?";
                    if($i < count($data)){
                        $this->sql .= ", ";
                    }
                }

                $this->sql .= ");";
                $stmt = $this->connection->prepare($this->sql);
                $i = 0;

                foreach($data as $key => $value){
                    $i++;
                    $stmt->bindValue($i, $value);
                }
                $stmt->execute();
            }else{
                throw new Exception("No table");
            }
        }catch(Exception $e){
            echo "<h2>Connection Failed: " . $e->getCode() ." </h2>";
            echo "<p><b>Message:</b> " . $e->getMessage() . "</p>";
            echo "<p><b>Line:</b> " . $e->getLine() . "</p>";
            echo "<p><b>File:</b> " . $e->getFile() . "</p>";
        }
        return $this;
    }

    public function select($columns = null) {
        try {
            if($this->table === null) {
                throw new Exception("Não selecionou a tabela que pretende");
            }

            $this->sql = "SELECT ";

            if($columns == null){
                $this->sql .= "* FROM `" . $this->table ."`";
            }else{
                $this->sql .= $columns . " FROM `" . $this->table ."`";
            }

            return $this;

        }catch(Exception $e){
            echo "<h2>Connection Failed: " . $e->getCode() ." </h2>";
            echo "<p><b>Message:</b> " . $e->getMessage() . "</p>";
            echo "<p><b>Line:</b> " . $e->getLine() . "</p>";
            echo "<p><b>File:</b> " . $e->getFile() . "</p>";
        }
    }

    /** Metodo que está responsavel para juntar o WHERE com o SELECT gerado pelo método.
    *
    * @param $field
    * @param $operator
    * @param $value
    * @param $this
    *
    **/

    public function where($field, $operator = null, $value) {
        try {
            if($field == null) {
                throw new Exeception ("<i>WHERE</i> necessita de um campo, e um valor para comparar");
            }else if($value == null) {
                throw new Exception("<i>WHERE</i> necessita de um campo, e um valor para comparar");
            }

            if(strpos($this->sql, "WHERE") == true) {
                $this->sql .= " AND `" . $field ."`";
            }else{
                $this->sql .= " WHERE `" . $field ."`";
            }

            if($operator == null){
                if(is_numeric($value)){
                    $this->sql .= " = ";
                }else{
                    $this->sql .= " LIKE ";
                }
            }else{
                $this->sql .= " " . $operator . " ";
            }

            $this->sql .= ":" . $field;

            $this->fields = array_merge($this->fields, array($field => $value));

            return $this;

        }catch(Exception $e){
            echo "<h2>Connection Failed: " . $e->getCode() ." </h2>";
            echo "<p><b>Message:</b> " . $e->getMessage() . "</p>";
            echo "<p><b>Line:</b> " . $e->getLine() . "</p>";
            echo "<p><b>File:</b> " . $e->getFile() . "</p>";
        }

        return $this->sql;
    }

    public function orWhere($field, $operator = null, $value) {
        try {
            if($field == null) {
                throw new Exeception ("<i>WHERE</i> necessita de um campo, e um valor para comparar");
            }else if($value == null) {
                throw new Exception("<i>WHERE</i> necessita de um campo, e um valor para comparar");
            }

            if(strpos($this->sql, "WHERE") == true) {
                $this->sql .= " OR `" . $field ."`";
            }else{
                $this->sql .= " WHERE `" . $field ."`";
            }

            if($operator == null){
                if(is_numeric($value)){
                    $this->sql .= " = ";
                }else{
                    $this->sql .= " LIKE ";
                }
            }else{
                $this->sql .= " " . $operator . " ";
            }

            $this->sql .= ":" . $field;

            $this->fields = array_merge($this->fields, array($field => $value));

            return $this;

        }catch(Exception $e){
            echo "<h2>Connection Failed: " . $e->getCode() ." </h2>";
            echo "<p><b>Message:</b> " . $e->getMessage() . "</p>";
            echo "<p><b>Line:</b> " . $e->getLine() . "</p>";
            echo "<p><b>File:</b> " . $e->getFile() . "</p>";
        }
    }

    public function get()
    {
        $i = 0;

        $columnsName = [];
        $data = new stdClass();

        #Get columns names
        try {
            $stmt = $this->connection->prepare("DESCRIBE `" . $this->table . "`");
            $stmt->execute();
            $columns = $stmt->fetchAll(PDO::FETCH_COLUMN);

            foreach($columns as $column)
            {
                array_push($columnsName, $column);
            }

            $stmt = $this->connection->prepare($this->sql);
            
            if($this->fields !== null){
                foreach($this->fields as $field => $value)
                {
                    $i++;
                    if(is_numeric($value)){
                        $stmt->bindValue(':' . $field, $value, PDO::PARAM_INT);
                    }else{
                        $stmt->bindValue(':' . $field, $value, PDO::PARAM_STR);
                    }
                }
            }
            
            $stmt->execute();
            $stmt->setFetchMode(PDO::FETCH_ASSOC);

            $x = 0;
            while($object = $stmt->fetch()){
                for($i = 0; $i < count($columnsName); $i++)
                {
                   $this->data[$x][$columnsName[$i]] = $object[$columnsName[$i]];
                }
                $x++;
            }
        }catch(PDOException $e){
            echo "<h2>Connection Failed: " . $e->getCode() ." </h2>";
            echo "<p><b>Message:</b> " . $e->getMessage() . "</p>";
            echo "<p><b>Line:</b> " . $e->getLine() . "</p>";
            echo "<p><b>File:</b> " . $e->getFile() . "</p>";
        }

        return (object) $this->data;
    }

    public function first()
    {
        $this->sql .= " ORDER BY `id` ASC LIMIT 1";

        $stmt = $this->connection->prepare($this->sql);
        
        foreach($this->fields as $field => $value)
        {
            if(is_numeric($value)){
                $stmt->bindValue(':' . $field, $value, PDO::PARAM_INT);
            }else{
                $stmt->bindValue(':' . $field, $value, PDO::PARAM_STR);
            }
        }

        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_OBJ);
        
        while($object = $stmt->fetch()){
            return $object;
        }   
    }

    public function last()
    {
        $this->sql .= " ORDER BY `id` DESC LIMIT 1";

        $stmt = $this->connection->prepare($this->sql);
        
        foreach($this->fields as $field => $value)
        {
            if(is_numeric($value)){
                $stmt->bindValue(':' . $field, $value, PDO::PARAM_INT);
            }else{
                $stmt->bindValue(':' . $field, $value, PDO::PARAM_STR);
            }
        }

        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_OBJ);
        
        while($object = $stmt->fetch()){
            return $object;
        }   
    }

    public function find($id)
    {
        try {

            if($this->table != null){

                $sql = "SELECT * FROM `" . $this->table . "` WHERE `id` = :id";

                $stmt = $this->connection->prepare($sql);
                $stmt->bindValue(':id', $id);
                $stmt->execute();
                $stmt->setFetchMode(PDO::FETCH_OBJ);

                return (object) $stmt->fetch();
            
            }else{
                throw new Exception("No table");
            }
        }catch(PDOException $e){
            echo "<h2>Connection Failed: " . $e->getCode() ." </h2>";
            echo "<p><b>Message:</b> " . $e->getMessage() . "</p>";
            echo "<p><b>Line:</b> " . $e->getLine() . "</p>";
            echo "<p><b>File:</b> " . $e->getFile() . "</p>";
        }
    }

    public function count()
    {
        $i = 0;
        $this->data = new StdClass();
        $stmt = $this->connection->prepare($this->sql);

        foreach($this->fields as $field => $value)
        {
            $i++;
            if(is_numeric($value)){
                $stmt->bindValue(':' . $field, $value, PDO::PARAM_INT);
            }else{
                $stmt->bindValue(':' . $field, $value, PDO::PARAM_STR);
            }
        }
        
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_OBJ);
        
        return $stmt->rowCount();  
    }

    public function update($data){
        try {
            $i = 0;            
            if($this->table !== null){
                $this->sql = "UPDATE `" . $this->table . "` SET ";

                foreach($data as $key => $value){
                    $i++;
                    $this->sql .= $key . "=";
                    if(is_numeric($value)){
                        $this->sql .= $value;
                    }else{
                        $this->sql .= "'" . $value . "'";
                    }
                    if($i < count($data)){
                        $this->sql .= ", ";
                    }
                }
            }else{
                throw new Exception("No table");
            }
        }catch(PDOException $e){
            echo "<h2>Connection Failed: " . $e->getCode() ." </h2>";
            echo "<p><b>Message:</b> " . $e->getMessage() . "</p>";
            echo "<p><b>Line:</b> " . $e->getLine() . "</p>";
            echo "<p><b>File:</b> " . $e->getFile() . "</p>";
        }
        
        return $this;
    }

    public function delete()
    {
        try {
            if($this->table === null){
                throw new Exception('No table!');  
            }

            if($this->primaryKey !== null){

                $sql = "DELETE FROM `" . $this->table . "` id = :id";
                $stmt = $this->connection->prepare($sql);
                $stmt = $this->bindValue(':id', $this->primaryKey);
                $stmt->execute();              
                
            }else{
                $this->sql = "DELETE FROM `" . $this->table . "` ";
            }

            return $this;

        }catch(PDOException $e){
            echo "<h2>Connection Failed: " . $e->getCode() ." </h2>";
            echo "<p><b>Message:</b> " . $e->getMessage() . "</p>";
            echo "<p><b>Line:</b> " . $e->getLine() . "</p>";
            echo "<p><b>File:</b> " . $e->getFile() . "</p>";
        }
    }

    public function __destruct()
    {
        try {
            if($this->connection != null){
                $this->connection = null;
                $this->sql = null;
            }
        }catch(PDOException $e){
            echo "<h2>Connection Failed: " . $e->getCode() ." </h2>";
            echo "<p><b>Message:</b> " . $e->getMessage() . "</p>";
            echo "<p><b>Line:</b> " . $e->getLine() . "</p>";
            echo "<p><b>File:</b> " . $e->getFile() . "</p>";
        }
    }
}
