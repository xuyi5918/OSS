<?php 
	require_once './autoload.php';
	use Qiniu\Auth;
	use Qiniu\Storage\UploadManager;
	use Qiniu\Storage\BucketManager;


	class oss {

		public $accessKey	=	null;
		public $secretKey	=	null;
		public $bucket		=	null;

		public $Auth		=	null;		#保存权限类对象

		public $tokenAuth	=	null;		#保存生成的token值

		public $upload		=	null;

		public $manage		=	null;


		public function run(){

			try{
				/** 实例化权限类 **/
				if(!is_object($this->Auth)){
					$this->Auth = new Auth($this->accessKey,$this->secretKey);
				}

				$this->tokenAuth=$this->Auth->uploadToken($this->bucket);	#生成token
				/** 实例化上传类 **/
				if(!is_object($this->upload)){
					$this->upload = new UploadManager();	#实例化上传类
				}
				/** 管理类 **/
				if(!is_object($this->manage)){

					$this->manage = new  BucketManager($this->Auth);
				}


			}catch(Exception $e){
				return false;
			}

		}

		/**
		 * 选择远程 bucket
		 *
		 * @param null $bucket
		 * @return $this
		 */
		public function select($bucket=null){
			if(is_null($bucket) || $bucket !==''){
				$this->bucket=$bucket;
			}
			$this->tokenAuth=$this->Auth->uploadToken($this->bucket); #重新生成token
			return $this;
		}


		public function renameFiles($key=null){
			if(is_null($key)){

			}
		}

		/**
		 * 删除远程文件
		 *
		 * @param null $key
		 * @return bool
		 */
		public function deleteFiles($key=null){
			if(!is_array($key)){
				if($this->checkExists($key)){
					return $this->manage->delete($this->bucket,$key)===null?true:false;
				}else{
					return true;
				}
			}else{
				foreach($key as $item=>$value){
					$this->manage->delete($this->bucket,$value);
				}
				return true;
			}
		}

		/**
		 * 判断文件是否存在
		 *
		 * @param null $key
		 * @return bool
		 */
		public  function checkExists($key=null){
			if(!is_null($key)){
				$info= $this->check_error($this->manage->stat($this->bucket,$key));
				if(!is_object($info)){
					return true;
				}
					return false;
			}
			return false;
		}
		/**
		 * 列出空间中的所有文件
		 *
		 * @param null $bcket
		 * @param int $limit
		 * @param string $prefix
		 * @param string $marker
		 */
		public function getList($limit=3,$prefix='',$marker='' ){

			$list=$this->manage->listFiles($this->bucket,$prefix,$marker,$limit);
			list($iterms, $marker, $err) = $list;
			if($err !== null){
				return $err;
			}else{
				return $iterms;
			}

		}
		/**
		 * 上传文件方法
		 *
		 * @param $key
		 * @param $uploadFile
		 */
		public function uploadFile($key,$uploadFile){
			try{
				$info=$this->check_error($this->upload->putFile($this->tokenAuth,$key,$uploadFile));
				if(is_object($info)){
					return false;
				}else{
					return true;
				}
			}catch(Exception $e){
				return false;
			}

		}

		/**
		 * 上传内存中的数据到服务器
		 *
		 * @param $key
		 * @param $value
		 * @return bool
		 */
		function uploadAccess($key,$value){
			try{
				$this->check_error($this->upload->put($this->tokenAuth,$key,$value));
			}catch(Exception $e){
				return false;
			}
		}

		/**
		 * 检测返回状态
		 *
		 * @param null $check_error
		 */
		private function check_error($check_error=null){
			if(!is_null($check_error)){
				list($result,$error)	=	$check_error;

				if ($error !== null) {
					return ($error);
				} else {
					return ($result);
				}
			}
		}


	}

$oss=new oss();
$oss->accessKey='u-yTGzEYucRgOyUhfykJYeTQmya2ENUucR3rXwyk';
$oss->secretKey='gLDFqQOR-lyT4EqbfiAqpuFxiZkuojv5315UZauO';
$oss->bucket='ipensoft';
$oss->run();
$a=$oss->select('ipensoft')->uploadFile('index.html','E:\index.html');

var_dump($a);


//$oss->uploadFile('./php/index.html','E:\index.html');