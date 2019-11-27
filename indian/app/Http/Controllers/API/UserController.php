<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpKernel\DependencyInjection\MergeExtensionConfigurationPass;

class UserController extends Controller
{

    public function __construct() {
          
        $this->middleware('auth:api');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {   
        //$this->authorize('isAdmin');
        if (\Gate::allows('isAdmin') || \Gate::allows('isAuthor')) {

            return User::latest()->paginate(10);
        }
       
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {   
        $this->validate($request, [
            'name'=> 'required|max:191',
            'email' => 'required|unique:users|email|string|max:191',
            'password' => 'required|string'

        ]);

       return  User::create([
                'name' => $request->name,
                'email' => $request->email,
                'bio' => $request->bio,
                'type' => $request->type, 
                'photo' => $request->photo,
                'password' => Hash::make($request->password)
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

     /**
     * Display the specified resource.
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function updateProfile(Request $request)
    {
        $user = auth('api')->user();

        $this->validate(
            $request, [
            'name'=> 'required|max:191',
            'email' => 'required|email|string|max:191|unique:users,email,'.$user->id,
            'password' => 'sometimes|required|min:6'
            ]
        );

        $currentPhoto = $user->photo;        
        
        if ($request->photo != $currentPhoto) {

            $name = time().'.' . explode('/', explode(':', substr($request->photo, 0, strpos($request->photo, ';')))[1])[1];

            \Image::make($request->photo)->save(public_path('img/profile/').$name);

            //update the photo filed with new image
            $request->merge(['photo' => $name]);

            $userPhoto = public_path('img/profile/').$currentPhoto;

            if (file_exists($userPhoto)) {
               @unlink($userPhoto);
            }
        }        
        //bcrypt password if it has been changed
        if (!empty($request->password)) {
            $request->merge(['password'=> Hash::make($request->password)]);
        }

        $user->update($request->all()); 
             
    }

   
    public function profile()
    {
        return auth('api')->user();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->authorize('isAdmin');
        $user = User::findOrFail($id);

        $this->validate(
            $request, [
            'name'=> 'required|max:191',
            'email' => 'required|email|string|max:191|unique:users,email,'.$user->id,
            'password' => 'sometimes|required|min:6'
            ]
        );        

         //bcrypt password if it has been changed
        if (!empty($request->password)) {
            $request->merge(['password' => Hash::make($request['password'])]);
        }
        //update the users table in the database
        $user->update($request->all());
        
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {   
        $this->authorize('isAdmin');

        $user = User::findOrFail($id);       
        //delete the user
        $user->delete();        
    }
}
