<?php

namespace App\Http\Controllers;

use App\Http\Interfaces\CourseMaterialArticleServiceInterface;
use App\Http\Interfaces\CourseMaterialMenuServiceInterface;
use App\Http\Interfaces\CourseMaterialServiceInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Interfaces\JWTAuthServiceInterface;
use App\Models\CourseMaterialArticleModel;
use App\Models\CourseMaterialMenuModel;
use App\Models\CourseMaterialParentMenuModel;

class CourseMaterialMenuController extends Controller
{
    /**
	 * jwtAuthServiceInterface
	 *
	 * @var mixed
	 */
	protected $jwtAuthServiceInterface;
	
	/**
	 * courseMaterialArticleServiceInterface
	 *
	 * @var mixed
	 */
	protected $courseMaterialArticleServiceInterface;

	protected $courseMaterialMenuServiceInterface;

	protected $courseMaterialServiceInterface;

	/**
	 * __construct
	 *
	 * @return void
	 */
	public function __construct(
		JWTAuthServiceInterface $jwtAuthServiceInterface,
		CourseMaterialArticleServiceInterface $courseMaterialArticleServiceInterface,
		CourseMaterialMenuServiceInterface $courseMaterialMenuServiceInterface,
		CourseMaterialServiceInterface $courseMaterialServiceInterface
	) {
		$this->jwtAuthServiceInterface = $jwtAuthServiceInterface;
		$this->courseMaterialArticleServiceInterface = $courseMaterialArticleServiceInterface;
		$this->courseMaterialMenuServiceInterface = $courseMaterialMenuServiceInterface;
		$this->courseMaterialServiceInterface = $courseMaterialServiceInterface;
	}

	/**
	 * rules
	 *
	 * @return void
	 */
	public function rules()
	{
		return [
			'operation_type' => 'required|in:CREATE,EDIT,DELETE',
			'article_title' => 'required',
			'parent_article_id' => 'exclude_if:operation_type,CREATE|required|alpha_num',
			'parent_article_order' => 'required|numeric'
		];
	}

	/**
	 * custom messages
	 *
	 * @return void
	 */
	public function customMessages()
	{
		return [
			//
		];
	}

	/**
	 * all
	 *
	 * @param  mixed $request
	 * @return void
	 */
	public function all(Request $request)
	{
		//creating a validator
        // $validator = Validator::make($request->all(), $this->rules(), $this->customMessages());

        // //if validation fails 
        // if ($validator->fails()) {
        //     return response(
        //         array(
        //             'error' => true,
        //             'message' => $validator->errors()->all()
        //         ),
        //         400
        //     );
        // }
		
		
		$courseMaterial = $this->courseMaterialServiceInterface->getCourseMaterialById($request->input('course_material_id'));

		$courseMaterialMenu = $this->courseMaterialMenuServiceInterface->getAllMenuForMaterial(
			$request->input('course_material_id')
		);

		$data = array(
			'courseMaterial' => $courseMaterial,
			'courseMaterialMenu' => $courseMaterialMenu
		);

		return $this->jwtAuthServiceInterface->sendBackToClient(
			$request->header('Auth'), 
			$request->header('UserId'), 
			'data', 
			$data
		);
	}

	/**
	 * add
	 *
	 * @param  mixed $request
	 * @return void
	 */
	public function add(Request $request)
    {
		$token = $request->header('Auth');
        $userId = $request->header('UserId');

        //creating a validator
        $validator = Validator::make($request->all(), $this->rules(), $this->customMessages());

        //if validation fails 
        if ($validator->fails()) {
            return response(
                array(
                    'error' => true,
                    'message' => $validator->errors()->all()
                ),
                400
            );
        }

		// generate article id
		$articleId = uniqid();

		// save course martial article model
		//creating a new model
        $model = new CourseMaterialArticleModel();

		//adding values to the model
        $model->course_material_id = $request->input('course_material_id');
        $model->article_id = $articleId;
        $model->article_title = $request->input('article_title');
        
        //saving the model to database
        $model->save();

		// save course martial sub child menu
        //creating a new model
        $model = new CourseMaterialParentMenuModel();

		//adding values to the model
        $model->parent_article_id = $articleId;
		$model->course_material_id = $request->input('course_material_id');
        $model->parent_article_order = $request->input('parent_article_order');

        //saving the model to database
        $model->save();

		
		// get the saved sub child mode;
		$model = $this->courseMaterialMenuServiceInterface->getParentMenuById(
			$request->input('course_material_id'),
			$articleId
		);

        // return to client
		return $this->jwtAuthServiceInterface->sendBackToClient($token, $userId, 'resource', $model);
    }
}
