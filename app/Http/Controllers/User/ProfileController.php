<?php

namespace App\Http\Controllers\User;

use App\Models\User;
use App\Models\SchoolsUser;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ProfileController extends Controller
{
    //
    public function show()
    {
        return view('users.profile', [
            'user' => Auth::user()->load('schoolsusers'),
        ]);
    }

    public function general (Request $request)
    {
        
        $user = Auth::user();

        $validator = Validator::make($request->all(), [
            'name'          => 'required|string|min:3|max:255',
            'surname'       => 'required|string|min:3|max:255',
            'phone'         => 'required|string|min:9|max:15',
            'country'       => 'required|string|min:3|max:255',
            'city'          => 'required|string|min:3|max:255',
            'residence'     => 'required|string|min:3|max:255',
            'gender'        => 'required|string|in:male,female,other',
            'birthday'      => 'required|date|before_or_equal:today',
            'website_link'  => 'nullable|url|max:255',
            'github_link'   => 'nullable|url|max:255',
            'linkedin_link' => 'nullable|url|max:255',
        ]);

        if ($validator->fails()) {
            return redirect()->route('users.profile')->with('error', 'Enter alls user informations');
        }

        $user->name    = $request->name;
        $user->surname = $request->surname;
        $user->phone   = $request->phone;

        $user->save();

        $detailsData = $request->only([
            'country',
            'city',
            'residence',
            'gender',
            'birthday',
            'website_link',
            'github_link',
            'linkedin_link',
        ]);

        $user->detailsusers()->updateOrCreate([],$detailsData);


        return redirect()->route('users.profile')->with('success', 'User updated successfully');

    }

    public function updateProfile(Request $request){
        $user = Auth::user();
        $validator = Validator::make($request->all(),[
            'profile_picture' => 'required|image|mimes:jpeg,png,jpg,gif',
        ]);

        if($validator->fails()){
            return redirect()->route('users.profile')->with('error', 'Enter an profile');
        }

        if ($request->hasFile('profile_picture')) {
            
            if ($user->profile_picture) {
                Storage::disk('public')->delete($user->profile_picture);
            }
 
            $imagePath = $request->file('profile_picture')->store('profile_pictures', 'public');

            $user->profile = $imagePath;
        }

        $user->save();

        $user->profile = Storage::url('profile_images/' . $user->image); // Génère l'URL complète

        return redirect()->route('users.profile')->with('success', 'User updated successfully !');
    }

    public function addSchool(Request $request)
    {
        //
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|min:3',
            'start' => 'required|date',
            'end' => 'required|date'
        ]);

        if ($validator->fails()) {
            return redirect()->route('users.profile')->with('error', 'Enter alls school informations');
        }

        $school = new SchoolsUser();
        $school->user_id = Auth::user()->id;
        $school->name = $request->name;
        $school->start_date = $request->start;
        $school->end_date = $request->end;
        $school->save();

        return redirect()->route('users.profile')->with('success', 'School added successfully !');
    }

    public function deleteSchool(string $id)
    {
        //
        $school = SchoolsUser::find($id);
        $school->delete();
        return redirect()->route('users.profile')->with('success', 'School deleted successfully !');
    }

        public function updateBio(Request $request){
        $user = Auth::user();


        $validator = Validator::make($request->all(), [
            'biography'          => 'required|string',
        ]);

        if ($validator->fails()) {
            return redirect()->route('users.profile')->with('error', 'Enter your biography');
        }

        $detailsData = $request->only([
            'biography'
        ]);

        $user->detailsusers()->updateOrCreate([],$detailsData);

        return redirect()->route('users.profile')->with('success', 'biography updated successfully');
    }

    public function account()
    {
        return view('users.account', [
            'user' => Auth::user()->load('schoolsusers','post'),
        ]);
    }

    public function Useraccount($account)
    {
        return view('users.user-account', [
            'user' => User::find($account)->load('schoolsusers','post'),
        ]);
    }

    public function AdminUseraccount($account)
    {
        return view('admins.view-user', [
            'user' => User::find($account)->load('schoolsusers','post'),
        ]);
    }

    
}
