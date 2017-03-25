<?php
class MongoBase{
	private $host = '127.0.0.1';
	private $port = '27017';
	private $db;
	private $collection;

	private $manager;

	private $writeConcern;

	private $bulk;


	public function __construct($host,$port,$db,$collection){
		$this->host = $host;
		$this->port = $port;
		$this->db = $db;
		$this->collection = $collection;
		$this->manager = new MongoDB\Driver\Manager("mongodb://".$this->host.":".$this->port);//链接mongodb
		$this->writeConcern = new MongoDB\Driver\writeConcern('majority',100);
		$this->bulk = new MongoDB\Driver\BulkWrite();//增删改

	}

	//'allowPartialResults', 'batchSize', 'comment', 'limit', 'maxTimeMS', 'noCursorTimeout', 'oplogReplay', 'projection', 'readConcern', 'skip', 'sort'
	// link  http://php.net/manual/zh/mongodb-driver-manager.executequery.php
	public function mongoSelect($filter=array(),$option=array()){
		$query  = new MongoDB\Driver\Query($filter,$option);
		$cursor = $this->manager->executeQuery($this->db.'.'.$this->collection,$query);
		foreach ($cursor as $key => $value) {
			$arr[] = $value;
			# code...
		}
		return $arr;
		        


	}

	/**
	 * [mongoInsetBulk 新增数据]
	 * @author 1023
	 * @date          2017-03-25
	 * @param  [type] $arr       [description]
	 * $arr = array("name"=>"xxx","address"=>"xxxxyyyyx");
	 * 键=>值
	 * @return [type]            [description]
	 */
	public function mongoInsetBulk($data){
		$this->bulk->insert($data);      
		try{
			$result = $this->manager->executeBulkWrite($this->db.'.'.$this->collection,$this->bulk,$this->writeConcern);  
			return $result->getInsertedCount();
		}catch(Exception $e){
			return $e->getMessage();
		}finally{

		}
	}
	/**
	 * [mongoUpdateBulk 修改数据]
	 * @author 1023
	 * @date           2017-03-25
	 * @param  [type]  $where     [where条件]
	 * @param  [type]  $data      [要修改的数据]
	 * @param  boolean $multi     [是否修改多行]
	 * @param  boolean $upsert    [如果没有数据，选择新增操作]
	 * @return [type]             [int]
	 */
	public function mongoUpdateBulk($filter,$data,$multi=true,$upsert=true){
		$this->bulk->update($filter,$data,array("multi"=>$multi,"upsert"=>$upsert));
		try{
			$result = $this->manager->executeBulkWrite($this->db.'.'.$this->collection,$this->bulk,$this->writeConcern);  
			return $result->getModifiedCount();//返回插入行数
		}catch(Exception $e){
			return $e->getMessage();
		}finally{

		}
		        
	}

	/**
	 * [mongoDeleteBulk 删除指定文档]
	 * @author 1023
	 * @date           2017-03-25
	 * @param  [type]  $filter    [where条件]
	 * @param  boolean $limit     [false:删除所有符合的，1:一行]
	 * @return [type]             [description]
	 */
	public function mongoDeleteBulk($filter,$limit=false){
		$this->bulk->delete($filter,array("limit"=>$limit));
		try{
			$result = $this->manager->executeBulkWrite($this->db.'.'.$this->collection,$this->bulk,$this->writeConcern);  
			return $result->getDeletedCount();//返回插入行数
		}catch(Exception $e){
			return $e->getMessage();
		}finally{

		}
	}

	public function __destruct(){
		unset($host);
		unset($port);
		unset($db);
		unset($collection);
		unset($manager);
		unset($writeConcern);
		unset($bulk);

	}


}
// for ($i=0; $i <100 ; $i++) { 
	$mongo  = new MongoBase("127.0.0.1","27017","testdb","collection");
	// $arr = array("username"=>"add","x"=>"123");
	$where = array("username"=>"add");
	// $data = array('$set'=>array("x"=>"ethan"));
	$mongo->mongoSelect();

	        

	# code...
	// $result = $mongo->mongoInsetBulk($arr);
// }
// echo "<pre>";
// var_dump($result);
// die;
























?>