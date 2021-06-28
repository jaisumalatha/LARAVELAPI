<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;
use GuzzleHttp\Client;

class ApiController extends Controller
{
  
    public function getAllStudents() {
        $students = Student::get()->toJson(JSON_PRETTY_PRINT);
        return response($students, 200);
      }

    public function createStudent(Request $request) {
        $student = new Student;
        $student->name = $request->name;
        $student->email = $request->email;
		$student->phone = $request->phone;
		$student->profile_picture = $request->profile_picture;
		$student->password = $request->password;
        $student->save();
    
        return response()->json([
            "message" => "student record created"
        ], 201);
      }

      public function getStudent($id) {
        if (Student::where('id', $id)->exists()) {
            $student = Student::where('id', $id)->get()->toJson(JSON_PRETTY_PRINT);
            return response($student, 200);
          } else {
            return response()->json([
              "message" => "Student not found"
            ], 404);
          }
      }

      public function updateStudent(Request $request, $id) {
        if (Student::where('id', $id)->exists()) {
            $student = Student::find($id);
            $student->name = is_null($request->name) ? $student->name : $request->name;
            $student->profile_picture = is_null($request->profile_picture) ? $student->profile_picture : $request->profile_picture;
            $student->email = is_null($request->email) ? $student->email : $request->email;
            $student->phone = is_null($request->phone) ? $student->phone : $request->phone;
            $student->password = is_null($request->password) ? $student->password : $request->password;
            $student->save();
    
            return response()->json([
                "message" => "records updated successfully"
            ], 200);
            } else {
            return response()->json([
                "message" => "Student not found"
            ], 404);
            
        }
    }

    public function deleteStudent ($id) {
        if(Student::where('id', $id)->exists()) {
          $student = Student::find($id);
          $student->delete();
  
          return response()->json([
            "message" => "records deleted"
          ], 202);
        } else {
          return response()->json([
            "message" => "Student not found"
          ], 404);
        }
      }

     
}
