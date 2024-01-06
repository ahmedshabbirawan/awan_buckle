<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\Employee;
use App\Models\Project;
use App\Models\Organization;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
class EmployeeHttpReq extends Model
{
    use HasFactory;

    
    public $allEmployeeURL = "https://test.hr. .gov.pk/api/rest_api/ EmployeesInventory";




    static function syncRemoteData(){



        $POSTFIELDS  = array('X-API-KEY' => '$2y$11$Qutk6KR7qGwazA3Nk8aeoureByn');
        
                
        $post_string= http_build_query($POSTFIELDS);
                    
        $url='https://hr. .gov.pk/beta/api/rest_api/ EmployeesInventory';
        $ch= curl_init();// or die("Cannot init");
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST,"GET");                                                                     
        curl_setopt($ch, CURLOPT_POSTFIELDS,  $POSTFIELDS);
        $headers = array(
            'Content-Type:application/json',
            'X-API-KEY: $2y$11$Qutk6KR7qGwazA3Nk8aeoureByn',
            'Authorization: Basic '. base64_encode("us_inventory:Vr4E3e3!ToHA&MK") // <---
        );
         curl_setopt($ch, CURLOPT_HTTPHEADER, $headers );

        $response = curl_exec($ch);
        curl_close($ch);

        $resArr = json_decode($response,true);

        if($resArr['code'] == 200){
            // print_r($resArr->empList);
            $data = $resArr['empList']; // ->toArray();
            $orgnization =  Organization::where('name', 'LIKE',  ' ')->first()->id;

            foreach($data as $rec){
                    $rec['source']     = ' ';
                    $rec['org_id']     = $orgnization;
                    Employee::updateOrCreate(['emp_code' => $rec['emp_code']], $rec);
            }
        

        //  Employee::upsert($data, ['emp_code'],[
        //     'emp_code',
        //     'first_name',
        //     'last_name',
        //     'designation',
        //     'project',
        //     'project_id',
        //     'department',
        //     'cnic',
        //     'mobile',
        //     'email',
        //     'status'           
        //  ]);
        }        
       // echo $response;
    }

    static function syncProjectData(){



        $POSTFIELDS  = array('X-API-KEY' => '$2y$11$Qutk6KR7qGwazA3Nk8aeoureByn');
        
                
        $post_string= http_build_query($POSTFIELDS);
                    
        $url='https://hr. .gov.pk/beta/api/rest_api/getActiveProjects';
        $ch= curl_init();// or die("Cannot init");
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST,"GET");                                                                     
        curl_setopt($ch, CURLOPT_POSTFIELDS,  $POSTFIELDS);
        $headers = array(
            'Content-Type:application/json',
            'X-API-KEY: $2y$11$Qutk6KR7qGwazA3Nk8aeoureByn',
            'Authorization: Basic '. base64_encode("us_inventory:Vr4E3e3!ToHA&MK") // <---
        );
         curl_setopt($ch, CURLOPT_HTTPHEADER, $headers );

        $response = curl_exec($ch);
        curl_close($ch);

        $resArr = json_decode($response,true);

       

        if($resArr['code'] == 200){
            // print_r($resArr->empList);
            $data = $resArr['activeProjects']; // ->toArray();
            $orgnization =  Organization::where('name', 'LIKE',  ' ')->first()->id;

            foreach($data as $rec){
                $projectData = array(
                                    'id'          => $rec['id'],
                                    'name'        => $rec['projectName'],
                                    'code'        => $rec['projectCode'],
                                    'manager_id'  => 0,
                                    'org_id'      => $orgnization,
                                    'detail'      => $rec['projectName'],
                                    'status'      => 1,
                                    'created_by'  => auth()->user()->id
                                    );
                Project::updateOrCreate(['code' => $rec['projectCode']], $projectData);
            }
        }        
    }
}
