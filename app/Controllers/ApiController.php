<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use App\Models\EmployeeModel;

class ApiController extends ResourceController
{
    // POST - Save employee to table
    public function addEmployee()
    {
        $rules = array(
            "name" => "required",
            "email" => "required|valid_email|is_unique[employees.email]"
        );

        if (!$this->validate($rules)) {

            // Error
            $response = [
                "status" => false,
                "message" => $this->validator->getErrors(),
                "data" => []
            ];
        } else {

            // Success
            $fileImage = $this->request->getFile("profile_image");

            if (!empty($fileImage)) {

                // Profile image available
                $imageName = $fileImage->getName();

                //abc.png, abc.png -> ["abc", "png"]
                $temp = explode(".", $imageName);

                $newImageName = round(microtime(true)) . "." . end($temp);
                // dasdasdas.png

                if ($fileImage->move("images", $newImageName)) {

                    // Success block - image uploaded
                    $data = [
                        "name" => $this->request->getVar("name"),
                        "email" => $this->request->getVar("email"),
                        "profile_image" => "/images/" . $newImageName
                    ];

                    $employeeObject = new EmployeeModel();

                    if ($employeeObject->insert($data)) {

                        // Success block
                        $response = [
                            "status" => true,
                            "message" => "Employee created successfully",
                            "data" => []
                        ];
                    } else {

                        // Failed block
                        $response = [
                            "status" => false,
                            "message" => "Failed to create an employee",
                            "data" => []
                        ];
                    }
                } else {

                    // Failed to upload image
                    $response = [
                        "status" => false,
                        "message" => "Failed to upload profile image",
                        "data" => []
                    ];
                }
            } else {

                // No profile image
                $data = [
                    "name" => $this->request->getVar("name"),
                    "email" => $this->request->getVar("email")
                ];

                $employeeObject = new EmployeeModel();

                if ($employeeObject->insert($data)) {

                    // Success
                    $response = [
                        "status" => true,
                        "message" => "Employee created successfully",
                        "data" => []
                    ];
                } else {

                    // Error
                    $response = [
                        "status" => false,
                        "message" => "Failed to create an employee",
                        "data" => []
                    ];
                }
            }
        }

        return $this->respondCreated($response);
    }

    // GET - List all employees of table
    public function listEmployee()
    {
        $employeeObject = new EmployeeModel();

        $employees = $employeeObject->findAll();

        if (!empty($employees)) {

            $response = [
                "status" => true,
                "message" => "Employee found",
                "data" => $employees
            ];
        } else {
            // No employee found in table
            $response = [
                "status" => false,
                "message" => "No Employee found",
                "data" => []
            ];
        }

        return $this->respondCreated($response);
    }

    // GET - Return single employee information data
    public function getSingleEmployee($employeeId)
    {
        $employeeObject = new EmployeeModel();

        $data = $employeeObject->find($employeeId);

        if (!empty($data)) {

            // Employee exists
            $response = [
                "status" => true,
                "message" => "Employee found",
                "data" => $data
            ];
        } else {

            // No employee found
            $response = [
                "status" => false,
                "message" => "No Employee found",
                "data" => []
            ];
        }

        return $this->respondCreated($response);
    }

    // (POST - PUT) - Update existing data of an employee
    public function updateEmployee($employeeId)
    {
        $employeeObject = new EmployeeModel();

        $data = $employeeObject->find($employeeId);

        if (!empty($data)) {

            // Exists

            $rules = [
                "name" => "required",
                "email" => "required|valid_email"
            ];

            if (!$this->validate($rules)) {

                // Error
                $response = [
                    "status" => false,
                    "message" => $this->validator->getErrors(),
                    "data" => []
                ];
            } else {

                $fileImage = $this->request->getFile("profile_image");

                if (!empty($fileImage)) {

                    // Profile image uploaded
                    $fileName = $fileImage->getName();

                    // abc.png ["abc", "png"];
                    $temp = explode(".", $fileName);

                    $newImageName = round(microtime(true)) . "." . end($temp);

                    // randomchars.png
                    if ($fileImage->move("images", $newImageName)) {

                        $updatedData = [
                            "name" => $this->request->getVar("name"),
                            "email" => $this->request->getVar("email"),
                            "profile_image" => "/images/" . $newImageName
                        ];

                        if($employeeObject->update($employeeId, $updatedData)){

                            // Success block
                            $response = [
                                "status" => true,
                                "message" => "Employee data successfully updated",
                                "data" => []
                            ];
                        }else{

                            // Error block
                            $response = [
                                "status" => false,
                                "message" => "Failed to update an employee",
                                "data" => []
                            ];
                        }
                    } else {

                        // No Image upload
                        $response = [
                            "status" => false,
                            "message" => "Failed to upload image",
                            "data" => []
                        ];
                    }
                } else {

                    // No image
                    $updatedData = [
                        "name" => $this->request->getVar("name"),
                        "email" => $this->request->getVar("email")
                    ];

                    if($employeeObject->update($employeeId, $updatedData)){

                        // Success block
                        $response = [
                            "status" => true,
                            "message" => "Employee data successfully updated",
                            "data" => []
                        ];
                    }else{

                        // Error block
                        $response = [
                            "status" => false,
                            "message" => "Failed to update an employee",
                            "data" => []
                        ];
                    }
                }
            }
        } else {

            // Employee not found
            $response = [
                "status" => false,
                "message" => "Employee not found",
                "data" => []
            ];
        }

        return $this->respondCreated($response);
    }

    // (POST - DELETE) - To delete an employee
    public function deleteEmployee($employeeId)
    {
        $employeeObject = new EmployeeModel();

        $empData = $employeeObject->find($employeeId);

        if(!empty($empData)){

            // Employee exists
            if($employeeObject->delete($employeeId)){

                // Success block
                $response = [
                    "status" => true,
                    "message" => "Employee deleted successfully",
                    "data" => []
                ];
            }else{

                // Error block
                $response = [
                    "status" => false,
                    "message" => "Failed to delete an employee",
                    "data" => []
                ];
            }
        }else{

            // not found
            $response = [
                "status" => false,
                "message" => "Employee not found",
                "data" => []
            ];
        }

        return $this->respondCreated($response);
    }
}
