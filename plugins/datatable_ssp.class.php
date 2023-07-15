<?php 

class SSP {
    static function complex ( $request, $conn, $table, $primaryKey, $columns, $joinQuery, $whereResult=null, $whereAll=null )
    {
        $bindings = array();
        $db = self::db( $conn, $table, $primaryKey, $columns, $joinQuery, $bindings );
 
        // Build the SQL query string from the request
        $limit = self::limit( $request, $columns );
        $order = self::order( $request, $columns );
        $where = self::filter( $request, $columns, $bindings );
 
        // Custom where
        if ( $whereResult ) {
            $where .= $where ? " AND ($whereResult)" : " WHERE ($whereResult)";
        }
 
        if ( $whereAll ) {
            $where .= $where ? " AND ($whereAll)" : " WHERE ($whereAll)";
        }
 
        // Main query to actually get the data
        $data = self::sql_exec( $db,
            "SELECT SQL_CALC_FOUND_ROWS ".implode(", ", self::pluck($columns, 'db'))."
             FROM $table
             $joinQuery
             $where
             $order
             $limit"
            , $bindings
        );
 
        // Data set length after filtering
        $resFilterLength = self::sql_exec( $db,
            "SELECT FOUND_ROWS()"
        );
        $recordsFiltered = $resFilterLength[0][0];
 
        // Total data set length
        $resTotalLength = self::sql_exec( $db,
            "SELECT COUNT(".$primaryKey.")
             FROM $table"
        );
        $recordsTotal = $resTotalLength[0][0];
 
        /*
         * Output
         */
        return array(
            "draw"            => intval( $request['draw'] ),
            "recordsTotal"    => intval( $recordsTotal ),
            "recordsFiltered" => intval( $recordsFiltered ),
            "data"            => self::data_output( $columns, $data )
        );
    }
}
