<?php
ini_set('display_errors', 1);

function array2str($array, $pre = '', $pad = '', $sep = '')
{
    $str = '';
    if(is_array($array)) {
        if(count($array)) {
            foreach($array as $v) {
                $str .= $pre.$v.$pad.$sep;
            }
            $str = substr($str, 0, -strlen($sep));
        }
    } else {
        $str .= $pre.$array.$pad;
    }

    return $str;
}


class TestController
{
    /**
     * Returns a JSON string object to the browser when hitting the root of the domain
     *
     * @url GET /
     */
    public function test()
    {
        return "Hello World";
    }

    /**
     * @url GET /coincoin
     */
    public function coincoin()
    {
        return "coincoin";
    }
 
     /**
     * @url GET /products
     */
    public function products()
    { 
	$m = new MongoClient("localhost:7777");
	$db = $m->selectDB('off');
	$collection = new MongoCollection($db, 'products');
	$i=0;
	$value;
	$cursor = $collection->find();
	$num_docs = $cursor->count();
	echo $num_docs;
	foreach ($cursor as $doc) {
   // 		$value = $value + $doc['text'];
		$i++;
		if($i>20) break;
	}	
	return $value;
    }

    /**
     * @url GET /product/$barecode
     */
    public function product($barecode = null)
    {
        $m = new MongoClient("localhost:7777");
        $db = $m->selectDB('off');
        $collection = new MongoCollection($db, 'products');
        $i=0;
        $value;
        //$cursor = $collection->findOne(array('code' => $barcode));
	//$cursor = $collection->findOne(array("products.code" =>"{$barecode}"));
	$cursor = $db->execute('db.products.findOne({"code":"'.$barecode.'"})');
	//print_r(
	//var_dump($cursor);
        return $cursor;
    }

    /**
     * Logs in a user with the given username and password POSTed. Though true
     * REST doesn't believe in sessions, it is often desirable for an AJAX server.
     *
     * @url POST /login
     */
    public function login()
    {
        $username = $_POST['username'];
        $password = $_POST['password'];
        return array("success" => "Logged in " . $username);
    }

    /**
     * Gets the user by id or current user
     *
     * @url GET /users/$id
     * @url GET /users/current
     */
    public function getUser($id = null)
    {
        // if ($id) {
        //     $user = User::load($id); // possible user loading method
        // } else {
        //     $user = $_SESSION['user'];
        // }

        return array("id" => $id, "name" => null); // serializes object into JSON
    }

    /**
     * Saves a user to the database
     *
     * @url POST /users
     * @url PUT /users/$id
     */
    public function saveUser($id = null, $data)
    {
        // ... validate $data properties such as $data->username, $data->firstName, etc.
        // $data->id = $id;
        // $user = User::saveUser($data); // saving the user to the database
        $user = array("id" => $id, "name" => null);
        return $user; // returning the updated or newly created user object
    }
}
