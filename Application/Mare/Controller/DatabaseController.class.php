<?php
namespace Mare\Controller;
use Think\Controller;
use Think\Db;
use Org\DB\Database;


/**
 * 数据库备份还原控制器
 * @author 
 */
class DatabaseController extends CommonController{
    protected  $allow_backup_table_arr = [
            'at_analysis_results',
            'at_appinfo',
            'at_customrules',
            'at_expand',
            'at_licinfo',
            'at_user',
            'at_userauth',
            'at_usertoken',
            'at_version_upgrade',
            'at_vulinfo'
    ];
    
    public function __construct(){
        parent::__construct();
        $tid 			= $_SESSION[$_SESSION['randomstr']]['tid'];
        if ($tid != 5) {
            $this->error('没有权限操作数据库管理');
        }
    }
    
    /**
     * 数据库备份/还原列表
     * @param  String $type import-还原，export-备份
     * @author 
     */
    public function index($type = null){
        //列出备份文件列表
        $path = C('DATA_BACKUP_PATH');
        if(!is_dir($path)){
            mkdir($path, 0755, true);
        }
        $path = realpath($path);
        $flag = \FilesystemIterator::KEY_AS_FILENAME;
        $glob = new \FilesystemIterator($path,  $flag);
        $list = array();
        foreach ($glob as $name => $file) {
            if(preg_match('/^\d{8,8}-\d{6,6}-\d+\.sql(?:\.gz)?$/', $name)){
                $info['backup_name'] = $name;
                $name = sscanf($name, '%4s%2s%2s-%2s%2s%2s-%d');
                $date = "{$name[0]}-{$name[1]}-{$name[2]}";
                $time = "{$name[3]}:{$name[4]}:{$name[5]}";
                $part = $name[6];

                if(isset($list["{$date} {$time}"])){
                    $info = $list["{$date} {$time}"];
                    $info['part'] = max($info['part'], $part);
                    $info['size'] = $info['size'] + $file->getSize();
                } else {
                    $info['part'] = $part;
                    $info['size'] = $file->getSize();
                }
                $extension        = strtoupper(pathinfo($file->getFilename(), PATHINFO_EXTENSION));
                $info['compress'] = ($extension === 'SQL') ? '-' : $extension;
                $info['time']     = strtotime("{$date} {$time}");
                $list["{$date} {$time}"] = $info;
                
            }
        }
        arsort($list);
        $this->assign('list', $list);
        $this->display();
    }

    /**
     * 删除备份文件
     * @param  Integer $time 备份时间
     * @author qxn
     */
    public function del(){
    	if(IS_POST){    
    	    if(I('post.time')){
    	        $name  = date('Ymd-His', I('post.time')) . '-*.sql*';
    	        $path  = realpath(C('DATA_BACKUP_PATH')) . DIRECTORY_SEPARATOR . $name;
    	        array_map("unlink", glob($path));//glob() 函数返回匹配指定模式的文件名或目录。
    	        if(count(glob($path))){
    	            $this->ajaxReturn(array('code'=>'-1', 'status' => 'error', 'msg'=>['info'=>'备份文件删除失败，请检查权限！']));
    	        } else {
    	            $this->ajaxReturn(array('code'=>'1', 'status' => 'success', 'msg'=>['info'=>'备份文件删除成功！']));
    	        }
    	    } else {
        		$ids = I('ids');
        		$ids = substr($ids, 0, -1);
        		$ids = explode(',', $ids);
    
        		foreach ($ids as $k => $v){
        			$path  = realpath(C('DATA_BACKUP_PATH')) . DIRECTORY_SEPARATOR . date('Ymd-His', $v) . '-*.sql*';
        			array_map("unlink", glob($path));
         			if(count(glob($path))){
         			    $this->ajaxReturn(array('code'=>'-1', 'status' => 'error', 'msg'=>['info'=>'备份文件删除失败，请检查权限！']));
        			    break;
        			}
        			
        		}
        		$this->ajaxReturn(array('code'=>'1', 'status' => 'success', 'msg'=>['info'=>'备份文件删除成功！']));
    	    }
    	}else {
    	    $this->ajaxReturn(array('code'=>'-1', 'status' => 'error', 'msg'=>['info'=>'参数错误！']));
        }
    }

    public function del_lock() {
        $path = C('DATA_BACKUP_PATH');
        $lock = realpath($path) . DIRECTORY_SEPARATOR."/backup.lock";
        if(is_file($lock)){
           $result =  unlink($lock);
           if ($result) {
               $this->ajaxReturn(array('code'=>'1', 'status' => 'success', 'msg'=>['info'=>'备份文件删除成功！']));
           } else {
               $this->ajaxReturn(array('code'=>'-1', 'status' => 'error', 'msg'=>['info'=>'删除失败，请检查权限！']));
           }
        } else {
            $this->ajaxReturn(array('code'=>'-1', 'status' => 'error', 'msg'=>['info'=>'文件不存在！']));
        }
    }
    
