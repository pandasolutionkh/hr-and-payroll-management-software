<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Attendance_Bonus extends CI_Controller 
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library('session');
        $this->load->helper('url');
        $this->load->helper('form');
        $this->load->model('Operation_Model');
        $this->load->model('Search_Model');
    }
    
    public function index()
    {
        
        if($this->session->userdata('username'))
        {
            $parsingData['unit'] = $this->Operation_Model->getParentTable('unit_id', 'unit_name', 'unit');
            $parsingData['username'] = $this->session->userdata('username');
            $this->load->view('pages/attendance_bonus', $parsingData);
        }
        else 
        {
            redirect('login', 'refresh');
        }
    }
    
    public function searchEmployee()
    {
        $unitId = $this->input->post('unit');
        $floorId = $this->input->post('floor');
        $sectionId = $this->input->post('section');
        $subSectionId = $this->input->post('subsection');
        $inchargeId = $this->input->post('incharge');
        $supervisorId = $this->input->post('supervisor');
        $month = $this->input->post('month');
        
        $parsingData['month'] = $month;
        $parsingData['productionData'] = $this->Operation_Model->getAllDataFromTable('production');
        $parsingData['attendanceBonusData'] =  $this->Search_Model->searchAttendanceBonusData($unitId, $floorId, $sectionId, $subSectionId, $inchargeId, $supervisorId,$month);
        $parsingData['attendanceBonusSetupData'] = $this->Operation_Model->getAllDataFromTable('attendance_bonus_setup');
        $parsingData['employeeData'] =  $this->Search_Model->searchEmployeeData($unitId, $floorId, $sectionId, $subSectionId, $inchargeId, $supervisorId);
        $parsingData['salaryData'] =  $this->Search_Model->searchEmployeeSalary($unitId, $floorId, $sectionId, $subSectionId, $inchargeId, $supervisorId);
        $parsingData['salaryDataMonth'] =  $this->Search_Model->searchEmployeeSalaryMonth($unitId, $floorId, $sectionId, $subSectionId, $inchargeId, $supervisorId, $month);
        $parsingData['unit'] = $this->Operation_Model->getParentTable('unit_id', 'unit_name', 'unit');
        $parsingData['username'] = $this->session->userdata('username');
        $this->load->view('pages/attendance_bonus', $parsingData);
        
    }
    
    public function loadBlockMajor()
    {
        $supervisorId = $this->input->post('supervisorId');
        $BlockMajorData = $this->Operation_Model->blockMajor($supervisorId);
        echo json_encode($BlockMajorData);
    }
    
    public function submitData()
    {
        $loopcount = $this->input->post('loopCount');
        $month = $this->input->post('monthField');
        $year = date('Y');
        for($i=1; $i<$loopcount; $i++)
        {
            $employeeId = $this->input->post('employeeId_'.$i);
            $calculateSalary = $this->input->post('bonusAmmount_'.$i);

            $insertionData = array(
                'employee_id' => $employeeId,
                'attendance_bonus_month' => $month,
                'attendance_bonus_year' => $year,
                'attendance_bonus_amount' => $calculateSalary
            );
            $callInsertionMethod = $this->Operation_Model->insertDataInToTable('attendance_bonus', $insertionData);
        }     
        
        if(empty($callInsertionMethod))
        {
            if($this->session->userdata('username'))
            {
                $parsingData['unit'] = $this->Operation_Model->getParentTable('unit_id', 'unit_name', 'unit');
                $parsingData['successInsered'] = "Bonus Inserted Successfully!";
                $parsingData['username'] = $this->session->userdata('username');
                $this->load->view('pages/attendance_bonus', $parsingData);
            }
            else 
            {
                redirect('login', 'refresh');
            }
        }
        else
        {
            if($this->session->userdata('username'))
            {
                $parsingData['unit'] = $this->Operation_Model->getParentTable('unit_id', 'unit_name', 'unit');
                $parsingData['errorInsered'] = $callInsertionMethod;
                $parsingData['username'] = $this->session->userdata('username');
                $this->load->view('pages/attendance_bonus', $parsingData);
            }
            else 
            {
                redirect('login', 'refresh');
            }
        }
        
//       
    }
    
    public function checkData()
    {
        $employeeId = $this->input->post('employee_id');
        $callDuplicacyMethod = $this->Operation_Model->checkDuplicacy('employee_id', $employeeId, 'production');
        echo $callDuplicacyMethod;
    }
    
    public function deleteData()
    {
        $employeeId = $this->input->post('employee_id');
        $calldeleteMethod = $this->Operation_Model->deleteTableData('employee_id', $employeeId, 'production');
        echo $calldeleteMethod;
    }
}