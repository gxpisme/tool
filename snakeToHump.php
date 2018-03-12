<?php


if (!isset($argv[1]) || !file_exists($argv[1])) {
    exit('file not exists');
}

$fileName = $argv[1];
$obj = new snakeToHump($fileName);
$obj->execute();


/**
* 蛇形命名转成驼峰命名
* 目前仅支持文件
*/
class snakeToHump {

    private $_strFileContent = '';
    private $_strFileName = '';

    public function __construct($fileName) {
        $this->_strFileContent = file_get_contents($fileName);
        $this->_strFileName = $fileName;
    }

    /**
     * 主函数
     * @return [type]           [description]
     */
    public function execute() {
        $arrFileContent = explode(PHP_EOL, $this->_strFileContent);
        $arrLastFileConent = [];
        foreach ($arrFileContent as $strLine) {
            $pattern = '/\$[a-z]+(_[a-z]+)+/';
            preg_match_all($pattern, $strLine, $arrMatches);
            if($arrMatches) {
                $arrMatchObj = $arrMatches[0];
                foreach ($arrMatchObj as $snakeVal) {
                    $arrItem = explode('_', $snakeVal);
                    $humpVal = '';
                    foreach ($arrItem as $strItem) {
                        if ($strItem[0] === '$') {
                            $humpVal .= $strItem;
                        } else {
                            $humpVal .= ucfirst($strItem);
                        }
                    }
                    if ($this->fullTextConflict($strVal)) {
                        echo $snakeVal . ' replace failed. reason is ' . $humpVal . ' exists file' . PHP_EOL;
                        continue;
                    }
                    $strLine = str_replace($snakeVal, $humpVal, $strLine);
                }
            }
            $arrLastFileConent[] = $strLine;
        }
        $lastFileConent = implode(PHP_EOL, $arrLastFileConent);
        file_put_contents($this->_strFileName, $lastFileConent);
    }

    /**
     * 是否全文冲突，冲突则不予替换
     * @param  [type] $strVal [description]
     * @return [type]         [description]
     */
    private function fullTextConflict($strVal) {
        if (false === stripos($this->_strFileContent, $strVal)) {
            return false;
        }
        return true;
    }
}
