<?php


namespace vendor\classes;


use PDO;

class Model
{

    private $_config;
    private $_db;

    protected $_host;
    protected $_dbname;
    protected $_username;
    protected $_passwd;
    protected $_charset;
    protected $_tableName;

    private $_asArray = false;
    private $_select;
    private $_from;
    private $_where;
    //список значений для подготовленного выражения
    private $_whereArray = [];
    private $_limit;
    private $_orderBy;
    private $_join;
    private $_insert;
    private $_update;
    //список значений для подготовленного выражения
    private $_insertArray = [];
    private $_updateArray = [];

    private $_attributes;
    private $_primaryKey;
    private $_oldAttributes;


    public function __construct()
    {
        $this->_config = Core::$config;

        $this->_host = $this->_config['db']['host'];
        $this->_dbname = $this->_config['db']['dbname'];
        $this->_username = $this->_config['db']['username'];
        $this->_passwd = $this->_config['db']['password'];
        $this->_charset = $this->_config['db']['charset'];
        $this->_tableName = $this->tableName();

    }


    public function tableName()
    {
        return strtolower((new \ReflectionClass($this))->getShortName());

    }

    public function getDb()
    {
        if ($this->_db === null) {
            $DSN = 'mysql:host=' . $this->_host . ';dbname=' . $this->_dbname . ';charset=' . $this->_charset;
            $opt = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ];

            $this->_db = new PDO($DSN, $this->_username, $this->_passwd, $opt);
        }

        return $this->_db;

    }

