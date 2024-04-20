<?php
defined('BASEPATH') OR exit('');

class Api_model extends CI_Model {
    // private $UC = 'UNICODE';
    private $iYARD = "ITC";

    function __construct() {
        parent::__construct();
        $this->ceh = $this->load->database('mssql', TRUE);
        // $this->his = $this->load->database('mssqlsvr', TRUE);   
    }

    public function checkHIS_API_loadData($arrs) {
        $this->ceh->where('insert_time >=', $arrs['FormDate'])
                 ->where('insert_time <=', $arrs['ToDate']);
        if ($arrs['Method'] && $arrs['Method'] != "*") {
            $this->ceh->where('Method', $arrs['Method']);
        }
        if ($arrs['TableName'] && $arrs['TableName'] != "*") {
            $this->ceh->where('TableName', $arrs['TableName']);
        }
        if ($arrs['isSuccess'] && $arrs['isSuccess'] != "*") {
            $this->ceh->where('isSuccess', $arrs['isSuccess']);
        }
        if ($arrs['SearchText'] && $arrs['SearchText'] != "") {
            $this->ceh->like('JsonString', $arrs['SearchText']);
        }
        return  $this->ceh->order_by('insert_time', 'desc')->get('HIS_API')->result_array();
    }

    public function checkHIS_API_saveData($arrs) {
        if (!empty($arrs)) {    
            $iSQL = "EXEC dbo.SaveHIS_API @pRowguid=?";
            $xyzt = $this->ceh->query($iSQL, array( $arrs ));
            $dataReturn = $xyzt->row_array();
            if ( intval($dataReturn['ErrorNumber']) < 0) {
                $result['iStatus'] = 'Fail';
            }
            else {
                $result['iStatus'] = 'Success';
            }
            $result['iMess'] = $dataReturn['Error_Msg'];
            return $result;


            // $this->ceh->trans_begin();
            // $this->ceh->trans_strict(FALSE);
            // foreach ($arrs as $key => $item) { 
            //     if(isset($itemX)) unset($itemX);
            //     $itemX = $this->ceh->where('rowguid', $item['rowguid'])->get("HIS_API")->row_array();
            //     if (is_array($itemX) && count($itemX) > 0) { 
            //         $arrayUpdate = array (
            //             'insert_time' => date('Y-m-d H:i:s'),
            //             'JsonString' => $item['JsonString'],
            //             'SoLanGui' => 0,
            //         );
            //         $this->ceh->where('rowguid', $itemX['rowguid'])->update("HIS_API", $arrayUpdate);
            //     }
            // }
            // $this->ceh->trans_complete();
            // if ($this->ceh->trans_status() === FALSE) {
            //     $this->ceh->trans_rollback();
            //     $result['iStatus'] = 'Fail';
            //     $result['iMess'] = 'Phát sinh lỗi khi cập nhật!';
            // } 
            // else {
            //     $this->ceh->trans_commit();
            //     $result['iStatus'] = 'Success';
            //     $result['iMess'] = 'Cập nhật thành công!';
            // }
            // return $result;
        }
    }
}
