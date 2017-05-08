<?php 
/**
* @desc 上传文件类
* @version 1.1
*/
/**
 *使用说明
 * $up = new upload(上传路径,允许格式,允许大小(Byte),是否允许覆盖,缩略图信息array('prefix'=>'前缀','width'=>'宽','height'=>'高'));
 * $up->up(input表单名,文件命名方式)
 */
class upload{
	private $saveName;// 上传文件保存的文件名
	private $savePath;// 保存路径
	private $fileFormat = array('jpg','jpeg','gif','bmp','png','swf','doc','docx','xls','ppt','wps','pdf','txt','rar','zip');// 默认允许上传的文件格式
	private $overwrite = 0;// 覆盖模式
	private $maxSize = 0;// 文件最大字节
	private $thumb = array(); // 缩略图信息
	private $thumbPrefix = "thumb_";// 缩略图前缀
	private $errno; // 错误代号
	private $fileArray= array();// 所有文件的返回信息
	private $fileInfo= array();// 每个文件返回信息

	/**
	 * @desc 构造方法
	 *
	 * @param string $savePath 文件保存路径
	 * @param string $fileFormat 文件格式限制数组
	 * @param int $maxSize 上传文件最大字节数
	 * @param int $overwrite 是否覆盖 1 允许覆盖 0 禁止覆盖
	 */
	function __construct($savePath, $fileFormat='jpg|jpeg|gif|bmp|png|swf|doc|docx|xls|ppt|pdf|wps|zip|rar|txt',$maxSize = 0, $overwrite = 0 ,$thumb = array('prefix'=>'','width'=>'','height'=>'')) {
		$this->setSavepath($savePath); //设置上传路径
		$this->setFileformat($fileFormat); //设置允许上传的文件格式
		$this->maxSize = $maxSize; //上传文件最大字节数
		$this->overwrite = $overwrite; //覆盖模式 1:允许覆盖 0:禁止覆盖
		$this->setThumb($thumb); //产生缩略图
		$this->errno = 0; //错误代码
	}

	/**
	 * @desc 上传文件
	 *
	 * @param string $fileInput 网页Form(表单)中input的名称
	 * @param int $changeName 传文件名 0:系统生成名称 1：采用原名 string：采用string做文件名
	 * @return boolean
	 */
	public function up($fileInput,$fileName = 0){
		if(isset($_FILES[$fileInput])){ //上传文件input存在
			$fileArr = $_FILES[$fileInput];
			if(is_array($fileArr['name'])) { //上传同文件域名称多个文件
				for($i = 0; $i < count($fileArr['name']); $i++){
					$fileInfo = array();
					$fileInfo['tmp_name'] = $fileArr['tmp_name'][$i]; //临时文件
					$fileInfo['name'] = $fileArr['name'][$i]; //上传文件原名
					$fileInfo['type'] = $fileArr['type'][$i]; //文件的 MIME 类型
					$fileInfo['size'] = $fileArr['size'][$i]; //已上传文件的大小，单位为字节
					$fileInfo['error'] = $fileArr['error'][$i]; //错误代码，0表示没有错误发生
					if ($fileInfo['error'] == 0) { //系统默认情况下，没有发生错误
						$fileInfo['ext'] = $this->getExt($fileInfo['name']); //文件扩展名
						$this->setSavename($fileName===1 ? $fileInfo['name'] : $fileName,$fileInfo['ext']);//设置文件上传以后的命名，默认为时间+时间数命名
						if($this->copyfile($fileInfo)) { //如果上传文件成功
							$this->fileArray[] =  $this->fileInfo;
						} else {
							$this->fileInfo['error'] = $this->errno;
							$this->fileArray[] =  $this->fileInfo;
						}
					} else {
						$this->fileArray[] =  $fileInfo;
					}

				}
				foreach ($this->fileArray as $line) {
					if ($line['error']) return false;
				}
				return true;
				//return $this->errno ?  false :  true;
			}else{//上传单个文件
				if ($fileArr['error'] == 0) { //系统默认情况下，没有发生错误
					$fileArr['ext'] = $this->getExt($fileArr['name']); //文件扩展名
					$this->setSavename($fileName===1 ? $fileArr['name'] : $fileName,$fileArr['ext']);//设置保存文件名
					if($this->copyfile($fileArr)){ //如果上传成功
						$this->fileArray =  $this->fileInfo;
					}else{
						$this->fileInfo['error'] = $this->errno;
						$this->fileArray =  $this->fileInfo;
					}
				} else {
					$this->errno = $fileArr['error'];
					$this->fileArray =  $fileArr;
				}
				return $this->errno ?  false :  true;
			}
			return false;
		}else{
			$this->errno = 10;
			$this->fileInfo['error'] = $this->errno;
			$this->fileArray =  $this->fileInfo;
			return false;
		}
	}

