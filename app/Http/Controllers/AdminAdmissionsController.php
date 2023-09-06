<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Admission;
use App\Models\Classs;
use App\Models\Section;
use App\Models\Student;
use App\Models\Photo;

class AdminAdmissionsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // $admissions = Admission::all();
        $admissions = Admission::orderBy('created_at', 'desc')->paginate(5);
        return view('admin.admissions.index', compact('admissions'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $input = $request->all();

        $this->validate($request, [
            'student_birth_reg'=>'required|max:17|unique:students,student_birth_reg',
        ]);

        if($file = $request->file('photo_id')){
            $name = time() . $file->getClientOriginalName();

            $file->move('images', $name);

            $photo = Photo::create(['file'=>$name]);

            $input['photo_id'] = $photo->id;
        }

        if($file = $request->file('certificate_id')){
            $name = time() . $file->getClientOriginalName();

            $file->move('images', $name);

            $certificate = Photo::create(['file'=>$name]);

            $input['certificate_id'] = $certificate->id;
        }


        $result = Admission::where('id', $request->admission_id)
            ->update(['status' => 1]);

        $student = Student::create($input);


        return redirect('/admin/admissions');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $admission = Admission::findOrFail($id);
        $classes = Classs::pluck('name','id')->all();
        $sections = Section::pluck('name','id')->all();
        return view('admin.admissions.show', compact('admission', 'classes','sections'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $admission = Admission::findOrFail($id);

        if ($admission->photo_id !== null) {
            unlink(public_path() . $admission->photo->file);

            $photo = $admission->photo->id;
            Photo::findOrFail($photo)->delete();
        }

        if ($admission->certificate_id !== null) {
            unlink(public_path() . $admission->certificate->file);

            $certificate = $admission->certificate->id;
            Photo::findOrFail($certificate)->delete();
        }

        $admission->delete();
        return redirect('/admin/admissions');
    }
}