    /**
     * 备份数据库
     * @param  String  $tables 表名
     * @param  Integer $id     表ID
     * @param  Integer $start  起始行数
     * @author 
     */
    public function export($id = null, $start = null){
        if(IS_POST){
            $path = C('DATA_BACKUP_PATH');
            if(!is_dir($path)){
                mkdir($path, 0755, true);
            }
            if (I('post.del')){
                $path = C('DATA_BACKUP_PATH');
                $lock = realpath($path) . DIRECTORY_SEPARATOR."/backup.lock";
                unlink($lock);
            }
            //读取备份配置
            $config = array(
                'path'     => realpath($path) . DIRECTORY_SEPARATOR,
                'part'     => C('DATA_BACKUP_PART_SIZE'),
                'compress' => C('DATA_BACKUP_COMPRESS'),
                'level'    => C('DATA_BACKUP_COMPRESS_LEVEL'),
            );
            
            $lock = "{$config['path']}backup.lock";
            if(is_file($lock)){
                //$this->error('检测到有一个备份任务正在执行，请稍后再试！');
                $this->ajaxReturn(array('status' => '-2', 'info'=>'检测到有一个备份任务正在执行，请确认是否重新执行备份！'));
            } else {
                 file_put_contents($lock, NOW_TIME);
            }
            
            is_writeable($config['path']) || $this->error('备份目录不存在或不可写，请检查后重试！');
            session('backup_config', $config);
            
            $file = array(
                'name' => date('Ymd-His', NOW_TIME),
                'part' => 1,
            );
            session('backup_file', $file);

            session('backup_tables', $this->allow_backup_table_arr);

            $Database = new Database($file, $config);

            if(false !== $Database->create()){
                $tab = array('id' => 0, 'start' => 0);
                $this->success('初始化成功！', '', array('tables' => session('backup_tables'), 'tab' => $tab));
            } else {
                $this->error('初始化失败，备份文件创建失败！');
            }
        } elseif (IS_GET && is_numeric($id) && is_numeric($start)) {
            $tables = session('backup_tables');
            $Database = new Database(session('backup_file'), session('backup_config'));
            $start  = $Database->backup($tables[$id], $start);
            if(false === $start){
                $this->error('备份出错！');
            } elseif (0 === $start) {
                if(isset($tables[++$id])){
                    $tab = array('id' => $id, 'start' => 0);
                    $this->success("备份完成！", '', array('tab' => $tab));
                    //$this->success("表({$tables[$id-1]})备份完成！", '', array('tab' => $tab));
                } else { 
                    unlink(session('backup_config.path') . 'backup.lock');
                    session('backup_tables', null);
                    session('backup_file', null);
                    session('backup_config', null);
                    $this->success('备份完成！');
                }
            } else {
                $tab  = array('id' => $id, 'start' => $start[0]);
                $rate = floor(100 * ($start[0] / $start[1]));
                $alert_table = strtolower($tables[$id]);
                $this->success("正在备份中...({$rate}%)", '', array('tab' => $tab));
            }
            
        } else {
            $this->error('参数错误！');
        }
    }
    
    /**
     * 还原数据库
     * @author 
     */
    public function import($time = 0, $part = null, $start = null){
        if(is_numeric($time) && is_null($part) && is_null($start)){ //初始化
            //获取备份文件信息
            $name  = date('Ymd-His', $time) . '-*.sql*';
            $path  = realpath(C('DATA_BACKUP_PATH')) . DIRECTORY_SEPARATOR . $name;
            $files = glob($path);
            $list  = array();
            foreach($files as $name){
                $basename = basename($name);
                $match    = sscanf($basename, '%4s%2s%2s-%2s%2s%2s-%d');
                $gz       = preg_match('/^\d{8,8}-\d{6,6}-\d+\.sql.gz$/', $basename);
                $list[$match[6]] = array($match[6], $name, $gz);
            }

            ksort($list);
            //检测文件正确性
            $last = end($list);
            if(count($list) === $last[0]){
                session('backup_list', $list);
                $this->success('初始化完成！', '', array('part' => 1, 'start' => 0));
            } else {
                $this->error('备份文件可能已经损坏，请检查！');
            }
        } elseif(is_numeric($part) && is_numeric($start)) {
            $list  = session('backup_list');
            $db = new Database($list[$part], array(
                'path'     => realpath(C('DATA_BACKUP_PATH')) . DIRECTORY_SEPARATOR,
                'compress' => $list[$part][2]));

            $start = $db->import($start);

            if(false === $start){
                $this->error('还原数据出错！');
            } elseif(0 === $start) { //下一卷
                if(isset($list[++$part])){
                    $data = array('part' => $part, 'start' => 0);
                    $this->success("正在还原...#{$part}", '', $data);
                } else {
                    session('backup_list', null);
                    $this->success('还原完成！');
                }
            } else {
                $data = array('part' => $part, 'start' => $start[0]);
                if($start[1]){
                    $rate = floor(100 * ($start[0] / $start[1]));
                    $this->success("正在还原...#{$part} ({$rate}%)", '', $data);
                } else {
                    $data['gz'] = 1;
                    $this->success("正在还原...#{$part}", '', $data);
                }
            }

        } else {
            $this->error('参数错误！');
        }
    }

}
