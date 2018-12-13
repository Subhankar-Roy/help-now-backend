<?php

namespace App\Http\Controllers;
use App\Education;
use App\Ethnicity;
use App\Language;
use App\Relationship;
use App\State;


class CommondataController extends Controller
{	
	/**
     * This function will fetch all education catagories
     * @return json
     */
	public function getEducationvalues(){
		try{
			$getEducations=Education::all();
			if($getEducations){
				return response()->json([
                	'status'   => true,
                	'response' => [
                        'education' => $getEducations,
                        'education-count' => count($getEducations)
                    ]
            	],200); 
			}else{
				return response()->json([
                	'status'   => false,
                	'response' => "Try Again!"
            	],400); 
			}
		}catch(\Exception $e){
			return response()->json([
                'status'   => false,
                'response' => $e->getMessage()
            ],$e->getCode()); 
		}
	}

	/**
     * This function will fetch all Ethnicity catagories
     * @return json
     */
	public function getEthnicityvalues(){
		try{
			$getEthnicitys=Ethnicity::all();
			if($getEthnicitys){
				return response()->json([
                	'status'   => true,
                	'response' => [
                        'ethnicity' => $getEthnicitys,
                        'ethnicity-count' => count($getEthnicitys)
                    ]
            	],200); 
			}else{
				return response()->json([
                	'status'   => false,
                	'response' => "Try Again!"
            	],400); 
			}
		}catch(\Exception $e){
			return response()->json([
                'status'   => false,
                'response' => $e->getMessage()
            ],$e->getCode()); 
		}
	}

	/**
     * This function will fetch all Language catagories
     * @return json
     */
	public function getLanguagevalues(){
		try{
			$getLanguages=Language::all();
			if($getLanguages){
				return response()->json([
                	'status'   => true,
                	'response' => [
                        'language' => $getLanguages,
                        'language-count' => count($getLanguages)
                    ]
            	],200); 
			}else{
				return response()->json([
                	'status'   => false,
                	'response' => "Try Again!"
            	],400); 
			}
		}catch(\Exception $e){
			return response()->json([
                'status'   => false,
                'response' => $e->getMessage()
            ],$e->getCode()); 
		}
	}

	/**
     * This function will fetch all Relationship catagories
     * @return json
     */
	public function getRelationshipvalues(){
		try{
			$getRelationships=Relationship::all();
			if($getRelationships){
				return response()->json([
                	'status'   => true,
                	'response' => [
                        'relationship' => $getRelationships,
                        'relationship-count' => count($getRelationships)
                    ]
            	],200); 
			}else{
				return response()->json([
                	'status'   => false,
                	'response' => "Try Again!"
            	],400); 
			}
		}catch(\Exception $e){
			return response()->json([
                'status'   => false,
                'response' => $e->getMessage()
            ],$e->getCode()); 
		}
	}

	/**
     * This function will fetch all State
     * @return json
    */
	public function getStatevalues(){
		try{
			$getStates=State::all();
			if($getStates){
				return response()->json([
                	'status'   => true,
                	'response' => [
                        'state' => $getStates,
                        'state-count' => count($getStates)
                    ]
            	],200); 
			}else{
				return response()->json([
                	'status'   => false,
                	'response' => "Try Again!"
            	],400); 
			}
		}catch(\Exception $e){
			return response()->json([
                'status'   => false,
                'response' => $e->getMessage()
            ],$e->getCode()); 
		}
	}

}