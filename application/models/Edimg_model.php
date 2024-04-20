<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once APPPATH.'third_party/edifact/vendor/autoload.php';

Class Edimg_model extends CI_Model {

    private $ceh;
	
	private $skipUp = array('OCL');

    public function __construct() {
        parent::__construct();
		
		if(in_array($this->session->userdata("oprid"), array('kmtc','hua','hlag','oocl_test', 'hmm_test', 'maerskline_test', 'ducdv_test'))) {
			$this->ceh = $this->load->database('mssql_test' , TRUE);
		} else {
			$this->ceh = $this->load->database('mssql' , TRUE);
		}

        $this->yard_id = $this->config->item("YARD_ID");
    }

	/**
    * EDImg Class
    *
    * method rCOREOR($localFile)
    *
    * Đọc file EDI, insert DB
    *
    * @param	string	$localFile
    * @return	no
    */
	
    public function rCOREOR($localFile) {
		$checkContent = file_get_contents($localFile);
		
		if(!preg_match('/UNA|EQD|UNB|TDT|RFF|LOC|NAD|DTM/', $checkContent)) {
			exit;
		}
		
        $message = \Metroplex\Edifact\Message::fromFile( $localFile );

        $CntrNo = $OprID = $LocalSZPT = $ISO_SZTP = $CntrClass = $Status = $DELIVERYORDER = $BLNo = $EdoDate = $PickedUpDate = $ExpDate = $Shipper_Name = 
        $ShipName = $ShipID = $ImVoy = $ExVoy = $POL = $POD = $FPOD = $CJMODE_CD = $DMETHOD_CD = $RetLocation = $Haulage_Instruction = $Note = $result = array();

        foreach ($message->getSegments('EQD') as $segment) {
            switch($segment->getElement(0)) {
                case 'CN':
                    array_push( $CntrNo , $segment->getElement(1));
                    array_push( $ISO_SZTP, $segment->getElement(2)[0]);
                    array_push($CntrClass, $segment->getElement(4));
                    array_push($Status, $segment->getElement(2)[2] );
                    break;
                default:
                    break;
            }
        }

        foreach ($message->getSegments('NAD') as $segment) {
            if( $segment->getElement(0) == 'CA') {
				switch($segment->getElement(1)[0]) {
					case 'HLC':
						$tmp = "HPL";
						break;
					default:
						$tmp = $segment->getElement(1)[0];
						break;
				}
				
				//$tmp = $segment->getElement(1)[0] == "HLC" ? "HPL" : $segment->getElement(1)[0];
                array_push(  $OprID, $tmp); //HLC ==> HPL
            }  
        }

        // $LocalSZPT
        // Tạm để trống
        //

        foreach ($message->getSegments('RFF') as $segment) {
            switch($segment->getElement(0)[0]) {
                case 'AAJ':
                    array_push($DELIVERYORDER, $segment->getElement(0)[1]);
                    break;
                case 'BM':
                    array_push($BLNo, $segment->getElement(0)[1]);
                    break;
                default:
                    break;
            }
        }

        foreach ($message->getSegments('DTM') as $segment) {
            switch($segment->getElement(0)[0]) {
                //case 137:
                //    preg_match('/^(\d{4})(\d{2})(\d{2})(\d{2})(\d{2})/', $segment->getElement(0)[1], $date);
                //    array_push($EdoDate, "$date[1]-$date[2]-$date[3] $date[4]:$date[5]");
                //    break;
                case 200:
                    preg_match('/^(\d{4})(\d{2})(\d{2})(\d{2})(\d{2})/', $segment->getElement(0)[1], $date);
                    array_push($EdoDate, "$date[1]-$date[2]-$date[3] $date[4]:$date[5]"); //$PickedUpDate
                    break;
                case 400:
                    preg_match('/^(\d{4})(\d{2})(\d{2})(\d{2})(\d{2})/', $segment->getElement(0)[1], $date);
                    //array_push($ExpDate, "$date[1]-$date[2]-$date[3] $date[4]:$date[5]");
					array_push($ExpDate, "$date[1]-$date[2]-$date[3] 23:59");
                    break;
                default:
                    break;
            }
        }

        foreach ($message->getSegments('NAD') as $segment) {
            if($segment->getElement(0) == 'BJ') {
                array_push($Shipper_Name, $segment->getElement(2) );
            }
        }

        foreach ($message->getSegments('TDT') as $segment) {
            array_push($ShipName, $segment->getElement(7)[3] );
            array_push(  $ImVoy, $segment->getElement(1));
        }

        // $ShipID
        // Tạm để trống
        //

        // $ExVoy
        // Tạm để trống
        //

        // $POL
        // Tạm để trống
        //

        foreach ($message->getSegments('LOC') as $segment) {
            switch($segment->getElement(0)) {
                case 176:
                    array_push($POD, $segment->getElement(1)[0] . ' : ' . $segment->getElement(2)[0]);
                    break;
                case 170:
                    array_push($FPOD, $segment->getElement(1)[0] . ' : ' . $segment->getElement(2)[0]);
                    break;
                case 99:
                    array_push($RetLocation, $segment->getElement(1)[0] . ' : ' . $segment->getElement(2)[0]);
                    break;
                default :
                    break;
            }    
        }
		
		foreach ($message->getSegments('FTX') as $segment) {
            array_push(  $Haulage_Instruction, $segment->getElement(3));
        }

        //$currenTime = date('Y-m-d H:i:s' .substr((string)microtime(), 1, 4), time());
		
		$tempTime = $this->ceh->query("SELECT GETDATE() AS currents")->row();
        $currenTime = $tempTime->currents;

        for($i=0; $i<count($CntrNo); $i++) {
            $result[$i]['CreatedBy'] = 'EDISERVER';
            //$result[$i]['insert_time'] = $currenTime;
            $result[$i]['CntrNo'] = isset($CntrNo[$i]) ? trim($CntrNo[$i]) : '';
            $result[$i]['OprID'] = isset($OprID[$i]) ? trim($OprID[$i]) : '';
            $result[$i]['ISO_SZTP'] = isset($ISO_SZTP[$i]) ? trim($ISO_SZTP[$i]) : '';
            $result[$i]['CntrClass'] = isset($CntrClass[$i]) ? (($CntrClass[$i] == 3) ? 1 : (($CntrClass[$i] == 2) ? 3 : NULL)) : NULL;
            $result[$i]['Status'] = isset($Status[$i]) ? (($Status[$i] == 5) ? 'F' : (($Status[$i] == 4) ? 'E' : NULL)) : NULL;
            $result[$i]['DELIVERYORDER'] = isset($DELIVERYORDER[$i]) ? trim($DELIVERYORDER[$i]) : '';
            $result[$i]['BLNo'] = isset($BLNo[$i]) ? trim($BLNo[$i]) : '';
            //$result[$i]['EdoDate'] = isset($EdoDate[$i]) ? trim($EdoDate[$i]) : '';
			$result[$i]['EdoDate'] = isset($currenTime) ? trim($currenTime) : '';
            $result[$i]['PickedUpDate'] = isset($PickedUpDate[$i]) ? trim($PickedUpDate[$i]) : '';
            $result[$i]['ExpDate'] = isset($ExpDate[$i]) ? trim($ExpDate[$i]) : '';
            $result[$i]['Shipper_Name'] = isset($Shipper_Name[$i]) ? trim($Shipper_Name[$i]) : '';
            $result[$i]['ShipName'] = isset($ShipName[$i]) ? trim($ShipName[$i]) : '';
            $result[$i]['ImVoy'] = isset($ImVoy[$i]) ? trim($ImVoy[$i]) : '';
            $result[$i]['POD'] = isset($POD[$i]) ? trim($POD[$i]) : '';
            $result[$i]['FPOD'] = isset($FPOD[$i]) ? trim($FPOD[$i]) : '';
			$result[$i]['Haulage_Instruction'] = isset($Haulage_Instruction[$i]) ? trim($Haulage_Instruction[$i]) : NULL;
            $result[$i]['RetLocation'] = isset($RetLocation[$i]) ? trim($RetLocation[$i]) : NULL;
            $result[$i]['YARD_ID'] = $this->yard_id;
			$result[$i]['HBILL_CHK'] = 0;
			
			$this->ceh->like('CntrNo', trim($result[$i]['CntrNo']), 'none');
			$this->ceh->like('DELIVERYORDER', trim($result[$i]['DELIVERYORDER']), 'none');
			$this->ceh->like('BLNo', trim($result[$i]['BLNo']), 'none');
			$this->ceh->from('EDI_EDO');

			$checkExited = $this->ceh->count_all_results();			
			
			$tmp = $result[$i];
			
			if($checkExited > 0) {
				
				$this->ceh->set(array(
					'ExpDate' => $tmp['ExpDate'],
				));
				$this->ceh->where('DELIVERYORDER', $tmp["DELIVERYORDER"]);
				$this->ceh->where("BLNo", $tmp["BLNo"]);
				$this->ceh->where("CntrNo", $tmp["CntrNo"]);
				$this->ceh->where("EIR_STATUS", 'A');
				$this->ceh->update("EIR_DRAFT");
				
				$this->ceh->set(array(
					'ExpDate' => $tmp['ExpDate'],
				));
				$this->ceh->where('DELIVERYORDER', $tmp["DELIVERYORDER"]);
				$this->ceh->where("BLNo", $tmp["BLNo"]);
				$this->ceh->where("CntrNo", $tmp["CntrNo"]);
				$this->ceh->where("bXNVC", '0');
				$this->ceh->update("EIR");
				
				$tmp['EDO_Status'] = 'U';
				$tmp['update_time'] = $currenTime;
				$this->ceh->update('EDI_EDO', $tmp, array('CntrNo' => $tmp['CntrNo'], 'BLNo' => $tmp['BLNo'], 'DELIVERYORDER' => $tmp['DELIVERYORDER']));
			} else {
				$tmp['EDO_Status'] = 'A';
				$this->ceh->insert('EDI_EDO', $tmp);
			}			
        }

        //$this->ceh->insert_batch('EDI_EDO', $result);
        exit;
    }
	
	/**
    * EDImg Class
    *
    * method rCOPARN($localFile)
    *
    * Đọc file EDI, insert DB
    *
    * @param	string	$localFile
    * @return	no
    */
	
    public function rCOPARN($localFile) {
		
		$checkContent = file_get_contents($localFile);
		
		if(!preg_match('/UNA|EQD|UNB|TDT|RFF|LOC|NAD|DTM/', $checkContent)) {
			exit;
		}
		
		preg_match('/UNH(.*)\+COPARN\:D\:(.*)\:UN/', $checkContent, $version);		
		
		$CntrS = array();
		
		switch ($version[2]) {
			case '95B':
				$CntrS = $this->readD95B($checkContent, $version[2]);
				break;
			case '00B':
				$CntrS = $this->readD00B($checkContent, $version[2]);
				break;
			default:
				break;
		}
		
		/*
		var_export($CntrS);		
		exit;
		*/
		
		foreach($CntrS as $cntr) {
		/*
			$tmp = $this->ceh->select('ID')->where(array('BookingNo' =>  $cntr['BookingNo'], 'OprID' => $cntr['OprID'], "ShipName" =>  $cntr['ShipName']))
						->get("EMP_BOOK")->result_array();
			if(count($tmp) > 0) {
				$this->ceh->where(array('BookingNo' =>  $cntr['BookingNo'], 'OprID' => $cntr['OprID'], "ShipName" =>  $cntr['ShipName']));				
				$this->ceh->update('EMP_BOOK', $cntr);
			} else {
				$this->ceh->insert('EMP_BOOK', $cntr);
			}
		*/
		// 9 = insert
		// 5 = update
		// 1 = cancel
			
			if($cntr['action'] == '9') {
				unset($cntr['action']);
				$stmt = $this->ceh->select('CreatedBy')
									->where(array(
													'BookingNo' => $cntr['BookingNo'],
													'OPR_SZTP' => $cntr['OPR_SZTP'],		
													'OprID'		=> $cntr['OprID']
												)
									)->get('EMP_BOOK')->row_array();
				if(is_null($stmt)) {
					$this->ceh->insert('EMP_BOOK', $cntr);
				}				
			}
			
			if($cntr['action'] == '5') {
				unset($cntr['action']);
				
				$cntr['update_time'] = $cntr['insert_time'];
				$cntr['BOOK_STATUS'] = 'U';
				$cntr['ModifiedBy'] = $cntr['CreatedBy'];
				
				unset($cntr['insert_time']);
				
				$where = array(
					'BookingNo' => $cntr['BookingNo'],
					'OPR_SZTP' => $cntr['OPR_SZTP'],
					'OprID'		=> $cntr['OprID'],
				);
				
				$this->ceh->set($cntr);
				$this->ceh->where($where);
				$this->ceh->update('EMP_BOOK');
			}
			
			if($cntr['action'] == '1') {
				unset($cntr['action']);

				$set = array(
					'BOOK_STATUS'	=> 'C',
					'update_time'	=> $cntr['insert_time']
				);
				
				$where = array(
					'BookingNo' => $cntr['BookingNo'],
					'OPR_SZTP'	=> $cntr['OPR_SZTP'],
					'OprID'		=> $cntr['OprID']
				);
				
				$this->ceh->set($set);
				$this->ceh->where($where);
				$this->ceh->update('EMP_BOOK');
			}
		/*	
			unset($cntr['action']);
			$this->ceh->insert('EMP_BOOK', $cntr);
		*/
		}
		echo "done";
		//var_export( json_decode($interpreter->getJson(), true));
	}
	
	private function readD95B($checkContent, $version) {
		//$CntrNo = $OprID = $LocalSZPT = $ISO_SZTP = $CntrClass = $Status = $DELIVERYORDER = $BLNo = $EdoDate = $PickedUpDate = $ExpDate = $Shipper_Name = 
        //$ShipName = $ShipID = $ImVoy = $ExVoy = $POL = $POD = $FPOD = $CJMODE_CD = $DMETHOD_CD = $RetLocation = $Haulage_Instruction = $Note = $result = array();
		require_once APPPATH.'third_party/edifactNew/vendor/autoload.php';
		
        $p = new EDI\Parser($checkContent);
		
		$edi = $p->get();

		$mapping = new EDI\Mapping\MappingProvider('D' . $version);

		$analyser = new EDI\Analyser();
		$segs = $analyser->loadSegmentsXml($mapping->getSegments());
		$svc = $analyser->loadSegmentsXml($mapping->getServiceSegments(3));

		$interpreter = new EDI\Interpreter($mapping->getMessage('COPARN'), $segs, $svc);
		$prep = $interpreter->prepare($edi);
		$results = json_decode($interpreter->getJson(), true);		
		
		$CntrS = array();
		
		$tempTime = $this->ceh->query("SELECT GETDATE() AS currents")->row();
        $currenTime = $tempTime->currents;
		
		foreach($results as $result) {
			$tmp = array();
			
			foreach($result['SG1'] as $sg1) {
				$tmp["VoyAge"] = $sg1['detailsOfTransport']['conveyanceReferenceNumber']; 
				$tmp["VesselName"] = $sg1['detailsOfTransport']['transportIdentification']['idOfTheMeansOfTransport']; 
				foreach($sg1['placelocationIdentification'] as $pod) {
					if($pod['placelocationQualifier'] == "9") {
						$tmp["POL"] = $pod['locationIdentification']['placelocationIdentification']; // . ':' . $pod['locationIdentification']['placelocation'];
					}
					
					if($pod['placelocationQualifier'] == "11") {
						$tmp["POD"] = $pod['locationIdentification']['placelocationIdentification']; // . ':' . $pod['relatedLocationOneIdentification']['relatedPlacelocationOne'];
					}
					
					if($pod['placelocationQualifier'] == "163") {
						$tmp["FPOD"] = $pod['locationIdentification']['placelocationIdentification']; // . ':' . $pod['relatedLocationOneIdentification']['relatedPlacelocationOne'];
					}
				}
			}
			
			foreach($result['SG2'] as $sg2) {
				if($sg2['nameAndAddress']['partyQualifier'] == "CA") {
					$tmp["OprID"] = $sg2['nameAndAddress']['partyIdentificationDetails']['partyIdIdentification'];
				} else {
					$tmp['OprID'] = '';
				}
			}
			
			//$tmp['OprID'] = $result['SG2'][0]['nameAndAddress']['partyIdentificationDetails']['partyIdIdentification'];
			
			switch($tmp['OprID']) {
				case 'OOL':
					$tmp['OprID'] = 'OCL';
					break;
				default:
					break;
			}
			
			$tmp['CreatedBy'] = "EDICOPARN_" . $tmp['OprID'];
			$tmp["CmdID"] = $result['SG3'][0]['freeText']['textLiteral']['freeText'];
			$tmp["CJMode_CD"] = "CAPR";
			$tmp["isAssignCntr"] = "N";
			$tmp["YARD_ID"] = "ITC";
			$tmp['insert_time'] = $currenTime;
			$tmp['BOOK_STATUS'] = 'A';
			$tmp['BookingDate'] = $currenTime;
			$tmp['StackingAmount'] = '0';
			$tmp['CARGO_TYPE'] = 'MT';
			$tmp['action'] = $result['beginningOfMessage']['messageFunctionCoded'];
			
			foreach($result['SG3'] as $sg3) {
				$tmp["CmdID"] = $sg3['freeText']['textLiteral']['freeText'];
				if(isset($sg3['SG4'])) {
					foreach($sg3['SG4'] as $sg4) {
						$ckcodeListQualifier = $sg4['nameAndAddress']['partyIdentificationDetails']['codeListQualifier'];
						if($ckcodeListQualifier == '172') {
							//$tmp["FwName"] = $sg4['nameAndAddress']['nameAndAddress']['nameAndAddressLine'];
							$tmp["ShipName"] = $sg4['nameAndAddress']['nameAndAddress']['nameAndAddressLine'];
						}
					}
				}
				
				if(is_array($sg3['SG6'])) {
					foreach($sg3['SG6'] as $sg6) {
						$tmp["DG_CD"] = $sg6['dangerousGoods']['undgInformation']['undgNumber'];
					}
				}				
			}
			
			foreach($result['SG7'] as $sg7) {
				if(isset($sg7['placelocationIdentification'])) {
					foreach($sg7['placelocationIdentification'] as $tmpPOD) {
						if($tmpPOD['placelocationQualifier'] == "11") {
							$tmp["POD"] = $tmpPOD['locationIdentification']['placelocationIdentification']; // . ':' 
						}
					}
				}
				
				
				//TMD
				$tmp["Temperature"] = '';
				$tmp["BookAmount"] = $sg7['numberOfUnits']['numberOfUnitDetails']['numberOfUnits'] != '' ? $sg7['numberOfUnits']['numberOfUnitDetails']['numberOfUnits'] : 0;
				$tmp["BookingNo"] = $sg7['reference']['reference']['referenceNumber'];
				$tmp["LocalSZPT"] = $sg7['equipmentDetails']['equipmentSizeAndType']['equipmentSizeAndTypeIdentification'];
				$tmp["OPR_SZTP"] = $sg7['equipmentDetails']['equipmentSizeAndType']['equipmentSizeAndTypeIdentification'];
				
				if($sg7['datetimeperiod']['datetimeperiod']['datetimeperiodQualifier'] == '181') {
					$cacheDate = $sg7['datetimeperiod']['datetimeperiod']['datetimeperiod'];
				} else {
					$cacheDate = null;
				}
				
				if(!preg_match('/\-/', $cacheDate) && !is_null($cacheDate)) {
					preg_match('/^(\d{4})(\d{2})(\d{2})/', $cacheDate, $date);					
					$tmp["ExpDate"] =  "$date[1]-$date[2]-$date[3] 23:59";					
				}
				
				if($sg7['freeText']['textSubjectQualifier'] == 'SIN') {
					$tmp["Note"] = $sg7['freeText']['textReference']['freeTextCoded'];	
				}
							
				$tmp["KGW"] = $sg7['measurements']['valuerange']['measurementValue'];
				
				array_push($CntrS, $tmp);
			}
		}
		
		return $CntrS;
	} //ShipName
	
	private function readD00B($checkContent, $version) {
		//$CntrNo = $OprID = $LocalSZPT = $ISO_SZTP = $CntrClass = $Status = $DELIVERYORDER = $BLNo = $EdoDate = $PickedUpDate = $ExpDate = $Shipper_Name = 
        //$ShipName = $ShipID = $ImVoy = $ExVoy = $POL = $POD = $FPOD = $CJMODE_CD = $DMETHOD_CD = $RetLocation = $Haulage_Instruction = $Note = $result = array();
		require_once APPPATH.'third_party/edifactNew/vendor/autoload.php';
		
        $p = new EDI\Parser($checkContent);
		
		$edi = $p->get();

		$mapping = new EDI\Mapping\MappingProvider('D' . $version);

		$analyser = new EDI\Analyser();
		$segs = $analyser->loadSegmentsXml($mapping->getSegments());
		$svc = $analyser->loadSegmentsXml($mapping->getServiceSegments(3));

		$interpreter = new EDI\Interpreter($mapping->getMessage('COPARN'), $segs, $svc);
		$prep = $interpreter->prepare($edi);
		$results = json_decode($interpreter->getJson(), true);
		
		$CntrS = array();
		
		foreach($results as $result) {
			$tmp = array();
			
			foreach($result['SG1'] as $sg1) {
				$tmp["VoyAge"] = $sg1['detailsOfTransport']['conveyanceReferenceNumber'];
			}
			
			$tmp['OprID'] = $result['SG2'][0]['nameAndAddress']['partyIdentificationDetails']['partyIdIdentification'];
			$tmp["CmdID"] = $result['SG3'][0]['freeText']['textLiteral']['freeText'];
			
			foreach($result['SG3'] as $sg3) {
				$tmp["CmdID"] = $sg3['freeText']['textLiteral']['freeText'];
				foreach($sg3['SG4'] as $sg4) {
					$tmp["FwName"] = $sg4['nameAndAddress']['nameAndAddress']['nameAndAddressLine'];
					$tmp["CusName"] = $sg4['nameAndAddress']['nameAndAddress']['nameAndAddressLine'];
				}
			}
			
			foreach($result['SG7'] as $sg7) {
				$tmp["BookingNo"] = $sg7['reference']['reference']['referenceNumber'];
				$tmp["ISO_SZTP"] = $sg7['equipmentDetails']['equipmentSizeAndType']['equipmentSizeAndTypeIdentification'];
				
				$cacheDate = $sg7['datetimeperiod']['datetimeperiod']['datetimeperiod'];
				
				if(!preg_match('/\-/', $cacheDate)) {
					preg_match('/^(\d{4})(\d{2})(\d{2})/', $cacheDate, $date);					
					$tmp["ExpDate"] =  "$date[1]-$date[2]-$date[3] 23:59";					
				}
				
				array_push($CntrS, $tmp);
			}
		}
		
		return $CntrS;
	}
}