	/**
	 * 上传文件操作
	 *
	 * @param array $fileInfo 上传文件信息数组
	 * @return boolean
	 */
	private function copyfile($fileInfo){
		$this->fileInfo = array();
		// 返回信息
		$this->fileInfo['name'] = $fileInfo['name']; //上传文件原名
		$this->fileInfo['saveName'] = $this->saveName; //文件上传以后的名字
		$this->fileInfo['size'] = number_format($fileInfo['size'] / 1024 ,2,'.','')."KB"; //以KB为单位
		$this->fileInfo['type'] = $fileInfo['type']; //文件的 MIME 类型
		$this->fileInfo['error'] = $fileInfo['error'];
		
		//判断文件是否是通过 HTTP POST 上传的
		if(!is_uploaded_file($fileInfo['tmp_name'])) {
			$this->error = 23;
			return false;
		}
		
		// 检查文件格式
		if (!$this->validateFormat($fileInfo['ext'])){
			$this->errno = 11;
			return false;
		}
		//检查目录，或者创建目录
		if(!is_dir($this->savePath) && !mkdir($this->savePath, 0777,true)){
			$this->error = 24;
			return false;
		}
		// 检查目录是否可写
		if(!@is_writable($this->savePath)){
			$this->errno = 12;
			return false;
		}
		// 如果不允许覆盖，检查文件是否已经存在
		if($this->overwrite == 0 && @file_exists($this->savePath.$this->saveName)){
			$this->errno = 13;
			return false;
		}
		// 如果有大小限制，检查文件是否超过限制
		if ($this->maxSize != 0 ){
			if ($fileInfo["size"] > $this->maxSize){
				$this->errno = 14;
				return false;
			}
		}

		// 文件上传
		if(!move_uploaded_file($fileInfo["tmp_name"], $this->savePath.$this->saveName)){
			$this->errno = $fileInfo["error"];
			return false;
		}
		if( $this->thumb && in_array($fileInfo['ext'],array('jpg','jpeg','gif','bmp','png'))){// 创建缩略图
			$thumb=$this->thumb;
			$CreateFunction = "imagecreatefrom".($fileInfo['ext'] == 'jpg' ? 'jpeg' : $fileInfo['ext']);
			$SaveFunction = "image".($fileInfo['ext'] == 'jpg' ? 'jpeg' : $fileInfo['ext']);
			if (strtolower($CreateFunction) == "imagecreatefromgif" && !function_exists("imagecreatefromgif")) { //系统不支持imagecreatefromgif
				$this->errno = 16;
				return false;
			} elseif (strtolower($CreateFunction) == "imagecreatefromjpeg" && !function_exists("imagecreatefromjpeg")) { //系统不支持imagecreatefromjpeg
				$this->errno = 17;
				return false;
			} elseif (!function_exists($CreateFunction)) { //其他系统不支持的函数
				$this->errno = 18;
				return false;
			}

			$Original = @$CreateFunction($this->savePath.$this->saveName); //读取源图
			if (!$Original) {$this->errno = 19; return false;} //读取不成功
			$originalHeight = ImageSY($Original); //取得图像高度
			$originalWidth = ImageSX($Original); //取得图像宽度
			$this->fileInfo['originalHeight'] = $originalHeight;
			$this->fileInfo['originalWidth'] = $originalWidth;
			if (($originalHeight < $thumb['height'] && $originalWidth < $thumb['width'])) { // 如果比期望的缩略图小，那只Copy
				@copy($this->savePath.$this->saveName,$this->savePath.$thumb['prefix'].$this->saveName);
			} else {
				if( $originalWidth > $thumb['width'] ){// 宽 > 设定宽度
					$thumbWidth = $thumb['width'] ;
					$thumbHeight = $thumb['width'] * ( $originalHeight / $originalWidth ); //获取按比列缩小的高
					if($thumbHeight > $thumb['height']){// 如果缩小的高度仍然 > 设定高度
						$thumbWidth = $thumb['height'] * ( $thumbWidth / $thumbHeight ); //再按比列获取缩小的宽
						$thumbHeight = $thumb['height'] ;
					}
				}elseif( $originalHeight > $thumb['height'] ){// 高 > 设定高度
					$thumbHeight = $thumb['height'] ;
					$thumbWidth = $thumb['height'] * ( $originalWidth / $originalHeight );
					if($thumbWidth > $thumb['width']){// 宽 > 设定宽度
						$thumbHeight = $thumb['width'] * ( $thumbHeight / $thumbWidth );
						$thumbWidth = $thumb['width'] ;
					}
				}
				$thumbWidth = round($thumbWidth);
				$thumbHeight = round($thumbHeight);
				if ($thumbWidth == 0) $thumbWidth = 1;
				if ($thumbHeight == 0) $thumbHeight = 1;
				$this->fileInfo['thumb']['thumbWidth'] = $thumbWidth;
				$this->fileInfo['thumb']['thumbHeight'] = $thumbHeight;
				$createdThumb = imagecreatetruecolor($thumbWidth, $thumbHeight); //新建一个真彩色图像
				if ( !$createdThumb ) {$this->errno = 20; return false;}
				if ( !imagecopyresampled($createdThumb, $Original, 0, 0, 0, 0,$thumbWidth, $thumbHeight, $originalWidth, $originalHeight) ) //重采样拷贝部分图像并调整大小
				{$this->errno = 21; return false;}
				if ( !$SaveFunction($createdThumb,$this->savePath.$thumb['prefix'].$this->saveName) ) { //以 JPEG 格式将图像输出到浏览器或文件
					$this->errno = 22;
					return false;
				} else {
					$this->fileInfo['thumb']['name'] = $thumb['prefix'].$this->saveName ;
					$this->fileInfo['thumb']['size'] = number_format(filesize($this->savePath.$thumb['prefix'].$this->saveName) / 1024,2,'.','') ."KB" ;
				}
			}
		}
		// 删除临时文件
		if (file_exists($fileInfo["tmp_name"])) {
			if(!@$this->del($fileInfo["tmp_name"])){
				return false;
			}
		}
		return true;
	}
	/**
	 * 获取上传文件信息
	 *
	 * @return array
	 */
	public function getFileInfo(){
		return $this->fileArray;
	}
	/**
	 * 文件格式检查
	 *
	 * @param string $ext 文件扩展名
	 * @return boolean
	 */
	private function validateFormat($ext) {
		return in_array(strtolower($ext), $this->fileFormat);
	}

