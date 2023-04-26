<?php
namespace dynoser\tmpcat;

class CatGrouper {
    /*
     * This class returns an array of categories grouped by parent categories
     */
    
    public $conn;
    
    public $categories_arr;

    public function getCategoriesGrouped()
    {
        // Create empty arrays for results
        $all_parents = [];

        // Will contain &links to $all_parents
        $whereIsParentArr = [];

        foreach($this->walkCategoriesRows() as $cat_id => $parent_id) {

            // if we don't know where parent_id is, then it doesn't exist, and we'll create it
            if (!array_key_exists($parent_id, $whereIsParentArr)) {
                $all_parents[$parent_id] = [];
                $whereIsParentArr[$parent_id] = &$all_parents[$parent_id];
            }
            
            // Get &link to parent from memory
            $parent = &$whereIsParentArr[$parent_id];
            
            // add cat_id into $parent
            if (is_array($parent)) {
                $parent[$cat_id] = $cat_id;
            } else {
                $parent = [$cat_id => $cat_id];
            }
            $whereIsParentArr[$cat_id] = &$parent[$cat_id];
        }
        
        // if the result array contains only one root-level category, return it
        if (count($all_parents) === 1) {
            $root_category = reset($all_parents);
            if (is_array($root_category)) {
                return $root_category;
            }
        }

        // Otherwise, return all categories that were found
        return $all_parents;
    }
    
    public function walkCategoriesRows() {
        if ($this->conn) {

            // Walk source rows by db
            $sql = "SELECT categories_id, parent_id FROM categories";
            $db_result = $conn->query($sql);

            if ($db_result === false) {
                throw new \Exception("Error fetching categories from database");
            }

            while($row = $db_result->fetch_assoc()) {
                yield $row["categories_id"] => $row["parent_id"];
            }
            
        } elseif ($this->categories_arr) {

            // For test needs, we will provide a traversal of the array strings instead of the database
            foreach($this->categories_arr as $pair) {
                yield $pair[0] => $pair[1];
            }

        } else {
            throw new \Exception("No categories data");
        }
    }

    public function dbConnect(
        $servername = "localhost",
        $username = "username",
        $password = "password",
        $dbname = "myDB"
    ) {
        // Connect to DB with specified parameters
        $this->conn = new mysqli($servername, $username, $password, $dbname);
        if ($this->conn->connect_error) {
            throw new \Exception("Connection failed: " . $conn->connect_error);
        }
    }
}