//    INSERT INTO `test`.`users` (`surname`, `name`, `subid`, `schoolid`) VALUES ('Даидов', 'Рамзан', NULL, NULL);
//    UPDATE `test`.`users` SET `name`='Казбек4' WHERE  `id`=40;
//    UPDATE `test`.`users` SET `surname`='Дуров4', `name`='Казбек5', `subid`='5', `schoolid`='3' WHERE  `id`=40;
    public function save()
    {
//        $tableName = $this->tableName();
        $query = '';
        $arrayQuery = [];

        $attributes = $this->attributes();
        $arrayValue = [];
        unset($attributes[$this->_primaryKey]);

        foreach ($attributes as $key => $value){
            if(isset($this->$key)){
                $arrayValue[$key] = $this->$key;
            }
        }

        if($this->getIsNewRecord()){
//          INSERT
            $this->insert($arrayValue);
            $query = $this->_insert;
            $arrayQuery = $this->_insertArray;


        }else{
//          UPDATE
            $this->update($arrayValue);
            $primaryKey = $this->_primaryKey;
            $this->where([$primaryKey => $this->$primaryKey]);
            $query = $this->_update . $this->_where;
            $arrayQuery = array_merge($this->_updateArray, $this->_whereArray);

        }


        $result = $this->getDb()->prepare($query);
        $result->execute($arrayQuery);

        return $this;

    }

    public function getIsNewRecord()
    {
        return $this->_oldAttributes === null;
    }

    /**
     * @param bool $value
     */
    public function setIsNewRecord($value)
    {
        $this->_oldAttributes = $value ? null : $this->attributes();
    }

    /*  SELECT `test`.`users`.`surname`, `test`.`users`.`name`, `test`.`schools`.`name`
        FROM `test`.`users`
        LEFT JOIN `test`.`schools` ON `test`.`users`.`schoolid`=`test`.`schools`.`id`
        WHERE `test`.`users`.`id` = '1';
        ORDER BY `test`.`users`.`id` LIMIT 1, 1;
    */
    public function one()
    {
//        $this->setIsNewRecord(true);

        $result = $this->querySelect(true);

        if($this->_asArray){
            return $result->fetchAll();
        }

        $object = new $this ;
        $object->setIsNewRecord(false);
//        $result->setFetchMode(PDO::FETCH_CLASS, $object);
        $result->setFetchMode(PDO::FETCH_INTO, $object);

//        $object = $result->fetch();
        return $result->fetch();


    }

    public function all()
    {
        $result = $this->querySelect();

        if($this->_asArray){
            return $result->fetchAll();
        }

        $object = new $this ;
        $object->setIsNewRecord(false);

        $result->setFetchMode(PDO::FETCH_INTO, $object);

        return $result->fetchAll();

    }

    private function querySelect($one = false)
    {
        $query = '';

        if($one){
            $limit = ' LIMIT 0, 1';
            $arrayLimit =  empty($this->_limit) ? [] : explode(',', $this->_limit);
            $limit = empty($arrayLimit[1]) ? $limit: "$arrayLimit[0], 1";
        }else{
            $limit = $this->_limit;
        }
        $this->select();
        $this->from();

        $query .= $this->_select . $this->_from . $this->_join . $this->_where . $this->_orderBy . $limit;
        $result = $this->getDb()->prepare($query);
        $result->execute($this->_whereArray);
        return $result;

    }

    private function attributes()
    {

        $tableName = $this->tableName();
        'SHOW COLUMNS FROM users';
        $colum = static::find()->getDb()->query("SHOW COLUMNS FROM `$tableName`");
        $colum = $colum->fetchAll();

        foreach ($colum as $item => $key){
            $arrayAttributes[$key['Field']] = $key['Key'];
            if($key['Key'] == 'PRI') $this->_primaryKey = $key['Field'];
        }

        $this->_attributes = $arrayAttributes;

        return $this->_attributes;


    }

    public static function find(){
        return new static();
    }

    public function findOne($id)
    {
        return $this->select()->where([$this->tableName() => ['id'=> $id]])
        ->one();

//        $db = $this->getDb()->query('SELECT * FROM users');



    }

    public function asArray()
    {
        $this->_asArray = true;
        return $this;
    }



    //['id', 'users' => 'name']
    public function select($arrayCol = [])
    {
        if($this->_select == null){
            if(empty($arrayCol)){
                $this->_select = 'SELECT *';
            }else{
                $this->_select = '';
                foreach ($arrayCol as $tableName => $item){
                    $tableName = is_numeric($tableName) ? $this->tableName() : $tableName;
                    $this->_select .= ', `' . $tableName.'`.`' . $item . '`' ;
                }
                $this->_select = substr($this->_select, 1 );
                $this->_select = 'SELECT' . $this->_select;
            }

        }

        return $this;


    }

    public function from($stringTable = '')
    {

        if($this->_from == null){
            if(empty($arrayTable)){
                $this->_from = ' FROM `' . $this->tableName() . '`';
            }else{
                $this->_from = ' FROM `' . $stringTable . '`';
            }
        }


        return $this;
    }


    //[['users' =>['id'=>'123'], 'LIKE' => 'L'],'AND', ['price'=>'100', '>']] %123
    //[['id'=>'123', 'LIKE' => 'L'],'AND', ['price'=>'100', '>']]  %123
    //[['id'=>'123', 'LIKE' => 'R'],'AND', ['price'=>'100', '>']]  123%
    //[['id'=>'123', 'LIKE' => 'N'],'AND', ['price'=>'100', '>']]  123
    //[['id'=>'123', 'LIKE'],'AND', ['price'=>'100', '>']]         %123%
    //[['id'=>'1', '='],'OR', ['price'=>'100', '>']]
    //['id'=>'1']
    //['id'=>'100', '>']
    public function where($arrayCol)
    {
        if($this->_where == null){
            if(is_array($arrayCol)){

                $arrayCount = count($arrayCol);
                if($arrayCount < 3){
                    $this->setWhereCount($arrayCol, $arrayCount);
                }elseif ($arrayCount > 2){
                    foreach ($arrayCol as $item => $value){
                        if(is_array($value)){
                            $arrayCountValue = count($value);
                            $this->setWhereCount($value, $arrayCountValue);
                        }else{
                            $this->_where .= ' ' . $value;
                        }

                    }
                }
                $this->_where = ' WHERE' . $this->_where;


            }

        }
        return $this;


    }

    private function setWhereCount($arrayCol, $arrayCount)
    {
//        $arrayCount = count($arrayCol);
        if($arrayCount == 1){
            $key = key($arrayCol);

            if(is_array($arrayCol[$key])){
                $tableName = $key;
                $keyValue = key($arrayCol[$key]);
                $nameValue = $keyValue;
                $value =  $arrayCol[$key][$keyValue];
            }else{
                $tableName = $this->tableName();
                $nameValue = $key;
                $value = $arrayCol[$key];
            }

//            $this->_where .= ' `'. $tableName .'`.`' . $key . '` =  ?';
            $this->_where .= " `$tableName`.`$nameValue` =  ?";
            $this->_whereArray[] =  $value;


        }elseif ($arrayCount == 2){
            $key = key($arrayCol);

            if(is_array($arrayCol[$key])){
                $tableName = $key;
                $keyValue = key($arrayCol[$key]);
                $nameValue = $keyValue;
                $value =  $arrayCol[$key][$keyValue];
            }else{
                $tableName = $this->tableName();
                $nameValue = $key;
                $value = $arrayCol[$key];
            }

            next($arrayCol);
            $operator = key($arrayCol);

            if($operator === 'LIKE'){

                if($arrayCol[$operator] === 'L'){

                    $this->_whereArray[] =  "%$value";

                }elseif ($arrayCol[$operator] === 'R'){

                    $this->_whereArray[] =  "$value%";

                }elseif ($arrayCol[$operator] === 'N'){

                    $this->_whereArray[] =  "$value";

                }else{

                    $this->_whereArray[] =  "%$value%";
                }

                $this->_where .= " `$tableName`.`$nameValue` $operator ?";

            }elseif ($arrayCol[$operator] === 'LIKE'){

                $this->_where .= " `$tableName`.`$nameValue` $arrayCol[$operator] ?";
                $this->_whereArray[] =  "%$value%";

            }else{
                $this->_where .= " `$tableName`.`$nameValue` $arrayCol[$operator] ?";
                $this->_whereArray[] =  $value;
            }


        }
    }

    //limit(1, 3) - с первого индекса три позиции покажет
    //limit(4) - с нулевого индекса четыре позиции покажет
    public function limit($startRecord, $numberOfRecords = null)
    {
        if($this->_limit == null){
            if($numberOfRecords == null && is_int($startRecord + 0)){
                $this->_limit = " LIMIT $startRecord" ;
            }elseif (is_int($startRecord + 0) && is_int($numberOfRecords + 0)){
                $this->_limit = " LIMIT $startRecord, $numberOfRecords" ;
            }
        }
        return $this;

    }

    //[['users' => 'id', 'DESC'], 'name', 'users'=> 'id' ,['surname', 'ASC']]
    //['users'=> 'id']
    public function orderBy($arrayItems)
    {
        $tableName = $this->tableName();
        if($this->_orderBy == null){

            if(is_array($arrayItems)) {
                foreach ($arrayItems as $item => $value){

                    if(!is_array($value)){

                        if(is_string($item)){
                            $this->_orderBy .= " `$item`.`$value`,";
                        }else{
                            $this->_orderBy .= " `$tableName`.`$value`,";
                        }

                    }else{
                        $key = key($value);
                        next($value);
                        $sort = $value[key($value)];
                        $sort = $sort == 'DESC' ? 'DESC' : 'ASC';

                        if(is_string($key)){
                            $this->_orderBy .= " `$key`.`$value[$key]` $sort,";
                        }else{
                            $this->_orderBy .= " `$tableName`.`$value[$key]` $sort,";
                        }

                    }

                }

                $this->_orderBy = substr($this->_orderBy, 0, -1 );
                $this->_orderBy = ' ORDER BY' . $this->_orderBy ;
            }
        }
        return $this;

    }

    //['LEFT', 'surname', 'user'=>'id', 'surname'=> 'user_id' ]
    //['LEFT', 'surname', 'id', 'user_id' ]
    public function addJoin($arrayJoin)
    {
        $join = '';
        $key = key($arrayJoin);
        $operator = $arrayJoin[$key];
        next($arrayJoin);
        $key = key($arrayJoin);
        $tableJoin = $arrayJoin[$key];
        next($arrayJoin);

        for ($i = 0; $i < 2; $i++){
            $key = key($arrayJoin);

            if(is_string($key)){
                $tableName = $key;
                $join .= " `$tableName`.`$arrayJoin[$key]`";
            }else{
                $join .= " `$arrayJoin[$key]`";
            }
            if($i == 0) $join .= " =";
            next($arrayJoin);
        }

        $this->_join .= " $operator JOIN `$tableJoin` ON $join";

        return $this;
    }

    //$arrayValue = ['name' => 'Ислам', 'surname' => 'Ибрагимов', 'subid' => '1', 'schoolid' => '1']
    public function insert($arrayValue)
    {
        $tableName = $this->tableName();
        $names = '';
        $values = '';
        foreach ($arrayValue as $item => $value){
            $names .= " `$item`,";
            $values .= " ?,";
            $this->_insertArray[] = $value;
        }
        $names = substr($names, 0, -1 );
        $values = substr($values, 0, -1 );
        $this->_insert = "INSERT INTO `$tableName` ($names) VALUES ($values)";

        return $this;

    }

//    UPDATE `test`.`users` SET `name`='Казбек4' WHERE  `id`=40;
//    UPDATE `test`.`users` SET `surname`='Дуров4', `name`='Казбек5', `subid`='5', `schoolid`='3' WHERE  `id`=40;
//    $arrayValue = ['name' => 'Ислам', 'surname' => 'Ибрагимов', 'subid' => '1', 'schoolid' => '1']
    public function update($arrayValue)
    {
        $tableName = $this->tableName();
        $update = '';
        $values = '';
        foreach ($arrayValue as $item => $value){
            $values .= " `$item`= ?,";
            $this->_updateArray[] = $value;
        }
        $values = substr($values, 0, -1 );
        $this->_update = "UPDATE `$tableName` SET $values";


    }



}