	/**
	 * 获取文件扩展名
	 *
	 * @param string $fileName 上传文件的原文件名
	 */
	private function getExt($fileName){
		$ext = explode('.', $fileName);
		$ext = $ext[count($ext) - 1];
		return strtolower($ext);
	}

	/**
	 * 设置文件格式限定
	 *
	 * @param string $fileFormat
	 */
	function setFileformat($fileFormat){
		if ($fileFormat)
		$this->fileFormat = explode('|',$fileFormat) ;
	}


	/**
	 * 设置保存路径
	 *
	 * @param string $savePath
	 */
	private function setSavepath($savePath) {
		$this->savePath = substr( str_replace("\\","/", $savePath) , -1) == "/" ? $savePath : $savePath."/";
	}

	/**
	 * 设置缩略图.
	 *
	 * @param array $thumb 缩略图信息
	 */
	private function setThumb($thumb) {
		if ($thumb['width'] != '' && $thumb['height'] != '') {
			$thumb['prefix'] = $thumb['prefix']== '' ? $this->thumbPrefix : $thumb['prefix']; //设置缩略图前缀
			$this->thumb = $thumb;
		}
	}

	/**
	 * 设置文件上传以后的文件名
	 *
	 * @param string $saveName 文件名，如果为空，则系统根据时间自动生成一个随机的文件名
	 */
	private function setSavename($fileName,$ext){
		if ($fileName === 0)  // 如果未设置文件名，则生成一个随机文件名
		$name = date('YmdHis').rand(100,999).'.'.$ext;
		else
		$name = $fileName;
		$this->saveName = $name;
	}

	/**
	 * 删除文件
	 *
	 * @param string $fileName 所要删除的文件名
	 * @return boolean
	 */
	public function del($fileName){
		if(!@unlink($fileName)){
			$this->errno = 15;
			return false;
		}
		return true;
	}

	/**
	 * 得到错误信息
	 *
	 * @return mixed
	 */
	public function errmsg(){
		$uploadClassError = array(
		0	=>'文件上传成功.',
		1	=>'上传的文件超过了 php.ini 中 upload_max_filesize 选项限制的值.',
		2	=>'上传文件的大小超过了 HTML 表单中 MAX_FILE_SIZE 选项指定的值.',
		3	=>'文件只有部分被上传. ',
		4	=>'没有文件被上传. ',
		6	=>'找不到临时文件夹. ',
		7	=>'文件写入失败. ',
		10	=>'Input name is not unavailable!',
		11	=>'不允许上传该类型文件!',
		12	=>'附件目录没有写入权限!',
		13	=>'发现同名文件!',
		14	=>'文件超过了管理员限定的大小!',
		15	=>'删除临时文件失败!',
		16	=>'Your version of PHP does not appear to have GIF thumbnailing support.',
		17	=>'Your version of PHP does not appear to have JPEG thumbnailing support.',
		18	=>'Your version of PHP does not appear to have pictures thumbnailing support.',
		19	=>'An error occurred while attempting to copy the source image .
					Your version of php ('.phpversion().') may not have this image type support.',
		20	=>'An error occurred while attempting to create a new image.',
		21	=>'An error occurred while copying the source image to the thumbnail image.',
		22	=>'An error occurred while saving the thumbnail image to the filesystem.
					Are you sure that PHP has been configured with both read and write access on this folder?',
		23	=>'非法上传文件.',
		24	=>'创建目录失败.',
		);
		return $uploadClassError[$this->errno];
	}
}
?>