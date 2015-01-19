<?php

class database
{
    
    var $connection;
    private $profiling = false;
    

    function database ($host, $user, $password, $database)
        {
            $this->connect($host, $user, $password);
            $this->selectDatabase($database);
            if( $this->profiling )
            {
                $this->enableProfiling();
            }

        }


    public function connect($host, $user, $password )
    {
        if ( !$this->connection = @mysql_connect( $host, $user, $password ) )
        {
            exit ('<h1 style="text-align:center; color: #FF0000">Fatal Error! Please check configuration.</h1>');
        }
    }


    public function selectDatabase($database)
    {
        return mysql_select_db($database, $this->connection);
    }
    
    public function disconnect ()
        {

            if( $this->profiling )
            {
                $this->displayProfiling();
            }
            mysql_close( $this->connection );
            
        }



    /**
     * @param $table
     * @param $data
     * @return bool|int
     */
    public function insertQuery($table, $data)

        {

            $count=0; $fields=''; $values='';

            foreach($data as $field => $value)

                {

                    $glue = $count>0 ? ', ' : '';

                    $fields .= $glue . '`'.$field.'`';

                    $values .= $value == 'NOW()' ? $glue . 'NOW()' : $glue . '"' . cleanData($value) . '"';

                    $count++;

                }

            if(mysql_query('INSERT INTO ' . $table . ' (' . $fields . ') VALUES (' . $values . ')', $this->connection))

                {

                    return mysql_insert_id();

                }

            return false;

        }


    /**
     * Update Database query.
     *
     * @param $table
     * @param $data
     * @param $where
     * @return bool
     */
    function updateQuery($table, $data, $where)

        {

            $count=0;
            $update = "";

            foreach($data as $field => $value)

                {

                    $count>0 ? $update .= ', ' : $update = '';

                    $update .=  $value === 'NOW()' ? $field . "= NOW()" : $field . '="' . cleanData($value) . '"';

                    $count++;

                }


            if(mysql_query('UPDATE ' . $table . ' SET ' . $update . ' WHERE ' . $where . ' LIMIT 1', $this->connection))
                {
                    return true;
                }
            else
                {
                    echo mysql_error();exit();
                    return false;
                }


        }


    function count_all($table_name, $where = false)
    {
        $sql = "SELECT COUNT(*) AS total FROM ".$table_name;
        $sql .= $where ? " WHERE ".$where : "";
        $query = mysql_query($sql, $this->connection);
        if( !$query )
        {
            return false;
        }

        if( !$data = mysql_fetch_assoc($query) )
        {
            return false;
        }

        return $data['total'];

    }



     /**
      * Enable mysql profiling
      * @return void
      */
     function enableProfiling()
     {
         mysql_query('SET profiling = 1');
     }


     /**
      * Display Mysql Profiling data
      * @return void
      */
     function displayProfiling()
     {
         $query = mysql_query("SHOW profiles");
         echo '<table border="1" style="border-collapse: separate;border-spacing: 5px"> ';
         while($data = mysql_fetch_assoc($query))
         {
             echo "<tr><td>$data[Query_ID]</td><td>$data[Duration]</td><td>$data[Query]</td></tr>";
         }
         echo '</table>';
     }

     function __destruct()
     {
         $this->disconnect();
     }
           

}

$database = new database(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

global $database;